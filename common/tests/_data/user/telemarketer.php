<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;

return [
	[
		'id' => UserFixtureHelper::TELE_1,
		'username' => 'tele1',
		'auth_key' => 'iwTNae9t34mnK6l4T4Ieak-YWI2Rv',
		'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.lfmQ39O5JlHJVFpNn618OUS1HwaIi',
		'password_reset_token' => 't5GU9NwpuGYSFEZMAxqtz2PkEvv_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'tele1@vestra.info',
	],
	[
		'id' => UserFixtureHelper::TELE_2,
		'username' => 'tele2',
		'auth_key' => 'EdKfx88weFV0IxuTMKgfK2tS3Lp',
		'password_hash' => '$2y$13$g5nv41Px7VBqhS3hVsVN2.MKT3jFdkXEsMC4rQJLfaMa7VaJqL2',
		'password_reset_token' => '4NyiZNAuxjs5y990c47sVrgllIi_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'tele2@vestra.info',
		'status' => '3',

	],
	[
		'id' => UserFixtureHelper::TELE_3_INACTIVE,
		'username' => 'tele3',
		'auth_key' => 'O87Gk3_UfmMHYkyezQmkKNsllzT',
		//Test1234
		'password_hash' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
		'email' => 'tele3@vestra.info',
		'status' => User::STATUS_INACTIVE,
		'created_at' => '1548675330',
		'updated_at' => '1548675330',
		'verification_token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330',
	],
];
