<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\common\Flow;

$this->title = '业务基础数据-供应商';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="product/index" />
            <span>名称
                <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>状态
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>流程状态
                <?= Html::dropDownList('modify_status', Yii::$app->request->get('modify_status'), Flow::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <!--<caption>供应商</caption>-->
        <tr>
            <th width="3%">序号</th>
            <th width="8%">物料名</th>
            <th width="3%">物料ID</th>
            <th width="5%">供应商</th>
            <th width="3%">供应商<br>出品ID</th>
            <th width="5%">物料<br>类型</th>
            <th width="5%">条形码ID</th>
            <th width="5%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购<br>价格</th>
            <th width="3%">销售<br>定价</th>
            <th width="4%">库存预警</th>
            <th width="3%">当前<br>状态</th>
            <th width="3%">流程<br>状态</th>
            <th width="5%">下一步<br>操作</th>
            <th width="5%">下一步<br>操作人</th>
            <th width="5%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php }} else { ?>
        <tr><td colspan="17">暂无符合条件的物料</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
