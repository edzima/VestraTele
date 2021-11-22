<?php

use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;

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
	],
	'in-progress' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type_id' => 1,
		'title' => 'In progress summon',
		'issue_id' => 1,
		'owner_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_DUCHOWO,
		'entity_id' => 2,
	],
	'in-progress-301' => [
		'status' => Summon::STATUS_IN_PROGRESS,
		'type_id' => 2,
		'title' => 'In progress summon',
		'issue_id' => 2,
		'owner_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'contractor_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
	'realized' => [
		'status' => Summon::STATUS_REALIZED,
		'type_id' => 3,
		'title' => 'In progress summon',
		'issue_id' => 3,
		'owner_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'contractor_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
		'entity_id' => 1,
	],
];
