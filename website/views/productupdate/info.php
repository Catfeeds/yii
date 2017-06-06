<?php
use common\models\Supplier;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\ProductCategory;
use common\models\Product;
use libs\common\Flow;
use common\models\CommonRemark;
$this->title = '业务基础数据--供应商出品修改详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_PRODUCT_UPDATE, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_PRODUCT_UPDATE);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>供应商出品修改详情</caption>
            <tr id="quick-form">
                <td>物料名称：<?= $model->name; ?></td>
                <td>供应商：<?= Supplier::getNameById($model->supplier_id); ?></td>
                <td>供应商出品ID：<?= $model->supplier_product_id; ?></td>
                <td>出品编码：<?= $model->num; ?></td>
            </tr>
            <tr>
                <td>物料类别：<?= Product::showTypeName($model->material_type); ?></td>
                <td>物料分类：<?= ProductCategory::getNameById($model->product_category_id); ?></td>
                <td>条形码：<?= $model->barcode; ?></td>
                <td>进货参考价格：<?= number_format($model->purchase_price, 2); ?></td>
            </tr>
            <tr>
                <td>销售价格：<?= number_format($model->sale_price, 2); ?></td>
                <td>库存警告：<?= $model->inventory_warning >0 ? $model->inventory_warning : "不需要"; ?></td>
                <td>规格：<?= $model->spec; ?></td>
                <td>单位：<?= $model->unit; ?></td>
            </tr>
            <tr>
                <td colspan="2">是否需要批次号：<?= Product::showBatchesName($model->is_batches); ?></td>
                <td>创建人：<?= Admin::getNameById($model->create_admin_id); ?></td>
                <td>创建时间：<?= $model->create_time; ?></td>
            </tr>
            <tr>
                <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
                <td>审核人：<?= Admin::getNameById($model->verify_admin_id); ?></td>
                <td>批准人：<?= Admin::getNameById($model->approval_admin_id); ?></td>
                <td>完成人：<?= Admin::getNameById($model->operation_admin_id); ?></td>
            </tr>
            <tr>
                <td>流程状态：<?= Flow::showStatusAll($model->status) ?></td>
                <td>审核时间：<?= $model->verify_time ? $model->verify_time : "无"; ?></td>
                <td>批准时间：<?= $model->approval_time ? $model->approval_time : "无"; ?></td>
                <td>完成时间：<?= $model->operation_time ? $model->operation_time : "无"; ?></td>
            </tr>
            <tr>
                <td colspan="2">下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
                <td colspan="2">下一步操作人：<?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
            </tr>
            <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
            <tr>
                <td colspan="5">驳回理由：<?= $model->failCause ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
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
