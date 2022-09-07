<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\forms\LeadMarketForm;
use common\modules\lead\models\forms\LeadMarketMultipleForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\searches\LeadMarketSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * MarketController implements the CRUD actions for LeadMarket model.
 */
class MarketController extends BaseController {

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
				],
			],
		];
	}

	public function actionUser(int $regionId = null, bool $withoutCity = false): string {
		$searchModel = new LeadMarketSearch();
		if ($withoutCity) {
			$searchModel->addressSearch = null;
			$searchModel->withoutCity = true;
			$regionId = null;
		} elseif ($regionId !== null) {
			$searchModel->addressSearch->region_id = $regionId;
		}

		$searchModel->userId = Yii::$app->user->getId();
		$searchModel->withoutArchive = true;
		$searchModel->selfMarket = 0;
		$searchModel->selfAssign = 0;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setModels($searchModel->filterAddressOptions($dataProvider->getModels()));

		return $this->render('user', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all LeadMarket models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		if ($this->module->onlyUser) {
			return $this->redirect(['user']);
		}
		$searchModel = new LeadMarketSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single LeadMarket model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
			'onlyUser' => $this->module->onlyUser,
		]);
	}

	/**
	 * Creates a new LeadMarket model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $id) {
		$lead = $this->findLead($id);
		if ($lead->market !== null) {
			return $this->redirect(['update', 'id' => $lead->market->id]);
		}
		$model = new LeadMarketForm();
		$model->lead_id = $lead->getId();
		$model->status = LeadMarket::STATUS_NEW;
		$model->creator_id = Yii::$app->user->getId();

		if ($model->load(Yii::$app->request->post())
			&& $model->save()
			&& $model->saveReport(false)
		) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		if ($model->hasErrors('lead_id')) {
			Flash::add(Flash::TYPE_WARNING, $model->getFirstError('lead_id'));
			return $this->redirectLead($id);
		}

		return $this->render('create', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	public function actionCreateMultiple(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('leadsIds');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING, 'Ids cannot be blank.');
			return $this->redirect(['lead/index']);
		}
		if (count($ids) === 1) {
			$id = reset($ids);
			return $this->redirect(['create', 'id' => $id]);
		}
		$ids = array_unique($ids);
		$model = new LeadMarketMultipleForm();
		$model->status = LeadMarket::STATUS_NEW;
		$model->creator_id = Yii::$app->user->getId();
		$model->leadsIds = $ids;
		if ($model->load(Yii::$app->request->post())
			&& ($count = $model->save()) > 0
			&& $model->saveReports(false)
		) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success Move {count} Leads to Market', [
					'count' => $count,
				]));
			return $this->redirect(['lead/index']);
		}
		if (Yii::$app->request->isPost && empty($model->leadsIds)) {
			if ($model->withoutAddressFilter && $model->getWithoutAddressCount()) {
				Flash::add(Flash::TYPE_WARNING, Yii::t('lead', '{count} Leads without Address - is required for Market.', [
					'count' => $model->getWithoutAddressCount(),
				]));
			} else {
				Flash::add(Flash::TYPE_WARNING, Yii::t('lead', 'All Leads are already on Market'));
			}

			return $this->redirect(['lead/index']);
		}

		return $this->render('create-multiple', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadMarket model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$market = $this->findModel($id);
		if ($this->module->onlyUser && !$market->isCreatorOrOwnerLead(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('Only User or Market Creator can Edit Market.');
		}
		$model = new LeadMarketForm([
			'model' => $this->findModel($id),
		]);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadMarket model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		if ($this->module->onlyUser && !$model->isCreatorOrOwnerLead(Yii::$app->user->getId())) {
			throw new MethodNotAllowedHttpException('Only User or Market Creator can Delete Market.');
		}
		$model->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadMarket model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return LeadMarket the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LeadMarket {
		if (($model = LeadMarket::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
