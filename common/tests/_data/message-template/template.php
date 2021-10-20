<?php

use common\components\message\IssueMessageFactory;
use common\components\message\IssueSettlementMessageFactory;
use common\models\issue\IssueSettlement;

return [
	[
		'id' => 1,
		'key' => IssueMessageFactory::generateKey(
			IssueMessageFactory::TYPE_SMS,
			IssueMessageFactory::keyAboutCreateIssueToCustomer(),
			[1, 2]
		),
	],
	[
		'id' => 2,
		'key' => IssueMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueMessageFactory::keyAboutCreateIssueToCustomer(),
			[1, 2]
		),
	],
	[
		'id' => 3,
		'key' => IssueMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueMessageFactory::keyAboutCreateIssueToAgent(),
			[1, 2]
		),
	],
	[
		'id' => 4,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_SMS,
			IssueSettlementMessageFactory::keyAboutCreateSettlementToCustomer(IssueSettlement::TYPE_HONORARIUM),
			[1, 2],
		),
	],
	[
		'id' => 5,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueSettlementMessageFactory::keyAboutCreateSettlementToCustomer(IssueSettlement::TYPE_HONORARIUM),
			[1, 2],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 6,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueSettlementMessageFactory::keyAboutCreateSettlementToWorkers(IssueSettlement::TYPE_HONORARIUM),
			[1, 2],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 7,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_SMS,
			IssueSettlementMessageFactory::keyAboutCreateSettlementToWorkers(IssueSettlement::TYPE_HONORARIUM),
			[1, 2],
			IssueSettlement::TYPE_HONORARIUM,
		),
	],
	[
		'id' => 8,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_SMS,
			IssueSettlementMessageFactory::keyAboutPayPaidToCustomer(),
			[1, 2],
		),
	],
	[
		'id' => 9,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_SMS,
			IssueSettlementMessageFactory::keyAboutPayPaidToWorkers(),
			[1, 2],
		),
	],

	[
		'id' => 10,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueSettlementMessageFactory::keyAboutPayPaidToCustomer(),
			[1, 2],
		),
	],
	[
		'id' => 11,
		'key' => IssueSettlementMessageFactory::generateKey(
			IssueMessageFactory::TYPE_EMAIL,
			IssueSettlementMessageFactory::keyAboutPayPaidToWorkers(),
			[1, 2],
		),
	],

];
