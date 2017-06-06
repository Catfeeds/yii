<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\WarehousePlanning;
use libs\Utils;
$this->title = '业务设置-添加订单模版';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增订单模版</caption>
        <tr id="quick-form">
            <td style="width:50%">模版名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
            <td style="width:25%">供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '请选择', 'class' => "selSupplier"]) ?></td>
            <td style="width:25%">订单总价：<span class="totalAmount">0.00</span></td>
        </tr>
        <tr id="quick-form">
            <td>付款方式：<?= Html::activeDropDownList($model, 'payment', WarehousePlanning::getPaymentSelectData(), ['prompt' => '请选择']) ?></td>
            <td colspan="2">定金：<?= Html::activeTextInput($model, 'deposit', ['style' => 'width:50%', "onkeyup"=>"value=value.replace(/\D/g,'')", 'class' => 'verifyFloat']) ?></td>
        </tr>
        <?= Html::activeHiddenInput($model, 'sn', ['value'=>Utils::generateSn('OT')]) ?>
        <tr id="quick-form">
            <td>批准时间：<?= Html::activeTextInput($model, 'approval_time', ['style' => 'width:70%', 'class' => "selDate"]) ?></td>
            <td colspan="2">验收时间：<?= Html::activeTextInput($model, 'operation_time', ['style' => 'width:70%', 'class' => "selDate"]) ?></td>
        </tr>
        <tr id="quick-form">
            <td>用途说明：<?= Html::activeTextInput($model, 'common', ['style' => 'width:70%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 50, 'class' => 'verifySpecial']) ?></td>
            <td colspan="2">验收说明：<?= Html::activeTextInput($model, 'operation_cause', ['style' => 'width:70%', "onkeyup" => "javascript:validateValue(this)", "maxlength" => 50, 'class' => 'verifySpecial']) ?></td>
        </tr>
        <tr><td colspan="4">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">出品编码</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">物料分类</th>
                        <th width="10%">进货单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="10%">数量</th>
                        <th width="10%">总价</th>
                        <th width="10%">操作</th>
                    </tr>
                    <?php for ($i = 1; $i < 6; $i++) { ?>
                    <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                        <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                        <td><span class="goodsBarcode"></span></td>
                        <td><span class="goodsType"></span></td>
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
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['ordertemplate/add']) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php $type = "ordertemplate";?>
<?= $this->context->renderPartial('/jquery/supplierProductStock', compact("type")) ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<script>   
    function func(selData) {
        $.each(selData, function(k, data){
            if($(".selGoodsId[value='"+data.selStockId+"']").length > 0) {
                var showTr = $(".selStockId[value='"+data.selStockId+"']").parent("td").parent("tr");
                showTr.find(".selGoodsName").val(data.selectProductName);
                showTr.find(".goodsBarcode").text(data.productBarcode);
                showTr.find(".goodsType").text(data.productType);
                showTr.find(".goodsCate").text(data.productCate);
                showTr.find(".goodsPrice").text(data.productPrice);
                showTr.find(".goodsSpec").text(data.productSpec);
                showTr.find(".goodsUnit").text(data.productUnit);
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
                    showTr.find(".goodsBarcode").text(data.productBarcode);
                    showTr.find(".goodsType").text(data.productType);
                    showTr.find(".goodsCate").text(data.productCate);
                    showTr.find(".goodsPrice").text(data.productPrice);
                    showTr.find(".goodsSpec").text(data.productSpec);
                    showTr.find(".goodsUnit").text(data.productUnit);
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
