<?php

use yii\caching\FileCache;

return [
	'components' => [
		'cache' => [
			'class' => FileCache::class,
		],
		'db' => [
			'enableSchemaCache' => true,
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
		],
		'log' => [
			'traceLevel' => 0,
		],
	],
];
