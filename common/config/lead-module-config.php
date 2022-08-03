<?php

use common\models\user\User;
use common\modules\lead\Module;
use yii\db\Connection;

$config = [
	'class' => Module::class,
	'userClass' => User::class,
];

if (isset($_ENV['DB_LEAD_DSN']) &&
	$_ENV['DB_LEAD_DSN'] !== $_ENV['DB_DSN']
) {
	$config['components']['db'] = [
		'class' => Connection::class,
		'dsn' => getenv('DB_LEAD_DSN'),
		'username' => getenv('DB_LEAD_USERNAME'),
		'password' => getenv('DB_LEAD_PASSWORD'),
		'tablePrefix' => getenv('DB_LEAD_TABLE_PREFIX'),
		'charset' => 'utf8',
		'enableSchemaCache' => YII_ENV_PROD,
	];
}

return $config;
