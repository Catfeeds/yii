ALTER TABLE `DepartmentCheck`
ADD COLUMN `check_status`  tinyint(2) NULL DEFAULT 0 COMMENT '盘点状态' AFTER `check_type`;

ALTER TABLE `WarehouseCheck`
ADD COLUMN `check_department_id`  int(11) NULL COMMENT '盘点部门ID' AFTER `check_planning_id`;

ALTER TABLE `DepartmentCheckProduct`
ADD COLUMN `barcode`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '物料物品编号' AFTER `product_name`;

ALTER TABLE `DepartmentCheckAmount`
ADD COLUMN `department_check_id`  int(11) NOT NULL COMMENT '部门盘点ID' AFTER `id`;

ALTER TABLE `DepartmentCheckAmount`
DROP COLUMN `check_planning_id`;

