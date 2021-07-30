<?php

use common\models\issue\IssueCost;
use common\models\settlement\TransferType;

return [
	[
		'issue_id' => 1,
		'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => 615,
		'vat' => 23,
		'date_at' => '2020-02-10',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'transfer_type' => TransferType::TRANSFER_TYPE_BANK,
	],
	[
		'issue_id' => 1,
		'type' => IssueCost::TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => 100,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 2,
		'type' => IssueCost::TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 3,
		'type' => IssueCost::TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 3,
		'type' => IssueCost::TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'transfer_type' => TransferType::TRANSFER_TYPE_CASH,
		'settled_at' => '2020-03-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
];
