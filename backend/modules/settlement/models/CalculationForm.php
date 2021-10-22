<?php

namespace backend\modules\settlement\models;

use common\models\message\IssueSettlementCreateMessagesForm;
use common\models\settlement\CalculationForm as BaseCalculationForm;

class CalculationForm extends BaseCalculationForm {

	private ?IssueSettlementCreateMessagesForm $messagesForm = null;

	public function load($data, $formName = null) {
		$load = parent::load($data, $formName);
		$message = $this->getMessagesModel();
		if ($message === null) {
			return $load;
		}
		return $load && $message->load($data, $formName);
	}

	public function getMessagesModel(): ?IssueSettlementCreateMessagesForm {
		if (!$this->isCreateScenario()) {
			return null;
		}
		if ($this->messagesForm === null) {
			$this->messagesForm = new IssueSettlementCreateMessagesForm();
			$this->messagesForm->setSettlement($this->getModel());
		}
		return $this->messagesForm;
	}

	public function pushMessages(int $sms_owner_id = null): ?int {
		$message = $this->getMessagesModel();
		if ($message === null) {
			return null;
		}
		$message->sms_owner_id = $sms_owner_id;
		return $this->messagesForm->pushMessages();
	}

}
