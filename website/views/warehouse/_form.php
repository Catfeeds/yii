<?php
use yii\helpers\Url;
use yii\helpers\Html;
use libs\LinkageSelect;
use common\models\Area;
use app_web\assets\AppAsset;
use yii\web\JqueryAsset;
use common\models\Department;
use common\models\Admin;
?>
<tr id="quick-form">
    <td></td>
    <td><?= Html::activeTextInput($model, 'name', [ "onkeyup" => "javascript:validateValue(this)", "maxlength" => 12, 'class' => "verifySpecial"]) ?></td>
    <td><?= Html::activeDropDownList($model, 'type', $model::getTypeSelectData()) ?></td>
    <td><?= Html::activeTextInput($model, 'num', ["onkeyup" => "javascript:validateValue(this)",'style' => 'width:40%', "maxlength" => 20, 'class' => "verifySpecial"]) ?></td>
    <?php if($model->getIsNewRecord()){ ?>
    <td><?php echo LinkageSelect::widget([
            'model' => $model,
            'attribute' => 'area_id',
            'data' => Area::getAreaSelectData(0, Area::STATUS_OK),
            'empty' => true,
            'ajaxUrl' => Url::to(['area/ajaxgetlist']),
            'ajaxParams' => ["nextName" => "jqscript:$(this).attr('nextName')", "childName"=> "jqscript:$(this).attr('childName')"],
            'emptyValue' => "-1",
            'emptyText' => '请选择',
            'htmlOptions' => ["name" => "provinceId", 'nextName' => 'cityId', "childName" => "Warehouse[area_id]"]
    ]);?></td>
    <?php } else { ?>
    <td><?php $areaList = Area::getParentIdsById($model->area_id);
        echo LinkageSelect::widget([
           'data' => array(
			array(
				'name' => "provinceId",		
				'value' => $areaList["provinceId"],
				'data' => Area::getAreaSelectData(0, Area::STATUS_OK),
				'htmlOptions' => ["nextName" => "cityId", "childName" => "Warehouse[area_id]"]
			),
			array(
				'name' => 'cityId',
				'value' => $areaList["cityId"],
				'data' => Area::getAreaSelectData($areaList["provinceId"], Area::STATUS_OK),
				'htmlOptions' => ["nextName" => "Warehouse[area_id]", "childName" => ""]
			),
			array(
				'name' => 'Warehouse[area_id]',		
				'value' => $areaList["areaId"],
				'data' =>  Area::getAreaSelectData($areaList["cityId"], Area::STATUS_OK),
				'htmlOptions' => []
			),
		), 
		'empty' => true,
		'ajaxUrl' => Url::to(['area/ajaxgetlist']),
		'emptyValue' => '-1',
		'emptyText' => '请选择',
		'ajaxParams' => ["nextName" => "jqscript:$(this).attr('nextName')", "childName"=> "jqscript:$(this).attr('childName')"],
                'htmlOptions' => ["class" => "provinceId"]
        ]);
    ?></td>
    <?php } ?>
    <td><?= Html::activeDropDownList($model, 'is_sale', $model::getSaleSelectData(), ['prompt' => '请选择']) ?></td>
    <?php // $departmentAll = Admin::checkSupperFlowAdmin() ? Department::getAllDatas() : [Admin::getDepId() => Department::getNameById(Admin::getDepId())];?>
    <?php $departmentAll = Department::getAllDatas();?>
    <td><?= Html::activeDropDownList($model, 'department_id', $departmentAll, ['prompt' => '请选择']) ?></td>
    <td><?= Html::activeDropDownList($model, 'status', $model::getStatusSelectData()) ?></td>
    <td>
        <?php if($model->getIsNewRecord()){ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['create']) ?>">保存</a> |
        <?php }else{ ?>
        <a class="quick-form-button" href="javascript:void(0)" save-data="<?= Url::to(['update', 'id' => $model->id]) ?>">保存</a> |
        <?php } ?>
        <a class="quick-form-reset" href="javascript:void(0)" onclick="$(this).closest('tr').prev('tr').show(); $(this).closest('tr').remove();">取消</a>
    </td>
</tr>

<script type="text/javascript">
    $(".verifySpecial").attr("onkeyup","javascript:validateValue(this)");
    $(".verifySpecial").blur(function(){
        validateValue(this);
    });
</script>