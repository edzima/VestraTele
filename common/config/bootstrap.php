<?php

/**
 * Setting path aliases
 */
Yii::setAlias('root', realpath(__DIR__ . '/../../'));
Yii::setAlias('common', realpath(__DIR__ . '/../../common'));
Yii::setAlias('frontend', realpath(__DIR__ . '/../../frontend'));
Yii::setAlias('backend', realpath(__DIR__ . '/../../backend'));
Yii::setAlias('console', realpath(__DIR__ . '/../../console'));
Yii::setAlias('storage', realpath(__DIR__ . '/../../storage'));

/**
 * Setting url aliases
 */

function buildUrl(string $url) {
	$schema = getenv('SCHEMA_URL');
	$reversePort = getenv('REVERSE_PROXY_PORT');
	$url = $schema . $url;
	if ($reversePort != 80) {
		$url .= ":$reversePort";
	}
	return $url;
}

Yii::setAlias('frontendUrl', buildUrl(getenv('FRONTEND_URL')));
Yii::setAlias('backendUrl', buildUrl(getenv('BACKEND_URL')));
Yii::setAlias('storageUrl', buildUrl(getenv('STORAGE_URL')));
