<?php

namespace common\modules\czater\controllers;

use common\modules\czater\Module;
use yii\web\Controller;

abstract class BaseController extends Controller {

	/**
	 * @var Module
	 */
	public $module;
}
