<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->showName() ?></td>
    <td><?= $data->showNumber() ?></td>
    <td><?= $data->showLevel() ?></td>
    <td><?= $data->showPayPeriod() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <a href="<?= Url::to(['supplierproduct/index', 'supplier_id' => $data->id]) ?>">查看出品</a> |
        <a href="javascript:void(0)" delete-data="<?= Url::to(['supplier/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['supplier/form', 'id' => $data->id]) ?>">编辑</a>
    </td>
</tr>