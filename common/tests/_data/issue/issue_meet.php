<?php
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueMeet;

$todayDate =  date('Y-m-d');
$todayEventTenAmDate = $todayDate." "."10:00:00";
$todayEventElevenAmDate = $todayDate." "."11:00:00";
$todayEventOnePMDate = $todayDate." "."13:00:00";
$todayEventTwoPMDate = $todayDate." "."14:00:00";


return [
	[
		'type_id' => 1,
		'phone' => '724987023',
		'client_name' => 'Gorge',
		'client_surname' => 'Smith',
		'tele_id' => 1,
		'agent_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'created_at' => '2021-02-10 07:31:07',
		'updated_at' => '2021-02-10 07:35:01',
		'date_at' => $todayEventTenAmDate,
		'date_end_at' => $todayEventElevenAmDate,
		'details' => 'some details',
		'status' => IssueMeet::STATUS_NEW,
		'street' => 'Long road',
		'email' => 'test@email.com',
		'campaign_id' => 101,
	],
	[
		'type_id' => 2,
		'phone' => '871123997',
		'client_name' => 'Brandon',
		'client_surname' => 'Jones',
		'tele_id' => 1,
		'agent_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'created_at' => '2021-02-10 07:31:07',
		'updated_at' => '2021-02-10 07:35:01',
		'date_at' => $todayEventOnePMDate,
		'date_end_at' => $todayEventTwoPMDate,
		'details' => 'another details',
		'status' => IssueMeet::STATUS_SIGNED_CONTRACT,
		'street' => 'spicy wing',
		'email' => 'some@email.com',
		'campaign_id' => 101,
	],
];
