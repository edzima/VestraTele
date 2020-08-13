<?php

use yii\db\Connection;

$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/params.php')
);

return [
	'id' => 'app-console',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'console\controllers',
	'bootstrap' => ['gii'],
	'modules' => [
		'gii' => 'yii\gii\Module',
	],
	'params' => $params,
	'components' => [
		'oldDb' => [
			'class' => Connection::class,
			'dsn' => getenv('OLD_DB_DSN'),
			'username' => getenv('OLD_DB_USERNAME'),
			'password' => getenv('OLD_DB_PASSWORD'),
			'tablePrefix' => getenv('OLD_DB_TABLE_PREFIX'),
			'charset' => 'utf8',
			'enableSchemaCache' => YII_ENV_PROD,
		],
	],
];
