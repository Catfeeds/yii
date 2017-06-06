<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use libs\common\Flow;
use common\models\Department;
use common\models\Warehouse;
$this->title = '历史表单统计';
?>
 <?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="stats/index" />
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <?php //} ?>
            <?php // $department_id = !Admin::checkSupperFlowAdmin() ? Admin::getDepId() : (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <?php $department_id = (Yii::$app->request->get('department_id') ? Yii::$app->request->get('department_id') : "-1");?>
            <span>所属仓库
            <?= Html::dropDownList('warehouse_id', Yii::$app->request->get('warehouse_id'), Warehouse::getAllByStatus("", "",$department_id), ['prompt' => '请选择', 'class' => 'form-select selWarehouseId']) ?>
            </span>
            <span>表单号
                <input class="form-text verifySpecial" type="text" placeholder="" name="sn" value="<?= Yii::$app->request->get('sn') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <span>表单类型
                <?= Html::dropDownList('business_type', Yii::$app->request->get('business_type'), Flow::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span><br>
            <span>开始时间
                <input class="form-text selDate" type="text"  name="beginDate" value="<?= Yii::$app->request->get('beginDate') ?>"  style="width: 100px;" readonly="readonly" i="1"/>
            </span>
            <span>结束时间
                <input class="form-text selDate" type="text"  name="endDate" value="<?= Yii::$app->request->get('endDate') ?>"  readonly="readonly" style="width: 100px;" i="1"/>
            </span>
            <?= Html::hiddenInput("isDownload", 0, ["id" => "isDownload"]); ?>
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?= !Admin::checkSupperFlowAdmin() ? Admin::getDepName() : "全部";?>-历史表单统计</caption>
        <tr>
            <th width="3%">序号</th>
            <th width="8%">表单名</th>
            <th width="8%">表单号</th>
            <th width="6%">管理表单ID</th>
            <th width="6%">管理表单类型</th>
            <th width="6%">所属部门</th>
            <th width="6%">所属仓库</th>
            <th width="5%">创建人</th>
            <th width="5%">审核人</th>
            <th width="5%">批准人</th>
            <th width="5%">执行人</th>
            <th width="5%">状态</th>
            <th width="5%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key=>$data){ ?>
            <tr>
                <td><?= $key+1; ?></td>
                <td><?= $data->name ?></td>
                <td><?= $data->sn ?></td>
                <td><?= $data->business_id ?></td>
                <td><?= Flow::showType($data->business_type) ?></td>
                <td><?= Department::getNameById($data->department_id)?></td>
                <td><?= Warehouse::getNameById($data->warehouse_id)?></td>
                <td><?= Admin::getNameById($data->create_admin_id) ?></td>
                <td><?= Admin::getNameById($data->verify_admin_id) ?></td>
                <td><?= Admin::getNameById($data->approval_admin_id) ?></td>
                <td><?= Admin::getNameById($data->operation_admin_id) ?></td>
                <td><?= Flow::showStatusAll($data->status); ?></td>
                <td> <a href="<?= Url::to([Flow::showTypeUrl($data->business_type).'/info', 'id' => $data->business_id]) ?>">详情</a></td>
            </tr>
        <?php } } else { ?>
        <tr><td colspan="13">暂无符合条件的历史表单统计记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>
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

Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'index');
?>
