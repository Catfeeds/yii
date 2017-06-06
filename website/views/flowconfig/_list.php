<?php 
use yii\helpers\Url; 
use common\models\FlowCondition;
use common\models\Role;
use common\models\Department;
use common\models\Admin;
use libs\common\Flow;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
    <td><?= Flow::showType($data->type) ?></td>
    <td><?= $data->create_role_id ? Role::getNameByRoleId($data->create_role_id) : "无" ?></td>
    <td><?= $data->create_name ? $data->create_name : "无" ?></td>
    <td><?= $data->create_department_id ? Department::getNameById($data->create_department_id) : "无" ?></td>
    <td><?= $data->verify_role_id ? Role::getNameByRoleId($data->verify_role_id) : "无" ?></td>
    <td><?= $data->verify_name ? $data->verify_name : "无" ?></td>
    <td><?= $data->verify_department_id ? Department::getNameById($data->verify_department_id) : "无" ?></td>
    <td><?= $data->approval_role_id ? Role::getNameByRoleId($data->approval_role_id) : "无" ?></td>
    <td><?= $data->approval_name ? $data->approval_name : "无" ?></td>
    <td><?= $data->approval_department_id ? Department::getNameById($data->approval_department_id) : "无" ?></td>
    <td><?= $data->operation_role_id ? Role::getNameByRoleId($data->operation_role_id) : "无" ?></td>
    <td><?= $data->operation_name ? $data->operation_name : "无" ?></td>
    <td><?= $data->operation_department_id ? Department::getNameById($data->operation_department_id) : "无" ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <a href="<?= Url::to(['flowconfig/info', 'id' => $data->id]) ?>">详情</a> 
        <?php $arr = [$data->create_department_id, $data->verify_department_id, $data->approval_department_id, $data->operation_department_id];?>
        <?php $arr = array_filter($arr); ?>
        <?php $arr = array_flip(array_flip($arr));?>
        <?php //if((count($arr) == 1 && reset($arr) == Yii::$app->user->getIdentity()->department_id) || Admin::checkSupperFlowAdmin() || Admin::checkBusinAdmin()){ ?>
            <?php if($data->status == 1){?>
                | <a href="javascript:void(0)" invalid-data="<?= Url::to(['flowconfig/invalid', 'id' => $data->id, 'status' => 0]) ?>">设置无效</a> |
                <a href="<?= Url::to(['flowconfig/addedit', 'id' => $data->id]) ?>">编辑</a>
            <?php }elseif($data->status == 0){?>
                | <a href="javascript:void(0)" valid-data="<?= Url::to(['flowconfig/invalid', 'id' => $data->id, 'status' => 1]) ?>">设置有效</a> |
                <!--<a href="javascript:void(0)" delete-data="<?= Url::to(['flowconfig/delete', 'id' => $data->id]) ?>">删除</a>--> 
            <?php }?> 
        <?php //}?> 
    </td>
</tr>