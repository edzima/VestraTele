<?php

namespace backend\modules\settlement\controllers;

use backend\modules\settlement\models\SettlementTypeForm;
use common\components\rbac\form\ModelActionsForm;
use common\components\rbac\SettlementTypeAccess;
use common\models\settlement\search\SettlementTypeSearch;
use common\models\settlement\SettlementType;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * TypeController implements the CRUD actions for SettlementType model.
 */
class TypeController extends Controller {

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

	public function actionAccess(?int $id = null) {
		$model = new ModelActionsForm();
		$model->setAccess(new SettlementTypeAccess());

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($id) {
				return $this->redirect(['view', 'id' => $id]);
			}
			return $this->redirect(['index']);
		}
		return $this->render('access', [
			'model' => $model,
		]);
	}

	/**
	 * Lists all SettlementType models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new SettlementTypeSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single SettlementType model.
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
	 * Creates a new SettlementType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new SettlementTypeForm();

		if ($model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing SettlementType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new SettlementTypeForm();
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing SettlementType model.
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
	 * Finds the SettlementType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return SettlementType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): SettlementType {
		if (($model = SettlementType::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('settlement', 'The requested page does not exist.'));
	}
}
