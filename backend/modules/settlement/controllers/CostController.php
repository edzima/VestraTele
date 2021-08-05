<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\settlement\models\DebtCostsForm;
use backend\modules\settlement\models\IssueCostForm;
use backend\modules\settlement\models\search\IssueCostSearch;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
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

	/**
	 * Create Debt Costs for Issue
	 *
	 * @param int $issue_id
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionCreateDebt(int $issue_id) {
		$model = new DebtCostsForm($this->findIssue($issue_id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue', 'id' => $issue_id]);
		}
		return $this->render('create-debt', [
			'model' => $model,
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
	public function actionCreate(int $id) {
		$issue = $this->findIssue($id);
		$model = new IssueCostForm($issue);
		$model->date_at = date(DATE_ATOM);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateInstallment(int $id, int $user_id = null) {
		$issue = $this->findIssue($id);
		$model = new IssueCostForm($issue);
		$model->setScenario(IssueCostForm::SCENARIO_CREATE_INSTALLMENT);
		$model->user_id = $user_id;
		$model->type = IssueCost::TYPE_INSTALLMENT;
		$model->date_at = date(DATE_ATOM);
		$model->vat = 0;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create-installment', [
			'model' => $model,
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
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
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
