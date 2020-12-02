<?php

use common\models\issue\IssuePay;

return [
	[
		'calculation_id' => 1,
		'value' => 1230,
		'vat' => 23,
		'deadline_at' => '2021-01-01',
		'transfer_type' => IssuePay::TRANSFER_TYPE_BANK,
	],
	[
		'calculation_id' => 2,
		'value' => 1230,
		'vat' => 23,
		'pay_at' => '2020-01-01',
		'deadline_at' => '2020-01-01',
		'transfer_type' => IssuePay::TRANSFER_TYPE_BANK,
	],
	[
		'calculation_id' => 3,
		'value' => 615,
		'vat' => 23,
		'pay_at' => '2020-01-01',
		'deadline_at' => '2020-01-01',
		'transfer_type' => IssuePay::TRANSFER_TYPE_BANK,
	],
	[
		'calculation_id' => 3,
		'value' => 615,
		'vat' => 23,
		'deadline_at' => '2020-01-01',
		'transfer_type' => IssuePay::TRANSFER_TYPE_BANK,
	],
];
