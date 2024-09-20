<?php

namespace common\widgets\charts;

use common\assets\CurrencyFormatterAsset;
use yii\web\AssetBundle;

class ChartsAsset extends AssetBundle {

	public $baseUrl = 'https://cdn.jsdelivr.net/npm/';

	//3.52 has default disable wheel zoom, but not valid data label for area not stacked chart.
	public $js = [
		'apexcharts@3.50',
	];

	public $depends = [
		CurrencyFormatterAsset::class,
		NavChartAsset::class,
	];

}
