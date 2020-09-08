<?php
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
	],
];
