<?php

use common\models\user\User;
use common\modules\lead\Module;

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
	'modules' => [
		'lead' => [
			'class' => Module::class,
			'userClass' => User::class,
		],
	],
];
