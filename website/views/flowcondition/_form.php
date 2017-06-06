<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\FlowConfig;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeDropDownList($model, 'config_id', FlowConfig::getAllSelectData(), ['prompt' => '请选择']) ?></td>
    <td><?= Html::activeTextInput($model, 'name', ['style' => 'width:70%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeDropDownList($model, 'type', $model::getTypeSelectData(), ['prompt' => '请选择']) ?></td>
    <td><?= Html::activeTextInput($model, 'lower_limit', ['style' => 'width:70%']) ?></td>
    <td><?= Html::activeTextInput($model, 'upper_limit', ['style' => 'width:70%']) ?></td>
     <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowcondition/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowcondition/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>