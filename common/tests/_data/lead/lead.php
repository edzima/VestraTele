<?php

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

return [
	'new-wordpress-accident' => [
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'email' => 'test@lead.com',
		'phone' => '777-222-122',
		'provider' => Lead::PROVIDER_FORM,
	],
	'archive-wordpress-accident' => [
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'date_at' => '2020-02-01',
	],
	'new-benefits' => [
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
	'new-without-owner' => [
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
	],
];

