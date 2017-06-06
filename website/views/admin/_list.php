<?php 
    use yii\helpers\Url; 
    use common\models\Admin;
    ?>
<tr>
    <td><?= $key + 1 ?></td>
    <td><?= $data->showName() ?></td>
    <td><?= $data->job_number ?></td>
    <td><?= $data->id_card ?></td>
    <td><?= $data->showDeparmentName() ?></td>
    <td><?= $data->showRoleName() ?></td>
    <td><?= $data->entry_date ?></td>
    <td><?= $data->leave_date ?></td>
    <td><?= $data->showStatus() ?></td>
    <td>
        <?php if(in_array($data->role_id, [1,2,3])) { ?>
            <?php if(in_array(Yii::$app->user->getIdentity()->role_id, [1,2,3])){ ?>
            <a href="javascript:void(0)" resetpwd-data="<?= Url::to(['resetpwd', 'id' => $data->id]) ?>">重置密码</a>
            <?php } ?>
        <?php } else { ?>
            <?php if($data->id != Yii::$app->user->getId()){ ?>
            <?php //if($data->department_id == Yii::$app->user->getIdentity()->department_id || Admin::checkSupperAdmin() || Admin::checkBusinAdmin()) { ?>
                <?php if($data->status == 1){?>
                    <a href="javascript:void(0)" invalid-data="<?= Url::to(['invalid', 'id' => $data->id]) ?>">无效</a> |
                    <a href="javascript:void(0)" resetpwd-data="<?= Url::to(['resetpwd', 'id' => $data->id]) ?>">重置密码</a> |
                <?php }elseif($data->status == 0){?>
                    <a href="javascript:void(0)" delete-data="<?= Url::to(['delete', 'id' => $data->id]) ?>">删除</a> |
                    <a href="javascript:void(0)" valid-data="<?= Url::to(['valid', 'id' => $data->id]) ?>">有效</a> |
                <?php }?> 
                <a href="javascript:void(0)" get-update-form="<?= Url::to(['form', 'id' => $data->id]) ?>">编辑</a> 
            <?php //} ?>
            <?php } else { ?>
                <a href="javascript:void(0)" resetpwd-data="<?= Url::to(['resetpwd', 'id' => $data->id]) ?>">重置密码</a>
            <?php } ?>
        <?php } ?>
    </td>
</tr>