<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\SupplierProduct;
use common\models\Supplier;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
    <td><?= Supplier::getNameById($data->supplier_id); ?></td>
    <td><?= $data->num ?></td>
    <td><?= $data->purchase_price ?></td>
    <td><?= $data->spec ?></td>
    <td><?= $data->unit ?></td>
    <td><?= $data->showType() ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <?php if(!$data->is_update){ ?>
            <?php if($data->status == SupplierProduct::STATUS_NO){ ?>
            <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['supplierproduct/addproduct', 'id' => $data->id]) ?>">有效</a> |
            <?php } else { ?>
            <a class="quick-form-button" href="javascript:void(0)" invalid-data="<?= Url::to(['supplierproduct/invalidproduct', 'id' => $data->id]) ?>">无效</a> |
            <?php } ?>
            <a href="javascript:void(0)" delete-data="<?= Url::to(['supplierproduct/delete', 'id' => $data->id]) ?>">删除</a> |
            <a href="javascript:void(0)" get-update-form="<?= Url::to(['supplierproduct/form', 'id' => $data->id]) ?>">编辑</a>
        <?php } else {echo "无";}?>
    </td>
</tr>