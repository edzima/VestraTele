<?php

namespace common\modules\court\controllers;

use common\components\message\MessageTemplate;
use common\helpers\Flash;
use common\helpers\Html;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueUserQuery;
use common\models\message\IssueLawsuitSmsForm;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitIssueForm;
use common\modules\court\models\search\LawsuitSearch;
use common\modules\court\Module;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitPartyDTO;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\widgets\LawsuitSmsBtnWidget;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * LawsuitController implements the CRUD actions for Lawsuit model.
 *
 * @property Module $module
 */
class LawsuitController extends Controller {

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
						'link-issue' => ['POST'],
						'unlink-issue' => ['POST'],
						'spi-confirm' => ['POST'],
						'sync-spi' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all Lawsuit models.
	 *
	 * @return string
	 */
	public function actionIndex(string $appeal = null): string {
		$searchModel = new LawsuitSearch();
		$searchModel->spiAppeal = $appeal;
		if ($this->module->onlyUserIssues) {
			$searchModel->setScenario(LawsuitSearch::SCENARIO_ISSUE_USER);
			$searchModel->issueUserId = Yii::$app->user->getId();
		}
		$dataProvider = $searchModel->search($this->request->queryParams);
		$dataProvider->sort->defaultOrder = [
			'updated_at' => SORT_DESC,
		];
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'withSPI' => $this->module->spiModuleConfig !== null && Yii::$app->user->can(Module::PERMISSION_SPI_LAWSUIT_DETAIL),
		]);
	}

	public function actionSmsTemplate(int $lawsuitId, int $issueId, string $key) {
		$template = $this->findTemplate($key);
		$lawsuit = $this->findModel($lawsuitId);
		$issue = $this->findIssue($issueId);
		if (empty($issue->getIssueModel()->customer->getPhone())) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('issue', 'Try send sms to Customer: {customer} without Phone.', [
					'customer' => $issue->getIssueModel()->customer->getFullName(),
				]));
			return $this->redirect(['view', 'id' => $lawsuitId]);
		}
		$model = new IssueLawsuitSmsForm($issue);
		$model->setLawsuit($lawsuit);
		$model->owner_id = Yii::$app->user->getId();
		$model->setFirstAvailablePhone();
		$model->message = LawsuitSmsBtnWidget::getSmsMessage($lawsuit, $template);
		if (Yii::$app->request->isPost) {
			$jobId = $model->pushJob();
			if (!empty($jobId)) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('common', 'Success add SMS: {message} to send queue.', [
						'message' => $model->message,
					]));
				return $this->redirect(['view', 'id' => $lawsuitId]);
			}
		}
		return $this->renderContent($model->message);
	}

	public function actionSpiLawsuit(string $appeal, string $court = null, string $signature = null, int $notificationId = null) {
		$searchModel = new LawsuitSearch();
		$searchModel->spiAppeal = $appeal;
		$searchModel->signature_act = $signature;
		$searchModel->courtName = $court;

		$dataProvider = $searchModel->search($this->request->queryParams);
		if ($dataProvider->getTotalCount() === 0) {
			return $this->redirect([
				'create-from-spi-lawsuit',
				'signature' => $signature,
				'appeal' => $appeal,
				'notificationId' => $notificationId,
				'courtName' => $court,
			]);
		}
		if ($dataProvider->getTotalCount() === 1) {
			$models = $dataProvider->getModels();
			$model = reset($models);
			return $this->redirect([
				'view',
				'id' => $model->id,
				'spiNotificationId' => $notificationId,
			]);
		}
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreateFromSpiLawsuit(string $signature, string $appeal, string $courtName, int $notificationId = null) {
		$spiModule = $this->module->getSPI();
		if (!$spiModule) {
			throw new NotFoundHttpException('SPI Module must be set.');
		}
		if (!$spiModule->isForAppeal($appeal)) {
			throw new NotFoundHttpException('Invalid Appeal.');
		}
		$spiModule->appeal = $appeal;
		$lawsuit = $spiModule
			->getRepositoryManager()
			->getLawsuits()
			->setAppeal($appeal)
			->findBySignature($signature, $courtName);

		if ($lawsuit === null) {
			throw new NotFoundHttpException('Not found SPI Lawsuit');
		}

		$model = new LawsuitIssueForm();
		$model->signature_act = $lawsuit->signature;
		$model->setCourtName($lawsuit->courtName);
		$model->creator_id = Yii::$app->user->getId();

		$partiesDataProvider = $spiModule
			->getRepositoryManager()
			->getParties()
			->setAppeal($appeal)
			->getByLawsuit($lawsuit->id);
		/**
		 * @var LawsuitPartyDTO $customerParty
		 */
		$customerParty = $partiesDataProvider->getModels()[0];
		$issueDataProvider = new ActiveDataProvider([
				'query' => Issue::find()
					->joinWith([
						'users' => function (IssueUserQuery $query) use ($customerParty) {
							$query->withUserFullName($customerParty->name);
							$query->withTypes(IssueUser::TYPES_CUSTOMERS);
						},
					])
					->groupBy(Issue::tableName() . '.id')
					->withoutArchives(),
			]
		);
		if (count($issueDataProvider->getModels()) === 0) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('court', 'Not found Issue for User: {user}', [
					'user' => $customerParty->name . ' - ' . $customerParty->role,
				])
			);
		} else {
			$model->issuesIds = $issueDataProvider->getKeys();
			if ($model->save()) {
				$createdModel = $model->getModel();
				$createdModel->detachBehaviors();
				$createdModel->updateAttributes([
					'created_at' => date(DATE_ATOM, strtotime($lawsuit->createdDate)),
					'updated_at' => date(DATE_ATOM, strtotime($lawsuit->modificationDate)),
				]);
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('court', 'Create Lawsuit for Issues: {issuesCount}', [
						'issuesCount' => count($model->issuesIds),
					]));
				return $this->redirect([
						'view',
						'id' => $model->getModel()->id,
						'spiNotificationId' => $notificationId,
					]
				);
			}
		}

		$this->view->params['appeal'] = $appeal;
		return $this->render('create-from-spi-lawsuit', [
			'model' => $model,
			'lawsuit' => $lawsuit,
			'partiesDataProvider' => $partiesDataProvider,
		]);
	}

	protected function findTemplate(string $key): MessageTemplate {
		$template = Yii::$app->messageTemplate->getTemplate($key);
		if ($template === null) {
			throw new NotFoundHttpException('Not Found Message Template for Key: ' . $key);
		}
		return $template;
	}

	/**
	 * Displays a single Lawsuit model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id, int $spiNotificationId = null): string {
		$model = $this->findModel($id);
		$lawsuitDetails = null;
		$notificationsDataProvider = null;
		if (Yii::$app->user->can(Module::PERMISSION_SPI_LAWSUIT_DETAIL)
			&& $this->module->getSPI() !== null
		) {
			$appeal = $model->court->getSPIAppealWithParents();
			if (!$appeal) {
				Yii::warning(
					Yii::t('court', 'Not found SPI Appeal for Court: {court}.', [
							'court' => $model->court,
						]
					)
				);
			} else {
				$spi = $this->module->getSPI();
				$spi->appeal = $appeal;
				$this->view->params['appeal'] = $appeal;
				if (empty($model->spi_confirmed_user) && !empty($model->spi_last_update_at)) {
					Flash::add(
						Flash::TYPE_WARNING,
						Yii::t('court', 'Changes from SPI to {confirmLink}.', [
							'confirmLink' => Html::a(
								Yii::t('court', 'Confirm'),
								['confirm-spi', 'id' => $model->id], [
									'data-method' => 'post',
								]
							),
						])
					);
				}

				$lawsuitDetails = $this->findSpiLawsuit($model);

				if ($lawsuitDetails) {
					$notificationsDataProvider = $spi
						->getRepositoryManager()
						->getNotifications()
						->setAppeal($appeal)
						->findByLawsuit($lawsuitDetails->id);
				}
			}
		}
		return $this->render('view', [
			'model' => $model,
			'lawsuitDetails' => $lawsuitDetails,
			'notificationsDataProvider' => $notificationsDataProvider,
			'notificationId' => $spiNotificationId,
		]);
	}

	public function actionConfirmSpi(int $id) {
		$model = $this->findModel($id);
		$model->spi_confirmed_user = Yii::$app->user->getId();
		$model->updateAttributes([
			'spi_confirmed_user' => Yii::$app->user->getId(),
		]);
		return $this->redirect(['view', 'id' => $model->id]);
	}

	public function actionSyncSpi(int $id) {
		$sync = $this->module->getSpiSync();

		if ($sync) {
			$model = $this->findModel($id);
			if ($sync->one($model)) {
				$sessionsCount = $sync->getSessionsCount();
				if ($sessionsCount) {
					Flash::add(
						Flash::TYPE_SUCCESS,
						Yii::t('court', 'Update Sessions: {count}', [
							'count' => $sessionsCount,
						])
					);
				} else {
					Flash::add(
						Flash::TYPE_INFO,
						Yii::t('court', 'All sessions is already synced.'),
					);
				}
			}
		}

		return $this->redirect(['view', 'id' => $id]);
	}

	private function findSpiLawsuit(Lawsuit $model): ?LawsuitViewIntegratorDto {
		if ($this->module->getSPI() === null) {
			throw new InvalidConfigException('SPI module not configured');
		}
		$appeal = $model->court->getSPIAppealWithParents();
		if ($appeal === null) {
			Yii::warning('Not found SPI Appeal for Court: ' . $model->court->name);
			return null;
		}
		return $this->module->getSPI()
			->getRepositoryManager()
			->getLawsuits()
			->setAppeal($appeal)
			->findBySignature(
				$model->signature_act,
				$model->court->name,
			);
	}

	/**
	 * Creates a new Lawsuit model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate(int $issueId) {
		$issue = $this->findIssue($issueId);
		$model = new LawsuitIssueForm();
		$model->signaturePattern = $this->module->signaturePattern;
		$model->creator_id = Yii::$app->user->getId();
		$model->setIssue($issue);

		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				return $this->redirect(['view', 'id' => $model->getModel()->id]);
			}
			$lawsuit = $model->getAlreadyExistedLawsuit();
			if ($lawsuit && $lawsuit->hasIssue($issueId)) {
				return $this->redirect(['view', 'id' => $lawsuit->id]);
			}
		}

		return $this->render('create', [
			'model' => $model,
			'issue' => $issue,
		]);
	}

	/**
	 * Updates an existing Lawsuit model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LawsuitIssueForm();
		$model->signaturePattern = $this->module->signaturePattern;
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Lawsuit model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id ID
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$model->delete();
		Yii::warning([
			'msg' => 'Delete Lawsuit',
			'attributes' => $model->getAttributes(),
			'userId' => Yii::$app->user->getId(),
		], __METHOD__);
		return $this->redirect(['index']);
	}

	public function actionLinkIssue(int $id, int $issueId) {
		$model = $this->findModel($id);
		if (!$model->hasIssue($issueId)) {
			$model->linkIssues([$issueId]);
		}
		return $this->redirect(['view', 'id' => $model->id]);
	}

	public function actionUnlinkIssue(int $id, int $issueId) {
		$model = $this->findModel($id);
		$model->unlinkIssue($issueId);
		return $this->redirect(['view', 'id' => $id]);
	}

	/**
	 * Finds the Lawsuit model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return Lawsuit the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): Lawsuit {
		if (($model = Lawsuit::findOne(['id' => $id])) !== null) {
			if ($this->module->onlyUserIssues && !$model->hasIssueUser(Yii::$app->user->getId())) {
				throw new ForbiddenHttpException(Yii::t('court', 'Not found Your issues.'));
			}
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('court', 'The requested page does not exist.'));
	}

	protected function findIssue(int $id): IssueInterface {
		if (($model = Issue::findOne(['id' => $id])) !== null) {
			if (Yii::$app->user->canSeeIssue($model, false)) {
				return $model;
			}
			throw new ForbiddenHttpException();
		}

		throw new NotFoundHttpException(Yii::t('court', 'The requested page does not exist.'));
	}

}
