<?php

use yii\swiftmailer\Mailer;

return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => getenv('DB_DSN'),
			'username' => getenv('DB_USERNAME'),
			'password' => getenv('DB_PASSWORD'),
			'charset' => 'utf8',
		],
		'mailer' => [
			'class' => Mailer::class,
			'viewPath' => '@common/mail',
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'encryption' => getenv('EMAIL_ENCRYPTION'),
				'host' => getenv('EMAIL_SMTP_HOST'),
				'port' => getenv('EMAIL_SMTP_PORT'),
				'username' => getenv('EMAIL_USERNAME'),
				'password' => getenv('EMAIL_PASSWORD'),
			],
		],
	],
];
