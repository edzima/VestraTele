<?php

namespace common\modules\lead\models\forms;

use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\Module;
use common\modules\reminder\models\ReminderForm;

class LeadReminderForm extends ReminderForm {

	public int $lead_id;
	public ?ReminderForm $reminder = null;

	public ?string $defaultPeriodDate = '+1 week';
	private ?ActiveLead $lead = null;
	private ?LeadReminder $leadReminder = null;

	public function init() {
		parent::init();
		if ($this->date_at === null && $this->defaultPeriodDate !== null) {
			$this->date_at = date($this->dateFormat, strtotime($this->defaultPeriodDate));
		}
	}

	public function save(): bool {
		if (!parent::save()) {
			return false;
		}
		$leadReminder = $this->getLeadReminder();
		$leadReminder->lead_id = $this->lead_id;
		$leadReminder->reminder_id = $this->getModel()->id;
		return $leadReminder->save();
	}

	public function getLead(): Lead {
		if ($this->lead === null || $this->lead->getId() !== $this->lead_id) {
			$this->lead = Module::manager()->findById($this->lead_id);
		}
		return $this->lead;
	}

	public function getUsersNames(): array {
		$usersIds = array_flip(array_values($this->getLead()->getUsers()));
		if (!empty($this->user_id) && !isset($usersIds[$this->user_id])) {
			$usersIds[$this->user_id] = $this->user_id;
		}

		return User::getSelectList(array_keys($usersIds));
	}

	private function getLeadReminder(): LeadReminder {
		if ($this->leadReminder === null) {
			$this->leadReminder = new LeadReminder();
		}
		return $this->leadReminder;
	}

	public function setLead(ActiveLead $lead) {
		$this->lead = $lead;
		$this->lead_id = $lead->getId();
		$this->usersRange = array_keys($this->getUsersNames());
	}

	public function setLeadReminder(LeadReminder $leadReminder) {
		$this->leadReminder = $leadReminder;
		$this->setLead($leadReminder->lead);
		$this->setModel($leadReminder->reminder);
	}
}
