<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;

return [
	[
		'id' => UserFixtureHelper::LAWYER_1,
		'username' => 'lawyer1',
		'auth_key' => 'iwTNae9t34mnK6l4T4IeaTk-YWI2Rv',
		'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.lfmQ39O5JlHJVFpNn618OUS1HwaIi',
		'password_reset_token' => 't5GU9NwpuGYSfFEZMAxqtz2PkEvv_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'lawyer1@vestra.info',
	],
	[
		'id' => UserFixtureHelper::LAWYER_2,
		'username' => 'lawyer2',
		'auth_key' => 'EdKfx88weFMV0IxuTMKgfK2tS3Lp',
		'password_hash' => '$2y$13$g5nv41Px7VBqhS3hVsVN2.MKT3jFdkXEsMC4rQJLfaMa7VaJqL2',
		'password_reset_token' => '4NyiZNAuxjs5My990c47sVrgllIi_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'lawyer2@vestra.info',
		'status' => '3',

	],
	[
		'id' => UserFixtureHelper::LAWYER_3_INACTIVE,
		'username' => 'lawyer3',
		'auth_key' => 'O87Gk3_UfmMHYkyezQLmkKNsllzT',
		//Test1234
		'password_hash' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
		'email' => 'lawyer3@vestra.info',
		'status' => User::STATUS_INACTIVE,
		'created_at' => '1548675330',
		'updated_at' => '1548675330',
		'verification_token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330',
	],
];
