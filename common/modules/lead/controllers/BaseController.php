<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\query\LeadQuery;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\Module;
use Yii;
use yii\base\ActionEvent;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller {

	public const LEADS_IDS_PARAM = 'leadsIds';
	public const LEADS_SEARCH_QUERY_PARAM = 'leadsSearchQuery';

	/**
	 * @var Module
	 */
	public $module;

	protected ?bool $allowDelete = null;
	protected string $deleteAction = 'delete';

	protected $leadsSearchModelClass = LeadSearch::class;

	public function init() {
		parent::init();
		if ($this->allowDelete === null) {
			$this->allowDelete = $this->module->allowDelete;
		}
		$this->attachBeforeDeleteAction();
	}

	protected function attachBeforeDeleteAction(): void {
		if (!$this->allowDelete) {
			$this->on(static::EVENT_BEFORE_ACTION, function (ActionEvent $actionEvent): void {
				if ($actionEvent->action->id === $this->deleteAction) {
					Yii::warning([
						'message' => Yii::t('lead', 'User {id} try access to delete action', ['id' => Yii::$app->user->getId()]),
						'controller' => $this->id,
					], 'lead.delete');
					throw new MethodNotAllowedHttpException();
				}
			});
		}
	}

	/**
	 * @param int $id
	 * @return ActiveLead|ActiveRecord
	 * @throws NotFoundHttpException
	 */
	protected function findLead(int $id, bool $forUser = true): ActiveLead {
		$model = $this->module->manager->findById($id, false);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		if ($forUser && !$this->module->manager->isForUser($model, Yii::$app->user->getId())) {
			throw new ForbiddenHttpException(Yii::t('lead', 'You have not access to Lead.'));
		}
		if ($forUser) {
			$this->afterFindLeadForUser($model);
		}
		return $model;
	}

	protected function afterFindLeadForUser(ActiveLead $model): void {
		$leadUser = $this->module->manager->getLeadUser($model, Yii::$app->user->getId());
		if ($leadUser) {
			if (empty($leadUser->first_view_at)) {
				$leadUser->first_view_at = date('Y-m-d H:i:s');
			}
			$leadUser->last_view_at = date('Y-m-d H:i:s');
			$leadUser->updateAttributes(['first_view_at', 'last_view_at']);
		}
	}

	protected function validateHash(ActiveLead $lead, string $hash, bool $throwException = true): bool {
		$validate = $this->module->manager->validateLead($lead, $hash);
		if (!$validate) {
			Yii::warning(
				'User: ' . Yii::$app->user->getId() . ' try check Lead: ' . $lead->getId() . ' with invaldiate hash.',
				__METHOD__
			);
			if ($throwException) {
				throw new NotFoundHttpException();
			}
		}
		return $validate;
	}

	protected function redirectLead(int $id): Response {
		return $this->redirect(['lead/view', 'id' => $id]);
	}

	protected function ensureLeadsIds(array &$ids): void {
		if (empty($ids)) {
			$ids = $this->getLeadsIds();
		}
		$ids = array_unique($ids);
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('leads', 'Ids cannot be blank.')
			);
			$this->goBack();
		}
	}

	private function getLeadsIds(): array {
		$selection = Yii::$app->request->post('selection');
		if (is_array($selection) && !empty($selection)) {
			return $selection;
		}
		$postIds = Yii::$app->request->post(static::LEADS_IDS_PARAM);
		if (is_string($postIds)) {
			$postIds = explode(',', $postIds);
		}
		if ($postIds) {
			return $postIds;
		}
		$queryParams = Yii::$app->request->post(static::LEADS_SEARCH_QUERY_PARAM);
		if ($queryParams) {
			$queryParams = Json::decode($queryParams);
			$searchModel = $this->getLeadsSearchModel();
			$dataProvider = $searchModel->search($queryParams);
			/**
			 * @var LeadQuery $query
			 */
			$query = $dataProvider->query;
			return $query->getIds();
		}

		return [];
	}

	protected function getLeadsSearchModel(): LeadSearch {
		$searchModel = Yii::createObject($this->leadsSearchModelClass);
		if ($this->module->onlyUser) {
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}
		return $searchModel;
	}

}
