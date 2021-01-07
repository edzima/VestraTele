<?php

use yii\caching\DummyCache;

return [
	'components' => [
		'cache' => [
			'class' => DummyCache::class,
		],
		'db' => [
			'enableSchemaCache' => false,
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => true,
		],
		'log' => [
			'traceLevel' => 3,
		],
	],
];
