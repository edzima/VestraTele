<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\settlement\models\AdministrativeCalculationForm;
use backend\modules\settlement\models\CalculationForm;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use common\components\provision\exception\MissingProvisionUserException;
use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\PaysForm;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
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
	public function actionIndex(): string {
		if (!Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			throw new ForbiddenHttpException();
		}
		$searchModel = new IssuePayCalculationSearch();
		$searchModel->onlyWithProblems = false;
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
		$searchModel = new IssuePayCalculationSearch();
		$searchModel->setScenario(IssuePayCalculationSearch::SCENARIO_ARCHIVE);
		$searchModel->withIssueStage = true;
		$searchModel->withoutProvisions = true;
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
		$searchModel = new IssuePayCalculationSearch();
		$searchModel->issue_id = $id;
		if ($searchModel->issue === null) {
			throw new NotFoundHttpException('Issue dont exist.');
		}
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
		$model = $this->findModel($id);
		if (Yii::$app->user->can(User::PERMISSION_PROVISION)) {
			foreach ($model->pays as $pay) {
				if (empty($pay->provisions)) {
					Yii::$app->session->addFlash('warning',
						Yii::t('backend', 'Pay: {value} dont has provisions.', [
							'value' => Yii::$app->formatter->asCurrency($pay->getValue()),
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

	public function actionCreateAdministrative(int $id) {
		$issue = $this->findIssueModel($id);
		$model = new AdministrativeCalculationForm(Yii::$app->user->getId(), $issue);
		$model->deadline_at = date($model->dateFormat, strtotime('last day of this month'));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('administrative', [
			'model' => $model,
		]);
	}

	public function actionCreate(int $id) {
		$issue = $this->findIssueModel($id);
		$model = new CalculationForm(Yii::$app->user->getId(), $issue);
		if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
			Yii::$app->session->addFlash('warning', Yii::t('settlement', 'You try create calculation as Admin.'));
		}
		$model->vat = $issue->type->vat;
		$model->deadline_at = date($model->dateFormat, strtotime('last day of this month'));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->sendEmailToCustomer) {
				$model->sendCreateEmailToCustomer();
			}
			if ($model->sendEmailToWorkers) {
				$model->sendCreateEmailToWorkers();
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
		$calculation = $this->findModel($id);
		if (!Yii::$app->user->can(User::ROLE_BOOKKEEPER)
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
		$calculation = $this->findModel($id);
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
			} catch (MissingProvisionUserException $exception) {
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
		$model = $this->findModel($id);
		if ($model->owner_id === Yii::$app->user->getId()
			|| Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
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
	 * @return IssuePayCalculation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
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
}
