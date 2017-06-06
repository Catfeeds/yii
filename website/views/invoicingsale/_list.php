<?php 
use yii\helpers\Url; 
use common\models\Warehouse;
use common\models\Admin;
use common\models\ProductInvoicingSale;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
    <td><?= $data->name ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
    <td><?= number_format($data->total_amount, 2) ?></td>
    <td><?= Admin::getNameById($data->create_admin_id) ?></td>
    <td><?= $data->showStatus() ?></td>
    <td><?= $data->status == ProductInvoicingSale::STATUS_NO_SALE ? "销存" : "无" ?></td>
    <td>
        <a class="quick-form-button" href="<?= Url::to(['invoicingsale/info',"id" => $data->id]) ?>">详情</a>
        <?php if($data->status == ProductInvoicingSale::STATUS_NO_SALE) { ?>
            <a class="quick-form-button" href="<?= Url::to(['invoicingsale/info',"id" => $data->id]) ?>" style="margin-left: 10px;">销存</a>
        <?php } else {  ?>
            <span style="margin-left: 10px;">销存</span>
        <?php } ?>
    </td>
</tr>