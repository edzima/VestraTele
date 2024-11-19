<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\settlement\models\CalculationForm;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\modules\settlement\models\search\IssueViewPayCalculationSearch;
use common\components\provision\exception\Exception;
use common\components\rbac\SettlementTypeAccessManager;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\KeyStorageItem;
use common\models\settlement\PaysForm;
use common\models\settlement\SettlementType;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CalculationController implements the CRUD actions for IssuePayCalculation model.
 */
class CalculationController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssuePayCalculation models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		if (!Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			return $this->redirect(['owner']);
		}
		$searchModel = new IssuePayCalculationSearch(Yii::$app->user->id);
		$searchModel->action = SettlementTypeAccessManager::ACTION_INDEX;
		$searchModel->onlyWithPayProblems = false;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionOwner() {
		$searchModel = new IssuePayCalculationSearch(Yii::$app->user->id);
		$searchModel->scenario = IssuePayCalculationSearch::SCENARIO_OWNER;
		$searchModel->owner_id = Yii::$app->user->getId();
		$searchModel->action = SettlementTypeAccessManager::ACTION_INDEX;
		$searchModel->onlyWithPayProblems = false;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionWithoutProvisions(): string {
		if (!Yii::$app->user->can(User::PERMISSION_PROVISION)) {
			throw new ForbiddenHttpException();
		}
		Url::remember();
		$searchModel = new IssuePayCalculationSearch(Yii::$app->user->id);
		$searchModel->setScenario(IssuePayCalculationSearch::SCENARIO_ARCHIVE);
		$searchModel->withArchive = true;
		$searchModel->withIssueStage = true;
		$searchModel->withoutProvisions = true;

		$types = Yii::$app->keyStorage->get(KeyStorageItem::KEY_SETTLEMENT_TYPES_FOR_PROVISIONS, []);
		if (!empty($types)) {
			$searchModel->type_id = Json::decode($types);
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('without-provisions', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionToCreate(): string {
		$searchModel = new IssueToCreateCalculationSearch();
		$dataProvider = null;
		if ($searchModel->existMinCalculationSettings()) {
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		} else {
			Yii::$app->session->addFlash('warning', Yii::t('backend', 'Min calculation count must be set.'));
		}
		return $this->render('to-create', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssuePayCalculation models for Issue.
	 *
	 * @return mixed
	 */
	public function actionIssue(int $id): string {
		$searchModel = new IssueViewPayCalculationSearch($id, Yii::$app->user->getId());
		if ($searchModel->issue === null) {
			throw new NotFoundHttpException('Issue dont exist.');
		}
		$searchModel->action = SettlementTypeAccessManager::ACTION_ISSUE_VIEW;
		$searchModel->withCustomer = false;
		$toCreateSearchModel = new IssueToCreateCalculationSearch();
		$toCreateSearchModel->issue_id = $id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$toCreateProvider = $toCreateSearchModel->search(Yii::$app->request->queryParams);
		return $this->render('issue', [
			'toCreateSearchModel' => $toCreateSearchModel,
			'toCreateProvider' => $toCreateProvider,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePayCalculation model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id, SettlementTypeAccessManager::ACTION_VIEW);
		if (Yii::$app->user->can(User::PERMISSION_PROVISION)) {
			foreach ($model->pays as $pay) {
				if (empty($pay->provisions)) {
					Yii::$app->session->addFlash('warning',
						Yii::t('backend', 'Pay: {value} dont has provisions.', [
							'value' => $model->type->is_percentage
								? Yii::$app->formatter->asPercent($pay->getValue())
								: Yii::$app->formatter->asCurrency($pay->getValue()),
						])
					);
				}
			}
		}
		Url::remember();
		$diff = $model->getValue()->sub($model->getPays()->getValueSum());
		if (!$diff->isZero()) {
			Yii::$app->session->addFlash('error',
				Yii::t('backend', 'Settlement value is not same as sum value from pays. Diff: {diffValue}.', [
					'diffValue' => Yii::$app->formatter->asCurrency($diff),
				])
			);
			Yii::warning(Yii::t('backend', 'Settlement: {id} has not valid total value.', ['id' => $id]), 'settlement.diffValue');
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * @throws Exception
	 * @throws NotFoundHttpException
	 * @throws ForbiddenHttpException
	 */
	public function actionCreate(int $issueId, int $typeId) {
		$issue = $this->findIssueModel($issueId);
		$model = new CalculationForm(Yii::$app->user->getId(), $issue);
		$type = $this->findType($typeId);
		$this->checkTypeAccess($type, SettlementTypeAccessManager::ACTION_CREATE);
		if (!$type->isForIssueTypeId($issue->getIssueTypeId())) {
			throw new NotFoundHttpException();
		}
		$model->setType($type, true);
		if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
			Yii::$app->session->addFlash('warning', Yii::t('settlement', 'You try create calculation as Admin.'));
		}
		$model->getMessagesModel()
			->addExtraWorkersEmailsIds(
				Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_SETTLEMENT_CREATE)
			);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->pushMessages(Yii::$app->user->getId())) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('settlement', 'Push Messages about Create Settlement.')
				);
			}
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssuePayCalculation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$calculation = $this->findModel($id, SettlementTypeAccessManager::ACTION_CREATE);
		if (!Yii::$app->user->can(User::PERMISSION_CALCULATION_UPDATE)
			&& $calculation->owner_id !== Yii::$app->user->getId()) {
			throw new ForbiddenHttpException(Yii::t('backend', 'Only bookkeeper or owner can update settlement.'));
		}
		$model = CalculationForm::createFromModel($calculation);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionPays(int $id) {
		$calculation = $this->findModel($id, SettlementTypeAccessManager::ACTION_PAYS);
		if ($calculation->isPayed()) {
			Yii::$app->session->addFlash('Warning', 'Only in not payed calculation can be generate pays.');
			return $this->redirect(['view', 'id' => $id]);
		}
		$model = new PaysForm();
		$model->deadline_at = date($model->dateFormat, strtotime('last day of this month'));
		$model->value = $calculation->getValueToPay()->toFixed(2);
		$model->count = 2;
		$pay = $calculation->getPays()->one();
		if ($pay) {
			$model->vat = $pay->getVAT() ? $pay->getVAT()->toFixed(2) : null;
		} else {
			$model->vat = $calculation->issue->type->vat;
		}

		if ($model->load(Yii::$app->request->post())
			&& $model->validate()
			&& !$model->isGenerate()
		) {
			$calculation->unlinkAll('notPayedPays', true);
			$pays = $model->getPays();
			foreach ($pays as $pay) {
				$calculationPay = new IssuePay();
				$calculationPay->setPay($pay);
				$calculation->link('pays', $calculationPay);
			}
			$calculation->refresh();
			Yii::$app->provisions->removeForPays($calculation->getPays()->getIds(true));
			try {
				Yii::$app->provisions->settlement($calculation);
			} catch (Exception $exception) {
			}
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('pays', [
			'calculation' => $calculation,
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssuePayCalculation model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws MethodNotAllowedHttpException
	 */
	public function actionDelete(int $id): Response {
		$model = $this->findModel($id, SettlementTypeAccessManager::ACTION_DELETE);
		if ($model->owner_id === Yii::$app->user->getId()
			|| Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
			|| Yii::$app->user->can(Worker::PERMISSION_SETTLEMENT_DELETE_NOT_SELF)) {
			$model->delete();
			return $this->redirect(['index']);
		}
		throw new MethodNotAllowedHttpException('Only Owner or Bookkeeper can delete settlement.');
	}

	/**
	 * Finds the IssuePayCalculation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param string $action
	 * @return IssuePayCalculation the loaded model
	 * @throws ForbiddenHttpException if user can not allow for model type action
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id, string $action): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			$this->checkTypeAccess($model->type, $action);
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssueModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param SettlementType $type
	 * @param string $action
	 * @return void
	 * @throws ForbiddenHttpException
	 */
	private function checkTypeAccess(SettlementType $type, string $action): void {
		if (!$type->hasAccess(Yii::$app->user->getId(), $action)) {
			throw new ForbiddenHttpException(Yii::t('settlement', 'Not allow action:{action} to Type: {type}', [
				'action' => Yii::t('rbac', $action),
				'type' => $type->name,
			]));
		}
	}

	private function findType(int $id): SettlementType {
		$model = SettlementType::find()
			->active()
			->andWhere([
				'id' => $id,
			])->one();
		if ($model === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		return $model;
	}
}
