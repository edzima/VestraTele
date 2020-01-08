<?php

namespace backend\modules\provision\controllers;

use backend\modules\provision\models\ProvisionUserForm;
use common\models\provision\ProvisionType;
use common\models\User;
use Yii;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for ProvisionUser model.
 */
class UserController extends Controller {

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
	 * Lists all ProvisionUser models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ProvisionUserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUser(int $id, array $typesIds = []) {
		$model = new ProvisionUserForm($this->findUser($id));
		if (!empty($typesIds)) {
			$types = ProvisionType::find()->andWhere(['id' => $typesIds])->all();
			$model->setTypes($types);
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('index');
		}

		return $this->render('user', [
			'model' => $model,
		]);
	}

	private function findUser(int $id): User {
		$user = User::findOne($id);
		if ($user === null) {
			throw new NotFoundHttpException();
		}
		return $user;
	}

	/**
	 * Deletes an existing ProvisionUser model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $from_user_id
	 * @param integer $to_user_id
	 * @param integer $type_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($from_user_id, $to_user_id, $type_id) {
		$this->findModel($from_user_id, $to_user_id, $type_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the ProvisionUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $from_user_id
	 * @param integer $to_user_id
	 * @param integer $type_id
	 * @return ProvisionUser the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($from_user_id, $to_user_id, $type_id): ProvisionUser {
		if (($model = ProvisionUser::findOne(['from_user_id' => $from_user_id, 'to_user_id' => $to_user_id, 'type_id' => $type_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
