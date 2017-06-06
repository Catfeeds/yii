
ALTER TABLE `WarehouseProcurement`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `failCause`;

ALTER TABLE `WarehouseBuying`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `failCause`;

ALTER TABLE `ProductStock`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `supplier_id`;

ALTER TABLE `WarehouseCheckoutProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseTransferProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseTransferDepProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseWastageProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseSaleProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseGateway`
ADD COLUMN `product_type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `comment`;

ALTER TABLE `WarehouseMaterialReturnProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;

ALTER TABLE `WarehouseBack`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '退仓说明' AFTER `failCause`;

ALTER TABLE `WarehouseBackProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;