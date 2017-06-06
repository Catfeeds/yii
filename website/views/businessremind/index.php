<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = '业务提醒';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
   <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="businessremind/index" />
            <span>状态</span>
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), ["0" => "待处理", "1" => "已处理"], [ 'class' => 'form-select']) ?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务提醒</caption>
        <tr>
            <th width="10%">序号</th>
            <th width="50%">内容</th>
            <th width="30%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="3">暂无符合条件的业务提醒记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
