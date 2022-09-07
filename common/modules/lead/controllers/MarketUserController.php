<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\helpers\Url;
use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\modules\lead\models\forms\LeadMarketAccessResponseForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\searches\LeadMarketUserSearch;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MarketUserController implements the CRUD actions for LeadMarketUser model.
 */
class MarketUserController extends BaseController {

	public ?bool $allowDelete = true;

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'give-up' => ['POST'],
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
		if ($this->module->onlyUser) {
			$searchModel->scenario = LeadMarketUserSearch::SCENARIO_USER;
			$searchModel->user_id = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionSelfMarket(): string {
		$searchModel = new LeadMarketUserSearch();
		$searchModel->marketCreatorId = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$searchModel->scenario = LeadMarketUserSearch::SCENARIO_USER_MARKET;

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionSelf(): string {
		$searchModel = new LeadMarketUserSearch();
		$searchModel->user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$searchModel->scenario = LeadMarketUserSearch::SCENARIO_USER;

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
		$this->checkIsCreatorOrOwnerLead($model);
		$responseForm = new LeadMarketAccessResponseForm($model);
		$type = $responseForm->accept();
		if ($type) {
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead',
				'Success Reserved Lead Market to: {reserved_at}', [
					'reserved_at' => Yii::$app->formatter->asDate($model->reserved_at),
				])
			);
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead',
				'Assign User: {user} as {typeName} to Lead: {leadName}.', [
					'leadName' => $model->market->lead->getName(),
					'user' => $model->user->getFullName(),
					'typeName' => LeadMarketAccessResponseForm::getLinkedUserTypeName($type),
				])
			);
			$responseForm->sendAcceptEmail();
		} elseif ($model->isWaiting()) {
			$responseForm->sendWaitingEmail();
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Access Request cannot be Accepted. Market is Already Reserved.')
			);
		}
		return $this->redirect(['market/view', 'id' => $market_id]);
	}

	public function actionReject(int $market_id, int $user_id): Response {
		$model = $this->findModel($market_id, $user_id);
		if (!$model->isToConfirm()) {
			throw new NotFoundHttpException('Model is not to Confirm');
		}
		$this->checkIsCreatorOrOwnerLead($model);

		$responseForm = new LeadMarketAccessResponseForm($model);
		$responseForm->reject();
		$responseForm->sendRejectEmail();
		return $this->redirect(['market/view', 'id' => $market_id]);
	}

	public function actionGiveUp(int $market_id): Response {
		$model = $this->findModel($market_id, Yii::$app->user->getId());
		if (!$model->isAllowGiven()) {
			throw new MethodNotAllowedHttpException('This Access Request cannot be given up.');
		}

		$responseForm = new LeadMarketAccessResponseForm($model);
		$responseForm->giveUp();
		$this->module->getMarket()->expireProcess($model->market);
		Flash::add(Flash::TYPE_SUCCESS,
			Yii::t('lead', 'Your Access Request is Given Up.')
		);
		return $this->redirect(['market/view', 'id' => $market_id]);
	}

	/**
	 * Creates a new LeadMarketUser model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionAccessRequest(int $market_id, int $days = LeadMarketAccessRequest::DEFAULT_DAYS, string $returnUrl = null) {
		$market = LeadMarket::findOne($market_id);
		if ($market === null) {
			throw new NotFoundHttpException();
		}
		if (!$market->userCanAccessRequest(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('You cant access request for this market.');
		}
		$model = new LeadMarketAccessRequest();

		try {
			$model->setMarket($market);
		} catch (InvalidArgumentException $exception) {
			throw new NotFoundHttpException($exception->getMessage());
		}
		$model->days = $days;
		$model->user_id = Yii::$app->user->getId();

		if (Yii::$app->request->isPost) {
			$model->load(Yii::$app->request->post());
		}
		if ($model->save()) {
			if ($model->getModel()->isToConfirm()) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Your Access Request is waiting for Accept. You will be notify by Email.')
				);
				$model->sendEmail();
			}
			if ($returnUrl === null) {
				$returnUrl = Url::toRoute(['market/view', 'id' => $market_id]);
			}
			return $this->redirect($returnUrl);
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
	public function actionDelete(int $market_id) {
		$model = $this->findModel($market_id, Yii::$app->user->getId());
		if ($model->isToConfirm()) {
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

	/**
	 * @param LeadMarketUser $model
	 * @return void
	 * @throws MethodNotAllowedHttpException
	 */
	private function checkIsCreatorOrOwnerLead(LeadMarketUser $model): void {
		if (!$model->market->isCreatorOrOwnerLead(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('Only Lead Owner or Market Creator make this Action.');
		}
	}
}
