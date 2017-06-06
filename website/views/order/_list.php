<?php 
use common\models\Department;
use yii\helpers\url;
use common\models\Role;
?>
<tr>
    <td><?= isset($key) ? $key+1 : 0; ?></td>
    <td><?= $data->name ?></td>
     <td><?= $data->sn ?></td>
         <td><?= $data->create_admin_id ? Role::getNameByRoleId($data->create_admin_id) : "无" ?></td>
       <td><?= $data->create_time ?></td>
         <td><?= $data->department_id ? Department::getNameById($data->department_id) : "无" ?></td>
        <td><?= $data->operation_admin_id?Role::getNameByRoleId($data->operation_admin_id) : "无"?></td>
         <td><?= $data->operation_time?></td>
         <td> <?=$data->customer_company?></td>
         <td><?=$data->showStatus()?></td>
         <td>
              <a class="quick-form-button" href="<?= Url::to(['order/info',"id" => $data->id]) ?>">详情</a>  
              <?php if($data->status) {?>
                 <span style="margin-left: 10px;"><?php echo '执行';?></span>
               
              <?php }else{?>
           <a class="quick-form-button" href="<?= Url::to(['order/info',"id" => $data->id]) ?>">执行</a>
                 <?php }?>
         </td>
    