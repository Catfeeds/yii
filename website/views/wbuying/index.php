<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\common\Flow;
$this->title = '业务操作-物料采购下定入库列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="wbuying/index" />
            <span>订单名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>订单编号
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="sn" value="<?= Yii::$app->request->get('sn') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly"  i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;"  i="1"/>
            </span>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>物料采购下定入库列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">采购名称</th>
            <th width="6%">仓库名称</th>
            <th width="5%">编号</th>
            <th width="5%">采购总价</th>
            <th width="7%">计划<br>下单时间</th>
            <th width="8%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">进展<br>状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="10%">操作</th>
        </tr>
        <?php foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
