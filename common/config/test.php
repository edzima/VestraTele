<?php

use common\models\user\User;

return [
	'id' => 'app-common-tests',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['lead'],
	'language' => 'en-US',
	'components' => [
		'user' => [
			'class' => 'yii\web\User',
			'identityClass' => User::class,
		],
		'authManager' => [
			'cache' => null,
		],
	],
];
