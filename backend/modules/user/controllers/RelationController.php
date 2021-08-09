<?php

namespace backend\modules\user\controllers;

use Yii;
use common\models\user\UserRelation;
use backend\modules\user\models\search\RelationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RelationController implements the CRUD actions for UserRelation model.
 */
class RelationController extends Controller {

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
	 * Lists all UserRelation models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new RelationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single UserRelation model.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $user_id, int $to_user_id, string $type) {
		return $this->render('view', [
			'model' => $this->findModel($user_id, $to_user_id, $type),
		]);
	}

	/**
	 * Creates a new UserRelation model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new UserRelation();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id, 'type' => $model->type]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing UserRelation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $user_id, int $to_user_id, string $type) {
		$model = $this->findModel($user_id, $to_user_id, $type);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id, 'type' => $model->type]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing UserRelation model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $user_id, int $to_user_id, string $type) {
		$this->findModel($user_id, $to_user_id, $type)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the UserRelation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @param string $type
	 * @return UserRelation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $user_id, int $to_user_id, string $type): UserRelation {
		if (($model = UserRelation::findOne(['user_id' => $user_id, 'to_user_id' => $to_user_id, 'type' => $type])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
	}
}
