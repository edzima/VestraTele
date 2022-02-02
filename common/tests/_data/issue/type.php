<?php

use common\models\issue\Provision;

return [
	[
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
		'with_additional_date' => true,
		'provision_type' => Provision::TYPE_MULTIPLICITY,

	],
	[
		'name' => 'Commission refunds',
		'short_name' => 'CR',
		'meet' => true,
		'vat' => 0,
		'with_additional_date' => true,
		'provision_type' => Provision::TYPE_PERCENTAGE,
	],
	[
		'name' => 'Anti Vindication',
		'short_name' => 'AV',
		'meet' => true,
		'vat' => 0,
		'provision_type' => Provision::TYPE_PERCENTAGE,
	],
];
