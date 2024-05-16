<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

class CustomerLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_CRM_CUSTOMER;

	public bool $onlyActiveSource = false;

	public function load($data, $formName = ''): bool {
		return parent::load($data, $formName);
	}

}
