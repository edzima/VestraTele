<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

class ZapierLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_FORM_ZAPIER;

	public function load($data, $formName = ''): bool {
		return parent::load($data, $formName);
	}

}
