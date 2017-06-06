<?php

namespace app_web\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/login.css',
    ];
    public $js = [
        'script/login.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
