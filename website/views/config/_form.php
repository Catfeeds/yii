<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<tr>
    <td></td>
    <td><?= Html::activeTextInput($model, 'set_name') ?></td>
    <td><?= Html::activeTextInput($model, 'set_value') ?></td>
   <td><?= Html::activeTextInput($model, 'set_desc') ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').remove();">取消</a>
    </td>
</tr>