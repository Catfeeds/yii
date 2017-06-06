<?php 
use yii\helpers\Url; 
use common\models\Department;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_ABNORMAL_FUND, $data);
$allStep = Flow::showAllStep(Flow::TYPE_ABNORMAL_FUND, $data);
?>
<tr>
    <td><?= $key + 1; ?></td>
    <td><?= date("Y-m-d", strtotime($data->create_time)) . "<br>" . date("H:i:s", strtotime($data->create_time)) ?></td>
    <td><?= $data->name ?></td>
    <td><?= Department::getNameById($data->department_id) ?></td>
    <td><?= Department::getNameById($data->income_department_id) ?></td>
    <td><?= $data->showMod(); ?></td>
    <td><?= number_format($data->current_balance) ?></td>
    <td><?= FlowConfig::getNameById($data->config_id) ?></td>
    <td><?= Admin::getNameById($data->create_admin_id) ?></td>
    <td><?= Flow::showStatusAll($data->status) ?></td>
    <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
    <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
    <td>
        <a class="quick-form-button" href="<?= Url::to(['abnormalbalance/info',"id" => $data->id]) ?>">详情</a>
        <?php foreach ($allStep as $stepVal) { ?>
            <?php if($stepVal["state"]) { ?>
                <a class="quick-form-button" href="<?= Url::to(['abnormalbalance/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
            <?php } else {  ?>
                <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
            <?php } ?>
        <?php } ?>
    </td>
</tr>