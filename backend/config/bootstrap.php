<?php

use ymaker\email\templates\repositories\EmailTemplatesRepository;
use ymaker\email\templates\repositories\EmailTemplatesRepositoryInterface;

Yii::$container->set(
	EmailTemplatesRepositoryInterface::class,
	EmailTemplatesRepository::class
);
