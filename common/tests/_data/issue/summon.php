<?php

use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;

return [
	'new' => [
		'status' => Summon::STATUS_NEW,
		'type' => Summon::TYPE_APPEAL,
		'title' => 'Document summon',
		'issue_id' => 1,
		'owner_id' => 300,
		'contractor_id' => 300,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
	'in-progress' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type' => Summon::TYPE_APPEAL,
		'title' => 'In progress summon',
		'issue_id' => 1,
		'owner_id' => 300,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_DUCHOWO,
		'entity_id' => 2,
	],
	'in-progress-301' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type' => Summon::TYPE_APPEAL,
		'title' => 'In progress summon',
		'issue_id' => 2,
		'owner_id' => 301,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
	'realized' => [
		'status' => Summon::STATUS_REALIZED,
		'type' => Summon::TYPE_ANTIVINDICATION,
		'title' => 'In progress summon',
		'issue_id' => 3,
		'owner_id' => 301,
		'contractor_id' => 300,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
];
