CREATE TABLE `WarehouseSale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='仓库销售表';

CREATE TABLE `WarehouseSaleProduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `sale_id` int(11) NOT NULL COMMENT '销售ID',
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
  `material_type` varchar(20) NOT NULL COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='仓库销售物料表';

ALTER TABLE `WarehouseBuying`
ADD COLUMN `verify_time`  datetime NOT NULL COMMENT '审核时间' AFTER `verify_admin_id`,
ADD COLUMN `approval_time`  datetime NOT NULL COMMENT '批准时间' AFTER `approval_admin_id`,
ADD COLUMN `operation_time`  datetime NOT NULL COMMENT '执行时间' AFTER `operation_admin_id`;

ALTER TABLE `WarehouseProcurement`
ADD COLUMN `verify_time`  datetime NOT NULL COMMENT '审核时间' AFTER `verify_admin_id`,
ADD COLUMN `approval_time`  datetime NOT NULL COMMENT '批准时间' AFTER `approval_admin_id`,
ADD COLUMN `operation_time`  datetime NOT NULL COMMENT '执行时间' AFTER `operation_admin_id`;

DROP TABLE `DepartmentBalanceLog`;
CREATE TABLE `DepartmentBalanceLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name`  varchar(255) DEFAULT NULL COMMENT '名称',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `business_id` int(11) NOT NULL COMMENT '业务ID',
  `business_type` tinyint(2) NOT NULL COMMENT '业务类型 对应表名',
  `balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `current_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前金额',
  `mod` tinyint(1) NOT NULL DEFAULT '1' COMMENT '进出：1 进 2出',
  `content` varchar(1000) NOT NULL COMMENT '操作内容',
  `status` tinyint(1) NOT NULL COMMENT '0 无效 1有效 99删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_admin_id` int(11) NOT NULL COMMENT '创建用户ID',
  `verify_admin_id` int(11) DEFAULT NULL COMMENT '审核用户ID',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT NULL COMMENT '批准用户ID',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) DEFAULT NULL COMMENT '完成用户ID',
  `operation_time` datetime DEFAULT NULL COMMENT '完成时间',
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资金流水日志表';

