<?php 
use common\models\Role;
use yii\helpers\Html;
use common\models\Department;
use common\models\Supplier;
use common\models\Product;
?>
<?php foreach ($item as $val) { ?>
<tr class="showWarehouseList">
    <td>
        <?= Html::checkbox("selWarehouse[]", false, ["class" => "selWarehouse", "value" => $val->id, "style"=>"width: 18px;margin: 5px;height: 16px;"])?>
        <?= $val->name;?>
        <?= Html::hiddenInput("warehouse_id[]", "", ["class" => "warehouseId"])?>
    </td>
    <td><?= Html::listBox("check_admin_role[]", "", Role::getAllRoleByStatus(Role::STATUS_OK), ['prompt' => '请选择', "size" => 1, "disabled" =>"disabled", "class" => "wareInput"])?></td>
    <td><?= Html::listBox("check_admin_department[]", "",  Department::getSelectData(-1), ['prompt' => '请选择', "size" => 1, "disabled" =>"disabled", "class" => "wareInput"])?></td>
    <td><?= Html::textInput("check_time[]", "", ["class"=>"selDate wareInput", "disabled" =>"disabled"])?></td>
    <td><?= Html::listBox("supplier_id[]", "",  Supplier::getSupplierSelectData(Supplier::STATUS_OK), ['prompt' => '全部', "size" => 1, "disabled" =>"disabled", "class" => "wareInput"])?></td>
    <td><?= Html::listBox("material_type[]", "", Product::getTypeSelectData(), ['prompt' => '全部', "size" => 1, "disabled" =>"disabled", "class" => "wareInput"])?></td>
</tr>
<?php }?>
