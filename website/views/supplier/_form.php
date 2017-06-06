<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => "verifySpecial"]) ?></td>
    <td><?= Html::activeTextInput($model, 'num', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 20, 'class' => "verifySpecial"]) ?></td>
    <td><?= Html::activeDropDownList($model, 'level', $model::getLevelSelectData()) ?></td>
    <td><?= Html::activeDropDownList($model, 'pay_period', $model::getPayPeriodSelectData()) ?></td>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['supplier/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['supplier/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>