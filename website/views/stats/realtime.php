<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\ProductCategory;
$this->title = '查询统计-实时库存统计';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/realtime" />
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>库区
                <?= Html::dropDownList('warehouseId', Yii::$app->request->get('warehouseId'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $department_id), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>物料ID或名称
                <input class="form-text verifySpecial" type="text" placeholder="" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>  
            </span>
            <span>供应商
                <input class="form-text verifySpecial" type="text" placeholder="" name="supplierName" value="<?= Yii::$app->request->get('supplierName') ?>" />  
            </span>
            <span>分类
                <?= Html::dropDownList('product_category_id', Yii::$app->request->get('product_category_id'), ProductCategory::getCatrgorySelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span> 
            <?= Html::hiddenInput("isDownload", 0, ["id" => "isDownload"]); ?>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>实时库存统计</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="3%">物料ID</th>
            <th width="8%">物料名</th>
            <th width="8%">批次号</th>
            <th width="5%">所属仓库</th>
            <th width="5%">供应商ID<br>名称</th>
            <th width="3%">供应商<br>物料ID</th>
            <th width="4%">物料类型</th>
            <th width="5%">条形码ID</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购价格</th>
            <th width="3%">销售定价</th>
            <th width="4%">库存预警</th>
            <th width="5%">库存</th>
        </tr>
        <?php if($listDatas) { foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr>
            <td colspan="15" style="text-align: center;">暂无记录</td>
        </tr>
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
<?= $this->context->renderPartial('/jquery/excel') ?>

