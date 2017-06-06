<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use libs\Utils;
use common\models\Department;
use common\models\Supplier;
use common\models\ProductCategory;
use common\models\Admin;
use common\models\Warehouse;
use libs\common\Flow;
$this->title = '业务操作-部门盘点计划';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption><?php echo $model->id ? "修改" : "添加"?>部门盘点计划</caption>
        <tr id="quick-form">
            <td style="width: 30%;">计划名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td style="width: 30%;">预计盘点时间：<?= Html::activeTextInput($model, 'check_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly"]) ?></td>
            <td style="width: 20%;">盘点供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '全部']) ?></td>
            <td style="width: 20%;">是否盘点资金：<?= Html::checkbox("isCheckAmount", false, [ "style"=>'width:20px;height:15px;'])?></td>
        </tr>
        <tr>
            <td>计划单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value' => Utils::generateSn(Flow::TYPE_CHECK_DEPARTMENT),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点结束时间：<?= Html::activeTextInput($model, 'end_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly"]) ?></td>
            <td>盘点物料名称：<?= Html::activeTextInput($model, 'product_name', ['style' => 'width:50%', 'placeholder' => '默认全部',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点物料分类：<?= Html::activeDropDownList($model, 'product_cate_id', ProductCategory::getCatrgorySelectData(), ['prompt' => '全部']) ?></td>
        </tr>
        <tr>
            <td colspan="4">计划原因：<?= Html::activeTextInput($model, 'remark', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
        </tr>
        <?php //if(Admin::checkSupperFlowAdmin()){  ?>
        <tr><td colspan="4">
                盘点部门：<?= Html::activeDropDownList($model, 'department_id', Department::getSelectData(-1), ['class' => "setDepartment"]) ?></td></tr>
        <?php //} else { ?>
<!--        <tr><td colspan="4">
                盘点部门：<?= Department::getNameById(Admin::getDepId());?>
                <?= Html::activeHiddenInput($model, 'department_id', ["value" => Admin::getDepId()]); ?>
            </td></tr>-->
        <?php //} ?>
        <tr class="afterSelWarehouse">
            <td colspan="4"><h2>选择盘点仓库</h2></td>
        </tr>
        <?php //$warehouseAll = Admin::checkSupperFlowAdmin() ? [] : Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", Admin::getDepId());?>
        <?php $warehouseAll = [];?>
        <?php if(count($warehouseAll) > 1): foreach ($warehouseAll as $wId => $wName) : ?>
        <tr class="showSelWarehouse">
            <td colspan="4" style="text-align: left;">
                <?= Html::checkbox("dataId[{$wId}]", false, ["value" => $wName, 'style' => 'width:20px;height:15px;margin: 0 20px;'])?>
                    <?php echo $wName;?></td>
        </tr>
        <?php endforeach; else:?>
        <tr class="showSelWarehouse"><td colspan="4" style="text-align: left;">请选择盘点部门</td></tr>
        <?php endif;?>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['departmentcheckplanning/addorupdate', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?> 
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?php 
$url = Url::to(["ajax/getwarehouselist"]);
$js = <<<JS
    $(".setDepartment").change(function(){
        var id = $(this).val();
        $(".showSelWarehouse").remove();
        $.get("{$url}",{"id":id}, function(result){
            var html = "";
            $.each(result, function(k,v){
                html += '<tr class="showSelWarehouse"><td colspan="4" style="text-align: left;">';
                html += '<input type="checkbox" name="dataId['+k+']" value="'+v+'" style="width:20px;height:15px;margin: 0 20px;">';
                html += v+'</td></tr>';
            }); 
            $(".afterSelWarehouse").after(html);
            if($(".showSelWarehouse").length == 0) {
                $(".afterSelWarehouse").after('<tr class="showSelWarehouse"><td colspan="4" style="text-align: left;">该部门无下属仓库</td></tr>');
            }
        },"json");
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addorupdate');
?>