<?php

namespace common\widgets\charts;

use yii\web\AssetBundle;

class ChartsAsset extends AssetBundle {

	public $baseUrl = 'https://cdn.jsdelivr.net/npm/';

	public $js = [
		'apexcharts',
	];

}
