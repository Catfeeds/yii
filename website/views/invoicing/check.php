<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Warehouse;
use common\models\Department;
use common\models\Admin;
use common\models\Product;
//$departmentId = Admin::checkSupperFlowAdmin() ? 0 : Admin::getDepId();
$departmentId = 0;

$this->title = '销存管理-部门盘点管理';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="invoicing/check" />
            <span>盘点名称
                <input class="form-text" type="text" placeholder="" name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />  
            </span>
            <span>库区
                <?= Html::dropDownList('warehouseId', Yii::$app->request->get('warehouseId'), Warehouse::getAllByStatus(Warehouse::STATUS_OK, '', $departmentId), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= Admin::getDepName();?>-盘点管理</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="10%">盘点计划名</th>
            <th width="10%">盘点计划单号</th>
            <th width="8%">盘点部门</th>
            <th width="8%">盘点仓库</th>
            <th width="8%">盘点员</th>
            <th width="10%">盘点时间</th>
            <th width="10%">盘点类型</th>
            <th width="8%">进展状态</th>
            <th width="8%">下一步操作</th>
            <th width="15%">操作</th>
        </tr>
        <?php foreach($listDatas as $key => $data){ 
            $nextStep = $data->showNextStep();
            $allStep = $data->showAllStep();?>
            <tr>
                <td><?= $key + 1; ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->check_sn ?></td>
                <td><?= Department::getNameById($data->department_id); ?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
                <td><?= Admin::getNameById($data->check_admin_id) ?></td>
                <td><?= $data->check_time ?></td>
                <td><?= $data->check_type ? Product::showTypeName($data->check_type) : "全部" ?></td>
                <td><?= $data->showStatus();?></td>
                <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无";?></td>
                <td>
                    <a class="quick-form-button" href="<?= Url::to(['invoicing/checkinfo',"id" => $data->id]) ?>">详情</a>
                    <?php foreach ($allStep as $k => $stepVal) { 
                        if($k == 1) {echo "<br>";}
                        ?>
                        <?php if($stepVal["state"]) { ?>
                            <a class="quick-form-button" href="<?= Url::to(['invoicing/checkinfo',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
                        <?php } else {  ?>
                            <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
