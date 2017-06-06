<?php use yii\helpers\Url; ?>
<tr>
    <td><?= $data['name']; ?></td>
    <td><?= $data['rows'] ?></td>
    <td><?= Yii::$app->formatter->asShortSize($data['data_length']); ?></td>
    <td><?= $data['create_time'] ?></td>

 
    <td>
        <a href="javascript:void(0)" ajax-data="<?= Url::to(['export/optimize', 'tables' => $data['name']]) ?>">优化表</a> |
        <a href="javascript:void(0)" ajax-data="<?= Url::to(['export/repair', 'tables' => $data['name']]) ?>">修复表</a>
    </td>
</tr>