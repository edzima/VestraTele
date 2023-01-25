<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\search\UserVisibleSearch;
use common\helpers\ArrayHelper;
use common\models\user\User;
use common\models\user\UserVisible;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * VisibleController implements the CRUD actions for UserVisible model.
 */
class VisibleController extends Controller {

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
	 * Lists all UserVisible models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new UserVisibleSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single UserVisible model.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $user_id, int $to_user_id) {
		return $this->render('view', [
			'model' => $this->findModel($user_id, $to_user_id),
		]);
	}

	/**
	 * Finds the UserVisible model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @return UserVisible the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $user_id, int $to_user_id): UserVisible {
		if (($model = UserVisible::findOne(['user_id' => $user_id, 'to_user_id' => $to_user_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
	}

	/**
	 * Creates a new UserVisible model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $user_id = null) {
		$model = new UserVisible();
		$model->user_id = $user_id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id]);
		}

		return $this->render('create', [
			'model' => $model,
			'user' => $user_id !== null ? User::findOne($user_id) : null,
			'users' => ArrayHelper::map(
				Worker::find()
					->with('userProfile')
					->all(), 'id', 'fullName'),
		]);
	}

	/**
	 * Updates an existing UserVisible model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $user_id, int $to_user_id) {
		$model = $this->findModel($user_id, $to_user_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id]);
		}

		return $this->render('update', [
			'model' => $model,
			'users' => ArrayHelper::map(
				Worker::find()
					->with('userProfile')
					->all(), 'id', 'fullName'),
		]);
	}

	/**
	 * Deletes an existing UserVisible model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $user_id
	 * @param integer $to_user_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $user_id, int $to_user_id) {
		$this->findModel($user_id, $to_user_id)->delete();

		return $this->redirect(['index']);
	}
}
