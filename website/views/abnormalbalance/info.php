<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
use common\models\CommonRemark;
$this->title = '业务操作-例外资金流水详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_ABNORMAL_FUND, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_ABNORMAL_FUND);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>例外资金流水日志详情</caption>
        <tr id="quick-form">
            <td style="width: 30%;">流水名称：<?= $model->name; ?></td>
            <td style="width: 20%;">支出部门：<?= Department::getNameById($model->department_id) ?></td>
            <td style="width: 25%;">变动类型：<?= $model->showMod(); ?></td>
            <td style="width: 25%;">变动金额：<?= number_format($model->current_balance) ?></td>
        </tr>
        <tr>
            <td>流程单号：<?= $model->sn ?></td>
            <td>收入部门：<?= Department::getNameById($model->income_department_id) ?></td>
            <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
            <td>进展状态：<?= Flow::showStatusAll($model->status); ?></td>
        </tr>
        <tr>
            <td>制定人：<?= Admin::getNameById($model->create_admin_id) ?></td>
            <td>制定时间：<?= $model->create_time ?></td>
            <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
            <td>审核时间：<?= $model->verify_time ?></td>
        </tr>
        <tr>
            <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
            <td>批准时间：<?= $model->approval_time ?></td>
            <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            <td>执行时间：<?= $model->operation_time ?></td>
        </tr>
        <tr>
            <td colspan="2">下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
            <td colspan="2">下一步操作人：<?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无"  ?></td>
        </tr>
        <tr><td colspan="4">操作内容：<?= $model->content ?></td></tr>
        <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
            <tr><td colspan="4">驳回理由：<?= $model->failCause ?></td></tr>
        <?php } ?>
        <tr><td colspan="4">审核说明：<?= isset($remarkList[CommonRemark::TYPE_VERIFY]) ? ($remarkList[CommonRemark::TYPE_VERIFY] ? $remarkList[CommonRemark::TYPE_VERIFY] : "无") : "无"  ?></td></tr>
        <tr><td colspan="4">操作内容：<?= isset($remarkList[CommonRemark::TYPE_APPROVAL]) ? ($remarkList[CommonRemark::TYPE_APPROVAL] ? $remarkList[CommonRemark::TYPE_APPROVAL] : "无") : "无"  ?></td></tr>
        <tr><td colspan="4">操作内容：<?= isset($remarkList[CommonRemark::TYPE_OPERATOR]) ? ($remarkList[CommonRemark::TYPE_OPERATOR] ? $remarkList[CommonRemark::TYPE_OPERATOR] : "无") : "无"  ?></td></tr>
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
