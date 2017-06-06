<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $data['name']; ?></td>
    <td><?= Yii::$app->formatter->asShortSize($data['size']) ?></td>
    <td><?= $data['time']; ?></td>
    <td><?= $data['compress'] ?></td>
    <td>
        <a href="javascript:void(0)" delete-data="<?= Url::to(['import/del', 'id' => $data['time']]) ?>">删除</a> 
    </td>
</tr>