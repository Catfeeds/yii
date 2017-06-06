TRUNCATE TABLE `FlowConfigStep`;
ALTER TABLE `FlowConfigStep`
MODIFY COLUMN `business_begin_table`  int(11) NULL DEFAULT 0 COMMENT '业务操作表单' AFTER `operation_step`,
MODIFY COLUMN `business_end_table`  int(11) NULL DEFAULT 0 COMMENT '业务终止表单' AFTER `business_begin_table`;

ALTER TABLE `OrderProcurement`
ADD COLUMN `pay_state`  tinyint(2) NULL DEFAULT 0 COMMENT '支付状态 0【未支付】1【定金支付】2【全额支付】' AFTER `department_id`;

ALTER TABLE `OrderProcurement`
ADD COLUMN `pay_deposit_time`  datetime NULL COMMENT '部门支付时间' AFTER `pay_state`,
ADD COLUMN `pay_all_time`  datetime NULL COMMENT '全部支付时间' AFTER `pay_deposit_time`;

ALTER TABLE `WarehouseMaterialReturn`
ADD COLUMN `planning_date`  datetime NULL COMMENT '退货时间' AFTER `common`,
ADD COLUMN `payment_term`  datetime NULL COMMENT '退款时间' AFTER `planning_date`;



CREATE TABLE `OrderMaterialReturn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `procurement_id` int(11) NOT NULL COMMENT '退货id',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `order_sn` varchar(40) NOT NULL COMMENT '订单编号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `planning_date` date NOT NULL COMMENT '计划退货时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 预付 2定金 3后付',
  `deposit` float(10,2) DEFAULT '0.00' COMMENT '定金',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `payment_term` date DEFAULT NULL COMMENT '计划退款时间',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `operation_admin_id` int(11) NOT NULL COMMENT '下单操作人',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL,
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  `remark` VARCHAR(255) DEFAULT NULL COMMENT '退货原因',
  `operation_time` datetime DEFAULT NULL,
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `pay_state` tinyint(2) DEFAULT '0' COMMENT '支付状态 0【未支付】1【定金支付】2【全额支付】',
  `pay_deposit_time` datetime DEFAULT NULL COMMENT '部分支付时间',
  `pay_all_time` datetime DEFAULT NULL COMMENT '全部支付时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退货退款表';

CREATE TABLE `OrderMaterialReturnProduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `order_procurement_id` int(11) NOT NULL COMMENT '退货退款ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '退货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  `type` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '类型 1：正常 2：例行 3：例外',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退货退款物料表';

