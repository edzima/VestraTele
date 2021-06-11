<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadStatusInterface;

class LandingLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;

	public function load($data, $formName = ''): bool {
		return parent::load($data, $formName);
	}

}
