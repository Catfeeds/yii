<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\ProductStock;
use common\models\ProductCategory;
use common\models\Supplier;
use common\models\WarehousePlanning;
use libs\Utils;
$this->title = '业务操作-退货申请';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
    
        <tr id="quick-form">
            <td>表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:50%',"onkeyup" => "javascript:validateValue(this)", 'class' => "verifySpecial"]) ?></td>
            <td>退货单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:50%', 'value'=>Utils::generateSn(Flow::TYPE_MATERIALRETURN),"onkeyup" => "javascript:validateValue(this)", 'class' => "verifySpecial"]) ?></td>
            <td>退货仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
            <td>退货供应商：<?= Supplier::getNameById($model->supplier_id)?></td>
            <td>退货总价：<span class="totalAmount"><?= number_format($model->total_amount, 2);?></span></td>
        </tr>
        <tr>
            <td colspan="2">退货时间：<?= Html::activeTextInput($model, 'planning_date', ['style' => 'width:50%', 'class' => "selDate", "readonly" => "readonly"]) ?></td>
            <td colspan="3">退款时间：<?= Html::activeTextInput($model, 'payment_term', ['style' => 'width:50%', 'class' => "selDate", "readonly" => "readonly"]) ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="5">
                <table class="showGoodsList table-list" style="width: 100%">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">批次号</th>
                        <th width="10%">条形码</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">采购单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="5%">采购数量</th>
                        <th width="10%">退货数量</th>
                        <th width="10%">退货总价</th>
                    </tr>
                    <?php foreach($buyingProduct as $data){ 
                        $stockId = $buyingItem->type == WarehousePlanning::TYPE_EXCEPTION ? $data->id : $data->product_id;?>
                        <tr class="tr_<?= $buyingItem->type . "_" . $stockId ?>">
                            <td><?php echo $data->batches;?></td>
                            <td><?php echo $data->name;?></td>
                            <td><?= $data->num ?></td>
                            <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                            <td><span class="goodsPrice"><?= $data->purchase_price ?></span></td>
                            <td><?= $data->spec ?></td>
                            <td><?= $data->unit ?></td>
                            <td><?= $data->product_number;?></td>
                            <td><?= Html::input("text", "goodsNum[".$stockId."]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto('".$buyingItem->type . "_" . $stockId."')", "onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
                            <td>
                                <span class="goodsTotalMoney"></span>
                                <?= Html::hiddenInput("stockId[".$stockId."]", $stockId ,["class" => "selStockId"])?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
            <tr id="quick-form">
                <td colspan="5">退货原因：<?= Html::activeTextInput($model, 'common', ['style' => 'width:50%',"onkeyup" => "javascript:validateValue(this)", 'class' => "verifySpecial"]) ?></td>
            </tr>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['wmaterial/addbuying', "buyingId" => $model->buying_id]) ?>">保存</a> 
      <a class="button blue-button" href="<?= Url::to(['wcheckout/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<script>
    function ckprto(id){
        var sum = $(".tr_"+id+" .selGoodsNum").val();
        $(".tr_"+id+" .goodsTotalMoney").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .goodsPrice").text();
        var total =  accMul(sum , price);
        $(".tr_"+id+" .goodsTotalMoney").text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
    }
    function totalMoney() {
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
    }
</script>
