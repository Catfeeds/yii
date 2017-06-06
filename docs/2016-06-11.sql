ALTER TABLE `WarehouseCheck`
ADD COLUMN `check_planning_id`  int(11) NULL COMMENT '盘点计划ID' AFTER `id`;

ALTER TABLE `Product`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseBuyingProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseCheckoutProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseCheckProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseMaterialReturnProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehousePlanningProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseProcurementProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseSaleProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseTransferdepProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseTransferProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseWastageProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `OrderProcurementProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `OrderTemplateProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `PaymentOrderProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `SupplierProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `VerificationProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseBackProduct`
MODIFY COLUMN `material_type`  tinyint(2) NOT NULL DEFAULT 1 COMMENT '物料类别' AFTER `unit`;

ALTER TABLE `WarehouseMaterialReturn`
ADD COLUMN `buying_id`  int(11) NULL COMMENT '入库ID' AFTER `sn`,
MODIFY COLUMN `verify_admin_id`  int(11) NULL DEFAULT 0 COMMENT '审核人' AFTER `create_admin_id`,
MODIFY COLUMN `verify_time`  datetime NULL COMMENT '审核时间' AFTER `verify_admin_id`,
MODIFY COLUMN `approval_admin_id`  int(11) NULL DEFAULT 0 COMMENT '批准人' AFTER `verify_time`,
MODIFY COLUMN `approval_time`  datetime NULL COMMENT '批准时间' AFTER `approval_admin_id`,
ADD COLUMN `supplier_id`  int(11) NOT NULL COMMENT '供应商ID' AFTER `warehouse_id`,
ADD COLUMN `operation_admin_id`  int(11) NULL COMMENT ' 执行人' AFTER `create_time`,
ADD COLUMN `operation_time`  datetime NULL COMMENT '执行时间' AFTER `operation_admin_id`,
ADD COLUMN `config_id`  int(11) NULL COMMENT '流程ID' AFTER `operation_time`,
ADD COLUMN `failCause`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '驳回理由' AFTER `config_id`,
ADD COLUMN `Common`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '退货原因' AFTER `failCause`;

ALTER TABLE `WarehousePlanning`
ADD COLUMN `verify_time`  datetime NOT NULL COMMENT '审核时间' AFTER `verify_admin_id`,
ADD COLUMN `approval_time`  datetime NOT NULL COMMENT '批准时间' AFTER `approval_admin_id`,
ADD COLUMN `operation_time`  datetime NOT NULL COMMENT '执行时间' AFTER `operation_admin_id`;

ALTER TABLE `WarehouseMaterialreturn`
ADD COLUMN `department_id`  int(11) NOT NULL COMMENT '部门ID' AFTER `supplier_id`;

