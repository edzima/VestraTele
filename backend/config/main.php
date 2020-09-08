<?php

use backend\modules\benefit\Module as BenefitModule;
use backend\modules\entityResponsible\Module as EntityResponsibleModule;
use backend\modules\issue\Module as IssueModule;
use backend\modules\provision\Module as ProvisionModule;
use yii\web\User;
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
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-back', 'httpOnly' => true],
			'on beforeLogin' => function (UserEvent $event): void {
				/** @var User $user */
				$user = $event->sender;
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
				'class' => 'common\behaviors\GlobalAccessBehavior',
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
		'provision' => [
			'class' => ProvisionModule::class,
		],
		'webshell' => [
			'class' => 'samdark\webshell\Module',
			'yiiScript' => '@root/yii', // adjust path to point to your ./yii script
			'allowedIPs' => ['*'],
			'as access' => [
				'class' => 'common\behaviors\GlobalAccessBehavior',
				'rules' => [
					[
						'allow' => true,
						'roles' => ['administrator'],
					],
				],
			],
		],
		'rbac' => [
			'class' => 'developeruz\db_rbac\Yii2DbRbac',
			'as access' => [
				'class' => 'common\behaviors\GlobalAccessBehavior',
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
		'class' => 'common\behaviors\GlobalAccessBehavior',
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
				'controllers' => ['user'],
				'allow' => true,
				'roles' => ['administrator'],
			],
			[
				'controllers' => ['user'],
				'allow' => false,
			],
			[
				'allow' => true,
				'roles' => ['manager'],
			],
		],
	],
	'as beforeAction' => [
		'class' => 'common\behaviors\LastActionBehavior',
	],
	'params' => $params,
];
