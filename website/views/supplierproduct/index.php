<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
$this->title = '业务基础数据-供应商出品列表';
$supplierAll = Supplier::getSupplierSelectData();
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="supplierproduct/index" />
            <span>出品名称
            <input class="form-text verifySpecial" type="text" placeholder="出品名称..." name="name" value="<?= Yii::$app->request->get('name') ?>" />
            </span>
            <span>供应商
            <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'), $supplierAll, ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>状态
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>供应商物料出品列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="20%">名称</th>
            <th width="10%">供应商</th>
            <th width="10%">供应商<br>出品编码</th>
            <th width="10%">进货<br>参考价格</th>
            <th width="8%">规格</th>
            <th width="8%">单位</th>
            <th width="5%">物料<br>类别</th>
            <th width="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="10">暂无符合条件的供应商物料出品记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['supplierproduct/form']) ?>" href="javascript:void(0)" style="margin: 0 15px;">新增</a>
        <a class="button blue-button" href="<?= Url::to(['supplierproduct/downtemplate']) ?>" style="margin: 0 15px;">下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['supplierproduct/import']) ?>"  href="javascript:void(0)" style="margin: 0 15px;">导入</a>
        <a class="button blue-button" download-excel='subSearch' style="margin: 0 15px;">导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>