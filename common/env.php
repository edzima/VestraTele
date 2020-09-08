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


