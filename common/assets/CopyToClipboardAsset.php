<?php

namespace common\assets;

use yii\web\AssetBundle;

class CopyToClipboardAsset extends AssetBundle {

	public $sourcePath = __DIR__;

	public $js = [
		'js/copyToClipboard.js',
	];
}
