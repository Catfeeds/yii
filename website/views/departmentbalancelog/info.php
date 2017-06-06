<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
$this->title = '业务操作-资金流水日志详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_SALE, $model);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <table id="table-list" class="table-list taleft">
        <caption>资金流水日志详情</caption>
        <tr id="quick-form">
            <td>流水名称：<?= $model->name; ?></td>
            <td>部门名称：<?= Department::getNameById($model->department_id) ?></td>
            <td>业务类型：<?= $model->showBusinessTypeName(); ?></td>
            <td colspan="2">业务ID：<?= $model->business_id ?></td>
        </tr>
        <tr>
            <td>变动类型：<?= $model->showMod(); ?></td>
            <td>部门金额：<?= number_format($model->balance, 2) ?></td>
            <td>变动金额：<?= number_format($model->current_balance) ?></td>
            <td colspan="2">操作内容：<?= $model->content ?></td>
        </tr>
        <tr>
            <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
            <td>制定人：<?= Admin::getNameById($model->create_admin_id) ?></td>
            <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
            <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
            <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
        </tr>
        <tr>
            <td>进展状态：<?= Flow::showStatusAll($model->status); ?></td>
            <td>下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
            <td colspan="3">
                <?php if(in_array($model->status, [Flow::STATUS_VERIFY_REJECT, Flow::STATUS_APPROVAL_REJECT, Flow::STATUS_FINISH_REJECT, Flow::STATUS_UNION_REJECT, Flow::STATUS_HANG_UP])){ ?>
                驳回理由：<?= $model->failCause ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    <div class="buttons">
        <?= isset($nextStep["nextStepA"]) ? $nextStep["nextStepA"] : "" ?>
        <a class="button blue-button" href="<?= Url::to([$model->showBusinessTypeCName().'/info', "id" => $model->business_id])?>" target="_blank">业务详情</a>
        <a class="button blue-button" href="<?= Url::to(['departmentbalancelog/index']) ?>">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
?>
