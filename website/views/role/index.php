<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Admin;
use common\models\Department;
$this->title = '部门基础数据-角色管理';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="role/index" />
            <span>角色名称：
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" onkeyup="javascript:validateValue(this)"/>
            </span>
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门：
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selDepartmentId']) ?>
            </span>
            <?php //} ?>
            <span>状态：
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择','class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>角色列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="40%">角色</th>
            <th width="20%">所属部门</th>
            <th width="10%">是否唯一<br>
                <a href="javascript:void(0)" class="showRemark" style="color: #06c;white-space: nowrap;">说明</a></th>
            <th width="10%">状态</th>
            <th width="15%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="6">暂无符合条件的角色记录</td></tr>
        <?php } ?>
    </table>
    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['form']) ?>" href="javascript:void(0)">新增</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <a class="button blue-button" href="<?= Url::to(['role/downtemplate']) ?>" >下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['role/import']) ?>"  href="javascript:void(0)">导入</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?php
$js = <<<JS
    $(function(){
        $(".showRemark").click(function(){
            if($(window).width() > 1200) {
                $(".tanchuang").attr("style","width:400px;height:100px;right:40%;display: none");
            } else {
                $(".tanchuang").attr("style","display: none;width: 40%;height: 100px;min-width: 40%;right: 30%;");
            }
            var html = '<div style="text-align:center;font-size:18px;">角色唯一说明</div>';
            html += '<div style="text-align:center;">审核批准执行等操作角色必须是唯一的！</div>';
            $(".tanchuang .showContent").html(html);
            $("#failCause").val("");
            $(".houtai_overlay").show();
            $(".tanchuang").show();
        });
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'realtime');
?>