<?php

namespace common\modules\lead\controllers;

use backend\helpers\Url;
use backend\widgets\CsvForm;
use common\behaviors\SelectionRouteBehavior;
use common\helpers\Flash;
use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\forms\LeadDeadlineForm;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\LeadMultipleUpdate;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadNameSearch;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\Module;
use common\modules\reminder\models\ReminderQuery;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
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
					'delete-multiple' => ['POST'],
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
	public function actionIndex(int $pageSize = 50) {
		$searchModel = new LeadSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$dataProvider->pagination->defaultPageSize = $pageSize;
		$dataProvider->pagination->pageSize = $pageSize;
		if (Yii::$app->request->isPjax) {
			return $this->renderAjax('_grid', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'assignUsers' => Yii::$app->user->can(
					Module::PERMISSION_ASSIGN_USERS
				),
				'visibleButtons' => [
					'delete' => $this->module->allowDelete,
				],
			]);
		}

		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			$query = $dataProvider->query;
			$ids = Yii::$app->request->post('selection');
			if (!empty($ids)) {
				$query->andWhere([Lead::tableName() . '.id' => $ids]);
			}
			$query->orderBy(['date_at' => SORT_DESC]);
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

	public function actionPhone(string $phone = null) {
		$model = new LeadPhoneSearch();
		if ($phone !== null) {
			$model->phone = $phone;
		}
		$dataProvider = $model->search(Yii::$app->request->queryParams);
		if (empty(Yii::$app->request->queryParams)) {
			$model->clearErrors();
		}
		return $this->render('phone', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionName(string $name = null) {
		$model = new LeadNameSearch();
		if ($name !== null) {
			$model->name = $name;
		}
		$dataProvider = $model->search(Yii::$app->request->queryParams);
		if (empty(Yii::$app->request->queryParams)) {
			$model->clearErrors();
		}
		return $this->render('name', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionDeadline(int $id, string $returnUrl = null) {
		$lead = $this->findLead($id);
		$model = new LeadDeadlineForm();
		$model->setLead($lead);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success update Lead deadline.'));
			if ($returnUrl) {
				return $this->redirect($returnUrl);
			}
			return $this->redirectLead($id);
		}
		return $this->render('deadline', [
			'model' => $model,
		]);
	}

	public function actionUpdateMultiple(array $ids = []) {
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
			return $this->redirect(['index']);
		}
		$ids = array_unique($ids);
		if (count($ids) === 1) {
			return $this->redirect(['update', 'id' => reset($ids)]);
		}
		$model = new LeadMultipleUpdate();
		$model->ids = $ids;
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$sourceCount = $model->updateSource();
			if ($sourceCount) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success Change Source: {source} for Leads: {count}.', [
						'source' => $model->getSourceModel()->getSourceName(),
						'count' => $sourceCount,
					]));
			}
			$model->getStatusModel()->owner_id = Yii::$app->user->getId();
			$statusCount = $model->updateStatus();
			if ($statusCount) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success Change Status: {status} for Leads: {count}.', [
						'status' => $model->getStatusModel()->getStatusName(),
						'count' => $statusCount,
					]));
			}
			$usersCount = $model->updateUsers();
			if ($usersCount) {
				$userModel = $model->getUsersModel();
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success assign {user} as {type} to {count} leads.', [
						'user' => $userModel->getUserName(),
						'type' => $userModel->getTypeName(),
						'count' => $usersCount,
					])
				);
				$userModel->sendEmail();
			}
			return $this->redirect(['index']);
		}
		return $this->render('update-multiple', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single Lead model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id) {
		/**
		 * @var Lead $model
		 */
		$model = $this->findLead($id);
		$reminderQuery = $model->getLeadReminders()
			->joinWith([
				'reminder' => function (ReminderQuery $query) {
					if ($this->module->onlyUser) {
						$query->onlyUser(Yii::$app->user->getId());
					}
				},
			]);
		$remindersDataProvider = new ActiveDataProvider([
			'query' => $reminderQuery,
		]);
		if (Yii::$app->request->isPjax) {
			return $this->renderAjax('_reminder-grid', [
				'model' => $model,
				'dataProvider' => $remindersDataProvider,
				'onlyUser' => $this->module->onlyUser,
			]);
		}

		$userIsFromMarket = $this->module->market->isFromMarket($model->getUsers(), Yii::$app->user->getId());
		if (
			$userIsFromMarket
			&& $this->module->market->hasExpiredReservation($id, Yii::$app->user->getId())
		) {
			Yii::warning(
				Yii::t('lead', 'Reservation for Lead: #{id} - {lead} from Market has expired, but User: {user} try View them.', [
					'id' => $model->getId(),
					'lead' => $model->getName(),
					'user' => Yii::$app->user->getId(),
				]), 'lead.view.reservationExpired'
			);

			//@todo temp allow expired reservation see Lead.
//			Flash::add(Flash::TYPE_WARNING, Yii::t('lead', 'Reservation for Lead: {lead} from Market has expired.', [
//				'lead' => $model->getName(),
//			])
//			);
//			if ($model->market) {
//				return $this->redirect(['market/view', 'id' => $model->market->id]);
//			}
//			return $this->redirect(['index']);
		}
		if ($model->market !== null && Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)) {
			Flash::add(Flash::TYPE_INFO,
				Yii::t('lead', 'Lead is in Market') . ' '
				. Html::a(Html::icon('link'), ['market/view', 'id' => $model->market->id])
			);
		}
		$sameContactsCount = count($model->getSameContacts());

		if ($sameContactsCount > 0) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('lead', 'Find Similars Leads: {count}.', [
					'count' => $sameContactsCount,
				])
			);
		}

		if (YII_IS_FRONTEND && Yii::$app->user->can(User::PERMISSION_LEAD_MANAGER)) {
			$backendUrl = Url::class;
			Flash::add(Flash::TYPE_INFO,
				Html::a(Yii::t('frontend', 'Backend'), $backendUrl::leadView($id, true))
			);
		}

		$isOwner = $this->module->manager->isOwner($model, Yii::$app->user->getId());
		$users = $model->leadUsers;
		$usersDataProvider = null;
		if (!$this->module->onlyUser
			|| ($isOwner && count($users) > 1)) {
			$usersDataProvider = new ArrayDataProvider([
				'allModels' => $users,
			]);
		}

		return $this->render('view', [
			'model' => $model,
			'withDelete' => $this->module->allowDelete && !$userIsFromMarket,
			'onlyUser' => $this->module->onlyUser,
			'isOwner' => $isOwner,
			'userIsFromMarket' => $userIsFromMarket,
			'usersDataProvider' => $usersDataProvider,
			'remindersDataProvider' => $remindersDataProvider,
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

		if ($model->load(Yii::$app->request->post()) && $model->updateLead($lead, Yii::$app->user->getId())) {
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

	public function actionDeleteMultiple(array $ids) {
		$selection = Yii::$app->request->post('selection');
		if (is_array($selection)) {
			$ids = $selection;
		}

		if (!empty($ids)) {
			$count = Lead::deleteAll(['id' => $ids]);
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success Delete Leads: {count}.', [
					'count' => $count,
				]));
			}
		}
		return $this->redirect(['index']);
	}

}
