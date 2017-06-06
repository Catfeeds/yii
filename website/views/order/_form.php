<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Warehouse;
use common\models\Supplier;
use common\models\Admin;
use libs\common\Flow;
use libs\Utils;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;
?>
<tr id="quick-form">
    <td style="width: 30%">表名：<?= Html::activeTextInput($model, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
    <td style="width: 20%">表单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value'=>Utils::generateSn(Flow::STATUS_CREATE_ORDER),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
    <td style="width: 20%">下订员工：<?= Html::activeDropDownList($model, 'create_admin_id',Admin::getDepAdmin($department_id), ['prompt' => '请选择']) ?></td>
    <td style="width: 30%">下订时间：<?= Html::activeTextInput($model, 'create_time', ['style' => 'width:50%', 'class' => "selDate"]) ?></td>
    <td style="display:none"> <?= Html::activeTextInput($model, 'department_id', ['value'=>$department_id]) ?></td>
</tr>
<tr id="quick-form">
    <td>特别优惠减免：<?= Html::activeTextInput($model, 'benefit_money', ['style' => 'width:50%', 'class' => 'verifyFloat']) ?></td>
    <td>订单处理人：<?= Html::activeDropDownList($model,'operation_admin_id',Admin::getDepAdmin($department_id), ['prompt' => '请选择']) ?></td>
    <td>收支员工：<?= Html::activeDropDownList($model,'custom_pay_service_id',Admin::getDepAdmin($department_id), ['prompt' => '请选择']) ?></td>
    <td>顾客单位：<?= Html::activeTextInput($model, 'customer_company', ['style' => 'width:50%',"onkeyup" => "javascript:validateValue(this)"]) ?></td>
</tr>
<tr>
<td >执行员工：<?= Html::activeDropDownList($model,'operation_admin_id',Admin::getDepAdmin($department_id), ['prompt' => '请选择']) ?></td>

    <td >销售总额：金额：<span class="totalMoney">0</span></td>
    <td colspan="2">说明：<?= Html::activeTextInput($model, 'remark', ['style' => 'width:50%',"onkeyup" => "javascript:validateValue(this)"]) ?></td>
</tr>
<tr><td colspan="4">选择商品</td></tr>
<tr class="showSelProduct">
    <td colspan="4">
        <table class="showGoodsList" style="width: 100%" border="1">
            <tr>
                <th width="10%">物料名称</th>
                <th width="15%">物料条形码</th>
                <th width="15%">物料分类</th>
                <th width="10%">销售单价</th>
                <th width="10%">销售定价</th>
                <th width="10%">销售数量</th>
                <th width="10%">销售总价</th>
                <th width="5%">规格</th>
                <th width="5%">单位</th>
                <th width="10%">操作</th>
            </tr>
            <?php for ($i = 1; $i < 6; $i++) { ?>
            <tr class="showGoodsTr tr_<?= $i ?>" i="0">
                <td><?= Html::input("text", "goodsName[]", "",["class" => "selGoodsName", "readonly" => "readonly", "i" => $i, 'placeholder' => "点击选择商品"]);?></td>
                <td><span class="goodsBarcode"></span></td>
                <td><span class="goodsCate"></span></td>
                <td><span class="goodsPrice"></span></td>
                <td><?= Html::textInput("goodsPrice[]", "",["class" => "reGoodsPrice verifyFloat",'style' => 'width: 80%;', 'i' => $i]);?></td>
                <td><?= Html::textInput("goodsNum[]", "",["class" => "selGoodsNum", "onblur"=>"javascript:ckprto(".$i.")", "onkeyup"=>"value=value.replace(/\D/g,'')"]);?></td>
                <td><span class="goodsTotalMoney"></span></td>
                <td><span class="goodsSpec"></span></td>
                <td><span class="goodsUnit"></span></td>
                <td>
                    <?= Html::hiddenInput("goodsId[]", "0",["class" => "selGoodsId"])?>
                        <?= Html::hiddenInput("warehouseId[]", "0",["class" => "warehouseId"])?>
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