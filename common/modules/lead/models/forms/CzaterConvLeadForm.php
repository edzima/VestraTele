<?php

namespace common\modules\lead\models\forms;

use common\modules\czater\entities\Conv;
use common\modules\lead\models\Lead;
use DateTime;
use yii\helpers\Json;

class CzaterConvLeadForm extends CzaterLeadForm {

	public $provider = Lead::PROVIDER_CZATER_CONV;

	private Conv $conv;

	public function setConv(Conv $conv): void {
		$this->conv = $conv;
		$this->id = $conv->id;
		$this->setReferer(!empty($conv->referer) ? $conv->referer : $conv->getClient()->firstReferer);
		$this->email = $this->getEmail();
		$this->name = $this->getName();
		$this->phone = $this->getPhone();
		$this->date_at = $this->getDateTime()->format($this->dateFormat);
		$this->data = Json::encode($this->getData());
	}

	public function getData(): array {
		return $this->conv->toArray();
	}

	public function getDateTime(): DateTime {
		return new DateTime($this->conv->dateBegin);
	}

	public function getName(): string {
		return $this->conv->getClient()->name;
	}

	public function getPhone(): ?string {
		return $this->conv->getClient()->phone;
	}

	public function getEmail(): ?string {
		return $this->conv->getClient()->email;
	}

}
