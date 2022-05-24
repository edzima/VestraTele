<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadSource;
use common\modules\lead\Module;
use Yii;
use yii\helpers\Json;

class CzaterLeadForm extends LeadForm {

	public int $id;
	protected string $referer;

	public function rules(): array {
		return array_merge([
			['referer', 'required'],
			['data', 'unique', 'targetClass' => Lead::class, 'targetAttribute' => 'data'],
		],
			parent::rules()
		);
	}

	protected function setReferer(string $referer): void {
		$this->referer = $referer;
		$this->source_id = LeadSource::findByURL($this->referer)->id ?? null;
		$this->campaign_id = LeadCampaign::findByURL($this->referer)->id ?? null;
	}

	public function getReferer(): string {
		return $this->referer;
	}

	public function findLead(): ?ActiveLead {
		$same = $this->getSameContacts(true);
		foreach ($same as $lead) {
			if ($lead->getProvider() === $this->provider) {
				$id = (int) ArrayHelper::getValue($lead->getData(), 'id');
				if ($id === $this->id) {
					return $lead;
				}
			}
			if ($lead->getSourceId() === $this->getSourceId()) {
				return $lead;
			}
		}
		return null;
	}

	public function pushOrUpdateLead(bool $validate = true): ?ActiveLead {
		if ($validate && !$this->validate()) {
			return null;
		}

		$lead = $this->findLead();
		if ($lead === null) {
			Yii::debug('Push New Czater Lead', 'lead.czater');
			Module::manager()->pushLead($this);
			return $lead;
		}
		$this->updateLead($lead);
		return $lead;
	}

	public function updateLead(Lead $lead): bool {
		if ($lead->getSourceId() !== $this->getSourceId()) {
			return false;
		}
		if (empty($lead->getProvider())) {
			$lead->provider = $this->getProvider();
		}
		if (empty($lead->getCampaignId())) {
			$lead->campaign_id = $this->getCampaignId();
		}
		if (empty($lead->data)) {
			$lead->data = Json::encode($this->getData());
		} else {
			$lead->data = Json::encode(
				array_merge($lead->getData(), $this->getData())
			);
		}
		return $lead->save();
	}

}
