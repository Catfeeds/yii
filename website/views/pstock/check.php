<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\Admin;
use libs\common\Flow;
use libs\Utils;
$this->title = '业务操作-盘点申请';
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增盘点记录</caption>
        <tr id="quick-form">
            <td colspan="2">
                表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td style="width:30%">
                盘点单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value'=>Utils::generateSn(Flow::TYPE_CHECKOUT),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td style="width:20%">
                盘点仓库：<?= Html::activeDropDownList($model, 'warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择', 'class' => "selWarehouse"]) ?></td>
        </tr>
        <tr><td colspan="4">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">批次号</th>
                        <th width="8%">条形码</th>
                        <th width="5%">采购<br>单价</th>
                        <th width="5%">销售<br>单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="5%">库存</th>
                        <th width="10%">盘点数量</th>
                        <th width="5%">数量<br>差额</th>
                        <th width="5%">盘点<br>总价</th>
                        <th width="5%">盘点<br>成本</th>
                        <th width="5%">总价<br>差额</th>
                        <th width="5%">操作</th>
                    </tr>
                    <?php for ($i = 1; $i < 6; $i++) { ?>
                        <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                            <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品", 'style' => 'width: 80%;']);?></td>
                            <td><span class="goodsBatches"></span></td>
                            <td><span class="goodsBarcode"></span></td>
                            <td><span class="goodsPrice"></span></td>
                            <td><span class="salePrice"></span></td>
                            <td><span class="goodsSpec"></span></td>
                            <td><span class="goodsUnit"></span></td>
                            <td><span class="goodsStock"></span></td>
                            <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")", "onkeyup"=>"value=value.replace(/\D/g,'')", 'style' => 'width: 80%;']);?></td>
                            <td><span class="numBlan"></span></td>
                            <td><span class="goodsTotalMoney">0.00</span></td>
                            <td><span class="goodsSaleTotal">0.00</span></td>
                            <td><span class="amountBlan">0.00</span></td>
                            <td>
                                <?= Html::hiddenInput("stockId[]", "0",["class" => "selStockId"])?>
                                <a href="javascript:void(0)" class="<?= $i==5 ? "addGoods" : "delGoods" ?>"><?= $i==5 ? "添加" : "删除" ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <tr>
            <td>盘点采购总价：<span class="totalAmount">0.00</span></td>
            <td>盘点销售总价：<span class="totalSaleAmount">0.00</span></td>
            <td>制表人：<?= Yii::$app->user->getIdentity()->username; ?></td>
            <td>制表时间：<?= date("Y-m-d H:i:s") ?></td>
        </tr>
        <tr>
            <td colspan="4">盘点说明：<?= Html::activeTextInput($model, "remark" ,['style' => "width:80%","onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial'])?></td>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['pstock/check']) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php $type = 'check';?>
<?= $this->context->renderPartial('/jquery/warehouseProductStock', compact("type")) ?>
<script>
    function func(selData) {
        $.each(selData, function(k, data){
            if($(".selStockId[value='"+data.selStockId+"']").length > 0) {
                var showTr = $(".selStockId[value='"+data.selStockId+"']").parent("td").parent("tr");
                showTr.find(".selGoodsName").val(data.selectProductName);
                showTr.find(".goodsBatches").text(data.productBatches);
                showTr.find(".goodsBarcode").text(data.productBarcode);
                showTr.find(".goodsPrice").text(data.productPrice);
                showTr.find(".salePrice").text(data.salePrice);
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
                    showTr.find(".goodsPrice").text(data.productPrice);
                    showTr.find(".salePrice").text(data.salePrice);
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
        $(".tr_"+id+" .goodsSaleTotal").text("");
        $(".tr_"+id+" .numBlan").text("");
        $(".tr_"+id+" .amountBlan").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var stock = $(".tr_"+id+" .goodsStock").text();
        var price = $(".tr_"+id+" .goodsPrice").text();
        var salePrice = $(".tr_"+id+" .salePrice").text();
        var total = accMul(stock , price);
        var saleTotal = accMul(stock , salePrice);
        $(".tr_"+id+" .goodsTotalMoney").text(parseFloat(total).toFixed(2));
        $(".tr_"+id+" .goodsSaleTotal").text(parseFloat(saleTotal).toFixed(2));
        var numBlan = stock * 1 - sum * 1;
        $(".tr_"+id+" .numBlan").text(numBlan);
        var amountBlan = numBlan * 1  * Number(s2.replace(".", "")) / Math.pow(10, m);
        $(".tr_"+id+" .amountBlan").text(parseFloat(amountBlan).toFixed(2));
        var ztotal = 0, stotal =  0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".goodsSaleTotal").each(function(){
            stotal = stotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
        $(".totalSaleAmount").text(parseFloat(stotal).toFixed(2));
    }
    function totalMoney() {
        var ztotal = 0, stotal =  0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".goodsSaleTotal").each(function(){
            stotal = stotal * 1 + $(this).text() * 1;
        });
        $(".totalAmount").text(parseFloat(ztotal).toFixed(2));
        $(".totalSaleAmount").text(parseFloat(stotal).toFixed(2));
    }
</script>
