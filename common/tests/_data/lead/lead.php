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
		'phone' => '48777-222-122',
		'provider' => Lead::PROVIDER_FORM,
		'data' => '{"external_id":1}',
	],
	'equal-accident-phone-from-other-source' => [
		'name' => 'John2',
		'source_id' => 3,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'email' => 'test@lead.com',
		'phone' => '48777-222-122',
		'provider' => Lead::PROVIDER_FORM,
		'data' => '[]',
	],
	'agree' => [
		'name' => 'John',
		'source_id' => 1,
		'status_id' => 2,
		'data' => '{"external_id":2, "agree" :"2020-01-01"}',
	],
	'equal-accident-phone-from-other-source-and-archive' => [
		'name' => 'John2',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'date_at' => '2020-01-01',
		'email' => 'test@lead.com',
		'phone' => '48777-222-122',
		'provider' => Lead::PROVIDER_FORM,
		'data' => '[]',
	],
	'archive-wordpress-accident' => [
		'name' => 'Alan',
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		'email' => 'john@wayne.com',
		'date_at' => '2020-02-01',
		'data' => '[]',
	],
	'new-benefits' => [
		'name' => 'Emily',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'phone' => '48555-222-111',
		'data' => '[]',
	],
	'new-without-owner-with-soruce-with-dialer-phone' => [
		'name' => 'Tommy Back',
		'source_id' => 1,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'phone' => '48555-222-111',
		'data' => '[]',
	],
	'new-without-phone-and-owner_and_source-without-phone' => [
		'name' => 'Tommy Back',
		'source_id' => 2,
		'status_id' => LeadStatusInterface::STATUS_NEW,
		'date_at' => '2020-01-01',
		'data' => '[]',
	],

];

