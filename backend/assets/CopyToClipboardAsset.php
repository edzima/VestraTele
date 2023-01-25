<?php

namespace backend\assets;

use yii\web\AssetBundle;

class CopyToClipboardAsset extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
		'static/js/copyToClipboard.js',
	];
}
