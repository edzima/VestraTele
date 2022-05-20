<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;

class CzaterLeadForm extends LeadForm {

	public int $id;
	public string $referer;

	public function rules(): array {
		return array_merge([
			['referer', 'required'],
			['data', 'unique', 'targetClass' => Lead::class, 'targetAttribute' => 'data'],
		],
			parent::rules()
		);
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
		}
		return null;
	}

}
