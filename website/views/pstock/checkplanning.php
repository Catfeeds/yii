<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use libs\Utils;
$this->title = '业务操作-盘点申请';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>盘点申请</caption>
        <tr id="quick-form">
            <td>表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:50%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:50%', 'value'=>Utils::generateSn('PD'),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
            <td>盘点总价：<span class="totalAmount">0.00</span><?= Html::activeHiddenInput($model, "check_planning_id") ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="5">
                <table class="showGoodsList" style="width: 100%">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料条形码</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">采购单价</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="5%">库存</th>
                        <th width="10%">盘点数量</th>
                        <th width="10%">盘点总价</th>
                    </tr>
                    <?php foreach ($pStockAll as $stockVal) {  $productItem = $productAll[$stockVal->product_id];?>
                        <tr class="tr_<?= $stockVal->id ?>">
                            <td><?= $productItem->name;?></td>
                            <td><?= $productItem->barcode;?></td>
                            <td><?= $productItem->showType();?></td>
                            <td><span class="goodsPrice"><?= $productItem->purchase_price;?></span></td>
                            <td><?= $productItem->spec;?></td>
                            <td><?= $productItem->unit;?></td>
                            <td><?= $stockVal->number;?></td>
                            <td><?= Html::input("text", "goodsNum[".$stockVal->id."]", "0",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$stockVal->id.")", "onkeyup"=>"value=value.replace(/\D/g,'')", "maxleng" => 8]);?></td>
                            <td><span class="goodsTotalMoney">0</span><?= Html::hiddenInput("stockId[".$stockVal->id."]", $stockVal->id, ["class" => "selStockId"])?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
   <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['pstock/checkplanning', "planningId" => Yii::$app->request->get("planningId")]) ?>">保存</a> 
        <a class="button blue-button" href="<?= Url::to(['checkplanning/info', 'id' => $model->check_planning_id]) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<script>
    function ckprto(id){
        var sum = $(".tr_"+id+" .selGoodsNum").val().replace(/\D/g,'');
        $(".tr_"+id+" .selGoodsNum").val(sum);
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
