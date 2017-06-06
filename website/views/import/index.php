<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = '业务基础数据-数据库备份文件';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>数据库备份文件列表</caption>
        <tr>
            <th width="10%">备份名称</th>
            <th width="10%">数据大小</th>
            <th width="15%">时间</th>
            <th width="10%">压缩方式</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key=>$data){ ?>
            <tr>
                <td><?= $data['name']; ?></td>
                <td><?= Yii::$app->formatter->asShortSize($data['size']) ?></td>
                <td><?= $data['time']; ?></td>
                <td><?= $data['compress'] ?></td>
                <td>
                    <a href="javascript:void(0)" recover-data="<?= Url::to(['import/recover', 'name' => $data['name']]) ?>">还原</a> 
                    <a href="<?= Url::to('/dbBack/'.$data['name']) ?>">下载</a> 
                    <a href="javascript:void(0)" delete-data="<?= Url::to(['import/del', 'name' => $data['name']]) ?>">删除</a> 
                </td>
            </tr>
        <?php } } else { ?>
            <tr><td colspan="5">暂无数据库备份文件</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>

    <?php ActiveForm::end(); ?>

    
    <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" backup-data="<?= Url::to(['import/backup']) ?>">手工备份</a> 
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
