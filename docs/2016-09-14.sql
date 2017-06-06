ALTER TABLE `ProductInvoicingSale`
ADD COLUMN `sale_amount`  float(10,2) NULL COMMENT '应销金额统计' AFTER `create_time`,
ADD COLUMN `check_sale_amount`  float(10,2) NULL COMMENT '实际销售金额统计' AFTER `sale_amount`,
ADD COLUMN `profit_loss_cause`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '损益原因' AFTER `check_sale_amount`,
ADD COLUMN `last_invoic_amount`  float(10,2) NULL COMMENT '上次结存余额' AFTER `profit_loss_cause`,
ADD COLUMN `predict_invoic_amount`  float(10,2) NULL COMMENT '预计结存余额' AFTER `last_invoic_amount`,
ADD COLUMN `paid_amount`  float(10,2) NULL COMMENT '上缴金额' AFTER `predict_invoic_amount`;


