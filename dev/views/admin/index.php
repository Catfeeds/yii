<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '账户';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建账户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            'department_id',
            'role_id',
            'username',
            'mobile',
            'status',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {auth} {delete}',
                'buttons' => [
                    'auth' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-credit-card']), \yii\helpers\Url::to(['role/item', 'user_id' => $model->id]));
                    },
                ],
            ],
        ],
    ]); ?>
</div>
