<?php 
use yii\helpers\Url; 
use common\models\Department;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_SALE, $data);
$allStep = Flow::showAllStep(Flow::TYPE_SALE, $data);
?>
<tr>
    <td><?= $key + 1; ?></td>
    <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
    <td><?= $data->name ?></td>
    <td><?= Department::getNameById($data->department_id) ?></td>
    <td><?= $data->mod == 1 || $data->mod == 3 ? number_format($data->balance , 2):0; ?></td>
    <td><?= $data->mod == 2 || $data->mod == 3 ? number_format($data->balance , 2):0; ?></td>
    <td><?= $data->showMod(); ?></td>
    <td><?= $data->current_balance; ?></td>
    <td><?= $data->content; ?></td>
    <td><?= Admin::getNameById($data->operation_admin_id) ?></td>
<!--    <td>
        <a class="quick-form-button" href="<?= Url::to(['departmentbalancelog/info',"id" => $data->id]) ?>">详情</a>
    </td>-->
</tr>