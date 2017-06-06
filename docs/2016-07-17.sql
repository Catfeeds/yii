ALTER TABLE `BusinessAll`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `business_type`,
ADD COLUMN `warehouse_id`  int(11) NULL COMMENT '仓库ID' AFTER `department_id`;

ALTER TABLE `WarehouseBuying`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `warehouse_id`;

ALTER TABLE `WarehouseCheckout`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `warehouse_id`;

ALTER TABLE `WarehousePlanning`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `warehouse_id`;

ALTER TABLE `WarehouseProcurement`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `warehouse_id`;

