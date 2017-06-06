/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50621
Source Host           : 127.0.0.1:3306
Source Database       : wms

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2016-06-05 21:57:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单 ID',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单\n\n最多3级',
  `depth` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `arr_parant` varchar(255) NOT NULL DEFAULT '0' COMMENT '父级链',
  `name` varchar(45) NOT NULL COMMENT '菜单名',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单指向链接\n\n不为空表示为最终菜单\n为空时表示为菜单节点',
  `params` varchar(255) NOT NULL DEFAULT '' COMMENT '地址参数',
  `icon` varchar(120) NOT NULL DEFAULT '' COMMENT '菜单 Icon 样式名',
  `auth` varchar(64) NOT NULL DEFAULT '' COMMENT '所需权限\n\n填最上一级权限',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认开启\n0: 不开启\n1: 开启',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示\n0: 不显示\n1: 显示',
  `sort` bigint(20) unsigned NOT NULL DEFAULT '99' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `is_show` (`is_show`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of menu
-- ----------------------------
replace INTO `menu` VALUES ('1', '0', '1', '0', '查询统计', 'stats/realtime', 'id=1', 'icon-1', '', '0', '1', '0');
replace INTO `menu` VALUES ('2', '0', '1', '0', '销存管理', 'invoicing/realtime', '', 'icon-2', 'invoicing_realtime', '1', '1', '0');
replace INTO `menu` VALUES ('3', '0', '1', '0', '业务操作', 'wplanning/index', '', 'icon-3', 'warehouseplanning', '1', '1', '0');
replace INTO `menu` VALUES ('4', '0', '1', '0', '业务设置', 'flowconfig/index', '', 'icon-4', 'flowconfig_index', '1', '1', '0');
replace INTO `menu` VALUES ('5', '0', '1', '0', '部门基础数据', 'department/index', '', 'icon-5', '', '0', '1', '0');
replace INTO `menu` VALUES ('6', '0', '1', '0', '业务基础数据', 'supplier/index', '', 'icon-6', '', '0', '1', '0');
replace INTO `menu` VALUES ('7', '0', '1', '0', '系统基础数据', 'system/logo', '', 'icon-7', '', '1', '1', '0');
replace INTO `menu` VALUES ('8', '1', '2', '0,1', '实时库存统计', 'stats/realtime', '', '', 'stats_realtime', '1', '1', '0');
replace INTO `menu` VALUES ('9', '1', '2', '0,1', '历史表单统计', 'stats/index', '', '', '', '0', '1', '0');
replace INTO `menu` VALUES ('13', '6', '2', '0,6', '供应商设置', 'supplier/index', '', '', 'supplier_index', '1', '1', '1');
replace INTO `menu` VALUES ('14', '7', '2', '0,7', 'logo设置', 'system/logo', '', '', 'system_logo', '1', '1', '0');
replace INTO `menu` VALUES ('15', '7', '2', '0,7', '公司名称', 'system/company', '', '', 'system_company', '1', '1', '0');
replace INTO `menu` VALUES ('16', '7', '2', '0,7', '业务计算机设置', 'computer/index', '', '', ' computer_index', '1', '1', '0');
replace INTO `menu` VALUES ('17', '5', '2', '0,5', '部门管理', 'department/index', '', '', 'department_index', '1', '1', '0');
replace INTO `menu` VALUES ('18', '5', '2', '0,5', '角色管理', 'role/index', '', '', 'role_index', '1', '1', '0');
replace INTO `menu` VALUES ('19', '5', '2', '0,5', '员工管理', 'admin/index', '', '', 'admin_index', '1', '1', '0');
replace INTO `menu` VALUES ('20', '7', '2', '0,7', '事件日志', 'adminlog/index', '', '', 'adminlog_index', '1', '1', '0');
replace INTO `menu` VALUES ('21', '1', '2', '0,1', '业务表单统计查询', 'stats/purchase', '', '', 'stats_bussiness', '1', '1', '0');
replace INTO `menu` VALUES ('22', '3', '2', '0,3', '采购计划', 'wplanning/index', '', '', 'wplanning_index', '1', '1', '0');
replace INTO `menu` VALUES ('23', '3', '2', '0,3', '采购下定', 'wprocurement/index', '', '', 'wprocurement', '1', '1', '0');
replace INTO `menu` VALUES ('24', '3', '2', '0,3', '库存管理', 'pstock/index', '', '', 'pstock_index', '1', '1', '0');
replace INTO `menu` VALUES ('25', '24', '3', '0,3,24', '库存管理', 'pstock/index', '', '', 'pstock_index', '1', '1', '0');
replace INTO `menu` VALUES ('26', '7', '2', '0,7', '数据库备份及恢复', 'import/index', '', '', 'database', '1', '1', '0');
replace INTO `menu` VALUES ('27', '26', '3', '0,7,26', '还原数据', 'import/index', '', '', 'import_ndex', '1', '1', '0');
replace INTO `menu` VALUES ('28', '26', '3', '0,7,26', '备份数据', 'export/index', '', '', 'export_index', '1', '1', '0');
replace INTO `menu` VALUES ('29', '6', '2', '0,6', '物料管理', 'product/index', '', '', 'product_index', '1', '1', '3');
replace INTO `menu` VALUES ('30', '6', '2', '0,6', '供应商出品列表', 'supplierproduct/index', '', '', 'supplierproduct_index', '1', '1', '2');
replace INTO `menu` VALUES ('31', '4', '2', '0,4', '业务流程列表', 'flowconfig/index', '', '', 'flowconfig_index', '1', '1', '1');
replace INTO `menu` VALUES ('32', '4', '2', '0,4', '业务流程条件列表', 'flowcondition/index', '', '', 'flowcondition_index', '1', '1', '0');
replace INTO `menu` VALUES ('33', '6', '2', '0,6', '仓库分区', 'warehouse/index', '', '', 'warehouse_index', '1', '1', '0');
replace INTO `menu` VALUES ('34', '6', '2', '0,6', '地区列表', 'area/index', '', '', 'area_index', '1', '1', '0');
replace INTO `menu` VALUES ('35', '24', '3', '0,3,24', '库存入库记录', 'wbuying/index', '', '', 'wbuying_index', '1', '1', '0');
replace INTO `menu` VALUES ('36', '24', '3', '0,3,24', '库存盘点记录', 'wcheck/index', '', '', 'wcheck_index', '1', '1', '0');
replace INTO `menu` VALUES ('37', '24', '3', '0,3,24', '库存出库记录', 'wcheckout/index', '', '', 'wcheckout_index', '1', '1', '0');
replace INTO `menu` VALUES ('38', '24', '3', '0,3,24', '库存调仓记录', 'wtransfer/index', '', '', 'wtransfer_index', '1', '1', '0');
replace INTO `menu` VALUES ('39', '24', '3', '0,3,24', '库存转货记录', 'wtransferdep/index', '', '', 'wtransferdep_index', '1', '1', '0');
replace INTO `menu` VALUES ('40', '24', '3', '0,3,24', '库存耗损记录', 'wwastage/index', '', '', 'wwastage_index', '1', '1', '0');
replace INTO `menu` VALUES ('41', '24', '3', '0,3,24', '库存退货记录', 'wmaterial/index', '', '', 'wmaterial_index', '1', '1', '0');
replace INTO `menu` VALUES ('42', '24', '3', '0,3,24', '库存出入库日志', 'wgateway/index', '', '', 'wgateway_index', '1', '1', '0');
replace INTO `menu` VALUES ('43', '4', '2', '0,4', '订单模版管理', 'ordertemplate/index', '', '', 'ordertemplate_index', '1', '1', '0');
replace INTO `menu` VALUES ('44', '7', '2', '0,7', '开业清库', 'system/default', '', '', 'system_default', '1', '1', '0');
replace INTO `menu` VALUES ('45', '7', '2', '0,7', '配置数据', 'config/index', '', '', 'system_config', '1', '1', '0');
replace INTO `menu` VALUES ('46', '7', '2', '0,7', '管理员权限', 'system/auth', '', '', 'system_auth', '1', '1', '0');
replace INTO `menu` VALUES ('47', '21', '3', '0,1,21', '采购入库报表', 'stats/purchase', '', '', 'stats_purchase', '1', '1', '0');
replace INTO `menu` VALUES ('48', '21', '3', '0,1,21', '物料出入报表', 'stats/product', '', '', 'stats_product', '1', '1', '0');
replace INTO `menu` VALUES ('49', '21', '3', '0,1,21', '供应商供货', 'stats/supply', '', '', 'stats_supply', '1', '1', '0');
replace INTO `menu` VALUES ('50', '21', '3', '0,1,21', '供应商结算', 'stats/settlement', '', '', 'stats_settlement', '1', '1', '0');
replace INTO `menu` VALUES ('52', '21', '3', '0,1,21', '毛利', 'stats/grossProfit', '', '', 'stats_grossProfit', '1', '1', '0');
replace INTO `menu` VALUES ('53', '21', '3', '0,1,21', '利润报表', 'stats/profit', '', '', 'stats_profit', '1', '1', '0');
replace INTO `menu` VALUES ('54', '2', '2', '0,2', '实时库存状态管理', 'invoicing/realtime', '', '', 'invoicing_realtime', '1', '1', '0');
replace INTO `menu` VALUES ('55', '2', '2', '0,2', '销存商品管理', 'invoicing/product', '', '', 'invoicing_product', '1', '1', '0');
replace INTO `menu` VALUES ('56', '2', '2', '0,2', '销存库存管理', 'invoicing/stock', '', '', 'invoicing_stock', '1', '1', '0');
replace INTO `menu` VALUES ('57', '2', '2', '0,2', '部门盘点管理', 'invoicing/check', '', '', 'invoicing_check', '1', '1', '0');
replace INTO `menu` VALUES ('58', '3', '2', '0,3', '资金流水', 'fund/index', '', '', 'fund_index', '1', '1', '0');
replace INTO `menu` VALUES ('59', '58', '3', '0,3,58', '资金流水记录', 'fund/index', '', '', 'fund_index', '1', '1', '0');
replace INTO `menu` VALUES ('60', '58', '3', '0,3,58', '采购下定支付', 'oprocurement/index', '', '', 'oprocurement_index', '1', '1', '0');