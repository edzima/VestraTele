<?php

use common\models\user\User;
use common\modules\lead\components\LeadDialer;
use common\modules\lead\Module;
use yii\helpers\ArrayHelper;

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
			'userNames' => static function () {
				return ArrayHelper::map(User::find()->asArray()->all(), 'id', 'username');
			},
			'dialer' => [
				'class' => LeadDialer::class,
				'callingStatus' => 2,
				'notAnsweredStatus' => 3,
				'answeredStatus' => 4,
			],
		],
	],
];
