<?php

use common\behaviors\GlobalAccessBehavior;
use common\components\User as WebUser;
use common\models\user\User;
use common\modules\calendar\Module as CalendarModule;
use common\modules\lead\Module as LeadModule;
use frontend\controllers\ApiLeadController;
use frontend\controllers\LeadDialerController;
use yii\base\Action;

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
		'calendar' => [
			'class' => CalendarModule::class,
		],
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
				'dialer' => LeadDialerController::class,
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
							'lead/sms',
							'lead/phone-blacklist',
						],
						'matchCallback' => static function ($rule, Action $action): bool {
							if ($action->controller->id === 'sms') {
								if ($action->id === 'push-multiple') {
									return Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS);
								}
								return Yii::$app->user->can(User::PERMISSION_SMS) || Yii::$app->user->can(User::PERMISSION_LEAD_SMS_WELCOME);
							}
							return true;
						},
						'permissions' => [User::PERMISSION_LEAD],
					],
					[
						'allow' => true,
						'controllers' => [
							'lead/market',
							'lead/market-user',
						],
						'permissions' => [User::PERMISSION_LEAD_MARKET],
					],
					[
						'allow' => true,
						'controllers' => [
							'article',
						],
						'permissions' => [User::PERMISSION_NEWS],
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
						'actions' => ['self'],
						'controllers' => [
							'lead/archive',
						],
					],
					[
						'allow' => true,
						'controllers' => [
							'lead/api',
							'lead/dialer',
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
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
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
