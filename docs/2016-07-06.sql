ALTER TABLE `WarehouseProcurement` CHANGE `deposit` `deposit` FLOAT( 10, 2 ) NULL DEFAULT '0.00' COMMENT '定金';

ALTER TABLE  WarehouseBuying CHANGE `deposit` `deposit` FLOAT(10,2) NULL DEFAULT '0.00' COMMENT '定金';

ALTER TABLE `OrderProcurement` CHANGE `deposit` `deposit` FLOAT(10,2) NULL DEFAULT '0.00' COMMENT '定金';

ALTER TABLE `Department` CHANGE `parent_id` `parent_id` INT( 11 ) NULL DEFAULT '0' COMMENT '上级部门ID';


ALTER TABLE `OrderProcurement` ADD `operation_time` DATETIME NOT NULL ;