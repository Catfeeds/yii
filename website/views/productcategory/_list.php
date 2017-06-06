<?php 
    use yii\helpers\Url;
    use common\models\ProductCategory;
?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->showName() ?></td>
    <td><?= $data->factor ?></td>
    <td><?= ProductCategory::showBatchesName($data->is_batches) ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
       
        <a href="javascript:void(0)" delete-data="<?= Url::to(['productcategory/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['productcategory/form', 'id' => $data->id]) ?>">编辑</a>
    </td>
</tr>