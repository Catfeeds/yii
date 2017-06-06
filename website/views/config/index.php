<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use libs\common\Flow;
$this->title = '系统基础数据-参数设置';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>系统参数设置</caption>
        <tr>
            <th>序号</th>
            <th>名称</th>
            <th>配置天数</th>
        </tr>
        <tr>
            <td>1</td>
            <td>过期操作类型</td>
            <?php $commonSet = isset($configInfo["commonSet"]) ? $configInfo["commonSet"] : 0;?>
            <td>
                <label>
                    <input type="radio" name="commonSet" value="0" style="margin-top: 8px;width: 20px;height: 20px;" <?= $commonSet ? "" : "checked='checked'"?>> 
                    驳回
                </label>
                <label style="margin-left: 36px;">
                    <input type="radio" name="commonSet" value="1" style="margin-top: 8px;width: 20px;height: 20px;" <?= $commonSet ? "checked='checked'" : ""?>> 
                    通过
                </label>
                <label style="margin-left: 10%;">
                    <a href="javascript:void(0)" class="setCommonAll">设置全局配置天数</a>
                </label>
            </td>
        </tr>
        <?php $i = 2; foreach(Flow::getTypeSelectData() as $type => $name){ ?>
        <tr>
            <td><?= $i;?></td>
            <td><?= $name;?></td>
            <?php $config = isset($configInfo["flowType_".$type]) ? $configInfo["flowType_".$type] : "";?>
            <td><?= Html::textInput("flowType[".$type."]", $config, ["onkeyup"=>"value=value.replace(/\D/g,'')", "style" => "width:60%", "class" => "setDayNum"])?></td>
        </tr>
        <?php $i++;} ?>
    </table>
    <div class="buttons">
        <a class="button blue-button" save-data="<?= Url::to(['config/setinfo']) ?>" href="javascript:void(0)">设置</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/setCombinationNum') ?>