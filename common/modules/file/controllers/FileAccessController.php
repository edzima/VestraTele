<?php

namespace common\modules\file\controllers;

use common\modules\file\models\FileAccess;
use common\modules\file\models\search\FileAccessSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * FileAccessController implements the CRUD actions for FileAccess model.
 */
class FileAccessController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors() {
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
	 * Lists all FileAccess models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new FileAccessSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single FileAccess model.
	 *
	 * @param int $file_id File ID
	 * @param int $user_id User ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($file_id, $user_id) {
		return $this->render('view', [
			'model' => $this->findModel($file_id, $user_id),
		]);
	}

	/**
	 * Creates a new FileAccess model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new FileAccess();

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				return $this->redirect(['view', 'file_id' => $model->file_id, 'user_id' => $model->user_id]);
			}
		} else {
			$model->loadDefaultValues();
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing FileAccess model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $file_id File ID
	 * @param int $user_id User ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($file_id, $user_id) {
		$model = $this->findModel($file_id, $user_id);

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'file_id' => $model->file_id, 'user_id' => $model->user_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing FileAccess model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $file_id File ID
	 * @param int $user_id User ID
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($file_id, $user_id) {
		$this->findModel($file_id, $user_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the FileAccess model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $file_id File ID
	 * @param int $user_id User ID
	 * @return FileAccess the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($file_id, $user_id) {
		if (($model = FileAccess::findOne(['file_id' => $file_id, 'user_id' => $user_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('file', 'The requested page does not exist.'));
	}
}
