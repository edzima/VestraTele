<?php

use common\components\callpage\CallPageClient;
use common\components\DbManager;
use common\components\Formatter;
use common\components\HierarchyComponent;
use common\components\keyStorage\KeyStorage;
use common\components\message\MessageTemplateManager;
use common\components\PayComponent;
use common\components\provision\Provisions;
use common\components\TaxComponent;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\czater\Czater;
use common\modules\file\Module as FileModule;
use common\modules\lead\components\LeadClient;
use common\modules\lead\Module as LeadModule;
use common\modules\reminder\Module as ReminderModule;
use creocoder\flysystem\LocalFilesystem;
use edzima\teryt\Module as TerytModule;
use Edzima\Yii2Adescom\AdescomSender;
use Edzima\Yii2Adescom\AdescomSoap;
use League\Flysystem\AdapterInterface;
use yii\caching\DummyCache;
use yii\caching\FileCache;
use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;

//use fredyns\attachments\Module as AttachmentsModule;

$config = [
	'name' => $_ENV['APP_NAME'],
	'vendorPath' => dirname(__DIR__, 2) . '/vendor',
	'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
	'timeZone' => 'Europe/Warsaw', //@todo load from .env
	'sourceLanguage' => 'en-US',
	'language' => 'pl',
	'bootstrap' => [
		'log',
		'teryt',
		'queue',
	],
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
//		'attachments' => [
//			'class' => AttachmentsModule::class,
//			'tempPath' => '@runtime/uploads/temp',
//			'rules' => [ // Rules according to the FileValidator
//						 'maxFiles' => 10, // Allow to upload maximum 3 files, default to 3
//						// 'mimeTypes' => 'image/png', // Only png images
//						// 'maxSize' => 1024 * 1024 * 5, // 1 MB
//			],
//			'filesystem' => 'fs',
//			'tableName' => '{{%attachments}}', // Optional, default to 'attach_file'
//		],
'file' => [
	'class' => FileModule::class,
	'tempPath' => '@runtime/uploads/temp',
	//			'rules' => [ // Rules according to the FileValidator
	//						 'maxFiles' => 10, // Allow to upload maximum 3 files, default to 3
	//						 // 'mimeTypes' => 'image/png', // Only png images
	//						 // 'maxSize' => 1024 * 1024 * 5, // 1 MB
	//			],
	'filesystem' => 'fs',
],
'teryt' => [
	'class' => TerytModule::class,
],
'lead' => [
	'class' => LeadModule::class,
	'userClass' => User::class,
],
'reminder' => [
	'class' => ReminderModule::class,
],
	],
	'components' => [

		'fs' => [
			'class' => LocalFilesystem::class,
			'path' => '@protected',
			'config' => [
				'visibility' => AdapterInterface::VISIBILITY_PRIVATE,
			],
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
		'messageTemplate' => [
			'class' => MessageTemplateManager::class,
		],
		'authManager' => [
			'class' => DbManager::class,
			'cache' => 'cache',
		],
		'assetManager' => [
			'class' => 'yii\web\AssetManager',
			'linkAssets' => getenv('LINK_ASSETS'),
			'appendTimestamp' => true,
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
				'vova07/imperavi' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages',
					'fileMap' => [
						'vova07/imperavi' => 'imperavi.php',
					],
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
		'leadClient' => [
			'class' => LeadClient::class,
			'baseUrl' => $_ENV['LEADS_HOST'],
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
		'sms' => [
			'class' => AdescomSender::class,
			'client' => [
				'class' => AdescomSoap::class,
				'keySessionIdCache' => null,
				'login' => $_ENV['ADESCOM_LOGIN'],
				'password' => $_ENV['ADESCOM_PASSWORD'],
			],
			'messageConfig' => [
				'src' => $_ENV['ADESCOM_SRC'],
				'overwriteSrc' => $_ENV['ADESCOM_OVERWRITE_SRC'],
				'retryInterval' => 60,
				'maxRetryCount' => 1,
			],
		],
		'tax' => [
			'class' => TaxComponent::class,
		],
		'userHierarchy' => [
			'class' => HierarchyComponent::class,
			'modelClass' => Worker::class,
			'parentColumn' => 'boss',
		],
		'mutex' => [
			'class' => MysqlMutex::class,
		],
		'queue' => [
			'class' => Queue::class,
		],
	],
];
if (isset($_ENV['CALLPAGE_API_KEY'])) {
	$config['components']['callPageClient'] = [
		'class' => CallPageClient::class,
		'apiKey' => $_ENV['CALLPAGE_API_KEY'],
	];
}

if (isset($_ENV['CZATER_API_KEY'])) {
	$config['components']['czater'] = [
		'class' => Czater::class,
		'apiKey' => $_ENV['CZATER_API_KEY'],
	];
}

return $config;

