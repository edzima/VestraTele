<?php

namespace common\assets;

use yii\web\AssetBundle;

class Toggle extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-toggle';

    public $js = [
        'js/bootstrap-toggle.min.js'
    ];

    public $css = [
        'css/bootstrap-toggle.min.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
