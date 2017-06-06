<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<tr id="quick-form">
    <td><?= Html::activeHiddenInput($model, 'parentId') ?></td>
    <td><?= Html::activeTextInput($model, 'name') ?></td>
    <td><?= $model->showParentName() ?></td>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <td><?= Html::activeTextInput($model, 'sort') ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['area/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['area/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>