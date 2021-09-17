<?php

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

return [
	'new-wordpress-accident' => [
		'name' => 'John',
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'email' => 'test@lead.com',
		'phone' => '777-222-122',
		'provider' => Lead::PROVIDER_FORM,
	],
	'equal-accident-phone-from-other-source' => [
		'name' => 'John2',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'email' => 'test@lead.com',
		'phone' => '777-222-122',
		'provider' => Lead::PROVIDER_FORM,
	],
	'archive-wordpress-accident' => [
		'name' => 'Alan',
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'email' => 'john@wayne.com',

		'date_at' => '2020-02-01',
	],
	'new-benefits' => [
		'name' => 'Emily',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
	'new-without-owner' => [
		'name' => 'Tommy Back',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
];

