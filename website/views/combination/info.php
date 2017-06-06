<?php
use yii\helpers\Url;
use common\models\Warehouse;
use common\models\Admin;
use common\models\ProductCategory;
$this->title = '业务设置-组合物料模版详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>组合物料模版详情</caption>
        <tr id="quick-form" >
            <td style="width:30%;">模版名称：<?= $model->name ?></td>
            <td style="width:20%;">仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
            <td style="width:30%;">批准时间：<?= $model->approval_time ?></td>
            <td style="width:20%;">订单总价：<span class="totalAmount"><?= number_format($model->total_amount, 2);?></span></td>
        </tr>
        <tr>
            <td>付款方式：<?= $model->showPayment() ?></td>
            <td colspan="2">验收时间：<?= $model->operation_time ?></td>
            <td>定金：<?= number_format($model->deposit, 2) ?></td>
        </tr>
        <tr>
            <td colspan="2">用途说明：<?= $model->common ? $model->common : "无" ?></td>
            <td colspan="2">验收说明：<?= $model->operation_cause ? $model->operation_cause : "无" ?></td>
        </tr>
    </table>
    <table id="table-list" class="table-list">
        <tr>
            <th width="12%">批次号</th>
            <th width="12%">物料名称</th>
            <th width="10%">物料ID</th>
            <th width="5%">物料<br>类型</th>
            <th width="10%">出品编号</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="8%">采购价格</th>
            <th width="8%">预定<br>采购数量</th>
            <th width="10%">采购总价</th>
        </tr>
        <?php foreach($info as $data){ ?>
            <tr>
                <td><?= $data->batches ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><?= $data->product_number ?></td>
                <td><?= number_format($data->total_amount, 2) ?></td>
            </tr>
        <?php } ?>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['combination/index']) ?>">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>