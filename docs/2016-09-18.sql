ALTER TABLE `WarehouseCheckout`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注' AFTER `failCause`,
ADD COLUMN `total_cost`  float(10,2) NULL COMMENT '总成本' AFTER `remark`;

CREATE TABLE `CommonRemark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_id` int(11) NOT NULL COMMENT '流程ID',
  `flow_type` tinyint(4) NOT NULL COMMENT '流程类型',
  `remark` varchar(255) DEFAULT NULL COMMENT '操作说明',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型 1【审核】2【批准】3【执行】',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通用操作说明表';

ALTER TABLE `WarehouseProcurement`
MODIFY COLUMN `procurement_planning_id`  int(11) NOT NULL COMMENT '采购计划id' AFTER `name`,
MODIFY COLUMN `verify_time`  datetime NULL COMMENT '审核时间' AFTER `verify_admin_id`,
MODIFY COLUMN `approval_time`  datetime NULL COMMENT '批准时间' AFTER `approval_admin_id`,
MODIFY COLUMN `operation_time`  datetime NULL COMMENT '执行时间' AFTER `operation_admin_id`;

ALTER TABLE `WarehouseBuying`
MODIFY COLUMN `verify_time`  datetime NULL COMMENT '审核时间' AFTER `verify_admin_id`,
MODIFY COLUMN `approval_time`  datetime NULL COMMENT '批准时间' AFTER `approval_admin_id`,
MODIFY COLUMN `operation_time`  datetime NULL COMMENT '执行时间' AFTER `operation_admin_id`;

ALTER TABLE `OrderProcurement`
MODIFY COLUMN `operation_time`  datetime NULL AFTER `failCause`;

ALTER TABLE `AbnormalBalance`
ADD COLUMN `sn`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '编号' AFTER `id`;

