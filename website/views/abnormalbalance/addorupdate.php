<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\Department;
use common\models\FlowConfig;
use common\models\Admin;
use libs\common\Flow;
$this->title = '业务操作-业务收支流水日志详情';
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list taleft">
        <caption>添加业务收支流水</caption>
        <tr id="quick-form">
            <td>流水名称：<?= Html::activeTextInput($model, 'name', ['style' => 'width:80%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
            <td>变动类型：<?= Html::activeDropDownList($model, 'mod', $model::getModSelectData(), ['prompt' => '请选择', "class" => "selMod"]) ?></td>
        </tr>
        <tr>
            <td colspan="2">变动金额：<?= Html::activeTextInput($model, 'current_balance', ['style' => 'width:80%', 'class' => 'verifyFloat', 'onkeyup'=>"javascript:CheckInputIntFloat(this)"]) ?></td>
        </tr>
        <tr>
            <td colspan="2">变动说明：<?= Html::activeTextInput($model, 'content', ['style' => 'width:80%',"onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial']) ?></td>
        </tr>
    </table>
    <div class="buttons">
        <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['abnormalbalance/addorupdate', 'id' => $model->id]) ?>">保存</a> 
        <a class="button blue-button" href="<?= Url::to(['abnormalbalance/index']) ?>">返回</a>
    </div>
    <?php ActiveForm::end(); ?> 
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?php 
$departmentId = Yii::$app->user->getIdentity()->department_id;
$departmentSelf = Admin::checkSupperFlowAdmin() ? Department::getSelectData(-1) : ["" => "请选择",$departmentId => Department::getNameById($departmentId)];
$departmentAll = Department::getSelectData(-1);
$modUrl = Url::to(["ajax/ajaxabnormalmod"]);
$url = Url::to(["department/ajaxdepartmentbalance"]);
$expenSelf = '<tr class="showDepart"><td>支出部门：<select id="abnormalbalance-department_id" class="selDepartment" name="AbnormalBalance[department_id]">';
foreach ($departmentSelf as $key => $value) {
    $expenSelf .= '<option value="'.$key.'">'.$value.'</option>';
}
$expenSelf .= '</select></td><td>部门余额：<span class="showDepartBalance"></span></td></tr>';
$expenAll = '<tr class="showDepart"><td>支出部门：<select id="abnormalbalance-department_id" class="selDepartment" name="AbnormalBalance[department_id]">';
foreach ($departmentAll as $key => $value) {
    $expenAll .= '<option value="'.$key.'">'.$value.'</option>';
}
$expenAll .= '</select></td><td>部门余额：<span class="showDepartBalance"></span></td></tr>';
$incomeSelf = '<tr class="showDepart"><td>收入部门：<select id="abnormalbalance-department_id" class="selDepartment" name="AbnormalBalance[income_department_id]">';
foreach ($departmentSelf as $key => $value) {
    $incomeSelf .= '<option value="'.$key.'">'.$value.'</option>';
}
$incomeSelf .= '</select></td><td>部门余额：<span class="showDepartBalance"></span></td></tr>';
$incomeAll = '<tr class="showDepart"><td>收入部门：<select id="abnormalbalance-department_id" class="selDepartment" name="AbnormalBalance[income_department_id]">';
foreach ($departmentAll as $key => $value) {
    $incomeAll .= '<option value="'.$key.'">'.$value.'</option>';
}
$incomeAll .= '</select></td><td>部门余额：<span class="showDepartBalance"></span></td></tr>';

$js = <<<JS
    $(".selMod").change(function(){
        var mod = $(this).val();
        $(".showDepart").remove();
        if(!mod){
            return false;
        }
        $.get("{$modUrl}",{"mod":mod}, function(result){
            if(!result.state){
                alert(result.message.length > 0 ? result.message : "变动类型错误");
                return false;
            }
            if(result.departmentList.expen == 1) {
                $("#quick-form").after('{$expenSelf}');
            }
            if(result.departmentList.expen == 2) {
                $("#quick-form").after('{$expenAll}');
            }
            if(result.departmentList.income == 1) {
                $("#quick-form").after('{$incomeSelf}');
            }
            if(result.departmentList.income == 2) {
                $("#quick-form").after('{$incomeAll}');
            }
        },"json");
    });
    $(document).on("change", ".selDepartment", function(){
        var department = $(this);
        $.get("{$url}",{"id":department.val()}, function(result){
            department.parents("tr").find("td .showDepartBalance").text(result);
        },"json");
    });
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'add');
?>