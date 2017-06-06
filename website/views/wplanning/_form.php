<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Warehouse;
use common\models\Supplier;
use common\models\WarehousePlanning;
use common\models\Admin;
use libs\common\Flow;
use libs\Utils;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<tr id="quick-form">
    <td style="width: 30%">表单名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
    <td style="width: 20%">仓库：<?= Html::activeDropDownList($model, 'warehouse_id', Warehouse::getAllByStatus(Warehouse::STATUS_OK, '', $departmentId), ['prompt' => '请选择']) ?></td>
    <td style="width: 30%">采购单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value'=>Utils::generateSn(Flow::TYPE_PLANNING),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
    <td style="width: 20%">金额：<span class="totalMoney">0</span><?= Html::activeHiddenInput($model, "type")?></td>
</tr>
<tr id="quick-form">
    <td>供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '请选择', 'class' => 'selSupplier']) ?></td>
    <td>付款方式：<?= Html::activeDropDownList($model, 'payment', WarehousePlanning::getPaymentSelectData(), ['prompt' => '请选择']) ?></td>
    <td>定金：<?= Html::activeTextInput($model, 'deposit', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
    <td>扣项：<?= Html::activeTextInput($model, 'buckle_amount', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
</tr>
<tr>
    <td colspan="2">采购计划日期：<?= Html::activeTextInput($model, 'planning_date', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
    <td colspan="2">付款日期：<?= Html::activeTextInput($model, 'payment_term', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
</tr>
<tr><td colspan="4">选择商品</td></tr>
<tr class="showSelProduct">
    <td colspan="4">
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
            <?php for ($i = 1; $i < 6; $i++) { ?>
            <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                <td><span class="goodsBarcode"></span></td>
                <td><span class="goodsType"></span></td>
                <td><span class="goodsCate"></span></td>
                <td><span class="goodsPrice"></span></td>
                <td><?= Html::textInput("goodsPrice[]", "",["class" => "reGoodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $i]);?></td>
                <td><?= Html::textInput("goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")", "onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
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
<?= $this->context->renderPartial('/jquery/js') ?>
<?php
$js = <<<JS
    $(document).on("blur", ".reGoodsPrice", function(){ 
        var id = $(this).attr("i");
        ckprto(id);
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addPlanning');
?>