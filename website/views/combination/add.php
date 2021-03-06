<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\Admin;
use libs\Utils;
$this->title = '业务设置-添加组合物料模版';
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>添加组合物料模版</caption>
        <tr id="quick-form" >
            <td style="width:30%;">模版名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:60%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
            <td style="width:20%;">出库仓库：总部<?= Html::activeHiddenInput($model, "warehouse_id", ["class" => "selWarehouse"])?></td>
            <td style="width:30%;">批准时间：<?= Html::activeTextInput($model, 'approval_time', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
            <td style="width:20%;">订单总价：<span class="totalAmount">0.00</span></td>
        </tr>
        <tr>
            <td>付款方式：<?= Html::activeDropDownList($model, 'payment', WarehousePlanning::getPaymentSelectData(), ['prompt' => '请选择']) ?></td>
            <td>定金：<?= Html::activeTextInput($model, 'deposit', ['style' => 'width:50%', "onkeyup"=>"value=value.replace(/\D/g,'')"]) ?></td>
            <td colspan="2">验收时间：<?= Html::activeTextInput($model, 'operation_time', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
        </tr>
        <?= Html::activeHiddenInput($model, 'sn', ['value'=>Utils::generateSn('OT')]) ?>
        <tr>
            <td colspan="2">用途说明：<?= Html::activeTextInput($model, 'common', ['style' => 'width:50%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 50, 'class' => 'verifySpecial']) ?></td>
            <td colspan="2">验收说明：<?= Html::activeTextInput($model, 'operation_cause', ['style' => 'width:50%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 50, 'class' => 'verifySpecial']) ?></td>
        </tr>
        <tr><td colspan="4">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="12%">物料名称</th>
                        <th width="12%">批次号</th>
                        <th width="10%">出品编码</th>
                        <th width="5%">物料<br>类型</th>
                        <th width="8%">进货<br>单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="10%">数量</th>
                        <th width="10%">总价</th>
                        <th width="10%">操作</th>
                    </tr>
                    <?php for ($i = 1; $i < 6; $i++) { ?>
                    <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                        <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                        <td><span class="goodsBatches"></span></td>
                        <td><span class="goodsBarcode"></span></td>
                        <td><span class="goodsCate"></span></td>
                        <td><span class="goodsPrice"></span></td>
                        <td><span class="goodsSpec"></span></td>
                        <td><span class="goodsUnit"></span></td>
                        <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")", "onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
                        <td><span class="goodsTotalMoney"></span></td>
                        <td>
                            <?= Html::hiddenInput("goodsId[]", "0",["class" => "selGoodsId"])?>
                            <a href="javascript:void(0)" class="<?= $i==5 ? "addGoods" : "delGoods" ?>"><?= $i==5 ? "添加" : "删除" ?></a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['combination/add']) ?>">保存</a> 
      <a class="button blue-button" href="<?= Url::to(['combination/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/warehouseProductStock') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<script>
    function func(selData) {
        $.each(selData, function(k, data){
            if($(".selGoodsId[value='"+data.selStockId+"']").length > 0) {
                var showTr = $(".selGoodsId[value='"+data.selStockId+"']").parent("td").parent("tr");
                showTr.find(".selGoodsName").val(data.selectProductName);
                showTr.find(".goodsBatches").text(data.productBatches);
                showTr.find(".goodsBarcode").text(data.productBarcode);
                showTr.find(".goodsCate").text(data.productCate);
                showTr.find(".goodsPrice").text(data.productPrice);
                showTr.find(".goodsSpec").text(data.productSpec);
                showTr.find(".goodsUnit").text(data.productUnit);     
                showTr.find(".goodsStock").text(data.productStock);
                showTr.find(".selGoodsNum").val(data.selGoodsNum);
                showTr.attr("i", "1");
                var i = showTr.find(".selGoodsName").attr("i");
                ckprto(i);
                return true;
            }
            if($(".showGoodsTr[i='0']").length == 0) {
                $(".addGoods").click();
            }
            $(".showGoodsTr").each(function(index){
                if($(this).attr("i") == "0") {
                    var showTr = $(this);
                    showTr.find(".selGoodsId").val(data.selStockId);
                    showTr.find(".selGoodsName").val(data.selectProductName);
                    showTr.find(".goodsBatches").text(data.productBatches);
                    showTr.find(".goodsBarcode").text(data.productBarcode);
                    showTr.find(".goodsCate").text(data.productCate);
                    showTr.find(".goodsPrice").text(data.productPrice);
                    showTr.find(".goodsSpec").text(data.productSpec);
                    showTr.find(".goodsUnit").text(data.productUnit);     
                    showTr.find(".goodsStock").text(data.productStock);
                    showTr.find(".selGoodsNum").val(data.selGoodsNum);
                    showTr.attr("i", "1");
                    var i = showTr.find(".selGoodsName").attr("i");
                    ckprto(i);
                    return false;
                }
            });
        });
        $(".nui-msgbox .nui-msgbox-close").click();
    }  
    function ckprto(id){
        var sum = $(".tr_"+id+" .selGoodsNum").val();
        $(".tr_"+id+" .goodsTotalMoney").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .goodsPrice").text();
        var total = accMul(sum , price);
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
