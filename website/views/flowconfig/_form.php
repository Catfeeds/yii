<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Role;
use common\models\Department;
use libs\common\Flow;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name') ?></td>
    <td><?= Html::activeDropDownList($model, 'type', Flow::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'selFlowType']) ?></td>
    <td><?= Html::activeDropDownList($model, 'create_role_id', Role::getAllRoleByStatus(Role::STATUS_OK), ['prompt' => '请选择', 'class' => 'create']) ?></td>
    <td><?= Html::activeTextInput($model, 'create_name', [ 'class' => 'create']) ?></td>
    <td><?= Html::activeDropDownList($model, 'create_department_id', Department::getSelectData(-1), [ 'class' => 'create']) ?></td>
    <td><?= Html::activeDropDownList($model, 'verify_role_id', Role::getAllRoleByStatus(Role::STATUS_OK), ['prompt' => '请选择', 'class' => 'verify']) ?></td>
    <td><?= Html::activeTextInput($model, 'verify_name', [ 'class' => 'verify']) ?></td>
    <td><?= Html::activeDropDownList($model, 'verify_department_id', Department::getSelectData(-1), [ 'class' => 'verify']) ?></td>
    <td><?= Html::activeDropDownList($model, 'approval_role_id', Role::getAllRoleByStatus(Role::STATUS_OK), ['prompt' => '请选择', 'class' => 'approval']) ?></td>
    <td><?= Html::activeTextInput($model, 'approval_name', ['class' => 'approval']) ?></td>
    <td><?= Html::activeDropDownList($model, 'approval_department_id', Department::getSelectData(-1), [ 'class' => 'approval']) ?></td>
    <td><?= Html::activeDropDownList($model, 'operation_role_id', Role::getAllRoleByStatus(Role::STATUS_OK), ['prompt' => '请选择', 'class' => 'operation']) ?></td>
    <td><?= Html::activeTextInput($model, 'operation_name', [ 'class' => 'operation']) ?></td>
    <td><?= Html::activeDropDownList($model, 'operation_department_id', Department::getSelectData(-1), ['class' => 'operation']) ?></td>
    <?php if(!$model->getIsNewRecord()){ ?>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData(), ['prompt' => '请选择']) ?></td>
    <?php } else { ?>
    <td></td>
    <?php } ?>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowconfig/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowconfig/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>
<script type="text/javascript">
    $(".selFlowType").change(function(){
        var flowType = $(this).val();
        if(flowType) {
            ajaxFlowLogic(flowType);
        }
    });
    function ajaxFlowLogic(flowType) {
        $.get("<?php echo Url::to(["flowconfig/ajaxflowlogic"]);?>",{"flowType":flowType}, function(result){
            $.each(result, function(key, val){
                if($("."+key).length > 0) {
                    if(val){
                        $("."+key).removeAttr("disabled");
                    } else {
                        $("."+key).attr("disabled","true");
                    }
                }
            });
        }, "json");
    }
    <?php if(!$model->getIsNewRecord()){ ?>
        ajaxFlowLogic(<?php echo $model->type;?>);
    <?php } ?>
</script>