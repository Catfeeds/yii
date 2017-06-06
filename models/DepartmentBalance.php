<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "DepartmentBalance".
 *
 * @property integer $id
 * @property integer $department_id
 * @property double $balance
 * @property double $income_amount
 * @property double $expenses_amount
 */
class DepartmentBalance extends namespace\base\DepartmentBalance
{
    public static function getBalanceByDepartmentId($departmentId) {
        $item = DepartmentBalance::findOne(["department_id" => $departmentId]);
        return $item ? $item->balance : 0;
    }
}

