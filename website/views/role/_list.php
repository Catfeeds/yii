<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $key+1 ?></td>
    <td><?= $data->name; ?></td>
    <td><?= $data->showDeparmentName() ?></td>
    <td><?= $data->showSole() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <?php if(!in_array($data->id, [1,2,3])):?>
        <?php if($data->status == 1){?>
        <a href="javascript:void(0)" invalid-data="<?= Url::to(['invalid', 'id' => $data->id]) ?>">无效</a> |
        <?php }elseif($data->status == 0){?>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['delete', 'id' => $data->id]) ?>">删除</a> |
         <?php }?> 
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['role/form', 'id' => $data->id]) ?>">编辑</a>
        <?php endif;?>
    </td>
</tr>