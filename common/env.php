<?php

use Dotenv\Dotenv;

/**
 * Require shortcuts
 */
require_once(__DIR__ . '/shortcuts.php');
/**
 * Load application environment from .env file
 */
$dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__) . '/');
$dotenv->load();

$baseRequired = [
	'FRONTEND_URL',
	'BACKEND_URL',
	'STORAGE_URL',
];

$databaseRequired = !YII_ENV_TEST
	? [
		'DB_DSN',
		'DB_USERNAME',
		'DB_PASSWORD',
	]
	: [
		'TEST_DB_DSN',
		'TEST_DB_USERNAME',
		'TEST_DB_PASSWORD',
	];

$emailRequired = YII_ENV_PROD
	? [
		'EMAIL_ENCRYPTION',
		'EMAIL_SMTP_HOST',
		'EMAIL_SMTP_PORT',
		'EMAIL_USERNAME',
		'EMAIL_PASSWORD',
		'EMAIL_ADMIN',
		'EMAIL_SUPPORT',
		'EMAIL_SENDER',
	]
	: [];

$required = array_merge($baseRequired, $databaseRequired, $emailRequired);
$dotenv->required($required);

