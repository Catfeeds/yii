<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use libs\Utils;
use common\models\Admin;
use libs\common\Flow;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
$this->title = '销存管理-新增实时销存';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="taleft table-list">
        <caption>新增实时销存</caption>
        <tr id="quick-form">
            <td style="width:50%;">
                表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
            <td style="width:25%;">
                收银订单仓库：<?= Html::activeDropDownList($model, 'warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, Warehouse::SALE_YES, $departmentId), ['prompt' => '请选择', 'class' => "selWarehouse"]) ?></td>
            <td style="width:25%;">
                收银订单总价：<span class="totalAmount">0.00</span></td>
        </tr>
        <?= Html::activeHiddenInput($model, 'sn', ['value'=>Utils::generateSn('IS')]) ?>
        <tr>
            <td>当前结存金额：<?= Html::activeTextInput($model, 'last_invoic_amount', ['style' => 'width:50%', 'class' => 'verifyFloat', 'onkeyup'=>"javascript:CheckInputIntFloat(this)"])?></td>
            <td colspan="2">上缴金额：<?= Html::activeTextInput($model, 'paid_amount', ['style' => 'width:50%', 'class' => 'paidAmount verifyFloat', 'onkeyup'=>"javascript:CheckInputIntFloat(this)"])?></td>
        </tr>
        <tr><td colspan="4">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">批次号</th>
                        <th width="6%">物料条形码</th>
                        <th width="5%">物料<br>类型</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="5%">库存</th>
                        <th width="5%">预计<br>销售单价</th>
                        <th width="8%">实际<br>销售单价</th>
                        <th width="8%">实际<br>销存数量</th>
                        <th width="8%">订单总价</th>
                        <th width="5%">操作</th>
                    </tr>
                    <?php for ($i = 1; $i < 6; $i++) { ?>
                        <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                            <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                            <td><span class="goodsBatches"></span></td>
                            <td><span class="goodsBarcode"></span></td>
                            <td><span class="goodsCate"></span></td>
                            <td><span class="goodsSpec"></span></td>
                            <td><span class="goodsUnit"></span></td>
                            <td><span class="goodsStock"></span></td>
                            <td><span class="goodsSalePrice"></span></td>
                            <td><?= Html::input("text", "salePrice[]", "",["class" => "reGoodsPrice verifyFloat",'style' => 'width: 90%;', 'i' => $i, "onblur"=>"javascript:ckprto(".$i.")"]);?></td>
                            <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")","onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
                            <td><span class="goodsTotalMoney"></span></td>
                            <td>
                                <?= Html::hiddenInput("stockId[]", "0",["class" => "selStockId"])?>
                                <a href="javascript:void(0)" class="<?= $i==5 ? "addGoods" : "delGoods" ?>"><?= $i==5 ? "添加" : "删除" ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['pstock/invoicingsale']) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?><?php $type = 'invoicingSale';?>
<?= $this->context->renderPartial('/jquery/warehouseProductStock', compact("type")) ?>
<script>
    function func(selData) {
        $.each(selData, function(k, data){
            if($(".selStockId[value='"+data.selStockId+"']").length > 0) {
                var showTr = $(".selStockId[value='"+data.selStockId+"']").parent("td").parent("tr");
                showTr.find(".selGoodsName").val(data.selectProductName);
                showTr.find(".goodsBatches").text(data.productBatches);
                showTr.find(".goodsBarcode").text(data.productBarcode);
                showTr.find(".goodsCate").text(data.productCate);
                showTr.find(".goodsSalePrice").text(data.salePrice);
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
                    showTr.find(".selStockId").val(data.selStockId);
                    showTr.find(".selGoodsName").val(data.selectProductName);
                    showTr.find(".goodsBatches").text(data.productBatches);
                    showTr.find(".goodsBarcode").text(data.productBarcode);
                    showTr.find(".goodsCate").text(data.productCate);
                    showTr.find(".goodsSalePrice").text(data.salePrice);
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
        var sum = $(".tr_"+id+" .selGoodsNum").val().replace(/\D/g,'');
        $(".tr_"+id+" .selGoodsNum").val(sum);
        $(".tr_"+id+" .goodsTotalMoney").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .reGoodsPrice").val();
        if(!price) {
            return false;
        }
        var total = accMul(sum , price);
        $(".tr_"+id+" .goodsTotalMoney").text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
        $(".paidAmount").val(parseFloat(ztotal).toFixed(2));
    }
    function totalMoney() {
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
        $(".paidAmount").val(parseFloat(ztotal).toFixed(2));
    }
</script>
