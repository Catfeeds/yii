<?php 
use yii\helpers\Url; 
use common\models\Warehouse;
use common\models\FlowConfig;
use common\models\Admin;
use common\models\Supplier;
use libs\common\Flow;
$nextStep = Flow::showNextStepByInfo(Flow::TYPE_PLANNING, $data);
$allStep = Flow::showAllStep(Flow::TYPE_PLANNING, $data);
?>
<tr>
     <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
    <td><?= Warehouse::getNameById($data->warehouse_id) ?></td>
    <td><?= Supplier::getNameById($data->supplier_id) ?></td>
    <td><?= $data->sn ?></td>
    <td><?= number_format($data->total_money, 2) ?></td>
    <td><?= $data->showPayment() ?></td>
    <td><?= number_format($data->deposit, 2) ?></td>
    <td><?= $data->planning_date ?></td>
    <td><?= FlowConfig::getNameById($data->config_id) ?></td>
    <td><?= Admin::getNameById($data->create_admin_id) ?></td>
    <td><?= Flow::showStatusAll($data->status) ?></td>
    <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
    <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
    <td>
        <a class="quick-form-button" href="<?= Url::to(['wplanning/info',"id" => $data->id]) ?>">详情</a>
        <?php foreach ($allStep as $stepVal) { ?>
            <?php if($stepVal["state"]) { ?>
                <a class="quick-form-button" href="<?= Url::to(['wplanning/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
            <?php } else {  ?>
                <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
            <?php } ?>
        <?php } ?>
    </td>
</tr>