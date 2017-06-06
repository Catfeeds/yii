<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\ProductCategory;
use common\models\Supplier;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name') ?></td>
    <td><?= Html::activeDropDownList($model, 'product_category_id', ProductCategory::getCatrgorySelectData()) ?></td>
    <td><?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData()) ?></td>
    <td></td>
    <td><?= Html::activeDropDownList($model, 'supplier_id', Supplier::getSupplierSelectData()) ?></td>
    <td><?= Html::activeTextInput($model, 'barcode') ?></td>
    <td><?= Html::activeTextInput($model, 'spec') ?></td>
    <td><?= Html::activeTextInput($model, 'unit') ?></td>
    <td><?= Html::activeTextInput($model, 'purchase_price') ?></td>
    <td><?= Html::activeTextInput($model, 'sale_price') ?></td>
    <td><?= Html::activeTextInput($model, 'inventory_warning') ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['product/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['product/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>