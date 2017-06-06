<?php
use yii\helpers\Url;
use libs\common\Flow;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
use common\models\ProductCategory;
use common\models\CommonRemark;
use common\models\Warehouse;
$this->title = '业务操作-部门盘点计划详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_WAREHOUSE, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_CHECK_WAREHOUSE);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>部门盘点计划详情</caption>
            <tr id="quick-form">
                <td width="22%">计划名称：<?= $model->name; ?></td>
                <td width="18%">计划状态：<?= $model->status == Flow::STATUS_FINISH && !$model->is_proof ? "待盘点" : Flow::showStatusAll($model->status) ?></td>
                <td width="22%">预计盘点时间：<?= $model->check_time ?></td>
                <td width="20%">盘点供应商：<?= $model->supplier_id ? Supplier::getNameById($model->supplier_id) : "全部" ?></td>
            </tr>
            <tr>
                <td>盘点编号：<?= $model->sn ?></td>
                <td>盘点商品名称：<?= $model->product_name ? $model->product_name : "全部" ?></td>
                <td>下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
                <td>盘点商品分类：<?= $model->product_cate_id ? ProductCategory::getNameById($model->product_cate_id) : "全部" ?></td>
            </tr>
            <tr>
                <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
                <td>盘点仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
                <td colspan="2">制表时间：<?= $model->create_time ?></td>
            </tr>
            <tr>
                <td>制表人：<?= Admin::getNameById($model->create_admin_id) ?></td>
                <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
                <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
                <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            </tr>
            <tr>
                <td colspan="5">盘点计划说明：<?= $model->remark ? $model->remark : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="5">审核说明：<?= isset($remarkList[CommonRemark::TYPE_VERIFY]) ? ($remarkList[CommonRemark::TYPE_VERIFY] ? $remarkList[CommonRemark::TYPE_VERIFY] : "无") : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="5">批准说明：<?= isset($remarkList[CommonRemark::TYPE_APPROVAL]) ? ($remarkList[CommonRemark::TYPE_APPROVAL] ? $remarkList[CommonRemark::TYPE_APPROVAL] : "无") : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="5">执行说明：<?= isset($remarkList[CommonRemark::TYPE_OPERATOR]) ? ($remarkList[CommonRemark::TYPE_OPERATOR] ? $remarkList[CommonRemark::TYPE_OPERATOR] : "无") : "无"  ?></td>
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
        <?php if($model->status == Flow::STATUS_FINISH && !$model->is_proof){ ?>
            <a class="button blue-button" href="<?= Url::to(['warehousecheckplanning/proof',"id" => $model->id]) ?>">盘点</a> 
         <?php } ?>
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>

<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/commonOperation') ?>
<?= $this->context->renderPartial('/jquery/authOperation') ?>
<?= $this->context->renderPartial('/jquery/commonReject');?>