DROP TABLE `Combination`;
CREATE TABLE `Combination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `payment` tinyint(2) NOT NULL COMMENT '支付方式',
  `deposit` float(10,2) NOT NULL COMMENT '定金',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `approval_time` date NOT NULL COMMENT '批准时间',
  `operation_time` date NOT NULL COMMENT '验收时间',
  `operation_cause` varchar(255) DEFAULT NULL COMMENT '验收说明',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `common` varchar(255) DEFAULT NULL COMMENT '用途说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合物料模板';

CREATE TABLE `OrderProcurement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `procurement_id` int(11) NOT NULL COMMENT '采购计划id',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `planning_date` date NOT NULL COMMENT '计划下单时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 预付 2定金 3后付',
  `deposit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '定金',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `payment_term` date DEFAULT NULL COMMENT '付款期',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `operation_admin_id` int(11) NOT NULL COMMENT '下单操作人',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL,
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='采购下定支付表';

CREATE TABLE `OrderProcurementProduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `order_procurement_id` int(11) NOT NULL COMMENT '采购下定ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '采购数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` varchar(20) NOT NULL COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='采购下定支付物料表';

ALTER TABLE `WarehouseProcurement`
ADD COLUMN `order_sn`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号' AFTER `sn`;

ALTER TABLE `WarehouseBuying`
ADD COLUMN `order_sn`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号' AFTER `sn`;

ALTER TABLE `OrderProcurement`
ADD COLUMN `order_sn`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号' AFTER `sn`;
