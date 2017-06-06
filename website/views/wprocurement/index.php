<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\WarehousePlanning;
use libs\common\Flow;
$this->title = '业务操作-物料采购计划下定列表';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="wprocurement/index" />
            <span>采购名称
                <input class="form-text verifySpecial" type="text" placeholder="采购名称" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <span>采购编号
                <input class="form-text verifySpecial" type="text" placeholder="采购编号" name="sn" value="<?= Yii::$app->request->get('sn') ?>" />
            </span>
            <span>供应商
                <input class="form-text verifySpecial" type="text" placeholder="供应商名称" name="supplierName" value="<?= Yii::$app->request->get('supplierName') ?>" />
            </span>
            <span>付款方式
                <?= Html::dropDownList('payment', Yii::$app->request->get('payment'), WarehousePlanning::getPaymentSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            </span>
            <?= Html::hiddenInput("isDownload", 0, ["id" => "isDownload"]); ?>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>物料采购计划下定列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="5%">创建时间</th>
            <th width="10%">采购名称</th>
            <th width="8%">仓库名称</th>
            <th width="5%">编号</th>
            <th width="5%">采购总价</th>
            <th width="7%">计划<br>下单时间</th>
            <th width="8%">流程名称</th>
            <th width="5%">制表人</th>
            <th width="5%">进展<br>状态</th>
            <th width="3%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="8%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="13">暂无符合条件的物料采购计划下定记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
