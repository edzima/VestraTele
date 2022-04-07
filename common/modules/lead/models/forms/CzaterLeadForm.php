<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadStatusInterface;

class CzaterLeadForm extends LeadForm {

	public function getStatusId(): int {
		$sameLeads = $this->getSameContacts();
		if (!empty($sameLeads)) {
			foreach ($sameLeads as $lead) {
				if ($lead->getSourceId() === $this->getSourceId()) {
					return LeadStatusInterface::STATUS_NEW;
				}
			}
		}
		return LeadStatusInterface::STATUS_ARCHIVE;
	}

}
