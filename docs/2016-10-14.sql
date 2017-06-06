ALTER TABLE `WarehouseCheckProduct`
ADD COLUMN `type`  tinyint(2) NULL DEFAULT 1 COMMENT '类型 1：正常 2：例行 3：例外' AFTER `status`;
