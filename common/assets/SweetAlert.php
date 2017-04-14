<?php
namespace common\assets;
use yii\web\AssetBundle;
class SweetAlert extends AssetBundle
{
    public $sourcePath = '@bower/sweetalert/dist';
    public $css = [
        'sweetalert.css',
    ];
    public $js = [
        'sweetalert.min.js'
    ];
}
