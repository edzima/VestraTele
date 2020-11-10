<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'static/css/style.css',
	];
	public $js = [
		'static/js/ajax-modal-popup.js',
		'static/js/yii_overrides.js',
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'common\assets\OpenSans',
		'rmrevin\yii\fontawesome\AssetBundle',
	//	'common\assets\SweetAlert',
	];
}
