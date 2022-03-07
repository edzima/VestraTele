<?php

namespace common\modules\lead\controllers;

use backend\widgets\CsvForm;
use common\behaviors\SelectionRouteBehavior;
use common\helpers\Flash;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
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
					'delete' => ['POST'],
				],
			],
			'selection' => [
				'class' => SelectionRouteBehavior::class,
			],
		];
	}

	/**
	 * Lists all Lead models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
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
		$sameContactsCount = count($model->getSameContacts());

		if ($sameContactsCount > 0) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Find Similars Leads: {count}.', [
					'count' => $sameContactsCount,
				])
			);
		}

		return $this->render('view', [
			'model' => $model,
			'withDelete' => $this->module->allowDelete,
			'onlyUser' => $this->module->onlyUser,
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
		if ($this->module->onlyUser) {
			$model->setScenario(LeadForm::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}
		if (!$model->validate(['source_id'])) {
			throw new NotFoundHttpException();
		}
		$model->phone = $phone;
		$model->date_at = date($model->dateFormat);
		$source = LeadSource::getModels()[$id];
		$report = new ReportForm();
		$report->lead_type_id = $source->getType()->getID();
		$report->status_id = LeadStatusInterface::STATUS_NEW;
		$report->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post())
			&& $report->load(Yii::$app->request->post())
			&& $model->validate()
			&& $report->validate()) {
			$lead = $this->module->manager->pushLead($model);
			if ($lead) {
				$report->setLead($lead, false);
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

	public function actionCopy(int $id, int $typeId) {
		if (!isset(LeadType::getModels()[$typeId])) {
			throw new NotFoundHttpException();
		}
		$type = LeadType::getModels()[$typeId];
		$lead = $this->findLead($id, false);
		$model = new LeadForm();
		$model->scenario = LeadForm::SCENARIO_OWNER;
		$model->setLead($lead);
		$model->typeId = $typeId;
		$model->status_id = LeadStatusInterface::STATUS_NEW;
		$model->provider = Lead::PROVIDER_COPY;
		$model->date_at = date($model->dateFormat);
		$model->owner_id = Yii::$app->user->getId();
		$report = new ReportForm();
		$report->setLead($lead, true);
		$report->withSameContacts = false;
		$report->leadName = $lead->getName();
		$report->lead_type_id = $typeId;
		$report->status_id = LeadStatusInterface::STATUS_NEW;
		$report->owner_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post())
			&& $report->load(Yii::$app->request->post())
			&& $model->validate()
			&& $report->validate()) {
			$newLead = $this->module->manager->pushLead($model);
			if ($newLead) {
				$parentLeadReport = new LeadReport([
					'lead_id' => $lead->getId(),
					'old_status_id' => $lead->getStatusId(),
					'status_id' => $lead->getStatusId(),
					'details' => Yii::t('lead', 'Copied Lead: {id} from this', [
						'id' => $newLead->getId(),
					]),
					'owner_id' => Yii::$app->user->getId(),
				]);
				$parentLeadReport->save();
				$report->setLead($newLead, false);
				$report->save(false);
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success Copy Lead.'));
				return $this->redirect(['view', 'id' => $newLead->getId()]);
			}
		}
		return $this->render('copy', [
			'lead' => $lead,
			'model' => $model,
			'type' => $type,
			'report' => $report,
		]);
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
		if ($this->module->onlyUser) {
			$model->setScenario(LeadForm::SCENARIO_OWNER);
		}

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
