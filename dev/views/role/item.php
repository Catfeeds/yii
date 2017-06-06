<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '账户角色';
$this->params['breadcrumbs'][] = ['label' => '角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="grid-view">
        <?= Html::beginForm() ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>选择</th>
                <th>角色、部门、权限组名称</th>
                <th>分配权限</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($roles as $role){ ?>
            <tr>
                <td><?= Html::checkbox('roles[]', in_array($role->name, $userRoles), ['value' => $role->name, 'id' => $role->name]) ?></td>
                <td><?= Html::label($role->name) ?></td>
                <td>
                    <?= Html::a('修改', ['update', 'name' => $role->name, 'user_id' => $user_id]) ?>
                    &nbsp;
                    <?= Html::a('权限', ['auth', 'name' => $role->name, 'user_id' => $user_id]) ?>
                    &nbsp;
                    <?= Html::a('删除', ['delete', 'name' => $role->name], [
                        'data' => [
                            'confirm' => '确认删除吗？',
                            'method' => 'post',
                        ]
                    ]) ?>
                </td>
            <tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" align="center">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </td>
            <tr>
            </tfoot>
        </table>
        <?= Html::endForm() ?>
    </div>
</div>