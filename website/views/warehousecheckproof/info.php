<?php
use libs\common\Flow;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\CommonRemark;

$this->title = '业务操作-仓库盘点计划校对';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_WAREHOUSE_PROOF, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_CHECK_WAREHOUSE_PROOF);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>仓库盘点计划校对详情</caption>
            <tr>
                <td style="width: 45%;">校对表单名：<?= $model->name ?></td>
                <td style="width: 30%;">状态：<?= Flow::showStatusAll($model->status) ?></td>
                <td style="width: 25%;">流程名：<?= FlowConfig::getNameById($model->config_id); ?></td>
            </tr>
            <tr>
                <td>校对表单号：<?= $model->sn ?></td>
                <td>盘点时间：<?= $model->create_time ?></td>
                <td>盘点人：<?= Admin::getNameById($model->create_admin_id) ?></td>
            </tr>
            <tr>
                <td>盘点仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
                <td>盘点前总金额：<?= $model->total_buying_amount ?></td>
                <td>盘点后总金额：<?= $model->check_buying_amount ?></td>
            </tr>
            <tr>
                <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
                <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
                <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            </tr>
            <tr>
                <td>下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
                <td colspan="2">下一步操作：<?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="3">审核说明：<?= isset($remarkList[CommonRemark::TYPE_VERIFY]) ? ($remarkList[CommonRemark::TYPE_VERIFY] ? $remarkList[CommonRemark::TYPE_VERIFY] : "无") : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="3">批准说明：<?= isset($remarkList[CommonRemark::TYPE_APPROVAL]) ? ($remarkList[CommonRemark::TYPE_APPROVAL] ? $remarkList[CommonRemark::TYPE_APPROVAL] : "无") : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="3">执行说明：<?= isset($remarkList[CommonRemark::TYPE_OPERATOR]) ? ($remarkList[CommonRemark::TYPE_OPERATOR] ? $remarkList[CommonRemark::TYPE_OPERATOR] : "无") : "无"  ?></td>
            </tr>
            <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
            <tr>
                <td colspan="3">驳回理由：<?= $model->failCause ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <table id="table-list" class="table-list taleft">
        <caption>盘点的部门列表</caption>
        <?php foreach($data as $val){ ?>
            <tr>
                <td colspan="3"><h1 style="color: red;"><?= $val->data_name ?></h1></td>
            </tr>
            <?php if(isset($amount[$val->data_id])){ ?>
                <tr>
                    <td><h2>盘点资金</h2></td>
                    <td>部门余额：<?= $amount[$val->data_id]->amount;?></td>
                    <td>真实余额：<?= $amount[$val->data_id]->check_amount;?></td>
                </tr>
            <?php } ?>
            <tr><td colspan="3"><h2>盘点物料</h2></td></tr>
            <tr>
                <td colspan="3" style="padding: 0px;">
                    <?php if(isset($product[$val->data_id]) && count($product[$val->data_id]) > 0){?>
                    <table id="table-list" class="table-list tacenter">
                        <tr>
                            <th width="10%">所属仓库</th>
                            <th width="10%">物料名称</th>
                            <th width="10%">批次号</th>
                            <th width="10%">供应商</th>
                            <th width="5%">供应商<br>物料ID</th>
                            <th width="10%">条形码ID</th>
                            <th width="5%">规格</th>
                            <th width="5%">单位</th>
                            <th width="5%">采购<br>价格</th>
                            <th width="5%">库存<br>数量</th>
                            <th width="10%">盘点<br>数量</th>
                        </tr>
                        <?php foreach($product[$val->data_id] as $infoVal){ ?>
                            <tr>
                                <td><?= Warehouse::getNameById($infoVal["warehouse_id"]) ?></td>
                                <td><?= $infoVal["name"] ?></td>
                                <td><?= $infoVal["batches"] ?></td>
                                <td><?= Supplier::getNameById($infoVal["supplier_id"]) ?></td>
                                <td><?= $infoVal["supplier_product_id"] ?></td>
                                <td><?= $infoVal["barcode"] ?></td>
                                <td><?= $infoVal["spec"] ?></td>
                                <td><?= $infoVal["unit"] ?></td>
                                <td><?= number_format($infoVal["purchase_price"], 2) ?></td>
                                <td><?= $infoVal["product_number"] ?></td>
                                <td><?= $infoVal["buying_number"] ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php } else {echo  "&nbsp;&nbsp;&nbsp;&nbsp;该部门没有符合条件的物料!";}?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <div class="buttons">
        <?= isset($nextStep["nextStepA"]) ? $nextStep["nextStepA"] : "" ?>
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>

<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/commonOperation') ?>
<?= $this->context->renderPartial('/jquery/authOperation') ?>
<?= $this->context->renderPartial('/jquery/commonReject');?>