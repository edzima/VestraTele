<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\searches\LeadMarketUserSearch;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * MarketUserController implements the CRUD actions for LeadMarketUser model.
 */
class MarketUserController extends BaseController {

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
	 * Lists all LeadMarketUser models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new LeadMarketUserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionAccept(int $market_id, int $user_id) {
		$model = $this->findModel($market_id, $user_id);
		if ($model->isAccepted()) {
			throw new NotFoundHttpException('Model is already accepted.');
		}
		if (!$model->market->isCreatorOrOwnerLead(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('Only Lead Owner or Market Creator can Accepted.');
		}
		$model->accept();
		Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead',
			'Success Reserved Lead Market to: {reserved_at}', [
				'reserved_at' => Yii::$app->formatter->asDate($model->reserved_at),
			])
		);
		return $this->redirect(['market/view', 'market_id' => $market_id, 'user_id' => $user_id]);
	}

	public function actionReject(int $market_id, int $user_id) {
		$model = $this->findModel($market_id, $user_id);
		if (!$model->isToConfirm()) {
			throw new NotFoundHttpException('Model is not to Confirm');
		}
		if (!$model->market->isCreatorOrOwnerLead(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('Only Lead Owner or Market Creator can Rejected.');
		}
		$model->reject();
		return $this->redirect(['market/view', 'market_id' => $market_id, 'user_id' => $user_id]);
	}

	/**
	 * Displays a single LeadMarketUser model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $market_id, int $user_id): string {
		return $this->render('view', [
			'model' => $this->findModel($market_id, $user_id),
		]);
	}

	/**
	 * Creates a new LeadMarketUser model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionAccessRequest(int $market_id) {
		$market = LeadMarket::findOne($market_id);
		if ($market === null) {
			throw new NotFoundHttpException();
		}
		$model = new LeadMarketAccessRequest();
		try {
			$model->setMarket($market);
		} catch (InvalidArgumentException $exception) {
			throw new NotFoundHttpException($exception->getMessage());
		}
		$model->user_id = Yii::$app->user->getId();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['market/view', 'id' => $market_id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadMarketUser model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $market_id
	 * @param int $user_id
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $market_id, int $user_id) {
		$model = $this->findModel($market_id, $user_id);
		if ($model->isToConfirm() && Yii::$app->user->getId() === $user_id) {
			$model->delete();
		} else {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Allow Delete only self Model who is to Confirm.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadMarketUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $market_id
	 * @return LeadMarketUser the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $market_id, int $user_id): LeadMarketUser {
		if (($model = LeadMarketUser::findOne([
				'market_id' => $market_id,
				'user_id' => $user_id,
			])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
