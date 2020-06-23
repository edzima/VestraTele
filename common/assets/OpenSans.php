<?php

namespace common\assets;

use yii\web\AssetBundle;

class OpenSans extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
	];
}
