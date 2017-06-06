<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use common\models\Admin;
use common\models\Role;
$this->title = '业务基础数据-员工管理';
$departmentId = Admin::checkSupperFlowAdmin() ? "" : Admin::getDepId();
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <div class="filter">
        <form method="get">
            <input type="hidden" name="r" value="admin/index" />
            <span>名称
            <input class="form-text verifySpecial" type="text" placeholder="关键字..." name="keyword" value="<?= Yii::$app->request->get('keyword') ?>" />
            </span>
            <?php //if(Admin::checkSupperFlowAdmin()){ ?>
            <span>所属部门
                <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), Department::getSelectData(-1), ['class' => 'form-select selIndexDepartment']) ?>
            </span>
            <?php //} ?>
<!--            <span>所属角色
                <?= Html::dropDownList('role_id', Yii::$app->request->get('role_id'), Role::getListByDepartmentId($departmentId, 'create'), ['prompt' => '请选择', 'class' => 'form-select selIndexRole']) ?>
            </span>-->
            <span>所属角色
                <?= Html::dropDownList('role_id', Yii::$app->request->get('role_id'), Role::getListByDepartmentId('', 'create'), ['prompt' => '请选择', 'class' => 'form-select selIndexRole']) ?>
            </span>
            <span>状态
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $model::getStatusSelectData(), ['prompt' => '请选择', 'class' => 'form-select']) ?>
            </span>
            <input type="hidden" name="isDownload" value="0" id="isDownload" />
            <input class="form-button subSearch" type="submit" value="搜索" />
        </form>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption>员工列表</caption>
        <tr>
            <th width="5%">序号</th>
            <th width="10%">姓名</th>
            <th width="10%">工号</th>
            <th width="10%">证件号</th>
            <th width="10%">部门</th>
            <th width="10%">角色</th>
			<th width="10%">入职时间</th>
			<th width="10%">离职时间</th>
			 <th width="5%">状态</th>
            <th width="12%">操作</th>
        </tr>
        <?php if($listDatas){ foreach($listDatas as $key => $data){ ?>
        <?= $this->context->renderPartial('_list', compact(['data', 'key'])) ?>
        <?php } } else { ?>
        <tr><td colspan="10">暂无符合条件的员工记录</td></tr>
        <?php } ?>
    </table>

    <?= LinkPager::widget([
        'pagination' => $listPages,
    ]); ?>
    <?php ActiveForm::end(); ?>
    <div class="buttons">
        <a class="button blue-button" get-create-form="<?= Url::to(['form']) ?>" href="javascript:void(0)">新增</a>
        <a class="button blue-button" download-excel='subSearch'>导出</a>
        <a class="button blue-button" href="<?= Url::to(['admin/downtemplate']) ?>" >下载导入模板</a>
        <a class="button blue-button" import-excel="<?= Url::to(['admin/import']) ?>"  href="javascript:void(0)">导入</a>
        <div style="display:none"><input type="file" id="uploadExcel" name="excel" /></div>
    </div>
</div>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/excel') ?>
<?= $this->context->renderPartial('/jquery/dateInput', compact("message")) ?>

<?php
$ajaxDepartmentRoleUrl = Url::to(["ajax/ajaxdepartmentrole"]);
$js = <<<JS
    $(document).on("change", ".selDepartment", function(){
        var departmentId = $(this).val();
        $.get("{$ajaxDepartmentRoleUrl}",{"dId":departmentId, "type": "create"}, function(result){
            $(".selRole").find("option").remove();
            var html = "<option>请选择</option>";
            $.each(result, function(k,v){
               html += "<option value='"+k+"'>"+v+"</option>" 
            });
            $(".selRole").html(html);
        }, "json");
    });
    $(".selIndexDepartment").change(function(){
        var departmentId = $(this).val();
        $.get("{$ajaxDepartmentRoleUrl}",{"dId":departmentId, "type": "create"}, function(result){
            $(".selIndexRole").find("option").remove();
            var html = "<option>请选择</option>";
            $.each(result, function(k,v){
               html += "<option value='"+k+"'>"+v+"</option>" 
            });
            $(".selIndexRole").html(html);
        }, "json");
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'adminIndex');
?>