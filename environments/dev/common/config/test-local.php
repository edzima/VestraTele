<?php
return [
	'components' => [
		'db' => [
			'dsn' => getenv('TEST_DB_DSN'),
			'username' => getenv('TEST_DB_USERNAME'),
			'password' => getenv('TEST_DB_PASSWORD'),
		],
	],
];
