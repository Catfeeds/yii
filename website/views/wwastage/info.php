<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\WarehouseWastage;
use libs\common\Flow;
use common\models\ProductCategory;
use common\models\CommonRemark;
$this->title = '业务操作-物料耗损详情';
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_WASTAGE, $model, 'operator_url');
$remarkList = CommonRemark::getRemarkList($model->id, Flow::TYPE_WASTAGE);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <table id="table-list" class="table-list taleft">
            <caption>物料耗损详情</caption>
            <tr id="quick-form">
                <td width="30%">耗损名称：<?= $model->name; ?></td>
                <td width="18%">进展状态：<?= Flow::showStatusAll($model->status) ?></td>
                <td width="18%">耗损仓库：<?= Warehouse::getNameById($model->warehouse_id) ?></td>
                <td width="18%">耗损总价：<?= number_format($model->total_amount, 2) ?></td>
                <td width="18%">是否扣仓：<?= Flow::showBuckleName($model->is_buckle) ?></td>
            </tr>
            <tr>
                <td>流程名称：<?= FlowConfig::getNameById($model->config_id) ?></td>
                <td>制表人：<?= Admin::getNameById($model->create_admin_id) ?></td>
                <td>审核人：<?= Admin::getNameById($model->verify_admin_id) ?></td>
                <td>批准人：<?= Admin::getNameById($model->approval_admin_id) ?></td>
                <td>执行人：<?= Admin::getNameById($model->operation_admin_id) ?></td>
            </tr>
            <tr>
                <td colspan="2">耗损编号：<?= $model->sn ?></td>
                <td colspan="3">下一步操作：<?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无"  ?></td>
            </tr>
            <tr>
                <td colspan="5">耗损说明：<?= $model->remark ? $model->remark : "无"  ?></td>
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
    <table id="table-list" class="table-list">
        <tr>
            <th width="12%">批次号</th>
            <th width="12%">物料名称</th>
            <th width="5%">物料ID</th>
            <th width="10%">物料分类</th>
            <th width="10%">供应商</th>
            <th width="5%">供应商<br>物料ID</th>
            <th width="10%">条形码</th>
            <th width="5%">规格</th>
            <th width="5%">单位</th>
            <th width="8%">采购<br>价格</th>
            <th width="5%">耗损<br>数量</th>
            <th width="10%">耗损总价</th>
        </tr>
        <?php foreach($info as $data){ ?>
            <tr>
                <td><?= $data->batches ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->product_id ?></td>
                <td><?= ProductCategory::getNameById($data->material_type) ?></td>
                <td><?= Supplier::getNameById($data->supplier_id) ?></td>
                <td><?= $data->supplier_product_id ?></td>
                <td><?= $data->num ?></td>
                <td><?= $data->spec ?></td>
                <td><?= $data->unit ?></td>
                <td><?= number_format($data->purchase_price, 2) ?></td>
                <td><?= $data->buying_number ?></td>
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