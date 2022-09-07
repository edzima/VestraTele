<?php

return [
	[
		'name' => 'Accident',
		'short_name' => 'ACC',
		'meet' => true,
		'vat' => 23,
	],
	[
		'name' => 'Benefits - administrative proceedings',
		'short_name' => 'B-AP',
		'meet' => true,
		'vat' => 23,
	],
	[
		'name' => 'Benefits - civil proceedings',
		'short_name' => 'B-CP',
		'meet' => false,
		'vat' => 23,
		'with_additional_date' => true,

	],
	[
		'name' => 'Commission refunds',
		'short_name' => 'CR',
		'meet' => true,
		'vat' => 0,
		'with_additional_date' => true,
	],
	[
		'name' => 'Anti Vindication',
		'short_name' => 'AV',
		'meet' => true,
		'vat' => 0,
	],
];
