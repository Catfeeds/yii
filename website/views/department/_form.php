<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name', array("onkeyup" => "javascript:validateValue(this)", "maxlength" => 10, 'class' => 'verifySpecial')) ?></td>
    <td><?= Html::activeTextInput($model, 'number', array("onkeyup" => "javascript:validateValue(this)", "maxlength" => 10, 'class' => 'verifySpecial')) ?></td>
    <td><?= Html::activeTextInput($model, 'acronym', array("onkeyup" => "javascript:validateValue(this)", "maxlength" => 10, 'class' => 'verifySpecial')) ?></td>
    <td><?= Html::activeDropDownList($model, 'parent_id', $model::getSelectData(-1)) ?></td>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>
<?= $this->context->renderPartial('/jquery/js') ?>
<script type="text/javascript">
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
</script>