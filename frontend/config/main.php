<?php

use common\behaviors\GlobalAccessBehavior;
use common\components\User as WebUser;
use common\models\user\User;
use common\modules\lead\Module as LeadModule;

$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

return [
	'id' => 'app-frontend',
	'homeUrl' => Yii::getAlias('@frontendUrl'),
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'controllerNamespace' => 'frontend\controllers',
	'modules' => [
		'account' => [
			'class' => 'frontend\modules\account\Module',
		],
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
		'lead' => [
			'class' => LeadModule::class,
			'onlyUser' => true,
			'allowDelete' => false,
			'as access' => [
				'class' => GlobalAccessBehavior::class,
				'rules' => [
					[
						'allow' => true,
						'controllers' => ['lead/lead'],
						'permissions' => [User::PERMISSION_LEAD],
					],
				],
			],
		],
	],
	'components' => [
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'request' => [
			'csrfParam' => '_csrf-frontend',
		],
		'user' => [
			'class' => WebUser::class,
			'identityClass' => User::class,
			//			'loginUrl' => ['/account/sign-in/login'],
			'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-front', 'httpOnly' => true],
		],
		'session' => [
			// this is the name of the session cookie used for login on the frontend
			'name' => 'app-front',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'urlManager' => require(__DIR__ . '/_urlManager.php'),
		'cache' => require(__DIR__ . '/_cache.php'),
	],
	'as beforeAction' => [
		'class' => 'common\behaviors\LastActionBehavior',
	],
	'params' => $params,
];
