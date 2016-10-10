<?php

use Dotenv\Dotenv;

/**
 * Require shortcuts
 */
require_once(__DIR__ . '/shortcuts.php');

/**
 * Load application environment from .env file
 */
$dotenv = new Dotenv(dirname(__DIR__));
$dotenv->load();

/**
 * Init application constants
 */
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') === 'true');
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'prod');
