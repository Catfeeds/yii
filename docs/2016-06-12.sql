ALTER TABLE `Product` CHANGE `verfiy_time` `verify_time` DATETIME NULL DEFAULT NULL COMMENT '审核时间';
ALTER TABLE `Product` CHANGE `verfiy_admin_id` `verify_admin_id` INT( 11 ) NULL DEFAULT NULL COMMENT '审核人ID';