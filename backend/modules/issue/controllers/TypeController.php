<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueStageChangeForm;
use backend\modules\issue\models\IssuesUpdateTypeMultiple;
use backend\modules\issue\models\IssueTypeForm;
use backend\modules\issue\models\IssueTypePermissionForm;
use backend\modules\issue\models\search\IssueTypeSearch;
use common\helpers\Flash;
use common\models\issue\IssueType;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function array_key_first;

/**
 * TypeController implements the CRUD actions for IssueType model.
 */
class TypeController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'stages-list' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssueType models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new IssueTypeSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdateMultiple(array $ids = []) {
		if (empty($ids)) {
			$ids = IssueController::getSelectionSearchIds();
		}

		$model = new IssuesUpdateTypeMultiple();
		$model->ids = $ids;

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$count = $model->update(false);
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('backend', 'Change {count} to Type: {type}.', [
					'count' => $count,
					'type' => IssueType::getTypesNames()[$model->typeId],
				]));
			}
			return $this->redirect(['issue/index']);
		}
		return $this->render('update-multiple', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single IssueType model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new IssueTypeForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionPermission(int $id) {
		$model = new IssueTypePermissionForm();
		$model->setModel($this->findModel($id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->typeId]);
		}
		return $this->render('permission', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$model = new IssueTypeForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueType model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	public function actionStagesList(int $stageId = null): array {
		$params = Yii::$app->request->post('depdrop_parents');
		if (empty($params)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		Yii::$app->response->format = Response::FORMAT_JSON;
		$id = (int) reset($params);

		$stages = IssueStageChangeForm::getStagesNames($id);
		$output = [];
		foreach ($stages as $id => $name) {
			$output[] = [
				'id' => $id,
				'name' => $name,
			];
		}
		$selected = $stageId;
		if (!isset($stages[$stageId])) {
			$selected = array_key_first($stages);
		}

		return [
			'output' => $output,
			'selected' => $selected,
		];
	}

	/**
	 * Finds the IssueType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueType {
		if (($model = IssueType::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
