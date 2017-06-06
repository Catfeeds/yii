<?php 
use yii\helpers\Url;
use common\models\Admin;
use common\models\Product;
$nextStep = $data->showNextStepByInfo();
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
    <td><?= $data->id ?></td>
    <td><?= $data->showSupplierName() ?></td>
    <td><?= $data->supplier_product_id ?></td>
    <td><?= Product::showTypeName($data->material_type) ?></td>
    <td><?= $data->barcode ?></td>
    <td><?= $data->spec ?></td>
    <td><?= $data->unit ?></td>
    <td><?= number_format($data->purchase_price, 2) ?></td>
    <td><?= number_format($data->sale_price, 2) ?></td>
    <td><?= $data->showInventoryWarning() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td><?= $data->showModifyStatus() ?></td>
    <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
    <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
    <td>
        <a href="<?= Url::to(['product/info', 'id' => $data->id]) ?>">详情</a> 
    </td>
</tr>