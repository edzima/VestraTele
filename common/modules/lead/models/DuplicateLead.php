<?php

namespace common\modules\lead\models;

class DuplicateLead extends Lead {

	public $duplicateCount;

	public function getSameContactsTypesNames(): array {
		$names = [];
		$names[$this->getTypeId()] = $this->getTypeName();
		foreach ($this->getSameContacts(false) as $lead) {
			if (!isset($names[$lead->getTypeId()])) {
				$names[$lead->getTypeId()] = $lead->getTypeName();
			}
		}
		return $names;
	}

	public function getSameContactsStatusesNames(): array {
		$names = [];
		$names[$this->getStatusId()] = $this->getStatusName();
		foreach ($this->getSameContacts(false) as $lead) {
			if (!isset($names[$lead->getStatusId()])) {
				$names[$lead->getStatusId()] = $lead->getStatusName();
			}
		}
		return $names;
	}
}
