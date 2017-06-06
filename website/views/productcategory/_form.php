<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\ProductCategory;
?>
<tr id="quick-form">

    <td></td>
    <td><?= Html::activeTextInput($model, 'name',[ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeTextInput($model, 'factor',[ "onkeyup" => "value=value.replace(/\D/g,'')"]) ?></td>
    <td><?= Html::activeDropDownList($model, 'is_batches', ProductCategory::getBatchesSelectData()) ?></td>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['productcategory/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['productcategory/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>

<script type="text/javascript">
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
</script>