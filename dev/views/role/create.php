<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app_dev\models\RoleForm */

$this->title = '创建角色';
$this->params['breadcrumbs'][] = ['label' => '角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'user_id' => $user_id,
    ]) ?>

</div>
