<?php

use common\components\rbac\ManagerFactory;
use motion\i18n\ConfigLanguageProvider;

return [
	'id' => 'app-backend-tests',
	'homeUrl' => null,
	'defaultRoute' => 'user/customer/index',
	'components' => [
		'assetManager' => [
			'basePath' => __DIR__ . '/../web/assets',
		],
		'accessManagerFactory' => [
			'class' => ManagerFactory::class,
			'mapAppsIds' => [
				ManagerFactory::FRONTEND_APP => 'app-frontend-tests',
				ManagerFactory::BACKEND_APP => 'app-backend-tests',
			],
		],
		'urlManager' => [
			'showScriptName' => true,
		],
		'request' => [
			'cookieValidationKey' => 'test',
		],
	],
	'modules' => [
		'email-templates' => [
			'languageProvider' => [
				'class' => ConfigLanguageProvider::class,
				'languages' => [
					[
						'locale' => 'en-US',
						'label' => 'English',
					],
				],
				'defaultLanguage' => [
					'locale' => 'en-US',
					'label' => 'English',
				],
			],
		],
	],
];
