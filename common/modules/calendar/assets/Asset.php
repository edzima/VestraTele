<?php

namespace common\modules\calendar\assets;

use yii\web\AssetBundle;

class Asset extends AssetBundle {

	public $sourcePath = __DIR__;

	public $js = [
		'js/chunk-vendors.js',
		'js/app.js',
	];

	public $css = [
		'css/chunk-vendors.css',
		'css/app.css',
	];

	public $publishOptions = [
		'forceCopy' => true,
	];
}
