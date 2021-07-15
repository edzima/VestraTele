<?php

namespace common\modules\lead\components;

use common\modules\lead\events\LeadEvent;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadInterface;
use Yii;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class LeadManager extends Component {

	public const EVENT_AFTER_PUSH = 'push.after';

	/**
	 * @var string|array
	 */
	public $model = Lead::class;
	public bool $onlyForUser = false;

	public function findById(string $id): ?ActiveLead {
		$model = $this->getModel()::findById($id);
		if ($model && $this->isForUser($model)) {
			return $model;
		}
		return null;
	}

	public function isForUser(ActiveLead $lead, $userId = null): bool {
		if (!$this->onlyForUser) {
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

	/**
	 * @return ActiveLead|BaseActiveRecord
	 */
	protected function getModel(): ActiveLead {
		if (!$this->model instanceof ActiveLead) {
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
