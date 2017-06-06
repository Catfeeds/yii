<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Role;
use common\models\Department;
use libs\common\Flow;
use common\models\FlowCondition;
use common\models\Supplier;
use common\models\Product;
use common\models\ProductCategory;
use common\models\Admin;
$this->title = '业务设置-业务流程操作';
//$departmentAll = (Admin::checkSupperFlowAdmin() || Admin::checkBusinAdmin()) ? Department::getSelectData(-1) : ['' => '请选择',Admin::getDepId() => Department::getNameById(Admin::getDepId())];
$departmentAll = Department::getSelectData(-1);
unset($departmentAll['']);
?>
<?= $this->context->renderPartial('/public/menu') ?>
<div class="main-container">
   
    <?php $form = ActiveForm::begin(); ?>
    <table id="table-list" class="table-list">
        <caption><?php echo $model->id ? "修改" : "添加"?>业务流程设置</caption>
        <tr id="quick-form">
            <td colspan="3" style="text-align: left;width: 50%;">流程名称：<?= Html::activeTextInput($model, 'name', array("onkeyup" => "javascript:validateValue(this)", 'class' => 'verifySpecial', 'style' => 'width:70%;')) ?></td>
            <td colspan="3" style="text-align: left;">流程类型：<?= Html::activeDropDownList($model, 'type', Flow::getTypeSelectData(), ['class' => 'selFlowType']) ?></td>
        </tr>
        <tr>
            <th width="15%">创建部门</th>
            <th width="18%">创建名称</th>
            <th width="18%">创建角色</th>
            <th width="15%">审核部门</th>
            <th width="18%">审核名称</th>
            <th width="18%">审核角色</th>
        </tr>
        <tr>
            <td><?= Html::activeDropDownList($model, 'create_department_id', $departmentAll, ['prompt' => '请选择', 'class' => 'create selcreate selDepartment', 'i' => 'create']) ?></td>
            <td><?= Html::activeTextInput($model, 'create_name', [ 'class' => 'create inputcreate verifySpecial',"onkeyup" => "javascript:validateValue(this)", 'style' => 'width:80%;']) ?></td>
            <td><?= Html::activeDropDownList($model, 'create_role_id', $model->id ? Role::getListByDepartmentId($model->create_department_id, 'create') : [], ['prompt' => '请选择', 'class' => 'create selcreate selRole', 'i' => 'create']) ?></td>
            <td><?= Html::activeDropDownList($model, 'verify_department_id', $departmentAll, ['prompt' => '请选择', 'class' => 'verify selverify selDepartment', 'i' => 'verify']) ?></td>
            <td><?= Html::activeTextInput($model, 'verify_name', [ 'class' => 'verify inputverify verifySpecial',"onkeyup" => "javascript:validateValue(this)", 'style' => 'width:80%;']) ?></td>
            <td><?= Html::activeDropDownList($model, 'verify_role_id', $model->id ? Role::getListByDepartmentId($model->verify_department_id, 'verify') : [], ['prompt' => '请选择', 'class' => 'verify selverify selRole', 'i' => 'verify']) ?></td>
        </tr>
        <tr>
            <th>批准部门</th>
            <th>批准名称</th>
            <th>批准角色</th>
            <th>执行部门</th>
            <th>执行名称</th>
            <th>执行角色</th>
        </tr>
        <tr>
            <td><?= Html::activeDropDownList($model, 'approval_department_id', $departmentAll, ['prompt' => '请选择', 'class' => 'approval selapproval selDepartment', 'i' => 'approval']) ?></td>
            <td><?= Html::activeTextInput($model, 'approval_name', ['class' => 'approval inputapproval verifySpecial',"onkeyup" => "javascript:validateValue(this)", 'style' => 'width:80%;']) ?></td>
            <td><?= Html::activeDropDownList($model, 'approval_role_id', $model->id ? Role::getListByDepartmentId($model->approval_department_id, 'approval') : [], ['prompt' => '请选择', 'class' => 'approval selapproval selRole', 'i' => 'approval']) ?></td>
            <td><?= Html::activeDropDownList($model, 'operation_department_id', $departmentAll, ['prompt' => '请选择', 'class' => 'operation seloperation selDepartment', 'i' => 'operation']) ?></td>
            <td><?= Html::activeTextInput($model, 'operation_name', [ 'class' => 'operation inputoperation verifySpecial',"onkeyup" => "javascript:validateValue(this)", 'style' => 'width:80%;']) ?></td>
            <td><?= Html::activeDropDownList($model, 'operation_role_id', $model->id ? Role::getListByDepartmentId($model->operation_department_id, 'operation') : [], ['prompt' => '请选择', 'class' => 'operation seloperation selRole', 'i' => 'operation']) ?></td>
        </tr>
    </table>
    <table id="table-list" class="table-list taleft">
        <caption><?php echo $model->id ? "修改" : "添加"?>流程设置条件</caption>
        <tr class="showCondition_<?= FlowCondition::TYPE_PRICE?>">
            <td>价格范围</td>
            <?php $lower_limit = isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["lower_limit"] : "";?>
            <td>下限金额：<input type="text" name="info[<?php echo FlowCondition::TYPE_PRICE;?>][lower_limit]" value="<?php echo $lower_limit;?>" style="width: 70%;" onkeyup="value=value.replace(/\D/g,'')"/></td>
            <?php $upper_limit = isset($info[FlowCondition::TYPE_PRICE]) ? $info[FlowCondition::TYPE_PRICE]["upper_limit"] : "";?>
            <td>上限金额：<input type="text" name="info[<?php echo FlowCondition::TYPE_PRICE;?>][upper_limit]" value="<?php echo $upper_limit;?>" style="width: 70%;" onkeyup="value=value.replace(/\D/g,'')"/></td>
        </tr>
       
        <tr class="showCondition_<?= FlowCondition::TYPE_TIME?>">
            <td>时间范围</td>
            <?php $lower_limit = isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["lower_limit"] : "";?>
            <td>下限时间：<input type="text" name="info[<?php echo FlowCondition::TYPE_TIME;?>][lower_limit]" value="<?php echo $lower_limit;?>" style="width: 70%;" class="selDate" readonly="readonly"/></td>
            <?php $upper_limit = isset($info[FlowCondition::TYPE_TIME]) ? $info[FlowCondition::TYPE_TIME]["upper_limit"] : "";?>
            <td>上限时间：<input type="text" name="info[<?php echo FlowCondition::TYPE_TIME;?>][upper_limit]" value="<?php echo $upper_limit;?>" style="width: 70%;" class="selDate" readonly="readonly"/></td>
        </tr>
       
        <tr class="showCondition_<?= FlowCondition::TYPE_AREA?>">
            <td>部门</td>
            <?php $lower_limit = isset($info[FlowCondition::TYPE_AREA]) ? $info[FlowCondition::TYPE_AREA]["lower_limit"] : "";?>
            <td colspan="2"><div style="width: 100px;float: left;">选择部门：</div><div style="width: 70%;float: left;">
                <?php echo Html::dropDownList('info['.FlowCondition::TYPE_AREA.'][lower_limit]', $lower_limit, $departmentAll, array('prompt' => '全部', "style"=>"width: 80%;"))?>
            </div></td>          
        </tr>
        
        <tr class="showCondition_<?= FlowCondition::TYPE_SUPPLIER?>">
            <td>供应商</td>
            <?php $lower_limit = isset($info[FlowCondition::TYPE_SUPPLIER]) ? $info[FlowCondition::TYPE_SUPPLIER]["lower_limit"] : "";?>
            <td colspan="2"><div style="width: 100px;float: left;">选择供应商：</div><div style="width: 70%;float: left;">
                <?php echo Html::dropDownList('info['.FlowCondition::TYPE_SUPPLIER.'][lower_limit]', $lower_limit, Supplier::getSupplierSelectData(), array('prompt' => '全部', "style"=>"width: 80%;"))?>
            </div></td>
        </tr>
        
        <tr class="showCondition_<?= FlowCondition::TYPE_CATEGORY?>">
            <td>商品分类</td>
            <?php $lower_limit = isset($info[FlowCondition::TYPE_CATEGORY]) ? $info[FlowCondition::TYPE_CATEGORY]["lower_limit"] : "";?>
            <td colspan="2"><div style="width: 100px;float: left;">选择商品类别：</div><div style="width: 70%;float: left;">
                <?php echo Html::dropDownList('info['.FlowCondition::TYPE_CATEGORY.'][lower_limit]', $lower_limit, ProductCategory::getCatrgorySelectData(), array('prompt' => '全部', "style"=>"width: 80%;"))?>
            </div></td>
        </tr>
    </table>
   <div class="buttons">
      <a class="button blue-button" href="javascript:void(0)" save-data="<?= Url::to(['flowconfig/addedit', 'id' => $model->id]) ?>">保存</a> 
      <a class="button blue-button" href="javascript:history.back(-1)">返回</a>
    </div>
    <?php ActiveForm::end(); ?>
    
    
</div>
<?= $this->context->renderPartial('/site/popping') ?>
<?= $this->context->renderPartial('/jquery/js') ?>
<?= $this->context->renderPartial('/jquery/dateInput') ?>

<?php
$ajaxUrl = Url::to(["ajax/ajaxflowlogic"]);
$ajaxDepartmentRoleUrl = Url::to(["ajax/ajaxdepartmentrole"]);
$type = $model->type;
$modelId = $model->id ? $model->id : 0;
$js = <<<JS
    $(".selFlowType").change(function(){
        var flowType = $(this).val();
        var flowName = $(this).find("option:selected").text();
        if(flowType) {
            ajaxFlowLogic(flowType, 1);
            $("input[name='FlowConfig[name]']").val(flowName);
        }
    });
    $(".selDepartment").change(function(){
        var departmentId = $(this).val();
        var type = $(this).attr("i");
        $.get("{$ajaxDepartmentRoleUrl}",{"dId":departmentId, "type":type}, function(result){
            $(".selRole[i='"+type+"']").find("option").remove();
            var html = "<option value=''>请选择</option>";
            $.each(result, function(k,v){
               html += "<option value='"+k+"'>"+v+"</option>" 
            });
            $(".selRole[i='"+type+"']").html(html);
        }, "json");
    });
    function ajaxFlowLogic(flowType, isEdit) {
        $.get("{$ajaxUrl}",{"flowType":flowType}, function(result){
            if(!result.form) {
                alert("系统没有配置流程步骤，默认系统配置文件。");
            }
            $.each(result.result, function(key, val){
                if($(".sel"+key).length > 0) {
                    if(!val){
                        $(".sel"+key + " option").removeAttr("selected");
                    }
                }
                if(isEdit && $(".input"+key).length > 0) {
                    if(val){
                        var name = key=="create" ? "创建" : (key=="verify" ? "审核" : (key=="approval" ? "批准" : "执行"));
                        $(".input"+key).val(name)
                    } else {
                        $(".input"+key).val("");
                    }
                }
                if($("."+key).length > 0) {
                    if(val){
                        $("."+key).removeAttr("disabled");
                    } else {
                        $("."+key).attr("disabled","true");
                    }
                }
            });
            $.each(result.condition, function(k,v){
                if(v){
                    if(!{$modelId}) {
                        $(".showCondition_"+k).find("input").val("");
                        $(".showCondition_"+k).find("select").find("option[value='']").attr("selected", "selected")
                    }
                    $(".showCondition_"+k).show();
                } else {
                    if(k == 1) {
                        $(".showCondition_"+k).find("input[name='info[1][lower_limit]']").val("1");
                        $(".showCondition_"+k).find("input[name='info[1][upper_limit]']").val("9999");
                    } else if(k == 2){
                        $(".showCondition_"+k).find("input[name='info[2][lower_limit]']").val("2016-01-01");
                        $(".showCondition_"+k).find("input[name='info[2][upper_limit]']").val("2020-12-30");
                    }
                    $(".showCondition_"+k).find("select").find("option[value='']").attr("selected", "selected")
                    $(".showCondition_"+k).hide();
                }
            });
        }, "json");
    }  
//    if(!{$modelId}) {
        ajaxFlowLogic({$type}, 0);
//    }
JS;
Yii::$app->getView()->registerJs($js, \yii\web\View::POS_READY, 'addedit');
?>