<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\PayCalculationForm;
use common\models\issue\Issue;
use common\models\User;
use Yii;
use common\models\issue\IssuePayCalculation;
use backend\modules\issue\models\searches\IssuePayCalculationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayCalculationController implements the CRUD actions for IssuePayCalculation model.
 */
class PayCalculationController extends Controller {

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

	public function beforeAction($action) {
		if (!Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		return parent::beforeAction($action);
	}

	/**
	 * Lists all IssuePayCalculation models.
	 *
	 * @param bool $onlyNew
	 * @param int $status
	 * @return mixed
	 */
	public function actionIndex(int $status = IssuePayCalculationSearch::STATUS_ACTIVE, bool $onlyNew = false) {
		$searchModel = new IssuePayCalculationSearch(['isOnlyNew' => $onlyNew, 'status' => $status]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
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
	public function actionView(int $id) {
		$model = IssuePayCalculation::findOne($id);
		if ($model === null) {
			return $this->redirect(['create', 'id' => $id]);
		}

		return $this->render('view', [
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
	public function actionCreate(int $id) {
		if (IssuePayCalculation::findOne($id) !== null) {
			return $this->redirect(['update', 'id' => $id]);
		}

		$model = new PayCalculationForm($this->findIssueModel($id, true));
		if ($model->load(Yii::$app->request->post()) && (!$model->isGenerate()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getPayCalculation()->issue_id]);
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
		$model = new PayCalculationForm($this->findIssueModel($id, false));

		if ($model->load(Yii::$app->request->post()) && (!$model->isGenerate()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getPayCalculation()->issue_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssuePayCalculation model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssuePayCalculation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePayCalculation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @param bool $onlyPositiveDecision
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssueModel(int $id, bool $onlyPositiveDecision): Issue {
		$query = Issue::find()
			->where(['id' => $id]);
		if ($onlyPositiveDecision) {
			$query->onlyPositiveDecision();
		}
		if (($model = $query
				->one()) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
