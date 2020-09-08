<?php

use common\components\DbManager;
use common\components\Provisions;
use common\components\TaxComponent;
use common\formatters\Formatter;
use yii\caching\DummyCache;
use yii\caching\FileCache;
use common\modules\address\Module as AddressModule;
use edzima\teryt\Module as TerytModule;

return [
	'name' => 'Vestra System',
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

	'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
	'timeZone' => 'Europe/Warsaw',
	'sourceLanguage' => 'en-US',
	'language' => 'pl-PL',
	'bootstrap' => [
		'log',
	],
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
		'address' => [
			'class' => AddressModule::class,
		],
		'teryt' => [
			'class' => TerytModule::class,
		],
	],
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => getenv('DB_DSN'),
			'username' => getenv('DB_USERNAME'),
			'password' => getenv('DB_PASSWORD'),
			'tablePrefix' => getenv('DB_TABLE_PREFIX'),
			'charset' => 'utf8',
			'enableSchemaCache' => YII_ENV_PROD,
		],
		'authManager' => [
			'class' => DbManager::class,
		],
		'assetManager' => [
			'class' => 'yii\web\AssetManager',
			'linkAssets' => getenv('LINK_ASSETS'),
		],
		'formatter' => [
			'class' => Formatter::class,
			'nullDisplay' => '',
			'dateFormat' => 'dd.MM.yyyy',
			'decimalSeparator' => ',',
			'thousandSeparator' => ' ',
			'currencyCode' => 'PLN',
		],
		'validator' => [
			'class' => 'yii\validators\IpValidator',
			'ipv6' => false,
		],
		'log' => [
			'traceLevel' => YII_ENV_DEV ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\DbTarget',
					'levels' => ['error', 'warning'],
					'except' => ['yii\web\HttpException:*', 'yii\i18n\*'],
					'prefix' => function () {
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
			'class' => 'common\components\keyStorage\KeyStorage',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
		'cache' => [
			'class' => YII_ENV_DEV ? DummyCache::class : FileCache::class,
		],
		'provisions' => [
			'class' => Provisions::class,
		],
		'tax' => [
			'class' => TaxComponent::class,
		],
	],
];
