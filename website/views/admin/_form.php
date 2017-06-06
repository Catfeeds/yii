<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Department;
use common\models\Role;
use common\models\Admin;
//$departmentAll = Admin::checkSupperFlowAdmin() ? Department::getSelectData(-1) : ['' => '请选择',Admin::getDepId() => Department::getNameById(Admin::getDepId())];
$departmentAll = Department::getSelectData(-1);
unset($departmentAll['']);
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'username', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeTextInput($model, 'job_number', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 18, 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeTextInput($model, 'id_card', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => 'verifySpecial']) ?></td>
    <td><?= Html::activeDropDownList($model, 'department_id', $departmentAll, ['prompt' => '请选择', 'class' => 'selDepartment']) ?></td>
    <td><?= Html::activeDropDownList($model, 'role_id', $model->id ? Role::getListByDepartmentId($model->department_id, 'create') : [],['prompt' => '请选择', "class" => "selRole"]) ?></td>
    <td><?= Html::activeTextInput($model, 'entry_date',['class'=>'selDate', "i"=>"1", "readonly" => "readonly"]) ?></td>
    <td><?= Html::activeTextInput($model, 'leave_date',['class'=>'selDate', "i"=>"2", "readonly" => "readonly"]) ?></td>
    <?php if($data->id == Yii::$app->user->getId()){ ?>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <?php } else { ?>
    <td></td>
    <?php } ?>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>
<script src="/script/jquery.date_input.js"></script>
<script type="text/javascript">$(".selDate").date_input();</script>

<script type="text/javascript">
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
</script>