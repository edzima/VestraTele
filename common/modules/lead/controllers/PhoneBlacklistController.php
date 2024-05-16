<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\LeadPhoneBlacklist;
use common\modules\lead\models\searches\LeadPhoneBlacklistSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PhoneBlacklistController implements the CRUD actions for LeadPhoneBlacklist model.
 */
class PhoneBlacklistController extends BaseController {

	public ?bool $allowDelete = true;

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
						'create' => ['POST'],
						'delete' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all LeadPhoneBlacklist models.
	 *
	 * @return string
	 */
	public function actionIndex() {
		$searchModel = new LeadPhoneBlacklistSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new LeadPhoneBlacklist model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate(string $phone, string $returnUrl) {

		$model = new LeadPhoneBlacklist();
		$model->user_id = Yii::$app->user->getId();
		$model->phone = $phone;
		if ($model->save()) {
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Add phone: {phone} to blacklist. Blocked from sending new SMS.', [
				'phone' => $model->phone,
			]));
		}
		return $this->redirect($returnUrl);
	}

	/**
	 * Deletes an existing LeadPhoneBlacklist model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param string $phone
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(string $phone, string $returnUrl = null) {
		$this->findModel($phone)->delete();

		return $this->redirect($returnUrl ? $returnUrl : ['index']);
	}

	/**
	 * Finds the LeadPhoneBlacklist model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $phone
	 * @return LeadPhoneBlacklist the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(string $phone): LeadPhoneBlacklist {
		if (($model = LeadPhoneBlacklist::findOne(['phone' => $phone])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
