<?php 
    use yii\helpers\Url;
    ?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->showName() ?></td>
    <td><?= $data->showNumber() ?></td>
    <td><?= $data->acronym ?></td>
    <td><?= $data->showParentName() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <?php if($data->id != 1) { ?>
        <?php if($data->status == 1){?>
            <a href="javascript:void(0)" invalid-data="<?= Url::to(['invalid', 'id' => $data->id]) ?>">无效</a> |
        <?php }elseif($data->status == 0){?>
            <a href="javascript:void(0)" delete-data="<?= Url::to(['delete', 'id' => $data->id]) ?>">删除</a> |
        <?php }?> 
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['form', 'id' => $data->id]) ?>">编辑</a>
        <?php } ?>
    </td>
</tr>