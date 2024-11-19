<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\CostTypeForm;
use common\components\rbac\form\ActionsAccessForm;
use common\components\rbac\form\SingleActionAccessForm;
use common\models\settlement\CostType;
use common\models\settlement\search\CostTypeSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CostTypeController implements the CRUD actions for CostType model.
 */
class CostTypeController extends Controller {

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

	public function actionSingleAccess(int $id, string $app, string $action) {
		$type = $this->findModel($id);
		$manager = $type->getModelAccess()
			->setApp($app)
			->setAction($action);
		$model = new SingleActionAccessForm($manager);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('single-access', [
			'model' => $model,
			'type' => $type,
		]);
	}

	public function actionAccess(int $id) {
		$model = new ActionsAccessForm();
		$type = $this->findModel($id);
		$model->setAccess($type->getModelAccess());
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('access', [
			'model' => $model,
			'type' => $type,
		]);
	}

	/**
	 * Lists all CostType models.
	 *
	 * @return string
	 */
	public function actionIndex() {
		$searchModel = new CostTypeSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single CostType model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new CostType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new CostTypeForm();

		if ($model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing CostType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new CostTypeForm();
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing CostType model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id ID
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the CostType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return CostType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): CostType {
		if (($model = CostType::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('settlement', 'The requested page does not exist.'));
	}
}
