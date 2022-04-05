<?php

namespace common\modules\czater;

use yii\base\Module as BaseModule;
use yii\di\Instance;

/**
 * Czater Module
 *
 * @property Czater $czater
 *
 */
class Module extends BaseModule {

	public $controllerNamespace = 'common\modules\czater\controllers';

	/**
	 * @var Czater|array|string
	 */
	public $czater = 'czater';

	public function init(): void {
		parent::init();
		$this->czater = Instance::ensure($this->czater, Czater::class);
	}

}
