<?php

use common\components\DbManager;
use common\components\EmailTemplateManager;
use common\components\RelationComponent;
use common\components\keyStorage\KeyStorage;
use common\components\PayComponent;
use common\components\provision\Provisions;
use common\components\TaxComponent;
use common\components\Formatter;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\czater\Czater;
use edzima\teryt\Module as TerytModule;
use common\modules\lead\Module as LeadModule;
use yii\caching\DummyCache;
use yii\caching\FileCache;

return [
	'name' => 'Vestra CRM',
	'vendorPath' => dirname(__DIR__, 2) . '/vendor',
	'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
	'timeZone' => 'Europe/Warsaw', //@todo load from .env
	'sourceLanguage' => 'en-US',
	'language' => 'pl',
	'bootstrap' => [
		'log',
		'teryt',
	],
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
		'teryt' => [
			'class' => TerytModule::class,
		],
		'lead' => [
			'class' => LeadModule::class,
			'userClass' => User::class,
		],
	],
	'components' => [
		'czater' => [
			'class' => Czater::class,
			'apiKey' => $_ENV['CZATER_API_KEY'],
		],
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => getenv('DB_DSN'),
			'username' => getenv('DB_USERNAME'),
			'password' => getenv('DB_PASSWORD'),
			'tablePrefix' => getenv('DB_TABLE_PREFIX'),
			'charset' => 'utf8',
			'enableSchemaCache' => YII_ENV_PROD,
		],
		'emailTemplate' => [
			'class' => EmailTemplateManager::class,
		],
		'authManager' => [
			'class' => DbManager::class,
			'cache' => 'cache',
		],
		'assetManager' => [
			'class' => 'yii\web\AssetManager',
			'linkAssets' => getenv('LINK_ASSETS'),
		],
		'formatter' => [
			'class' => Formatter::class,
			'defaultTimeZone' => 'Europe/Warsaw',//@todo load from .env
			'decimalSeparator' => ',',
			'thousandSeparator' => ' ',
			'currencyCode' => 'PLN',
		],
		'log' => [
			'traceLevel' => YII_ENV_DEV ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\DbTarget',
					'levels' => ['error', 'warning'],
					'except' => ['yii\web\HttpException:*', 'yii\i18n\*'],
					'prefix' => static function (): string {
						$url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;

						return sprintf('[%s][%s]', Yii::$app->id, $url);
					},
					'logVars' => [],
				],
			],
		],
		'i18n' => [
			'translations' => [
				'app' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
				],
				'noty' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
				],
				'db_rbac' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
				],
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
					'fileMap' => [
						'common' => 'common.php',
						'backend' => 'backend.php',
						'frontend' => 'frontend.php',
						'kvgrid' => 'kvgrid.php',
					],
				],
			],
		],

		'keyStorage' => [
			'class' => KeyStorage::class,
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
		'cache' => [
			'class' => YII_ENV_DEV ? DummyCache::class : FileCache::class,
		],
		'pay' => [
			'class' => PayComponent::class,
		],
		'provisions' => [
			'class' => Provisions::class,
		],
		'tax' => [
			'class' => TaxComponent::class,
		],
		'userHierarchy' => [
			'class' => RelationComponent::class,
		],
	],
];
