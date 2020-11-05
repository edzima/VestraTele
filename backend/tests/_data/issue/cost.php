<?php

use common\models\issue\IssueCost;

return [
	[
		'issue_id' => 1,
		'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => (new \Decimal\Decimal(600))->toFixed(2),
		'vat' => 23,
		'date_at' => '2020-02-10',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 1,
		'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => 100,
		'vat' => 23,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 1,
		'type' => IssueCost::TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
];
