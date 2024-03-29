<?php

use common\modules\lead\entities\Dialer;
use common\modules\lead\models\LeadStatusInterface;

return [
	[
		'id' => LeadStatusInterface::STATUS_ARCHIVE,
		'name' => 'Archive',
		'not_for_dialer' => true,
	],
	[
		'id' => LeadStatusInterface::STATUS_NEW,
		'name' => 'New',
		'not_for_dialer' => false,
	],
	[
		'name' => 'Agree',
		'not_for_dialer' => true,
	],
	[
		'name' => 'Not Answered',
		'short_report' => true,
		'not_for_dialer' => false,
		'id' => Dialer::STATUS_UNESTABLISHED,
	],
	[
		'name' => 'Establish',
		'short_report' => true,
		'not_for_dialer' => true,
		'id' => Dialer::STATUS_ESTABLISHED,
	],
	[
		'name' => 'Calling',
		'not_for_dialer' => true,
		'id' => Dialer::STATUS_CALLING,
	],
];
