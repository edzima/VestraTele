<?php /** @noinspection PhpIncludeInspection */

//check if not in same subnet /16 (255.255.0.0)
if ((ip2long(@$_SERVER['REMOTE_ADDR']) ^ ip2long(@$_SERVER['SERVER_ADDR'])) >= 2 ** 16) {
	die('You are not allowed to access this file.');
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_IS_FRONTEND') or define('YII_IS_FRONTEND', true);


require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
	require __DIR__ . '/../../common/config/main.php',
	require __DIR__ . '/../../common/config/main-local.php',
	require __DIR__ . '/../../common/config/test.php',
	require __DIR__ . '/../../common/config/test-local.php',
	require __DIR__ . '/../config/main.php',
	require __DIR__ . '/../config/main-local.php',
	require __DIR__ . '/../config/test.php',
	require __DIR__ . '/../config/test-local.php'
);

(new yii\web\Application($config))->run();
