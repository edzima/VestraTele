<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\search\UserSearch;
use backend\modules\user\models\UserForm;
use common\helpers\Flash;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller {

	public $searchModel = UserSearch::class;
	public $formModel = UserForm::class;
	public $model = User::class;

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
	 * Lists all User models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new $this->searchModel();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate() {
		/** @var UserForm $model */
		$model = new $this->formModel();
		$model->setScenario(UserForm::SCENARIO_CREATE);

		if (
			$model->load(Yii::$app->request->post())
			&& $model->validate()
			&& $model->acceptDuplicates()
			&& $model->save(false)
		) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('backend', 'Created Account: {user}.', [
					'user' => $model->getModel()->getFullName(),
				])
			);
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing User model.
	 * If update is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		/** @var UserForm $model */
		$model = new $this->formModel();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Deletes an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		if ($id == Yii::$app->user->id) {
			Yii::$app->session->setFlash('error', Yii::t('backend', 'You can not remove your own account.'));
		} else {
			$this->findModel($id)->delete();
			Yii::$app->authManager->revokeAll($id);
			Yii::$app->session->setFlash('success', Yii::t('backend', 'User has been deleted.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): User {
		if (($model = $this->model::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
