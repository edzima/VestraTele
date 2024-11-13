<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\settlement\models\IssueCostForm;
use backend\modules\settlement\models\search\IssueCostSearch;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\message\IssueCostMessagesForm;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CostController implements the CRUD actions for IssueCost model.
 */
class CostController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'settlement-link' => ['POST'],
					'settlement-unlink' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssueCost models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$model = new IssueCostSearch();
		$dataProvider = $model->search(Yii::$app->request->queryParams);
		$dataProvider->pagination->setPageSize(500);

		return $this->render('index', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssueCost models for Issue.
	 *
	 * @param int $id
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionIssue(int $id): string {
		$issue = $this->findIssue($id);
		$model = new IssueCostSearch();
		$model->issue_id = $id;
		$dataProvider = $model->search(Yii::$app->request->queryParams);

		return $this->render('issue', [
			'model' => $model,
			'issue' => $issue,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionSettlementLink(int $id, int $settlementId) {
		$model = $this->findModel($id);
		$settlement = IssuePayCalculation::findOne($settlementId);
		if ($settlement) {
			$model->link('settlements', $settlement);
		}
		return $this->redirect(Url::previous());
	}

	public function actionSettlementUnlink(int $id, int $settlementId) {
		$model = $this->findModel($id);
		$settlement = IssuePayCalculation::findOne($settlementId);
		if ($settlement) {
			$model->unlinkSettlement($settlementId);
		}
		return $this->redirect(Url::previous());
	}

	public function actionHideOnReport(int $id) {
		$model = $this->findModel($id);
		if (!$model->hide_on_report) {
			$model->hide_on_report = true;
			$model->updateAttributes(['hide_on_report']);
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('settlement', 'Hide Cost: {value} in Report.', [
				'value' => Yii::$app->formatter->asCurrency($model->getValue()),
			]));
		}
		return $this->redirect(Url::previous());
	}

	public function actionVisibleOnReport(int $id) {
		$model = $this->findModel($id);
		if ($model->hide_on_report) {
			$model->hide_on_report = false;
			$model->updateAttributes(['hide_on_report']);
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('settlement', 'Visible Cost: {value} in Report.', [
				'value' => Yii::$app->formatter->asCurrency($model->getValue()),
			]));
		}
		return $this->redirect(Url::previous());
	}

	/**
	 * Displays a single IssueCost model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueCost model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $id = null, bool $usersFromIssue = true) {
		$model = new IssueCostForm();
		$message = null;
		if ($id) {
			$issue = $this->findIssue($id);
			$model->setIssue($issue);
			$message = new IssueCostMessagesForm();
			$message->setIssue($issue);
			$message->sendEmailToCustomer = false;
			$message->sendSmsToCustomer = false;
			$message->workersTypes = [];
			$message->sms_owner_id = Yii::$app->user->getId();
			$message->addExtraWorkersEmailsIds(User::getAssignmentIds([User::PERMISSION_ISSUE]), false);
		} else {
			$model->setScenario(IssueCostForm::SCENARIO_WITHOUT_ISSUE);
		}

		$model->usersFromIssue = $usersFromIssue;
		$model->date_at = date(DATE_ATOM);
		$model->creator_id = Yii::$app->user->getId();

		if ($model->load(Yii::$app->request->post())
			&& ($message === null || $message->load(Yii::$app->request->post()))
		) {
			if ($model->validate() && ($message === null || $message->validate())) {
				if ($model->save() && $message !== null) {
					$message->setCost($model->getModel());
					$message->pushMessages();
				}
				return $this->redirect(['view', 'id' => $model->getModel()->id]);
			}
			Yii::warning([
				'model' => $model->getErrors(),
				'message' => $message->getErrors(),
			]);
		}

		return $this->render('create', [
			'model' => $model,
			'message' => $message,
		]);
	}

	public function actionSettle(int $id, string $redirectUrl = null) {
		$cost = $this->findModel($id);
		if ($cost->getIsSettled()) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('settlement', 'Warning! Try settle already settled Cost.')
			);
			return $this->redirect('index');
		}
		$model = IssueCostForm::createFromModel($cost);
		$model->setScenario(IssueCostForm::SCENARIO_SETTLE);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect($redirectUrl ?? 'index');
		}

		return $this->render('settle', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueCost model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$cost = $this->findModel($id);
		$model = IssueCostForm::createFromModel($cost);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueCost model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id, string $returnUrl = null) {
		$this->findModel($id)->delete();

		return $this->redirect($returnUrl ?: ['index']);
	}

	/**
	 * Finds the IssueCost model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueCost the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueCost {
		if (($model = IssueCost::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssue(int $id): Issue {
		$model = Issue::find()->andWhere(['id' => $id]);
		if (!Yii::$app->user->can(User::PERMISSION_ARCHIVE)) {
			$model->withoutArchives();
		}
		$model = $model->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
