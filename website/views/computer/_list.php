<?php use yii\helpers\Url;use common\models\Role; ?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->name ?></td>
    <td><?= $data->mac ?></td>
    <td><?= $data->showType() ?></td>
    <td><?= $data->role_id ? Role::getNameByRoleId($data->role_id) : "全部" ?></td>
    <td><?= $data->showPosition() ?></td>
     <td><?= $data->showStatus() ?></td>
    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['form', 'id' => $data->id]) ?>">编辑</a>
    </td>
</tr>