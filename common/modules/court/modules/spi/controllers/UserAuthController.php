<?php

namespace common\modules\court\modules\spi\controllers;

use common\helpers\Flash;
use common\modules\court\modules\spi\components\exceptions\SPIApiException;
use common\modules\court\modules\spi\models\SpiUserAuth;
use common\modules\court\modules\spi\models\SpiUserAuthForm;
use common\modules\court\modules\spi\models\UserAuthSearch;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserAuthController implements the CRUD actions for SpiUserAuth model.
 *
 * @property Module $module
 */
class UserAuthController extends Controller {

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
	 * Lists all SpiUserAuth models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new UserAuthSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUser(int $id) {
		$model = SpiUserAuth::findByUserId($id);
		if ($model) {
			return $this->redirect(['view', 'id' => $model->id]);
		}
		return $this->redirect(['create', 'user_id' => $id]);
	}

	/**
	 * Displays a single SpiUserAuth model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new SpiUserAuth model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate(int $user_id) {
		$model = new SpiUserAuthForm($this->module->userAuthApiPasswordKey);
		$model->user_id = $user_id;
		$model->scenario = SpiUserAuthForm::SCENARIO_CREATE;
		if ($model->getModel()->user === null) {
			throw new NotFoundHttpException();
		}

		if ($model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing SpiUserAuth model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new SpiUserAuthForm($this->module->userAuthApiPasswordKey);
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionCheckAuth(int $id) {
		$model = $this->findModel($id);
		$api = $this->module->getSpiApi();
		$api->username = $model->username;
		$api->password = $model->decryptPassword($this->module->userAuthApiPasswordKey);
		try {
			if ($api->authenticate()) {
				Flash::add(
					Flash::TYPE_SUCCESS,
					Yii::t('spi', 'Success API Authentication')
				);
			} else {
				Flash::add(
					Flash::TYPE_WARNING,
					Yii::t('spi', 'Problem with API Authentication')
				);
			}
		} catch (SPIApiException $apiException) {
			Flash::add(
				Flash::TYPE_ERROR,
				$apiException->getMessage()
			);
		}
		return $this->redirect(['view', 'id' => $id]);
	}

	/**
	 * Deletes an existing SpiUserAuth model.
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
	 * Finds the SpiUserAuth model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return SpiUserAuth the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): SpiUserAuth {
		if (($model = SpiUserAuth::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('spi', 'The requested page does not exist.'));
	}
}
