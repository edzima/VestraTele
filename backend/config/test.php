<?php

use motion\i18n\ConfigLanguageProvider;

return [
	'id' => 'app-backend-tests',
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
