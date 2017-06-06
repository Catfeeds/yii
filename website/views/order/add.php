<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
$this->title = '销存管理-新增订单';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增订单</caption>
        <?= $this->context->renderPartial('_form', compact(['model','department_id'])) ?>
    </table>
   <div class="buttons">
      <a class="button blue-button"  save-data="<?= Url::to(['order/create']) ?>">保存</a> 
      <a class="button blue-button" href="<?= Url::to(['order/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?= $this->context->renderPartial('/jquery/DePartmentStock',compact('department_id')) ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<script>
    function func(selData) {
        $.each(selData, function(k, data){
            if($(".selGoodsId[value='"+data.selStockId+"']").length > 0) {
                var showTr = $(".selStockId[value='"+data.selStockId+"']").parent("td").parent("tr");
                showTr.find(".selGoodsName").val(data.selectProductName);
                showTr.find(".goodsBarcode").text(data.productBarcode);
                showTr.find(".goodsType").text(data.productType);
                showTr.find(".goodsCate").text(data.productCate);
                showTr.find(".goodsPrice").text(data.salePrice);
                showTr.find(".reGoodsPrice").val(data.salePrice);
                showTr.find(".goodsSpec").text(data.productSpec);
                showTr.find(".goodsUnit").text(data.productUnit);
                showTr.find(".selGoodsNum").val(data.selGoodsNum);
                 showTr.find(".warehouseId").val(data.warehouseId);
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
                    showTr.find(".goodsPrice").text(data.salePrice);
                    showTr.find(".reGoodsPrice").val(data.salePrice);
                    showTr.find(".goodsSpec").text(data.productSpec);
                    showTr.find(".goodsUnit").text(data.productUnit);
                    showTr.find(".selGoodsNum").val(data.selGoodsNum);
                    showTr.find(".warehouseId").val(data.warehouseId);
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
        var price = $(".tr_"+id+" .reGoodsPrice").val();
        $(".tr_"+id+" .goodsTotalMoney").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var total =  accMul(sum , price);
        $(".tr_"+id+" .goodsTotalMoney").text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalMoney").text(parseFloat(ztotal).toFixed(2));
    }
    function totalMoney() {
        var ztotal = 0;
        $(".goodsTotalMoney").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".totalMoney").text(parseFloat(ztotal).toFixed(2));
    }
</script>
