<?php 
use yii\helpers\Url;
use common\models\Supplier;
use common\models\ProductCategory;
use libs\common\Flow;

$nextStep = Flow::showNextStepByInfo(Flow::TYPE_CHECK_PLANNING, $data);
$allStep = Flow::showAllStep(Flow::TYPE_CHECK_PLANNING, $data);
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
    <td><?= $data->sn ?></td>
    <td><?= $data->check_time ?></td>
    <td><?= $data->product_name ? $data->product_name : "全部" ?></td>
    <td><?= $data->product_cate_id ? ProductCategory::getNameById($data->product_cate_id): "全部" ?></td>
    <td><?= $data->supplier_id ? Supplier::getNameById($data->supplier_id) : "全部" ?></td>
    <td><?= $data->is_check_amount ? "是" : "否" ?></td>
    <td><?= Flow::showStatusAll($data->status) ?></td>
    <td><?= isset($nextStep["nextStep"]) ? $nextStep["nextStep"] : "无" ?></td>
    <td><?= isset($nextStep["nextStepAdmin"]) ? $nextStep["nextStepAdmin"] : "无" ?></td>
    <td>
        <a href="<?= Url::to(['checkplanning/info', 'id' => $data->id]) ?>">详情</a>
        <?php foreach ($allStep as $stepVal) { ?>
            <?php if($stepVal["state"]) { ?>
                <a class="quick-form-button" href="<?= Url::to(['checkplanning/info',"id" => $data->id]) ?>" style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></a>
            <?php } else {  ?>
                <span style="margin-left: 10px;"><?php echo $stepVal["stepName"];?></span>
            <?php } ?>
        <?php } ?>
    </td>
</tr>