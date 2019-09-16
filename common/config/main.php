<?php

use common\components\DbManager;
use common\formatters\Formatter;
use yii\caching\DummyCache;
use yii\caching\FileCache;

$config = [
	'name' => 'Vestra System',
	'vendorPath' => dirname(__DIR__, 2) . '/vendor',
	'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
	'timeZone' => getenv('TIMEZONE'),
	'sourceLanguage' => 'en-US',
	'language' => getenv('LANGUAGE'),
	'bootstrap' => ['log'],
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
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
			'appendTimestamp' => YII_ENV_DEV,
			'converter' => [
				'class' => 'yii\web\AssetConverter',
				'commands' => [
					'less' => ['css', 'lessc {from} {to} --no-color'],
				],
			],
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
				'noty' =>[
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
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => YII_ENV_DEV,
		],
		'cache' => [
			'class' => YII_ENV_DEV ? DummyCache::class : FileCache::class,
		],
	],
];

return $config;
