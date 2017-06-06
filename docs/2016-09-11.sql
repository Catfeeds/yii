ALTER TABLE `CheckPlanning`
DROP COLUMN `department_id`,
DROP COLUMN `check_admin_id`,
ADD COLUMN `product_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '盘点商品名称：默认为空为全部' AFTER `check_type`,
ADD COLUMN `product_cate_id`  int(11) NULL DEFAULT 0 COMMENT '盘点商品分类 0【全部】' AFTER `product_name`,
CHANGE COLUMN `check_type` `is_check_amount`  tinyint(2) NULL DEFAULT 0 COMMENT '是否盘点资金0【否】1【是】' AFTER `supplier_id`;

