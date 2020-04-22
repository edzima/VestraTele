<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle {

	public $sourcePath = '@webroot/static/calendar';

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
