<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;

return [
	'with-childs' => [
		'id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		'username' => 'peter-nowak',
		'auth_key' => 'iwTNae9t34mnK6l4vT4IeaTk-YWI2Rv',
		'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.lfmQ39O5JlHJVFpNn618OUS1HwaIi',
		'password_reset_token' => 't5GU9NwpuGYSfFEZMAxqtuz2PkEvv_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'agent@vestra.info',
	],
	'with-parent-and-child' => [
		'id' => UserFixtureHelper::AGENT_AGNES_MILLER,
		'username' => 'agnes-miller',
		'auth_key' => 'EdKfXrx88weFMV0IxuTMKgfK2tS3Lp',
		'password_hash' => '$2y$13$g5nv41Px7VBqhS3hVsVN2.MKT3jFdkXEsMC4rQJLfaMa7VaJqL2',
		'password_reset_token' => '4NyiZNAuxjs5Mty990c47sVrgllIi_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'agent2@vestra.info',
		'status' => User::STATUS_ACTIVE,
	],
	'with-parents-without-childs' => [
		'id' => UserFixtureHelper::AGENT_TOMMY_SET,
		'username' => 'tommy',
		'auth_key' => 'O87Gk3_UfmMHYkyezZ7QLmkKNsllzT',
		//Test1234
		'password_hash' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
		'email' => 'agent3@vestra.info',
		'status' => User::STATUS_ACTIVE,
		'created_at' => '1548675330',
		'updated_at' => '1548675330',
		'verification_token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330',
	],
	'without-parent-and-childs' => [
		'id' => UserFixtureHelper::AGENT_EMILY_PAT,
		'username' => 'emily',
		'auth_key' => '4XXdVqi3rDpa_a6JH6zqreFxUPcUPvJ',
		//Test1234
		'password_hash' => '$2y$13$d17z0w/wKC4LFwtzBcm6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
		'email' => 'agent4@vestra.info',
		'status' => User::STATUS_ACTIVE,
		'created_at' => '1548675330',
		'updated_at' => '1548675330',
		'verification_token' => 'already_used_token_1548675330',
	],
	'some-agent' => [
		'username' => 'agent5',
		'auth_key' => '4XXdVqi3rDpa_a6JH62zqreFxUPcUPvJ',
		//Test1234
		'password_hash' => '$2y$13$d172w/wKC4LFwtzBcm6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
		'email' => 'agent5@vestra.info',
		'status' => User::STATUS_ACTIVE,
		'created_at' => '1548675330',
		'updated_at' => '1548675330',
		'verification_token' => 'already_used_token_1548675330',
	],
];
