<?php

use common\models\message\Message;
use common\models\user\User;
use common\modules\lead\components\LeadClient;
use common\modules\lead\components\LeadDialerManager;
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
		'leadClient' => [
			'class' => LeadClient::class,
			'baseUrl' => Yii::getAlias('@frontendUrl'),
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
				'class' => LeadDialerManager::class,
				'callingStatus' => 2,
				'notAnsweredStatus' => 3,
				'answeredStatus' => 4,
			],
		],
	],
	'container' => [
		'definitions' => [
			'yii\swiftmailer\Message' => Message::class,
			'yii\mail\BaseMailer' => [
				'messageClass' => Message::class,
			],
			'Codeception\Lib\Connector\Yii2\TestMailer' => [
				'messageClass' => Message::class,
			],
		],
	],
];
