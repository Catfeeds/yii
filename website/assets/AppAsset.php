<?php

namespace app_web\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/reset.css',
        'style/form.css',
        'style/global.css',
        'style/date_input.css',
    ];
    public $js = [
            'script/jquery-migrate-1.2.1.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
