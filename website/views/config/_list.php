<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $data->id ?></td>
    <td><?= $data->set_name ?></td>
    <td><?= $data->set_value ?></td>
    <td><?= $data->set_desc ?></td>

<!--    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['form', 'id' => $data->id]) ?>">编辑</a>
    </td>-->
</tr>