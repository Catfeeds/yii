<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */

$this->title = '更新账户：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '账户', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="admin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
