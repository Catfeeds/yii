<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限';
$this->params['breadcrumbs'][] = ['label' => '角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="grid-view">
        <?= Html::beginForm(['auth', 'name' => $name], 'post')?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th><?= Html::checkbox('check', false, ['id' => 'check_all'])?></th>
                <th>名称</th>
                <th>描述</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($auths as $auth){?>
            <tr>
                <td><?= Html::checkbox('auth[]', in_array($auth->name, $roleNodes) ? true : false, ['value' => $auth->name, 'class' => 'auth_check'])?></td>
                <td><?= $auth->name ?></td>
                <td><?= $auth->description ?></td>
            </tr>
            <?php }?>
            <tr>
                <td align="center" colspan="3">
                    <?= Html::submitButton('提交',['class'=>'btn btn-primary'])?>
                </td>
            </tr>
            </tbody>
        </table>
        <?= Html::endForm()?>
    </div>
</div>