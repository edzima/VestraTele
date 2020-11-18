<?php

use common\models\issue\IssueType;
use common\models\issue\Provision;

return [
	[
		'id' => IssueType::ACCIDENT_ID,
		'name' => 'Accident',
		'short_name' => 'ACC',
		'meet' => true,
		'vat' => 23,
		'provision_type' => Provision::TYPE_PERCENTAGE,
	],
	[
		'name' => 'Benefits - administrative proceedings',
		'short_name' => 'B-AP',
		'meet' => true,
		'vat' => 23,
		'provision_type' => Provision::TYPE_MULTIPLICITY,
	],
	[
		'name' => 'Benefits - civil proceedings',
		'short_name' => 'B-CP',
		'meet' => false,
		'vat' => 23,
		'provision_type' => Provision::TYPE_MULTIPLICITY,

	],
	[
		'name' => 'Commission refunds',
		'short_name' => 'CR',
		'meet' => true,
		'vat' => 0,
		'provision_type' => Provision::TYPE_PERCENTAGE,
	],
];
