<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Product;
use common\models\ProductCategory;
use common\models\Admin;
use common\models\Department;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;

$this->title = '销存管理-销存实时库存管理';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="invoicing/stock" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
                <span>所属部门
                    <?= Html::dropDownList('departmentId', Yii::$app->request->get('departmentId'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
                </span>
            <?php //} ?>
            <span>商品
                <input class="form-text verifySpecial" type="text" placeholder="" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>  
            </span>
          <!--查询他们部门下面 仓库 下面的部门-->
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= Admin::getDepName();?>-销存实时库存管理</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">物料名</th>
            <th width="8%">批次号</th>
            <th width="3%">物料ID</th>
            <th width="5%">所属仓库</th>
            <th width="5%">所属部门</th>
            <th width="5%">物料类型</th>
            <th width="5%">条形码ID</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购价格</th>
            <th width="3%">销售定价</th>
            <th width="4%">库存预警</th>
            <th width="5%">存货数量</th>
            <th width="5%" class="saleNum" style="display: none;">已销数量</th>
        </tr>
        <?php foreach($listDatas as $data){ 
                $warehouseItem = Warehouse::findOne($data->warehouse_id);
                if($data->type == WarehousePlanning::TYPE_EXCEPTION) {
                    $productItem = WarehouseBuyingProduct::findOne($data->product_id);
        ?>
        <tr>
            <td><?= $data->id ?></td>
            <td><?= $productItem->name ?></td>
            <td><?= $data->batches ?></td>
            <td><?= 0 ?></td>
            <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
            <td><?= Department::getNameById($warehouseItem->department_id); ?></td>
            <td><?= ProductCategory::getNameById($productItem->material_type)?></td>
            <td><?= $productItem->num ?></td>
            <td><?= $productItem->spec ?></td>
            <td><?= $productItem->unit ?></td>
            <td><?= number_format($productItem->price, 2) ?></td>
            <td><?= number_format($productItem->purchase_price, 2) ?></td>
            <td><?= "不需要" ?></td>
            <td><?= $data->number ?></td>
            <td class="saleNum" style="display: none;"><input type="text" name="real[<?= $data->id ?>]"></td>
        </tr>
        <?php }else {
            $productItem = Product::findOne($data->product_id); ?>
            <tr>
                <td><?= $data->id ?></td>
                <td><?= $productItem->name ?></td>
                <td><?= $data->batches ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
                <td><?= Department::getNameById($warehouseItem->department_id); ?></td>
                <td><?= ProductCategory::getNameById($productItem->product_category_id) ?></td>
                <td><?= $productItem->barcode ?></td>
                <td><?= $productItem->spec ?></td>
                <td><?= $productItem->unit ?></td>
                <td><?= number_format($productItem->purchase_price, 2) ?></td>
                <td><?= number_format($productItem->sale_price, 2) ?></td>
                <td><?= $productItem->showInventoryWarning() ?></td>
                <td><?= $data->number ?></td>
                <td class="saleNum" style="display: none;"><input type="text" name="real[<?= $data->id ?>]"></td>
            </tr>
        <?php }  } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <div class="buttons" style="padding: 30px 15px;">
        <a class="button blue-button" href="<?= Url::to(['pstock/checkout']) ?>" style="margin: 0px 10px;">出库申请</a>
        <a class="button blue-button" href="<?= Url::to(['wmaterial/add']) ?>" style="margin: 0px 10px;">退仓申请</a>
        <a class="button blue-button" href="<?= Url::to(['pstock/transferdep']) ?>" style="margin: 0px 10px;">转货申请</a>
        <a class="button blue-button" href="javascript:void(0)" onclick="window.location.reload()" style="margin: 0px 10px;">商品数据刷新</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>

