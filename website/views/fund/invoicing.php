<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

use common\models\Supplier;
$this->title = '资金流水-销存核实';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="ordertemplate/index" />
            <span>模版名称</span>
            <input class="form-text" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            <span>供应商</span>
            <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), Supplier::getSupplierSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            <span>开始时间</span>
            <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            <span>结束时间</span>
            <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <!--<caption>供应商</caption>-->
        <tr>
            <th width="3%">序号</th>
            <th width="20%">模版名称</th>
            <th width="8%">供应商</th>
            <th width="10%">模版制定时间</th>
            <th width="5%">订单付款方式</th>
            <th width="5%">定金</th>
            <th width="10%">用途说明</th>
            <th width="5%">制定人</th>
            <th width="8%">操作</th>
        </tr>
    </table>
    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['ordertemplate/add']) ?>">新增</a>
        <a class="button blue-button" href="<?= Url::to(['ordertemplate/export']) ?>">导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
