<?php

use common\modules\court\Module as CourtModule;

$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

return [
	'id' => 'app-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log', 'teryt', 'lead'],
	'controllerNamespace' => 'console\controllers',
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'modules' => [
		'court' => [
			'class' => CourtModule::class,
			'spiModuleConfig' => [
				'userAuthApiPasswordKey' => $_ENV['SPI_USER_AUTH_PASSWORD_KEY'],
			],
		],
	],
	'controllerMap' => [
		'migrate' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationPath' => [
				'@app/migrations',
				'@yii/log/migrations',
				'@yii/rbac/migrations/',
				'@edzima/teryt/migrations',
			],
			'migrationNamespaces' => [
				'ymaker\email\templates\migrations',
				'yii\queue\db\migrations',
			],
		],
		'fixture' => [
			'class' => 'yii\console\controllers\FixtureController',
			'namespace' => 'common\fixtures',
		],
	],
	'components' => [
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
	],
	'params' => $params,

];
