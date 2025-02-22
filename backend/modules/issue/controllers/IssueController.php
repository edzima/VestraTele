<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueForm;
use backend\modules\issue\models\IssueStageChangeForm;
use backend\modules\issue\models\search\IssueLeadsSearch;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\models\search\SummonSearch;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\widgets\CsvForm;
use common\behaviors\IssueTypeParentIdAction;
use common\behaviors\SelectionRouteBehavior;
use common\helpers\ArrayHelper;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\message\IssueCreateMessagesForm;
use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\lead\models\forms\IssueLeadForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii2tech\csvgrid\CsvGrid;

/**
 * IssueController implements the CRUD actions for Issue model.
 *
 * @see SelectionRouteBehavior
 * @method array getQueryParams()
 */
class IssueController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'selection' => static::getSelectionBehaviorsConfig(),
			'typeTypeParent' => [
				'class' => IssueTypeParentIdAction::class,
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * @param bool $requiredThrow
	 * @return array|null
	 * @throws BadRequestHttpException
	 */
	public static function getSelectionSearchIds(bool $requiredThrow = true): ?array {
		$selection = Yii::createObject(static::getSelectionBehaviorsConfig());
		/* @var SelectionRouteBehavior $selection */
		$ids = $selection->getSelectionSearchIds();
		if ($requiredThrow && empty($ids)) {
			throw new BadRequestHttpException(Yii::t('yii', 'IDs must be set.'));
		}
		return $ids;
	}

	/**
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex(?int $parentTypeId = null) {
		$searchModel = $this->createSearchModel($parentTypeId);

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			/** @var IssueQuery $query */
			$query = clone($dataProvider->query);
			$query->with('customer.userProfile');
			$query->with('tele.userProfile');
			$query->with('type');
			$columns = [
				[
					'attribute' => 'longID',
					'label' => 'Nr',
				],
				[
					'attribute' => 'agent.fullName',
					'label' => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
				],
				[
					'attribute' => 'tele.fullName',
					'label' => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
				],
				[
					'attribute' => 'customer.profile.firstname',
					'label' => 'Imie',
				],
				[
					'attribute' => 'customer.profile.lastname',
					'label' => 'Nazwisko',
				],
				[
					'attribute' => 'customer.userProfile.phone',
					'label' => 'Telefon [1]',
				],
				[
					'attribute' => 'customer.userProfile.phone_2',
					'label' => 'Telefon [2]',
				],
				[
					'attribute' => 'customer.email',
					'label' => 'Email',
				],
				[
					'attribute' => 'typeName',
					'label' => Yii::t('issue', 'Type'),
				],
				[
					'attribute' => 'stageName',
					'label' => Yii::t('issue', 'Stage'),
				],
			];
			$addressSearch = $searchModel->addressSearch;
			if (!empty($addressSearch->region_id)
				|| !empty($addressSearch->city_name)
				|| !empty($addressSearch->postal_code)) {
				$query->with('customer.addresses.address.city.terc');
				$query->with('customer.addresses.address.city.terc.districts');

				$addressColumns = [
					[
						'attribute' => 'customer.homeAddress.city.region.name',
						'label' => Yii::t('address', 'Region'),
					],
					[
						'attribute' => 'customer.homeAddress.city.terc.district.name',
						'label' => Yii::t('address', 'District'),
					],
					[
						'attribute' => 'customer.homeAddress.city.terc.commune.name',
						'label' => Yii::t('address', 'Commune'),
					],
					[
						'attribute' => 'customer.homeAddress.postal_code',
						'label' => Yii::t('address', 'Code'),
					],
					[
						'attribute' => 'customer.homeAddress.city.name',
						'label' => Yii::t('address', 'City'),
					],
					[
						'attribute' => 'customer.homeAddress.info',
						'label' => Yii::t('address', 'Info'),
					],
				];
				$columns = array_merge($columns, $addressColumns);
			}
			if ($searchModel->withClaimsSum) {
				$columns = array_merge($columns, [
					[
						'label' => Yii::t('issue', 'Issue Claims'),
						'attribute' => 'claimsSum',
					],
				]);
			}
			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => $columns,
			]);
			return $exporter->export()->send('export.csv');
		}

		if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM)) {
			$dataProvider->query->joinWith('claims');
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'queryParams' => $this->getQueryParams(),
		]);
	}

	private static function createSearchModel(?int $parentTypeId = null): IssueSearch {
		$parentTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		$searchModel = new IssueSearch();
		$searchModel->parentTypeId = $parentTypeId;
		$searchModel->userId = Yii::$app->user->id;
		if (Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
			$searchModel->excludeArchiveStage();
		}
		if (Yii::$app->user->can(Worker::PERMISSION_ARCHIVE_DEEP)) {
			$searchModel->withArchiveDeep = true;
			$searchModel->excludeArchiveDeepStage();
		}
		if (Yii::$app->user->can(Worker::PERMISSION_PAY_ALL_PAID)) {
			$searchModel->scenario = IssueSearch::SCENARIO_ALL_PAYED;
			if (Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)) {
				$searchModel->withArchiveOnAllPayedPay = true;
			}
		}
		return $searchModel;
	}

	public function actionArchive(): string {
		$searchModel = new IssueSearch();
		$searchModel->scenario = IssueSearch::SCENARIO_ARCHIVE_CUSTOMER;
		$searchModel->userId = Yii::$app->user->getId();
		$searchModel->load(Yii::$app->request->queryParams);
		$searchModel->excludedStages = [];
		$dataProvider = $searchModel->search([]);
		return $this->render('archive', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionLead(): string {
		$searchModel = new IssueLeadsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('lead', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Issue model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$search = new IssuePayCalculationSearch();
		$search->withArchive = true;
		$search->issue_id = $id;
		$calculationsDataProvider = $search->search([]);
		$summonDataProvider = (new SummonSearch(['issue_id' => $model->id]))->search([]);
		$summonDataProvider->sort = false;
		$summonDataProvider->pagination = false;

		return $this->render('view', [
			'model' => $model,
			'calculationsDataProvider' => $calculationsDataProvider,
			'summonDataProvider' => $summonDataProvider,
		]);
	}

	public function actionCreateAndLink(int $id) {
		$baseIssue = $this->findModel($id);
		$model = new IssueForm(['customer' => $baseIssue->customer]);

		$model->entity_responsible_id = $baseIssue->entity_responsible_id;
		$model->details = $baseIssue->details;
		$model->signing_at = date('Y-m-d');
		$model->signature_act = $baseIssue->signature_act;
		$model->tagsIds = ArrayHelper::getColumn($baseIssue->tags, 'id');
		$model->setUsers(ArrayHelper::map($baseIssue->users, 'type', 'user_id'));

		$messagesModel = $this->createCreateMessagesForm($model);
		$data = Yii::$app->request->post();
		if ($model->load($data)
			&& $model->save()) {
			$messagesModel->setIssue($model->getModel());
			if ($messagesModel->load($data)) {
				$messagesModel->pushMessages();
			}
			$this->createCustomerLead($model->getModel());
			$model->getModel()->linkIssue($baseIssue->id);
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create-and-link', [
			'model' => $model,
			'baseIssue' => $baseIssue,
			'messagesModel' => $messagesModel,
		]);
	}

	protected function createCreateMessagesForm(IssueForm $model): IssueCreateMessagesForm {
		$messagesModel = new IssueCreateMessagesForm();
		$messagesModel->setIssue($model->getModel());
		$messagesModel->workersTypes = [
			IssueUser::TYPE_AGENT => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
			IssueUser::TYPE_TELEMARKETER => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
			IssueUser::TYPE_LAWYER => IssueUser::getTypesNames()[IssueUser::TYPE_LAWYER],
		];
		$messagesModel->addExtraWorkersEmailsIds(Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_CREATE));
		$messagesModel->sendSmsToCustomer = $messagesModel->hasSmsCustomerTemplate(false);
		$messagesModel->sms_owner_id = Yii::$app->user->getId();

		return $messagesModel;
	}

	/**
	 * Creates a new Issue model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $customerId
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $customerId, int $issueId = null) {
		$customer = Customer::findOne($customerId);
		if ($customer === null) {
			throw new NotFoundHttpException('Client not exist');
		}

		$model = new IssueForm(['customer' => $customer]);
		$baseIssue = null;
		if ($issueId !== null) {
			$baseIssue = $this->findModel($issueId);
			$model->loadFromModel($baseIssue, false);
		}

		$messagesModel = $this->createCreateMessagesForm($model);
		$data = Yii::$app->request->post();
		if ($model->load($data)
			&& $model->save()) {
			$messagesModel->setIssue($model->getModel());
			if ($messagesModel->load($data)) {
				$messagesModel->pushMessages();
			}
			$this->createCustomerLead($model->getModel());
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		$duplicatesCustomersDataProvider = new ActiveDataProvider([
			'query' => User::find()
				->joinWith('userProfile UP')
				->joinWith('addresses.address.city')
				->andWhere(['UP.firstname' => $customer->profile->firstname])
				->andWhere(['UP.lastname' => $customer->profile->lastname])
				->andWhere(['<>', User::tableName() . '.id', $customer->id]),
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);
		if ($duplicatesCustomersDataProvider->totalCount) {
			Flash::add(Flash::TYPE_WARNING, Yii::t('backend', 'Warning! Duplicates Customers ({user}) exists: {count}.', [
				'count' => $duplicatesCustomersDataProvider->totalCount,
				'user' => $customer->getFullName(),
			]));
		}
		return $this->render('create', [
			'model' => $model,
			'duplicatesCustomersDataProvider' => $duplicatesCustomersDataProvider,
			'messagesModel' => $messagesModel,
			'baseIssue' => $baseIssue,
		]);
	}

	/**
	 * Updates an existing Issue model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$form = new IssueForm(['model' => $this->findModel($id)]);
		if ($form->load(Yii::$app->request->post()) && $form->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('update', [
			'model' => $form,
		]);
	}

	public function actionStage(int $issueId, int $stageId = null, string $returnUrl = null) {
		$model = new IssueStageChangeForm($this->findModel($issueId));
		if ($stageId !== null) {
			$model->stage_id = $stageId;
		}
		$model->date_at = date($model->dateFormat);
		$model->user_id = Yii::$app->user->getId();
		$model->getMessagesModel()->addExtraWorkersEmailsIds(Yii::$app->authManager->getUserIdsByRole(Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_STAGE_CHANGE));
		if ($model->load(Yii::$app->request->post())
			&& $model->save()
		) {
			$message = $returnUrl
				? Yii::t('issue', 'In Issue: {issue} the stage was changed', [
					'issue' => $model->getIssue()->getIssueName(),
				])
				: Yii::t('issue', 'The stage was changed');

			$message .= ': ' . $model->getNoteTitle();
			Flash::add(Flash::TYPE_SUCCESS, $message);
			$model->pushMessages();

			return $this->redirect($returnUrl ?? ['view', 'id' => $issueId]);
		}
		return $this->render('stage', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Issue model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(int $id): Response {
		$model = $this->findModel($id);
		Yii::warning([
			'message' => 'Delete issue: ' . $id, [
				'attributes' => $model->getAttributes(),
				'user_id' => Yii::$app->user->getId(),
			],
		], __METHOD__);
		$model->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Issue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Issue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws ForbiddenHttpException
	 */
	protected function findModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null && Yii::$app->user->canSeeIssue($model)) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private function createCustomerLead(Issue $model) {
		$data = IssueLeadForm::issueCustomerAttributes($model);
		if (!empty($data)) {
			Yii::$app->leadClient->addFromCustomer($data);
		}
	}

	private static function getSelectionBehaviorsConfig(): array {
		return [
			'class' => SelectionRouteBehavior::class,
			'searchModel' => function (): IssueSearch {
				return static::createSearchModel();
			},
		];
	}

}

