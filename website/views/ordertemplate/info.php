<?php
use yii\helpers\Url;
use common\models\Supplier;
use common\models\Admin;
use common\models\Product;
use common\models\ProductCategory;
$this->title = '业务设置-订单模版详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>订单模版详情</caption>
        <tr id="quick-form">
            <td style="width: 30%;">模版名称：<?= $model->name ?></td>
            <td style="width: 20%;">供应商：<?= Supplier::getNameById($model->supplier_id) ?></td>
            <td style="width: 30%;">订单总价：<?= number_format($model->total_amount, 2) ?></td>
            <td style="width: 20%;">制定人：<?= Admin::getNameById($model->create_admin_id) ?></td>
        </tr>
        <tr>
            <td>付款方式：<?= $model->showPayment() ?></td>
            <td>定金：<?= number_format($model->deposit, 2) ?></td>
            <td colspan="2">制定时间：<?= $model->create_time ?></td>
        </tr>
        <tr>
            <td colspan="2">批准时间：<?= $model->approval_time ?></td>
            <td colspan="2">验收时间：<?= $model->operation_time ?></td>
        </tr>
        <tr>
            <td colspan="2">用途说明：<?= $model->common ? $model->common : "无" ?></td>
            <td colspan="2">验收说明：<?= $model->operation_cause ? $model->operation_cause : "无" ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table id="table-list" class="table-list">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料ID</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">物料分类</th>
                        <th width="10%">出品编号</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="8%">采购价格</th>
                        <th width="8%">预定采购数量</th>
                        <th width="10%">采购总价</th>
                    </tr>
                    <?php foreach($info as $data){ ?>
                        <tr>
                            <td><?= $data->name ?></td>
                            <td><?= $data->product_id ?></td>
                            <td><?= Product::showTypeName($data->material_type) ?></td>
                            <td><?= ProductCategory::getNameById($data->product_cate_id) ?></td>
                            <td><?= $data->num ?></td>
                            <td><?= $data->spec ?></td>
                            <td><?= $data->unit ?></td>
                            <td><?= number_format($data->purchase_price, 2) ?></td>
                            <td><?= $data->buying_number ?></td>
                            <td><?= number_format($data->total_amount, 2) ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="<?= Url::to(['wplanning/addroutine', 'tempId' => $model->id]) ?>">生成订单</a> 
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>