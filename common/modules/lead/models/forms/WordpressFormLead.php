<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

class WordpressFormLead extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_FORM_WORDPRESS;

	public function load($data, $formName = ''): bool {
		if (!isset($data['name']) && isset($data['wname'])) {
			$data['name'] = $data['wname'];
		}
		return parent::load($data, $formName);
	}

}
