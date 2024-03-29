<?php

namespace frontend\assets;

use common\assets\CopyToClipboardAsset;
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
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'common\assets\OpenSans',
		'rmrevin\yii\fontawesome\AssetBundle',
		CopyToClipboardAsset::class,
	];
}
