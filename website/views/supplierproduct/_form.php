<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Supplier;
use common\models\Product;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => "verifySpecial"]) ?></td>
    <td><?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData(Supplier::STATUS_OK)) ?></td>
    <td><?= Html::activeTextInput($model, 'num', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => "verifySpecial", "style" => "width:80%;"]) ?></td>
    <td><?= Html::activeTextInput($model, 'purchase_price',['class' => "verifyFloat", "style" => "width:80%;", "maxLength" => 8]) ?></td>
    <td><?= Html::activeTextInput($model, 'spec', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 10, 'class' => "verifySpecial", "style" => "width:80%;"]) ?></td>
    <td><?= Html::activeTextInput($model, 'unit', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 10, 'class' => "verifySpecial", "style" => "width:80%;"]) ?></td>
    <td><?= Html::activeDropDownList($model, 'material_type', Product::getTypeSelectData()) ?></td>
    <td></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['supplierproduct/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['supplierproduct/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>
<script type="text/javascript">
    $(".verifyFloat").attr("onkeyup", "javascript:CheckInputIntFloat(this)");
    $(".verifyFloat").blur(function(){
        CheckInputIntFloat(this);
    });
</script>
