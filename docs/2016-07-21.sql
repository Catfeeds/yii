ALTER TABLE `BusinessAll`
MODIFY COLUMN `operation_time`  datetime NULL COMMENT '完成时间' AFTER `operation_admin_id`;

ALTER TABLE `OrderProcurement`
ADD COLUMN `department_id`  int(11) NULL COMMENT '部门ID' AFTER `failCause`,
ADD COLUMN `verify_time`  datetime NULL COMMENT '审核时间' AFTER `department_id`,
ADD COLUMN `approval_time`  datetime NULL COMMENT '批准时间' AFTER `verify_time`,
ADD COLUMN `operation_time`  datetime NULL COMMENT '执行时间' AFTER `approval_time`;

ALTER TABLE `WarehouseMaterialReturn`
CHANGE COLUMN `Common` `common`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '退货原因' AFTER `failCause`;

DROP TABLE IF EXISTS `WarehouseBack`;
CREATE TABLE `WarehouseBack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `receive_warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '接受仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退仓表';

DROP TABLE IF EXISTS `WarehouseBackProduct`;
CREATE TABLE `WarehouseBackProduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `back_id` int(11) NOT NULL COMMENT '退仓订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退仓物料表';

