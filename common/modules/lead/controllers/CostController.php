<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\forms\LeadCostForm;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\searches\LeadCostSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CostController implements the CRUD actions for LeadCost model.
 */
class CostController extends BaseController {

	/**
	 * @inheritDoc
	 */
	public function behaviors(): array {
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'delete' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all LeadCost models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new LeadCostSearch();
		if ($this->module->onlyUser) {
			$searchModel->scenario = LeadCostSearch::SCENARIO_USER;
			$searchModel->userId = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single LeadCost model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new LeadCost model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new LeadCostForm();
		if ($this->module->onlyUser) {
			$model->scenario = LeadCostForm::SCENARIO_USER;
			$model->userId = Yii::$app->user->getId();
		}
		$model->date_at = time();

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->getModel()->id]);
			}
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadCost model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LeadCostForm();
		if ($this->module->onlyUser) {
			$model->scenario = LeadCostForm::SCENARIO_USER;
			$model->userId = Yii::$app->user->getId();
		}
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadCost model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id ID
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadCost model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return LeadCost the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = LeadCost::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}