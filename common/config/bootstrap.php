<?php

use ymaker\email\templates\repositories\EmailTemplatesRepository;
use ymaker\email\templates\repositories\EmailTemplatesRepositoryInterface;

Yii::setAlias('@root', dirname(__DIR__, 2));
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(__DIR__, 2) . '/frontend');
Yii::setAlias('@backend', dirname(__DIR__, 2) . '/backend');
Yii::setAlias('@console', dirname(__DIR__, 2) . '/console');
Yii::setAlias('@storage', dirname(__DIR__, 2) . '/storage');
Yii::setAlias('@protected', dirname(__DIR__, 2) . '/protected');

/**
 * Setting url aliases
 */
require __DIR__ . '/../env.php';
if (!YII_ENV_TEST) {
	Yii::setAlias('frontendUrl', getenv('FRONTEND_URL'));
	Yii::setAlias('backendUrl', getenv('BACKEND_URL'));
	Yii::setAlias('storageUrl', getenv('STORAGE_URL'));
} else {
	Yii::setAlias('frontendUrl', 'http://frontend.dev');
	Yii::setAlias('backendUrl', 'http://backend.dev');
	Yii::setAlias('storageUrl', 'http://storage.dev');
}

Yii::$container->set(
	EmailTemplatesRepositoryInterface::class,
	EmailTemplatesRepository::class
);


