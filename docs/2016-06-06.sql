
ALTER TABLE `ProductStock`
ADD COLUMN `supplier_id`  int(11) NOT NULL COMMENT '供应商ID' AFTER `warehouse_id`;

