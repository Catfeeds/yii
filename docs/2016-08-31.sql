ALTER TABLE `OrderProcurement` ADD `department_id` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `OrderProcurement` ADD `verify_time` DATETIME NULL COMMENT '���ʱ��';
ALTER TABLE `OrderProcurement` ADD `approval_time` DATETIME NULL COMMENT '��׼ʱ��';
