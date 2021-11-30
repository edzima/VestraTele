<?php

namespace common\modules\lead\controllers;

use backend\widgets\CsvForm;
use common\helpers\Flash;
use common\helpers\Url;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;

/**
 * LeadController implements the CRUD actions for Lead model.
 */
class LeadController extends BaseController {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'copy' => ['POST'],
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all Lead models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		if (Yii::$app->request->post('selection')
			&& !empty(Yii::$app->request->post('route'))) {
			Url::remember();
			return $this->redirect([
				Yii::$app->request->post('route'),
				'ids' => Yii::$app->request->post('selection'),
			]);
		}
		$searchModel = new LeadSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination->defaultPageSize = 50;

		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			$query = $dataProvider->query;
			$columns = [
				'name',
				'phone',
				'email',
			];
			$addressSearch = $searchModel->addressSearch;
			if (!empty($addressSearch->region_id)
				|| !empty($addressSearch->city_name)
				|| !empty($addressSearch->postal_code)) {
				$query->joinWith('addresses.address.city.terc');
				$columns = array_merge($columns, [
					[
						'attribute' => 'customerAddress.city.region.name',
						'label' => Yii::t('address', 'Region'),
					],
					[
						'attribute' => 'customerAddress.city.terc.district.name',
						'label' => Yii::t('address', 'District'),
					],
					[
						'attribute' => 'customerAddress.city.terc.commune.name',
						'label' => Yii::t('address', 'Commune'),
					],
					[
						'attribute' => 'customerAddress.postal_code',
						'label' => Yii::t('address', 'Code'),
					],
					[
						'attribute' => 'customerAddress.city.name',
						'label' => Yii::t('address', 'City'),
					],
					[
						'attribute' => 'customerAddress.info',
						'label' => Yii::t('address', 'Info'),
					],
				]);
			}
			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => $columns,
			]);
			return $exporter->export()->send('lead.csv');
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'assignUsers' => !$this->module->onlyUser,
			'visibleButtons' => [
				'delete' => $this->module->allowDelete,
			],
		]);
	}

	public function actionPhone() {
		$model = new LeadPhoneSearch();
		$dataProvider = $model->search(Yii::$app->request->queryParams);
		if (empty(Yii::$app->request->queryParams)) {
			$model->clearErrors();
		}
		return $this->render('phone', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Lead model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		$model = $this->findLead($id);
		$sameContacts = $model->getSameContacts();
		if (!empty($sameContacts)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Find Similars Leads: {count}.', [
					'count' => count($sameContacts),
				])
			);
		}
		return $this->render('view', [
			'model' => $model,
			'sameContacts' => $sameContacts,
			'withDelete' => $this->module->allowDelete,
		]);
	}

	/**
	 * Creates a new Lead model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(string $phone = null) {
		$model = new LeadForm();
		$model->phone = $phone;
		$model->date_at = date($model->dateFormat);
		if ($this->module->onlyUser) {
			$model->setScenario(LeadForm::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead = $this->module->manager->pushLead($model);
			if ($lead) {
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success create Lead.'));
				return $this->redirect(['view', 'id' => $lead->getId()]);
			}
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateFromSource(int $id, string $phone = null) {
		$model = new LeadForm();
		$model->source_id = $id;
		$model->phone = $phone;
		if ($this->module->onlyUser) {
			$model->setScenario(LeadForm::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}
		if (!$model->validate(['source_id'])) {
			throw new NotFoundHttpException();
		}
		$model->date_at = date($model->dateFormat);
		$source = LeadSource::getModels()[$id];
		$report = new ReportForm(['source' => $source]);
		$report->status_id = LeadStatusInterface::STATUS_NEW;
		$report->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post())
			&& $report->load(Yii::$app->request->post())
			&& $model->validate()
			&& $report->validate()) {
			$lead = $this->module->manager->pushLead($model);
			if ($lead) {
				$report->setLead($lead);
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success create Lead.'));
				$report->save(false);
				return $this->redirect(['view', 'id' => $lead->getId()]);
			}
		}
		return $this->render('create-from-source', [
			'model' => $model,
			'report' => $report,
		]);
	}

	public function actionCopy(int $id) {
		$lead = $this->findLead($id, false);
		if ($lead->isForUser(Yii::$app->user->getId())) {
			Flash::add(Flash::TYPE_WARNING, Yii::t('lead', 'Only not self Lead can Copy.'));
			return $this->redirect(['index']);
		}
		$model = new LeadForm();
		$model->setLead($lead);
		$model->owner_id = Yii::$app->user->getId();
		$lead = $this->module->manager->pushLead($model);
		if ($lead) {
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success Copy Lead.'));
			return $this->redirect(['view', 'id' => $lead->getId()]);
		}
		return $this->redirect(Url::previous());
	}

	/**
	 * Updates an existing Lead model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LeadForm();
		$lead = $this->findLead($id);
		$model->setLead($lead);

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead->setLead($model);
			$lead->update();
			return $this->redirect(['view', 'id' => $lead->getId()]);
		}

		return $this->render('update', [
			'id' => $id,
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Lead model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findLead($id)->delete();

		return $this->redirect(['index']);
	}

}
