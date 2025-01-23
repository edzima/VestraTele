<?php

namespace common\modules\lead\components;

use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

class LeadManager extends Component {

	public const EVENT_AFTER_PUSH = 'push.after';

	/**
	 * @var string|array
	 */
	public $model = Lead::class;
	public bool $onlyForUser = false;

	protected const SALT_HASH = '7812asdEsa@@';

	public function validateLead(ActiveLead $lead, string $hash): bool {
		$data = $this->hashLeadData($lead);
		return $data === Yii::$app->security->validateData($hash . $data, static::SALT_HASH);
	}

	public function hashLead(ActiveLead $lead): string {
		$data = $this->hashLeadData($lead);
		$hashData = Yii::$app->security->hashData($data, static::SALT_HASH);
		return str_replace($data, '', $hashData);
	}

	protected function hashLeadData(ActiveLead $lead): string {
		return Json::encode($lead->toArray([
			'phone', 'email', 'name', 'source_id', 'type_id', 'date_at',
		]));
	}

	public function init() {
		parent::init();
		Event::on(Lead::class, Lead::EVENT_AFTER_STATUS_UPDATE, function (LeadEvent $event): void {
			$this->afterStatusUpdate($event);
		});
	}

	public function findById(string $id, bool $forUser = true): ?ActiveLead {
		$model = $this->getModel()::findById($id);
		if (!$model
			|| ($forUser && !$this->isForUser($model))) {
			return null;
		}
		return $model;
	}

	public function isOwner(ActiveLead $lead, int $userId): bool {
		foreach ($lead->getUsers() as $type => $id) {
			if ($id === $userId && $type === LeadUser::TYPE_OWNER) {
				return true;
			}
		}
		return false;
	}

	public function canUserReport(ActiveLead $lead, $userId = null): bool {
		$users = $lead->getUsers();
		if (empty($users)) {
			return true;
		}
		return $this->isForUser($lead, $userId);
	}

	public function isForUser(ActiveLead $lead, $userId = null): bool {
		if (Yii::$app->user->can('lead.manager')) {
			return true;
		}
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		if (empty($userId)) {
			return false;
		}
		return $lead->isForUser($userId);
	}

	public function getLeadUser(ActiveLead $lead, int $userId = null, string $type = null): ?LeadUser {
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		if ($userId === null) {
			return null;
		}
		$users = $lead->leadUsers;
		foreach ($users as $leadUser) {
			if ($type === null || $leadUser->type === $type) {
				if ($leadUser->user_id === $userId) {
					return $leadUser;
				}
			}
		}
		return null;
	}

	/**
	 * @param LeadInterface $lead
	 * @return ActiveLead|ActiveRecord|null
	 */
	public function pushLead(LeadInterface $lead): ?ActiveLead {
		if (empty($lead->getPhone()) && empty($lead->getEmail())) {
			Yii::warning([
				'message' => 'Push lead without phone or email.',
				'lead' => $lead,
			], 'lead.push.empty-contact');
			return null;
		}

		$model = $this->getModel();
		$model->setLead($lead);
		if ($model->validate()) {
			Yii::info([
				'message' => 'Push new lead.',
				'lead' => $lead,
			], 'lead.push.create.success');
			$model->save();
			$this->afterPush($model);
		} else {
			Yii::warning([
				'message' => 'Try push lead with validate errors.',
				'lead' => $lead,
				'errors' => $model->getErrors(),
			], 'lead.push.create.error');
		}
		return $model;
	}

	protected function afterPush(ActiveLead $lead): void {
		$event = new LeadEvent($lead);
		$this->trigger(self::EVENT_AFTER_PUSH, $event);
	}

	public function groupLeads(LeadInterface $lead): void {
		$models = $this->findByLead($lead);
		if (count($models) > 1) {
			foreach ($models as $model) {
				$model->updateFromLead($lead);
			}
		}
	}

	protected function afterStatusUpdate(LeadEvent $event): void {
		/** @var Lead $lead */
		$lead = $event->getLead();
		$lead->updateAttributes([
			'deadline_at' => null,
		]);
		$status = LeadStatus::getModels()[$lead->getStatusId()];
		if (!empty($status->market_status) && $lead->market !== null) {

			$market = $lead->market;
			$market->status = $status->market_status;

			if ($market->updateAttributes([
				'status',
			])) {
				Module::getInstance()->market->sendLeadChangeStatusEmail($market);
			}
		}
	}

	/**
	 * @return ActiveLead|BaseActiveRecord
	 * @throws InvalidConfigException
	 */
	protected function getModel(): ActiveLead {
		if (!$this->model instanceof ActiveLead) {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return Yii::createObject($this->model);
		}
		return $this->model;
	}

	/**
	 * @param LeadInterface $lead
	 * @return Lead[]
	 */
	public function findByLead(LeadInterface $lead): array {
		return $this->getModel()::findByLead($lead);
	}

}
