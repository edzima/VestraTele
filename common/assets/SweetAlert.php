<?php
namespace common\assets;
use yii\web\AssetBundle;
class SweetAlert extends AssetBundle
{
    public $sourcePath = '@bower/sweetalert2/dist';
    public $css = [
        'sweetalert2.css',
    ];
    public $js = [
        'sweetalert2.min.js'
    ];
}
