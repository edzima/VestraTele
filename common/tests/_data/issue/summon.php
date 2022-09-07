<?php

use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;


$todayEventSevenAmDate = date('Y-m-d 07:00:00');
$todayEventTenAmDate = date('Y-m-d 10:00:00');
$todayEventElevenAmDate = date('Y-m-d 11:00:00');

return [
	'new' => [
		'status' => Summon::STATUS_NEW,
		'type_id' => 1,
		'title' => 'Document summon',
		'issue_id' => 1,
		'owner_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'contractor_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
		'start_at' => '2020-09-22 00:00:00'
	],
	'in-progress' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type_id' => 1,
		'title' => 'In progress summon',
		'issue_id' => 1,
		'owner_id' => 300,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_DUCHOWO,
		'entity_id' => 2,
		'start_at' => '2020-09-22 00:00:00',
	],
	'in-progress-301' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type_id' => 2,
		'title' => 'In progress summon 301',
		'issue_id' => 2,
		'owner_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
	'realized' => [
		'status' => Summon::STATUS_REALIZED,
		'type_id' => 3,
		'title' => 'Realized summon',
		'issue_id' => 3,
		'owner_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'contractor_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
		'start_at' => '2020-09-22 00:00:00',
	],
	'beautiful-day-summon' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type' => 2,
		'title' => 'some summon',
		'issue_id' => 1,
		'owner_id' => 404,
		'contractor_id' => 300,
		'city_id' => TerytFixtureHelper::SIMC_ID_DUCHOWO,
		'entity_id' => 2,
		'created_at' => '1600757862',
		'updated_at' => '1600757862',
		'start_at' => $todayEventElevenAmDate,
	],
	'pretty-day-summon' =>[
		'status' => Summon::STATUS_NEW,
		'type' => 2,
		'title' => 'Document summon',
		'issue_id' => 1,
		'owner_id' => 404,
		'contractor_id' => 300,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
		'created_at' => '1600757862',
		'updated_at' => '1600757862',
		'start_at' => $todayEventSevenAmDate,
		'deadline_at' => $todayEventTenAmDate
	]
];
