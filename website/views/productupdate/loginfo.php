<?php
use common\models\Supplier;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\ProductCategory;
use common\models\Product;
use libs\common\Flow;
use common\models\CommonRemark;
$this->title = '业务基础数据--物料修改日志详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>物料修改日志详情</caption>
            <tr id="quick-form">
                <td>物料名称：<?= $model->name; ?></td>
                <td>供应商：<?= Supplier::getNameById($model->supplier_id); ?></td>
                <td>供应商出品ID：<?= $model->supplier_product_id; ?></td>
                <td>出品编码：<?= $model->num; ?></td>
            </tr>
            <tr>
                <td>物料类别：<?= Product::showTypeName($model->material_type); ?></td>
                <td>物料分类：<?= ProductCategory::getNameById($model->product_category_id); ?></td>
                <td>条形码：<?= $model->barcode; ?></td>
                <td>进货参考价格：<?= number_format($model->purchase_price, 2); ?></td>
            </tr>
            <tr>
                <td>销售价格：<?= number_format($model->sale_price, 2); ?></td>
                <td>库存警告：<?= $model->inventory_warning >0 ? $model->inventory_warning : "不需要"; ?></td>
                <td>规格：<?= $model->spec; ?></td>
                <td>单位：<?= $model->unit; ?></td>
            </tr>
            <tr>
                <td colspan="2">是否需要批次号：<?= Product::showBatchesName($model->is_batches); ?></td>
                <td colspan="2">修改时间：<?= $model->create_time; ?></td>
            </tr>
        </table>
    </div>
    <div class="buttons">
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
