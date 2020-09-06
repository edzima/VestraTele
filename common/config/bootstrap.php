<?php
Yii::setAlias('@root', dirname(__DIR__, 2));
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(__DIR__, 2) . '/frontend');
Yii::setAlias('@backend', dirname(__DIR__, 2) . '/backend');
Yii::setAlias('@console', dirname(__DIR__, 2) . '/console');
Yii::setAlias('@storage', dirname(__DIR__, 2) . '/storage');

/**
 * Setting url aliases
 */
Yii::setAlias('frontendUrl', getenv('FRONTEND_URL'));
Yii::setAlias('backendUrl', getenv('BACKEND_URL'));
Yii::setAlias('storageUrl', getenv('STORAGE_URL'));
