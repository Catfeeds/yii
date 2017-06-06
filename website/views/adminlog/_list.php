<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $key+1; ?></td>
    <td><?= $data->content ?></td>
    <td><?= $data->create_time ?></td>
    <td><?= $data->showAdminName() ?></td>
    <td><?= $data->admin_id ?></td>
    <td><?= $data->showStatus() ?></td>
</tr>