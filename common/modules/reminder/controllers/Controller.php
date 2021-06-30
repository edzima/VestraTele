<?php

namespace common\modules\reminder\controllers;

use common\modules\reminder\Module;
use yii\web\Controller as BaseController;

abstract class Controller extends BaseController {

	/* @var Module */
	public $module;
}
