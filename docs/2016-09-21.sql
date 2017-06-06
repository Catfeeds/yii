
ALTER TABLE `WarehouseTransferDep`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '转货说明' AFTER `failCause`,
ADD COLUMN `total_cost`  float(10,2) NULL COMMENT '总成本' AFTER `remark`;

ALTER TABLE `WarehouseCheck`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '转货说明' AFTER `failCause`;
