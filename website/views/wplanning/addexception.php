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
use libs\common\Flow;
use libs\Utils;
$this->title = '业务基础数据-新增例外订单';
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>新增例外订单</caption>
       <tr id="quick-form">
            <td style="width:30%">表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%', 'class' => 'verifySpecial']) ?></td>
            <td style="width:20%">仓库：<?= Html::activeDropDownList($model, 'warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", $departmentId), ['prompt' => '请选择']) ?></td>
            <td style="width:30%">供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '请选择', 'class' => 'selSupplier']) ?></td>
            <td style="width:20%">金额：<span class="totalMoney">0</span><?= Html::activeHiddenInput($model, "type")?></td>
        </tr>
        <tr id="quick-form">
            <td colspan="2">表单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:70%', 'value'=>Utils::generateSn(Flow::TYPE_PLANNING_EXCEPTION), 'class' => 'verifySpecial']) ?></td>
            <td colspan="2">采购计划日期：<?= Html::activeTextInput($model, 'planning_date', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
        </tr>
        <tr id="quick-form">
            <td>付款方式：<?= Html::activeDropDownList($model, 'payment', WarehousePlanning::getPaymentSelectData(), ['prompt' => '请选择']) ?></td>
            <td>定金：<?= Html::activeTextInput($model, 'deposit', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
            <td>付款日期：<?= Html::activeTextInput($model, 'payment_term', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
            <td>扣项：<?= Html::activeTextInput($model, 'buckle_amount', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table class="showGoodsList" style="width: 100%" border="1">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料条形码</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">物料分类</th>
                        <th width="10%">实际<br>采购单价</th>
                        <th width="10%">采购数量</th>
                        <th width="10%">采购总价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="10%">操作</th>
                    </tr>
                    <?php for ($i = 1; $i < 6; $i++) { ?>
                    <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                        <td><?= Html::input("text", "goodsName[]", "",['class' => 'verifySpecial']);?></td>
                        <td><?= Html::input("text", "goodsBarcode[]", "",['class' => 'verifySpecial']);?></td>
                        <td><?= Html::listBox("goodsType[]", "", Product::getTypeSelectData(), ["size" => 1]);?></td>
                        <td><?= Html::listBox("goodsCate[]", "", ProductCategory::getCatrgorySelectData(), ["size" => 1]);?></td>
                        <td><?= Html::textInput("goodsPrice[]", "",["class" => "reGoodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $i]);?></td>
                        <td><?= Html::input("text", "goodsNum[]", "",["class" => "selGoodsNum", "onkeyup"=>"value=value.replace(/\D/g,'')", "onblur"=>"javascript:ckprto(".$i.")"]);?></td>
                        <td><span class="goodsTotalMoney"></span></td>
                        <td><?= Html::input("text", "goodsSpec[]", "",["style" => "width:80%",'class' => 'verifySpecial']);?></td>
                        <td><?= Html::input("text", "goodsUnit[]", "",["style" => "width:80%",'class' => 'verifySpecial']);?></td>
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
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['wplanning/addexception']) ?>">保存</a> 
      <a class="button blue-button" href="<?= Url::to(['wplanning/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/addexception') ?>
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
     function ckprto(id){
         $(".tr_"+id+" .goodsTotalMoney").text("");
         totalMoney();
        var sum = $(".tr_"+id+" .selGoodsNum").val();
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .reGoodsPrice").val();
        if(!price) {
            return false;
        }
        var total = accMul(sum , price);
        $(".tr_"+id+" .goodsTotalMoney").text(total);
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
