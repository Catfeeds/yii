<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Role;
?>
<tr>
    <td></td>
    <td><?= Html::activeTextInput($model, 'name', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeTextInput($model, 'mac', ["onkeyup" => "value=value.replace(/[^\w\-]/ig,'')","class" => "computerMac", "maxlength" => 17]) ?></td>
    <td><?= Html::activeDropDownList($model, 'type', $model::getTypeSelectData()) ?></td>
    <td><?= Html::activeDropDownList($model, 'role_id', Role::getListByDepartmentId("", "create"), ['prompt' => '全部']) ?></td>
    <td><?= Html::activeDropDownList($model, 'position', $model::getPositionSelectData()) ?></td>
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
<script type="text/javascript">
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
</script>