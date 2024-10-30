<?php

use common\components\rbac\ManagerFactory;

return [
	'id' => 'app-frontend-tests',
	'homeUrl' => null,
	'components' => [
		'assetManager' => [
			'basePath' => __DIR__ . '/../web/assets',
		],
		'urlManager' => [
			'showScriptName' => true,
		],
		'request' => [
			'cookieValidationKey' => 'test',
		],
		'accessManagerFactory' => [
			'class' => ManagerFactory::class,
			'mapAppsIds' => [
				ManagerFactory::FRONTEND_APP => 'app-frontend-tests',
			],
		],
	],
];
