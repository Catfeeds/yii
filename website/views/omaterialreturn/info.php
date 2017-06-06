<?php
use yii\helpers\Url;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\Admin;
use common\models\FlowConfig;
use common\models\ProductCategory;
use libs\common\Flow;
use common\models\CommonRemark;
$this->title = '业务操作-退货收款详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_ORDER_MATERIAL, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_ORDER_MATERIAL);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>退货收款详情</caption>
        <tr id="quick-form">
            <td style="width: 30%">表单名称：<?= $model->name ?></td>
            <td style="width: 20%">进展状态：<?= Flow::showStatusAll($model->status) ?></td>
            <td style="width: 20%">退货仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
            <td style="width: 30%">供应商：<?= Supplier::getNameById($model->supplier_id) ?></td>
        </tr>
        <tr>
            <td>退货编号：<?= $model->sn ?></td>
            <td>退货时间：<?= $model->planning_date?></td>
            <td>退货总价：<?= number_format($model->total_amount, 2) ?></td>
            <td>预计退款时间：<?= $model->payment_term ?></td>
        </tr>
        <tr>
            <td>退款单号：<?= $model->order_sn ?></td>
            <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
            <td colspan="2">下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
        </tr>
        <tr>
            <td>创建人：<?= Admin::getNameById($model->create_admin_id) ?></td>
            <td>创建时间：<?= $model->create_time ?></td>
            <td>审核员：<?= Admin::getNameById($model->verify_admin_id) ?></td>
            <td>审核时间：<?= $model->verify_time ? $model->verify_time : "无"; ?></td>
        </tr>
        <tr>
            <td>批准员：<?= Admin::getNameById($model->approval_admin_id) ?></td>
            <td>批准时间：<?= $model->approval_time ? $model->approval_time : "无" ?></td>
            <td>执行员：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            <td>执行时间：<?= $model->operation_time ? $model->operation_time : "无" ?></td>
        </tr>
        <tr>
            <td>退款状态：<?= $model->showPayState() ?></td>
            <td colspan="3">实际退款时间：<?= $model->pay_all_time ?></td>
        </tr>
        <tr id="quick-form"><td colspan="4">退货原因：<?= $model->remark ?></td></tr>
        <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
            <tr id="quick-form"><td colspan="4">驳回理由：<?= $model->failCause ?></td></tr>
        <?php } ?>
        <tr id="quick-form">
            <td colspan="4">审核说明：<?= isset($remarkList[CommonRemark::TYPE_VERIFY]) ? ($remarkList[CommonRemark::TYPE_VERIFY] ? $remarkList[CommonRemark::TYPE_VERIFY] : "无") : "无"  ?></td>
        </tr>
        <tr id="quick-form">
            <td colspan="4">批准说明：<?= isset($remarkList[CommonRemark::TYPE_APPROVAL]) ? ($remarkList[CommonRemark::TYPE_APPROVAL] ? $remarkList[CommonRemark::TYPE_APPROVAL] : "无") : "无"  ?></td>
        </tr>
        <tr id="quick-form">
            <td colspan="4">执行说明：<?= isset($remarkList[CommonRemark::TYPE_OPERATOR]) ? ($remarkList[CommonRemark::TYPE_OPERATOR] ? $remarkList[CommonRemark::TYPE_OPERATOR] : "无") : "无"  ?></td>
        </tr>
        <tr class="showSelProduct">
            <td colspan="4">
                <table id="table-list" class="table-list">
                    <tr>
                        <th width="10%">物料名称</th>
                        <th width="10%">物料ID</th>
                        <th width="10%">物料类型</th>
                        <th width="10%">出品编号</th>
                        <th width="5%">规格</th>
                        <th width="5%">单位</th>
                        <th width="8%">采购价格</th>
                        <th width="8%">退货数量</th>
                        <th width="10%">退货总价</th>
                    </tr>
                    <?php foreach($info as $data){ ?>
                        <tr>
                            <td><?= $data->name ?></td>
                            <td><?= $data->product_id ?></td>
                            <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                            <td><?= $data->num ?></td>
                            <td><?= $data->spec ?></td>
                            <td><?= $data->unit ?></td>
                            <td><?= number_format($data->price, 2) ?></td>
                            <td><?= $data->product_number ?></td>
                            <td><?= number_format($data->total_amount, 2) ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    <div class="buttons">
        <?= isset($nextStep["nextStepA"]) ? $nextStep["nextStepA"] : "" ?>
        <?php $payUrl = $model->showPayUrl(); echo isset($payUrl["payUrl"]) ? $payUrl["payUrl"] : "" ?>
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/commonOperation') ?>
<?= $this->context->renderPartial('/jquery/authOperation') ?>
<?= $this->context->renderPartial('/jquery/commonReject');?>