<?php 
    use yii\helpers\Url; 
    use common\models\Area;
    use common\models\Department;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name; ?></td>
    <td><?= $data->showType() ?></td>
    <td><?= $data->num ?></td>
    <td><?= Area::getNameById($data->area_id); ?></td>
     <td><?= $data->showSale(); ?></td>
     <td><?= Department::getNameById($data->department_id); ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['warehouse/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['warehouse/form', 'id' => $data->id]) ?>">编辑</a>
    </td>
</tr>