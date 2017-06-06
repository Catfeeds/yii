<?php 
use yii\helpers\Url;
use common\models\Admin;
?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->content ?></td>
  
    
    <td>
       
        <?php if($data->status==0){?>
         <a href="<?= Url::to(['businessremind/confirm', 'id' => $data->id]) ?>">确认</a> |
        <?php }?>	
        <a href="<?= Url::to([$data->business_type.'/info', 'id' => $data->business_id]) ?>">链接</a>
    </td>
</tr>