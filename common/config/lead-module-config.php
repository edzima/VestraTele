<?php

use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\components\LeadDialerManager;
use common\modules\lead\Module;
use yii\db\Connection;

$config = [
	'class' => Module::class,
	'userClass' => User::class,
];

if (YII_ENV_TEST) {
	$config['userNames'] = static function () {
		return ArrayHelper::map(User::find()->asArray()->all(), 'id', 'username');
	};
	$config['dialer'] = [
		'class' => LeadDialerManager::class,
		'callingStatus' => 2,
		'notAnsweredStatus' => 3,
		'answeredStatus' => 4,
	];
}

if (!YII_ENV_TEST
	&& isset($_ENV['DB_LEAD_DSN']) &&
	$_ENV['DB_LEAD_DSN'] !== $_ENV['DB_DSN']) {
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
