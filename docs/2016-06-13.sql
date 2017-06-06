ALTER TABLE `FlowConfig`
ADD COLUMN `create_role_id`  int(11) NULL COMMENT '创建角色' AFTER `status`,
ADD COLUMN `create_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '创建名' AFTER `create_role_id`,
ADD COLUMN `create_department_id`  int(11) NULL COMMENT '创建部门' AFTER `create_name`;

CREATE TABLE `FlowConfigStep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '流程类名称',
  `create_step` tinyint(2) NOT NULL COMMENT '创建步骤 0【无】 1【有】',
  `verify_step` tinyint(2) NOT NULL COMMENT '审核步骤 0【无】 1【有】',
  `approval_step` tinyint(2) NOT NULL COMMENT '批准步骤 0【无】 1【有】',
  `operation_step` tinyint(2) NOT NULL COMMENT '执行步骤 0【无】 1【有】',
  `business_begin_table` varchar(100) NOT NULL COMMENT '业务操作表单',
  `business_end_table` varchar(100) DEFAULT NULL COMMENT '业务终止表单',
  `config_sn` tinyint(2) NOT NULL COMMENT '流程类标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='业务流程类步骤表';



