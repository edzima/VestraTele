<?php

use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\settlement\TransferType;

return [
	[
		'issue_id' => 1,
		'type_id' => SettlementFixtureHelper::COST_TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => 615,
		'vat' => 23,
		'date_at' => '2020-02-10',
		'settled_at' => '2020-03-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'transfer_type' => TransferType::TRANSFER_TYPE_BANK,
	],
	[
		'issue_id' => 1,
		'type_id' => SettlementFixtureHelper::COST_TYPE_PURCHASE_OF_RECEIVABLES,
		'value' => 100,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'settled_at' => '2020-02-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 2,
		'type_id' => SettlementFixtureHelper::COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'settled_at' => '2020-02-12',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 3,
		'type_id' => SettlementFixtureHelper::COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'deadline_at' => '2020-03-12',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 3,
		'type_id' => SettlementFixtureHelper::COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'transfer_type' => TransferType::TRANSFER_TYPE_CASH,
		'deadline_at' => '2020-03-11',
		'settled_at' => '2020-03-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
	[
		'issue_id' => 3,
		'type_id' => SettlementFixtureHelper::COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT,
		'value' => 150,
		'vat' => 0,
		'date_at' => '2020-02-11',
		'transfer_type' => TransferType::TRANSFER_TYPE_CASH,

		'confirmed_at' => '2020-03-12',
		'settled_at' => '2020-03-11',
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
	],
];
