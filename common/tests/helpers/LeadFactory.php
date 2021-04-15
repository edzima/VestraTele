<?php

namespace common\tests\helpers;

use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\LeadInterface;

class LeadFactory {

	public static function createLead(array $config): LeadInterface {
		return new LeadForm($config);
	}
}
