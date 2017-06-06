<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = '业务基础数据-供应商';
?>


 <?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="supplier/index" />
            <span>供应商
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <span>等级
            <?= Html::dropDownList('level', Yii::$app->request->get('level'), $model::getLevelSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>付款账期
            <?= Html::dropDownList('pay_period', Yii::$app->request->get('pay_period'), $model::getPayPeriodSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>供应商列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="40%">供应商</th>
            <th width="20%">供应商 编号</th>
            <th width="10%">分级</th>
            <th width="10%">付款账期</th>
            <th width="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="7">暂无符合条件的供应商记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['supplier/form']) ?>" href="javascript:void(0)">新增</a>
        <a class="button blue-button" href="<?= Url::to(['supplier/downtemplate']) ?>" >下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['supplier/import']) ?>"  href="javascript:void(0)">导入</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>