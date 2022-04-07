<?php

namespace common\modules\lead\models\forms;

use common\modules\czater\entities\Call;
use common\modules\lead\models\Lead;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

class CzaterCallLeadForm extends CzaterLeadForm {

	public $provider = Lead::PROVIDER_CZATER_CALL;

	private Call $call;

	public function setCall(Call $call): void {
		$this->call = $call;
		$this->name = $this->getName();
		$this->phone = $this->getPhone();
		$this->source_id = $this->getSourceId();
		$this->date_at = $this->getDateTime()->format($this->dateFormat);
		$this->data = Json::encode($this->getData());
	}

	public function getData(): array {
		return $this->call->toArray();
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->call->dateRequested);
	}

	public function getName(): string {
		if (empty($this->call->clientName)) {
			return Yii::t('lead', 'Czater Call Lead');
		}
		return $this->call->clientName;
	}

	public function getPhone(): string {
		return $this->call->getClientFullNumber();
	}

	public function getSourceId(): int {
		$parts = explode('_', $this->call->consultantName);
		$sourceID = (int) end($parts);
		if ($sourceID) {
			return $sourceID;
		}
		throw new InvalidConfigException('Not Found Source ID on End of ConsultantName: ' . $this->call->consultantName);
	}

}
