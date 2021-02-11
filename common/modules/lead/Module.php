<?php

namespace common\modules\lead;

use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface {

	public $controllerNamespace = 'common\modules\lead\controllers';

	public string $userClass;

	public bool $onlyOwner = false;

	public function bootstrap($app) {

	}

	public static function userClass(): string {
		$instance = static::getInstance();
		if ($instance === null) {
			throw new InvalidConfigException('Lead module must be configured.');
		}
		return $instance->userClass;
	}

}
