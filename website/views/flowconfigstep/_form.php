<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\FlowConfigStep;
use libs\common\Flow;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeDropDownList($model, 'config_sn', Flow::getTypeSelectData(), ['prompt' => '请选择', 'class' => 'selFlow']) ?></td>
    <?= Html::activeHiddenInput($model, 'name', ["class" => "selFlowName"]); ?>
    <td><?= Html::activeDropDownList($model, 'create_step', FlowConfigStep::getStepSelectData(), ['prompt' => '请选择', 'class' => 'create']) ?></td>
    <td><?= Html::activeDropDownList($model, 'verify_step', FlowConfigStep::getStepSelectData(), ['prompt' => '请选择', 'class' => 'verify']) ?></td>
    <td><?= Html::activeDropDownList($model, 'approval_step', FlowConfigStep::getStepSelectData(), ['prompt' => '请选择', 'class' => 'approval']) ?></td>
    <td><?= Html::activeDropDownList($model, 'operation_step', FlowConfigStep::getStepSelectData(), ['prompt' => '请选择', 'class' => 'operation']) ?></td>

   <td><?= Html::activeDropDownList($model, 'business_end_table', Flow::getTypeSelectData(), ['prompt' => '请选择']) ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowconfigstep/create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['flowconfigstep/update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>
<script type="text/javascript">
    $(".selFlow").change(function (){
        var flowName = $(this).find("option:selected").text();
        $(".selFlowName").val(flowName);
        <?php foreach (Flow::getOperationType() as $type) { ?>
            if($(this).val() == <?= $type;?>) {
                $(".operation").find("option[value=1]").attr("selected", "selected");
                $(".operation").attr("onfocus", "this.defaultIndex=this.selectedIndex;").attr("onchange", "this.selectedIndex=this.defaultIndex;");
//                $(".operation").attr("disabled","true");
                return true;
            } else {
                $(".operation").find("option[value='']").attr("selected", "selected");
                $(".operation").removeAttr("onfocus").removeAttr("onchange");
            }
        <?php } ?>
    });
    $(".verify").change(function(){
        if($(this).val() == 0 && $.isNumeric($(this).val())) {
            $(this).parent("td").next("td").find(".approval").find("option[value=0]").attr("selected", "selected");
            $(this).parent("td").next("td").find(".approval").attr("onfocus", "this.defaultIndex=this.selectedIndex;").attr("onchange", "this.selectedIndex=this.defaultIndex;");
//            $(".approval").attr("disabled","true");
        } else {
            $(this).parent("td").next("td").find(".approval").find("option[value='']").attr("selected", "selected");
            $(this).parent("td").next("td").find(".approval").removeAttr("onfocus").removeAttr("onchange");
//            $(".approval").removeAttr("disabled");
        }
    });
</script>