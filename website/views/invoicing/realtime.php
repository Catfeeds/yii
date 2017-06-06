<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Department;
use common\models\ProductCategory;
use common\models\Admin;
use libs\common\Flow;
use libs\Utils;

$this->title = '销存管理-销存核实状态管理';
$departmentAll = Department::getSelectData(-1);
$department_id = Yii::$app->request->get('department_id');
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="invoicing/realtime" />
            <span>物料ID或名称</span>
            <input class="form-text verifySpecial" type="text" placeholder="" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/> 
            <?php //if(Admin::checkSupperFlowAdmin()){?>
            <span>部门</span>
            <?= Html::dropDownList('department_id', $department_id, $departmentAll, ['class' => 'form-select']) ?>
            <?php //} ?>
          <!--查询他们部门下面 仓库 下面的部门-->
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= $department_id ? $departmentAll[$department_id] : "无";?>-销存盘点申请</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">物料名</th>
            <th width="8%">批次号</th>
            <th width="3%">物料ID</th>
            <th width="5%">所属仓库</th>
            <th width="5%">所属部门</th>
            <th width="5%">物料类型</th>
            <th width="5%">条形码ID</th>
            <th width="3%">规格</th>
            <th width="3%">单位</th>
            <th width="3%">采购价格</th>
            <th width="3%">实际<br>销售定价</th>
            <th width="5%">实际<br>销售数量</th>
            <th width="5%" class="saleNum" style="display: none;">核实<br>销售数量</th>
        </tr>
        <?php $totalSaleAmount = $totalPaidAmount = $totalLastAmount =  0;
            if($listDatas) {
            foreach($listDatas as $key => $data){ ?>
            <?php if(!isset($saleAll[$data->invoicing_sale_id])){continue;}?>
            <?php $totalSaleAmount += $data->sale_price * $data->buying_number;?>
            <?php $totalPaidAmount += $saleAll[$data->invoicing_sale_id]['paid_amount'];?>
            <?php $totalLastAmount += $saleAll[$data->invoicing_sale_id]['last_invoic_amount'];?>
            <tr>
                <td><?= $key+1 ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->batches ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= Warehouse::getNameById($saleAll[$data->invoicing_sale_id]["warehouse_id"]); ?></td>
                <td><?= Department::getNameById($saleAll[$data->invoicing_sale_id]["department_id"]); ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><span class="salePrice_<?= $data->id ?>"><?= $data->sale_price ?></span></td>
                <td><?= $data->buying_number ?></td>
                <td class="saleNum" style="display: none;">
                    <input type="text" name="real[<?= $data->id ?>]" value="" onblur="javascript:ckprto(<?= $data->id ?>)" class="real_<?= $data->id ?>" onkeyup="value=value.replace(/\D/g,'')">
                    <input type="hidden" id="totalSale_<?= $data->id ?>" class="totalSale" value="0" />
                </td>
            </tr>
            <?php } } else { ?>
                <tr><td colspan="14">暂无符合条件的销存核实状态记录</td></tr>
            <?php } ?>
            <tr class="saleNum" style="display: none;">
                <td colspan="7"  style="text-align:left;margin-left: 3px;">
                    表单名：<input type="text" name="name" value="" class="verifySpecial"  onkeyup="javascript:validateValue(this)" style="width:60%;"/></td>
                <td colspan="7" style="text-align:left;margin-left: 3px;">
                    补偿金额：<?= Html::textInput("compensationAmount", "0", ["class" => "verifyFloat",  "onkeyup"=>"javascript:CheckInputIntFloat(this)", "style" => "width:60%;"]);?></td>
            </tr>
            <tr class="saleNum" style="display: none;">
                <td colspan="14"  style="text-align:left;margin-left: 3px;">
                    损益原因：<input type="text" name="profitLossCause" value="" class="verifySpecial"  onkeyup="javascript:validateValue(this)" width="60%;"/></td>
            </tr>
            <tr class="saleNum" style="display: none;">
                <td colspan="2">应销金额统计：<?php echo number_format($totalSaleAmount, 2); ?></td>
                <td colspan="3">实际销售金额统计：<span class="checkSaleAmount">0.00</span></td>
                <td colspan="3">上次结存余额：<span class="lastAmount"><?php echo number_format($totalLastAmount, 2);?></span></td>
                <td colspan="3">预计结存余额：<span class="checkLastAmount">0.00</span></td>
                <td colspan="3">上缴金额：<span class="checkPaidAmount">0.00</span></td>
            </tr>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <div class="buttons">
        <a class="button blue-button normal-button" href="<?= Url::to(['pstock/invoicingsale']) ?>">新增实时销存</a>
        <?php if($listDatas) { ?>
        <a class="button blue-button normal-button configRealtime" href="javascript:void(0)">销存盘点</a>
        <?php } ?>
        <a class="button blue-button normal-button" href="<?= Url::to(['invoicing/realtime']) ?>" >部门数据刷新</a>
        <a class="button blue-button checkRealtime" href="javascript:void(0)" save-data="<?= Url::to(['invoicing/checksale']) ?>" style="display: none;">确定销存</a>
        <a class="button blue-button checkRealtime returnNormal" href="javascript:void(0)" style="display: none;">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<script type="text/javascript">
    function ckprto(id){
        var sum = $(".real_"+id).val();
        $("#totalSale_"+id).text("");
        if(!sum) {
            return false;
        }
        var price = $(".salePrice_"+id).text();
        var total = accMul(sum , price);
        $("#totalSale_"+id).text(parseFloat(total).toFixed(2));
        var ztotal = 0;
        $(".totalSale").each(function(){
            ztotal = ztotal * 1 + $(this).text() * 1;
        });
        $(".checkSaleAmount").text(parseFloat(ztotal).toFixed(2));
        $(".checkPaidAmount").text(parseFloat(ztotal).toFixed(2));
        var lastAmount = $(".lastAmount").text();
        var totalLast = lastAmount * 1 + ztotal * 1;
        $(".checkLastAmount").text(parseFloat(totalLast).toFixed(2));
    }
</script>
<?php
$js = <<<JS
    $(function(){
        $(".configRealtime").click(function(){
            $(".saleNum").show();
            $(".buttons .blue-button").hide();
            $(".checkRealtime").show();
        });
        $(".returnNormal").click(function(){
            $(".saleNum").hide();
            $(".buttons .blue-button").hide();
            $(".normal-button").show();
        });
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'realtime');
?>
