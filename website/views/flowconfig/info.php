<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Role;
use common\models\Department;
use libs\common\Flow;
use common\models\FlowConfig;
use common\models\FlowCondition;
use common\models\Supplier;
use common\models\ProductCategory;
$this->title = '业务设置-业务流程详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>业务流程设置</caption>
        <tr>
            <th colspan="3">流程名称</th>
            <th colspan="3">流程类型</th>
        </tr>
        <tr id="quick-form">
            <td colspan="3"><?= $item->name ?></td>
            <td colspan="3"><?= Flow::showType($item->type) ?></td>
        </tr>
        <tr>
            <th width="15%">创建部门</th>
            <th width="18%">创建名称</th>
            <th width="18%">创建角色</th>
            <th width="15%">审核部门</th>
            <th width="18%">审核名称</th>
            <th width="18%">审核角色</th>
        </tr>
        <tr>
            <td><?= Department::getNameById($item->create_department_id) ?></td>
            <td><?= $item->create_name ?></td>
            <td><?= Role::getNameByRoleId($item->create_role_id) ?></td>
            <td><?= Department::getNameById($item->verify_department_id) ?></td>
            <td><?= $item->verify_name ?></td>
            <td><?= Role::getNameByRoleId($item->verify_role_id) ?></td>
        </tr>
        <tr>
            <th width="5%">批准部门</th>
            <th width="5%">批准名称</th>
            <th width="5%">批准角色</th>
            <th width="5%">执行部门</th>
            <th width="5%">执行名称</th>
            <th width="5%">执行角色</th>
        </tr>
        <tr>
            <td><?= Department::getNameById($item->approval_department_id) ?></td>
            <td><?= $item->approval_name ?></td>
            <td><?= Role::getNameByRoleId($item->approval_role_id) ?></td>
            <td><?= Department::getNameById($item->operation_department_id) ?></td>
            <td><?= $item->operation_name ?></td>
            <td><?= Role::getNameByRoleId($item->operation_role_id) ?></td>
        </tr>
    </table>
    <table id="table-list" class="table-list taleft">
        <caption>流程设置条件</caption>
        <?php $statusAll = FlowCondition::getStatusSelectData();?>
        <?php if($condition[FlowCondition::TYPE_PRICE]){ ?>
        <tr>
            <td>价格范围</td>
            <td>下限金额：<?php echo isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["lower_limit"] : "";?></td>
            <td>上限金额：<?php echo isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["upper_limit"] : ""?></td>
            <?php $status = isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["status"] : FlowCondition::STATUS_YES;?>
            <td>状态：<?php echo isset($statusAll[$status]) ? $statusAll[$status] : $status;?></td>
        </tr>
        <?php } ?>
        <?php if($condition[FlowCondition::TYPE_TIME]){ ?>
        <tr>
            <td>时间范围</td>
            <td>下限时间：<?php echo isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["lower_limit"] : "";?></td>
            <td>上限时间：<?php echo isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["upper_limit"] : ""?></td>
            <?php $status = isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["status"] : FlowCondition::STATUS_YES;?>
            <td>状态：<?php echo isset($statusAll[$status]) ? $statusAll[$status] : $status;?></td>
        </tr>
        <?php } ?>
        <?php if($condition[FlowCondition::TYPE_AREA]){ ?>
        <tr>
            <td>部门</td>
            <td colspan="2"><?php echo isset($info[FlowCondition::TYPE_AREA]) && $info[FlowCondition::TYPE_AREA]["lower_limit"] ? Department::getNameById($info[FlowCondition::TYPE_AREA]["lower_limit"]) : "全部";?></td>
            <?php $status = isset($info[FlowCondition::TYPE_AREA]) ? $info[FlowCondition::TYPE_AREA]["status"] : FlowCondition::STATUS_YES;?>
            <td>状态：<?php echo isset($statusAll[$status]) ? $statusAll[$status] : $status;?></td>
        </tr>
        <?php } ?>
        <?php if($condition[FlowCondition::TYPE_SUPPLIER]){ ?>
        <tr>
            <td>供应商</td>
            <td colspan="2"><?php echo isset($info[FlowCondition::TYPE_SUPPLIER]) && $info[FlowCondition::TYPE_SUPPLIER]["lower_limit"] ? Supplier::getNameById($info[FlowCondition::TYPE_SUPPLIER]["lower_limit"]) : "全部";?></td>
            <?php $status = isset($info[FlowCondition::TYPE_SUPPLIER]) ? $info[FlowCondition::TYPE_SUPPLIER]["status"] : FlowCondition::STATUS_YES;?>
            <td>状态：<?php echo isset($statusAll[$status]) ? $statusAll[$status] : $status;?></td>
        </tr>
        <?php } ?>
        <?php if($condition[FlowCondition::TYPE_CATEGORY]){ ?>
        <tr>
            <td>商品分类</td>
            <td colspan="2"><?php echo isset($info[FlowCondition::TYPE_CATEGORY]) && $info[FlowCondition::TYPE_CATEGORY]["lower_limit"] ? ProductCategory::getNameById($info[FlowCondition::TYPE_CATEGORY]["lower_limit"]) : "全部";?></td>
            <?php $status = isset($info[FlowCondition::TYPE_CATEGORY]) ? $info[FlowCondition::TYPE_CATEGORY]["status"] : FlowCondition::STATUS_YES;?>
            <td>状态：<?php echo isset($statusAll[$status]) ? $statusAll[$status] : $status;?></td>
        </tr>
        <?php } ?>
    </table>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>