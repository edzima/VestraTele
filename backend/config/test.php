<?php
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
];
