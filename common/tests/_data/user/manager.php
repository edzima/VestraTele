<?php

use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\User;

return [
	[
		'id' => UserFixtureHelper::MANAGER_JOHN,
		'username' => 'john',
		'auth_key' => 'iwTNae9t34mnK6l4vT4IeaTk-YWI2Rv',
		'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.lfmQ39O5JlHJVFpNn618OUS1HwaIi',
		'password_reset_token' => 't5GU9NwpuGYSfFEZMAxqtuz2PkE22_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'john@vestra.info',
		'status' => User::STATUS_ACTIVE,
	],
	[
		'id' => UserFixtureHelper::MANAGER_NICOLE,
		'username' => 'nicole',
		'auth_key' => 'EdKfXrx88weFMV0IxuTMKgfK2tS3Lp',
		'password_hash' => '$2y$13$g5nv41Px7VBqhS3hVsVN2.MKT3jFdkXEsMC4rQJLfaMa7VaJqL2',
		'password_reset_token' => '4NyiZNAuxjs5Mty990c4ssVrgllIi_' . time(),
		'created_at' => '1391885313',
		'updated_at' => '1391885313',
		'email' => 'nicole@vestra.info',
		'status' => User::STATUS_DELETED,
	],
];
