<?php

namespace common\widgets\charts;

use common\assets\CurrencyFormatterAsset;
use yii\web\AssetBundle;

class ChartsAsset extends AssetBundle {

	public $baseUrl = 'https://cdn.jsdelivr.net/npm/';

	public $js = [
		'apexcharts',
	];

	public $depends = [
		CurrencyFormatterAsset::class,
	];

}
