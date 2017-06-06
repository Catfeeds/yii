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

$this->title = '业务操作-仓库盘点计划';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption><?php echo $model->id ? "修改" : "添加"?>仓库盘点计划</caption>
        <tr id="quick-form">
            <td style="width: 30%;">计划名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td style="width: 25%;">预计盘点时间：<?= Html::activeTextInput($model, 'check_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly"]) ?></td>
            <td style="width: 25%;">结束盘点时间：<?= Html::activeTextInput($model, 'end_time', ['style' => 'width:50%',"class"=>"selDate", "readonly" => "readonly"]) ?></td>
            <td style="width: 20%;">盘点供应商：<?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '全部']) ?></td>
        </tr>
        <tr>
            <td>计划单号：<?= Html::activeTextInput($model, 'sn', ['style' => 'width:60%', 'value' => Utils::generateSn(Flow::TYPE_CHECK_WAREHOUSE),"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td colspan="2">盘点商品名称：<?= Html::activeTextInput($model, 'product_name', ['style' => 'width:50%', 'placeholder' => '默认全部',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>盘点商品分类：<?= Html::activeDropDownList($model, 'product_cate_id', ProductCategory::getCatrgorySelectData(), ['prompt' => '全部']) ?></td>
        </tr>
        <tr>
            <td colspan="4">计划原因：<?= Html::activeTextInput($model, 'remark', ['style' => 'width:60%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
        </tr>
        <?php // $warehouseAll = Admin::checkSupperFlowAdmin() ? Warehouse::getAllByStatus(Warehouse::STATUS_OK) : Warehouse::getAllByStatus(Warehouse::STATUS_OK, "", Admin::getDepId());?>
        <?php $warehouseAll = Warehouse::getAllByStatus(Warehouse::STATUS_OK);?>
        <tr><td colspan="4">盘点仓库：<?= Html::activeDropDownList($model, 'warehouse_id', $warehouseAll, ['prompt' => '请选择', 'class' => "setWarehouse"]) ?></td></tr>
        <input type="hidden" name="dataId[0]" value="" class="selDataVal"/>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['warehousecheckplanning/addorupdate', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?> 
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>
<?php 
$url = Url::to(["ajax/getwarehouselist"]);
$js = <<<JS
    $(".setWarehouse").change(function(){
        var id = $(this).val();
        var name = $(this).find("option:selected").text();
        $(".selDataVal").val(name).attr("name", "dataId["+id+"]");
    }); 
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addorupdate');
?>