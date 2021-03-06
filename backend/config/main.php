<?php

use backend\modules\benefit\Module as BenefitModule;
use backend\modules\entityResponsible\Module as EntityResponsibleModule;
use backend\modules\issue\Module as IssueModule;
use backend\modules\hint\Module as HintModule;
use backend\modules\provision\Module as ProvisionModule;
use backend\modules\settlement\Module as SettlementModule;
use backend\modules\user\Module as UserModule;
use common\modules\lead\Module as LeadModule;
use common\modules\reminder\Module as ReminderModule;
use common\behaviors\GlobalAccessBehavior;
use common\behaviors\LastActionBehavior;
use common\components\User as WebUser;
use common\models\user\User;
use yii\web\UserEvent;

$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

return [
	'id' => 'app-backend',
	'homeUrl' => Yii::getAlias('@backendUrl'),
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'backend\controllers',
	'defaultRoute' => 'issue/issue/index',
	'controllerMap' => [
		'file-manager-elfinder' => [
			'class' => 'mihaildev\elfinder\Controller',
			'access' => ['@'],
			'disabledCommands' => ['netmount'],
			'roots' => [
				[
					'baseUrl' => '/backend',
					'basePath' => '@storage',
					'path' => '/',
					'access' => ['read' => 'manager', 'write' => 'manager'],
					'options' => [
						'attributes' => [
							[
								'pattern' => '#.*(\.gitignore|\.htaccess)$#i',
								'read' => false,
								'write' => false,
								'hidden' => true,
								'locked' => true,
							],
						],
					],
				],
			],
		],
	],
	'components' => [
		'request' => [
			'csrfParam' => '_csrf-backend',
		],
		'user' => [
			'class' => WebUser::class,
			'identityClass' => User::class,
			'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-back', 'httpOnly' => true],
			'on beforeLogin' => function (UserEvent $event): void {
				$event->isValid = Yii::$app->authManager->checkAccess($event->identity->getId(), 'loginToBackend');
				if (!$event->isValid) {
					$id = $event->identity->getId();
					Yii::info("User '$id'  without permission try logged to backend.", 'User.beforeLogin');
				}
			},
		],
		'session' => [
			// this is the name of the session cookie used for login on the backend
			'name' => 'app-back',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'urlManager' => require __DIR__ . '/_urlManager.php',
		'frontendCache' => require Yii::getAlias('@frontend/config/_cache.php'),
	],
	'modules' => [
		'benefit' => [
			'class' => BenefitModule::class,
		],
		'db-manager' => [
			'class' => 'bs\dbManager\Module',
			// path to directory for the dumps
			'path' => '@root/backups',
			// list of registerd db-components
			'dbList' => ['db'],
			'as access' => [
				'class' => GlobalAccessBehavior::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['administrator'],
					],
				],
			],
		],
		'entity-responsible' => [
			'class' => EntityResponsibleModule::class,
		],
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
		'issue' => [
			'class' => IssueModule::class,
		],
		'hint' => [
			'class' => HintModule::class,
		],
		'lead' => [
			'class' => LeadModule::class,
			'userClass' => User::class,
			'userNames' => static function (): array {
				return User::getSelectList(User::getAssignmentIds([User::PERMISSION_LEAD]));
			},
			'as access' => [
				'class' => GlobalAccessBehavior::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [User::PERMISSION_LEAD],
					],
				],
			],
		],
		'settlement' => [
			'class' => SettlementModule::class,
		],
		'provision' => [
			'class' => ProvisionModule::class,
		],
		'user' => [
			'class' => UserModule::class,
		],
		'webshell' => [
			'class' => 'samdark\webshell\Module',
			'yiiScript' => '@root/yii', // adjust path to point to your ./yii script
			'allowedIPs' => ['*'],
			'as access' => [
				'class' => GlobalAccessBehavior::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['administrator'],
					],
				],
			],
		],
	],
	'as globalAccess' => [
		'class' => GlobalAccessBehavior::class,
		'rules' => [
			[
				'controllers' => ['site'],
				'allow' => true,
				'actions' => ['login'],
				'roles' => ['?'],
			],
			[
				'controllers' => ['site'],
				'allow' => true,
				'actions' => ['logout'],
				'roles' => ['@'],
			],
			[
				'controllers' => ['site'],
				'allow' => true,
				'actions' => ['error'],
				'roles' => ['?', '@'],
			],
			[
				'allow' => true,
				'roles' => ['manager'],
			],
		],
	],
	'as beforeAction' => [
		'class' => LastActionBehavior::class,
	],
	'params' => $params,
];
