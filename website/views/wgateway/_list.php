<?php 
use yii\helpers\Url; 
use common\models\Warehouse;
use common\models\Product;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
$product = "";
if($data->product_type == WarehousePlanning::TYPE_EXCEPTION) {
    $product = WarehouseBuyingProduct::findOne($data->product_id);
}
?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? 0 : $data->product_id ?></td>
    <td><?= $data->batches ?></td>
    <td><?= $data->product_type == WarehousePlanning::TYPE_EXCEPTION ? ($product ? $product->name : "未知") : Product::getNameById($data->product_id) ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
    <td><?= $data->showType() ?></td>
    <td><?= $data->stock ?></td>
    <td><?= $data->num ?></td>
    <td><?= $data->showGatewayType() ?></td>
    <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
    <td><?= $data->comment ? $data->comment : "无" ?></td>
</tr>