ALTER TABLE `WarehousePlanning` CHANGE `verify_time` `verify_time` DATETIME NULL COMMENT '审核时间';

ALTER TABLE `WarehousePlanning` CHANGE `approval_time` `approval_time` DATETIME NULL COMMENT '批准时间';

ALTER TABLE `WarehousePlanning` CHANGE `operation_time` `operation_time` DATETIME NULL COMMENT '执行时间';


ALTER TABLE `WarehousePlanning` CHANGE `payment` `payment` TINYINT( 2 ) NULL DEFAULT '3' COMMENT '预定付款方式';