<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error" style="padding: 10px 20px;">

    <h1>没有权限访问</h1>

    <div class="alert alert-danger">
        对不起，您没有权限访问该功能页面，请联系管理员设置权限！
    </div>

</div>
