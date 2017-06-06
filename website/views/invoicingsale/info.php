<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Admin;
use common\models\ProductCategory;
use common\models\ProductInvoicingSale;
$this->title = '业务操作-物料销存详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>物料销存详情</caption>
            <tr id="quick-form">
                <td width="40%">销存名称：<?= $model->name; ?></td>
                <td width="40%">销存仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
                <td width="30%">销存总价：<?= number_format($model->total_amount, 2) ?></td>
            </tr>
            <tr>
                <td>申请人：<?= Admin::getNameById($model->create_admin_id) ?></td>
                <td>进展状态：<?= $model->showStatus() ?></td>
                <td>下一步操作：<?= $model->status == ProductInvoicingSale::STATUS_NO_SALE ? "销存" : "无" ?></td>
            </tr>
        </table>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>销存物料详情列表</caption>
        <tr>
            <th width="10%">物料名称</th>
            <th width="10%">批次号</th>
            <th width="5%">物料ID</th>
            <th width="10%">供应商</th>
            <th width="5%">供应商<br>物料ID</th>
            <th width="5%">物料类型</th>
            <th width="10%">条形码ID</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="5%">销售价格</th>
            <th width="5%">库存数量</th>
            <th width="5%">销存数量</th>
            <th width="10%" class="saleNum" style="display: none;">实际销售数量</th>
            <th width="10%">销存总价</th>
        </tr>
        <?php foreach($info as $data){ ?>
            <tr>
                <td><?= $data->name ?></td>
                <td><?= $data->batches ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><span class="salePrice_<?= $data->id ?>"><?= $data->sale_price ?></span></td>
                <td><?= $data->product_number ?></td>
                <td><?= $data->buying_number ?></td>
                <td class="saleNum" style="display: none;">
                    <input type="text" name="real[<?= $data->id ?>]" value="" onblur="javascript:ckprto(<?= $data->id ?>)" class="real_<?= $data->id ?>" onkeyup="value=value.replace(/\D/g,'')">
                    <input type="hidden" id="totalSale_<?= $data->id ?>" class="totalSale" value="0" />
                </td>
                <td><?= number_format($data->total_amount, 2) ?></td>
            </tr>
        <?php } ?>
        <tr class="saleNum" style="display: none;">
            <td colspan="7"  style="text-align:left;margin-left: 3px;">
                表单名：<input type="text" name="name" value="" class="verifySpecial"  onkeyup="javascript:validateValue(this)"  style="width:60%;"/></td>
            <td colspan="7" style="text-align:left;margin-left: 3px;">
                补偿金额：<?= Html::textInput("compensationAmount", "0", ["class" => "verifyFloat",  "onkeyup"=>"javascript:CheckInputIntFloat(this)", "style" => "width:60%;"]);?></td>
        </tr>
        <tr class="saleNum" style="display: none;">
            <td colspan="14"  style="text-align:left;margin-left: 3px;">
                损益原因：<input type="text" name="profitLossCause" value="" class="verifySpecial"  onkeyup="javascript:validateValue(this)" width="60%;"/></td>
        </tr>
        <tr class="saleNum" style="display: none;">
            <td colspan="2">应销金额统计：<?php echo number_format($model->sale_amount, 2); ?></td>
            <td colspan="3">实际销售金额统计：<span class="checkSaleAmount">0.00</span></td>
            <td colspan="3">上次结存余额：<span class="lastAmount"><?php echo number_format($model->last_invoic_amount, 2);?></span></td>
            <td colspan="3">预计结存余额：<span class="checkLastAmount">0.00</span></td>
            <td colspan="3">上缴金额：<span class="checkPaidAmount">0.00</span></td>
        </tr>
    </table>
    
    <div class="buttons">
        <?php if($model->status == ProductInvoicingSale::STATUS_NO_SALE){ ?>
            <a class="button blue-button normal-button configRealtime" href="javascript:void(0)">销存盘点</a>
            <a class="button blue-button normal-button" cancel-data="<?= Url::to(['invoicingsale/cancel', "id" => $model->id])?>">取消</a>
            <a class="button blue-button checkRealtime" href="javascript:void(0)" save-data="<?= Url::to(['invoicing/checksale']) ?>" style="display: none;">确定销存</a>
            <a class="button blue-button checkRealtime returnNormal" href="javascript:void(0)" style="display: none;">返回</a>
        <?php } ?>
        <a class="button blue-button normal-button" href="<?= Url::to(['invoicingsale/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?php
$js = <<<JS
    $(function(){
        $(".configRealtime").click(function(){
            $(".saleNum").show();
            $(".buttons .blue-button").hide();
            $(".checkRealtime").show();
        });
        $(".returnNormal").click(function(){
            $(".saleNum").hide();
            $(".buttons .blue-button").hide();
            $(".normal-button").show();
        });
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'realtime');
?>
<script type="text/javascript">
    function ckprto(id){
        var sum = $(".real_"+id).val();
        $("#totalSale_"+id).text("");
        if(!sum) {
            return false;
        }
        var price = $(".salePrice_"+id).text();
        var total = accMul(sum , price);
        $("#totalSale_"+id).text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".totalSale").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".checkSaleAmount").text(parseFloat(ztotal).toFixed(2));
        $(".checkPaidAmount").text(parseFloat(ztotal).toFixed(2));
        var lastAmount = $(".lastAmount").text();
        var totalLast = lastAmount * 1 + ztotal * 1;
        $(".checkLastAmount").text(parseFloat(totalLast).toFixed(2));
    }
</script>