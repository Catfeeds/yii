ALTER TABLE `WarehousePlanning`
ADD COLUMN `payment`  tinyint(2) NULL COMMENT '预定付款方式' AFTER `supplier_id`,
ADD COLUMN `deposit`  float(11,0) NULL COMMENT '定金' AFTER `payment`;

ALTER TABLE `WarehousePlanning`
ADD COLUMN `payment_term`  date NULL COMMENT '付款时间' AFTER `deposit`,
AUTO_INCREMENT=20;

ALTER TABLE `FlowConfig`
MODIFY COLUMN `operation_role_id`  int(11) NULL COMMENT '操作角色' AFTER `type`,
MODIFY COLUMN `operation_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL   COMMENT '执行操作名' AFTER `operation_role_id`,
MODIFY COLUMN `operation_department_id`  int(11) NULL   COMMENT '操作部门' AFTER `operation_name`,
MODIFY COLUMN `verify_role_id`  int(11) NULL COMMENT '审核角色' AFTER `operation_department_id`,
MODIFY COLUMN `verify_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL   COMMENT '审核操作名' AFTER `verify_role_id`,
MODIFY COLUMN `verify_department_id`  int(11) NULL   COMMENT '审核部门' AFTER `verify_name`,
MODIFY COLUMN `approval_role_id`  int(11) NULL COMMENT '批准角色' AFTER `verify_department_id`,
MODIFY COLUMN `approval_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL   COMMENT '批准操作名' AFTER `approval_role_id`,
MODIFY COLUMN `approval_department_id`  int(11) NULL   COMMENT '批准部门' AFTER `approval_name`;




