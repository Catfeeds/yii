<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = '系统基础数据-备份数据';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
  
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <!--<caption>供应商</caption>-->
        <tr>
      
            <th width="10%">表名</th>
            <th width="20%">数据量</th>
            <th width="20%">数据大小</th>
            <th width="20%">创建时间</th>

            <th width="20%">操作</th>
        </tr>
        <?php foreach($listDatas as $key=>$data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data','key'])) ?>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
     
   
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
