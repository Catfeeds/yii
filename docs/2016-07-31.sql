CREATE TABLE `ProductInvoicingSale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '销存表单号',
  `sn` varchar(50) DEFAULT NULL COMMENT '销存单号',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `total_amount` float(10,2) DEFAULT NULL COMMENT '销存总价',
  `warehouse_id` int(11) DEFAULT NULL COMMENT '仓库ID',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态',
  `create_admin_id` int(11) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物料销存表';

CREATE TABLE `ProductInvoicingSaleInfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoicing_sale_id` int(11) NOT NULL COMMENT '销售ID',
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '库存数量 ',
  `buying_number` int(11) NOT NULL COMMENT '销售数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物料销存物料详情表';

