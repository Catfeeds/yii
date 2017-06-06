<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Warehouse;
use common\models\Department;
use libs\common\Flow;
$this->title = '销存管理-销存商品管理';
?>
<?= $this->context->renderPartial('/public/menu') ?>

<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="invoicing/product" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门</span>
            <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            <?php //} ?>
            <?php //$department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>所属仓库</span>
            <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus("", "",$department_id), ['prompt' => '请选择', 'class' => 'form-select selWarehouseId']) ?>
            <input class="form-button" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>库存管理表单</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="15%">库存管理类型</th>
            <th width="10%">库存管理单号</th>
            <th width="10%">所属部门</th>
            <th width="10%">所属仓库</th>
            <th width="10%">进展状态</th>
            <th width="10%">下一步操作</th>
            <th width="10%">操作</th>
        </tr>
        <?php foreach($listDatas as $key => $data){ ?>
            <?php $nextStep = Flow::showNextStepByInfo($data->business_type, $data);?>
            <tr>
                <td><?= $key+1; ?></td>
                <td><?= Flow::showType($data->business_type)?></td>
                <td><?= $data->sn; ?></td>
                <td><?= Department::getNameById($data->department_id)?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id)?></td>
                <td><?= Flow::showStatusAll($data->status)?></td>
                <td><?= $nextStep["nextStep"]?></td>
                <td>详情</td>
            </tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php
    $ajaxUrl = Url::to(["warehouse/ajaxwarehousebydepartmentid"]);
$js = <<<JS
    $(".selDepartmentId").change(function (){
        var departmentId = $(".selDepartmentId").val();
        var html = "<option value=''>请选择</option>";
        if(!departmentId) {
            $(".selWarehouseId").html(html);
            return false;
        }
        $.get("{$ajaxUrl}", {"departmentId":departmentId},function(result){
            $.each(result, function (k, v){
                html += "<option value='"+k+"'>"+v+"</option>";
            });
            $(".selWarehouseId").html(html);
        }, "json");
    });
JS;

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'product');
