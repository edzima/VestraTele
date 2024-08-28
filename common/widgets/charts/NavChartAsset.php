<?php

namespace common\widgets\charts;

use yii\web\AssetBundle;

class NavChartAsset extends AssetBundle {

	public $sourcePath = '@common/widgets/charts/assets';

	public $js = [
		'nav.js',
	];

	public $css = [
		'nav.css',
	];
}
