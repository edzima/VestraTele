<?php

use common\behaviors\GlobalAccessBehavior;
use common\components\User as WebUser;
use common\models\user\User;
use common\modules\lead\Module as LeadModule;
use frontend\controllers\ApiLeadController;

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
			'controllerMap' => [
				'api' => ApiLeadController::class,
			],
			'onlyUser' => true,
			'allowDelete' => false,
			'userClass' => User::class,
			'userNames' => static function (): array {
				return User::getSelectList(User::getAssignmentIds([User::PERMISSION_LEAD]));
			},
			'as access' => [
				'class' => GlobalAccessBehavior::class,
				'rules' => [
					[
						'allow' => true,
						'controllers' => [
							'lead/lead',
							'lead/campaign',
							'lead/source',
							'lead/reminder',
							'lead/report',
						],
						'permissions' => [User::PERMISSION_LEAD],
					],
					[
						'allow' => true,
						'controllers' => [
							'lead/user',
						],
						'actions' => ['assign-single'],
						'permissions' => [User::PERMISSION_LEAD],
					],
					[
						'allow' => true,
						'controllers' => [
							'lead/api',
						],
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
