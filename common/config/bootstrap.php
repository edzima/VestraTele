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
$schema = getenv('SCHEMA_URL');
Yii::setAlias('frontendUrl', $schema . getenv('FRONTEND_URL'));
Yii::setAlias('backendUrl', $schema . getenv('BACKEND_URL'));
Yii::setAlias('storageUrl', $schema . getenv('STORAGE_URL'));
