<?php

namespace common\modules\czater\controllers;

use common\modules\czater\Module;
use yii\data\ArrayDataProvider;
use yii\web\Controller;

abstract class BaseController extends Controller {

	/**
	 * @var Module
	 */
	public $module;

	protected int $pageSize = 100;
	protected int $totalCount = 10000;

	protected function createDataProvider(): ArrayDataProvider {
		$provider = new ArrayDataProvider();
		$provider->pagination->pageSizeLimit = [1, $this->pageSize];
		$provider->pagination->pageSize = $this->pageSize;
		$provider->pagination->totalCount = $this->totalCount;
		return $provider;
	}

}
