<?php
use libs\common\Flow;
use libs\Utils;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
use common\models\ProductCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = '业务操作-总盘点计划校对';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>总盘点计划校对</caption>
            <tr id="quick-form">
                <td width="22%">计划名称：<?= $model->name; ?></td>
                <td width="18%">计划状态：<?= Flow::showStatusAll($model->status) ?></td>
                <td width="22%">预计盘点时间：<?= $model->check_time ?></td>
                <td width="20%">盘点供应商：<?= $model->supplier_id ? Supplier::getNameById($model->supplier_id) : "全部" ?></td>
                <td width="18%">盘点资金：<?= $model->is_check_amount ? "是" : "否" ?></td>
            </tr>
            <tr>
                <td>盘点编号：<?= $model->sn ?></td>
                <td>下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
                <td>结束盘点时间：<?= $model->end_time ?></td>
                <td>盘点商品名称：<?= $model->product_name ? $model->product_name : "全部" ?></td>
                <td>盘点商品分类：<?= $model->product_cate_id ? ProductCategory::getNameById($model->product_cate_id) : "全部" ?></td>
            </tr>
            <tr>
                <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
                <td>制表人：<?= Admin::getNameById($model->create_admin_id) ?></td>
                <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
                <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
                <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            </tr>
            <tr>
                <td colspan="5">盘点计划说明：<?= $model->remark ? $model->remark : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="2">校对表单名：<?= Html::activeTextInput($checkFlow, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
                <td colspan="3">校对表单号：<?= Html::activeTextInput($checkFlow, 'sn', ['style' => 'width:60%', 'value'=>Utils::generateSn(Flow::TYPE_CHECK_PLANNING_PROOF),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            </tr>
            <tr>
                <td colspan="5">数据刷新：<span class="isShowCheckData">待验证</span>
                    <a class="button blue-button checkFlowSuccess" href="javascript:;" style="margin-left: 20px;color: #FFFFFF">验证数据刷新</a></td>
            </tr>
        </table>
    </div>
    <table id="table-list" class="table-list taleft">
        <caption>盘点的部门列表</caption>
        <?php foreach($data as $val){ ?>
            <tr>
                <td colspan="4"><h1 style="color: red;"><?= $val->data_name ?></h1></td>
            </tr>
            <?php if($model->is_check_amount){ ?>
            <tr class="amountTr_<?= $val->id;?>">
                <td style="width:20%;"><h2>盘点资金</h2></td>
                <td style="width:30%;">部门余额：<span class="showAmount"><?= isset($checkInfo["amountList"][$val->data_id]) ? $checkInfo["amountList"][$val->data_id] : "0"?></span></td>
                <td style="width:30%;">真实余额：
                    <input type="text" name="checkAmount[<?= $val->data_id;?>]" class='checkAmount verifyMinus' style="width: 60%;" i="<?= $val->id;?>"></td>
                <td style="width:20%;">结存差额：<span class="showAmountDiff"><?= isset($checkInfo["amountList"][$val->data_id]) ? $checkInfo["amountList"][$val->data_id] : "0"?></span></td>
            </tr>
            <?php } ?>
            <tr><td colspan="4"><h2>盘点物料</h2></td></tr>
            <tr>
                <td colspan="4" style="padding: 0px;">
                    <?php if(isset($checkInfo["productList"][$val->data_id]) && count($checkInfo["productList"][$val->data_id]) > 0){ //print_r($checkInfo["productList"][$val->data_id]);exit;?>
                    <table id="table-list" class="table-list tacenter">
                        <tr>
                            <th width="3%">库存ID</th>
                            <th width="10%">所属仓库</th>
                            <th width="10%">物料名称</th>
                            <th width="8%">批次号</th>
                            <th width="8%">供应商</th>
                            <th width="5%">条形码</th>
                            <th width="5%">规格</th>
                            <th width="5%">单位</th>
                            <th width="5%">采购<br>价格</th>
                            <th width="5%">库存<br>数量</th>
                            <th width="10%">盘点<br>数量</th>
                            <th width="5%">数据<br>差额</th>
                            <th width="8%">总价<br>差额</th>
                        </tr>
                        <?php foreach($checkInfo["productList"][$val->data_id] as $pstockId => $infoVal){ ?>
                            <tr  class="productTr_<?= $pstockId;?>">
                                <td><?= $pstockId ?></td>
                                <td><?= $infoVal["warehouse_name"] ?></td>
                                <td><?= $infoVal["name"] ?></td>
                                <td><?= $infoVal["batches"] ?></td>
                                <td><?= Supplier::getNameById($infoVal["supplier_id"]) ?></td>
                                <td><?= $infoVal["barcode"] ?></td>
                                <td><?= $infoVal["spec"] ?></td>
                                <td><?= $infoVal["unit"] ?></td>
                                <td><span class="showPrice"><?= number_format($infoVal["purchase_price"], 2) ?></span></td>
                                <td><span class="showStock"><?= $infoVal["number"] ?></span></td>
                                <td><input type="text" name="checkStock[<?= $val->data_id;?>][<?= $pstockId?>]" value="<?= $infoVal["check_num"]?>" onkeyup="value=value.replace(/\D/g,'')" class="checkStock" i="<?=$pstockId?>" maxlength="8"></td>
                                <td><span class="showStockDiff"><?= $infoVal["check_num"] ? $infoVal["check_num"] - $infoVal["number"] : 0?></span></td>
                                <td><span class="showTotalDiff"><?= $infoVal["check_num"] ? ($infoVal["check_num"] - $infoVal["number"]) * $infoVal["purchase_price"] : 0?></span></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php } else {echo  "&nbsp;&nbsp;&nbsp;&nbsp;该部门没有符合条件的物料!";}?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <div class="buttons">
        <input type="hidden" id="isCheckData" value="0" />
        <a class="button blue-button confirmChcek" href="javascript:void(0)" save-data="<?= Url::to(['checkplanning/proof', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
        <a class="button blue-button" href="<?= Url::to(['checkplanning/downproof', 'id' => $model->id]) ?>" >导出</a>
        <a class="button blue-button" import-excel="<?= Url::to(['checkplanning/importproof', 'id' => $model->id]) ?>"  href="javascript:void(0)">导入</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php 
    $checkFlowSuccess = Url::to(["ajax/checkflowsuccess"]);
$js = <<<js
    $(".checkAmount").blur(function(){
        var departmentId = $(this).attr("i");
        var newAmount = $(this).val();
        var oldAmount = $(".amountTr_"+departmentId+" .showAmount").text();
        var amountDiff = accMul(newAmount , oldAmount);
        $(".amountTr_"+departmentId+" .showAmountDiff").text(amountDiff);
    });
    $(".checkStock").blur(function(){
        var pstockId = $(this).attr("i");
        var checkStock = $(this).val();
        $(".productTr_"+pstockId+" .showStockDiff").text("");
        $(".productTr_"+pstockId+" .showTotalDiff").text("");
        if(!checkStock) {
            return false;
        }
        var salePrice = $(".productTr_"+pstockId+" .showPrice").text();
        var stock = $(".productTr_"+pstockId+" .showStock").text();
        var stockDiff = accSub(checkStock, stock);
        var totalDiff = accMul(stockDiff , salePrice);
        $(".productTr_"+pstockId+" .showStockDiff").text(stockDiff);
        $(".productTr_"+pstockId+" .showTotalDiff").text(totalDiff);
    });
    $(".checkFlowSuccess").click(function(){
        $.get("{$checkFlowSuccess}",{"dataId":{$model->id}}, function(result) {
            alert(result.message);
            if(result.state) {
                $(".isShowCheckData").text("通过");
                $("#isCheckData").val("1");
                if(typeof($(".confirmChcek").attr("save-data_back")) != "undefined"){
                    $(".confirmChcek").attr("save-data", $(".confirmChcek").attr("save-data_back"));
                    $(".confirmChcek").removeAttr("save-data_back");
                }
            } else {
                $(".isShowCheckData").text("不通过");
                $("#isCheckData").val("0");
                $(".confirmChcek").attr("save-data_back", $(".confirmChcek").attr("save-data"));
                $(".confirmChcek").removeAttr("save-data");
            }
        }, "json");
    });
    $(".confirmChcek").click(function(){
        var isCheckData = $("#isCheckData").val();
        if(isCheckData == 0) {
            alert("请先进行验证数据刷新操作！");
            return false;
        }
        var isCheckAmount = true;
        $(".checkAmount").each(function(index){
            if($(this).val() == "") {
                isCheckAmount = false;
                return false;
            }
        });
        if(!isCheckAmount){
            alert("部门真实余额不能为空！");
            return false;
        }
        var isCheckStock = true;
        $(".checkStock").each(function(index){
            if($(this).val() == "") {
                isCheckStock = false;
                return false;
            }
        });
        if(!isCheckStock){
            alert("物料的盘点库存不能为空！");
            return false;
        }
        var isDiff = true;
        $(".showStockDiff").each(function(index){
            if($(this).text() != 0) {
                isDiff = false;
            }
        });
        if(!isDiff && !confirm("您确定当前库存的差异")) {
            return false;
        }
        return false;
    });     
js;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'checkProof');
?>
<script type="text/javascript">
    function ckprto(id){
        var sum = $(".tr_"+id+" .selGoodsNum").val();
        $(".tr_"+id+" .goodsTotalMoney").text("");
        $(".tr_"+id+" .goodsSaleTotal").text("");
        $(".tr_"+id+" .numBlan").text("");
        $(".tr_"+id+" .amountBlan").text("");
        if(!sum) {
            return false;
        }
        var stock = $(".tr_"+id+" .goodsStock").text();
        var price = $(".tr_"+id+" .goodsPrice").text();
        var salePrice = $(".tr_"+id+" .salePrice").text();
        var total = accMul(stock, price);
        var saleTotal = accMul(stock, salePrice);
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
</script>