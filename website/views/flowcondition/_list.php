<?php 
use yii\helpers\Url; 
use common\models\FlowConfig;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= FlowConfig::getNameById($data->config_id) ?></td>
    <td><?= $data->name ?></td>
    <td><?= $data->showType() ?></td>
    <td><?= $data->lower_limit ?></td>
    <td><?= $data->upper_limit ?></td>
    <td><?= $data->showStatus() ?></td>
<!--    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['flowcondition/delete', 'id' => $data->id]) ?>">删除</a> |
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['flowcondition/form', 'id' => $data->id]) ?>">编辑</a>
    </td>-->
</tr>