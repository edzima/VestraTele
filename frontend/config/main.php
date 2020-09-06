<?php

$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

$config = [
	'id' => 'app-frontend',
	'homeUrl' => Yii::getAlias('@frontendUrl'),
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],

	'controllerNamespace' => 'frontend\controllers',
	'modules' => [
		'account' => [
			'class' => 'frontend\modules\account\Module',
		],
		'noty' => [
			'class' => 'lo\modules\noty\Module',
		],
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
		'comment' => [
			'class' => 'yii2mod\comments\Module',
			// when admin can edit comments on frontend
			'enableInlineEdit' => true,
		],

	],
	'components' => [
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'request' => [
			'cookieValidationKey' => getenv('FRONTEND_COOKIE_VALIDATION_KEY'),
			'csrfParam' => '_csrf-frontend',
			'baseUrl' => '',
			//@todo created csrf validation in Vue
			'enableCsrfValidation' => false,
		],
		'user' => [
			'identityClass' => 'common\models\User',
			'loginUrl' => ['/account/sign-in/login'],
			'enableAutoLogin' => true,
			'identityCookie' => ['name' => '_identity-front', 'httpOnly' => true],
		],
		'session' => [
			// this is the name of the session cookie used for login on the frontend
			'name' => 'app-front',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'urlManager' => require(__DIR__ . '/_urlManager.php'),
		'cache' => require(__DIR__ . '/_cache.php'),
	],
	'as beforeAction' => [
		'class' => 'common\behaviors\LastActionBehavior',
	],
	'params' => $params,
];

if (YII_ENV) { //YII_ENV_PROD
	// maintenance mode
	$config['bootstrap'] = ['maintenance'];
	$config['components']['maintenance'] = [
		'class' => 'common\components\maintenance\Maintenance',
		'enabled' => function ($app) {
			return $app->keyStorage->get('frontend.maintenance');
		},
		'route' => 'maintenance/index',
		'message' => 'Przerwa techniczna',
		// year-month-day hour:minute:second
		'time' => '0000-00-00 00:00:00', // время окончания работ
	];

	// Compressed assets
	/*$config['components']['assetManager'] = [
	   'bundles' => require(__DIR__ . '/assets/_bundles.php'),
	];*/
}

if (YII_DEBUG) { //YII_ENV_DEV
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => ['*']
		//'traceLine' => '<a href="phpstorm://open?url={file}&line={line}">{file}:{line}</a>'
	];

	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => ['*'],
	];
}

return $config;
