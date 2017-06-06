<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Department;
use common\models\Product;
use libs\common\Flow;
$this->title = '业务操作-物料盘点详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<?php $form = ActiveForm::begin(['id' => 'subCheckForm', 'action' => Url::to(['invoicing/checkinfo',"id"=>$model->id])]); ?>
<div class="main-container">
    <div class="filter">
        <span>盘点计划名称：</span>
        <span style="margin-right: 50px;"><?= $model->name ?></span>
        <?php if($model->check_type == Product::TYPE_PRODUCT) {?>
        <span>供应商：</span>
        <span style="margin-right: 50px;"><?= $model->supplier_id ? Supplier::getNameById($model->supplier_id) : "全部" ?></span>
        <span>当前结存金额：</span>
        <span style="margin-right: 50px;"><?= $balance ? number_format($balance->balance, 2) : "0.00" ?></span><br>
        <?php } ?>
        <span>盘点计划编号：</span>
        <span style="margin-right: 50px;"><?= $model->check_sn ?></span>
        <span>盘点员：</span>
        <span style="margin-right: 50px;"><?= Admin::getNameById($model->check_admin_id) ?></span>
        <span>盘点时间：</span>
        <span style="margin-right: 50px;"><?= $model->check_time ?></span>
        <?php if($model->check_type == Product::TYPE_PRODUCT) {?>
        <span>盘点仓库：</span>
        <span style="margin-right: 50px;"><?= Warehouse::getNameById($model->warehouse_id) ?></span>
        <?php } else { ?>
        <span>盘点部门：</span>
        <span style="margin-right: 50px;"><?= Department::getNameById($model->department_id) ?></span>
        <?php } ?>
        <span>盘点类型：</span>
        <span style="margin-right: 50px;"><?= Product::showTypeName($model->check_type) ?></span>
        <span>盘点状态：</span>
        <span style="margin-right: 50px;"><?= $model->showStatus() ?></span>
    </div>
    <table id="table-list" class="table-list">
        <?php if($model->check_type == Product::TYPE_PRODUCT) {?>
            <tr>
                <th width="10%">物料名称</th>
                <th width="5%">物料ID</th>
                <th width="10%">供应商</th>
                <th width="5%">供应商物料ID</th>
                <th width="5%">物料类型</th>
                <th width="10%">条形码ID</th>
                <th width="7%">规格</th>
                <th width="7%">单位</th>
                <th width="8%">销售价格</th>
                <th width="8%">库存数量</th>
                <th width="8%">盘点数量</th>
                <th width="8%">盘点总价</th>
            </tr>
            <?php  $productAmount = 0;
                foreach($info as $data){ 
                    $productAmount += $data->sale_price * $data->stock;?>
                <tr class="tr_<?php echo $data->id;?>">
                    <td><?= $data->product_name ?></td>
                    <td><?= $data->product_id ?></td>
                    <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                    <td><?= $data->supplier_product_id ?></td>
                    <td><?= Product::showTypeName($data->material_type) ?></td>
                    <td><?= $data->barcode ?></td>
                    <td><?= $data->spec ?></td>
                    <td><?= $data->unit ?></td>
                    <td class="goodsPrice"><?= number_format($data->sale_price, 2) ?></td>
                    <td><?= $data->stock ?></td>
                    <?php if($model->status == Department::STATUS_NO){ ?>
                    <td><?= Html::input("text", "check_num[".$data->id."]", "",["class" => "selCheck","onkeyup"=>"value=value.replace(/\D/g,'')","onbeforepaste"=>"clipboardData.setData('text',clipboardData.getData('text').replace(/\D/g/g,''))","onblur"=>"javascript:ckprto(".$data->id.")"]);?></td>
                    <td class="showCheckAmout">0.00</td>
                    <?php } else { ?>
                    <td><?= $data->check_num ?></td>
                    <td><?= number_format($data->sale_price * $data->check_num, 2) ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
                <tr>
                    <td colspan="4">制表人：<?= Yii::$app->user->identity->username;?></td>
                    <td colspan="4">创建时间：<?= date("Y-m-d H:i:s")?></td>
                    <td colspan="2">物料总价：<?php echo $productAmount;?></td>
                    <td colspan="2">盘点总价：<span class="showTotalCheckAmount">0.00</span></td>
                </tr>
        <?php } else { ?>
            <tr>
                <th width="10%">部门余额</th>
                <th width="10%">盘点余额</th>
            </tr>
            <tr>
                <td><?= number_format($info->warehouse_total_amount, 2) ?></td>
                <td><?= Html::input("text", "check_amount", "",["class" => "selCheck","onkeyup"=>"value=value.replace(/[^\-\d{1,}\.\d{1,}|\d{1,}]/g,'')","onbeforepaste"=>"clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,''))"]);?></td>
            </tr>
        <?php } ?>
    </table>
    <div class="buttons">
        <?php if($model->status == Department::STATUS_NO){ ?>
        <a class="button blue-button subCheck" href="javascript:void(0)">盘点</a>
        <?php } ?>
        <a class="button blue-button" href="javascript:history.back(-1);">返回</a>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact('message')) ?>
<script type="text/javascript">
    function ckprto(id){
        var sum = $(".tr_"+id+" .selCheck").val();
        $(".tr_"+id+" .showCheckAmout").text("");
        totalMoney();
        if(!sum) {
            return false;
        }
        var price = $(".tr_"+id+" .goodsPrice").text();
        var total = accMul(sum , price);
        $(".tr_"+id+" .showCheckAmout").text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".showCheckAmout").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".showTotalCheckAmount").text(parseFloat(ztotal).toFixed(2));
    }  
    function totalMoney() {
        var ztotal = 0;
        $(".showCheckAmout").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".showTotalCheckAmount").text(parseFloat(ztotal).toFixed(2));
    }
</script>
<?php 
$checkType = $model->check_type == Product::TYPE_PRODUCT ? 1 : 0;
$js = <<<JS
    $(".subCheck").click(function(){
        var isSub = true;
        $(".selCheck").each(function(index){
            if(!$.isNumeric($(this).val())) {
                isSub = false;
                return false;
            }
        });
        if(!isSub) {
            alert("请填写盘点" + ({$checkType} ? "数量" : "金额"));
            return false;
        }
        $("#subCheckForm").submit();
        return true;
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'checkInfo');
?>