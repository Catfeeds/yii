<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Warehouse;
use common\models\WarehousePlanning;
use common\models\Supplier;
use common\models\Product;
use common\models\ProductCategory;
use libs\Utils;
use libs\common\Flow;
$this->title = '业务基础数据-新增例行订单';
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增例行订单</caption>
        <tr id="quick-form">
            <td style="width: 30%;">表单名:<?= Html::activeTextInput($model, 'name', ['style' => 'width:60%']) ?></td>
            <td style="width: 20%;">仓库:<?= Html::activeDropDownList($model, 'warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择']) ?></td>
            <td style="width: 30%;">供应商:<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '请选择', 'class' => 'selSupplier']) ?></td>
            <td style="width: 20%;">金额：<span class="totalMoney"><?= number_format($item->total_amount, 2)?></span><?= Html::activeHiddenInput($model, "type")?></td>
        </tr>
        <tr>
            <td>采购单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value'=>Utils::generateSn(Flow::TYPE_PLANNING_ROUTINE)]) ?></td>
            <td>支付方式： <?= $model->showPayment() ?><?php echo Html::activeHiddenInput($model, "payment");?></td>
            <td>定金： <?= number_format($model->deposit)?><?php echo Html::activeHiddenInput($model, "deposit");?></td>
            <td>扣项：<?= Html::activeTextInput($model, 'buckle_amount', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
        </tr>
        <tr>
            <td colspan="2">采购计划日期：<?= Html::activeTextInput($model, 'planning_date', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
            <td colspan="2">付款日期：<?= Html::activeTextInput($model, 'payment_term', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
        </tr>
        <tr><td colspan="5">选择商品</td></tr>
        <tr class="showSelProduct">
            <td colspan="5">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料条形码</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">物料分类</th>
                        <th width="10%">预计<br>采购单价</th>
                        <th width="10%">实际<br>采购单价</th>
                        <th width="10%">采购数量</th>
                        <th width="10%">采购总价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="10%">操作</th>
                    </tr>
                    <?php foreach($info as $key => $data){ ?>
                        <tr class="showGoodsTr tr_<?= $key+1 ?>" i="1">
                            <td><?= Html::input("text", "goodsName[]", $data->name,["class" => "selGoodsName", "readonly" => "readonly", "i" => $key+1, 'placeholder' => "点击选择商品"]);?></td>
                            <td><span class="goodsBarcode"><?= $data->num ?></span></td>
                            <td><span class="goodsType"><?= Product::showTypeName($data->material_type) ?></span></td>
                            <td><span class="goodsCate"><?= ProductCategory::getNameById($data->product_cate_id) ?></span></td>
                            <td><span class="goodsPrice"><?= $data->purchase_price ?></span></td>
                            <td><?= Html::textInput("goodsPrice[]", $data->purchase_price,["class" => "reGoodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $key+1]);?></td>
                            <td><?= Html::input("text", "goodsNum[]", $data->buying_number,["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".($key + 1).")"]);?></td>
                            <td><span class="goodsTotalMoney"><?= number_format($data->total_amount, 2) ?></span></td>
                            <td><span class="goodsSpec"><?= $data->spec ?></span></td>
                            <td><span class="goodsUnit"><?= $data->unit ?></span></td>
                            <td>
                                <?= Html::hiddenInput("goodsId[]", $data->product_id ,["class" => "selGoodsId"])?>
                                <a href="javascript:void(0)" class="<?= count($info) >= 5 && count($info) == $key+1 ? "addGoods" : "delGoods" ?>">
                                <?= count($info) >= 5 && count($info) == $key+1 ? "添加" : "删除" ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php for ($i = count($info)+1; $i < 6; $i++) { ?>
                    <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                        <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                        <td><span class="goodsBarcode"></span></td>
                        <td><span class="goodsType"></span></td>
                        <td><span class="goodsCate"></span></td>
                        <td><span class="goodsPrice"></span></td>
                        <td><?= Html::textInput("goodsPrice[]", "",["class" => "reGoodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $i]);?></td>
                        <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")"]);?></td>
                        <td><span class="goodsTotalMoney"></span></td>
                        <td><span class="goodsSpec"></span></td>
                        <td><span class="goodsUnit"></span></td>
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
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['wplanning/create']) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1);">返回</a>
    </div>
    <?php ActiveForm::end(); ?>    
</div>
<?php $num = count($info) > 5 ? count($info) + 1 : 6;?>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?= $this->context->renderPartial('/jquery/supplierProductStock', compact('num')) ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php
$js = <<<JS
    $(document).on("blur", ".reGoodsPrice", function(){ 
        var id = $(this).attr("i");
        ckprto(id);
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addPlanning');
?>
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
        var price = $(".tr_"+id+" .reGoodsPrice").val();
        var total = accMul(sum , price);
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
