<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use libs\Utils;
use common\models\Department;
use common\models\Supplier;
use common\models\ProductCategory;
use common\models\Admin;
use libs\common\Flow;
$this->title = '业务操作-盘点计划';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption><?php echo $model->id ? "修改" : "添加"?>总盘点计划</caption>
        <tr id="quick-form">
            <td style="width: 30%;">计划名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td style="width: 30%;">预计盘点时间：<?= Html::activeTextInput($model, 'check_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly", "i"=>"2"]) ?></td>
            <td style="width: 20%;">盘点供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '全部']) ?></td>
            <td style="width: 20%;">是否盘点资金：<?= Html::checkbox("isCheckAmount", false, [ "style"=>'width:20px;height:15px;'])?></td>
        </tr>
        <tr>
            <td>计划单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value' => Utils::generateSn(Flow::TYPE_CHECK_PLANNING),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点结束时间：<?= Html::activeTextInput($model, 'end_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly", "i"=>"2"]) ?></td>
            <td>盘点商品名称：<?= Html::activeTextInput($model, 'product_name', ['style' => 'width:50%', 'placeholder' => '默认全部',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点商品分类：<?= Html::activeDropDownList($model, 'product_cate_id', ProductCategory::getCatrgorySelectData(), ['prompt' => '全部']) ?></td>
        </tr>
        <tr>
            <td colspan="4">计划原因：<?= Html::activeTextInput($model, 'remark', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
        </tr>
        <tr>
            <td colspan="4"><h2>选择盘点部门</h2></td>
        </tr>
        <?php //$departmentAll = Admin::checkSupperFlowAdmin() ? Department::getSelectData(-1) : [Admin::getDepId()];?>
        <?php $departmentAll = Department::getSelectData(-1);?>
        <?php foreach ($departmentAll as $departmentId => $departmentName) : ?>
        <?php if(!$departmentId){continue;}?>
        <tr>
            <td colspan="4" style="text-align: left;">
                <?= Html::checkbox("dataId[{$departmentId}]", false, ["value" => $departmentName, 'style' => 'width:20px;height:15px;margin: 0 20px;'])?>
                    <?php echo $departmentName;?></td>
        </tr>
        <?php endforeach;?>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['checkplanning/addorupdate', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?> 
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?php 
$url = Url::to(["warehouse/ajaxdepartmentwarehouselist"]);
$js = <<<JS
    $(".selDepartment").change(function(){
        var id = $(this).val();
        $.get("{$url}",{"id":id}, function(result){
            if($.isNumeric(result)) {
                alert("请选择部门");
                return false;
            }
            $(".showWarehouseList").remove();
            $(".showWarehouseTitle").after(result);
            $(".selDate").date_input();
        },"html");
    });
    $(document).on("click", ".selWarehouse", function(){
        if($(this).is(':checked')){
            var id = $(this).val();
            $(this).parent("td").find(".warehouseId").val(id);
            $(this).parent("td").parent("tr").find(".wareInput").removeAttr("disabled");
        } else {
            $(this).parent("td").find(".warehouseId").val("");
            $(this).parent("td").parent("tr").find(".wareInput").val("");
            $(this).parent("td").parent("tr").find(".wareInput").attr("disabled", "disabled");
        }
    });
JS;
//Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addorupdate');
?>