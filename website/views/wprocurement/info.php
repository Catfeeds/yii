<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
use common\models\Product;
use common\models\CommonRemark;
use common\models\ProductCategory;
$this->title = '业务操作-物料采购计划下定详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_ORDER, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_ORDER);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>物料采购计划下定详情</caption>
        <tr id="quick-form">
            <td style="width: 25%">计划名称：<?= $model->name; ?></td>
            <td style="width: 25%">进展状态：<?= Flow::showStatusAll($model->status); ?></td>
            <td style="width: 25%">仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
            <td style="width: 25%">计划下单时间：<?= $model->planning_date ?></td>
        </tr>
        <tr>
            <td>计划编号：<?= $model->sn ?></td>
            <td>供应商：<?= Supplier::getNameById($model->supplier_id) ?></td>
            <td colspan="2">采购总价：<?= number_format($model->total_amount, 2) ?></td>
        </tr>
        <tr>
            <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
            <td>付款时间：<?= $model->payment_term ?></td>
            <td>支付方式：<?= $model->showPayment() ?></td>
            <td>定金：<?= number_format($model->deposit, 2) ?></td>
        </tr>
        <tr>
            <td >制定人：<?= Admin::getNameById($model->create_admin_id) ?></td>
            <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
            <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
            <td >执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
        </tr>
        <tr>
            <td >下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
            <td colspan="3">
                <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
                驳回理由：<?= $model->failCause ?>
                <?php } ?>
            </td>
        </tr>
        <tr><td colspan="4">审核说明：<?= isset($remarkList[CommonRemark::TYPE_VERIFY]) ? ($remarkList[CommonRemark::TYPE_VERIFY] ? $remarkList[CommonRemark::TYPE_VERIFY] : "无") : "无"  ?></td></tr>
        <tr><td colspan="4">批准说明：<?= isset($remarkList[CommonRemark::TYPE_APPROVAL]) ? ($remarkList[CommonRemark::TYPE_APPROVAL] ? $remarkList[CommonRemark::TYPE_APPROVAL] : "无") : "无"  ?></td></tr>
        <tr><td colspan="4">执行说明：<?= isset($remarkList[CommonRemark::TYPE_OPERATOR]) ? ($remarkList[CommonRemark::TYPE_OPERATOR] ? $remarkList[CommonRemark::TYPE_OPERATOR] : "无") : "无"  ?></td></tr>
    </table>
    <table id="table-list" class="table-list">
        <tr>
            <th width="10%">物料名称</th>
            <th width="10%">物料ID</th>
            <th width="10%">供应商</th>
            <th width="5%">供应商<br>物料ID</th>
            <th width="10%">物料分类</th>
            <th width="10%">条形码ID</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="10%">采购价格</th>
            <th width="10%">采购数量</th>
            <th width="10%">采购总价</th>
        </tr>
        <?php foreach($info as $data){ ?>
            <tr>
                <td><?= $data->name ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><?= $data->product_number ?></td>
                <td><?= number_format($data->total_amount, 2) ?></td>
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