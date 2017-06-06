DROP TABLE `AbnormalBalance`;
CREATE TABLE `AbnormalBalance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `current_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前金额',
  `mod` tinyint(1) NOT NULL DEFAULT '1' COMMENT '进出：1 进 2出',
  `content` varchar(1000) NOT NULL COMMENT '变动内容',
  `status` tinyint(1) NOT NULL COMMENT '状态',
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='非常态资金变化业务表';

CREATE TABLE `CheckPlanning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '表单名',
  `sn` varchar(50) NOT NULL COMMENT '单号',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `create_admin_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建人',
  `status` tinyint(2) NOT NULL COMMENT '状态 1：盘点中 2：数据校验 3：盘点完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='盘点计划表';

CREATE TABLE `CheckPlanningCondition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_planning_id` int(11) NOT NULL COMMENT '盘点计划表',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `check_admin_id` int(11) NOT NULL COMMENT '盘点人',
  `check_time` datetime NOT NULL COMMENT '盘点时间',
  `supplier_id` int(11) DEFAULT '0' COMMENT '供应商ID 0为全部',
  `material_type` tinyint(2) DEFAULT '0' COMMENT '物料类型 0：全部',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态 1：盘点中 2：数据校验 3：盘点完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='盘点计划物料条件表';

