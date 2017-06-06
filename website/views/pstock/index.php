<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Admin;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
$this->title = '业务操作-库存管理';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="pstock/index" />
            <span>关键词
                <input class="form-text verifySpecial" type="text" placeholder="物料名或物料ID" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>供应商
                <?= Html::dropDownList('supplier_id', Yii::$app->request->get('supplier_id'),  Supplier::getSupplierSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <span>仓库名称
                <?= Html::dropDownList('warehouseId', Yii::$app->request->get('warehouseId'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, '', $departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>库存管理列表</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">批次号</th>
            <th width="3%">物料ID</th>
            <th width="8%">物料名</th>
            <th width="5%">所属仓库</th>
            <th width="5%">供应商</th>
            <th width="5%">物料<br>类型</th>
            <th width="5%">条形码ID</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购<br>价格</th>
            <th width="3%">销售<br>定价</th>
            <th width="4%">库存预警</th>
            <th width="5%">库存</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="14">暂无符合条件的物料库存记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    
</div>


<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
