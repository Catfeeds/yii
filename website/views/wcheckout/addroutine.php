<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\ProductStock;
use common\models\ProductCategory;
use libs\common\Flow;
use libs\Utils;
$this->title = '业务操作-出库申请';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增例行出库记录</caption>
        <tr id="quick-form">
            <td style="width:30%;">表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:50%']) ?></td>
            <td style="width:20%;">出库仓库：总部<?= Html::activeHiddenInput($model, "warehouse_id", ["class" => "selWarehouse"])?></td>
            <td style="width:30%;">入库仓库：<?= Html::activeDropDownList($model, 'receive_warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, Warehouse::SALE_YES), ['prompt' => '请选择']) ?></td>
            <td style="width:20%;">出库总价：<span class="totalAmount"><?= number_format($model->total_amount, 2);?></span></td>
        </tr>
        <tr>
            <td colspan="2">出库单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:50%', 'value'=>Utils::generateSn(Flow::TYPE_CHECKOUT)]) ?></td>
            <td colspan="2">是否扣仓：<?= Html::activeDropDownList($model, 'is_buckle', Flow::getBuckleSelectData()) ?></td>
        </tr>
        <tr><td colspan="5">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="5">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                       <th width="12%">物料名称</th>
                        <th width="12%">批次号</th>
                        <th width="10%">出品编码</th>
                        <th width="5%">物料<br>类型</th>
                        <th width="8%">进货<br>单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="10%">库存</th>
                        <th width="10%">数量</th>
                        <th width="10%">出库总价</th>
                        <th width="10%">操作</th>
                    </tr>
                    <?php foreach($info as $key => $data){ 
                        $pstockItem = ProductStock::findOne($data->pstock_id);?>
                        <tr class="showGoodsTr tr_<?= $key+1 ?>" i="1">
                            <td><?= Html::input("text", "goodsName[]", $data->name,["class" => "selGoodsName", "readonly" => "readonly", "i" => $key+1, 'placeholder' => "点击选择商品"]);?></td>
                            <td><span class="goodsBatches"><?= $data->batches ?></span></td>
                            <td><span class="goodsBarcode"><?= $data->num ?></span></td>
                            <td><span class="goodsCate"><?= ProductCategory::getNameById($data->material_type) ?></span></td>
                            <td><span class="goodsPrice"><?= $data->purchase_price ?></span></td>
                            <td><span class="goodsSpec"><?= $data->spec ?></span></td>
                            <td><span class="goodsUnit"><?= $data->unit ?></span></td>
                            <td><span class="goodsStock"><?= $pstockItem->number ?></span></td>
                            <td><?= Html::input("text", "goodsNum[]", $data->product_number * $combNum,["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".($key + 1).")", "onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
                            <td><span class="goodsTotalMoney"><?= $data->total_amount * $combNum;?></span></td>
                            <td>
                                <?= Html::hiddenInput("stockId[]", $data->pstock_id ,["class" => "selStockId"])?>
                                <a href="javascript:void(0)" class="<?= count($info) >= 5 && count($info) == $key+1 ? "addGoods" : "delGoods" ?>">
                                <?= count($info) >= 5 && count($info) == $key+1 ? "添加" : "删除" ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php for ($i = count($info)+1; $i < 6; $i++) { ?>
                        <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                        <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                        <td><span class="goodsBatches"></span></td>
                        <td><span class="goodsBarcode"></span></td>
                        <td><span class="goodsCate"></span></td>
                        <td><span class="goodsPrice"></span></td>
                        <td><span class="goodsSpec"></span></td>
                        <td><span class="goodsUnit"></span></td>
                        <td><span class="goodsStock"></span></td>
                        <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")", ]);?></td>
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
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['pstock/checkout']) ?>">保存</a> 
      <a class="button blue-button" onclick="javascript:history.back(-1);">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php $type = "routine"; $num = count($info) > 5 ? count($info) + 1 : 6;?>
<?= $this->context->renderPartial('/jquery/warehouseProductStock', compact("num", "type")) ?>
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
