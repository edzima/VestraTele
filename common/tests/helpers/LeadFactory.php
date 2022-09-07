<?php

namespace common\tests\helpers;

use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadStatusInterface;

class LeadFactory {

	public static function createLead(array $config): LeadInterface {
		return new LeadForm($config);
	}

	public static function createNewLead(array $config): LeadInterface {
		$config['status_id'] = LeadStatusInterface::STATUS_NEW;
		return static::createLead($config);
	}

}
