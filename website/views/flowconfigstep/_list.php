<?php 
use yii\helpers\Url; 
use common\models\FlowConfigStep;
use libs\common\Flow;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= Flow::showType($data->config_sn) ?></td>
   
    <td><?= FlowConfigStep::showStep($data->create_step) ?></td>
    <td><?= FlowConfigStep::showStep($data->verify_step) ?></td>
    <td><?= FlowConfigStep::showStep($data->approval_step) ?></td>
    <td><?= FlowConfigStep::showStep($data->operation_step) ?></td>

    <td><?= Flow::showType($data->business_end_table) ?></td>
    
    <td>
        <a href="javascript:void(0)" get-update-form="<?= Url::to(['flowconfigstep/form', 'id' => $data->id]) ?>">编辑</a> |
        <a href="javascript:void(0)" delete-data="<?= Url::to(['flowconfigstep/delete', 'id' => $data->id]) ?>">删除</a>
    </td>
</tr>