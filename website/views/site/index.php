<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = '业务提醒';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
   

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务提醒</caption>
        <tr>
            <th width="10%">序号</th>
            <th width="50%">内容</th>
            <th width="30%">操作</th>

      
        </tr>
        <?php foreach($listDatas as $key=>$data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data','key'])) ?>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
