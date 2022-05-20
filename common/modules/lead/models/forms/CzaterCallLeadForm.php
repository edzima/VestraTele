<?php

namespace common\modules\lead\models\forms;

use common\modules\czater\entities\Call;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use DateTime;
use Yii;
use yii\helpers\Json;

class CzaterCallLeadForm extends CzaterLeadForm {

	public $provider = Lead::PROVIDER_CZATER_CALL;

	private Call $call;

	public function setCall(Call $call): void {
		$this->call = $call;
		$this->id = $call->id;
		$this->referer = !empty($call->referer) ? $call->referer : $call->getClient()->firstReferer;
		$this->source_id = LeadSource::findByURL($this->referer)->id ?? null;
		$this->name = $this->getName();
		$this->phone = $call->getClientFullNumber();
		$this->date_at = $this->getDateTime()->format($this->dateFormat);
		$this->status_id = $this->getStatusId();
		$this->data = Json::encode($this->getData());
	}

	public function getStatusId(): int {
		if ($this->validate(['phone', 'email'])) {
			$sameLeads = $this->getSameContacts();
			if (!empty($sameLeads)) {
				foreach ($sameLeads as $sameLead) {
					if ($sameLead->getSourceId() === $this->getSourceId()) {
						return LeadStatusInterface::STATUS_ARCHIVE;
					}
				}
			}
		}
		return LeadStatusInterface::STATUS_NEW;
	}

	public function getData(): array {
		return $this->call->toArray();
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->call->dateRequested);
	}

	public function getName(): string {
		if (empty($this->call->clientName)) {
			return $this->call->getClient()->name ?? Yii::t('lead', 'Czater Call Lead');
		}
		return $this->call->clientName;
	}

}
