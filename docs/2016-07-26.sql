DROP TABLE IF EXISTS  `CheckPlanning`;
CREATE TABLE `CheckPlanning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '表单名',
  `sn` varchar(50) NOT NULL COMMENT '单号',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `create_admin_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建人',
  `status` tinyint(2) NOT NULL COMMENT '状态 1：盘点中 2：数据校验 3：盘点完成',
  `check_admin_id` int(11) NOT NULL COMMENT '盘点员',
  `check_time` date NOT NULL COMMENT '盘点时间',
  `supplier_id` int(11) DEFAULT NULL COMMENT '盘点供应商ID',
  `check_type` tinyint(2) DEFAULT NULL COMMENT '盘点类型 1【物料】2【资金】',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='盘点计划表';

DROP TABLE IF EXISTS  `CheckPlanningCondition`;

CREATE TABLE `DepartmentCheck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_planning_id` int(11) NOT NULL COMMENT '盘点计划ID',
  `name` varchar(100) NOT NULL COMMENT '盘点名称',
  `check_sn` varchar(50) NOT NULL COMMENT '盘点单号',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `warehouse_id` int(11) NOT NULL COMMENT '盘点仓库ID',
  `check_admin_id` int(11) NOT NULL COMMENT '盘点员',
  `check_time` date NOT NULL COMMENT '盘点类型',
  `status` tinyint(2) NOT NULL COMMENT '盘点状态 0【待盘点】1【已盘点】',
  `check_type` tinyint(2) NOT NULL COMMENT '盘点类型 1【商品】2【资产】',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门盘点表';

CREATE TABLE `DepartmentCheckAmount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_planning_id` int(11) NOT NULL COMMENT '盘点计划ID',
  `warehouse_total_amount` float(10,2) NOT NULL COMMENT '仓库余额',
  `check_total_amount` float(10,2) NOT NULL COMMENT '盘点余额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门盘点金额表';

CREATE TABLE `DepartmentCheckProduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_check_id` int(11) NOT NULL COMMENT '部门盘点ID',
  `product_id` int(11) NOT NULL COMMENT '物料ID',
  `product_name` varchar(100) NOT NULL COMMENT '物料名称',
  `material_type` tinyint(2) DEFAULT NULL COMMENT '物料类型',
  `spec` varchar(40) DEFAULT NULL COMMENT '物料规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '物料单位',
  `purchase_price` float(10,2) DEFAULT NULL COMMENT '物料进货价',
  `sale_price` float(10,2) DEFAULT NULL COMMENT '物料销售价',
  `stock` int(11) NOT NULL COMMENT '物料库存',
  `check_num` int(11) DEFAULT NULL COMMENT '盘点数量',
  `total_amount` float(10,2) DEFAULT NULL COMMENT '盘点价格',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商物料ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门盘点物料表';

