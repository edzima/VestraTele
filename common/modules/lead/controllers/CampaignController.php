<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\helpers\Url;
use common\models\user\User;
use common\modules\lead\models\forms\LeadCampaignForm;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\searches\LeadCampaignCostSearch;
use common\modules\lead\models\searches\LeadCampaignSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * CampaignController implements the CRUD actions for LeadCampaign model.
 */
class CampaignController extends BaseController {

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
	 * Lists all LeadCampaign models or when Module::onlyUser list all users LeadCampaign models.
	 *
	 * @return mixed
	 */
	public function actionIndex(string $type = LeadCampaign::TYPE_CAMPAIGN): string {
		$searchModel = new LeadCampaignSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadCampaignSearch::SCENARIO_OWNER);
			$searchModel->owner_id = Yii::$app->user->getId();
		}
		$searchModel->type = $type;

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'visibleButtons' => [
				'delete' => $this->module->allowDelete,
			],
		]);
	}

	public function actionAssign(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('leadsIds');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		$model = new LeadCampaignForm();
		$ids = array_unique($ids);
		$model->leadsIds = array_combine($ids, $ids);
		if ($this->module->onlyUser) {
			$model->scenario = LeadCampaignForm::SCENARIO_OWNER;
			$model->ownerId = Yii::$app->user->getId();
		}
		if ($model->load(Yii::$app->request->post())) {
			$count = $model->save();
			if ($count) {
				if ($model->campaignId) {
					Flash::add(Flash::TYPE_SUCCESS,
						Yii::t('lead', 'Success assign {count} Leads to Campaign: {campaign}.', [
							'campaign' => $model->getCampaignNames()[$model->campaignId],
							'count' => $count,
						])
					);
				}
				if ($model->campaignId) {
					Flash::add(Flash::TYPE_SUCCESS,
						Yii::t('lead', '{count} Leads without Campaign.', [
							'campaign' => $model->getCampaignNames()[$model->campaignId],
							'count' => $count,
						])
					);
				}

				return $this->redirect(Url::previous());
			}
		}
		return $this->render('assign', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single LeadCampaign model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id, string $fromAt = null, string $toAt = null): string {
		$model = $this->findModel($id);
		$campaignCost = null;
		if (Yii::$app->user->can(User::PERMISSION_LEAD_COST)) {
			$campaignCost = new LeadCampaignCostSearch();
			$campaignCost->campaignIds = [
				$id,
			];
			$campaignCost->fromAt = $fromAt;
			$campaignCost->toAt = $toAt;

			$campaignCost->load(Yii::$app->request->queryParams);
			if (empty($campaignCost->fromAt) || empty($campaignCost->toAt)) {
				$campaignCost->setDateFromCosts();
				if (empty($campaignCost->fromAt)) {
					$campaignCost->setDateFromLeads();
				}
			}

			if ($this->module->onlyUser) {
				$campaignCost->userId = Yii::$app->user->getId();
				$campaignCost->scenario = LeadCampaignCostSearch::SCENARIO_USER;
			}
			if (Yii::$app->user->can(User::PERMISSION_LEAD_COST)) {
				if ($campaignCost->getLeadsTotalCount()) {
					$cost = $this->module->getCost();
					$data = $cost->recalculateFromDate($campaignCost->fromAt, $campaignCost->toAt, $campaignCost->getCampaignsIds());
				}
			}
		}

		return $this->render('view', [
			'model' => $model,
			'campaignCost' => $campaignCost,
		]);
	}

	/**
	 * Creates a new LeadCampaign model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadCampaign();
		if ($this->module->onlyUser) {
			$model->setScenario(LeadCampaign::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadCampaign model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadCampaign model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadCampaign model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return LeadCampaign the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LeadCampaign {
		$model = LeadCampaign::findOne($id);
		if ($model !== null) {
			if (!$this->module->onlyUser
				|| ($model->owner_id !== null && $model->owner_id === (int) Yii::$app->user->getId())) {
				return $model;
			}
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
