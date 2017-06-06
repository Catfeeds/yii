<?php 
use yii\helpers\Url;
use common\models\Product;
use common\models\ProductCategory;
use common\models\Warehouse;
use common\models\Supplier;
use common\models\WarehousePlanning;
use common\models\WarehouseBuyingProduct;
if($data->type == WarehousePlanning::TYPE_EXCEPTION) {
    $productItem = WarehouseBuyingProduct::findOne($data->product_id);
?>
<tr>
    <td><?= $key + 1 ?></td>
    <td><?= $data->batches ?></td>
    <td><?= 0 ?></td>
    <td><?= $productItem->name ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
    <td><?= Supplier::getNameById($data->supplier_id); ?></td>
    <td><?= ProductCategory::getNameById($productItem->material_type)?></td>
    <td><?= $productItem->num ?></td>
    <td><?= $productItem->spec ?></td>
    <td><?= $productItem->unit ?></td>
    <td><?= number_format($productItem->price, 2) ?></td>
    <td><?= number_format($data->purchase_price, 2) ?></td>
    <td><?= "不需要" ?></td>
    <td><?= $data->number ?></td>
</tr>
<?php
}else {
    $productItem = Product::findOne($data->product_id);
?>
<tr>
    <td><?= $key + 1 ?></td>
    <td><?= $data->batches ?></td>
    <td><?= $data->product_id ?></td>
    <td><?= $productItem->name ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id); ?></td>
    <td><?= Supplier::getNameById($data->supplier_id); ?></td>
    <td><?= ProductCategory::getNameById($productItem->product_category_id)?></td>
    <td><?= $productItem->barcode ?></td>
    <td><?= $productItem->spec ?></td>
    <td><?= $productItem->unit ?></td>
    <td><?= number_format($data->purchase_price, 2) ?></td>
    <td><?= number_format($productItem->sale_price, 2) ?></td>
    <td><?= $productItem ? $productItem->showInventoryWarning() : "无" ?></td>
    <td><?= $data->number ?></td>
</tr>
<?php } ?>