<?php

namespace backend\assets;

use common\assets\OpenSans;
use dmstr\web\AdminLteAsset;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'static/css/style.css',
	];
	public $js = [
		'static/js/toggle.js',
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		AdminLteAsset::class,
		'common\assets\Html5shiv',
		OpenSans::class,
	];
}
