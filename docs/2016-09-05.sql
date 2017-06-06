/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  deanwang
 * Created: 2016-9-5
 */
ALTER TABLE `WarehouseTransfer`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '调仓说明' AFTER `failCause`;

ALTER TABLE `WarehouseWastage`
ADD COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '报损原因' AFTER `failCause`;



