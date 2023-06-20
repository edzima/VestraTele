<?php

namespace common\models\issue;

use common\models\user\User;
use common\modules\reminder\models\ReminderForm;

class SummonReminderForm extends ReminderForm {

	public int $summon_id;
	public ?string $defaultPeriodDate = '+1 week';
	private ?Summon $summon = null;
	private ?SummonReminder $summonReminder = null;

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
		$summonReminder = $this->getSummonReminder();
		$summonReminder->summon_id = $this->summon_id;
		$summonReminder->reminder_id = $this->getModel()->id;
		return $summonReminder->save();
	}

	public function getUsersNames(): array {
		$usersIds = [
			$this->getSummon()->owner_id,
			$this->getSummon()->contractor_id,
		];
		if (!empty($this->user_id) && !in_array($this->user_id, $usersIds)) {
			$usersIds[] = $this->user_id;
		}

		return User::getSelectList($usersIds, false);
	}

	private function getSummonReminder(): SummonReminder {
		if ($this->summonReminder === null) {
			$this->summonReminder = new SummonReminder();
		}
		return $this->summonReminder;
	}

	public function setSummonReminder(SummonReminder $summonReminder) {
		$this->summonReminder = $summonReminder;
		$this->setModel($summonReminder->reminder);
		$this->setSummon($summonReminder->summon);
	}

	public function getSummon(): Summon {
		if ($this->summon === null) {
			$this->summon = Summon::findOne($this->summon_id);
		}
		return $this->summon;
	}

	public function setSummon(Summon $summon) {
		$this->summon = $summon;
		$this->summon_id = $summon->id;
		$this->usersRange = array_keys($this->getUsersNames());
	}
}
