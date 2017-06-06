<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="grid-view">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>角色名称</th>
                <th>角色描述</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($roles as $role){ ?>
            <tr>
                <td align="center"><?= $role->name ?></td>
                <td align="center"><?= $role->description ?></td>
                <td align="center">
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
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>