<?php

namespace common\modules\calendar;

use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle {

	public $sourcePath = '@common/modules/calendar/static';

	public $js = [
		'js/chunk-vendors.js',
		'js/app.js',
	];

	public $css = [
		'css/chunk-vendors.css',
		'css/app.css',
	];

	public $publishOptions = [
		'forceCopy' => YII_ENV_DEV,
	];

}
