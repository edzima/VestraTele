<?php


return [
	'not-payed' => [
		'calculation_id' => 1,
		'value' => 1230,
		'vat' => 23,
		'deadline_at' => '2019-01-01',
	],
	'payed' => [
		'calculation_id' => 2,
		'value' => 1230,
		'vat' => 23,
		'pay_at' => '2020-01-01',
		'deadline_at' => '2020-01-01',
	],
	[
		'calculation_id' => 3,
		'value' => 400,
		'vat' => 23,
		'pay_at' => '2020-01-01',
		'deadline_at' => '2020-01-01',
	],
	[
		'calculation_id' => 3,
		'value' => 215,
		'vat' => 23,
		'pay_at' => '2020-02-01',
		'deadline_at' => '2020-02-01',
	],
	'status-analyse' => [
		'calculation_id' => 3,
		'value' => 615,
		'vat' => 23,
		'deadline_at' => '2020-03-01',
	],
	[
		'calculation_id' => 5,
		'value' => 1230,
		'vat' => 23,
		'deadline_at' => '2020-02-01',
	],
	[
		'calculation_id' => 6,
		'value' => 1230,
		'vat' => 23,
		'deadline_at' => '2020-02-01',
		'pay_at' => '2020-03-01',
	],
];
