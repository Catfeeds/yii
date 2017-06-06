<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $data->id ?></td>
    <td><a href="<?= Url::to(['area/index', 'parentId' => $data->id]) ?>"><?= $data->name ?></a></td>
    <td><?= $data->showParentName() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td><?= $data->sort ?></td>
    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['area/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['area/form', 'id' => $data->id]) ?>">编辑</a>
    </td>
</tr>