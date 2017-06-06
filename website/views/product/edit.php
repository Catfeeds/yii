<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Supplier;
use common\models\ProductCategory;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Product;
$this->title = '业务基础数据-物料修改申请';
$nextStep = $model->showNextStepByInfo();
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>物料修改申请</caption>
        <tr id="quick-form">
            <td style="width: 20%;">物料名称：<?= $model->name; ?></td>
            <td style="width: 30%;">供应商：<?= Supplier::getNameById($model->supplier_id); ?></td>
            <td style="width: 20%;">供应商出品ID：<?= $model->supplier_product_id; ?></td>
            <td style="width: 30%;">出品编码：<?= $model->num; ?></td>
        </tr>
        <tr>
            <td>进货参考价格：<?= Html::activeTextInput($model, 'purchase_price', ['style' => 'width:50%', 'class' => 'purchasePrice verifyFloat', 'onkeyup'=>"javascript:CheckInputIntFloat(this)"]) ?></td>
            <td>规格：<?= $model->spec; ?></td>
            <td>单位：<?= $model->unit; ?></td>
            <td>物料类别：<?= Product::showTypeName($model->material_type); ?></td>
        </tr>
        <tr>
            <td>物料分类：<?= Html::activeDropDownList($model, 'product_category_id', ProductCategory::getCatrgorySelectData(), ['prompt' => '请选择', 'class' => 'selCate']) ?></td>
            <td>条形码：<?= Html::activeTextInput($model, 'barcode', ['style' => 'width:50%',"onkeyup" => "value=value.replace(/[^\w\.\/]/ig,'')"]) ?></td>
            <td>销售价格：<?= Html::activeTextInput($model, 'sale_price', ['style' => 'width:50%', 'class' => 'salePrice verifyFloat', 'onkeyup'=>"javascript:CheckInputIntFloat(this)"]) ?></td>
            <td>是否需要批次号：<?= Html::activeDropDownList($model, 'is_batches', ProductCategory::getBatchesSelectData(), ['prompt' => '请选择', 'class' => 'selBatches']) ?></td>
        </tr>
        <tr>
            <td  colspan="4">库存警告：<?= Html::activeTextInput($model, 'inventory_warning', ['style' => 'width:50%', "onkeyup" => "value=value.replace(/\D/g,'')", "class" => "verifyFloat"]) ?></td>
        </tr>
    </table>
   <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['product/edit', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="javascript:history.back(-1);">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?php 
    $ajaxCateInfoUrl = Url::to(["productcategory/ajaxinfo"]);
    $price = $model->purchase_price;
    $js = <<<js
    $(".selCate").change(function (){
        var cateId = $(this).val();
        var purchase_price = $(".purchasePrice").val();
        if(!cateId || !purchase_price) {
            return false;
        }
        $.get("{$ajaxCateInfoUrl}", {"cateId":cateId}, function(result){
            if(result.factor){
                var salePrice = accMul(purchase_price, result.factor);
                $(".salePrice").val(salePrice / 100);
                $(".selBatches").find("option[value='"+result.is_batches+"']").attr("selected", "selected");
            }
        }, "json");
    });
    $(".purchasePrice").blur(function(){
        var cateId = $(".selCate").val();
        var purchase_price = $(".purchasePrice").val();
        if(!cateId || !purchase_price) {
            return false;
        }
        $.get("{$ajaxCateInfoUrl}", {"cateId":cateId}, function(result){
            if(result.factor){
                var salePrice = accMul(purchase_price, result.factor);
                $(".salePrice").val(salePrice / 100);
                $(".selBatches").find("option[value='"+result.is_batches+"']").attr("selected", "selected");
            }
        }, "json");
    });
js;
    Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'updateProduct');
?>