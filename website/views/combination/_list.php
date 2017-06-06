<?php 
    use yii\helpers\Url; 
    use common\models\Admin;
    use common\models\Warehouse;
?>
<tr>
    <td><?= $key + 1 ?></td>
    <td><?= $data->name ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
    <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
    <td><?= $data->showPayment() ?></td>
    <td><?= $data->deposit ?></td>
    <td><?= $data->common ? $data->common : "无" ?></td>
    <td><?= Admin::getNameById($data->create_admin_id) ?></td>
    <td>
        <a href="<?= Url::to(['combination/info', 'id' => $data->id]) ?>">详情</a> |
        <a href="javascript:void(0)" delete-data="<?= Url::to(['combination/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="<?= Url::to(['combination/update', 'id' => $data->id]) ?>">编辑</a> |
        <a href="javascript:void(0)" class="sendCheckout" i="<?= $data->id?>">生成出库</a> 
    </td>
</tr>