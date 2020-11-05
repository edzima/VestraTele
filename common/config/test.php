<?php

return [
	'id' => 'app-common-tests',
	'basePath' => dirname(__DIR__),
	'language' => 'en-US',
	'components' => [
		'user' => [
			'class' => 'yii\web\User',
			'identityClass' => 'common\models\user\User',
		],
		'authManager' => [
			'cache' => null,
		],
	],
];
