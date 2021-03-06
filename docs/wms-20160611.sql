/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50621
Source Host           : 127.0.0.1:3306
Source Database       : wms

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2016-06-11 21:02:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `abnormalbalance`
-- ----------------------------
DROP TABLE IF EXISTS `abnormalbalance`;
CREATE TABLE `abnormalbalance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `current_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前金额',
  `mod` tinyint(1) NOT NULL DEFAULT '1' COMMENT '进出：1 进 2出',
  `content` varchar(1000) NOT NULL COMMENT '变动内容',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_admin_id` int(11) NOT NULL COMMENT '创建用户ID',
  `verify_admin_id` int(11) DEFAULT NULL COMMENT '审核用户ID',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT NULL COMMENT '批准用户ID',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) DEFAULT NULL COMMENT '完成用户ID',
  `operation_time` datetime DEFAULT NULL COMMENT '完成时间',
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='非常态资金变化业务表';

-- ----------------------------
-- Records of abnormalbalance
-- ----------------------------
INSERT INTO `abnormalbalance` VALUES ('1', '看快速的离开了', '3', '10120.00', '100.00', '2', '撒旦法司法', '2', '2016-06-10 19:24:14', '1', '0', '2016-06-10 19:24:14', '0', '2016-06-10 19:24:14', '4', '2016-06-10 19:24:14', '6', null);
INSERT INTO `abnormalbalance` VALUES ('3', '啊时代发生', '3', '10120.00', '100.00', '1', '啊的说法是否', '3', '2016-06-10 19:26:31', '1', '3', '2016-06-10 19:32:07', '2', '2016-06-10 19:32:23', '4', '2016-06-10 19:33:07', '12', null);

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员 ID',
  `username` varchar(45) NOT NULL COMMENT '登录账户',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `auth_key` varchar(32) NOT NULL COMMENT '校验码',
  `mobile` varchar(15) NOT NULL COMMENT '联系电话',
  `department_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员角色ID',
  `last_login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '最后登录 IP',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '账号状态\n0: 禁用\n1: 启用\n99：删除',
  `name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `id_card` varchar(100) DEFAULT NULL COMMENT '证件号',
  `job_number` varchar(100) DEFAULT NULL COMMENT '工号',
  `entry_date` date DEFAULT NULL COMMENT '入职日期',
  `leave_date` date DEFAULT NULL COMMENT '离职日期',
  `create_time` datetime DEFAULT NULL COMMENT '创建日期',
  `update_time` datetime DEFAULT NULL COMMENT '更新日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='管理员主表';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$13$MPwsMwP/Wc0TMwpXp3k7d.eHNqtMl/Fo2t4ymCQPAp7tKozW1P/Z2', 'EtOAf_2RIqL9BpkGfS8zye_iXDVUPk6K', '', '0', '0', '1465636953', '127.0.0.1', '1', null, null, null, null, null, '2016-06-04 15:06:25', '2016-06-11 17:22:33');
INSERT INTO `admin` VALUES ('2', 'nanshan1', '$2y$13$FVl3uQgpkWOlQL4.hulBJ.uww9s.mqPvOffb064rrTqGWci5B7OcW', '8ldABjXn9g_C8_oh15PZHJrkyowgPE_l', '', '3', '5', '1465636932', '127.0.0.1', '1', null, '001', '456', '0000-00-00', null, '2016-06-04 16:30:45', '2016-06-11 17:22:12');
INSERT INTO `admin` VALUES ('3', 'nanshan2', '$2y$13$AwNRbBdlHyX6d6pb4H5E6OwqZuFY6Yy/dAlWFP76bW/o/32b93jOW', '2dwPao5_9K6ZWZB45W75jZmJplcnleK6', '', '3', '4', '1465636540', '127.0.0.1', '1', null, '003', '233', null, null, '2016-06-04 16:31:15', '2016-06-11 17:15:40');
INSERT INTO `admin` VALUES ('4', 'nanshan3', '$2y$13$Knr9b598kvORZ0ai7/L7..eXQnv8ks5ZHEsFM6t6sg0bYDrHHjWZy', 'y36vZ_E2EkB4jkzO950tEfmBz0sY3p50', '', '3', '6', '1465636960', '127.0.0.1', '1', null, '666', '333', null, null, '2016-06-04 16:31:35', '2016-06-11 17:22:40');

-- ----------------------------
-- Table structure for `adminlog`
-- ----------------------------
DROP TABLE IF EXISTS `adminlog`;
CREATE TABLE `adminlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '操作员ID',
  `content` varchar(50) NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL COMMENT '状态：0 操作失败；1 操作成功',
  `type` varchar(20) DEFAULT NULL COMMENT '操作类型',
  `ip` varchar(20) NOT NULL COMMENT '操作 IP',
  `os` varchar(100) DEFAULT NULL COMMENT '操作系统',
  `browser` varchar(100) DEFAULT NULL COMMENT '浏览器',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of adminlog
-- ----------------------------
INSERT INTO `adminlog` VALUES ('1', '1', '新增角色1', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:29:16');
INSERT INTO `adminlog` VALUES ('2', '1', '新增角色2', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:29:26');
INSERT INTO `adminlog` VALUES ('3', '1', '新增角色3', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:29:34');
INSERT INTO `adminlog` VALUES ('4', '1', '新增角色4', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:29:46');
INSERT INTO `adminlog` VALUES ('5', '1', '新增角色5', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:29:59');
INSERT INTO `adminlog` VALUES ('6', '1', '新增角色6', '1', 'add_Role', '120.236.32.58', null, null, '2016-06-04 08:30:08');
INSERT INTO `adminlog` VALUES ('7', '1', '新增管理员2', '1', '新增管理员2', '120.236.32.58', null, null, '2016-06-04 08:30:45');
INSERT INTO `adminlog` VALUES ('8', '1', '新增管理员3', '1', '新增管理员3', '120.236.32.58', null, null, '2016-06-04 08:31:15');
INSERT INTO `adminlog` VALUES ('9', '1', '新增管理员4', '1', '新增管理员4', '120.236.32.58', null, null, '2016-06-04 08:31:35');
INSERT INTO `adminlog` VALUES ('10', '1', '新增业务流程1', '1', 'add_flowconfig', '120.236.32.58', null, null, '2016-06-04 08:33:10');
INSERT INTO `adminlog` VALUES ('11', '1', '新增供应商1', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:33:48');
INSERT INTO `adminlog` VALUES ('12', '1', '新增供应商2', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:34:01');
INSERT INTO `adminlog` VALUES ('13', '1', '新增供应商1', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:35:14');
INSERT INTO `adminlog` VALUES ('14', '1', '新增供应商2', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:35:34');
INSERT INTO `adminlog` VALUES ('15', '1', '新增供应商出品', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:36:07');
INSERT INTO `adminlog` VALUES ('16', '1', '新增供应商出品1', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:36:21');
INSERT INTO `adminlog` VALUES ('17', '1', '供应商出品加入物料1', '1', 'add_supplier_product', '120.236.32.58', null, null, '2016-06-04 08:36:27');
INSERT INTO `adminlog` VALUES ('18', '1', '新增供应商出品2', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:36:58');
INSERT INTO `adminlog` VALUES ('19', '1', '新增供应商出品3', '1', 'add_supplier', '120.236.32.58', null, null, '2016-06-04 08:37:22');
INSERT INTO `adminlog` VALUES ('20', '1', '供应商出品加入物料2', '1', 'add_supplier_product', '120.236.32.58', null, null, '2016-06-04 08:37:26');
INSERT INTO `adminlog` VALUES ('21', '1', '供应商出品加入物料3', '1', 'add_supplier_product', '120.236.32.58', null, null, '2016-06-04 08:37:28');
INSERT INTO `adminlog` VALUES ('22', '1', '新增仓库1', '1', 'add_Warehouse', '120.236.32.58', null, null, '2016-06-04 08:43:17');
INSERT INTO `adminlog` VALUES ('23', '1', '新增仓库2', '1', 'add_Warehouse', '120.236.32.58', null, null, '2016-06-04 08:43:44');
INSERT INTO `adminlog` VALUES ('24', '1', '物料采购计划申请成功：1', '1', 'wplanning_add', '120.236.32.58', null, null, '2016-06-04 08:45:13');
INSERT INTO `adminlog` VALUES ('25', '1', '物料采购计划申请成功：2', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-04 16:54:42');
INSERT INTO `adminlog` VALUES ('26', '1', '物料采购计划申请成功：10', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-04 17:00:57');
INSERT INTO `adminlog` VALUES ('27', '1', '物料采购计划申请成功：15', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-04 17:08:28');
INSERT INTO `adminlog` VALUES ('28', '3', '物料采购计划审核通过：15', '1', 'wplanning_verify', '127.0.0.1', null, null, '2016-06-04 17:08:54');
INSERT INTO `adminlog` VALUES ('29', '2', '物料采购计划批准通过：15', '1', 'wplanning_approval', '127.0.0.1', null, null, '2016-06-04 17:19:34');
INSERT INTO `adminlog` VALUES ('30', '4', '物料采购计划下定申请成功：1', '1', 'wprocurement_add', '127.0.0.1', null, null, '2016-06-04 17:20:13');
INSERT INTO `adminlog` VALUES ('31', '1', '供应商出品加入物料4', '1', 'add_supplier_product', '127.0.0.1', null, null, '2016-06-04 17:23:17');
INSERT INTO `adminlog` VALUES ('32', '1', '新增供应商3', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-04 17:29:32');
INSERT INTO `adminlog` VALUES ('33', '1', '物料采购计划申请成功：16', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-05 16:35:15');
INSERT INTO `adminlog` VALUES ('34', '1', '物料采购计划申请成功：19', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-05 16:38:04');
INSERT INTO `adminlog` VALUES ('35', '1', '物料耗损申请成功：1', '1', 'wwastage_add', '127.0.0.1', null, null, '2016-06-05 17:25:12');
INSERT INTO `adminlog` VALUES ('36', '1', '物料耗损申请成功：2', '1', 'wwastage_add', '127.0.0.1', null, null, '2016-06-05 17:25:43');
INSERT INTO `adminlog` VALUES ('37', '1', '物料采购计划申请成功：23', '1', 'wplanning_add', '127.0.0.1', null, null, '2016-06-08 17:26:05');
INSERT INTO `adminlog` VALUES ('38', '3', '采购计划审核通过：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 18:35:12');
INSERT INTO `adminlog` VALUES ('39', '2', '采购计划执行驳回：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 21:35:56');
INSERT INTO `adminlog` VALUES ('40', '2', '采购计划批准通过：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 21:39:30');
INSERT INTO `adminlog` VALUES ('41', '4', '物料采购计划下定申请成功：23', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-08 21:42:03');
INSERT INTO `adminlog` VALUES ('42', '4', '采购计划执行通过：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 21:42:03');
INSERT INTO `adminlog` VALUES ('43', '4', '新增业务流程2', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-08 21:54:47');
INSERT INTO `adminlog` VALUES ('44', '1', '新增供应商4', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-08 21:55:23');
INSERT INTO `adminlog` VALUES ('45', '4', '物料采购计划下定申请成功：23', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-08 21:57:22');
INSERT INTO `adminlog` VALUES ('46', '4', '采购计划执行通过：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 21:57:22');
INSERT INTO `adminlog` VALUES ('47', '1', '新增业务流程3', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-08 22:13:10');
INSERT INTO `adminlog` VALUES ('48', '1', '删除业务流程3', '1', 'delete_flowconfig', '127.0.0.1', null, null, '2016-06-08 22:13:18');
INSERT INTO `adminlog` VALUES ('49', '4', '物料采购计划下定申请成功：23', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-08 22:14:11');
INSERT INTO `adminlog` VALUES ('50', '4', '采购计划执行通过：23', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-08 22:14:11');
INSERT INTO `adminlog` VALUES ('51', '3', '采购下订审核通过：6', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-08 22:51:58');
INSERT INTO `adminlog` VALUES ('52', '4', '新增业务流程4', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-09 09:54:09');
INSERT INTO `adminlog` VALUES ('53', '1', '新增供应商5', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-09 09:54:49');
INSERT INTO `adminlog` VALUES ('54', '1', '编辑业务流程4', '1', 'update_flowconfig', '127.0.0.1', null, null, '2016-06-09 10:22:59');
INSERT INTO `adminlog` VALUES ('55', '4', '采购计划下定财务记录添加成功：1', '1', 'oprocurement_add', '127.0.0.1', null, null, '2016-06-09 10:42:55');
INSERT INTO `adminlog` VALUES ('56', '4', '物料采购下定入库申请成功：3', '1', 'wbuying_add', '127.0.0.1', null, null, '2016-06-09 10:42:55');
INSERT INTO `adminlog` VALUES ('57', '1', '新增业务流程5', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-09 14:27:46');
INSERT INTO `adminlog` VALUES ('58', '1', '新增供应商6', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-09 14:28:06');
INSERT INTO `adminlog` VALUES ('59', '1', '物料销售申请成功：7', '1', 'wsale_add', '127.0.0.1', null, null, '2016-06-09 14:31:54');
INSERT INTO `adminlog` VALUES ('60', '3', '新增业务流程6', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 00:28:43');
INSERT INTO `adminlog` VALUES ('61', '1', '新增供应商7', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 00:29:58');
INSERT INTO `adminlog` VALUES ('62', '3', '采购计划审核通过：7', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-10 00:30:38');
INSERT INTO `adminlog` VALUES ('63', '2', '采购计划批准通过：7', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-10 00:31:08');
INSERT INTO `adminlog` VALUES ('68', '4', '申请成功：5', '1', 'departblanlog_add', '127.0.0.1', null, null, '2016-06-10 01:01:43');
INSERT INTO `adminlog` VALUES ('69', '4', '采购计划执行通过：7', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-10 01:01:43');
INSERT INTO `adminlog` VALUES ('70', '4', '资金变动执行通过：5', '1', 'departmentbalancelog', '127.0.0.1', null, null, '2016-06-10 01:36:27');
INSERT INTO `adminlog` VALUES ('71', '4', '物料销售申请成功：8', '1', 'wsale_add', '127.0.0.1', null, null, '2016-06-10 14:28:00');
INSERT INTO `adminlog` VALUES ('72', '4', '新增业务流程7', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 14:45:26');
INSERT INTO `adminlog` VALUES ('73', '1', '新增供应商8', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 14:46:08');
INSERT INTO `adminlog` VALUES ('74', '1', '物料盘点申请成功：1', '1', 'wcheck_add', '127.0.0.1', null, null, '2016-06-10 14:46:57');
INSERT INTO `adminlog` VALUES ('75', '3', '盘点申请批准通过：1', '1', 'wcheck', '127.0.0.1', null, null, '2016-06-10 14:50:06');
INSERT INTO `adminlog` VALUES ('76', '4', '新增业务流程8', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 15:09:11');
INSERT INTO `adminlog` VALUES ('77', '1', '新增供应商9', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 15:09:47');
INSERT INTO `adminlog` VALUES ('78', '1', '物料出库申请成功：2', '1', 'wcheckout_add', '127.0.0.1', null, null, '2016-06-10 15:12:20');
INSERT INTO `adminlog` VALUES ('79', '3', '出库申请审核通过：2', '1', 'wcheckout', '127.0.0.1', null, null, '2016-06-10 15:13:29');
INSERT INTO `adminlog` VALUES ('80', '2', '出库申请批准通过：2', '1', 'wcheckout', '127.0.0.1', null, null, '2016-06-10 15:13:49');
INSERT INTO `adminlog` VALUES ('81', '4', '物料出库申请成功完成：2', '1', 'wcheckout_finish', '127.0.0.1', null, null, '2016-06-10 15:15:54');
INSERT INTO `adminlog` VALUES ('82', '4', '出库申请执行通过：2', '1', 'wcheckout', '127.0.0.1', null, null, '2016-06-10 15:15:54');
INSERT INTO `adminlog` VALUES ('83', '4', '物料调仓申请成功：1', '1', 'wtransfer_add', '127.0.0.1', null, null, '2016-06-10 15:43:45');
INSERT INTO `adminlog` VALUES ('84', '1', '新增业务流程9', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 15:45:02');
INSERT INTO `adminlog` VALUES ('85', '1', '新增供应商10', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 15:45:23');
INSERT INTO `adminlog` VALUES ('86', '4', '物料调仓申请成功：1', '1', 'wtransfer_add', '127.0.0.1', null, null, '2016-06-10 15:46:11');
INSERT INTO `adminlog` VALUES ('87', '2', '调仓申请批准通过：1', '1', 'wtransfer', '127.0.0.1', null, null, '2016-06-10 15:51:29');
INSERT INTO `adminlog` VALUES ('88', '4', '物料调仓申请成功完成：1', '1', 'wtransfer_finish', '127.0.0.1', null, null, '2016-06-10 15:52:08');
INSERT INTO `adminlog` VALUES ('89', '4', '调仓申请执行通过：1', '1', 'wtransfer', '127.0.0.1', null, null, '2016-06-10 15:52:08');
INSERT INTO `adminlog` VALUES ('90', '1', '新增业务流程10', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 16:09:32');
INSERT INTO `adminlog` VALUES ('91', '1', '新增供应商11', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 16:09:53');
INSERT INTO `adminlog` VALUES ('92', '4', '物料转货申请成功：1', '1', 'wtransferdep_add', '127.0.0.1', null, null, '2016-06-10 16:12:22');
INSERT INTO `adminlog` VALUES ('93', '2', '转货申请批准通过：1', '1', 'wtransferdep', '127.0.0.1', null, null, '2016-06-10 16:16:24');
INSERT INTO `adminlog` VALUES ('94', '4', '物料转货申请成功完成：1', '1', 'wtrandep_finish', '127.0.0.1', null, null, '2016-06-10 16:16:56');
INSERT INTO `adminlog` VALUES ('95', '4', '转货申请执行通过：1', '1', 'wtransferdep', '127.0.0.1', null, null, '2016-06-10 16:16:57');
INSERT INTO `adminlog` VALUES ('96', '2', '订单入库批准通过：3', '1', 'wbuying', '127.0.0.1', null, null, '2016-06-10 16:24:49');
INSERT INTO `adminlog` VALUES ('97', '2', '订单入库批准通过：3', '1', 'wbuying', '127.0.0.1', null, null, '2016-06-10 16:26:02');
INSERT INTO `adminlog` VALUES ('98', '1', '新增业务流程11', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 16:38:41');
INSERT INTO `adminlog` VALUES ('99', '1', '新增供应商12', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 16:39:02');
INSERT INTO `adminlog` VALUES ('100', '4', '物料耗损申请成功：3', '1', 'wwastage_add', '127.0.0.1', null, null, '2016-06-10 16:41:29');
INSERT INTO `adminlog` VALUES ('101', '2', '物料耗损申请批准通过：3', '1', 'wwastage', '127.0.0.1', null, null, '2016-06-10 16:58:06');
INSERT INTO `adminlog` VALUES ('102', '4', '申请成功：6', '1', 'departblanlog_add', '127.0.0.1', null, null, '2016-06-10 16:58:32');
INSERT INTO `adminlog` VALUES ('103', '4', '驳回物料耗损申请成功完成：3', '1', 'wwastage_finish', '127.0.0.1', null, null, '2016-06-10 16:58:32');
INSERT INTO `adminlog` VALUES ('104', '4', '物料耗损申请执行通过：3', '1', 'wwastage', '127.0.0.1', null, null, '2016-06-10 16:58:32');
INSERT INTO `adminlog` VALUES ('105', '1', '新增业务流程12', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-10 19:19:00');
INSERT INTO `adminlog` VALUES ('106', '1', '新增供应商13', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-10 19:19:19');
INSERT INTO `adminlog` VALUES ('107', '1', '非常态资金流水申请成功：1', '1', 'departblan_add', '127.0.0.1', null, null, '2016-06-10 19:24:14');
INSERT INTO `adminlog` VALUES ('108', '1', '非常态资金流水申请成功：3', '1', 'departblan_add', '127.0.0.1', null, null, '2016-06-10 19:26:32');
INSERT INTO `adminlog` VALUES ('109', '3', '非常态资金变动审核通过：3', '1', 'abnormalbalance', '127.0.0.1', null, null, '2016-06-10 19:32:07');
INSERT INTO `adminlog` VALUES ('110', '2', '非常态资金变动批准通过：3', '1', 'abnormalbalance', '127.0.0.1', null, null, '2016-06-10 19:32:23');
INSERT INTO `adminlog` VALUES ('111', '4', '非常态资金变动执行通过：3', '1', 'abnormalbalance', '127.0.0.1', null, null, '2016-06-10 19:33:07');
INSERT INTO `adminlog` VALUES ('112', '4', '物料盘点申请成功：3', '1', 'wcheck_add', '127.0.0.1', null, null, '2016-06-11 14:49:52');
INSERT INTO `adminlog` VALUES ('113', '4', '物料盘点申请成功：7', '1', 'wcheck_add', '127.0.0.1', null, null, '2016-06-11 15:08:35');
INSERT INTO `adminlog` VALUES ('114', '4', '物料盘点申请成功：15', '1', 'wcheck_add', '127.0.0.1', null, null, '2016-06-11 15:13:56');
INSERT INTO `adminlog` VALUES ('115', '3', '盘点申请批准通过：15', '1', 'wcheck', '127.0.0.1', null, null, '2016-06-11 15:18:26');
INSERT INTO `adminlog` VALUES ('116', '4', '物料盘点成功完成：15', '1', 'wcheck_finish', '127.0.0.1', null, null, '2016-06-11 15:20:43');
INSERT INTO `adminlog` VALUES ('117', '4', '盘点申请执行通过：15', '1', 'wcheck', '127.0.0.1', null, null, '2016-06-11 15:20:43');
INSERT INTO `adminlog` VALUES ('118', '1', '新增供应商出品4', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-11 15:29:32');
INSERT INTO `adminlog` VALUES ('119', '1', '供应商出品加入物料7', '1', 'add_product', '127.0.0.1', null, null, '2016-06-11 15:34:29');
INSERT INTO `adminlog` VALUES ('120', '3', '采购计划审核通过：19', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-11 17:10:58');
INSERT INTO `adminlog` VALUES ('121', '3', '采购计划审核通过：19', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-11 17:11:31');
INSERT INTO `adminlog` VALUES ('122', '3', '采购计划审核通过：19', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-11 17:13:54');
INSERT INTO `adminlog` VALUES ('123', '2', '采购计划批准通过：19', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-11 17:14:16');
INSERT INTO `adminlog` VALUES ('124', '4', '物料采购计划下定申请成功：19', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-11 17:15:17');
INSERT INTO `adminlog` VALUES ('125', '4', '采购计划执行通过：19', '1', 'wplanning', '127.0.0.1', null, null, '2016-06-11 17:15:17');
INSERT INTO `adminlog` VALUES ('126', '3', '采购下订审核通过：7', '1', 'wprocurement', '127.0.0.1', null, null, '2016-06-11 17:18:28');
INSERT INTO `adminlog` VALUES ('127', '4', '采购计划下定财务记录添加成功：2', '1', 'oprocurement_add', '127.0.0.1', null, null, '2016-06-11 17:20:35');
INSERT INTO `adminlog` VALUES ('128', '4', '物料采购下定入库申请成功：4', '1', 'wbuying_add', '127.0.0.1', null, null, '2016-06-11 17:20:35');
INSERT INTO `adminlog` VALUES ('129', '2', '订单入库批准通过：4', '1', 'wbuying', '127.0.0.1', null, null, '2016-06-11 17:22:22');
INSERT INTO `adminlog` VALUES ('130', '4', '物料退货申请成功：7', '1', 'wmaterial_add', '127.0.0.1', null, null, '2016-06-11 18:37:08');
INSERT INTO `adminlog` VALUES ('131', '1', '新增业务流程13', '1', 'add_flowconfig', '127.0.0.1', null, null, '2016-06-11 18:38:39');
INSERT INTO `adminlog` VALUES ('132', '1', '新增供应商14', '1', 'add_supplier', '127.0.0.1', null, null, '2016-06-11 18:38:59');
INSERT INTO `adminlog` VALUES ('133', '4', '物料退货申请成功：1', '1', 'wmaterial_add', '127.0.0.1', null, null, '2016-06-11 18:40:40');
INSERT INTO `adminlog` VALUES ('134', '4', '申请成功：7', '1', 'departblanlog_add', '127.0.0.1', null, null, '2016-06-11 20:16:07');
INSERT INTO `adminlog` VALUES ('135', '4', '物料退货流程成功完成：1', '1', 'wmaterial_finish', '127.0.0.1', null, null, '2016-06-11 20:16:07');
INSERT INTO `adminlog` VALUES ('136', '4', '物料退货申请执行通过：1', '1', 'wmaterial', '127.0.0.1', null, null, '2016-06-11 20:16:07');
INSERT INTO `adminlog` VALUES ('137', '4', '物料退货申请成功：5', '1', 'wmaterial_add', '127.0.0.1', null, null, '2016-06-11 20:43:17');
INSERT INTO `adminlog` VALUES ('138', '4', '申请成功：8', '1', 'departblanlog_add', '127.0.0.1', null, null, '2016-06-11 20:43:35');
INSERT INTO `adminlog` VALUES ('139', '4', '物料退货流程成功完成：5', '1', 'wmaterial_finish', '127.0.0.1', null, null, '2016-06-11 20:43:35');
INSERT INTO `adminlog` VALUES ('140', '4', '物料退货申请执行通过：5', '1', 'wmaterial', '127.0.0.1', null, null, '2016-06-11 20:43:35');
INSERT INTO `adminlog` VALUES ('141', '4', '组合物料模板添加成功：1', '1', 'template_add', '127.0.0.1', null, null, '2016-06-11 20:50:05');
INSERT INTO `adminlog` VALUES ('142', '4', '物料出库申请成功：3', '1', 'wcheckout_add', '127.0.0.1', null, null, '2016-06-11 20:51:26');

-- ----------------------------
-- Table structure for `area`
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '地区名称',
  `parentId` int(11) NOT NULL COMMENT '父类ID',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态 0 无效 1 有效',
  `sort` smallint(5) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5025 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of area
-- ----------------------------
INSERT INTO `area` VALUES ('1', '北京', '0', '1', '0');
INSERT INTO `area` VALUES ('2', '天津', '0', '1', '0');
INSERT INTO `area` VALUES ('3', '河北省', '0', '1', '0');
INSERT INTO `area` VALUES ('4', '山西省', '0', '1', '0');
INSERT INTO `area` VALUES ('5', '内蒙古自治区', '0', '1', '0');
INSERT INTO `area` VALUES ('6', '辽宁省', '0', '1', '0');
INSERT INTO `area` VALUES ('7', '吉林省', '0', '1', '0');
INSERT INTO `area` VALUES ('8', '黑龙江省', '0', '1', '0');
INSERT INTO `area` VALUES ('9', '上海', '0', '1', '0');
INSERT INTO `area` VALUES ('10', '江苏省', '0', '1', '0');
INSERT INTO `area` VALUES ('11', '浙江省', '0', '1', '0');
INSERT INTO `area` VALUES ('12', '安徽省', '0', '1', '0');
INSERT INTO `area` VALUES ('13', '福建省', '0', '1', '0');
INSERT INTO `area` VALUES ('14', '江西省', '0', '1', '0');
INSERT INTO `area` VALUES ('15', '山东省', '0', '1', '0');
INSERT INTO `area` VALUES ('16', '河南省', '0', '1', '0');
INSERT INTO `area` VALUES ('17', '湖北省', '0', '1', '0');
INSERT INTO `area` VALUES ('18', '湖南省', '0', '1', '0');
INSERT INTO `area` VALUES ('19', '广东省', '0', '1', '0');
INSERT INTO `area` VALUES ('20', '广西壮族自治区', '0', '1', '0');
INSERT INTO `area` VALUES ('21', '海南省', '0', '1', '0');
INSERT INTO `area` VALUES ('22', '重庆', '0', '1', '0');
INSERT INTO `area` VALUES ('23', '四川省', '0', '1', '0');
INSERT INTO `area` VALUES ('24', '贵州省', '0', '1', '0');
INSERT INTO `area` VALUES ('25', '云南省', '0', '1', '0');
INSERT INTO `area` VALUES ('26', '西藏自治区', '0', '1', '0');
INSERT INTO `area` VALUES ('27', '陕西省', '0', '1', '0');
INSERT INTO `area` VALUES ('28', '甘肃省', '0', '1', '0');
INSERT INTO `area` VALUES ('29', '青海省', '0', '1', '0');
INSERT INTO `area` VALUES ('30', '宁夏回族自治区', '0', '1', '0');
INSERT INTO `area` VALUES ('31', '新疆维吾尔自治区', '0', '1', '0');
INSERT INTO `area` VALUES ('32', '台湾省', '0', '1', '0');
INSERT INTO `area` VALUES ('33', '香港特别行政区', '0', '1', '0');
INSERT INTO `area` VALUES ('34', '澳门特别行政区', '0', '1', '0');
INSERT INTO `area` VALUES ('35', '海外', '0', '1', '0');
INSERT INTO `area` VALUES ('36', '北京市', '1', '1', '0');
INSERT INTO `area` VALUES ('37', '东城区', '36', '1', '0');
INSERT INTO `area` VALUES ('38', '西城区', '36', '1', '0');
INSERT INTO `area` VALUES ('39', '上海市', '9', '1', '0');
INSERT INTO `area` VALUES ('40', '天津市', '2', '1', '0');
INSERT INTO `area` VALUES ('41', '朝阳区', '36', '1', '0');
INSERT INTO `area` VALUES ('42', '丰台区', '36', '1', '0');
INSERT INTO `area` VALUES ('43', '石景山区', '36', '1', '0');
INSERT INTO `area` VALUES ('44', '海淀区', '36', '1', '0');
INSERT INTO `area` VALUES ('45', '门头沟区', '36', '1', '0');
INSERT INTO `area` VALUES ('46', '房山区', '36', '1', '0');
INSERT INTO `area` VALUES ('47', '通州区', '36', '1', '0');
INSERT INTO `area` VALUES ('48', '顺义区', '36', '1', '0');
INSERT INTO `area` VALUES ('49', '昌平区', '36', '1', '0');
INSERT INTO `area` VALUES ('50', '大兴区', '36', '1', '0');
INSERT INTO `area` VALUES ('51', '怀柔区', '36', '1', '0');
INSERT INTO `area` VALUES ('52', '平谷区', '36', '1', '0');
INSERT INTO `area` VALUES ('53', '密云县', '36', '1', '0');
INSERT INTO `area` VALUES ('54', '延庆县', '36', '1', '0');
INSERT INTO `area` VALUES ('55', '和平区', '40', '1', '0');
INSERT INTO `area` VALUES ('56', '河东区', '40', '1', '0');
INSERT INTO `area` VALUES ('57', '河西区', '40', '1', '0');
INSERT INTO `area` VALUES ('58', '南开区', '40', '1', '0');
INSERT INTO `area` VALUES ('59', '河北区', '40', '1', '0');
INSERT INTO `area` VALUES ('60', '红桥区', '40', '1', '0');
INSERT INTO `area` VALUES ('61', '塘沽区', '40', '1', '0');
INSERT INTO `area` VALUES ('62', '重庆市', '22', '1', '0');
INSERT INTO `area` VALUES ('64', '东丽区', '40', '1', '0');
INSERT INTO `area` VALUES ('65', '西青区', '40', '1', '0');
INSERT INTO `area` VALUES ('66', '津南区', '40', '1', '0');
INSERT INTO `area` VALUES ('67', '北辰区', '40', '1', '0');
INSERT INTO `area` VALUES ('68', '武清区', '40', '1', '0');
INSERT INTO `area` VALUES ('69', '宝坻区', '40', '1', '0');
INSERT INTO `area` VALUES ('70', '宁河县', '40', '1', '0');
INSERT INTO `area` VALUES ('71', '静海县', '40', '1', '0');
INSERT INTO `area` VALUES ('72', '蓟县', '40', '1', '0');
INSERT INTO `area` VALUES ('73', '石家庄市', '3', '1', '0');
INSERT INTO `area` VALUES ('74', '唐山市', '3', '1', '0');
INSERT INTO `area` VALUES ('75', '秦皇岛市', '3', '1', '0');
INSERT INTO `area` VALUES ('76', '邯郸市', '3', '1', '0');
INSERT INTO `area` VALUES ('77', '邢台市', '3', '1', '0');
INSERT INTO `area` VALUES ('78', '保定市', '3', '1', '0');
INSERT INTO `area` VALUES ('79', '张家口市', '3', '1', '0');
INSERT INTO `area` VALUES ('80', '承德市', '3', '1', '0');
INSERT INTO `area` VALUES ('81', '衡水市', '3', '1', '0');
INSERT INTO `area` VALUES ('82', '廊坊市', '3', '1', '0');
INSERT INTO `area` VALUES ('83', '沧州市', '3', '1', '0');
INSERT INTO `area` VALUES ('84', '太原市', '4', '1', '0');
INSERT INTO `area` VALUES ('85', '大同市', '4', '1', '0');
INSERT INTO `area` VALUES ('86', '阳泉市', '4', '1', '0');
INSERT INTO `area` VALUES ('87', '长治市', '4', '1', '0');
INSERT INTO `area` VALUES ('88', '晋城市', '4', '1', '0');
INSERT INTO `area` VALUES ('89', '朔州市', '4', '1', '0');
INSERT INTO `area` VALUES ('90', '晋中市', '4', '1', '0');
INSERT INTO `area` VALUES ('91', '运城市', '4', '1', '0');
INSERT INTO `area` VALUES ('92', '忻州市', '4', '1', '0');
INSERT INTO `area` VALUES ('93', '临汾市', '4', '1', '0');
INSERT INTO `area` VALUES ('94', '吕梁市', '4', '1', '0');
INSERT INTO `area` VALUES ('95', '呼和浩特市', '5', '1', '0');
INSERT INTO `area` VALUES ('96', '包头市', '5', '1', '0');
INSERT INTO `area` VALUES ('97', '乌海市', '5', '1', '0');
INSERT INTO `area` VALUES ('98', '赤峰市', '5', '1', '0');
INSERT INTO `area` VALUES ('99', '通辽市', '5', '1', '0');
INSERT INTO `area` VALUES ('100', '鄂尔多斯市', '5', '1', '0');
INSERT INTO `area` VALUES ('101', '呼伦贝尔市', '5', '1', '0');
INSERT INTO `area` VALUES ('102', '巴彦淖尔市', '5', '1', '0');
INSERT INTO `area` VALUES ('103', '乌兰察布市', '5', '1', '0');
INSERT INTO `area` VALUES ('104', '兴安盟', '5', '1', '0');
INSERT INTO `area` VALUES ('105', '锡林郭勒盟', '5', '1', '0');
INSERT INTO `area` VALUES ('106', '阿拉善盟', '5', '1', '0');
INSERT INTO `area` VALUES ('107', '沈阳市', '6', '1', '0');
INSERT INTO `area` VALUES ('108', '大连市', '6', '1', '0');
INSERT INTO `area` VALUES ('109', '鞍山市', '6', '1', '0');
INSERT INTO `area` VALUES ('110', '抚顺市', '6', '1', '0');
INSERT INTO `area` VALUES ('111', '本溪市', '6', '1', '0');
INSERT INTO `area` VALUES ('112', '丹东市', '6', '1', '0');
INSERT INTO `area` VALUES ('113', '锦州市', '6', '1', '0');
INSERT INTO `area` VALUES ('114', '营口市', '6', '1', '0');
INSERT INTO `area` VALUES ('115', '阜新市', '6', '1', '0');
INSERT INTO `area` VALUES ('116', '辽阳市', '6', '1', '0');
INSERT INTO `area` VALUES ('117', '盘锦市', '6', '1', '0');
INSERT INTO `area` VALUES ('118', '铁岭市', '6', '1', '0');
INSERT INTO `area` VALUES ('119', '朝阳市', '6', '1', '0');
INSERT INTO `area` VALUES ('120', '葫芦岛市', '6', '1', '0');
INSERT INTO `area` VALUES ('121', '长春市', '7', '1', '0');
INSERT INTO `area` VALUES ('122', '吉林市', '7', '1', '0');
INSERT INTO `area` VALUES ('123', '四平市', '7', '1', '0');
INSERT INTO `area` VALUES ('124', '辽源市', '7', '1', '0');
INSERT INTO `area` VALUES ('125', '通化市', '7', '1', '0');
INSERT INTO `area` VALUES ('126', '白山市', '7', '1', '0');
INSERT INTO `area` VALUES ('127', '松原市', '7', '1', '0');
INSERT INTO `area` VALUES ('128', '白城市', '7', '1', '0');
INSERT INTO `area` VALUES ('129', '延边朝鲜族自治州', '7', '1', '0');
INSERT INTO `area` VALUES ('130', '哈尔滨市', '8', '1', '0');
INSERT INTO `area` VALUES ('131', '齐齐哈尔市', '8', '1', '0');
INSERT INTO `area` VALUES ('132', '鸡西市', '8', '1', '0');
INSERT INTO `area` VALUES ('133', '鹤岗市', '8', '1', '0');
INSERT INTO `area` VALUES ('134', '双鸭山市', '8', '1', '0');
INSERT INTO `area` VALUES ('135', '大庆市', '8', '1', '0');
INSERT INTO `area` VALUES ('136', '伊春市', '8', '1', '0');
INSERT INTO `area` VALUES ('137', '佳木斯市', '8', '1', '0');
INSERT INTO `area` VALUES ('138', '七台河市', '8', '1', '0');
INSERT INTO `area` VALUES ('139', '牡丹江市', '8', '1', '0');
INSERT INTO `area` VALUES ('140', '黑河市', '8', '1', '0');
INSERT INTO `area` VALUES ('141', '绥化市', '8', '1', '0');
INSERT INTO `area` VALUES ('142', '大兴安岭地区', '8', '1', '0');
INSERT INTO `area` VALUES ('143', '黄浦区', '39', '1', '0');
INSERT INTO `area` VALUES ('144', '卢湾区', '39', '1', '0');
INSERT INTO `area` VALUES ('145', '徐汇区', '39', '1', '0');
INSERT INTO `area` VALUES ('146', '长宁区', '39', '1', '0');
INSERT INTO `area` VALUES ('147', '静安区', '39', '1', '0');
INSERT INTO `area` VALUES ('148', '普陀区', '39', '1', '0');
INSERT INTO `area` VALUES ('149', '闸北区', '39', '1', '0');
INSERT INTO `area` VALUES ('150', '虹口区', '39', '1', '0');
INSERT INTO `area` VALUES ('151', '杨浦区', '39', '1', '0');
INSERT INTO `area` VALUES ('152', '闵行区', '39', '1', '0');
INSERT INTO `area` VALUES ('153', '宝山区', '39', '1', '0');
INSERT INTO `area` VALUES ('154', '嘉定区', '39', '1', '0');
INSERT INTO `area` VALUES ('155', '浦东新区', '39', '1', '0');
INSERT INTO `area` VALUES ('156', '金山区', '39', '1', '0');
INSERT INTO `area` VALUES ('157', '松江区', '39', '1', '0');
INSERT INTO `area` VALUES ('158', '青浦区', '39', '1', '0');
INSERT INTO `area` VALUES ('159', '南汇区', '39', '1', '0');
INSERT INTO `area` VALUES ('160', '奉贤区', '39', '1', '0');
INSERT INTO `area` VALUES ('161', '崇明县', '39', '1', '0');
INSERT INTO `area` VALUES ('162', '南京市', '10', '1', '0');
INSERT INTO `area` VALUES ('163', '无锡市', '10', '1', '0');
INSERT INTO `area` VALUES ('164', '徐州市', '10', '1', '0');
INSERT INTO `area` VALUES ('165', '常州市', '10', '1', '0');
INSERT INTO `area` VALUES ('166', '苏州市', '10', '1', '0');
INSERT INTO `area` VALUES ('167', '南通市', '10', '1', '0');
INSERT INTO `area` VALUES ('168', '连云港市', '10', '1', '0');
INSERT INTO `area` VALUES ('169', '淮安市', '10', '1', '0');
INSERT INTO `area` VALUES ('170', '盐城市', '10', '1', '0');
INSERT INTO `area` VALUES ('171', '扬州市', '10', '1', '0');
INSERT INTO `area` VALUES ('172', '镇江市', '10', '1', '0');
INSERT INTO `area` VALUES ('173', '泰州市', '10', '1', '0');
INSERT INTO `area` VALUES ('174', '宿迁市', '10', '1', '0');
INSERT INTO `area` VALUES ('175', '杭州市', '11', '1', '0');
INSERT INTO `area` VALUES ('176', '宁波市', '11', '1', '0');
INSERT INTO `area` VALUES ('177', '温州市', '11', '1', '0');
INSERT INTO `area` VALUES ('178', '嘉兴市', '11', '1', '0');
INSERT INTO `area` VALUES ('179', '湖州市', '11', '1', '0');
INSERT INTO `area` VALUES ('180', '绍兴市', '11', '1', '0');
INSERT INTO `area` VALUES ('181', '舟山市', '11', '1', '0');
INSERT INTO `area` VALUES ('182', '衢州市', '11', '1', '0');
INSERT INTO `area` VALUES ('183', '金华市', '11', '1', '0');
INSERT INTO `area` VALUES ('184', '台州市', '11', '1', '0');
INSERT INTO `area` VALUES ('185', '丽水市', '11', '1', '0');
INSERT INTO `area` VALUES ('186', '合肥市', '12', '1', '0');
INSERT INTO `area` VALUES ('187', '芜湖市', '12', '1', '0');
INSERT INTO `area` VALUES ('188', '蚌埠市', '12', '1', '0');
INSERT INTO `area` VALUES ('189', '淮南市', '12', '1', '0');
INSERT INTO `area` VALUES ('190', '马鞍山市', '12', '1', '0');
INSERT INTO `area` VALUES ('191', '淮北市', '12', '1', '0');
INSERT INTO `area` VALUES ('192', '铜陵市', '12', '1', '0');
INSERT INTO `area` VALUES ('193', '安庆市', '12', '1', '0');
INSERT INTO `area` VALUES ('194', '黄山市', '12', '1', '0');
INSERT INTO `area` VALUES ('195', '滁州市', '12', '1', '0');
INSERT INTO `area` VALUES ('196', '阜阳市', '12', '1', '0');
INSERT INTO `area` VALUES ('197', '宿州市', '12', '1', '0');
INSERT INTO `area` VALUES ('198', '巢湖市', '12', '1', '0');
INSERT INTO `area` VALUES ('199', '六安市', '12', '1', '0');
INSERT INTO `area` VALUES ('200', '亳州市', '12', '1', '0');
INSERT INTO `area` VALUES ('201', '池州市', '12', '1', '0');
INSERT INTO `area` VALUES ('202', '宣城市', '12', '1', '0');
INSERT INTO `area` VALUES ('203', '福州市', '13', '1', '0');
INSERT INTO `area` VALUES ('204', '厦门市', '13', '1', '0');
INSERT INTO `area` VALUES ('205', '莆田市', '13', '1', '0');
INSERT INTO `area` VALUES ('206', '三明市', '13', '1', '0');
INSERT INTO `area` VALUES ('207', '泉州市', '13', '1', '0');
INSERT INTO `area` VALUES ('208', '漳州市', '13', '1', '0');
INSERT INTO `area` VALUES ('209', '南平市', '13', '1', '0');
INSERT INTO `area` VALUES ('210', '龙岩市', '13', '1', '0');
INSERT INTO `area` VALUES ('211', '宁德市', '13', '1', '0');
INSERT INTO `area` VALUES ('212', '南昌市', '14', '1', '0');
INSERT INTO `area` VALUES ('213', '景德镇市', '14', '1', '0');
INSERT INTO `area` VALUES ('214', '萍乡市', '14', '1', '0');
INSERT INTO `area` VALUES ('215', '九江市', '14', '1', '0');
INSERT INTO `area` VALUES ('216', '新余市', '14', '1', '0');
INSERT INTO `area` VALUES ('217', '鹰潭市', '14', '1', '0');
INSERT INTO `area` VALUES ('218', '赣州市', '14', '1', '0');
INSERT INTO `area` VALUES ('219', '吉安市', '14', '1', '0');
INSERT INTO `area` VALUES ('220', '宜春市', '14', '1', '0');
INSERT INTO `area` VALUES ('221', '抚州市', '14', '1', '0');
INSERT INTO `area` VALUES ('222', '上饶市', '14', '1', '0');
INSERT INTO `area` VALUES ('223', '济南市', '15', '1', '0');
INSERT INTO `area` VALUES ('224', '青岛市', '15', '1', '0');
INSERT INTO `area` VALUES ('225', '淄博市', '15', '1', '0');
INSERT INTO `area` VALUES ('226', '枣庄市', '15', '1', '0');
INSERT INTO `area` VALUES ('227', '东营市', '15', '1', '0');
INSERT INTO `area` VALUES ('228', '烟台市', '15', '1', '0');
INSERT INTO `area` VALUES ('229', '潍坊市', '15', '1', '0');
INSERT INTO `area` VALUES ('230', '济宁市', '15', '1', '0');
INSERT INTO `area` VALUES ('231', '泰安市', '15', '1', '0');
INSERT INTO `area` VALUES ('232', '威海市', '15', '1', '0');
INSERT INTO `area` VALUES ('233', '日照市', '15', '1', '0');
INSERT INTO `area` VALUES ('234', '莱芜市', '15', '1', '0');
INSERT INTO `area` VALUES ('235', '临沂市', '15', '1', '0');
INSERT INTO `area` VALUES ('236', '德州市', '15', '1', '0');
INSERT INTO `area` VALUES ('237', '聊城市', '15', '1', '0');
INSERT INTO `area` VALUES ('238', '滨州市', '15', '1', '0');
INSERT INTO `area` VALUES ('239', '菏泽市', '15', '1', '0');
INSERT INTO `area` VALUES ('240', '郑州市', '16', '1', '0');
INSERT INTO `area` VALUES ('241', '开封市', '16', '1', '0');
INSERT INTO `area` VALUES ('242', '洛阳市', '16', '1', '0');
INSERT INTO `area` VALUES ('243', '平顶山市', '16', '1', '0');
INSERT INTO `area` VALUES ('244', '安阳市', '16', '1', '0');
INSERT INTO `area` VALUES ('245', '鹤壁市', '16', '1', '0');
INSERT INTO `area` VALUES ('246', '新乡市', '16', '1', '0');
INSERT INTO `area` VALUES ('247', '焦作市', '16', '1', '0');
INSERT INTO `area` VALUES ('248', '濮阳市', '16', '1', '0');
INSERT INTO `area` VALUES ('249', '许昌市', '16', '1', '0');
INSERT INTO `area` VALUES ('250', '漯河市', '16', '1', '0');
INSERT INTO `area` VALUES ('251', '三门峡市', '16', '1', '0');
INSERT INTO `area` VALUES ('252', '南阳市', '16', '1', '0');
INSERT INTO `area` VALUES ('253', '商丘市', '16', '1', '0');
INSERT INTO `area` VALUES ('254', '信阳市', '16', '1', '0');
INSERT INTO `area` VALUES ('255', '周口市', '16', '1', '0');
INSERT INTO `area` VALUES ('256', '驻马店市', '16', '1', '0');
INSERT INTO `area` VALUES ('257', '济源市', '16', '1', '0');
INSERT INTO `area` VALUES ('258', '武汉市', '17', '1', '0');
INSERT INTO `area` VALUES ('259', '黄石市', '17', '1', '0');
INSERT INTO `area` VALUES ('260', '十堰市', '17', '1', '0');
INSERT INTO `area` VALUES ('261', '宜昌市', '17', '1', '0');
INSERT INTO `area` VALUES ('262', '襄樊市', '17', '1', '0');
INSERT INTO `area` VALUES ('263', '鄂州市', '17', '1', '0');
INSERT INTO `area` VALUES ('264', '荆门市', '17', '1', '0');
INSERT INTO `area` VALUES ('265', '孝感市', '17', '1', '0');
INSERT INTO `area` VALUES ('266', '荆州市', '17', '1', '0');
INSERT INTO `area` VALUES ('267', '黄冈市', '17', '1', '0');
INSERT INTO `area` VALUES ('268', '咸宁市', '17', '1', '0');
INSERT INTO `area` VALUES ('269', '随州市', '17', '1', '0');
INSERT INTO `area` VALUES ('270', '恩施土家族苗族自治州', '17', '1', '0');
INSERT INTO `area` VALUES ('271', '仙桃市', '17', '1', '0');
INSERT INTO `area` VALUES ('272', '潜江市', '17', '1', '0');
INSERT INTO `area` VALUES ('273', '天门市', '17', '1', '0');
INSERT INTO `area` VALUES ('274', '神农架林区', '17', '1', '0');
INSERT INTO `area` VALUES ('275', '长沙市', '18', '1', '0');
INSERT INTO `area` VALUES ('276', '株洲市', '18', '1', '0');
INSERT INTO `area` VALUES ('277', '湘潭市', '18', '1', '0');
INSERT INTO `area` VALUES ('278', '衡阳市', '18', '1', '0');
INSERT INTO `area` VALUES ('279', '邵阳市', '18', '1', '0');
INSERT INTO `area` VALUES ('280', '岳阳市', '18', '1', '0');
INSERT INTO `area` VALUES ('281', '常德市', '18', '1', '0');
INSERT INTO `area` VALUES ('282', '张家界市', '18', '1', '0');
INSERT INTO `area` VALUES ('283', '益阳市', '18', '1', '0');
INSERT INTO `area` VALUES ('284', '郴州市', '18', '1', '0');
INSERT INTO `area` VALUES ('285', '永州市', '18', '1', '0');
INSERT INTO `area` VALUES ('286', '怀化市', '18', '1', '0');
INSERT INTO `area` VALUES ('287', '娄底市', '18', '1', '0');
INSERT INTO `area` VALUES ('288', '湘西土家族苗族自治州', '18', '1', '0');
INSERT INTO `area` VALUES ('289', '广州市', '19', '1', '0');
INSERT INTO `area` VALUES ('290', '韶关市', '19', '1', '0');
INSERT INTO `area` VALUES ('291', '深圳市', '19', '1', '0');
INSERT INTO `area` VALUES ('292', '珠海市', '19', '1', '0');
INSERT INTO `area` VALUES ('293', '汕头市', '19', '1', '0');
INSERT INTO `area` VALUES ('294', '佛山市', '19', '1', '0');
INSERT INTO `area` VALUES ('295', '江门市', '19', '1', '0');
INSERT INTO `area` VALUES ('296', '湛江市', '19', '1', '0');
INSERT INTO `area` VALUES ('297', '茂名市', '19', '1', '0');
INSERT INTO `area` VALUES ('298', '肇庆市', '19', '1', '0');
INSERT INTO `area` VALUES ('299', '惠州市', '19', '1', '0');
INSERT INTO `area` VALUES ('300', '梅州市', '19', '1', '0');
INSERT INTO `area` VALUES ('301', '汕尾市', '19', '1', '0');
INSERT INTO `area` VALUES ('302', '河源市', '19', '1', '0');
INSERT INTO `area` VALUES ('303', '阳江市', '19', '1', '0');
INSERT INTO `area` VALUES ('304', '清远市', '19', '1', '0');
INSERT INTO `area` VALUES ('305', '东莞市', '19', '1', '0');
INSERT INTO `area` VALUES ('306', '中山市', '19', '1', '0');
INSERT INTO `area` VALUES ('307', '潮州市', '19', '1', '0');
INSERT INTO `area` VALUES ('308', '揭阳市', '19', '1', '0');
INSERT INTO `area` VALUES ('309', '云浮市', '19', '1', '0');
INSERT INTO `area` VALUES ('310', '南宁市', '20', '1', '0');
INSERT INTO `area` VALUES ('311', '柳州市', '20', '1', '0');
INSERT INTO `area` VALUES ('312', '桂林市', '20', '1', '0');
INSERT INTO `area` VALUES ('313', '梧州市', '20', '1', '0');
INSERT INTO `area` VALUES ('314', '北海市', '20', '1', '0');
INSERT INTO `area` VALUES ('315', '防城港市', '20', '1', '0');
INSERT INTO `area` VALUES ('316', '钦州市', '20', '1', '0');
INSERT INTO `area` VALUES ('317', '贵港市', '20', '1', '0');
INSERT INTO `area` VALUES ('318', '玉林市', '20', '1', '0');
INSERT INTO `area` VALUES ('319', '百色市', '20', '1', '0');
INSERT INTO `area` VALUES ('320', '贺州市', '20', '1', '0');
INSERT INTO `area` VALUES ('321', '河池市', '20', '1', '0');
INSERT INTO `area` VALUES ('322', '来宾市', '20', '1', '0');
INSERT INTO `area` VALUES ('323', '崇左市', '20', '1', '0');
INSERT INTO `area` VALUES ('324', '海口市', '21', '1', '0');
INSERT INTO `area` VALUES ('325', '三亚市', '21', '1', '0');
INSERT INTO `area` VALUES ('326', '五指山市', '21', '1', '0');
INSERT INTO `area` VALUES ('327', '琼海市', '21', '1', '0');
INSERT INTO `area` VALUES ('328', '儋州市', '21', '1', '0');
INSERT INTO `area` VALUES ('329', '文昌市', '21', '1', '0');
INSERT INTO `area` VALUES ('330', '万宁市', '21', '1', '0');
INSERT INTO `area` VALUES ('331', '东方市', '21', '1', '0');
INSERT INTO `area` VALUES ('332', '定安县', '21', '1', '0');
INSERT INTO `area` VALUES ('333', '屯昌县', '21', '1', '0');
INSERT INTO `area` VALUES ('334', '澄迈县', '21', '1', '0');
INSERT INTO `area` VALUES ('335', '临高县', '21', '1', '0');
INSERT INTO `area` VALUES ('336', '白沙黎族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('337', '昌江黎族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('338', '乐东黎族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('339', '陵水黎族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('340', '保亭黎族苗族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('341', '琼中黎族苗族自治县', '21', '1', '0');
INSERT INTO `area` VALUES ('342', '西沙群岛', '21', '1', '0');
INSERT INTO `area` VALUES ('343', '南沙群岛', '21', '1', '0');
INSERT INTO `area` VALUES ('344', '中沙群岛的岛礁及其海域', '21', '1', '0');
INSERT INTO `area` VALUES ('345', '万州区', '62', '1', '0');
INSERT INTO `area` VALUES ('346', '涪陵区', '62', '1', '0');
INSERT INTO `area` VALUES ('347', '渝中区', '62', '1', '0');
INSERT INTO `area` VALUES ('348', '大渡口区', '62', '1', '0');
INSERT INTO `area` VALUES ('349', '江北区', '62', '1', '0');
INSERT INTO `area` VALUES ('350', '沙坪坝区', '62', '1', '0');
INSERT INTO `area` VALUES ('351', '九龙坡区', '62', '1', '0');
INSERT INTO `area` VALUES ('352', '南岸区', '62', '1', '0');
INSERT INTO `area` VALUES ('353', '北碚区', '62', '1', '0');
INSERT INTO `area` VALUES ('354', '双桥区', '62', '1', '0');
INSERT INTO `area` VALUES ('355', '万盛区', '62', '1', '0');
INSERT INTO `area` VALUES ('356', '渝北区', '62', '1', '0');
INSERT INTO `area` VALUES ('357', '巴南区', '62', '1', '0');
INSERT INTO `area` VALUES ('358', '黔江区', '62', '1', '0');
INSERT INTO `area` VALUES ('359', '长寿区', '62', '1', '0');
INSERT INTO `area` VALUES ('360', '綦江县', '62', '1', '0');
INSERT INTO `area` VALUES ('361', '潼南县', '62', '1', '0');
INSERT INTO `area` VALUES ('362', '铜梁县', '62', '1', '0');
INSERT INTO `area` VALUES ('363', '大足县', '62', '1', '0');
INSERT INTO `area` VALUES ('364', '荣昌县', '62', '1', '0');
INSERT INTO `area` VALUES ('365', '璧山县', '62', '1', '0');
INSERT INTO `area` VALUES ('366', '梁平县', '62', '1', '0');
INSERT INTO `area` VALUES ('367', '城口县', '62', '1', '0');
INSERT INTO `area` VALUES ('368', '丰都县', '62', '1', '0');
INSERT INTO `area` VALUES ('369', '垫江县', '62', '1', '0');
INSERT INTO `area` VALUES ('370', '武隆县', '62', '1', '0');
INSERT INTO `area` VALUES ('371', '忠县', '62', '1', '0');
INSERT INTO `area` VALUES ('372', '开县', '62', '1', '0');
INSERT INTO `area` VALUES ('373', '云阳县', '62', '1', '0');
INSERT INTO `area` VALUES ('374', '奉节县', '62', '1', '0');
INSERT INTO `area` VALUES ('375', '巫山县', '62', '1', '0');
INSERT INTO `area` VALUES ('376', '巫溪县', '62', '1', '0');
INSERT INTO `area` VALUES ('377', '石柱土家族自治县', '62', '1', '0');
INSERT INTO `area` VALUES ('378', '秀山土家族苗族自治县', '62', '1', '0');
INSERT INTO `area` VALUES ('379', '酉阳土家族苗族自治县', '62', '1', '0');
INSERT INTO `area` VALUES ('380', '彭水苗族土家族自治县', '62', '1', '0');
INSERT INTO `area` VALUES ('381', '江津市', '62', '1', '0');
INSERT INTO `area` VALUES ('382', '合川市', '62', '1', '0');
INSERT INTO `area` VALUES ('383', '永川市', '62', '1', '0');
INSERT INTO `area` VALUES ('384', '南川市', '62', '1', '0');
INSERT INTO `area` VALUES ('385', '成都市', '23', '1', '0');
INSERT INTO `area` VALUES ('386', '自贡市', '23', '1', '0');
INSERT INTO `area` VALUES ('387', '攀枝花市', '23', '1', '0');
INSERT INTO `area` VALUES ('388', '泸州市', '23', '1', '0');
INSERT INTO `area` VALUES ('389', '德阳市', '23', '1', '0');
INSERT INTO `area` VALUES ('390', '绵阳市', '23', '1', '0');
INSERT INTO `area` VALUES ('391', '广元市', '23', '1', '0');
INSERT INTO `area` VALUES ('392', '遂宁市', '23', '1', '0');
INSERT INTO `area` VALUES ('393', '内江市', '23', '1', '0');
INSERT INTO `area` VALUES ('394', '乐山市', '23', '1', '0');
INSERT INTO `area` VALUES ('395', '南充市', '23', '1', '0');
INSERT INTO `area` VALUES ('396', '眉山市', '23', '1', '0');
INSERT INTO `area` VALUES ('397', '宜宾市', '23', '1', '0');
INSERT INTO `area` VALUES ('398', '广安市', '23', '1', '0');
INSERT INTO `area` VALUES ('399', '达州市', '23', '1', '0');
INSERT INTO `area` VALUES ('400', '雅安市', '23', '1', '0');
INSERT INTO `area` VALUES ('401', '巴中市', '23', '1', '0');
INSERT INTO `area` VALUES ('402', '资阳市', '23', '1', '0');
INSERT INTO `area` VALUES ('403', '阿坝藏族羌族自治州', '23', '1', '0');
INSERT INTO `area` VALUES ('404', '甘孜藏族自治州', '23', '1', '0');
INSERT INTO `area` VALUES ('405', '凉山彝族自治州', '23', '1', '0');
INSERT INTO `area` VALUES ('406', '贵阳市', '24', '1', '0');
INSERT INTO `area` VALUES ('407', '六盘水市', '24', '1', '0');
INSERT INTO `area` VALUES ('408', '遵义市', '24', '1', '0');
INSERT INTO `area` VALUES ('409', '安顺市', '24', '1', '0');
INSERT INTO `area` VALUES ('410', '铜仁地区', '24', '1', '0');
INSERT INTO `area` VALUES ('411', '黔西南布依族苗族自治州', '24', '1', '0');
INSERT INTO `area` VALUES ('412', '毕节地区', '24', '1', '0');
INSERT INTO `area` VALUES ('413', '黔东南苗族侗族自治州', '24', '1', '0');
INSERT INTO `area` VALUES ('414', '黔南布依族苗族自治州', '24', '1', '0');
INSERT INTO `area` VALUES ('415', '昆明市', '25', '1', '0');
INSERT INTO `area` VALUES ('416', '曲靖市', '25', '1', '0');
INSERT INTO `area` VALUES ('417', '玉溪市', '25', '1', '0');
INSERT INTO `area` VALUES ('418', '保山市', '25', '1', '0');
INSERT INTO `area` VALUES ('419', '昭通市', '25', '1', '0');
INSERT INTO `area` VALUES ('420', '丽江市', '25', '1', '0');
INSERT INTO `area` VALUES ('421', '思茅市', '25', '1', '0');
INSERT INTO `area` VALUES ('422', '临沧市', '25', '1', '0');
INSERT INTO `area` VALUES ('423', '楚雄彝族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('424', '红河哈尼族彝族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('425', '文山壮族苗族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('426', '西双版纳傣族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('427', '大理白族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('428', '德宏傣族景颇族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('429', '怒江傈僳族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('430', '迪庆藏族自治州', '25', '1', '0');
INSERT INTO `area` VALUES ('431', '拉萨市', '26', '1', '0');
INSERT INTO `area` VALUES ('432', '昌都地区', '26', '1', '0');
INSERT INTO `area` VALUES ('433', '山南地区', '26', '1', '0');
INSERT INTO `area` VALUES ('434', '日喀则地区', '26', '1', '0');
INSERT INTO `area` VALUES ('435', '那曲地区', '26', '1', '0');
INSERT INTO `area` VALUES ('436', '阿里地区', '26', '1', '0');
INSERT INTO `area` VALUES ('437', '林芝地区', '26', '1', '0');
INSERT INTO `area` VALUES ('438', '西安市', '27', '1', '0');
INSERT INTO `area` VALUES ('439', '铜川市', '27', '1', '0');
INSERT INTO `area` VALUES ('440', '宝鸡市', '27', '1', '0');
INSERT INTO `area` VALUES ('441', '咸阳市', '27', '1', '0');
INSERT INTO `area` VALUES ('442', '渭南市', '27', '1', '0');
INSERT INTO `area` VALUES ('443', '延安市', '27', '1', '0');
INSERT INTO `area` VALUES ('444', '汉中市', '27', '1', '0');
INSERT INTO `area` VALUES ('445', '榆林市', '27', '1', '0');
INSERT INTO `area` VALUES ('446', '安康市', '27', '1', '0');
INSERT INTO `area` VALUES ('447', '商洛市', '27', '1', '0');
INSERT INTO `area` VALUES ('448', '兰州市', '28', '1', '0');
INSERT INTO `area` VALUES ('449', '嘉峪关市', '28', '1', '0');
INSERT INTO `area` VALUES ('450', '金昌市', '28', '1', '0');
INSERT INTO `area` VALUES ('451', '白银市', '28', '1', '0');
INSERT INTO `area` VALUES ('452', '天水市', '28', '1', '0');
INSERT INTO `area` VALUES ('453', '武威市', '28', '1', '0');
INSERT INTO `area` VALUES ('454', '张掖市', '28', '1', '0');
INSERT INTO `area` VALUES ('455', '平凉市', '28', '1', '0');
INSERT INTO `area` VALUES ('456', '酒泉市', '28', '1', '0');
INSERT INTO `area` VALUES ('457', '庆阳市', '28', '1', '0');
INSERT INTO `area` VALUES ('458', '定西市', '28', '1', '0');
INSERT INTO `area` VALUES ('459', '陇南市', '28', '1', '0');
INSERT INTO `area` VALUES ('460', '临夏回族自治州', '28', '1', '0');
INSERT INTO `area` VALUES ('461', '甘南藏族自治州', '28', '1', '0');
INSERT INTO `area` VALUES ('462', '西宁市', '29', '1', '0');
INSERT INTO `area` VALUES ('463', '海东地区', '29', '1', '0');
INSERT INTO `area` VALUES ('464', '海北藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('465', '黄南藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('466', '海南藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('467', '果洛藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('468', '玉树藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('469', '海西蒙古族藏族自治州', '29', '1', '0');
INSERT INTO `area` VALUES ('470', '银川市', '30', '1', '0');
INSERT INTO `area` VALUES ('471', '石嘴山市', '30', '1', '0');
INSERT INTO `area` VALUES ('472', '吴忠市', '30', '1', '0');
INSERT INTO `area` VALUES ('473', '固原市', '30', '1', '0');
INSERT INTO `area` VALUES ('474', '中卫市', '30', '1', '0');
INSERT INTO `area` VALUES ('475', '乌鲁木齐市', '31', '1', '0');
INSERT INTO `area` VALUES ('476', '克拉玛依市', '31', '1', '0');
INSERT INTO `area` VALUES ('477', '吐鲁番地区', '31', '1', '0');
INSERT INTO `area` VALUES ('478', '哈密地区', '31', '1', '0');
INSERT INTO `area` VALUES ('479', '昌吉回族自治州', '31', '1', '0');
INSERT INTO `area` VALUES ('480', '博尔塔拉蒙古自治州', '31', '1', '0');
INSERT INTO `area` VALUES ('481', '巴音郭楞蒙古自治州', '31', '1', '0');
INSERT INTO `area` VALUES ('482', '阿克苏地区', '31', '1', '0');
INSERT INTO `area` VALUES ('483', '克孜勒苏柯尔克孜自治州', '31', '1', '0');
INSERT INTO `area` VALUES ('484', '喀什地区', '31', '1', '0');
INSERT INTO `area` VALUES ('485', '和田地区', '31', '1', '0');
INSERT INTO `area` VALUES ('486', '伊犁哈萨克自治州', '31', '1', '0');
INSERT INTO `area` VALUES ('487', '塔城地区', '31', '1', '0');
INSERT INTO `area` VALUES ('488', '阿勒泰地区', '31', '1', '0');
INSERT INTO `area` VALUES ('489', '石河子市', '31', '1', '0');
INSERT INTO `area` VALUES ('490', '阿拉尔市', '31', '1', '0');
INSERT INTO `area` VALUES ('491', '图木舒克市', '31', '1', '0');
INSERT INTO `area` VALUES ('492', '五家渠市', '31', '1', '0');
INSERT INTO `area` VALUES ('493', '台北市', '32', '1', '0');
INSERT INTO `area` VALUES ('494', '高雄市', '32', '1', '0');
INSERT INTO `area` VALUES ('495', '基隆市', '32', '1', '0');
INSERT INTO `area` VALUES ('496', '台中市', '32', '1', '0');
INSERT INTO `area` VALUES ('497', '台南市', '32', '1', '0');
INSERT INTO `area` VALUES ('498', '新竹市', '32', '1', '0');
INSERT INTO `area` VALUES ('499', '嘉义市', '32', '1', '0');
INSERT INTO `area` VALUES ('500', '台北县', '32', '1', '0');
INSERT INTO `area` VALUES ('501', '宜兰县', '32', '1', '0');
INSERT INTO `area` VALUES ('502', '桃园县', '32', '1', '0');
INSERT INTO `area` VALUES ('503', '新竹县', '32', '1', '0');
INSERT INTO `area` VALUES ('504', '苗栗县', '32', '1', '0');
INSERT INTO `area` VALUES ('505', '台中县', '32', '1', '0');
INSERT INTO `area` VALUES ('506', '彰化县', '32', '1', '0');
INSERT INTO `area` VALUES ('507', '南投县', '32', '1', '0');
INSERT INTO `area` VALUES ('508', '云林县', '32', '1', '0');
INSERT INTO `area` VALUES ('509', '嘉义县', '32', '1', '0');
INSERT INTO `area` VALUES ('510', '台南县', '32', '1', '0');
INSERT INTO `area` VALUES ('511', '高雄县', '32', '1', '0');
INSERT INTO `area` VALUES ('512', '屏东县', '32', '1', '0');
INSERT INTO `area` VALUES ('513', '澎湖县', '32', '1', '0');
INSERT INTO `area` VALUES ('514', '台东县', '32', '1', '0');
INSERT INTO `area` VALUES ('515', '花莲县', '32', '1', '0');
INSERT INTO `area` VALUES ('516', '中西区', '33', '1', '0');
INSERT INTO `area` VALUES ('517', '东区', '33', '1', '0');
INSERT INTO `area` VALUES ('518', '九龙城区', '33', '1', '0');
INSERT INTO `area` VALUES ('519', '观塘区', '33', '1', '0');
INSERT INTO `area` VALUES ('520', '南区', '33', '1', '0');
INSERT INTO `area` VALUES ('521', '深水埗区', '33', '1', '0');
INSERT INTO `area` VALUES ('522', '黄大仙区', '33', '1', '0');
INSERT INTO `area` VALUES ('523', '湾仔区', '33', '1', '0');
INSERT INTO `area` VALUES ('524', '油尖旺区', '33', '1', '0');
INSERT INTO `area` VALUES ('525', '离岛区', '33', '1', '0');
INSERT INTO `area` VALUES ('526', '葵青区', '33', '1', '0');
INSERT INTO `area` VALUES ('527', '北区', '33', '1', '0');
INSERT INTO `area` VALUES ('528', '西贡区', '33', '1', '0');
INSERT INTO `area` VALUES ('529', '沙田区', '33', '1', '0');
INSERT INTO `area` VALUES ('530', '屯门区', '33', '1', '0');
INSERT INTO `area` VALUES ('531', '大埔区', '33', '1', '0');
INSERT INTO `area` VALUES ('532', '荃湾区', '33', '1', '0');
INSERT INTO `area` VALUES ('533', '元朗区', '33', '1', '0');
INSERT INTO `area` VALUES ('534', '澳门特别行政区', '34', '1', '0');
INSERT INTO `area` VALUES ('535', '美国', '45055', '1', '0');
INSERT INTO `area` VALUES ('536', '加拿大', '45055', '1', '0');
INSERT INTO `area` VALUES ('537', '澳大利亚', '45055', '1', '0');
INSERT INTO `area` VALUES ('538', '新西兰', '45055', '1', '0');
INSERT INTO `area` VALUES ('539', '英国', '45055', '1', '0');
INSERT INTO `area` VALUES ('540', '法国', '45055', '1', '0');
INSERT INTO `area` VALUES ('541', '德国', '45055', '1', '0');
INSERT INTO `area` VALUES ('542', '捷克', '45055', '1', '0');
INSERT INTO `area` VALUES ('543', '荷兰', '45055', '1', '0');
INSERT INTO `area` VALUES ('544', '瑞士', '45055', '1', '0');
INSERT INTO `area` VALUES ('545', '希腊', '45055', '1', '0');
INSERT INTO `area` VALUES ('546', '挪威', '45055', '1', '0');
INSERT INTO `area` VALUES ('547', '瑞典', '45055', '1', '0');
INSERT INTO `area` VALUES ('548', '丹麦', '45055', '1', '0');
INSERT INTO `area` VALUES ('549', '芬兰', '45055', '1', '0');
INSERT INTO `area` VALUES ('550', '爱尔兰', '45055', '1', '0');
INSERT INTO `area` VALUES ('551', '奥地利', '45055', '1', '0');
INSERT INTO `area` VALUES ('552', '意大利', '45055', '1', '0');
INSERT INTO `area` VALUES ('553', '乌克兰', '45055', '1', '0');
INSERT INTO `area` VALUES ('554', '俄罗斯', '45055', '1', '0');
INSERT INTO `area` VALUES ('555', '西班牙', '45055', '1', '0');
INSERT INTO `area` VALUES ('556', '韩国', '45055', '1', '0');
INSERT INTO `area` VALUES ('557', '新加坡', '45055', '1', '0');
INSERT INTO `area` VALUES ('558', '马来西亚', '45055', '1', '0');
INSERT INTO `area` VALUES ('559', '印度', '45055', '1', '0');
INSERT INTO `area` VALUES ('560', '泰国', '45055', '1', '0');
INSERT INTO `area` VALUES ('561', '日本', '45055', '1', '0');
INSERT INTO `area` VALUES ('562', '巴西', '45055', '1', '0');
INSERT INTO `area` VALUES ('563', '阿根廷', '45055', '1', '0');
INSERT INTO `area` VALUES ('564', '南非', '45055', '1', '0');
INSERT INTO `area` VALUES ('565', '埃及', '45055', '1', '0');
INSERT INTO `area` VALUES ('566', '其他', '36', '1', '0');
INSERT INTO `area` VALUES ('1126', '井陉县', '73', '1', '0');
INSERT INTO `area` VALUES ('1127', '井陉矿区', '73', '1', '0');
INSERT INTO `area` VALUES ('1128', '元氏县', '73', '1', '0');
INSERT INTO `area` VALUES ('1129', '平山县', '73', '1', '0');
INSERT INTO `area` VALUES ('1130', '新乐市', '73', '1', '0');
INSERT INTO `area` VALUES ('1131', '新华区', '73', '1', '0');
INSERT INTO `area` VALUES ('1132', '无极县', '73', '1', '0');
INSERT INTO `area` VALUES ('1133', '晋州市', '73', '1', '0');
INSERT INTO `area` VALUES ('1134', '栾城县', '73', '1', '0');
INSERT INTO `area` VALUES ('1135', '桥东区', '73', '1', '0');
INSERT INTO `area` VALUES ('1136', '桥西区', '73', '1', '0');
INSERT INTO `area` VALUES ('1137', '正定县', '73', '1', '0');
INSERT INTO `area` VALUES ('1138', '深泽县', '73', '1', '0');
INSERT INTO `area` VALUES ('1139', '灵寿县', '73', '1', '0');
INSERT INTO `area` VALUES ('1140', '藁城市', '73', '1', '0');
INSERT INTO `area` VALUES ('1141', '行唐县', '73', '1', '0');
INSERT INTO `area` VALUES ('1142', '裕华区', '73', '1', '0');
INSERT INTO `area` VALUES ('1143', '赞皇县', '73', '1', '0');
INSERT INTO `area` VALUES ('1144', '赵县', '73', '1', '0');
INSERT INTO `area` VALUES ('1145', '辛集市', '73', '1', '0');
INSERT INTO `area` VALUES ('1146', '长安区', '73', '1', '0');
INSERT INTO `area` VALUES ('1147', '高邑县', '73', '1', '0');
INSERT INTO `area` VALUES ('1148', '鹿泉市', '73', '1', '0');
INSERT INTO `area` VALUES ('1149', '丰南区', '74', '1', '0');
INSERT INTO `area` VALUES ('1150', '丰润区', '74', '1', '0');
INSERT INTO `area` VALUES ('1151', '乐亭县', '74', '1', '0');
INSERT INTO `area` VALUES ('1152', '古冶区', '74', '1', '0');
INSERT INTO `area` VALUES ('1153', '唐海县', '74', '1', '0');
INSERT INTO `area` VALUES ('1154', '开平区', '74', '1', '0');
INSERT INTO `area` VALUES ('1155', '滦南县', '74', '1', '0');
INSERT INTO `area` VALUES ('1156', '滦县', '74', '1', '0');
INSERT INTO `area` VALUES ('1157', '玉田县', '74', '1', '0');
INSERT INTO `area` VALUES ('1158', '路北区', '74', '1', '0');
INSERT INTO `area` VALUES ('1159', '路南区', '74', '1', '0');
INSERT INTO `area` VALUES ('1160', '迁安市', '74', '1', '0');
INSERT INTO `area` VALUES ('1161', '迁西县', '74', '1', '0');
INSERT INTO `area` VALUES ('1162', '遵化市', '74', '1', '0');
INSERT INTO `area` VALUES ('1163', '北戴河区', '75', '1', '0');
INSERT INTO `area` VALUES ('1164', '卢龙县', '75', '1', '0');
INSERT INTO `area` VALUES ('1165', '山海关区', '75', '1', '0');
INSERT INTO `area` VALUES ('1166', '抚宁县', '75', '1', '0');
INSERT INTO `area` VALUES ('1167', '昌黎县', '75', '1', '0');
INSERT INTO `area` VALUES ('1168', '海港区', '75', '1', '0');
INSERT INTO `area` VALUES ('1169', '青龙满族自治县', '75', '1', '0');
INSERT INTO `area` VALUES ('1170', '丛台区', '76', '1', '0');
INSERT INTO `area` VALUES ('1171', '临漳县', '76', '1', '0');
INSERT INTO `area` VALUES ('1172', '复兴区', '76', '1', '0');
INSERT INTO `area` VALUES ('1173', '大名县', '76', '1', '0');
INSERT INTO `area` VALUES ('1174', '峰峰矿区', '76', '1', '0');
INSERT INTO `area` VALUES ('1175', '广平县', '76', '1', '0');
INSERT INTO `area` VALUES ('1176', '成安县', '76', '1', '0');
INSERT INTO `area` VALUES ('1177', '曲周县', '76', '1', '0');
INSERT INTO `area` VALUES ('1178', '武安市', '76', '1', '0');
INSERT INTO `area` VALUES ('1179', '永年县', '76', '1', '0');
INSERT INTO `area` VALUES ('1180', '涉县', '76', '1', '0');
INSERT INTO `area` VALUES ('1181', '磁县', '76', '1', '0');
INSERT INTO `area` VALUES ('1182', '肥乡县', '76', '1', '0');
INSERT INTO `area` VALUES ('1183', '邯山区', '76', '1', '0');
INSERT INTO `area` VALUES ('1184', '邯郸县', '76', '1', '0');
INSERT INTO `area` VALUES ('1185', '邱县', '76', '1', '0');
INSERT INTO `area` VALUES ('1186', '馆陶县', '76', '1', '0');
INSERT INTO `area` VALUES ('1187', '魏县', '76', '1', '0');
INSERT INTO `area` VALUES ('1188', '鸡泽县', '76', '1', '0');
INSERT INTO `area` VALUES ('1189', '临城县', '77', '1', '0');
INSERT INTO `area` VALUES ('1190', '临西县', '77', '1', '0');
INSERT INTO `area` VALUES ('1191', '任县', '77', '1', '0');
INSERT INTO `area` VALUES ('1192', '内丘县', '77', '1', '0');
INSERT INTO `area` VALUES ('1193', '南和县', '77', '1', '0');
INSERT INTO `area` VALUES ('1194', '南宫市', '77', '1', '0');
INSERT INTO `area` VALUES ('1195', '威县', '77', '1', '0');
INSERT INTO `area` VALUES ('1196', '宁晋县', '77', '1', '0');
INSERT INTO `area` VALUES ('1197', '巨鹿县', '77', '1', '0');
INSERT INTO `area` VALUES ('1198', '平乡县', '77', '1', '0');
INSERT INTO `area` VALUES ('1199', '广宗县', '77', '1', '0');
INSERT INTO `area` VALUES ('1200', '新河县', '77', '1', '0');
INSERT INTO `area` VALUES ('1201', '柏乡县', '77', '1', '0');
INSERT INTO `area` VALUES ('1202', '桥东区', '77', '1', '0');
INSERT INTO `area` VALUES ('1203', '桥西区', '77', '1', '0');
INSERT INTO `area` VALUES ('1204', '沙河市', '77', '1', '0');
INSERT INTO `area` VALUES ('1205', '清河县', '77', '1', '0');
INSERT INTO `area` VALUES ('1206', '邢台县', '77', '1', '0');
INSERT INTO `area` VALUES ('1207', '隆尧县', '77', '1', '0');
INSERT INTO `area` VALUES ('1208', '北市区', '78', '1', '0');
INSERT INTO `area` VALUES ('1209', '南市区', '78', '1', '0');
INSERT INTO `area` VALUES ('1210', '博野县', '78', '1', '0');
INSERT INTO `area` VALUES ('1211', '唐县', '78', '1', '0');
INSERT INTO `area` VALUES ('1212', '安国市', '78', '1', '0');
INSERT INTO `area` VALUES ('1213', '安新县', '78', '1', '0');
INSERT INTO `area` VALUES ('1214', '定兴县', '78', '1', '0');
INSERT INTO `area` VALUES ('1215', '定州市', '78', '1', '0');
INSERT INTO `area` VALUES ('1216', '容城县', '78', '1', '0');
INSERT INTO `area` VALUES ('1217', '徐水县', '78', '1', '0');
INSERT INTO `area` VALUES ('1218', '新市区', '78', '1', '0');
INSERT INTO `area` VALUES ('1219', '易县', '78', '1', '0');
INSERT INTO `area` VALUES ('1220', '曲阳县', '78', '1', '0');
INSERT INTO `area` VALUES ('1221', '望都县', '78', '1', '0');
INSERT INTO `area` VALUES ('1222', '涞水县', '78', '1', '0');
INSERT INTO `area` VALUES ('1223', '涞源县', '78', '1', '0');
INSERT INTO `area` VALUES ('1224', '涿州市', '78', '1', '0');
INSERT INTO `area` VALUES ('1225', '清苑县', '78', '1', '0');
INSERT INTO `area` VALUES ('1226', '满城县', '78', '1', '0');
INSERT INTO `area` VALUES ('1227', '蠡县', '78', '1', '0');
INSERT INTO `area` VALUES ('1228', '阜平县', '78', '1', '0');
INSERT INTO `area` VALUES ('1229', '雄县', '78', '1', '0');
INSERT INTO `area` VALUES ('1230', '顺平县', '78', '1', '0');
INSERT INTO `area` VALUES ('1231', '高碑店市', '78', '1', '0');
INSERT INTO `area` VALUES ('1232', '高阳县', '78', '1', '0');
INSERT INTO `area` VALUES ('1233', '万全县', '79', '1', '0');
INSERT INTO `area` VALUES ('1234', '下花园区', '79', '1', '0');
INSERT INTO `area` VALUES ('1235', '宣化区', '79', '1', '0');
INSERT INTO `area` VALUES ('1236', '宣化县', '79', '1', '0');
INSERT INTO `area` VALUES ('1237', '尚义县', '79', '1', '0');
INSERT INTO `area` VALUES ('1238', '崇礼县', '79', '1', '0');
INSERT INTO `area` VALUES ('1239', '康保县', '79', '1', '0');
INSERT INTO `area` VALUES ('1240', '张北县', '79', '1', '0');
INSERT INTO `area` VALUES ('1241', '怀安县', '79', '1', '0');
INSERT INTO `area` VALUES ('1242', '怀来县', '79', '1', '0');
INSERT INTO `area` VALUES ('1243', '桥东区', '79', '1', '0');
INSERT INTO `area` VALUES ('1244', '桥西区', '79', '1', '0');
INSERT INTO `area` VALUES ('1245', '沽源县', '79', '1', '0');
INSERT INTO `area` VALUES ('1246', '涿鹿县', '79', '1', '0');
INSERT INTO `area` VALUES ('1247', '蔚县', '79', '1', '0');
INSERT INTO `area` VALUES ('1248', '赤城县', '79', '1', '0');
INSERT INTO `area` VALUES ('1249', '阳原县', '79', '1', '0');
INSERT INTO `area` VALUES ('1250', '丰宁满族自治县', '80', '1', '0');
INSERT INTO `area` VALUES ('1251', '兴隆县', '80', '1', '0');
INSERT INTO `area` VALUES ('1252', '双桥区', '80', '1', '0');
INSERT INTO `area` VALUES ('1253', '双滦区', '80', '1', '0');
INSERT INTO `area` VALUES ('1254', '围场满族蒙古族自治县', '80', '1', '0');
INSERT INTO `area` VALUES ('1255', '宽城满族自治县', '80', '1', '0');
INSERT INTO `area` VALUES ('1256', '平泉县', '80', '1', '0');
INSERT INTO `area` VALUES ('1257', '承德县', '80', '1', '0');
INSERT INTO `area` VALUES ('1258', '滦平县', '80', '1', '0');
INSERT INTO `area` VALUES ('1259', '隆化县', '80', '1', '0');
INSERT INTO `area` VALUES ('1260', '鹰手营子矿区', '80', '1', '0');
INSERT INTO `area` VALUES ('1261', '冀州市', '81', '1', '0');
INSERT INTO `area` VALUES ('1262', '安平县', '81', '1', '0');
INSERT INTO `area` VALUES ('1263', '故城县', '81', '1', '0');
INSERT INTO `area` VALUES ('1264', '景县', '81', '1', '0');
INSERT INTO `area` VALUES ('1265', '枣强县', '81', '1', '0');
INSERT INTO `area` VALUES ('1266', '桃城区', '81', '1', '0');
INSERT INTO `area` VALUES ('1267', '武强县', '81', '1', '0');
INSERT INTO `area` VALUES ('1268', '武邑县', '81', '1', '0');
INSERT INTO `area` VALUES ('1269', '深州市', '81', '1', '0');
INSERT INTO `area` VALUES ('1270', '阜城县', '81', '1', '0');
INSERT INTO `area` VALUES ('1271', '饶阳县', '81', '1', '0');
INSERT INTO `area` VALUES ('1272', '三河市', '82', '1', '0');
INSERT INTO `area` VALUES ('1273', '固安县', '82', '1', '0');
INSERT INTO `area` VALUES ('1274', '大厂回族自治县', '82', '1', '0');
INSERT INTO `area` VALUES ('1275', '大城县', '82', '1', '0');
INSERT INTO `area` VALUES ('1276', '安次区', '82', '1', '0');
INSERT INTO `area` VALUES ('1277', '广阳区', '82', '1', '0');
INSERT INTO `area` VALUES ('1278', '文安县', '82', '1', '0');
INSERT INTO `area` VALUES ('1279', '永清县', '82', '1', '0');
INSERT INTO `area` VALUES ('1280', '霸州市', '82', '1', '0');
INSERT INTO `area` VALUES ('1281', '香河县', '82', '1', '0');
INSERT INTO `area` VALUES ('1282', '东光县', '83', '1', '0');
INSERT INTO `area` VALUES ('1283', '任丘市', '83', '1', '0');
INSERT INTO `area` VALUES ('1284', '南皮县', '83', '1', '0');
INSERT INTO `area` VALUES ('1285', '吴桥县', '83', '1', '0');
INSERT INTO `area` VALUES ('1286', '孟村回族自治县', '83', '1', '0');
INSERT INTO `area` VALUES ('1287', '新华区', '83', '1', '0');
INSERT INTO `area` VALUES ('1288', '沧县', '83', '1', '0');
INSERT INTO `area` VALUES ('1289', '河间市', '83', '1', '0');
INSERT INTO `area` VALUES ('1290', '泊头市', '83', '1', '0');
INSERT INTO `area` VALUES ('1291', '海兴县', '83', '1', '0');
INSERT INTO `area` VALUES ('1292', '献县', '83', '1', '0');
INSERT INTO `area` VALUES ('1293', '盐山县', '83', '1', '0');
INSERT INTO `area` VALUES ('1294', '肃宁县', '83', '1', '0');
INSERT INTO `area` VALUES ('1295', '运河区', '83', '1', '0');
INSERT INTO `area` VALUES ('1296', '青县', '83', '1', '0');
INSERT INTO `area` VALUES ('1297', '黄骅市', '83', '1', '0');
INSERT INTO `area` VALUES ('1298', '万柏林区', '84', '1', '0');
INSERT INTO `area` VALUES ('1299', '古交市', '84', '1', '0');
INSERT INTO `area` VALUES ('1300', '娄烦县', '84', '1', '0');
INSERT INTO `area` VALUES ('1301', '小店区', '84', '1', '0');
INSERT INTO `area` VALUES ('1302', '尖草坪区', '84', '1', '0');
INSERT INTO `area` VALUES ('1303', '晋源区', '84', '1', '0');
INSERT INTO `area` VALUES ('1304', '杏花岭区', '84', '1', '0');
INSERT INTO `area` VALUES ('1305', '清徐县', '84', '1', '0');
INSERT INTO `area` VALUES ('1306', '迎泽区', '84', '1', '0');
INSERT INTO `area` VALUES ('1307', '阳曲县', '84', '1', '0');
INSERT INTO `area` VALUES ('1308', '南郊区', '85', '1', '0');
INSERT INTO `area` VALUES ('1309', '城区', '85', '1', '0');
INSERT INTO `area` VALUES ('1310', '大同县', '85', '1', '0');
INSERT INTO `area` VALUES ('1311', '天镇县', '85', '1', '0');
INSERT INTO `area` VALUES ('1312', '左云县', '85', '1', '0');
INSERT INTO `area` VALUES ('1313', '广灵县', '85', '1', '0');
INSERT INTO `area` VALUES ('1314', '新荣区', '85', '1', '0');
INSERT INTO `area` VALUES ('1315', '浑源县', '85', '1', '0');
INSERT INTO `area` VALUES ('1316', '灵丘县', '85', '1', '0');
INSERT INTO `area` VALUES ('1317', '矿区', '85', '1', '0');
INSERT INTO `area` VALUES ('1318', '阳高县', '85', '1', '0');
INSERT INTO `area` VALUES ('1319', '城区', '86', '1', '0');
INSERT INTO `area` VALUES ('1320', '平定县', '86', '1', '0');
INSERT INTO `area` VALUES ('1321', '盂县', '86', '1', '0');
INSERT INTO `area` VALUES ('1322', '矿区', '86', '1', '0');
INSERT INTO `area` VALUES ('1323', '郊区', '86', '1', '0');
INSERT INTO `area` VALUES ('1324', '城区', '87', '1', '0');
INSERT INTO `area` VALUES ('1325', '壶关县', '87', '1', '0');
INSERT INTO `area` VALUES ('1326', '屯留县', '87', '1', '0');
INSERT INTO `area` VALUES ('1327', '平顺县', '87', '1', '0');
INSERT INTO `area` VALUES ('1328', '武乡县', '87', '1', '0');
INSERT INTO `area` VALUES ('1329', '沁县', '87', '1', '0');
INSERT INTO `area` VALUES ('1330', '沁源县', '87', '1', '0');
INSERT INTO `area` VALUES ('1331', '潞城市', '87', '1', '0');
INSERT INTO `area` VALUES ('1332', '襄垣县', '87', '1', '0');
INSERT INTO `area` VALUES ('1333', '郊区', '87', '1', '0');
INSERT INTO `area` VALUES ('1334', '长子县', '87', '1', '0');
INSERT INTO `area` VALUES ('1335', '长治县', '87', '1', '0');
INSERT INTO `area` VALUES ('1336', '黎城县', '87', '1', '0');
INSERT INTO `area` VALUES ('1337', '城区', '88', '1', '0');
INSERT INTO `area` VALUES ('1338', '沁水县', '88', '1', '0');
INSERT INTO `area` VALUES ('1339', '泽州县', '88', '1', '0');
INSERT INTO `area` VALUES ('1340', '阳城县', '88', '1', '0');
INSERT INTO `area` VALUES ('1341', '陵川县', '88', '1', '0');
INSERT INTO `area` VALUES ('1342', '高平市', '88', '1', '0');
INSERT INTO `area` VALUES ('1343', '右玉县', '89', '1', '0');
INSERT INTO `area` VALUES ('1344', '山阴县', '89', '1', '0');
INSERT INTO `area` VALUES ('1345', '平鲁区', '89', '1', '0');
INSERT INTO `area` VALUES ('1346', '应县', '89', '1', '0');
INSERT INTO `area` VALUES ('1347', '怀仁县', '89', '1', '0');
INSERT INTO `area` VALUES ('1348', '朔城区', '89', '1', '0');
INSERT INTO `area` VALUES ('1349', '介休市', '90', '1', '0');
INSERT INTO `area` VALUES ('1350', '和顺县', '90', '1', '0');
INSERT INTO `area` VALUES ('1351', '太谷县', '90', '1', '0');
INSERT INTO `area` VALUES ('1352', '寿阳县', '90', '1', '0');
INSERT INTO `area` VALUES ('1353', '左权县', '90', '1', '0');
INSERT INTO `area` VALUES ('1354', '平遥县', '90', '1', '0');
INSERT INTO `area` VALUES ('1355', '昔阳县', '90', '1', '0');
INSERT INTO `area` VALUES ('1356', '榆次区', '90', '1', '0');
INSERT INTO `area` VALUES ('1357', '榆社县', '90', '1', '0');
INSERT INTO `area` VALUES ('1358', '灵石县', '90', '1', '0');
INSERT INTO `area` VALUES ('1359', '祁县', '90', '1', '0');
INSERT INTO `area` VALUES ('1360', '万荣县', '91', '1', '0');
INSERT INTO `area` VALUES ('1361', '临猗县', '91', '1', '0');
INSERT INTO `area` VALUES ('1362', '垣曲县', '91', '1', '0');
INSERT INTO `area` VALUES ('1363', '夏县', '91', '1', '0');
INSERT INTO `area` VALUES ('1364', '平陆县', '91', '1', '0');
INSERT INTO `area` VALUES ('1365', '新绛县', '91', '1', '0');
INSERT INTO `area` VALUES ('1366', '永济市', '91', '1', '0');
INSERT INTO `area` VALUES ('1367', '河津市', '91', '1', '0');
INSERT INTO `area` VALUES ('1368', '盐湖区', '91', '1', '0');
INSERT INTO `area` VALUES ('1369', '稷山县', '91', '1', '0');
INSERT INTO `area` VALUES ('1370', '绛县', '91', '1', '0');
INSERT INTO `area` VALUES ('1371', '芮城县', '91', '1', '0');
INSERT INTO `area` VALUES ('1372', '闻喜县', '91', '1', '0');
INSERT INTO `area` VALUES ('1373', '五台县', '92', '1', '0');
INSERT INTO `area` VALUES ('1374', '五寨县', '92', '1', '0');
INSERT INTO `area` VALUES ('1375', '代县', '92', '1', '0');
INSERT INTO `area` VALUES ('1376', '保德县', '92', '1', '0');
INSERT INTO `area` VALUES ('1377', '偏关县', '92', '1', '0');
INSERT INTO `area` VALUES ('1378', '原平市', '92', '1', '0');
INSERT INTO `area` VALUES ('1379', '宁武县', '92', '1', '0');
INSERT INTO `area` VALUES ('1380', '定襄县', '92', '1', '0');
INSERT INTO `area` VALUES ('1381', '岢岚县', '92', '1', '0');
INSERT INTO `area` VALUES ('1382', '忻府区', '92', '1', '0');
INSERT INTO `area` VALUES ('1383', '河曲县', '92', '1', '0');
INSERT INTO `area` VALUES ('1384', '神池县', '92', '1', '0');
INSERT INTO `area` VALUES ('1385', '繁峙县', '92', '1', '0');
INSERT INTO `area` VALUES ('1386', '静乐县', '92', '1', '0');
INSERT INTO `area` VALUES ('1387', '乡宁县', '93', '1', '0');
INSERT INTO `area` VALUES ('1388', '侯马市', '93', '1', '0');
INSERT INTO `area` VALUES ('1389', '古县', '93', '1', '0');
INSERT INTO `area` VALUES ('1390', '吉县', '93', '1', '0');
INSERT INTO `area` VALUES ('1391', '大宁县', '93', '1', '0');
INSERT INTO `area` VALUES ('1392', '安泽县', '93', '1', '0');
INSERT INTO `area` VALUES ('1393', '尧都区', '93', '1', '0');
INSERT INTO `area` VALUES ('1394', '曲沃县', '93', '1', '0');
INSERT INTO `area` VALUES ('1395', '永和县', '93', '1', '0');
INSERT INTO `area` VALUES ('1396', '汾西县', '93', '1', '0');
INSERT INTO `area` VALUES ('1397', '洪洞县', '93', '1', '0');
INSERT INTO `area` VALUES ('1398', '浮山县', '93', '1', '0');
INSERT INTO `area` VALUES ('1399', '翼城县', '93', '1', '0');
INSERT INTO `area` VALUES ('1400', '蒲县', '93', '1', '0');
INSERT INTO `area` VALUES ('1401', '襄汾县', '93', '1', '0');
INSERT INTO `area` VALUES ('1402', '隰县', '93', '1', '0');
INSERT INTO `area` VALUES ('1403', '霍州市', '93', '1', '0');
INSERT INTO `area` VALUES ('1404', '中阳县', '94', '1', '0');
INSERT INTO `area` VALUES ('1405', '临县', '94', '1', '0');
INSERT INTO `area` VALUES ('1406', '交口县', '94', '1', '0');
INSERT INTO `area` VALUES ('1407', '交城县', '94', '1', '0');
INSERT INTO `area` VALUES ('1408', '兴县', '94', '1', '0');
INSERT INTO `area` VALUES ('1409', '孝义市', '94', '1', '0');
INSERT INTO `area` VALUES ('1410', '岚县', '94', '1', '0');
INSERT INTO `area` VALUES ('1411', '文水县', '94', '1', '0');
INSERT INTO `area` VALUES ('1412', '方山县', '94', '1', '0');
INSERT INTO `area` VALUES ('1413', '柳林县', '94', '1', '0');
INSERT INTO `area` VALUES ('1414', '汾阳市', '94', '1', '0');
INSERT INTO `area` VALUES ('1415', '石楼县', '94', '1', '0');
INSERT INTO `area` VALUES ('1416', '离石区', '94', '1', '0');
INSERT INTO `area` VALUES ('1417', '和林格尔县', '95', '1', '0');
INSERT INTO `area` VALUES ('1418', '回民区', '95', '1', '0');
INSERT INTO `area` VALUES ('1419', '土默特左旗', '95', '1', '0');
INSERT INTO `area` VALUES ('1420', '托克托县', '95', '1', '0');
INSERT INTO `area` VALUES ('1421', '新城区', '95', '1', '0');
INSERT INTO `area` VALUES ('1422', '武川县', '95', '1', '0');
INSERT INTO `area` VALUES ('1423', '清水河县', '95', '1', '0');
INSERT INTO `area` VALUES ('1424', '玉泉区', '95', '1', '0');
INSERT INTO `area` VALUES ('1425', '赛罕区', '95', '1', '0');
INSERT INTO `area` VALUES ('1426', '东河区', '96', '1', '0');
INSERT INTO `area` VALUES ('1427', '九原区', '96', '1', '0');
INSERT INTO `area` VALUES ('1428', '固阳县', '96', '1', '0');
INSERT INTO `area` VALUES ('1429', '土默特右旗', '96', '1', '0');
INSERT INTO `area` VALUES ('1430', '昆都仑区', '96', '1', '0');
INSERT INTO `area` VALUES ('1431', '白云矿区', '96', '1', '0');
INSERT INTO `area` VALUES ('1432', '石拐区', '96', '1', '0');
INSERT INTO `area` VALUES ('1433', '达尔罕茂明安联合旗', '96', '1', '0');
INSERT INTO `area` VALUES ('1434', '青山区', '96', '1', '0');
INSERT INTO `area` VALUES ('1435', '乌达区', '97', '1', '0');
INSERT INTO `area` VALUES ('1436', '海勃湾区', '97', '1', '0');
INSERT INTO `area` VALUES ('1437', '海南区', '97', '1', '0');
INSERT INTO `area` VALUES ('1438', '元宝山区', '98', '1', '0');
INSERT INTO `area` VALUES ('1439', '克什克腾旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1440', '喀喇沁旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1441', '宁城县', '98', '1', '0');
INSERT INTO `area` VALUES ('1442', '巴林右旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1443', '巴林左旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1444', '敖汉旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1445', '松山区', '98', '1', '0');
INSERT INTO `area` VALUES ('1446', '林西县', '98', '1', '0');
INSERT INTO `area` VALUES ('1447', '红山区', '98', '1', '0');
INSERT INTO `area` VALUES ('1448', '翁牛特旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1449', '阿鲁科尔沁旗', '98', '1', '0');
INSERT INTO `area` VALUES ('1450', '奈曼旗', '99', '1', '0');
INSERT INTO `area` VALUES ('1451', '库伦旗', '99', '1', '0');
INSERT INTO `area` VALUES ('1452', '开鲁县', '99', '1', '0');
INSERT INTO `area` VALUES ('1453', '扎鲁特旗', '99', '1', '0');
INSERT INTO `area` VALUES ('1454', '科尔沁区', '99', '1', '0');
INSERT INTO `area` VALUES ('1455', '科尔沁左翼中旗', '99', '1', '0');
INSERT INTO `area` VALUES ('1456', '科尔沁左翼后旗', '99', '1', '0');
INSERT INTO `area` VALUES ('1457', '霍林郭勒市', '99', '1', '0');
INSERT INTO `area` VALUES ('1458', '东胜区', '100', '1', '0');
INSERT INTO `area` VALUES ('1459', '乌审旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1460', '伊金霍洛旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1461', '准格尔旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1462', '杭锦旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1463', '达拉特旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1464', '鄂东胜区', '100', '1', '0');
INSERT INTO `area` VALUES ('1465', '鄂托克前旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1466', '鄂托克旗', '100', '1', '0');
INSERT INTO `area` VALUES ('1467', '扎兰屯市', '101', '1', '0');
INSERT INTO `area` VALUES ('1468', '新巴尔虎右旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1469', '新巴尔虎左旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1470', '根河市', '101', '1', '0');
INSERT INTO `area` VALUES ('1471', '海拉尔区', '101', '1', '0');
INSERT INTO `area` VALUES ('1472', '满洲里市', '101', '1', '0');
INSERT INTO `area` VALUES ('1473', '牙克石市', '101', '1', '0');
INSERT INTO `area` VALUES ('1474', '莫力达瓦达斡尔族自治旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1475', '鄂伦春自治旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1476', '鄂温克族自治旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1477', '阿荣旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1478', '陈巴尔虎旗', '101', '1', '0');
INSERT INTO `area` VALUES ('1479', '额尔古纳市', '101', '1', '0');
INSERT INTO `area` VALUES ('1480', '临河区', '102', '1', '0');
INSERT INTO `area` VALUES ('1481', '乌拉特中旗', '102', '1', '0');
INSERT INTO `area` VALUES ('1482', '乌拉特前旗', '102', '1', '0');
INSERT INTO `area` VALUES ('1483', '乌拉特后旗', '102', '1', '0');
INSERT INTO `area` VALUES ('1484', '五原县', '102', '1', '0');
INSERT INTO `area` VALUES ('1485', '杭锦后旗', '102', '1', '0');
INSERT INTO `area` VALUES ('1486', '磴口县', '102', '1', '0');
INSERT INTO `area` VALUES ('1487', '丰镇市', '103', '1', '0');
INSERT INTO `area` VALUES ('1488', '兴和县', '103', '1', '0');
INSERT INTO `area` VALUES ('1489', '凉城县', '103', '1', '0');
INSERT INTO `area` VALUES ('1490', '化德县', '103', '1', '0');
INSERT INTO `area` VALUES ('1491', '卓资县', '103', '1', '0');
INSERT INTO `area` VALUES ('1492', '商都县', '103', '1', '0');
INSERT INTO `area` VALUES ('1493', '四子王旗', '103', '1', '0');
INSERT INTO `area` VALUES ('1494', '察哈尔右翼中旗', '103', '1', '0');
INSERT INTO `area` VALUES ('1495', '察哈尔右翼前旗', '103', '1', '0');
INSERT INTO `area` VALUES ('1496', '察哈尔右翼后旗', '103', '1', '0');
INSERT INTO `area` VALUES ('1497', '集宁区', '103', '1', '0');
INSERT INTO `area` VALUES ('1498', '乌兰浩特市', '104', '1', '0');
INSERT INTO `area` VALUES ('1499', '扎赉特旗', '104', '1', '0');
INSERT INTO `area` VALUES ('1500', '科尔沁右翼中旗', '104', '1', '0');
INSERT INTO `area` VALUES ('1501', '科尔沁右翼前旗', '104', '1', '0');
INSERT INTO `area` VALUES ('1502', '突泉县', '104', '1', '0');
INSERT INTO `area` VALUES ('1503', '阿尔山市', '104', '1', '0');
INSERT INTO `area` VALUES ('1504', '东乌珠穆沁旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1505', '二连浩特市', '105', '1', '0');
INSERT INTO `area` VALUES ('1506', '多伦县', '105', '1', '0');
INSERT INTO `area` VALUES ('1507', '太仆寺旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1508', '正蓝旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1509', '正镶白旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1510', '苏尼特右旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1511', '苏尼特左旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1512', '西乌珠穆沁旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1513', '锡林浩特市', '105', '1', '0');
INSERT INTO `area` VALUES ('1514', '镶黄旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1515', '阿巴嘎旗', '105', '1', '0');
INSERT INTO `area` VALUES ('1516', '阿拉善右旗', '106', '1', '0');
INSERT INTO `area` VALUES ('1517', '阿拉善左旗', '106', '1', '0');
INSERT INTO `area` VALUES ('1518', '额济纳旗', '106', '1', '0');
INSERT INTO `area` VALUES ('1519', '东陵区', '107', '1', '0');
INSERT INTO `area` VALUES ('1520', '于洪区', '107', '1', '0');
INSERT INTO `area` VALUES ('1521', '和平区', '107', '1', '0');
INSERT INTO `area` VALUES ('1522', '大东区', '107', '1', '0');
INSERT INTO `area` VALUES ('1523', '康平县', '107', '1', '0');
INSERT INTO `area` VALUES ('1524', '新民市', '107', '1', '0');
INSERT INTO `area` VALUES ('1525', '沈北新区', '107', '1', '0');
INSERT INTO `area` VALUES ('1526', '沈河区', '107', '1', '0');
INSERT INTO `area` VALUES ('1527', '法库县', '107', '1', '0');
INSERT INTO `area` VALUES ('1528', '皇姑区', '107', '1', '0');
INSERT INTO `area` VALUES ('1529', '苏家屯区', '107', '1', '0');
INSERT INTO `area` VALUES ('1530', '辽中县', '107', '1', '0');
INSERT INTO `area` VALUES ('1531', '铁西区', '107', '1', '0');
INSERT INTO `area` VALUES ('1532', '中山区', '108', '1', '0');
INSERT INTO `area` VALUES ('1533', '庄河市', '108', '1', '0');
INSERT INTO `area` VALUES ('1534', '旅顺口区', '108', '1', '0');
INSERT INTO `area` VALUES ('1535', '普兰店市', '108', '1', '0');
INSERT INTO `area` VALUES ('1536', '沙河口区', '108', '1', '0');
INSERT INTO `area` VALUES ('1537', '瓦房店市', '108', '1', '0');
INSERT INTO `area` VALUES ('1538', '甘井子区', '108', '1', '0');
INSERT INTO `area` VALUES ('1539', '西岗区', '108', '1', '0');
INSERT INTO `area` VALUES ('1540', '金州区', '108', '1', '0');
INSERT INTO `area` VALUES ('1541', '长海县', '108', '1', '0');
INSERT INTO `area` VALUES ('1542', '千山区', '109', '1', '0');
INSERT INTO `area` VALUES ('1543', '台安县', '109', '1', '0');
INSERT INTO `area` VALUES ('1544', '岫岩满族自治县', '109', '1', '0');
INSERT INTO `area` VALUES ('1545', '海城市', '109', '1', '0');
INSERT INTO `area` VALUES ('1546', '立山区', '109', '1', '0');
INSERT INTO `area` VALUES ('1547', '铁东区', '109', '1', '0');
INSERT INTO `area` VALUES ('1548', '铁西区', '109', '1', '0');
INSERT INTO `area` VALUES ('1549', '东洲区', '110', '1', '0');
INSERT INTO `area` VALUES ('1550', '抚顺县', '110', '1', '0');
INSERT INTO `area` VALUES ('1551', '新宾满族自治县', '110', '1', '0');
INSERT INTO `area` VALUES ('1552', '新抚区', '110', '1', '0');
INSERT INTO `area` VALUES ('1553', '望花区', '110', '1', '0');
INSERT INTO `area` VALUES ('1554', '清原满族自治县', '110', '1', '0');
INSERT INTO `area` VALUES ('1555', '顺城区', '110', '1', '0');
INSERT INTO `area` VALUES ('1556', '南芬区', '111', '1', '0');
INSERT INTO `area` VALUES ('1557', '平山区', '111', '1', '0');
INSERT INTO `area` VALUES ('1558', '明山区', '111', '1', '0');
INSERT INTO `area` VALUES ('1559', '本溪满族自治县', '111', '1', '0');
INSERT INTO `area` VALUES ('1560', '桓仁满族自治县', '111', '1', '0');
INSERT INTO `area` VALUES ('1561', '溪湖区', '111', '1', '0');
INSERT INTO `area` VALUES ('1562', '东港市', '112', '1', '0');
INSERT INTO `area` VALUES ('1563', '元宝区', '112', '1', '0');
INSERT INTO `area` VALUES ('1564', '凤城市', '112', '1', '0');
INSERT INTO `area` VALUES ('1565', '宽甸满族自治县', '112', '1', '0');
INSERT INTO `area` VALUES ('1566', '振兴区', '112', '1', '0');
INSERT INTO `area` VALUES ('1567', '振安区', '112', '1', '0');
INSERT INTO `area` VALUES ('1568', '义县', '113', '1', '0');
INSERT INTO `area` VALUES ('1569', '凌河区', '113', '1', '0');
INSERT INTO `area` VALUES ('1570', '凌海市', '113', '1', '0');
INSERT INTO `area` VALUES ('1571', '北镇市', '113', '1', '0');
INSERT INTO `area` VALUES ('1572', '古塔区', '113', '1', '0');
INSERT INTO `area` VALUES ('1573', '太和区', '113', '1', '0');
INSERT INTO `area` VALUES ('1574', '黑山县', '113', '1', '0');
INSERT INTO `area` VALUES ('1575', '大石桥市', '114', '1', '0');
INSERT INTO `area` VALUES ('1576', '盖州市', '114', '1', '0');
INSERT INTO `area` VALUES ('1577', '站前区', '114', '1', '0');
INSERT INTO `area` VALUES ('1578', '老边区', '114', '1', '0');
INSERT INTO `area` VALUES ('1579', '西市区', '114', '1', '0');
INSERT INTO `area` VALUES ('1580', '鲅鱼圈区', '114', '1', '0');
INSERT INTO `area` VALUES ('1581', '太平区', '115', '1', '0');
INSERT INTO `area` VALUES ('1582', '彰武县', '115', '1', '0');
INSERT INTO `area` VALUES ('1583', '新邱区', '115', '1', '0');
INSERT INTO `area` VALUES ('1584', '海州区', '115', '1', '0');
INSERT INTO `area` VALUES ('1585', '清河门区', '115', '1', '0');
INSERT INTO `area` VALUES ('1586', '细河区', '115', '1', '0');
INSERT INTO `area` VALUES ('1587', '蒙古族自治县', '115', '1', '0');
INSERT INTO `area` VALUES ('1588', '太子河区', '116', '1', '0');
INSERT INTO `area` VALUES ('1589', '宏伟区', '116', '1', '0');
INSERT INTO `area` VALUES ('1590', '弓长岭区', '116', '1', '0');
INSERT INTO `area` VALUES ('1591', '文圣区', '116', '1', '0');
INSERT INTO `area` VALUES ('1592', '灯塔市', '116', '1', '0');
INSERT INTO `area` VALUES ('1593', '白塔区', '116', '1', '0');
INSERT INTO `area` VALUES ('1594', '辽阳县', '116', '1', '0');
INSERT INTO `area` VALUES ('1595', '兴隆台区', '117', '1', '0');
INSERT INTO `area` VALUES ('1596', '双台子区', '117', '1', '0');
INSERT INTO `area` VALUES ('1597', '大洼县', '117', '1', '0');
INSERT INTO `area` VALUES ('1598', '盘山县', '117', '1', '0');
INSERT INTO `area` VALUES ('1599', '开原市', '118', '1', '0');
INSERT INTO `area` VALUES ('1600', '昌图县', '118', '1', '0');
INSERT INTO `area` VALUES ('1601', '清河区', '118', '1', '0');
INSERT INTO `area` VALUES ('1602', '西丰县', '118', '1', '0');
INSERT INTO `area` VALUES ('1603', '调兵山市', '118', '1', '0');
INSERT INTO `area` VALUES ('1604', '铁岭县', '118', '1', '0');
INSERT INTO `area` VALUES ('1605', '银州区', '118', '1', '0');
INSERT INTO `area` VALUES ('1606', '凌源市', '119', '1', '0');
INSERT INTO `area` VALUES ('1607', '北票市', '119', '1', '0');
INSERT INTO `area` VALUES ('1608', '双塔区', '119', '1', '0');
INSERT INTO `area` VALUES ('1609', '喀喇沁左翼蒙古族自治县', '119', '1', '0');
INSERT INTO `area` VALUES ('1610', '建平县', '119', '1', '0');
INSERT INTO `area` VALUES ('1611', '朝阳县', '119', '1', '0');
INSERT INTO `area` VALUES ('1612', '龙城区', '119', '1', '0');
INSERT INTO `area` VALUES ('1613', '兴城市', '120', '1', '0');
INSERT INTO `area` VALUES ('1614', '南票区', '120', '1', '0');
INSERT INTO `area` VALUES ('1615', '建昌县', '120', '1', '0');
INSERT INTO `area` VALUES ('1616', '绥中县', '120', '1', '0');
INSERT INTO `area` VALUES ('1617', '连山区', '120', '1', '0');
INSERT INTO `area` VALUES ('1618', '龙港区', '120', '1', '0');
INSERT INTO `area` VALUES ('1619', '九台市', '121', '1', '0');
INSERT INTO `area` VALUES ('1620', '二道区', '121', '1', '0');
INSERT INTO `area` VALUES ('1621', '农安县', '121', '1', '0');
INSERT INTO `area` VALUES ('1622', '南关区', '121', '1', '0');
INSERT INTO `area` VALUES ('1623', '双阳区', '121', '1', '0');
INSERT INTO `area` VALUES ('1624', '宽城区', '121', '1', '0');
INSERT INTO `area` VALUES ('1625', '德惠市', '121', '1', '0');
INSERT INTO `area` VALUES ('1626', '朝阳区', '121', '1', '0');
INSERT INTO `area` VALUES ('1627', '榆树市', '121', '1', '0');
INSERT INTO `area` VALUES ('1628', '绿园区', '121', '1', '0');
INSERT INTO `area` VALUES ('1629', '丰满区', '122', '1', '0');
INSERT INTO `area` VALUES ('1630', '昌邑区', '122', '1', '0');
INSERT INTO `area` VALUES ('1631', '桦甸市', '122', '1', '0');
INSERT INTO `area` VALUES ('1632', '永吉县', '122', '1', '0');
INSERT INTO `area` VALUES ('1633', '磐石市', '122', '1', '0');
INSERT INTO `area` VALUES ('1634', '舒兰市', '122', '1', '0');
INSERT INTO `area` VALUES ('1635', '船营区', '122', '1', '0');
INSERT INTO `area` VALUES ('1636', '蛟河市', '122', '1', '0');
INSERT INTO `area` VALUES ('1637', '龙潭区', '122', '1', '0');
INSERT INTO `area` VALUES ('1638', '伊通满族自治县', '123', '1', '0');
INSERT INTO `area` VALUES ('1639', '公主岭市', '123', '1', '0');
INSERT INTO `area` VALUES ('1640', '双辽市', '123', '1', '0');
INSERT INTO `area` VALUES ('1641', '梨树县', '123', '1', '0');
INSERT INTO `area` VALUES ('1642', '铁东区', '123', '1', '0');
INSERT INTO `area` VALUES ('1643', '铁西区', '123', '1', '0');
INSERT INTO `area` VALUES ('1644', '东丰县', '124', '1', '0');
INSERT INTO `area` VALUES ('1645', '东辽县', '124', '1', '0');
INSERT INTO `area` VALUES ('1646', '西安区', '124', '1', '0');
INSERT INTO `area` VALUES ('1647', '龙山区', '124', '1', '0');
INSERT INTO `area` VALUES ('1648', '东昌区', '125', '1', '0');
INSERT INTO `area` VALUES ('1649', '二道江区', '125', '1', '0');
INSERT INTO `area` VALUES ('1650', '柳河县', '125', '1', '0');
INSERT INTO `area` VALUES ('1651', '梅河口市', '125', '1', '0');
INSERT INTO `area` VALUES ('1652', '辉南县', '125', '1', '0');
INSERT INTO `area` VALUES ('1653', '通化县', '125', '1', '0');
INSERT INTO `area` VALUES ('1654', '集安市', '125', '1', '0');
INSERT INTO `area` VALUES ('1655', '临江市', '126', '1', '0');
INSERT INTO `area` VALUES ('1656', '八道江区', '126', '1', '0');
INSERT INTO `area` VALUES ('1657', '抚松县', '126', '1', '0');
INSERT INTO `area` VALUES ('1658', '江源区', '126', '1', '0');
INSERT INTO `area` VALUES ('1659', '长白朝鲜族自治县', '126', '1', '0');
INSERT INTO `area` VALUES ('1660', '靖宇县', '126', '1', '0');
INSERT INTO `area` VALUES ('1661', '干安县', '127', '1', '0');
INSERT INTO `area` VALUES ('1662', '前郭尔罗斯蒙古族自治县', '127', '1', '0');
INSERT INTO `area` VALUES ('1663', '宁江区', '127', '1', '0');
INSERT INTO `area` VALUES ('1664', '扶余县', '127', '1', '0');
INSERT INTO `area` VALUES ('1665', '长岭县', '127', '1', '0');
INSERT INTO `area` VALUES ('1666', '大安市', '128', '1', '0');
INSERT INTO `area` VALUES ('1667', '洮北区', '128', '1', '0');
INSERT INTO `area` VALUES ('1668', '洮南市', '128', '1', '0');
INSERT INTO `area` VALUES ('1669', '通榆县', '128', '1', '0');
INSERT INTO `area` VALUES ('1670', '镇赉县', '128', '1', '0');
INSERT INTO `area` VALUES ('1671', '和龙市', '129', '1', '0');
INSERT INTO `area` VALUES ('1672', '图们市', '129', '1', '0');
INSERT INTO `area` VALUES ('1673', '安图县', '129', '1', '0');
INSERT INTO `area` VALUES ('1674', '延吉市', '129', '1', '0');
INSERT INTO `area` VALUES ('1675', '敦化市', '129', '1', '0');
INSERT INTO `area` VALUES ('1676', '汪清县', '129', '1', '0');
INSERT INTO `area` VALUES ('1677', '珲春市', '129', '1', '0');
INSERT INTO `area` VALUES ('1678', '龙井市', '129', '1', '0');
INSERT INTO `area` VALUES ('1679', '五常市', '130', '1', '0');
INSERT INTO `area` VALUES ('1680', '依兰县', '130', '1', '0');
INSERT INTO `area` VALUES ('1681', '南岗区', '130', '1', '0');
INSERT INTO `area` VALUES ('1682', '双城市', '130', '1', '0');
INSERT INTO `area` VALUES ('1683', '呼兰区', '130', '1', '0');
INSERT INTO `area` VALUES ('1684', '哈尔滨市道里区', '130', '1', '0');
INSERT INTO `area` VALUES ('1685', '宾县', '130', '1', '0');
INSERT INTO `area` VALUES ('1686', '尚志市', '130', '1', '0');
INSERT INTO `area` VALUES ('1687', '巴彦县', '130', '1', '0');
INSERT INTO `area` VALUES ('1688', '平房区', '130', '1', '0');
INSERT INTO `area` VALUES ('1689', '延寿县', '130', '1', '0');
INSERT INTO `area` VALUES ('1690', '方正县', '130', '1', '0');
INSERT INTO `area` VALUES ('1691', '木兰县', '130', '1', '0');
INSERT INTO `area` VALUES ('1692', '松北区', '130', '1', '0');
INSERT INTO `area` VALUES ('1693', '通河县', '130', '1', '0');
INSERT INTO `area` VALUES ('1694', '道外区', '130', '1', '0');
INSERT INTO `area` VALUES ('1695', '阿城区', '130', '1', '0');
INSERT INTO `area` VALUES ('1696', '香坊区', '130', '1', '0');
INSERT INTO `area` VALUES ('1697', '依安县', '131', '1', '0');
INSERT INTO `area` VALUES ('1698', '克东县', '131', '1', '0');
INSERT INTO `area` VALUES ('1699', '克山县', '131', '1', '0');
INSERT INTO `area` VALUES ('1700', '富拉尔基区', '131', '1', '0');
INSERT INTO `area` VALUES ('1701', '富裕县', '131', '1', '0');
INSERT INTO `area` VALUES ('1702', '建华区', '131', '1', '0');
INSERT INTO `area` VALUES ('1703', '拜泉县', '131', '1', '0');
INSERT INTO `area` VALUES ('1704', '昂昂溪区', '131', '1', '0');
INSERT INTO `area` VALUES ('1705', '梅里斯达斡尔族区', '131', '1', '0');
INSERT INTO `area` VALUES ('1706', '泰来县', '131', '1', '0');
INSERT INTO `area` VALUES ('1707', '甘南县', '131', '1', '0');
INSERT INTO `area` VALUES ('1708', '碾子山区', '131', '1', '0');
INSERT INTO `area` VALUES ('1709', '讷河市', '131', '1', '0');
INSERT INTO `area` VALUES ('1710', '铁锋区', '131', '1', '0');
INSERT INTO `area` VALUES ('1711', '龙江县', '131', '1', '0');
INSERT INTO `area` VALUES ('1712', '龙沙区', '131', '1', '0');
INSERT INTO `area` VALUES ('1713', '城子河区', '132', '1', '0');
INSERT INTO `area` VALUES ('1714', '密山市', '132', '1', '0');
INSERT INTO `area` VALUES ('1715', '恒山区', '132', '1', '0');
INSERT INTO `area` VALUES ('1716', '梨树区', '132', '1', '0');
INSERT INTO `area` VALUES ('1717', '滴道区', '132', '1', '0');
INSERT INTO `area` VALUES ('1718', '虎林市', '132', '1', '0');
INSERT INTO `area` VALUES ('1719', '鸡东县', '132', '1', '0');
INSERT INTO `area` VALUES ('1720', '鸡冠区', '132', '1', '0');
INSERT INTO `area` VALUES ('1721', '麻山区', '132', '1', '0');
INSERT INTO `area` VALUES ('1722', '东山区', '133', '1', '0');
INSERT INTO `area` VALUES ('1723', '兴安区', '133', '1', '0');
INSERT INTO `area` VALUES ('1724', '兴山区', '133', '1', '0');
INSERT INTO `area` VALUES ('1725', '南山区', '133', '1', '0');
INSERT INTO `area` VALUES ('1726', '向阳区', '133', '1', '0');
INSERT INTO `area` VALUES ('1727', '工农区', '133', '1', '0');
INSERT INTO `area` VALUES ('1728', '绥滨县', '133', '1', '0');
INSERT INTO `area` VALUES ('1729', '萝北县', '133', '1', '0');
INSERT INTO `area` VALUES ('1730', '友谊县', '134', '1', '0');
INSERT INTO `area` VALUES ('1731', '四方台区', '134', '1', '0');
INSERT INTO `area` VALUES ('1732', '宝山区', '134', '1', '0');
INSERT INTO `area` VALUES ('1733', '宝清县', '134', '1', '0');
INSERT INTO `area` VALUES ('1734', '尖山区', '134', '1', '0');
INSERT INTO `area` VALUES ('1735', '岭东区', '134', '1', '0');
INSERT INTO `area` VALUES ('1736', '集贤县', '134', '1', '0');
INSERT INTO `area` VALUES ('1737', '饶河县', '134', '1', '0');
INSERT INTO `area` VALUES ('1738', '大同区', '135', '1', '0');
INSERT INTO `area` VALUES ('1739', '杜尔伯特蒙古族自治县', '135', '1', '0');
INSERT INTO `area` VALUES ('1740', '林甸县', '135', '1', '0');
INSERT INTO `area` VALUES ('1741', '红岗区', '135', '1', '0');
INSERT INTO `area` VALUES ('1742', '肇州县', '135', '1', '0');
INSERT INTO `area` VALUES ('1743', '肇源县', '135', '1', '0');
INSERT INTO `area` VALUES ('1744', '胡路区', '135', '1', '0');
INSERT INTO `area` VALUES ('1745', '萨尔图区', '135', '1', '0');
INSERT INTO `area` VALUES ('1746', '龙凤区', '135', '1', '0');
INSERT INTO `area` VALUES ('1747', '上甘岭区', '136', '1', '0');
INSERT INTO `area` VALUES ('1748', '乌伊岭区', '136', '1', '0');
INSERT INTO `area` VALUES ('1749', '乌马河区', '136', '1', '0');
INSERT INTO `area` VALUES ('1750', '五营区', '136', '1', '0');
INSERT INTO `area` VALUES ('1751', '伊春区', '136', '1', '0');
INSERT INTO `area` VALUES ('1752', '南岔区', '136', '1', '0');
INSERT INTO `area` VALUES ('1753', '友好区', '136', '1', '0');
INSERT INTO `area` VALUES ('1754', '嘉荫县', '136', '1', '0');
INSERT INTO `area` VALUES ('1755', '带岭区', '136', '1', '0');
INSERT INTO `area` VALUES ('1756', '新青区', '136', '1', '0');
INSERT INTO `area` VALUES ('1757', '汤旺河区', '136', '1', '0');
INSERT INTO `area` VALUES ('1758', '红星区', '136', '1', '0');
INSERT INTO `area` VALUES ('1759', '美溪区', '136', '1', '0');
INSERT INTO `area` VALUES ('1760', '翠峦区', '136', '1', '0');
INSERT INTO `area` VALUES ('1761', '西林区', '136', '1', '0');
INSERT INTO `area` VALUES ('1762', '金山屯区', '136', '1', '0');
INSERT INTO `area` VALUES ('1763', '铁力市', '136', '1', '0');
INSERT INTO `area` VALUES ('1764', '东风区', '137', '1', '0');
INSERT INTO `area` VALUES ('1765', '前进区', '137', '1', '0');
INSERT INTO `area` VALUES ('1766', '同江市', '137', '1', '0');
INSERT INTO `area` VALUES ('1767', '向阳区', '137', '1', '0');
INSERT INTO `area` VALUES ('1768', '富锦市', '137', '1', '0');
INSERT INTO `area` VALUES ('1769', '抚远县', '137', '1', '0');
INSERT INTO `area` VALUES ('1770', '桦南县', '137', '1', '0');
INSERT INTO `area` VALUES ('1771', '桦川县', '137', '1', '0');
INSERT INTO `area` VALUES ('1772', '汤原县', '137', '1', '0');
INSERT INTO `area` VALUES ('1773', '郊区', '137', '1', '0');
INSERT INTO `area` VALUES ('1774', '勃利县', '138', '1', '0');
INSERT INTO `area` VALUES ('1775', '新兴区', '138', '1', '0');
INSERT INTO `area` VALUES ('1776', '桃山区', '138', '1', '0');
INSERT INTO `area` VALUES ('1777', '茄子河区', '138', '1', '0');
INSERT INTO `area` VALUES ('1778', '东宁县', '139', '1', '0');
INSERT INTO `area` VALUES ('1779', '东安区', '139', '1', '0');
INSERT INTO `area` VALUES ('1780', '宁安市', '139', '1', '0');
INSERT INTO `area` VALUES ('1781', '林口县', '139', '1', '0');
INSERT INTO `area` VALUES ('1782', '海林市', '139', '1', '0');
INSERT INTO `area` VALUES ('1783', '爱民区', '139', '1', '0');
INSERT INTO `area` VALUES ('1784', '穆棱市', '139', '1', '0');
INSERT INTO `area` VALUES ('1785', '绥芬河市', '139', '1', '0');
INSERT INTO `area` VALUES ('1786', '西安区', '139', '1', '0');
INSERT INTO `area` VALUES ('1787', '阳明区', '139', '1', '0');
INSERT INTO `area` VALUES ('1788', '五大连池市', '140', '1', '0');
INSERT INTO `area` VALUES ('1789', '北安市', '140', '1', '0');
INSERT INTO `area` VALUES ('1790', '嫩江县', '140', '1', '0');
INSERT INTO `area` VALUES ('1791', '孙吴县', '140', '1', '0');
INSERT INTO `area` VALUES ('1792', '爱辉区', '140', '1', '0');
INSERT INTO `area` VALUES ('1793', '车逊克县', '140', '1', '0');
INSERT INTO `area` VALUES ('1794', '逊克县', '140', '1', '0');
INSERT INTO `area` VALUES ('1795', '兰西县', '141', '1', '0');
INSERT INTO `area` VALUES ('1796', '安达市', '141', '1', '0');
INSERT INTO `area` VALUES ('1797', '庆安县', '141', '1', '0');
INSERT INTO `area` VALUES ('1798', '明水县', '141', '1', '0');
INSERT INTO `area` VALUES ('1799', '望奎县', '141', '1', '0');
INSERT INTO `area` VALUES ('1800', '海伦市', '141', '1', '0');
INSERT INTO `area` VALUES ('1801', '绥化市北林区', '141', '1', '0');
INSERT INTO `area` VALUES ('1802', '绥棱县', '141', '1', '0');
INSERT INTO `area` VALUES ('1803', '肇东市', '141', '1', '0');
INSERT INTO `area` VALUES ('1804', '青冈县', '141', '1', '0');
INSERT INTO `area` VALUES ('1805', '呼玛县', '142', '1', '0');
INSERT INTO `area` VALUES ('1806', '塔河县', '142', '1', '0');
INSERT INTO `area` VALUES ('1807', '大兴安岭地区加格达奇区', '142', '1', '0');
INSERT INTO `area` VALUES ('1808', '大兴安岭地区呼中区', '142', '1', '0');
INSERT INTO `area` VALUES ('1809', '大兴安岭地区新林区', '142', '1', '0');
INSERT INTO `area` VALUES ('1810', '大兴安岭地区松岭区', '142', '1', '0');
INSERT INTO `area` VALUES ('1811', '漠河县', '142', '1', '0');
INSERT INTO `area` VALUES ('2027', '下关区', '162', '1', '0');
INSERT INTO `area` VALUES ('2028', '六合区', '162', '1', '0');
INSERT INTO `area` VALUES ('2029', '建邺区', '162', '1', '0');
INSERT INTO `area` VALUES ('2030', '栖霞区', '162', '1', '0');
INSERT INTO `area` VALUES ('2031', '江宁区', '162', '1', '0');
INSERT INTO `area` VALUES ('2032', '浦口区', '162', '1', '0');
INSERT INTO `area` VALUES ('2033', '溧水县', '162', '1', '0');
INSERT INTO `area` VALUES ('2034', '玄武区', '162', '1', '0');
INSERT INTO `area` VALUES ('2035', '白下区', '162', '1', '0');
INSERT INTO `area` VALUES ('2036', '秦淮区', '162', '1', '0');
INSERT INTO `area` VALUES ('2037', '雨花台区', '162', '1', '0');
INSERT INTO `area` VALUES ('2038', '高淳县', '162', '1', '0');
INSERT INTO `area` VALUES ('2039', '鼓楼区', '162', '1', '0');
INSERT INTO `area` VALUES ('2040', '北塘区', '163', '1', '0');
INSERT INTO `area` VALUES ('2041', '南长区', '163', '1', '0');
INSERT INTO `area` VALUES ('2042', '宜兴市', '163', '1', '0');
INSERT INTO `area` VALUES ('2043', '崇安区', '163', '1', '0');
INSERT INTO `area` VALUES ('2044', '惠山区', '163', '1', '0');
INSERT INTO `area` VALUES ('2045', '江阴市', '163', '1', '0');
INSERT INTO `area` VALUES ('2046', '滨湖区', '163', '1', '0');
INSERT INTO `area` VALUES ('2047', '锡山区', '163', '1', '0');
INSERT INTO `area` VALUES ('2048', '丰县', '164', '1', '0');
INSERT INTO `area` VALUES ('2049', '九里区', '164', '1', '0');
INSERT INTO `area` VALUES ('2050', '云龙区', '164', '1', '0');
INSERT INTO `area` VALUES ('2051', '新沂市', '164', '1', '0');
INSERT INTO `area` VALUES ('2052', '沛县', '164', '1', '0');
INSERT INTO `area` VALUES ('2053', '泉山区', '164', '1', '0');
INSERT INTO `area` VALUES ('2054', '睢宁县', '164', '1', '0');
INSERT INTO `area` VALUES ('2055', '贾汪区', '164', '1', '0');
INSERT INTO `area` VALUES ('2056', '邳州市', '164', '1', '0');
INSERT INTO `area` VALUES ('2057', '铜山县', '164', '1', '0');
INSERT INTO `area` VALUES ('2058', '鼓楼区', '164', '1', '0');
INSERT INTO `area` VALUES ('2059', '天宁区', '165', '1', '0');
INSERT INTO `area` VALUES ('2060', '戚墅堰区', '165', '1', '0');
INSERT INTO `area` VALUES ('2061', '新北区', '165', '1', '0');
INSERT INTO `area` VALUES ('2062', '武进区', '165', '1', '0');
INSERT INTO `area` VALUES ('2063', '溧阳市', '165', '1', '0');
INSERT INTO `area` VALUES ('2064', '金坛市', '165', '1', '0');
INSERT INTO `area` VALUES ('2065', '钟楼区', '165', '1', '0');
INSERT INTO `area` VALUES ('2066', '吴中区', '166', '1', '0');
INSERT INTO `area` VALUES ('2067', '吴江市', '166', '1', '0');
INSERT INTO `area` VALUES ('2068', '太仓市', '166', '1', '0');
INSERT INTO `area` VALUES ('2069', '常熟市', '166', '1', '0');
INSERT INTO `area` VALUES ('2070', '平江区', '166', '1', '0');
INSERT INTO `area` VALUES ('2071', '张家港市', '166', '1', '0');
INSERT INTO `area` VALUES ('2072', '昆山市', '166', '1', '0');
INSERT INTO `area` VALUES ('2073', '沧浪区', '166', '1', '0');
INSERT INTO `area` VALUES ('2074', '相城区', '166', '1', '0');
INSERT INTO `area` VALUES ('2075', '苏州工业园区', '166', '1', '0');
INSERT INTO `area` VALUES ('2076', '虎丘区', '166', '1', '0');
INSERT INTO `area` VALUES ('2077', '金阊区', '166', '1', '0');
INSERT INTO `area` VALUES ('2078', '启东市', '167', '1', '0');
INSERT INTO `area` VALUES ('2079', '如东县', '167', '1', '0');
INSERT INTO `area` VALUES ('2080', '如皋市', '167', '1', '0');
INSERT INTO `area` VALUES ('2081', '崇川区', '167', '1', '0');
INSERT INTO `area` VALUES ('2082', '海安县', '167', '1', '0');
INSERT INTO `area` VALUES ('2083', '海门市', '167', '1', '0');
INSERT INTO `area` VALUES ('2084', '港闸区', '167', '1', '0');
INSERT INTO `area` VALUES ('2085', '通州市', '167', '1', '0');
INSERT INTO `area` VALUES ('2086', '东海县', '168', '1', '0');
INSERT INTO `area` VALUES ('2087', '新浦区', '168', '1', '0');
INSERT INTO `area` VALUES ('2088', '海州区', '168', '1', '0');
INSERT INTO `area` VALUES ('2089', '灌云县', '168', '1', '0');
INSERT INTO `area` VALUES ('2090', '灌南县', '168', '1', '0');
INSERT INTO `area` VALUES ('2091', '赣榆县', '168', '1', '0');
INSERT INTO `area` VALUES ('2092', '连云区', '168', '1', '0');
INSERT INTO `area` VALUES ('2093', '楚州区', '169', '1', '0');
INSERT INTO `area` VALUES ('2094', '洪泽县', '169', '1', '0');
INSERT INTO `area` VALUES ('2095', '涟水县', '169', '1', '0');
INSERT INTO `area` VALUES ('2096', '淮阴区', '169', '1', '0');
INSERT INTO `area` VALUES ('2097', '清河区', '169', '1', '0');
INSERT INTO `area` VALUES ('2098', '清浦区', '169', '1', '0');
INSERT INTO `area` VALUES ('2099', '盱眙县', '169', '1', '0');
INSERT INTO `area` VALUES ('2100', '金湖县', '169', '1', '0');
INSERT INTO `area` VALUES ('2101', '东台市', '170', '1', '0');
INSERT INTO `area` VALUES ('2102', '亭湖区', '170', '1', '0');
INSERT INTO `area` VALUES ('2103', '响水县', '170', '1', '0');
INSERT INTO `area` VALUES ('2104', '大丰市', '170', '1', '0');
INSERT INTO `area` VALUES ('2105', '射阳县', '170', '1', '0');
INSERT INTO `area` VALUES ('2106', '建湖县', '170', '1', '0');
INSERT INTO `area` VALUES ('2107', '滨海县', '170', '1', '0');
INSERT INTO `area` VALUES ('2108', '盐都区', '170', '1', '0');
INSERT INTO `area` VALUES ('2109', '阜宁县', '170', '1', '0');
INSERT INTO `area` VALUES ('2110', '仪征市', '171', '1', '0');
INSERT INTO `area` VALUES ('2111', '宝应县', '171', '1', '0');
INSERT INTO `area` VALUES ('2112', '广陵区', '171', '1', '0');
INSERT INTO `area` VALUES ('2113', '江都市', '171', '1', '0');
INSERT INTO `area` VALUES ('2114', '维扬区', '171', '1', '0');
INSERT INTO `area` VALUES ('2115', '邗江区', '171', '1', '0');
INSERT INTO `area` VALUES ('2116', '高邮市', '171', '1', '0');
INSERT INTO `area` VALUES ('2117', '丹徒区', '172', '1', '0');
INSERT INTO `area` VALUES ('2118', '丹阳市', '172', '1', '0');
INSERT INTO `area` VALUES ('2119', '京口区', '172', '1', '0');
INSERT INTO `area` VALUES ('2120', '句容市', '172', '1', '0');
INSERT INTO `area` VALUES ('2121', '扬中市', '172', '1', '0');
INSERT INTO `area` VALUES ('2122', '润州区', '172', '1', '0');
INSERT INTO `area` VALUES ('2123', '兴化市', '173', '1', '0');
INSERT INTO `area` VALUES ('2124', '姜堰市', '173', '1', '0');
INSERT INTO `area` VALUES ('2125', '泰兴市', '173', '1', '0');
INSERT INTO `area` VALUES ('2126', '海陵区', '173', '1', '0');
INSERT INTO `area` VALUES ('2127', '靖江市', '173', '1', '0');
INSERT INTO `area` VALUES ('2128', '高港区', '173', '1', '0');
INSERT INTO `area` VALUES ('2129', '宿城区', '174', '1', '0');
INSERT INTO `area` VALUES ('2130', '宿豫区', '174', '1', '0');
INSERT INTO `area` VALUES ('2131', '沭阳县', '174', '1', '0');
INSERT INTO `area` VALUES ('2132', '泗洪县', '174', '1', '0');
INSERT INTO `area` VALUES ('2133', '泗阳县', '174', '1', '0');
INSERT INTO `area` VALUES ('2134', '上城区', '175', '1', '0');
INSERT INTO `area` VALUES ('2135', '下城区', '175', '1', '0');
INSERT INTO `area` VALUES ('2136', '临安市', '175', '1', '0');
INSERT INTO `area` VALUES ('2137', '余杭区', '175', '1', '0');
INSERT INTO `area` VALUES ('2138', '富阳市', '175', '1', '0');
INSERT INTO `area` VALUES ('2139', '建德市', '175', '1', '0');
INSERT INTO `area` VALUES ('2140', '拱墅区', '175', '1', '0');
INSERT INTO `area` VALUES ('2141', '桐庐县', '175', '1', '0');
INSERT INTO `area` VALUES ('2142', '江干区', '175', '1', '0');
INSERT INTO `area` VALUES ('2143', '淳安县', '175', '1', '0');
INSERT INTO `area` VALUES ('2144', '滨江区', '175', '1', '0');
INSERT INTO `area` VALUES ('2145', '萧山区', '175', '1', '0');
INSERT INTO `area` VALUES ('2146', '西湖区', '175', '1', '0');
INSERT INTO `area` VALUES ('2147', '余姚市', '176', '1', '0');
INSERT INTO `area` VALUES ('2148', '北仑区', '176', '1', '0');
INSERT INTO `area` VALUES ('2149', '奉化市', '176', '1', '0');
INSERT INTO `area` VALUES ('2150', '宁海县', '176', '1', '0');
INSERT INTO `area` VALUES ('2151', '慈溪市', '176', '1', '0');
INSERT INTO `area` VALUES ('2152', '江东区', '176', '1', '0');
INSERT INTO `area` VALUES ('2153', '江北区', '176', '1', '0');
INSERT INTO `area` VALUES ('2154', '海曙区', '176', '1', '0');
INSERT INTO `area` VALUES ('2155', '象山县', '176', '1', '0');
INSERT INTO `area` VALUES ('2156', '鄞州区', '176', '1', '0');
INSERT INTO `area` VALUES ('2157', '镇海区', '176', '1', '0');
INSERT INTO `area` VALUES ('2158', '乐清市', '177', '1', '0');
INSERT INTO `area` VALUES ('2159', '平阳县', '177', '1', '0');
INSERT INTO `area` VALUES ('2160', '文成县', '177', '1', '0');
INSERT INTO `area` VALUES ('2161', '永嘉县', '177', '1', '0');
INSERT INTO `area` VALUES ('2162', '泰顺县', '177', '1', '0');
INSERT INTO `area` VALUES ('2163', '洞头县', '177', '1', '0');
INSERT INTO `area` VALUES ('2164', '瑞安市', '177', '1', '0');
INSERT INTO `area` VALUES ('2165', '瓯海区', '177', '1', '0');
INSERT INTO `area` VALUES ('2166', '苍南县', '177', '1', '0');
INSERT INTO `area` VALUES ('2167', '鹿城区', '177', '1', '0');
INSERT INTO `area` VALUES ('2168', '龙湾区', '177', '1', '0');
INSERT INTO `area` VALUES ('2169', '南湖区', '178', '1', '0');
INSERT INTO `area` VALUES ('2170', '嘉善县', '178', '1', '0');
INSERT INTO `area` VALUES ('2171', '平湖市', '178', '1', '0');
INSERT INTO `area` VALUES ('2172', '桐乡市', '178', '1', '0');
INSERT INTO `area` VALUES ('2173', '海宁市', '178', '1', '0');
INSERT INTO `area` VALUES ('2174', '海盐县', '178', '1', '0');
INSERT INTO `area` VALUES ('2175', '秀洲区', '178', '1', '0');
INSERT INTO `area` VALUES ('2176', '南浔区', '179', '1', '0');
INSERT INTO `area` VALUES ('2177', '吴兴区', '179', '1', '0');
INSERT INTO `area` VALUES ('2178', '安吉县', '179', '1', '0');
INSERT INTO `area` VALUES ('2179', '德清县', '179', '1', '0');
INSERT INTO `area` VALUES ('2180', '长兴县', '179', '1', '0');
INSERT INTO `area` VALUES ('2181', '上虞市', '180', '1', '0');
INSERT INTO `area` VALUES ('2182', '嵊州市', '180', '1', '0');
INSERT INTO `area` VALUES ('2183', '新昌县', '180', '1', '0');
INSERT INTO `area` VALUES ('2184', '绍兴县', '180', '1', '0');
INSERT INTO `area` VALUES ('2185', '诸暨市', '180', '1', '0');
INSERT INTO `area` VALUES ('2186', '越城区', '180', '1', '0');
INSERT INTO `area` VALUES ('2187', '定海区', '181', '1', '0');
INSERT INTO `area` VALUES ('2188', '岱山县', '181', '1', '0');
INSERT INTO `area` VALUES ('2189', '嵊泗县', '181', '1', '0');
INSERT INTO `area` VALUES ('2190', '普陀区', '181', '1', '0');
INSERT INTO `area` VALUES ('2191', '常山县', '182', '1', '0');
INSERT INTO `area` VALUES ('2192', '开化县', '182', '1', '0');
INSERT INTO `area` VALUES ('2193', '柯城区', '182', '1', '0');
INSERT INTO `area` VALUES ('2194', '江山市', '182', '1', '0');
INSERT INTO `area` VALUES ('2195', '衢江区', '182', '1', '0');
INSERT INTO `area` VALUES ('2196', '龙游县', '182', '1', '0');
INSERT INTO `area` VALUES ('2197', '东阳市', '183', '1', '0');
INSERT INTO `area` VALUES ('2198', '义乌市', '183', '1', '0');
INSERT INTO `area` VALUES ('2199', '兰溪市', '183', '1', '0');
INSERT INTO `area` VALUES ('2200', '婺城区', '183', '1', '0');
INSERT INTO `area` VALUES ('2201', '武义县', '183', '1', '0');
INSERT INTO `area` VALUES ('2202', '永康市', '183', '1', '0');
INSERT INTO `area` VALUES ('2203', '浦江县', '183', '1', '0');
INSERT INTO `area` VALUES ('2204', '磐安县', '183', '1', '0');
INSERT INTO `area` VALUES ('2205', '金东区', '183', '1', '0');
INSERT INTO `area` VALUES ('2206', '三门县', '184', '1', '0');
INSERT INTO `area` VALUES ('2207', '临海市', '184', '1', '0');
INSERT INTO `area` VALUES ('2208', '仙居县', '184', '1', '0');
INSERT INTO `area` VALUES ('2209', '天台县', '184', '1', '0');
INSERT INTO `area` VALUES ('2210', '椒江区', '184', '1', '0');
INSERT INTO `area` VALUES ('2211', '温岭市', '184', '1', '0');
INSERT INTO `area` VALUES ('2212', '玉环县', '184', '1', '0');
INSERT INTO `area` VALUES ('2213', '路桥区', '184', '1', '0');
INSERT INTO `area` VALUES ('2214', '黄岩区', '184', '1', '0');
INSERT INTO `area` VALUES ('2215', '云和县', '185', '1', '0');
INSERT INTO `area` VALUES ('2216', '庆元县', '185', '1', '0');
INSERT INTO `area` VALUES ('2217', '景宁畲族自治县', '185', '1', '0');
INSERT INTO `area` VALUES ('2218', '松阳县', '185', '1', '0');
INSERT INTO `area` VALUES ('2219', '缙云县', '185', '1', '0');
INSERT INTO `area` VALUES ('2220', '莲都区', '185', '1', '0');
INSERT INTO `area` VALUES ('2221', '遂昌县', '185', '1', '0');
INSERT INTO `area` VALUES ('2222', '青田县', '185', '1', '0');
INSERT INTO `area` VALUES ('2223', '龙泉市', '185', '1', '0');
INSERT INTO `area` VALUES ('2224', '包河区', '186', '1', '0');
INSERT INTO `area` VALUES ('2225', '庐阳区', '186', '1', '0');
INSERT INTO `area` VALUES ('2226', '瑶海区', '186', '1', '0');
INSERT INTO `area` VALUES ('2227', '肥东县', '186', '1', '0');
INSERT INTO `area` VALUES ('2228', '肥西县', '186', '1', '0');
INSERT INTO `area` VALUES ('2229', '蜀山区', '186', '1', '0');
INSERT INTO `area` VALUES ('2230', '长丰县', '186', '1', '0');
INSERT INTO `area` VALUES ('2231', '三山区', '187', '1', '0');
INSERT INTO `area` VALUES ('2232', '南陵县', '187', '1', '0');
INSERT INTO `area` VALUES ('2233', '弋江区', '187', '1', '0');
INSERT INTO `area` VALUES ('2234', '繁昌县', '187', '1', '0');
INSERT INTO `area` VALUES ('2235', '芜湖县', '187', '1', '0');
INSERT INTO `area` VALUES ('2236', '镜湖区', '187', '1', '0');
INSERT INTO `area` VALUES ('2237', '鸠江区', '187', '1', '0');
INSERT INTO `area` VALUES ('2238', '五河县', '188', '1', '0');
INSERT INTO `area` VALUES ('2239', '固镇县', '188', '1', '0');
INSERT INTO `area` VALUES ('2240', '怀远县', '188', '1', '0');
INSERT INTO `area` VALUES ('2241', '淮上区', '188', '1', '0');
INSERT INTO `area` VALUES ('2242', '禹会区', '188', '1', '0');
INSERT INTO `area` VALUES ('2243', '蚌山区', '188', '1', '0');
INSERT INTO `area` VALUES ('2244', '龙子湖区', '188', '1', '0');
INSERT INTO `area` VALUES ('2245', '八公山区', '189', '1', '0');
INSERT INTO `area` VALUES ('2246', '凤台县', '189', '1', '0');
INSERT INTO `area` VALUES ('2247', '大通区', '189', '1', '0');
INSERT INTO `area` VALUES ('2248', '潘集区', '189', '1', '0');
INSERT INTO `area` VALUES ('2249', '田家庵区', '189', '1', '0');
INSERT INTO `area` VALUES ('2250', '谢家集区', '189', '1', '0');
INSERT INTO `area` VALUES ('2251', '当涂县', '190', '1', '0');
INSERT INTO `area` VALUES ('2252', '花山区', '190', '1', '0');
INSERT INTO `area` VALUES ('2253', '金家庄区', '190', '1', '0');
INSERT INTO `area` VALUES ('2254', '雨山区', '190', '1', '0');
INSERT INTO `area` VALUES ('2255', '杜集区', '191', '1', '0');
INSERT INTO `area` VALUES ('2256', '濉溪县', '191', '1', '0');
INSERT INTO `area` VALUES ('2257', '烈山区', '191', '1', '0');
INSERT INTO `area` VALUES ('2258', '相山区', '191', '1', '0');
INSERT INTO `area` VALUES ('2259', '狮子山区', '192', '1', '0');
INSERT INTO `area` VALUES ('2260', '郊区', '192', '1', '0');
INSERT INTO `area` VALUES ('2261', '铜官山区', '192', '1', '0');
INSERT INTO `area` VALUES ('2262', '铜陵县', '192', '1', '0');
INSERT INTO `area` VALUES ('2263', '大观区', '193', '1', '0');
INSERT INTO `area` VALUES ('2264', '太湖县', '193', '1', '0');
INSERT INTO `area` VALUES ('2265', '宜秀区', '193', '1', '0');
INSERT INTO `area` VALUES ('2266', '宿松县', '193', '1', '0');
INSERT INTO `area` VALUES ('2267', '岳西县', '193', '1', '0');
INSERT INTO `area` VALUES ('2268', '怀宁县', '193', '1', '0');
INSERT INTO `area` VALUES ('2269', '望江县', '193', '1', '0');
INSERT INTO `area` VALUES ('2270', '枞阳县', '193', '1', '0');
INSERT INTO `area` VALUES ('2271', '桐城市', '193', '1', '0');
INSERT INTO `area` VALUES ('2272', '潜山县', '193', '1', '0');
INSERT INTO `area` VALUES ('2273', '迎江区', '193', '1', '0');
INSERT INTO `area` VALUES ('2274', '休宁县', '194', '1', '0');
INSERT INTO `area` VALUES ('2275', '屯溪区', '194', '1', '0');
INSERT INTO `area` VALUES ('2276', '徽州区', '194', '1', '0');
INSERT INTO `area` VALUES ('2277', '歙县', '194', '1', '0');
INSERT INTO `area` VALUES ('2278', '祁门县', '194', '1', '0');
INSERT INTO `area` VALUES ('2279', '黄山区', '194', '1', '0');
INSERT INTO `area` VALUES ('2280', '黟县', '194', '1', '0');
INSERT INTO `area` VALUES ('2281', '全椒县', '195', '1', '0');
INSERT INTO `area` VALUES ('2282', '凤阳县', '195', '1', '0');
INSERT INTO `area` VALUES ('2283', '南谯区', '195', '1', '0');
INSERT INTO `area` VALUES ('2284', '天长市', '195', '1', '0');
INSERT INTO `area` VALUES ('2285', '定远县', '195', '1', '0');
INSERT INTO `area` VALUES ('2286', '明光市', '195', '1', '0');
INSERT INTO `area` VALUES ('2287', '来安县', '195', '1', '0');
INSERT INTO `area` VALUES ('2288', '琅玡区', '195', '1', '0');
INSERT INTO `area` VALUES ('2289', '临泉县', '196', '1', '0');
INSERT INTO `area` VALUES ('2290', '太和县', '196', '1', '0');
INSERT INTO `area` VALUES ('2291', '界首市', '196', '1', '0');
INSERT INTO `area` VALUES ('2292', '阜南县', '196', '1', '0');
INSERT INTO `area` VALUES ('2293', '颍东区', '196', '1', '0');
INSERT INTO `area` VALUES ('2294', '颍州区', '196', '1', '0');
INSERT INTO `area` VALUES ('2295', '颍泉区', '196', '1', '0');
INSERT INTO `area` VALUES ('2296', '颖上县', '196', '1', '0');
INSERT INTO `area` VALUES ('2297', '埇桥区', '197', '1', '0');
INSERT INTO `area` VALUES ('2298', '泗县辖', '197', '1', '0');
INSERT INTO `area` VALUES ('2299', '灵璧县', '197', '1', '0');
INSERT INTO `area` VALUES ('2300', '砀山县', '197', '1', '0');
INSERT INTO `area` VALUES ('2301', '萧县', '197', '1', '0');
INSERT INTO `area` VALUES ('2302', '含山县', '198', '1', '0');
INSERT INTO `area` VALUES ('2303', '和县', '198', '1', '0');
INSERT INTO `area` VALUES ('2304', '居巢区', '198', '1', '0');
INSERT INTO `area` VALUES ('2305', '庐江县', '198', '1', '0');
INSERT INTO `area` VALUES ('2306', '无为县', '198', '1', '0');
INSERT INTO `area` VALUES ('2307', '寿县', '199', '1', '0');
INSERT INTO `area` VALUES ('2308', '舒城县', '199', '1', '0');
INSERT INTO `area` VALUES ('2309', '裕安区', '199', '1', '0');
INSERT INTO `area` VALUES ('2310', '金安区', '199', '1', '0');
INSERT INTO `area` VALUES ('2311', '金寨县', '199', '1', '0');
INSERT INTO `area` VALUES ('2312', '霍山县', '199', '1', '0');
INSERT INTO `area` VALUES ('2313', '霍邱县', '199', '1', '0');
INSERT INTO `area` VALUES ('2314', '利辛县', '200', '1', '0');
INSERT INTO `area` VALUES ('2315', '涡阳县', '200', '1', '0');
INSERT INTO `area` VALUES ('2316', '蒙城县', '200', '1', '0');
INSERT INTO `area` VALUES ('2317', '谯城区', '200', '1', '0');
INSERT INTO `area` VALUES ('2318', '东至县', '201', '1', '0');
INSERT INTO `area` VALUES ('2319', '石台县', '201', '1', '0');
INSERT INTO `area` VALUES ('2320', '贵池区', '201', '1', '0');
INSERT INTO `area` VALUES ('2321', '青阳县', '201', '1', '0');
INSERT INTO `area` VALUES ('2322', '宁国市', '202', '1', '0');
INSERT INTO `area` VALUES ('2323', '宣州区', '202', '1', '0');
INSERT INTO `area` VALUES ('2324', '广德县', '202', '1', '0');
INSERT INTO `area` VALUES ('2325', '旌德县', '202', '1', '0');
INSERT INTO `area` VALUES ('2326', '泾县', '202', '1', '0');
INSERT INTO `area` VALUES ('2327', '绩溪县', '202', '1', '0');
INSERT INTO `area` VALUES ('2328', '郎溪县', '202', '1', '0');
INSERT INTO `area` VALUES ('2329', '仓山区', '203', '1', '0');
INSERT INTO `area` VALUES ('2330', '台江区', '203', '1', '0');
INSERT INTO `area` VALUES ('2331', '平潭县', '203', '1', '0');
INSERT INTO `area` VALUES ('2332', '晋安区', '203', '1', '0');
INSERT INTO `area` VALUES ('2333', '永泰县', '203', '1', '0');
INSERT INTO `area` VALUES ('2334', '福清市', '203', '1', '0');
INSERT INTO `area` VALUES ('2335', '罗源县', '203', '1', '0');
INSERT INTO `area` VALUES ('2336', '连江县', '203', '1', '0');
INSERT INTO `area` VALUES ('2337', '长乐市', '203', '1', '0');
INSERT INTO `area` VALUES ('2338', '闽侯县', '203', '1', '0');
INSERT INTO `area` VALUES ('2339', '闽清县', '203', '1', '0');
INSERT INTO `area` VALUES ('2340', '马尾区', '203', '1', '0');
INSERT INTO `area` VALUES ('2341', '鼓楼区', '203', '1', '0');
INSERT INTO `area` VALUES ('2342', '同安区', '204', '1', '0');
INSERT INTO `area` VALUES ('2343', '思明区', '204', '1', '0');
INSERT INTO `area` VALUES ('2344', '海沧区', '204', '1', '0');
INSERT INTO `area` VALUES ('2345', '湖里区', '204', '1', '0');
INSERT INTO `area` VALUES ('2346', '翔安区', '204', '1', '0');
INSERT INTO `area` VALUES ('2347', '集美区', '204', '1', '0');
INSERT INTO `area` VALUES ('2348', '仙游县', '205', '1', '0');
INSERT INTO `area` VALUES ('2349', '城厢区', '205', '1', '0');
INSERT INTO `area` VALUES ('2350', '涵江区', '205', '1', '0');
INSERT INTO `area` VALUES ('2351', '秀屿区', '205', '1', '0');
INSERT INTO `area` VALUES ('2352', '荔城区', '205', '1', '0');
INSERT INTO `area` VALUES ('2353', '三元区', '206', '1', '0');
INSERT INTO `area` VALUES ('2354', '大田县', '206', '1', '0');
INSERT INTO `area` VALUES ('2355', '宁化县', '206', '1', '0');
INSERT INTO `area` VALUES ('2356', '将乐县', '206', '1', '0');
INSERT INTO `area` VALUES ('2357', '尤溪县', '206', '1', '0');
INSERT INTO `area` VALUES ('2358', '建宁县', '206', '1', '0');
INSERT INTO `area` VALUES ('2359', '明溪县', '206', '1', '0');
INSERT INTO `area` VALUES ('2360', '梅列区', '206', '1', '0');
INSERT INTO `area` VALUES ('2361', '永安市', '206', '1', '0');
INSERT INTO `area` VALUES ('2362', '沙县', '206', '1', '0');
INSERT INTO `area` VALUES ('2363', '泰宁县', '206', '1', '0');
INSERT INTO `area` VALUES ('2364', '清流县', '206', '1', '0');
INSERT INTO `area` VALUES ('2365', '丰泽区', '207', '1', '0');
INSERT INTO `area` VALUES ('2366', '南安市', '207', '1', '0');
INSERT INTO `area` VALUES ('2367', '安溪县', '207', '1', '0');
INSERT INTO `area` VALUES ('2368', '德化县', '207', '1', '0');
INSERT INTO `area` VALUES ('2369', '惠安县', '207', '1', '0');
INSERT INTO `area` VALUES ('2370', '晋江市', '207', '1', '0');
INSERT INTO `area` VALUES ('2371', '永春县', '207', '1', '0');
INSERT INTO `area` VALUES ('2372', '泉港区', '207', '1', '0');
INSERT INTO `area` VALUES ('2373', '洛江区', '207', '1', '0');
INSERT INTO `area` VALUES ('2374', '石狮市', '207', '1', '0');
INSERT INTO `area` VALUES ('2375', '金门县', '207', '1', '0');
INSERT INTO `area` VALUES ('2376', '鲤城区', '207', '1', '0');
INSERT INTO `area` VALUES ('2377', '东山县', '208', '1', '0');
INSERT INTO `area` VALUES ('2378', '云霄县', '208', '1', '0');
INSERT INTO `area` VALUES ('2379', '华安县', '208', '1', '0');
INSERT INTO `area` VALUES ('2380', '南靖县', '208', '1', '0');
INSERT INTO `area` VALUES ('2381', '平和县', '208', '1', '0');
INSERT INTO `area` VALUES ('2382', '漳浦县', '208', '1', '0');
INSERT INTO `area` VALUES ('2383', '芗城区', '208', '1', '0');
INSERT INTO `area` VALUES ('2384', '诏安县', '208', '1', '0');
INSERT INTO `area` VALUES ('2385', '长泰县', '208', '1', '0');
INSERT INTO `area` VALUES ('2386', '龙文区', '208', '1', '0');
INSERT INTO `area` VALUES ('2387', '龙海市', '208', '1', '0');
INSERT INTO `area` VALUES ('2388', '光泽县', '209', '1', '0');
INSERT INTO `area` VALUES ('2389', '延平区', '209', '1', '0');
INSERT INTO `area` VALUES ('2390', '建瓯市', '209', '1', '0');
INSERT INTO `area` VALUES ('2391', '建阳市', '209', '1', '0');
INSERT INTO `area` VALUES ('2392', '政和县', '209', '1', '0');
INSERT INTO `area` VALUES ('2393', '松溪县', '209', '1', '0');
INSERT INTO `area` VALUES ('2394', '武夷山市', '209', '1', '0');
INSERT INTO `area` VALUES ('2395', '浦城县', '209', '1', '0');
INSERT INTO `area` VALUES ('2396', '邵武市', '209', '1', '0');
INSERT INTO `area` VALUES ('2397', '顺昌县', '209', '1', '0');
INSERT INTO `area` VALUES ('2398', '上杭县', '210', '1', '0');
INSERT INTO `area` VALUES ('2399', '新罗区', '210', '1', '0');
INSERT INTO `area` VALUES ('2400', '武平县', '210', '1', '0');
INSERT INTO `area` VALUES ('2401', '永定县', '210', '1', '0');
INSERT INTO `area` VALUES ('2402', '漳平市', '210', '1', '0');
INSERT INTO `area` VALUES ('2403', '连城县', '210', '1', '0');
INSERT INTO `area` VALUES ('2404', '长汀县', '210', '1', '0');
INSERT INTO `area` VALUES ('2405', '古田县', '211', '1', '0');
INSERT INTO `area` VALUES ('2406', '周宁县', '211', '1', '0');
INSERT INTO `area` VALUES ('2407', '寿宁县', '211', '1', '0');
INSERT INTO `area` VALUES ('2408', '屏南县', '211', '1', '0');
INSERT INTO `area` VALUES ('2409', '柘荣县', '211', '1', '0');
INSERT INTO `area` VALUES ('2410', '福安市', '211', '1', '0');
INSERT INTO `area` VALUES ('2411', '福鼎市', '211', '1', '0');
INSERT INTO `area` VALUES ('2412', '蕉城区', '211', '1', '0');
INSERT INTO `area` VALUES ('2413', '霞浦县', '211', '1', '0');
INSERT INTO `area` VALUES ('2414', '东湖区', '212', '1', '0');
INSERT INTO `area` VALUES ('2415', '南昌县', '212', '1', '0');
INSERT INTO `area` VALUES ('2416', '安义县', '212', '1', '0');
INSERT INTO `area` VALUES ('2417', '新建县', '212', '1', '0');
INSERT INTO `area` VALUES ('2418', '湾里区', '212', '1', '0');
INSERT INTO `area` VALUES ('2419', '西湖区', '212', '1', '0');
INSERT INTO `area` VALUES ('2420', '进贤县', '212', '1', '0');
INSERT INTO `area` VALUES ('2421', '青云谱区', '212', '1', '0');
INSERT INTO `area` VALUES ('2422', '青山湖区', '212', '1', '0');
INSERT INTO `area` VALUES ('2423', '乐平市', '213', '1', '0');
INSERT INTO `area` VALUES ('2424', '昌江区', '213', '1', '0');
INSERT INTO `area` VALUES ('2425', '浮梁县', '213', '1', '0');
INSERT INTO `area` VALUES ('2426', '珠山区', '213', '1', '0');
INSERT INTO `area` VALUES ('2427', '上栗县', '214', '1', '0');
INSERT INTO `area` VALUES ('2428', '安源区', '214', '1', '0');
INSERT INTO `area` VALUES ('2429', '湘东区', '214', '1', '0');
INSERT INTO `area` VALUES ('2430', '芦溪县', '214', '1', '0');
INSERT INTO `area` VALUES ('2431', '莲花县', '214', '1', '0');
INSERT INTO `area` VALUES ('2432', '九江县', '215', '1', '0');
INSERT INTO `area` VALUES ('2433', '修水县', '215', '1', '0');
INSERT INTO `area` VALUES ('2434', '庐山区', '215', '1', '0');
INSERT INTO `area` VALUES ('2435', '彭泽县', '215', '1', '0');
INSERT INTO `area` VALUES ('2436', '德安县', '215', '1', '0');
INSERT INTO `area` VALUES ('2437', '星子县', '215', '1', '0');
INSERT INTO `area` VALUES ('2438', '武宁县', '215', '1', '0');
INSERT INTO `area` VALUES ('2439', '永修县', '215', '1', '0');
INSERT INTO `area` VALUES ('2440', '浔阳区', '215', '1', '0');
INSERT INTO `area` VALUES ('2441', '湖口县', '215', '1', '0');
INSERT INTO `area` VALUES ('2442', '瑞昌市', '215', '1', '0');
INSERT INTO `area` VALUES ('2443', '都昌县', '215', '1', '0');
INSERT INTO `area` VALUES ('2444', '分宜县', '216', '1', '0');
INSERT INTO `area` VALUES ('2445', '渝水区', '216', '1', '0');
INSERT INTO `area` VALUES ('2446', '余江县', '217', '1', '0');
INSERT INTO `area` VALUES ('2447', '月湖区', '217', '1', '0');
INSERT INTO `area` VALUES ('2448', '贵溪市', '217', '1', '0');
INSERT INTO `area` VALUES ('2449', '上犹县', '218', '1', '0');
INSERT INTO `area` VALUES ('2450', '于都县', '218', '1', '0');
INSERT INTO `area` VALUES ('2451', '会昌县', '218', '1', '0');
INSERT INTO `area` VALUES ('2452', '信丰县', '218', '1', '0');
INSERT INTO `area` VALUES ('2453', '全南县', '218', '1', '0');
INSERT INTO `area` VALUES ('2454', '兴国县', '218', '1', '0');
INSERT INTO `area` VALUES ('2455', '南康市', '218', '1', '0');
INSERT INTO `area` VALUES ('2456', '大余县', '218', '1', '0');
INSERT INTO `area` VALUES ('2457', '宁都县', '218', '1', '0');
INSERT INTO `area` VALUES ('2458', '安远县', '218', '1', '0');
INSERT INTO `area` VALUES ('2459', '定南县', '218', '1', '0');
INSERT INTO `area` VALUES ('2460', '寻乌县', '218', '1', '0');
INSERT INTO `area` VALUES ('2461', '崇义县', '218', '1', '0');
INSERT INTO `area` VALUES ('2462', '瑞金市', '218', '1', '0');
INSERT INTO `area` VALUES ('2463', '石城县', '218', '1', '0');
INSERT INTO `area` VALUES ('2464', '章贡区', '218', '1', '0');
INSERT INTO `area` VALUES ('2465', '赣县', '218', '1', '0');
INSERT INTO `area` VALUES ('2466', '龙南县', '218', '1', '0');
INSERT INTO `area` VALUES ('2467', '万安县', '219', '1', '0');
INSERT INTO `area` VALUES ('2468', '井冈山市', '219', '1', '0');
INSERT INTO `area` VALUES ('2469', '吉安县', '219', '1', '0');
INSERT INTO `area` VALUES ('2470', '吉州区', '219', '1', '0');
INSERT INTO `area` VALUES ('2471', '吉水县', '219', '1', '0');
INSERT INTO `area` VALUES ('2472', '安福县', '219', '1', '0');
INSERT INTO `area` VALUES ('2473', '峡江县', '219', '1', '0');
INSERT INTO `area` VALUES ('2474', '新干县', '219', '1', '0');
INSERT INTO `area` VALUES ('2475', '永丰县', '219', '1', '0');
INSERT INTO `area` VALUES ('2476', '永新县', '219', '1', '0');
INSERT INTO `area` VALUES ('2477', '泰和县', '219', '1', '0');
INSERT INTO `area` VALUES ('2478', '遂川县', '219', '1', '0');
INSERT INTO `area` VALUES ('2479', '青原区', '219', '1', '0');
INSERT INTO `area` VALUES ('2480', '万载县', '220', '1', '0');
INSERT INTO `area` VALUES ('2481', '上高县', '220', '1', '0');
INSERT INTO `area` VALUES ('2482', '丰城市', '220', '1', '0');
INSERT INTO `area` VALUES ('2483', '奉新县', '220', '1', '0');
INSERT INTO `area` VALUES ('2484', '宜丰县', '220', '1', '0');
INSERT INTO `area` VALUES ('2485', '樟树市', '220', '1', '0');
INSERT INTO `area` VALUES ('2486', '袁州区', '220', '1', '0');
INSERT INTO `area` VALUES ('2487', '铜鼓县', '220', '1', '0');
INSERT INTO `area` VALUES ('2488', '靖安县', '220', '1', '0');
INSERT INTO `area` VALUES ('2489', '高安市', '220', '1', '0');
INSERT INTO `area` VALUES ('2490', '东乡县', '221', '1', '0');
INSERT INTO `area` VALUES ('2491', '临川区', '221', '1', '0');
INSERT INTO `area` VALUES ('2492', '乐安县', '221', '1', '0');
INSERT INTO `area` VALUES ('2493', '南丰县', '221', '1', '0');
INSERT INTO `area` VALUES ('2494', '南城县', '221', '1', '0');
INSERT INTO `area` VALUES ('2495', '宜黄县', '221', '1', '0');
INSERT INTO `area` VALUES ('2496', '崇仁县', '221', '1', '0');
INSERT INTO `area` VALUES ('2497', '广昌县', '221', '1', '0');
INSERT INTO `area` VALUES ('2498', '资溪县', '221', '1', '0');
INSERT INTO `area` VALUES ('2499', '金溪县', '221', '1', '0');
INSERT INTO `area` VALUES ('2500', '黎川县', '221', '1', '0');
INSERT INTO `area` VALUES ('2501', '万年县', '222', '1', '0');
INSERT INTO `area` VALUES ('2502', '上饶县', '222', '1', '0');
INSERT INTO `area` VALUES ('2503', '余干县', '222', '1', '0');
INSERT INTO `area` VALUES ('2504', '信州区', '222', '1', '0');
INSERT INTO `area` VALUES ('2505', '婺源县', '222', '1', '0');
INSERT INTO `area` VALUES ('2506', '广丰县', '222', '1', '0');
INSERT INTO `area` VALUES ('2507', '弋阳县', '222', '1', '0');
INSERT INTO `area` VALUES ('2508', '德兴市', '222', '1', '0');
INSERT INTO `area` VALUES ('2509', '横峰县', '222', '1', '0');
INSERT INTO `area` VALUES ('2510', '玉山县', '222', '1', '0');
INSERT INTO `area` VALUES ('2511', '鄱阳县', '222', '1', '0');
INSERT INTO `area` VALUES ('2512', '铅山县', '222', '1', '0');
INSERT INTO `area` VALUES ('2513', '历下区', '223', '1', '0');
INSERT INTO `area` VALUES ('2514', '历城区', '223', '1', '0');
INSERT INTO `area` VALUES ('2515', '商河县', '223', '1', '0');
INSERT INTO `area` VALUES ('2516', '天桥区', '223', '1', '0');
INSERT INTO `area` VALUES ('2517', '市中区', '223', '1', '0');
INSERT INTO `area` VALUES ('2518', '平阴县', '223', '1', '0');
INSERT INTO `area` VALUES ('2519', '槐荫区', '223', '1', '0');
INSERT INTO `area` VALUES ('2520', '济阳县', '223', '1', '0');
INSERT INTO `area` VALUES ('2521', '章丘市', '223', '1', '0');
INSERT INTO `area` VALUES ('2522', '长清区', '223', '1', '0');
INSERT INTO `area` VALUES ('2523', '即墨市', '224', '1', '0');
INSERT INTO `area` VALUES ('2524', '四方区', '224', '1', '0');
INSERT INTO `area` VALUES ('2525', '城阳区', '224', '1', '0');
INSERT INTO `area` VALUES ('2526', '崂山区', '224', '1', '0');
INSERT INTO `area` VALUES ('2527', '市北区', '224', '1', '0');
INSERT INTO `area` VALUES ('2528', '市南区', '224', '1', '0');
INSERT INTO `area` VALUES ('2529', '平度市', '224', '1', '0');
INSERT INTO `area` VALUES ('2530', '李沧区', '224', '1', '0');
INSERT INTO `area` VALUES ('2531', '胶南市', '224', '1', '0');
INSERT INTO `area` VALUES ('2532', '胶州市', '224', '1', '0');
INSERT INTO `area` VALUES ('2533', '莱西市', '224', '1', '0');
INSERT INTO `area` VALUES ('2534', '黄岛区', '224', '1', '0');
INSERT INTO `area` VALUES ('2535', '临淄区', '225', '1', '0');
INSERT INTO `area` VALUES ('2536', '博山区', '225', '1', '0');
INSERT INTO `area` VALUES ('2537', '周村区', '225', '1', '0');
INSERT INTO `area` VALUES ('2538', '张店区', '225', '1', '0');
INSERT INTO `area` VALUES ('2539', '桓台县', '225', '1', '0');
INSERT INTO `area` VALUES ('2540', '沂源县', '225', '1', '0');
INSERT INTO `area` VALUES ('2541', '淄川区', '225', '1', '0');
INSERT INTO `area` VALUES ('2542', '高青县', '225', '1', '0');
INSERT INTO `area` VALUES ('2543', '台儿庄区', '226', '1', '0');
INSERT INTO `area` VALUES ('2544', '山亭区', '226', '1', '0');
INSERT INTO `area` VALUES ('2545', '峄城区', '226', '1', '0');
INSERT INTO `area` VALUES ('2546', '市中区', '226', '1', '0');
INSERT INTO `area` VALUES ('2547', '滕州市', '226', '1', '0');
INSERT INTO `area` VALUES ('2548', '薛城区', '226', '1', '0');
INSERT INTO `area` VALUES ('2549', '东营区', '227', '1', '0');
INSERT INTO `area` VALUES ('2550', '利津县', '227', '1', '0');
INSERT INTO `area` VALUES ('2551', '垦利县', '227', '1', '0');
INSERT INTO `area` VALUES ('2552', '广饶县', '227', '1', '0');
INSERT INTO `area` VALUES ('2553', '河口区', '227', '1', '0');
INSERT INTO `area` VALUES ('2554', '招远市', '228', '1', '0');
INSERT INTO `area` VALUES ('2555', '栖霞市', '228', '1', '0');
INSERT INTO `area` VALUES ('2556', '海阳市', '228', '1', '0');
INSERT INTO `area` VALUES ('2557', '牟平区', '228', '1', '0');
INSERT INTO `area` VALUES ('2558', '福山区', '228', '1', '0');
INSERT INTO `area` VALUES ('2559', '芝罘区', '228', '1', '0');
INSERT INTO `area` VALUES ('2560', '莱山区', '228', '1', '0');
INSERT INTO `area` VALUES ('2561', '莱州市', '228', '1', '0');
INSERT INTO `area` VALUES ('2562', '莱阳市', '228', '1', '0');
INSERT INTO `area` VALUES ('2563', '蓬莱市', '228', '1', '0');
INSERT INTO `area` VALUES ('2564', '长岛县', '228', '1', '0');
INSERT INTO `area` VALUES ('2565', '龙口市', '228', '1', '0');
INSERT INTO `area` VALUES ('2566', '临朐县', '229', '1', '0');
INSERT INTO `area` VALUES ('2567', '坊子区', '229', '1', '0');
INSERT INTO `area` VALUES ('2568', '奎文区', '229', '1', '0');
INSERT INTO `area` VALUES ('2569', '安丘市', '229', '1', '0');
INSERT INTO `area` VALUES ('2570', '寒亭区', '229', '1', '0');
INSERT INTO `area` VALUES ('2571', '寿光市', '229', '1', '0');
INSERT INTO `area` VALUES ('2572', '昌乐县', '229', '1', '0');
INSERT INTO `area` VALUES ('2573', '昌邑市', '229', '1', '0');
INSERT INTO `area` VALUES ('2574', '潍城区', '229', '1', '0');
INSERT INTO `area` VALUES ('2575', '诸城市', '229', '1', '0');
INSERT INTO `area` VALUES ('2576', '青州市', '229', '1', '0');
INSERT INTO `area` VALUES ('2577', '高密市', '229', '1', '0');
INSERT INTO `area` VALUES ('2578', '任城区', '230', '1', '0');
INSERT INTO `area` VALUES ('2579', '兖州市', '230', '1', '0');
INSERT INTO `area` VALUES ('2580', '嘉祥县', '230', '1', '0');
INSERT INTO `area` VALUES ('2581', '市中区', '230', '1', '0');
INSERT INTO `area` VALUES ('2582', '微山县', '230', '1', '0');
INSERT INTO `area` VALUES ('2583', '曲阜市', '230', '1', '0');
INSERT INTO `area` VALUES ('2584', '梁山县', '230', '1', '0');
INSERT INTO `area` VALUES ('2585', '汶上县', '230', '1', '0');
INSERT INTO `area` VALUES ('2586', '泗水县', '230', '1', '0');
INSERT INTO `area` VALUES ('2587', '邹城市', '230', '1', '0');
INSERT INTO `area` VALUES ('2588', '金乡县', '230', '1', '0');
INSERT INTO `area` VALUES ('2589', '鱼台县', '230', '1', '0');
INSERT INTO `area` VALUES ('2590', '东平县', '231', '1', '0');
INSERT INTO `area` VALUES ('2591', '宁阳县', '231', '1', '0');
INSERT INTO `area` VALUES ('2592', '岱岳区', '231', '1', '0');
INSERT INTO `area` VALUES ('2593', '新泰市', '231', '1', '0');
INSERT INTO `area` VALUES ('2594', '泰山区', '231', '1', '0');
INSERT INTO `area` VALUES ('2595', '肥城市', '231', '1', '0');
INSERT INTO `area` VALUES ('2596', '乳山市', '232', '1', '0');
INSERT INTO `area` VALUES ('2597', '文登市', '232', '1', '0');
INSERT INTO `area` VALUES ('2598', '环翠区', '232', '1', '0');
INSERT INTO `area` VALUES ('2599', '荣成市', '232', '1', '0');
INSERT INTO `area` VALUES ('2600', '东港区', '233', '1', '0');
INSERT INTO `area` VALUES ('2601', '五莲县', '233', '1', '0');
INSERT INTO `area` VALUES ('2602', '岚山区', '233', '1', '0');
INSERT INTO `area` VALUES ('2603', '莒县', '233', '1', '0');
INSERT INTO `area` VALUES ('2604', '莱城区', '234', '1', '0');
INSERT INTO `area` VALUES ('2605', '钢城区', '234', '1', '0');
INSERT INTO `area` VALUES ('2606', '临沭县', '235', '1', '0');
INSERT INTO `area` VALUES ('2607', '兰山区', '235', '1', '0');
INSERT INTO `area` VALUES ('2608', '平邑县', '235', '1', '0');
INSERT INTO `area` VALUES ('2609', '沂南县', '235', '1', '0');
INSERT INTO `area` VALUES ('2610', '沂水县', '235', '1', '0');
INSERT INTO `area` VALUES ('2611', '河东区', '235', '1', '0');
INSERT INTO `area` VALUES ('2612', '罗庄区', '235', '1', '0');
INSERT INTO `area` VALUES ('2613', '苍山县', '235', '1', '0');
INSERT INTO `area` VALUES ('2614', '莒南县', '235', '1', '0');
INSERT INTO `area` VALUES ('2615', '蒙阴县', '235', '1', '0');
INSERT INTO `area` VALUES ('2616', '费县', '235', '1', '0');
INSERT INTO `area` VALUES ('2617', '郯城县', '235', '1', '0');
INSERT INTO `area` VALUES ('2618', '临邑县', '236', '1', '0');
INSERT INTO `area` VALUES ('2619', '乐陵市', '236', '1', '0');
INSERT INTO `area` VALUES ('2620', '夏津县', '236', '1', '0');
INSERT INTO `area` VALUES ('2621', '宁津县', '236', '1', '0');
INSERT INTO `area` VALUES ('2622', '平原县', '236', '1', '0');
INSERT INTO `area` VALUES ('2623', '庆云县', '236', '1', '0');
INSERT INTO `area` VALUES ('2624', '德城区', '236', '1', '0');
INSERT INTO `area` VALUES ('2625', '武城县', '236', '1', '0');
INSERT INTO `area` VALUES ('2626', '禹城市', '236', '1', '0');
INSERT INTO `area` VALUES ('2627', '陵县', '236', '1', '0');
INSERT INTO `area` VALUES ('2628', '齐河县', '236', '1', '0');
INSERT INTO `area` VALUES ('2629', '东昌府区', '237', '1', '0');
INSERT INTO `area` VALUES ('2630', '东阿县', '237', '1', '0');
INSERT INTO `area` VALUES ('2631', '临清市', '237', '1', '0');
INSERT INTO `area` VALUES ('2632', '冠县', '237', '1', '0');
INSERT INTO `area` VALUES ('2633', '茌平县', '237', '1', '0');
INSERT INTO `area` VALUES ('2634', '莘县', '237', '1', '0');
INSERT INTO `area` VALUES ('2635', '阳谷县', '237', '1', '0');
INSERT INTO `area` VALUES ('2636', '高唐县', '237', '1', '0');
INSERT INTO `area` VALUES ('2637', '博兴县', '238', '1', '0');
INSERT INTO `area` VALUES ('2638', '惠民县', '238', '1', '0');
INSERT INTO `area` VALUES ('2639', '无棣县', '238', '1', '0');
INSERT INTO `area` VALUES ('2640', '沾化县', '238', '1', '0');
INSERT INTO `area` VALUES ('2641', '滨城区', '238', '1', '0');
INSERT INTO `area` VALUES ('2642', '邹平县', '238', '1', '0');
INSERT INTO `area` VALUES ('2643', '阳信县', '238', '1', '0');
INSERT INTO `area` VALUES ('2644', '东明县', '239', '1', '0');
INSERT INTO `area` VALUES ('2645', '单县', '239', '1', '0');
INSERT INTO `area` VALUES ('2646', '定陶县', '239', '1', '0');
INSERT INTO `area` VALUES ('2647', '巨野县', '239', '1', '0');
INSERT INTO `area` VALUES ('2648', '成武县', '239', '1', '0');
INSERT INTO `area` VALUES ('2649', '曹县', '239', '1', '0');
INSERT INTO `area` VALUES ('2650', '牡丹区', '239', '1', '0');
INSERT INTO `area` VALUES ('2651', '郓城县', '239', '1', '0');
INSERT INTO `area` VALUES ('2652', '鄄城县', '239', '1', '0');
INSERT INTO `area` VALUES ('2653', '上街区', '240', '1', '0');
INSERT INTO `area` VALUES ('2654', '中原区', '240', '1', '0');
INSERT INTO `area` VALUES ('2655', '中牟县', '240', '1', '0');
INSERT INTO `area` VALUES ('2656', '二七区', '240', '1', '0');
INSERT INTO `area` VALUES ('2657', '巩义市', '240', '1', '0');
INSERT INTO `area` VALUES ('2658', '惠济区', '240', '1', '0');
INSERT INTO `area` VALUES ('2659', '新密市', '240', '1', '0');
INSERT INTO `area` VALUES ('2660', '新郑市', '240', '1', '0');
INSERT INTO `area` VALUES ('2661', '登封市', '240', '1', '0');
INSERT INTO `area` VALUES ('2662', '管城回族区', '240', '1', '0');
INSERT INTO `area` VALUES ('2663', '荥阳市', '240', '1', '0');
INSERT INTO `area` VALUES ('2664', '金水区', '240', '1', '0');
INSERT INTO `area` VALUES ('2665', '兰考县', '241', '1', '0');
INSERT INTO `area` VALUES ('2666', '尉氏县', '241', '1', '0');
INSERT INTO `area` VALUES ('2667', '开封县', '241', '1', '0');
INSERT INTO `area` VALUES ('2668', '杞县', '241', '1', '0');
INSERT INTO `area` VALUES ('2669', '禹王台区', '241', '1', '0');
INSERT INTO `area` VALUES ('2670', '通许县', '241', '1', '0');
INSERT INTO `area` VALUES ('2671', '金明区', '241', '1', '0');
INSERT INTO `area` VALUES ('2672', '顺河回族区', '241', '1', '0');
INSERT INTO `area` VALUES ('2673', '鼓楼区', '241', '1', '0');
INSERT INTO `area` VALUES ('2674', '龙亭区', '241', '1', '0');
INSERT INTO `area` VALUES ('2675', '伊川县', '242', '1', '0');
INSERT INTO `area` VALUES ('2676', '偃师市', '242', '1', '0');
INSERT INTO `area` VALUES ('2677', '吉利区', '242', '1', '0');
INSERT INTO `area` VALUES ('2678', '孟津县', '242', '1', '0');
INSERT INTO `area` VALUES ('2679', '宜阳县', '242', '1', '0');
INSERT INTO `area` VALUES ('2680', '嵩县', '242', '1', '0');
INSERT INTO `area` VALUES ('2681', '新安县', '242', '1', '0');
INSERT INTO `area` VALUES ('2682', '栾川县', '242', '1', '0');
INSERT INTO `area` VALUES ('2683', '汝阳县', '242', '1', '0');
INSERT INTO `area` VALUES ('2684', '洛宁县', '242', '1', '0');
INSERT INTO `area` VALUES ('2685', '洛龙区', '242', '1', '0');
INSERT INTO `area` VALUES ('2686', '涧西区', '242', '1', '0');
INSERT INTO `area` VALUES ('2687', '瀍河回族区', '242', '1', '0');
INSERT INTO `area` VALUES ('2688', '老城区', '242', '1', '0');
INSERT INTO `area` VALUES ('2689', '西工区', '242', '1', '0');
INSERT INTO `area` VALUES ('2690', '卫东区', '243', '1', '0');
INSERT INTO `area` VALUES ('2691', '叶县', '243', '1', '0');
INSERT INTO `area` VALUES ('2692', '宝丰县', '243', '1', '0');
INSERT INTO `area` VALUES ('2693', '新华区', '243', '1', '0');
INSERT INTO `area` VALUES ('2694', '汝州市', '243', '1', '0');
INSERT INTO `area` VALUES ('2695', '湛河区', '243', '1', '0');
INSERT INTO `area` VALUES ('2696', '石龙区', '243', '1', '0');
INSERT INTO `area` VALUES ('2697', '舞钢市', '243', '1', '0');
INSERT INTO `area` VALUES ('2698', '郏县', '243', '1', '0');
INSERT INTO `area` VALUES ('2699', '鲁山县', '243', '1', '0');
INSERT INTO `area` VALUES ('2700', '内黄县', '244', '1', '0');
INSERT INTO `area` VALUES ('2701', '北关区', '244', '1', '0');
INSERT INTO `area` VALUES ('2702', '安阳县', '244', '1', '0');
INSERT INTO `area` VALUES ('2703', '文峰区', '244', '1', '0');
INSERT INTO `area` VALUES ('2704', '林州市', '244', '1', '0');
INSERT INTO `area` VALUES ('2705', '殷都区', '244', '1', '0');
INSERT INTO `area` VALUES ('2706', '汤阴县', '244', '1', '0');
INSERT INTO `area` VALUES ('2707', '滑县', '244', '1', '0');
INSERT INTO `area` VALUES ('2708', '龙安区', '244', '1', '0');
INSERT INTO `area` VALUES ('2709', '山城区', '245', '1', '0');
INSERT INTO `area` VALUES ('2710', '浚县', '245', '1', '0');
INSERT INTO `area` VALUES ('2711', '淇县', '245', '1', '0');
INSERT INTO `area` VALUES ('2712', '淇滨区', '245', '1', '0');
INSERT INTO `area` VALUES ('2713', '鹤山区', '245', '1', '0');
INSERT INTO `area` VALUES ('2714', '凤泉区', '246', '1', '0');
INSERT INTO `area` VALUES ('2715', '卫滨区', '246', '1', '0');
INSERT INTO `area` VALUES ('2716', '卫辉市', '246', '1', '0');
INSERT INTO `area` VALUES ('2717', '原阳县', '246', '1', '0');
INSERT INTO `area` VALUES ('2718', '封丘县', '246', '1', '0');
INSERT INTO `area` VALUES ('2719', '延津县', '246', '1', '0');
INSERT INTO `area` VALUES ('2720', '新乡县', '246', '1', '0');
INSERT INTO `area` VALUES ('2721', '牧野区', '246', '1', '0');
INSERT INTO `area` VALUES ('2722', '红旗区', '246', '1', '0');
INSERT INTO `area` VALUES ('2723', '获嘉县', '246', '1', '0');
INSERT INTO `area` VALUES ('2724', '辉县市', '246', '1', '0');
INSERT INTO `area` VALUES ('2725', '长垣县', '246', '1', '0');
INSERT INTO `area` VALUES ('2726', '中站区', '247', '1', '0');
INSERT INTO `area` VALUES ('2727', '修武县', '247', '1', '0');
INSERT INTO `area` VALUES ('2728', '博爱县', '247', '1', '0');
INSERT INTO `area` VALUES ('2729', '孟州市', '247', '1', '0');
INSERT INTO `area` VALUES ('2730', '山阳区', '247', '1', '0');
INSERT INTO `area` VALUES ('2731', '武陟县', '247', '1', '0');
INSERT INTO `area` VALUES ('2732', '沁阳市', '247', '1', '0');
INSERT INTO `area` VALUES ('2733', '温县', '247', '1', '0');
INSERT INTO `area` VALUES ('2734', '解放区', '247', '1', '0');
INSERT INTO `area` VALUES ('2735', '马村区', '247', '1', '0');
INSERT INTO `area` VALUES ('2736', '华龙区', '248', '1', '0');
INSERT INTO `area` VALUES ('2737', '南乐县', '248', '1', '0');
INSERT INTO `area` VALUES ('2738', '台前县', '248', '1', '0');
INSERT INTO `area` VALUES ('2739', '清丰县', '248', '1', '0');
INSERT INTO `area` VALUES ('2740', '濮阳县', '248', '1', '0');
INSERT INTO `area` VALUES ('2741', '范县', '248', '1', '0');
INSERT INTO `area` VALUES ('2742', '禹州市', '249', '1', '0');
INSERT INTO `area` VALUES ('2743', '襄城县', '249', '1', '0');
INSERT INTO `area` VALUES ('2744', '许昌县', '249', '1', '0');
INSERT INTO `area` VALUES ('2745', '鄢陵县', '249', '1', '0');
INSERT INTO `area` VALUES ('2746', '长葛市', '249', '1', '0');
INSERT INTO `area` VALUES ('2747', '魏都区', '249', '1', '0');
INSERT INTO `area` VALUES ('2748', '临颍县', '250', '1', '0');
INSERT INTO `area` VALUES ('2749', '召陵区', '250', '1', '0');
INSERT INTO `area` VALUES ('2750', '源汇区', '250', '1', '0');
INSERT INTO `area` VALUES ('2751', '舞阳县', '250', '1', '0');
INSERT INTO `area` VALUES ('2752', '郾城区', '250', '1', '0');
INSERT INTO `area` VALUES ('2753', '义马市', '251', '1', '0');
INSERT INTO `area` VALUES ('2754', '卢氏县', '251', '1', '0');
INSERT INTO `area` VALUES ('2755', '渑池县', '251', '1', '0');
INSERT INTO `area` VALUES ('2756', '湖滨区', '251', '1', '0');
INSERT INTO `area` VALUES ('2757', '灵宝市', '251', '1', '0');
INSERT INTO `area` VALUES ('2758', '陕县', '251', '1', '0');
INSERT INTO `area` VALUES ('2759', '内乡县', '252', '1', '0');
INSERT INTO `area` VALUES ('2760', '南召县', '252', '1', '0');
INSERT INTO `area` VALUES ('2761', '卧龙区', '252', '1', '0');
INSERT INTO `area` VALUES ('2762', '唐河县', '252', '1', '0');
INSERT INTO `area` VALUES ('2763', '宛城区', '252', '1', '0');
INSERT INTO `area` VALUES ('2764', '新野县', '252', '1', '0');
INSERT INTO `area` VALUES ('2765', '方城县', '252', '1', '0');
INSERT INTO `area` VALUES ('2766', '桐柏县', '252', '1', '0');
INSERT INTO `area` VALUES ('2767', '淅川县', '252', '1', '0');
INSERT INTO `area` VALUES ('2768', '社旗县', '252', '1', '0');
INSERT INTO `area` VALUES ('2769', '西峡县', '252', '1', '0');
INSERT INTO `area` VALUES ('2770', '邓州市', '252', '1', '0');
INSERT INTO `area` VALUES ('2771', '镇平县', '252', '1', '0');
INSERT INTO `area` VALUES ('2772', '夏邑县', '253', '1', '0');
INSERT INTO `area` VALUES ('2773', '宁陵县', '253', '1', '0');
INSERT INTO `area` VALUES ('2774', '柘城县', '253', '1', '0');
INSERT INTO `area` VALUES ('2775', '民权县', '253', '1', '0');
INSERT INTO `area` VALUES ('2776', '永城市', '253', '1', '0');
INSERT INTO `area` VALUES ('2777', '睢县', '253', '1', '0');
INSERT INTO `area` VALUES ('2778', '睢阳区', '253', '1', '0');
INSERT INTO `area` VALUES ('2779', '粱园区', '253', '1', '0');
INSERT INTO `area` VALUES ('2780', '虞城县', '253', '1', '0');
INSERT INTO `area` VALUES ('2781', '光山县', '254', '1', '0');
INSERT INTO `area` VALUES ('2782', '商城县', '254', '1', '0');
INSERT INTO `area` VALUES ('2783', '固始县', '254', '1', '0');
INSERT INTO `area` VALUES ('2784', '平桥区', '254', '1', '0');
INSERT INTO `area` VALUES ('2785', '息县', '254', '1', '0');
INSERT INTO `area` VALUES ('2786', '新县', '254', '1', '0');
INSERT INTO `area` VALUES ('2787', '浉河区', '254', '1', '0');
INSERT INTO `area` VALUES ('2788', '淮滨县', '254', '1', '0');
INSERT INTO `area` VALUES ('2789', '潢川县', '254', '1', '0');
INSERT INTO `area` VALUES ('2790', '罗山县', '254', '1', '0');
INSERT INTO `area` VALUES ('2791', '商水县', '255', '1', '0');
INSERT INTO `area` VALUES ('2792', '太康县', '255', '1', '0');
INSERT INTO `area` VALUES ('2793', '川汇区', '255', '1', '0');
INSERT INTO `area` VALUES ('2794', '扶沟县', '255', '1', '0');
INSERT INTO `area` VALUES ('2795', '沈丘县', '255', '1', '0');
INSERT INTO `area` VALUES ('2796', '淮阳县', '255', '1', '0');
INSERT INTO `area` VALUES ('2797', '西华县', '255', '1', '0');
INSERT INTO `area` VALUES ('2798', '郸城县', '255', '1', '0');
INSERT INTO `area` VALUES ('2799', '项城市', '255', '1', '0');
INSERT INTO `area` VALUES ('2800', '鹿邑县', '255', '1', '0');
INSERT INTO `area` VALUES ('2801', '上蔡县', '256', '1', '0');
INSERT INTO `area` VALUES ('2802', '平舆县', '256', '1', '0');
INSERT INTO `area` VALUES ('2803', '新蔡县', '256', '1', '0');
INSERT INTO `area` VALUES ('2804', '正阳县', '256', '1', '0');
INSERT INTO `area` VALUES ('2805', '汝南县', '256', '1', '0');
INSERT INTO `area` VALUES ('2806', '泌阳县', '256', '1', '0');
INSERT INTO `area` VALUES ('2807', '确山县', '256', '1', '0');
INSERT INTO `area` VALUES ('2808', '西平县', '256', '1', '0');
INSERT INTO `area` VALUES ('2809', '遂平县', '256', '1', '0');
INSERT INTO `area` VALUES ('2810', '驿城区', '256', '1', '0');
INSERT INTO `area` VALUES ('2811', '济源市', '257', '1', '0');
INSERT INTO `area` VALUES ('2812', '东西湖区', '258', '1', '0');
INSERT INTO `area` VALUES ('2813', '新洲区', '258', '1', '0');
INSERT INTO `area` VALUES ('2814', '武昌区', '258', '1', '0');
INSERT INTO `area` VALUES ('2815', '汉南区', '258', '1', '0');
INSERT INTO `area` VALUES ('2816', '汉阳区', '258', '1', '0');
INSERT INTO `area` VALUES ('2817', '江夏区', '258', '1', '0');
INSERT INTO `area` VALUES ('2818', '江岸区', '258', '1', '0');
INSERT INTO `area` VALUES ('2819', '江汉区', '258', '1', '0');
INSERT INTO `area` VALUES ('2820', '洪山区', '258', '1', '0');
INSERT INTO `area` VALUES ('2821', '硚口区', '258', '1', '0');
INSERT INTO `area` VALUES ('2822', '蔡甸区', '258', '1', '0');
INSERT INTO `area` VALUES ('2823', '青山区', '258', '1', '0');
INSERT INTO `area` VALUES ('2824', '黄陂区', '258', '1', '0');
INSERT INTO `area` VALUES ('2825', '下陆区', '259', '1', '0');
INSERT INTO `area` VALUES ('2826', '大冶市', '259', '1', '0');
INSERT INTO `area` VALUES ('2827', '西塞山区', '259', '1', '0');
INSERT INTO `area` VALUES ('2828', '铁山区', '259', '1', '0');
INSERT INTO `area` VALUES ('2829', '阳新县', '259', '1', '0');
INSERT INTO `area` VALUES ('2830', '黄石港区', '259', '1', '0');
INSERT INTO `area` VALUES ('2831', '丹江口市', '260', '1', '0');
INSERT INTO `area` VALUES ('2832', '张湾区', '260', '1', '0');
INSERT INTO `area` VALUES ('2833', '房县', '260', '1', '0');
INSERT INTO `area` VALUES ('2834', '竹山县', '260', '1', '0');
INSERT INTO `area` VALUES ('2835', '竹溪县', '260', '1', '0');
INSERT INTO `area` VALUES ('2836', '茅箭区', '260', '1', '0');
INSERT INTO `area` VALUES ('2837', '郧县', '260', '1', '0');
INSERT INTO `area` VALUES ('2838', '郧西县', '260', '1', '0');
INSERT INTO `area` VALUES ('2839', '五峰土家族自治县', '261', '1', '0');
INSERT INTO `area` VALUES ('2840', '伍家岗区', '261', '1', '0');
INSERT INTO `area` VALUES ('2841', '兴山县', '261', '1', '0');
INSERT INTO `area` VALUES ('2842', '夷陵区', '261', '1', '0');
INSERT INTO `area` VALUES ('2843', '宜都市', '261', '1', '0');
INSERT INTO `area` VALUES ('2844', '当阳市', '261', '1', '0');
INSERT INTO `area` VALUES ('2845', '枝江市', '261', '1', '0');
INSERT INTO `area` VALUES ('2846', '点军区', '261', '1', '0');
INSERT INTO `area` VALUES ('2847', '秭归县', '261', '1', '0');
INSERT INTO `area` VALUES ('2848', '虢亭区', '261', '1', '0');
INSERT INTO `area` VALUES ('2849', '西陵区', '261', '1', '0');
INSERT INTO `area` VALUES ('2850', '远安县', '261', '1', '0');
INSERT INTO `area` VALUES ('2851', '长阳土家族自治县', '261', '1', '0');
INSERT INTO `area` VALUES ('2852', '保康县', '262', '1', '0');
INSERT INTO `area` VALUES ('2853', '南漳县', '262', '1', '0');
INSERT INTO `area` VALUES ('2854', '宜城市', '262', '1', '0');
INSERT INTO `area` VALUES ('2855', '枣阳市', '262', '1', '0');
INSERT INTO `area` VALUES ('2856', '樊城区', '262', '1', '0');
INSERT INTO `area` VALUES ('2857', '老河口市', '262', '1', '0');
INSERT INTO `area` VALUES ('2858', '襄城区', '262', '1', '0');
INSERT INTO `area` VALUES ('2859', '襄阳区', '262', '1', '0');
INSERT INTO `area` VALUES ('2860', '谷城县', '262', '1', '0');
INSERT INTO `area` VALUES ('2861', '华容区', '263', '1', '0');
INSERT INTO `area` VALUES ('2862', '粱子湖', '263', '1', '0');
INSERT INTO `area` VALUES ('2863', '鄂城区', '263', '1', '0');
INSERT INTO `area` VALUES ('2864', '东宝区', '264', '1', '0');
INSERT INTO `area` VALUES ('2865', '京山县', '264', '1', '0');
INSERT INTO `area` VALUES ('2866', '掇刀区', '264', '1', '0');
INSERT INTO `area` VALUES ('2867', '沙洋县', '264', '1', '0');
INSERT INTO `area` VALUES ('2868', '钟祥市', '264', '1', '0');
INSERT INTO `area` VALUES ('2869', '云梦县', '265', '1', '0');
INSERT INTO `area` VALUES ('2870', '大悟县', '265', '1', '0');
INSERT INTO `area` VALUES ('2871', '孝南区', '265', '1', '0');
INSERT INTO `area` VALUES ('2872', '孝昌县', '265', '1', '0');
INSERT INTO `area` VALUES ('2873', '安陆市', '265', '1', '0');
INSERT INTO `area` VALUES ('2874', '应城市', '265', '1', '0');
INSERT INTO `area` VALUES ('2875', '汉川市', '265', '1', '0');
INSERT INTO `area` VALUES ('2876', '公安县', '266', '1', '0');
INSERT INTO `area` VALUES ('2877', '松滋市', '266', '1', '0');
INSERT INTO `area` VALUES ('2878', '江陵县', '266', '1', '0');
INSERT INTO `area` VALUES ('2879', '沙市区', '266', '1', '0');
INSERT INTO `area` VALUES ('2880', '洪湖市', '266', '1', '0');
INSERT INTO `area` VALUES ('2881', '监利县', '266', '1', '0');
INSERT INTO `area` VALUES ('2882', '石首市', '266', '1', '0');
INSERT INTO `area` VALUES ('2883', '荆州区', '266', '1', '0');
INSERT INTO `area` VALUES ('2884', '团风县', '267', '1', '0');
INSERT INTO `area` VALUES ('2885', '武穴市', '267', '1', '0');
INSERT INTO `area` VALUES ('2886', '浠水县', '267', '1', '0');
INSERT INTO `area` VALUES ('2887', '红安县', '267', '1', '0');
INSERT INTO `area` VALUES ('2888', '罗田县', '267', '1', '0');
INSERT INTO `area` VALUES ('2889', '英山县', '267', '1', '0');
INSERT INTO `area` VALUES ('2890', '蕲春县', '267', '1', '0');
INSERT INTO `area` VALUES ('2891', '麻城市', '267', '1', '0');
INSERT INTO `area` VALUES ('2892', '黄州区', '267', '1', '0');
INSERT INTO `area` VALUES ('2893', '黄梅县', '267', '1', '0');
INSERT INTO `area` VALUES ('2894', '咸安区', '268', '1', '0');
INSERT INTO `area` VALUES ('2895', '嘉鱼县', '268', '1', '0');
INSERT INTO `area` VALUES ('2896', '崇阳县', '268', '1', '0');
INSERT INTO `area` VALUES ('2897', '赤壁市', '268', '1', '0');
INSERT INTO `area` VALUES ('2898', '通城县', '268', '1', '0');
INSERT INTO `area` VALUES ('2899', '通山县', '268', '1', '0');
INSERT INTO `area` VALUES ('2900', '广水市', '269', '1', '0');
INSERT INTO `area` VALUES ('2901', '曾都区', '269', '1', '0');
INSERT INTO `area` VALUES ('2902', '利川市', '270', '1', '0');
INSERT INTO `area` VALUES ('2903', '咸丰县', '270', '1', '0');
INSERT INTO `area` VALUES ('2904', '宣恩县', '270', '1', '0');
INSERT INTO `area` VALUES ('2905', '巴东县', '270', '1', '0');
INSERT INTO `area` VALUES ('2906', '建始县', '270', '1', '0');
INSERT INTO `area` VALUES ('2907', '恩施市', '270', '1', '0');
INSERT INTO `area` VALUES ('2908', '来凤县', '270', '1', '0');
INSERT INTO `area` VALUES ('2909', '鹤峰县', '270', '1', '0');
INSERT INTO `area` VALUES ('2910', '仙桃市', '271', '1', '0');
INSERT INTO `area` VALUES ('2911', '潜江市', '272', '1', '0');
INSERT INTO `area` VALUES ('2912', '天门市', '273', '1', '0');
INSERT INTO `area` VALUES ('2913', '神农架林区', '274', '1', '0');
INSERT INTO `area` VALUES ('2914', '天心区', '275', '1', '0');
INSERT INTO `area` VALUES ('2915', '宁乡县', '275', '1', '0');
INSERT INTO `area` VALUES ('2916', '岳麓区', '275', '1', '0');
INSERT INTO `area` VALUES ('2917', '开福区', '275', '1', '0');
INSERT INTO `area` VALUES ('2918', '望城县', '275', '1', '0');
INSERT INTO `area` VALUES ('2919', '浏阳市', '275', '1', '0');
INSERT INTO `area` VALUES ('2920', '芙蓉区', '275', '1', '0');
INSERT INTO `area` VALUES ('2921', '长沙县', '275', '1', '0');
INSERT INTO `area` VALUES ('2922', '雨花区', '275', '1', '0');
INSERT INTO `area` VALUES ('2923', '天元区', '276', '1', '0');
INSERT INTO `area` VALUES ('2924', '攸县', '276', '1', '0');
INSERT INTO `area` VALUES ('2925', '株洲县', '276', '1', '0');
INSERT INTO `area` VALUES ('2926', '炎陵县', '276', '1', '0');
INSERT INTO `area` VALUES ('2927', '石峰区', '276', '1', '0');
INSERT INTO `area` VALUES ('2928', '芦淞区', '276', '1', '0');
INSERT INTO `area` VALUES ('2929', '茶陵县', '276', '1', '0');
INSERT INTO `area` VALUES ('2930', '荷塘区', '276', '1', '0');
INSERT INTO `area` VALUES ('2931', '醴陵市', '276', '1', '0');
INSERT INTO `area` VALUES ('2932', '岳塘区', '277', '1', '0');
INSERT INTO `area` VALUES ('2933', '湘乡市', '277', '1', '0');
INSERT INTO `area` VALUES ('2934', '湘潭县', '277', '1', '0');
INSERT INTO `area` VALUES ('2935', '雨湖区', '277', '1', '0');
INSERT INTO `area` VALUES ('2936', '韶山市', '277', '1', '0');
INSERT INTO `area` VALUES ('2937', '南岳区', '278', '1', '0');
INSERT INTO `area` VALUES ('2938', '常宁市', '278', '1', '0');
INSERT INTO `area` VALUES ('2939', '珠晖区', '278', '1', '0');
INSERT INTO `area` VALUES ('2940', '石鼓区', '278', '1', '0');
INSERT INTO `area` VALUES ('2941', '祁东县', '278', '1', '0');
INSERT INTO `area` VALUES ('2942', '耒阳市', '278', '1', '0');
INSERT INTO `area` VALUES ('2943', '蒸湘区', '278', '1', '0');
INSERT INTO `area` VALUES ('2944', '衡东县', '278', '1', '0');
INSERT INTO `area` VALUES ('2945', '衡南县', '278', '1', '0');
INSERT INTO `area` VALUES ('2946', '衡山县', '278', '1', '0');
INSERT INTO `area` VALUES ('2947', '衡阳县', '278', '1', '0');
INSERT INTO `area` VALUES ('2948', '雁峰区', '278', '1', '0');
INSERT INTO `area` VALUES ('2949', '北塔区', '279', '1', '0');
INSERT INTO `area` VALUES ('2950', '双清区', '279', '1', '0');
INSERT INTO `area` VALUES ('2951', '城步苗族自治县', '279', '1', '0');
INSERT INTO `area` VALUES ('2952', '大祥区', '279', '1', '0');
INSERT INTO `area` VALUES ('2953', '新宁县', '279', '1', '0');
INSERT INTO `area` VALUES ('2954', '新邵县', '279', '1', '0');
INSERT INTO `area` VALUES ('2955', '武冈市', '279', '1', '0');
INSERT INTO `area` VALUES ('2956', '洞口县', '279', '1', '0');
INSERT INTO `area` VALUES ('2957', '绥宁县', '279', '1', '0');
INSERT INTO `area` VALUES ('2958', '邵东县', '279', '1', '0');
INSERT INTO `area` VALUES ('2959', '邵阳县', '279', '1', '0');
INSERT INTO `area` VALUES ('2960', '隆回县', '279', '1', '0');
INSERT INTO `area` VALUES ('2961', '临湘市', '280', '1', '0');
INSERT INTO `area` VALUES ('2962', '云溪区', '280', '1', '0');
INSERT INTO `area` VALUES ('2963', '华容县', '280', '1', '0');
INSERT INTO `area` VALUES ('2964', '君山区', '280', '1', '0');
INSERT INTO `area` VALUES ('2965', '岳阳县', '280', '1', '0');
INSERT INTO `area` VALUES ('2966', '岳阳楼区', '280', '1', '0');
INSERT INTO `area` VALUES ('2967', '平江县', '280', '1', '0');
INSERT INTO `area` VALUES ('2968', '汨罗市', '280', '1', '0');
INSERT INTO `area` VALUES ('2969', '湘阴县', '280', '1', '0');
INSERT INTO `area` VALUES ('2970', '临澧县', '281', '1', '0');
INSERT INTO `area` VALUES ('2971', '安乡县', '281', '1', '0');
INSERT INTO `area` VALUES ('2972', '桃源县', '281', '1', '0');
INSERT INTO `area` VALUES ('2973', '武陵区', '281', '1', '0');
INSERT INTO `area` VALUES ('2974', '汉寿县', '281', '1', '0');
INSERT INTO `area` VALUES ('2975', '津市市', '281', '1', '0');
INSERT INTO `area` VALUES ('2976', '澧县', '281', '1', '0');
INSERT INTO `area` VALUES ('2977', '石门县', '281', '1', '0');
INSERT INTO `area` VALUES ('2978', '鼎城区', '281', '1', '0');
INSERT INTO `area` VALUES ('2979', '慈利县', '282', '1', '0');
INSERT INTO `area` VALUES ('2980', '桑植县', '282', '1', '0');
INSERT INTO `area` VALUES ('2981', '武陵源区', '282', '1', '0');
INSERT INTO `area` VALUES ('2982', '永定区', '282', '1', '0');
INSERT INTO `area` VALUES ('2983', '南县', '283', '1', '0');
INSERT INTO `area` VALUES ('2984', '安化县', '283', '1', '0');
INSERT INTO `area` VALUES ('2985', '桃江县', '283', '1', '0');
INSERT INTO `area` VALUES ('2986', '沅江市', '283', '1', '0');
INSERT INTO `area` VALUES ('2987', '资阳区', '283', '1', '0');
INSERT INTO `area` VALUES ('2988', '赫山区', '283', '1', '0');
INSERT INTO `area` VALUES ('2989', '临武县', '284', '1', '0');
INSERT INTO `area` VALUES ('2990', '北湖区', '284', '1', '0');
INSERT INTO `area` VALUES ('2991', '嘉禾县', '284', '1', '0');
INSERT INTO `area` VALUES ('2992', '安仁县', '284', '1', '0');
INSERT INTO `area` VALUES ('2993', '宜章县', '284', '1', '0');
INSERT INTO `area` VALUES ('2994', '桂东县', '284', '1', '0');
INSERT INTO `area` VALUES ('2995', '桂阳县', '284', '1', '0');
INSERT INTO `area` VALUES ('2996', '永兴县', '284', '1', '0');
INSERT INTO `area` VALUES ('2997', '汝城县', '284', '1', '0');
INSERT INTO `area` VALUES ('2998', '苏仙区', '284', '1', '0');
INSERT INTO `area` VALUES ('2999', '资兴市', '284', '1', '0');
INSERT INTO `area` VALUES ('3000', '东安县', '285', '1', '0');
INSERT INTO `area` VALUES ('3001', '冷水滩区', '285', '1', '0');
INSERT INTO `area` VALUES ('3002', '双牌县', '285', '1', '0');
INSERT INTO `area` VALUES ('3003', '宁远县', '285', '1', '0');
INSERT INTO `area` VALUES ('3004', '新田县', '285', '1', '0');
INSERT INTO `area` VALUES ('3005', '江华瑶族自治县', '285', '1', '0');
INSERT INTO `area` VALUES ('3006', '江永县', '285', '1', '0');
INSERT INTO `area` VALUES ('3007', '祁阳县', '285', '1', '0');
INSERT INTO `area` VALUES ('3008', '蓝山县', '285', '1', '0');
INSERT INTO `area` VALUES ('3009', '道县', '285', '1', '0');
INSERT INTO `area` VALUES ('3010', '零陵区', '285', '1', '0');
INSERT INTO `area` VALUES ('3011', '中方县', '286', '1', '0');
INSERT INTO `area` VALUES ('3012', '会同县', '286', '1', '0');
INSERT INTO `area` VALUES ('3013', '新晃侗族自治县', '286', '1', '0');
INSERT INTO `area` VALUES ('3014', '沅陵县', '286', '1', '0');
INSERT INTO `area` VALUES ('3015', '洪江市/洪江区', '286', '1', '0');
INSERT INTO `area` VALUES ('3016', '溆浦县', '286', '1', '0');
INSERT INTO `area` VALUES ('3017', '芷江侗族自治县', '286', '1', '0');
INSERT INTO `area` VALUES ('3018', '辰溪县', '286', '1', '0');
INSERT INTO `area` VALUES ('3019', '通道侗族自治县', '286', '1', '0');
INSERT INTO `area` VALUES ('3020', '靖州苗族侗族自治县', '286', '1', '0');
INSERT INTO `area` VALUES ('3021', '鹤城区', '286', '1', '0');
INSERT INTO `area` VALUES ('3022', '麻阳苗族自治县', '286', '1', '0');
INSERT INTO `area` VALUES ('3023', '冷水江市', '287', '1', '0');
INSERT INTO `area` VALUES ('3024', '双峰县', '287', '1', '0');
INSERT INTO `area` VALUES ('3025', '娄星区', '287', '1', '0');
INSERT INTO `area` VALUES ('3026', '新化县', '287', '1', '0');
INSERT INTO `area` VALUES ('3027', '涟源市', '287', '1', '0');
INSERT INTO `area` VALUES ('3028', '保靖县', '288', '1', '0');
INSERT INTO `area` VALUES ('3029', '凤凰县', '288', '1', '0');
INSERT INTO `area` VALUES ('3030', '古丈县', '288', '1', '0');
INSERT INTO `area` VALUES ('3031', '吉首市', '288', '1', '0');
INSERT INTO `area` VALUES ('3032', '永顺县', '288', '1', '0');
INSERT INTO `area` VALUES ('3033', '泸溪县', '288', '1', '0');
INSERT INTO `area` VALUES ('3034', '花垣县', '288', '1', '0');
INSERT INTO `area` VALUES ('3035', '龙山县', '288', '1', '0');
INSERT INTO `area` VALUES ('3036', '萝岗区', '289', '1', '0');
INSERT INTO `area` VALUES ('3037', '南沙区', '289', '1', '0');
INSERT INTO `area` VALUES ('3038', '从化市', '289', '1', '0');
INSERT INTO `area` VALUES ('3039', '增城市', '289', '1', '0');
INSERT INTO `area` VALUES ('3040', '天河区', '289', '1', '0');
INSERT INTO `area` VALUES ('3041', '海珠区', '289', '1', '0');
INSERT INTO `area` VALUES ('3042', '番禺区', '289', '1', '0');
INSERT INTO `area` VALUES ('3043', '白云区', '289', '1', '0');
INSERT INTO `area` VALUES ('3044', '花都区', '289', '1', '0');
INSERT INTO `area` VALUES ('3045', '荔湾区', '289', '1', '0');
INSERT INTO `area` VALUES ('3046', '越秀区', '289', '1', '0');
INSERT INTO `area` VALUES ('3047', '黄埔区', '289', '1', '0');
INSERT INTO `area` VALUES ('3048', '乐昌市', '290', '1', '0');
INSERT INTO `area` VALUES ('3049', '乳源瑶族自治县', '290', '1', '0');
INSERT INTO `area` VALUES ('3050', '仁化县', '290', '1', '0');
INSERT INTO `area` VALUES ('3051', '南雄市', '290', '1', '0');
INSERT INTO `area` VALUES ('3052', '始兴县', '290', '1', '0');
INSERT INTO `area` VALUES ('3053', '新丰县', '290', '1', '0');
INSERT INTO `area` VALUES ('3054', '曲江区', '290', '1', '0');
INSERT INTO `area` VALUES ('3055', '武江区', '290', '1', '0');
INSERT INTO `area` VALUES ('3056', '浈江区', '290', '1', '0');
INSERT INTO `area` VALUES ('3057', '翁源县', '290', '1', '0');
INSERT INTO `area` VALUES ('3058', '南山区', '291', '1', '0');
INSERT INTO `area` VALUES ('3059', '宝安区', '291', '1', '0');
INSERT INTO `area` VALUES ('3060', '盐田区', '291', '1', '0');
INSERT INTO `area` VALUES ('3061', '福田区', '291', '1', '0');
INSERT INTO `area` VALUES ('3062', '罗湖区', '291', '1', '0');
INSERT INTO `area` VALUES ('3063', '龙岗区', '291', '1', '0');
INSERT INTO `area` VALUES ('3064', '斗门区', '292', '1', '0');
INSERT INTO `area` VALUES ('3065', '金湾区', '292', '1', '0');
INSERT INTO `area` VALUES ('3066', '香洲区', '292', '1', '0');
INSERT INTO `area` VALUES ('3067', '南澳县', '293', '1', '0');
INSERT INTO `area` VALUES ('3068', '潮南区', '293', '1', '0');
INSERT INTO `area` VALUES ('3069', '潮阳区', '293', '1', '0');
INSERT INTO `area` VALUES ('3070', '澄海区', '293', '1', '0');
INSERT INTO `area` VALUES ('3071', '濠江区', '293', '1', '0');
INSERT INTO `area` VALUES ('3072', '金平区', '293', '1', '0');
INSERT INTO `area` VALUES ('3073', '龙湖区', '293', '1', '0');
INSERT INTO `area` VALUES ('3074', '三水区', '294', '1', '0');
INSERT INTO `area` VALUES ('3075', '南海区', '294', '1', '0');
INSERT INTO `area` VALUES ('3076', '禅城区', '294', '1', '0');
INSERT INTO `area` VALUES ('3077', '顺德区', '294', '1', '0');
INSERT INTO `area` VALUES ('3078', '高明区', '294', '1', '0');
INSERT INTO `area` VALUES ('3079', '台山市', '295', '1', '0');
INSERT INTO `area` VALUES ('3080', '开平市', '295', '1', '0');
INSERT INTO `area` VALUES ('3081', '恩平市', '295', '1', '0');
INSERT INTO `area` VALUES ('3082', '新会区', '295', '1', '0');
INSERT INTO `area` VALUES ('3083', '江海区', '295', '1', '0');
INSERT INTO `area` VALUES ('3084', '蓬江区', '295', '1', '0');
INSERT INTO `area` VALUES ('3085', '鹤山市', '295', '1', '0');
INSERT INTO `area` VALUES ('3086', '吴川市', '296', '1', '0');
INSERT INTO `area` VALUES ('3087', '坡头区', '296', '1', '0');
INSERT INTO `area` VALUES ('3088', '廉江市', '296', '1', '0');
INSERT INTO `area` VALUES ('3089', '徐闻县', '296', '1', '0');
INSERT INTO `area` VALUES ('3090', '赤坎区', '296', '1', '0');
INSERT INTO `area` VALUES ('3091', '遂溪县', '296', '1', '0');
INSERT INTO `area` VALUES ('3092', '雷州市', '296', '1', '0');
INSERT INTO `area` VALUES ('3093', '霞山区', '296', '1', '0');
INSERT INTO `area` VALUES ('3094', '麻章区', '296', '1', '0');
INSERT INTO `area` VALUES ('3095', '信宜市', '297', '1', '0');
INSERT INTO `area` VALUES ('3096', '化州市', '297', '1', '0');
INSERT INTO `area` VALUES ('3097', '电白县', '297', '1', '0');
INSERT INTO `area` VALUES ('3098', '茂南区', '297', '1', '0');
INSERT INTO `area` VALUES ('3099', '茂港区', '297', '1', '0');
INSERT INTO `area` VALUES ('3100', '高州市', '297', '1', '0');
INSERT INTO `area` VALUES ('3101', '四会市', '298', '1', '0');
INSERT INTO `area` VALUES ('3102', '封开县', '298', '1', '0');
INSERT INTO `area` VALUES ('3103', '广宁县', '298', '1', '0');
INSERT INTO `area` VALUES ('3104', '德庆县', '298', '1', '0');
INSERT INTO `area` VALUES ('3105', '怀集县', '298', '1', '0');
INSERT INTO `area` VALUES ('3106', '端州区', '298', '1', '0');
INSERT INTO `area` VALUES ('3107', '高要市', '298', '1', '0');
INSERT INTO `area` VALUES ('3108', '鼎湖区', '298', '1', '0');
INSERT INTO `area` VALUES ('3109', '博罗县', '299', '1', '0');
INSERT INTO `area` VALUES ('3110', '惠东县', '299', '1', '0');
INSERT INTO `area` VALUES ('3111', '惠城区', '299', '1', '0');
INSERT INTO `area` VALUES ('3112', '惠阳区', '299', '1', '0');
INSERT INTO `area` VALUES ('3113', '龙门县', '299', '1', '0');
INSERT INTO `area` VALUES ('3114', '丰顺县', '300', '1', '0');
INSERT INTO `area` VALUES ('3115', '五华县', '300', '1', '0');
INSERT INTO `area` VALUES ('3116', '兴宁市', '300', '1', '0');
INSERT INTO `area` VALUES ('3117', '大埔县', '300', '1', '0');
INSERT INTO `area` VALUES ('3118', '平远县', '300', '1', '0');
INSERT INTO `area` VALUES ('3119', '梅县', '300', '1', '0');
INSERT INTO `area` VALUES ('3120', '梅江区', '300', '1', '0');
INSERT INTO `area` VALUES ('3121', '蕉岭县', '300', '1', '0');
INSERT INTO `area` VALUES ('3122', '城区', '301', '1', '0');
INSERT INTO `area` VALUES ('3123', '海丰县', '301', '1', '0');
INSERT INTO `area` VALUES ('3124', '陆丰市', '301', '1', '0');
INSERT INTO `area` VALUES ('3125', '陆河县', '301', '1', '0');
INSERT INTO `area` VALUES ('3126', '东源县', '302', '1', '0');
INSERT INTO `area` VALUES ('3127', '和平县', '302', '1', '0');
INSERT INTO `area` VALUES ('3128', '源城区', '302', '1', '0');
INSERT INTO `area` VALUES ('3129', '紫金县', '302', '1', '0');
INSERT INTO `area` VALUES ('3130', '连平县', '302', '1', '0');
INSERT INTO `area` VALUES ('3131', '龙川县', '302', '1', '0');
INSERT INTO `area` VALUES ('3132', '江城区', '303', '1', '0');
INSERT INTO `area` VALUES ('3133', '阳东县', '303', '1', '0');
INSERT INTO `area` VALUES ('3134', '阳春市', '303', '1', '0');
INSERT INTO `area` VALUES ('3135', '阳西县', '303', '1', '0');
INSERT INTO `area` VALUES ('3136', '佛冈县', '304', '1', '0');
INSERT INTO `area` VALUES ('3137', '清城区', '304', '1', '0');
INSERT INTO `area` VALUES ('3138', '清新县', '304', '1', '0');
INSERT INTO `area` VALUES ('3139', '英德市', '304', '1', '0');
INSERT INTO `area` VALUES ('3140', '连南瑶族自治县', '304', '1', '0');
INSERT INTO `area` VALUES ('3141', '连山壮族瑶族自治县', '304', '1', '0');
INSERT INTO `area` VALUES ('3142', '连州市', '304', '1', '0');
INSERT INTO `area` VALUES ('3143', '阳山县', '304', '1', '0');
INSERT INTO `area` VALUES ('3144', '东莞市', '305', '1', '0');
INSERT INTO `area` VALUES ('3145', '中山市', '306', '1', '0');
INSERT INTO `area` VALUES ('3146', '湘桥区', '307', '1', '0');
INSERT INTO `area` VALUES ('3147', '潮安县', '307', '1', '0');
INSERT INTO `area` VALUES ('3148', '饶平县', '307', '1', '0');
INSERT INTO `area` VALUES ('3149', '惠来县', '308', '1', '0');
INSERT INTO `area` VALUES ('3150', '揭东县', '308', '1', '0');
INSERT INTO `area` VALUES ('3151', '揭西县', '308', '1', '0');
INSERT INTO `area` VALUES ('3152', '普宁市', '308', '1', '0');
INSERT INTO `area` VALUES ('3153', '榕城区', '308', '1', '0');
INSERT INTO `area` VALUES ('3154', '云城区', '309', '1', '0');
INSERT INTO `area` VALUES ('3155', '云安县', '309', '1', '0');
INSERT INTO `area` VALUES ('3156', '新兴县', '309', '1', '0');
INSERT INTO `area` VALUES ('3157', '罗定市', '309', '1', '0');
INSERT INTO `area` VALUES ('3158', '郁南县', '309', '1', '0');
INSERT INTO `area` VALUES ('3159', '上林县', '310', '1', '0');
INSERT INTO `area` VALUES ('3160', '兴宁区', '310', '1', '0');
INSERT INTO `area` VALUES ('3161', '宾阳县', '310', '1', '0');
INSERT INTO `area` VALUES ('3162', '横县', '310', '1', '0');
INSERT INTO `area` VALUES ('3163', '武鸣县', '310', '1', '0');
INSERT INTO `area` VALUES ('3164', '江南区', '310', '1', '0');
INSERT INTO `area` VALUES ('3165', '良庆区', '310', '1', '0');
INSERT INTO `area` VALUES ('3166', '西乡塘区', '310', '1', '0');
INSERT INTO `area` VALUES ('3167', '邕宁区', '310', '1', '0');
INSERT INTO `area` VALUES ('3168', '隆安县', '310', '1', '0');
INSERT INTO `area` VALUES ('3169', '青秀区', '310', '1', '0');
INSERT INTO `area` VALUES ('3170', '马山县', '310', '1', '0');
INSERT INTO `area` VALUES ('3171', '三江侗族自治县', '311', '1', '0');
INSERT INTO `area` VALUES ('3172', '城中区', '311', '1', '0');
INSERT INTO `area` VALUES ('3173', '柳北区', '311', '1', '0');
INSERT INTO `area` VALUES ('3174', '柳南区', '311', '1', '0');
INSERT INTO `area` VALUES ('3175', '柳城县', '311', '1', '0');
INSERT INTO `area` VALUES ('3176', '柳江县', '311', '1', '0');
INSERT INTO `area` VALUES ('3177', '融安县', '311', '1', '0');
INSERT INTO `area` VALUES ('3178', '融水苗族自治县', '311', '1', '0');
INSERT INTO `area` VALUES ('3179', '鱼峰区', '311', '1', '0');
INSERT INTO `area` VALUES ('3180', '鹿寨县', '311', '1', '0');
INSERT INTO `area` VALUES ('3181', '七星区', '312', '1', '0');
INSERT INTO `area` VALUES ('3182', '临桂县', '312', '1', '0');
INSERT INTO `area` VALUES ('3183', '全州县', '312', '1', '0');
INSERT INTO `area` VALUES ('3184', '兴安县', '312', '1', '0');
INSERT INTO `area` VALUES ('3185', '叠彩区', '312', '1', '0');
INSERT INTO `area` VALUES ('3186', '平乐县', '312', '1', '0');
INSERT INTO `area` VALUES ('3187', '恭城瑶族自治县', '312', '1', '0');
INSERT INTO `area` VALUES ('3188', '永福县', '312', '1', '0');
INSERT INTO `area` VALUES ('3189', '灌阳县', '312', '1', '0');
INSERT INTO `area` VALUES ('3190', '灵川县', '312', '1', '0');
INSERT INTO `area` VALUES ('3191', '秀峰区', '312', '1', '0');
INSERT INTO `area` VALUES ('3192', '荔浦县', '312', '1', '0');
INSERT INTO `area` VALUES ('3193', '象山区', '312', '1', '0');
INSERT INTO `area` VALUES ('3194', '资源县', '312', '1', '0');
INSERT INTO `area` VALUES ('3195', '阳朔县', '312', '1', '0');
INSERT INTO `area` VALUES ('3196', '雁山区', '312', '1', '0');
INSERT INTO `area` VALUES ('3197', '龙胜各族自治县', '312', '1', '0');
INSERT INTO `area` VALUES ('3198', '万秀区', '313', '1', '0');
INSERT INTO `area` VALUES ('3199', '岑溪市', '313', '1', '0');
INSERT INTO `area` VALUES ('3200', '苍梧县', '313', '1', '0');
INSERT INTO `area` VALUES ('3201', '蒙山县', '313', '1', '0');
INSERT INTO `area` VALUES ('3202', '藤县', '313', '1', '0');
INSERT INTO `area` VALUES ('3203', '蝶山区', '313', '1', '0');
INSERT INTO `area` VALUES ('3204', '长洲区', '313', '1', '0');
INSERT INTO `area` VALUES ('3205', '合浦县', '314', '1', '0');
INSERT INTO `area` VALUES ('3206', '海城区', '314', '1', '0');
INSERT INTO `area` VALUES ('3207', '铁山港区', '314', '1', '0');
INSERT INTO `area` VALUES ('3208', '银海区', '314', '1', '0');
INSERT INTO `area` VALUES ('3209', '上思县', '315', '1', '0');
INSERT INTO `area` VALUES ('3210', '东兴市', '315', '1', '0');
INSERT INTO `area` VALUES ('3211', '港口区', '315', '1', '0');
INSERT INTO `area` VALUES ('3212', '防城区', '315', '1', '0');
INSERT INTO `area` VALUES ('3213', '浦北县', '316', '1', '0');
INSERT INTO `area` VALUES ('3214', '灵山县', '316', '1', '0');
INSERT INTO `area` VALUES ('3215', '钦北区', '316', '1', '0');
INSERT INTO `area` VALUES ('3216', '钦南区', '316', '1', '0');
INSERT INTO `area` VALUES ('3217', '平南县', '317', '1', '0');
INSERT INTO `area` VALUES ('3218', '桂平市', '317', '1', '0');
INSERT INTO `area` VALUES ('3219', '港北区', '317', '1', '0');
INSERT INTO `area` VALUES ('3220', '港南区', '317', '1', '0');
INSERT INTO `area` VALUES ('3221', '覃塘区', '317', '1', '0');
INSERT INTO `area` VALUES ('3222', '兴业县', '318', '1', '0');
INSERT INTO `area` VALUES ('3223', '北流市', '318', '1', '0');
INSERT INTO `area` VALUES ('3224', '博白县', '318', '1', '0');
INSERT INTO `area` VALUES ('3225', '容县', '318', '1', '0');
INSERT INTO `area` VALUES ('3226', '玉州区', '318', '1', '0');
INSERT INTO `area` VALUES ('3227', '陆川县', '318', '1', '0');
INSERT INTO `area` VALUES ('3228', '乐业县', '319', '1', '0');
INSERT INTO `area` VALUES ('3229', '凌云县', '319', '1', '0');
INSERT INTO `area` VALUES ('3230', '右江区', '319', '1', '0');
INSERT INTO `area` VALUES ('3231', '平果县', '319', '1', '0');
INSERT INTO `area` VALUES ('3232', '德保县', '319', '1', '0');
INSERT INTO `area` VALUES ('3233', '田东县', '319', '1', '0');
INSERT INTO `area` VALUES ('3234', '田林县', '319', '1', '0');
INSERT INTO `area` VALUES ('3235', '田阳县', '319', '1', '0');
INSERT INTO `area` VALUES ('3236', '西林县', '319', '1', '0');
INSERT INTO `area` VALUES ('3237', '那坡县', '319', '1', '0');
INSERT INTO `area` VALUES ('3238', '隆林各族自治县', '319', '1', '0');
INSERT INTO `area` VALUES ('3239', '靖西县', '319', '1', '0');
INSERT INTO `area` VALUES ('3240', '八步区', '320', '1', '0');
INSERT INTO `area` VALUES ('3241', '富川瑶族自治县', '320', '1', '0');
INSERT INTO `area` VALUES ('3242', '昭平县', '320', '1', '0');
INSERT INTO `area` VALUES ('3243', '钟山县', '320', '1', '0');
INSERT INTO `area` VALUES ('3244', '东兰县', '321', '1', '0');
INSERT INTO `area` VALUES ('3245', '凤山县', '321', '1', '0');
INSERT INTO `area` VALUES ('3246', '南丹县', '321', '1', '0');
INSERT INTO `area` VALUES ('3247', '大化瑶族自治县', '321', '1', '0');
INSERT INTO `area` VALUES ('3248', '天峨县', '321', '1', '0');
INSERT INTO `area` VALUES ('3249', '宜州市', '321', '1', '0');
INSERT INTO `area` VALUES ('3250', '巴马瑶族自治县', '321', '1', '0');
INSERT INTO `area` VALUES ('3251', '环江毛南族自治县', '321', '1', '0');
INSERT INTO `area` VALUES ('3252', '罗城仫佬族自治县', '321', '1', '0');
INSERT INTO `area` VALUES ('3253', '都安瑶族自治县', '321', '1', '0');
INSERT INTO `area` VALUES ('3254', '金城江区', '321', '1', '0');
INSERT INTO `area` VALUES ('3255', '兴宾区', '322', '1', '0');
INSERT INTO `area` VALUES ('3256', '合山市', '322', '1', '0');
INSERT INTO `area` VALUES ('3257', '忻城县', '322', '1', '0');
INSERT INTO `area` VALUES ('3258', '武宣县', '322', '1', '0');
INSERT INTO `area` VALUES ('3259', '象州县', '322', '1', '0');
INSERT INTO `area` VALUES ('3260', '金秀瑶族自治县', '322', '1', '0');
INSERT INTO `area` VALUES ('3261', '凭祥市', '323', '1', '0');
INSERT INTO `area` VALUES ('3262', '大新县', '323', '1', '0');
INSERT INTO `area` VALUES ('3263', '天等县', '323', '1', '0');
INSERT INTO `area` VALUES ('3264', '宁明县', '323', '1', '0');
INSERT INTO `area` VALUES ('3265', '扶绥县', '323', '1', '0');
INSERT INTO `area` VALUES ('3266', '江州区', '323', '1', '0');
INSERT INTO `area` VALUES ('3267', '龙州县', '323', '1', '0');
INSERT INTO `area` VALUES ('3268', '琼山区', '324', '1', '0');
INSERT INTO `area` VALUES ('3269', '秀英区', '324', '1', '0');
INSERT INTO `area` VALUES ('3270', '美兰区', '324', '1', '0');
INSERT INTO `area` VALUES ('3271', '龙华区', '324', '1', '0');
INSERT INTO `area` VALUES ('3272', '三亚市', '325', '1', '0');
INSERT INTO `area` VALUES ('3273', '五指山市', '326', '1', '0');
INSERT INTO `area` VALUES ('3274', '琼海市', '327', '1', '0');
INSERT INTO `area` VALUES ('3275', '儋州市', '328', '1', '0');
INSERT INTO `area` VALUES ('3276', '文昌市', '329', '1', '0');
INSERT INTO `area` VALUES ('3277', '万宁市', '330', '1', '0');
INSERT INTO `area` VALUES ('3278', '东方市', '331', '1', '0');
INSERT INTO `area` VALUES ('3279', '定安县', '332', '1', '0');
INSERT INTO `area` VALUES ('3280', '屯昌县', '333', '1', '0');
INSERT INTO `area` VALUES ('3281', '澄迈县', '334', '1', '0');
INSERT INTO `area` VALUES ('3282', '临高县', '335', '1', '0');
INSERT INTO `area` VALUES ('3283', '白沙黎族自治县', '336', '1', '0');
INSERT INTO `area` VALUES ('3284', '昌江黎族自治县', '337', '1', '0');
INSERT INTO `area` VALUES ('3285', '乐东黎族自治县', '338', '1', '0');
INSERT INTO `area` VALUES ('3286', '陵水黎族自治县', '339', '1', '0');
INSERT INTO `area` VALUES ('3287', '保亭黎族苗族自治县', '340', '1', '0');
INSERT INTO `area` VALUES ('3288', '琼中黎族苗族自治县', '341', '1', '0');
INSERT INTO `area` VALUES ('4209', '双流县', '385', '1', '0');
INSERT INTO `area` VALUES ('4210', '大邑县', '385', '1', '0');
INSERT INTO `area` VALUES ('4211', '崇州市', '385', '1', '0');
INSERT INTO `area` VALUES ('4212', '彭州市', '385', '1', '0');
INSERT INTO `area` VALUES ('4213', '成华区', '385', '1', '0');
INSERT INTO `area` VALUES ('4214', '新津县', '385', '1', '0');
INSERT INTO `area` VALUES ('4215', '新都区', '385', '1', '0');
INSERT INTO `area` VALUES ('4216', '武侯区', '385', '1', '0');
INSERT INTO `area` VALUES ('4217', '温江区', '385', '1', '0');
INSERT INTO `area` VALUES ('4218', '蒲江县', '385', '1', '0');
INSERT INTO `area` VALUES ('4219', '邛崃市', '385', '1', '0');
INSERT INTO `area` VALUES ('4220', '郫县', '385', '1', '0');
INSERT INTO `area` VALUES ('4221', '都江堰市', '385', '1', '0');
INSERT INTO `area` VALUES ('4222', '金堂县', '385', '1', '0');
INSERT INTO `area` VALUES ('4223', '金牛区', '385', '1', '0');
INSERT INTO `area` VALUES ('4224', '锦江区', '385', '1', '0');
INSERT INTO `area` VALUES ('4225', '青白江区', '385', '1', '0');
INSERT INTO `area` VALUES ('4226', '青羊区', '385', '1', '0');
INSERT INTO `area` VALUES ('4227', '龙泉驿区', '385', '1', '0');
INSERT INTO `area` VALUES ('4228', '大安区', '386', '1', '0');
INSERT INTO `area` VALUES ('4229', '富顺县', '386', '1', '0');
INSERT INTO `area` VALUES ('4230', '沿滩区', '386', '1', '0');
INSERT INTO `area` VALUES ('4231', '自流井区', '386', '1', '0');
INSERT INTO `area` VALUES ('4232', '荣县', '386', '1', '0');
INSERT INTO `area` VALUES ('4233', '贡井区', '386', '1', '0');
INSERT INTO `area` VALUES ('4234', '东区', '387', '1', '0');
INSERT INTO `area` VALUES ('4235', '仁和区', '387', '1', '0');
INSERT INTO `area` VALUES ('4236', '盐边县', '387', '1', '0');
INSERT INTO `area` VALUES ('4237', '米易县', '387', '1', '0');
INSERT INTO `area` VALUES ('4238', '西区', '387', '1', '0');
INSERT INTO `area` VALUES ('4239', '叙永县', '388', '1', '0');
INSERT INTO `area` VALUES ('4240', '古蔺县', '388', '1', '0');
INSERT INTO `area` VALUES ('4241', '合江县', '388', '1', '0');
INSERT INTO `area` VALUES ('4242', '江阳区', '388', '1', '0');
INSERT INTO `area` VALUES ('4243', '泸县', '388', '1', '0');
INSERT INTO `area` VALUES ('4244', '纳溪区', '388', '1', '0');
INSERT INTO `area` VALUES ('4245', '龙马潭区', '388', '1', '0');
INSERT INTO `area` VALUES ('4246', '中江县', '389', '1', '0');
INSERT INTO `area` VALUES ('4247', '什邡市', '389', '1', '0');
INSERT INTO `area` VALUES ('4248', '广汉市', '389', '1', '0');
INSERT INTO `area` VALUES ('4249', '旌阳区', '389', '1', '0');
INSERT INTO `area` VALUES ('4250', '绵竹市', '389', '1', '0');
INSERT INTO `area` VALUES ('4251', '罗江县', '389', '1', '0');
INSERT INTO `area` VALUES ('4252', '三台县', '390', '1', '0');
INSERT INTO `area` VALUES ('4253', '北川羌族自治县', '390', '1', '0');
INSERT INTO `area` VALUES ('4254', '安县', '390', '1', '0');
INSERT INTO `area` VALUES ('4255', '平武县', '390', '1', '0');
INSERT INTO `area` VALUES ('4256', '梓潼县', '390', '1', '0');
INSERT INTO `area` VALUES ('4257', '江油市', '390', '1', '0');
INSERT INTO `area` VALUES ('4258', '涪城区', '390', '1', '0');
INSERT INTO `area` VALUES ('4259', '游仙区', '390', '1', '0');
INSERT INTO `area` VALUES ('4260', '盐亭县', '390', '1', '0');
INSERT INTO `area` VALUES ('4261', '元坝区', '391', '1', '0');
INSERT INTO `area` VALUES ('4262', '利州区', '391', '1', '0');
INSERT INTO `area` VALUES ('4263', '剑阁县', '391', '1', '0');
INSERT INTO `area` VALUES ('4264', '旺苍县', '391', '1', '0');
INSERT INTO `area` VALUES ('4265', '朝天区', '391', '1', '0');
INSERT INTO `area` VALUES ('4266', '苍溪县', '391', '1', '0');
INSERT INTO `area` VALUES ('4267', '青川县', '391', '1', '0');
INSERT INTO `area` VALUES ('4268', '大英县', '392', '1', '0');
INSERT INTO `area` VALUES ('4269', '安居区', '392', '1', '0');
INSERT INTO `area` VALUES ('4270', '射洪县', '392', '1', '0');
INSERT INTO `area` VALUES ('4271', '船山区', '392', '1', '0');
INSERT INTO `area` VALUES ('4272', '蓬溪县', '392', '1', '0');
INSERT INTO `area` VALUES ('4273', '东兴区', '393', '1', '0');
INSERT INTO `area` VALUES ('4274', '威远县', '393', '1', '0');
INSERT INTO `area` VALUES ('4275', '市中区', '393', '1', '0');
INSERT INTO `area` VALUES ('4276', '资中县', '393', '1', '0');
INSERT INTO `area` VALUES ('4277', '隆昌县', '393', '1', '0');
INSERT INTO `area` VALUES ('4278', '五通桥区', '394', '1', '0');
INSERT INTO `area` VALUES ('4279', '井研县', '394', '1', '0');
INSERT INTO `area` VALUES ('4280', '夹江县', '394', '1', '0');
INSERT INTO `area` VALUES ('4281', '峨眉山市', '394', '1', '0');
INSERT INTO `area` VALUES ('4282', '峨边彝族自治县', '394', '1', '0');
INSERT INTO `area` VALUES ('4283', '市中区', '394', '1', '0');
INSERT INTO `area` VALUES ('4284', '沐川县', '394', '1', '0');
INSERT INTO `area` VALUES ('4285', '沙湾区', '394', '1', '0');
INSERT INTO `area` VALUES ('4286', '犍为县', '394', '1', '0');
INSERT INTO `area` VALUES ('4287', '金口河区', '394', '1', '0');
INSERT INTO `area` VALUES ('4288', '马边彝族自治县', '394', '1', '0');
INSERT INTO `area` VALUES ('4289', '仪陇县', '395', '1', '0');
INSERT INTO `area` VALUES ('4290', '南充市嘉陵区', '395', '1', '0');
INSERT INTO `area` VALUES ('4291', '南部县', '395', '1', '0');
INSERT INTO `area` VALUES ('4292', '嘉陵区', '395', '1', '0');
INSERT INTO `area` VALUES ('4293', '营山县', '395', '1', '0');
INSERT INTO `area` VALUES ('4294', '蓬安县', '395', '1', '0');
INSERT INTO `area` VALUES ('4295', '西充县', '395', '1', '0');
INSERT INTO `area` VALUES ('4296', '阆中市', '395', '1', '0');
INSERT INTO `area` VALUES ('4297', '顺庆区', '395', '1', '0');
INSERT INTO `area` VALUES ('4298', '高坪区', '395', '1', '0');
INSERT INTO `area` VALUES ('4299', '东坡区', '396', '1', '0');
INSERT INTO `area` VALUES ('4300', '丹棱县', '396', '1', '0');
INSERT INTO `area` VALUES ('4301', '仁寿县', '396', '1', '0');
INSERT INTO `area` VALUES ('4302', '彭山县', '396', '1', '0');
INSERT INTO `area` VALUES ('4303', '洪雅县', '396', '1', '0');
INSERT INTO `area` VALUES ('4304', '青神县', '396', '1', '0');
INSERT INTO `area` VALUES ('4305', '兴文县', '397', '1', '0');
INSERT INTO `area` VALUES ('4306', '南溪县', '397', '1', '0');
INSERT INTO `area` VALUES ('4307', '宜宾县', '397', '1', '0');
INSERT INTO `area` VALUES ('4308', '屏山县', '397', '1', '0');
INSERT INTO `area` VALUES ('4309', '江安县', '397', '1', '0');
INSERT INTO `area` VALUES ('4310', '珙县', '397', '1', '0');
INSERT INTO `area` VALUES ('4311', '筠连县', '397', '1', '0');
INSERT INTO `area` VALUES ('4312', '翠屏区', '397', '1', '0');
INSERT INTO `area` VALUES ('4313', '长宁县', '397', '1', '0');
INSERT INTO `area` VALUES ('4314', '高县', '397', '1', '0');
INSERT INTO `area` VALUES ('4315', '华蓥市', '398', '1', '0');
INSERT INTO `area` VALUES ('4316', '岳池县', '398', '1', '0');
INSERT INTO `area` VALUES ('4317', '广安区', '398', '1', '0');
INSERT INTO `area` VALUES ('4318', '武胜县', '398', '1', '0');
INSERT INTO `area` VALUES ('4319', '邻水县', '398', '1', '0');
INSERT INTO `area` VALUES ('4320', '万源市', '399', '1', '0');
INSERT INTO `area` VALUES ('4321', '大竹县', '399', '1', '0');
INSERT INTO `area` VALUES ('4322', '宣汉县', '399', '1', '0');
INSERT INTO `area` VALUES ('4323', '开江县', '399', '1', '0');
INSERT INTO `area` VALUES ('4324', '渠县', '399', '1', '0');
INSERT INTO `area` VALUES ('4325', '达县', '399', '1', '0');
INSERT INTO `area` VALUES ('4326', '通川区', '399', '1', '0');
INSERT INTO `area` VALUES ('4327', '名山县', '400', '1', '0');
INSERT INTO `area` VALUES ('4328', '天全县', '400', '1', '0');
INSERT INTO `area` VALUES ('4329', '宝兴县', '400', '1', '0');
INSERT INTO `area` VALUES ('4330', '汉源县', '400', '1', '0');
INSERT INTO `area` VALUES ('4331', '石棉县', '400', '1', '0');
INSERT INTO `area` VALUES ('4332', '芦山县', '400', '1', '0');
INSERT INTO `area` VALUES ('4333', '荥经县', '400', '1', '0');
INSERT INTO `area` VALUES ('4334', '雨城区', '400', '1', '0');
INSERT INTO `area` VALUES ('4335', '南江县', '401', '1', '0');
INSERT INTO `area` VALUES ('4336', '巴州区', '401', '1', '0');
INSERT INTO `area` VALUES ('4337', '平昌县', '401', '1', '0');
INSERT INTO `area` VALUES ('4338', '通江县', '401', '1', '0');
INSERT INTO `area` VALUES ('4339', '乐至县', '402', '1', '0');
INSERT INTO `area` VALUES ('4340', '安岳县', '402', '1', '0');
INSERT INTO `area` VALUES ('4341', '简阳市', '402', '1', '0');
INSERT INTO `area` VALUES ('4342', '雁江区', '402', '1', '0');
INSERT INTO `area` VALUES ('4343', '九寨沟县', '403', '1', '0');
INSERT INTO `area` VALUES ('4344', '壤塘县', '403', '1', '0');
INSERT INTO `area` VALUES ('4345', '小金县', '403', '1', '0');
INSERT INTO `area` VALUES ('4346', '松潘县', '403', '1', '0');
INSERT INTO `area` VALUES ('4347', '汶川县', '403', '1', '0');
INSERT INTO `area` VALUES ('4348', '理县', '403', '1', '0');
INSERT INTO `area` VALUES ('4349', '红原县', '403', '1', '0');
INSERT INTO `area` VALUES ('4350', '若尔盖县', '403', '1', '0');
INSERT INTO `area` VALUES ('4351', '茂县', '403', '1', '0');
INSERT INTO `area` VALUES ('4352', '金川县', '403', '1', '0');
INSERT INTO `area` VALUES ('4353', '阿坝县', '403', '1', '0');
INSERT INTO `area` VALUES ('4354', '马尔康县', '403', '1', '0');
INSERT INTO `area` VALUES ('4355', '黑水县', '403', '1', '0');
INSERT INTO `area` VALUES ('4356', '丹巴县', '404', '1', '0');
INSERT INTO `area` VALUES ('4357', '乡城县', '404', '1', '0');
INSERT INTO `area` VALUES ('4358', '巴塘县', '404', '1', '0');
INSERT INTO `area` VALUES ('4359', '康定县', '404', '1', '0');
INSERT INTO `area` VALUES ('4360', '得荣县', '404', '1', '0');
INSERT INTO `area` VALUES ('4361', '德格县', '404', '1', '0');
INSERT INTO `area` VALUES ('4362', '新龙县', '404', '1', '0');
INSERT INTO `area` VALUES ('4363', '泸定县', '404', '1', '0');
INSERT INTO `area` VALUES ('4364', '炉霍县', '404', '1', '0');
INSERT INTO `area` VALUES ('4365', '理塘县', '404', '1', '0');
INSERT INTO `area` VALUES ('4366', '甘孜县', '404', '1', '0');
INSERT INTO `area` VALUES ('4367', '白玉县', '404', '1', '0');
INSERT INTO `area` VALUES ('4368', '石渠县', '404', '1', '0');
INSERT INTO `area` VALUES ('4369', '稻城县', '404', '1', '0');
INSERT INTO `area` VALUES ('4370', '色达县', '404', '1', '0');
INSERT INTO `area` VALUES ('4371', '道孚县', '404', '1', '0');
INSERT INTO `area` VALUES ('4372', '雅江县', '404', '1', '0');
INSERT INTO `area` VALUES ('4373', '会东县', '405', '1', '0');
INSERT INTO `area` VALUES ('4374', '会理县', '405', '1', '0');
INSERT INTO `area` VALUES ('4375', '冕宁县', '405', '1', '0');
INSERT INTO `area` VALUES ('4376', '喜德县', '405', '1', '0');
INSERT INTO `area` VALUES ('4377', '宁南县', '405', '1', '0');
INSERT INTO `area` VALUES ('4378', '布拖县', '405', '1', '0');
INSERT INTO `area` VALUES ('4379', '德昌县', '405', '1', '0');
INSERT INTO `area` VALUES ('4380', '昭觉县', '405', '1', '0');
INSERT INTO `area` VALUES ('4381', '普格县', '405', '1', '0');
INSERT INTO `area` VALUES ('4382', '木里藏族自治县', '405', '1', '0');
INSERT INTO `area` VALUES ('4383', '甘洛县', '405', '1', '0');
INSERT INTO `area` VALUES ('4384', '盐源县', '405', '1', '0');
INSERT INTO `area` VALUES ('4385', '美姑县', '405', '1', '0');
INSERT INTO `area` VALUES ('4386', '西昌', '405', '1', '0');
INSERT INTO `area` VALUES ('4387', '越西县', '405', '1', '0');
INSERT INTO `area` VALUES ('4388', '金阳县', '405', '1', '0');
INSERT INTO `area` VALUES ('4389', '雷波县', '405', '1', '0');
INSERT INTO `area` VALUES ('4390', '乌当区', '406', '1', '0');
INSERT INTO `area` VALUES ('4391', '云岩区', '406', '1', '0');
INSERT INTO `area` VALUES ('4392', '修文县', '406', '1', '0');
INSERT INTO `area` VALUES ('4393', '南明区', '406', '1', '0');
INSERT INTO `area` VALUES ('4394', '小河区', '406', '1', '0');
INSERT INTO `area` VALUES ('4395', '开阳县', '406', '1', '0');
INSERT INTO `area` VALUES ('4396', '息烽县', '406', '1', '0');
INSERT INTO `area` VALUES ('4397', '清镇市', '406', '1', '0');
INSERT INTO `area` VALUES ('4398', '白云区', '406', '1', '0');
INSERT INTO `area` VALUES ('4399', '花溪区', '406', '1', '0');
INSERT INTO `area` VALUES ('4400', '六枝特区', '407', '1', '0');
INSERT INTO `area` VALUES ('4401', '水城县', '407', '1', '0');
INSERT INTO `area` VALUES ('4402', '盘县', '407', '1', '0');
INSERT INTO `area` VALUES ('4403', '钟山区', '407', '1', '0');
INSERT INTO `area` VALUES ('4404', '习水县', '408', '1', '0');
INSERT INTO `area` VALUES ('4405', '仁怀市', '408', '1', '0');
INSERT INTO `area` VALUES ('4406', '余庆县', '408', '1', '0');
INSERT INTO `area` VALUES ('4407', '凤冈县', '408', '1', '0');
INSERT INTO `area` VALUES ('4408', '务川仡佬族苗族自治县', '408', '1', '0');
INSERT INTO `area` VALUES ('4409', '桐梓县', '408', '1', '0');
INSERT INTO `area` VALUES ('4410', '正安县', '408', '1', '0');
INSERT INTO `area` VALUES ('4411', '汇川区', '408', '1', '0');
INSERT INTO `area` VALUES ('4412', '湄潭县', '408', '1', '0');
INSERT INTO `area` VALUES ('4413', '红花岗区', '408', '1', '0');
INSERT INTO `area` VALUES ('4414', '绥阳县', '408', '1', '0');
INSERT INTO `area` VALUES ('4415', '赤水市', '408', '1', '0');
INSERT INTO `area` VALUES ('4416', '道真仡佬族苗族自治县', '408', '1', '0');
INSERT INTO `area` VALUES ('4417', '遵义县', '408', '1', '0');
INSERT INTO `area` VALUES ('4418', '关岭布依族苗族自治县', '409', '1', '0');
INSERT INTO `area` VALUES ('4419', '平坝县', '409', '1', '0');
INSERT INTO `area` VALUES ('4420', '普定县', '409', '1', '0');
INSERT INTO `area` VALUES ('4421', '紫云苗族布依族自治县', '409', '1', '0');
INSERT INTO `area` VALUES ('4422', '西秀区', '409', '1', '0');
INSERT INTO `area` VALUES ('4423', '镇宁布依族苗族自治县', '409', '1', '0');
INSERT INTO `area` VALUES ('4424', '万山特区', '410', '1', '0');
INSERT INTO `area` VALUES ('4425', '印江土家族苗族自治县', '410', '1', '0');
INSERT INTO `area` VALUES ('4426', '德江县', '410', '1', '0');
INSERT INTO `area` VALUES ('4427', '思南县', '410', '1', '0');
INSERT INTO `area` VALUES ('4428', '松桃苗族自治县', '410', '1', '0');
INSERT INTO `area` VALUES ('4429', '江口县', '410', '1', '0');
INSERT INTO `area` VALUES ('4430', '沿河土家族自治县', '410', '1', '0');
INSERT INTO `area` VALUES ('4431', '玉屏侗族自治县', '410', '1', '0');
INSERT INTO `area` VALUES ('4432', '石阡县', '410', '1', '0');
INSERT INTO `area` VALUES ('4433', '铜仁市', '410', '1', '0');
INSERT INTO `area` VALUES ('4434', '兴义市', '411', '1', '0');
INSERT INTO `area` VALUES ('4435', '兴仁县', '411', '1', '0');
INSERT INTO `area` VALUES ('4436', '册亨县', '411', '1', '0');
INSERT INTO `area` VALUES ('4437', '安龙县', '411', '1', '0');
INSERT INTO `area` VALUES ('4438', '普安县', '411', '1', '0');
INSERT INTO `area` VALUES ('4439', '晴隆县', '411', '1', '0');
INSERT INTO `area` VALUES ('4440', '望谟县', '411', '1', '0');
INSERT INTO `area` VALUES ('4441', '贞丰县', '411', '1', '0');
INSERT INTO `area` VALUES ('4442', '大方县', '412', '1', '0');
INSERT INTO `area` VALUES ('4443', '威宁彝族回族苗族自治县', '412', '1', '0');
INSERT INTO `area` VALUES ('4444', '毕节市', '412', '1', '0');
INSERT INTO `area` VALUES ('4445', '纳雍县', '412', '1', '0');
INSERT INTO `area` VALUES ('4446', '织金县', '412', '1', '0');
INSERT INTO `area` VALUES ('4447', '赫章县', '412', '1', '0');
INSERT INTO `area` VALUES ('4448', '金沙县', '412', '1', '0');
INSERT INTO `area` VALUES ('4449', '黔西县', '412', '1', '0');
INSERT INTO `area` VALUES ('4450', '三穗县', '413', '1', '0');
INSERT INTO `area` VALUES ('4451', '丹寨县', '413', '1', '0');
INSERT INTO `area` VALUES ('4452', '从江县', '413', '1', '0');
INSERT INTO `area` VALUES ('4453', '凯里市', '413', '1', '0');
INSERT INTO `area` VALUES ('4454', '剑河县', '413', '1', '0');
INSERT INTO `area` VALUES ('4455', '台江县', '413', '1', '0');
INSERT INTO `area` VALUES ('4456', '天柱县', '413', '1', '0');
INSERT INTO `area` VALUES ('4457', '岑巩县', '413', '1', '0');
INSERT INTO `area` VALUES ('4458', '施秉县', '413', '1', '0');
INSERT INTO `area` VALUES ('4459', '榕江县', '413', '1', '0');
INSERT INTO `area` VALUES ('4460', '锦屏县', '413', '1', '0');
INSERT INTO `area` VALUES ('4461', '镇远县', '413', '1', '0');
INSERT INTO `area` VALUES ('4462', '雷山县', '413', '1', '0');
INSERT INTO `area` VALUES ('4463', '麻江县', '413', '1', '0');
INSERT INTO `area` VALUES ('4464', '黄平县', '413', '1', '0');
INSERT INTO `area` VALUES ('4465', '黎平县', '413', '1', '0');
INSERT INTO `area` VALUES ('4466', '三都水族自治县', '414', '1', '0');
INSERT INTO `area` VALUES ('4467', '平塘县', '414', '1', '0');
INSERT INTO `area` VALUES ('4468', '惠水县', '414', '1', '0');
INSERT INTO `area` VALUES ('4469', '独山县', '414', '1', '0');
INSERT INTO `area` VALUES ('4470', '瓮安县', '414', '1', '0');
INSERT INTO `area` VALUES ('4471', '福泉市', '414', '1', '0');
INSERT INTO `area` VALUES ('4472', '罗甸县', '414', '1', '0');
INSERT INTO `area` VALUES ('4473', '荔波县', '414', '1', '0');
INSERT INTO `area` VALUES ('4474', '贵定县', '414', '1', '0');
INSERT INTO `area` VALUES ('4475', '都匀市', '414', '1', '0');
INSERT INTO `area` VALUES ('4476', '长顺县', '414', '1', '0');
INSERT INTO `area` VALUES ('4477', '龙里县', '414', '1', '0');
INSERT INTO `area` VALUES ('4478', '东川区', '415', '1', '0');
INSERT INTO `area` VALUES ('4479', '五华区', '415', '1', '0');
INSERT INTO `area` VALUES ('4480', '呈贡县', '415', '1', '0');
INSERT INTO `area` VALUES ('4481', '安宁市', '415', '1', '0');
INSERT INTO `area` VALUES ('4482', '官渡区', '415', '1', '0');
INSERT INTO `area` VALUES ('4483', '宜良县', '415', '1', '0');
INSERT INTO `area` VALUES ('4484', '富民县', '415', '1', '0');
INSERT INTO `area` VALUES ('4485', '寻甸回族彝族自治县', '415', '1', '0');
INSERT INTO `area` VALUES ('4486', '嵩明县', '415', '1', '0');
INSERT INTO `area` VALUES ('4487', '晋宁县', '415', '1', '0');
INSERT INTO `area` VALUES ('4488', '盘龙区', '415', '1', '0');
INSERT INTO `area` VALUES ('4489', '石林彝族自治县', '415', '1', '0');
INSERT INTO `area` VALUES ('4490', '禄劝彝族苗族自治县', '415', '1', '0');
INSERT INTO `area` VALUES ('4491', '西山区', '415', '1', '0');
INSERT INTO `area` VALUES ('4492', '会泽县', '416', '1', '0');
INSERT INTO `area` VALUES ('4493', '宣威市', '416', '1', '0');
INSERT INTO `area` VALUES ('4494', '富源县', '416', '1', '0');
INSERT INTO `area` VALUES ('4495', '师宗县', '416', '1', '0');
INSERT INTO `area` VALUES ('4496', '沾益县', '416', '1', '0');
INSERT INTO `area` VALUES ('4497', '罗平县', '416', '1', '0');
INSERT INTO `area` VALUES ('4498', '陆良县', '416', '1', '0');
INSERT INTO `area` VALUES ('4499', '马龙县', '416', '1', '0');
INSERT INTO `area` VALUES ('4500', '麒麟区', '416', '1', '0');
INSERT INTO `area` VALUES ('4501', '元江哈尼族彝族傣族自治县', '417', '1', '0');
INSERT INTO `area` VALUES ('4502', '华宁县', '417', '1', '0');
INSERT INTO `area` VALUES ('4503', '峨山彝族自治县', '417', '1', '0');
INSERT INTO `area` VALUES ('4504', '新平彝族傣族自治县', '417', '1', '0');
INSERT INTO `area` VALUES ('4505', '易门县', '417', '1', '0');
INSERT INTO `area` VALUES ('4506', '江川县', '417', '1', '0');
INSERT INTO `area` VALUES ('4507', '澄江县', '417', '1', '0');
INSERT INTO `area` VALUES ('4508', '红塔区', '417', '1', '0');
INSERT INTO `area` VALUES ('4509', '通海县', '417', '1', '0');
INSERT INTO `area` VALUES ('4510', '施甸县', '418', '1', '0');
INSERT INTO `area` VALUES ('4511', '昌宁县', '418', '1', '0');
INSERT INTO `area` VALUES ('4512', '腾冲县', '418', '1', '0');
INSERT INTO `area` VALUES ('4513', '隆阳区', '418', '1', '0');
INSERT INTO `area` VALUES ('4514', '龙陵县', '418', '1', '0');
INSERT INTO `area` VALUES ('4515', '大关县', '419', '1', '0');
INSERT INTO `area` VALUES ('4516', '威信县', '419', '1', '0');
INSERT INTO `area` VALUES ('4517', '巧家县', '419', '1', '0');
INSERT INTO `area` VALUES ('4518', '彝良县', '419', '1', '0');
INSERT INTO `area` VALUES ('4519', '昭阳区', '419', '1', '0');
INSERT INTO `area` VALUES ('4520', '水富县', '419', '1', '0');
INSERT INTO `area` VALUES ('4521', '永善县', '419', '1', '0');
INSERT INTO `area` VALUES ('4522', '盐津县', '419', '1', '0');
INSERT INTO `area` VALUES ('4523', '绥江县', '419', '1', '0');
INSERT INTO `area` VALUES ('4524', '镇雄县', '419', '1', '0');
INSERT INTO `area` VALUES ('4525', '鲁甸县', '419', '1', '0');
INSERT INTO `area` VALUES ('4526', '华坪县', '420', '1', '0');
INSERT INTO `area` VALUES ('4527', '古城区', '420', '1', '0');
INSERT INTO `area` VALUES ('4528', '宁蒗彝族自治县', '420', '1', '0');
INSERT INTO `area` VALUES ('4529', '永胜县', '420', '1', '0');
INSERT INTO `area` VALUES ('4530', '玉龙纳西族自治县', '420', '1', '0');
INSERT INTO `area` VALUES ('4531', '临翔区', '422', '1', '0');
INSERT INTO `area` VALUES ('4532', '云县', '422', '1', '0');
INSERT INTO `area` VALUES ('4533', '凤庆县', '422', '1', '0');
INSERT INTO `area` VALUES ('4534', '双江拉祜族佤族布朗族傣族自治县', '422', '1', '0');
INSERT INTO `area` VALUES ('4535', '永德县', '422', '1', '0');
INSERT INTO `area` VALUES ('4536', '沧源佤族自治县', '422', '1', '0');
INSERT INTO `area` VALUES ('4537', '耿马傣族佤族自治县', '422', '1', '0');
INSERT INTO `area` VALUES ('4538', '镇康县', '422', '1', '0');
INSERT INTO `area` VALUES ('4539', '元谋县', '423', '1', '0');
INSERT INTO `area` VALUES ('4540', '南华县', '423', '1', '0');
INSERT INTO `area` VALUES ('4541', '双柏县', '423', '1', '0');
INSERT INTO `area` VALUES ('4542', '大姚县', '423', '1', '0');
INSERT INTO `area` VALUES ('4543', '姚安县', '423', '1', '0');
INSERT INTO `area` VALUES ('4544', '楚雄市', '423', '1', '0');
INSERT INTO `area` VALUES ('4545', '武定县', '423', '1', '0');
INSERT INTO `area` VALUES ('4546', '永仁县', '423', '1', '0');
INSERT INTO `area` VALUES ('4547', '牟定县', '423', '1', '0');
INSERT INTO `area` VALUES ('4548', '禄丰县', '423', '1', '0');
INSERT INTO `area` VALUES ('4549', '个旧市', '424', '1', '0');
INSERT INTO `area` VALUES ('4550', '元阳县', '424', '1', '0');
INSERT INTO `area` VALUES ('4551', '屏边苗族自治县', '424', '1', '0');
INSERT INTO `area` VALUES ('4552', '建水县', '424', '1', '0');
INSERT INTO `area` VALUES ('4553', '开远市', '424', '1', '0');
INSERT INTO `area` VALUES ('4554', '弥勒县', '424', '1', '0');
INSERT INTO `area` VALUES ('4555', '河口瑶族自治县', '424', '1', '0');
INSERT INTO `area` VALUES ('4556', '泸西县', '424', '1', '0');
INSERT INTO `area` VALUES ('4557', '石屏县', '424', '1', '0');
INSERT INTO `area` VALUES ('4558', '红河县', '424', '1', '0');
INSERT INTO `area` VALUES ('4559', '绿春县', '424', '1', '0');
INSERT INTO `area` VALUES ('4560', '蒙自县', '424', '1', '0');
INSERT INTO `area` VALUES ('4561', '金平苗族瑶族傣族自治县', '424', '1', '0');
INSERT INTO `area` VALUES ('4562', '丘北县', '425', '1', '0');
INSERT INTO `area` VALUES ('4563', '富宁县', '425', '1', '0');
INSERT INTO `area` VALUES ('4564', '广南县', '425', '1', '0');
INSERT INTO `area` VALUES ('4565', '文山县', '425', '1', '0');
INSERT INTO `area` VALUES ('4566', '砚山县', '425', '1', '0');
INSERT INTO `area` VALUES ('4567', '西畴县', '425', '1', '0');
INSERT INTO `area` VALUES ('4568', '马关县', '425', '1', '0');
INSERT INTO `area` VALUES ('4569', '麻栗坡县', '425', '1', '0');
INSERT INTO `area` VALUES ('4570', '勐海县', '426', '1', '0');
INSERT INTO `area` VALUES ('4571', '勐腊县', '426', '1', '0');
INSERT INTO `area` VALUES ('4572', '景洪市', '426', '1', '0');
INSERT INTO `area` VALUES ('4573', '云龙县', '427', '1', '0');
INSERT INTO `area` VALUES ('4574', '剑川县', '427', '1', '0');
INSERT INTO `area` VALUES ('4575', '南涧彝族自治县', '427', '1', '0');
INSERT INTO `area` VALUES ('4576', '大理市', '427', '1', '0');
INSERT INTO `area` VALUES ('4577', '宾川县', '427', '1', '0');
INSERT INTO `area` VALUES ('4578', '巍山彝族回族自治县', '427', '1', '0');
INSERT INTO `area` VALUES ('4579', '弥渡县', '427', '1', '0');
INSERT INTO `area` VALUES ('4580', '永平县', '427', '1', '0');
INSERT INTO `area` VALUES ('4581', '洱源县', '427', '1', '0');
INSERT INTO `area` VALUES ('4582', '漾濞彝族自治县', '427', '1', '0');
INSERT INTO `area` VALUES ('4583', '祥云县', '427', '1', '0');
INSERT INTO `area` VALUES ('4584', '鹤庆县', '427', '1', '0');
INSERT INTO `area` VALUES ('4585', '梁河县', '428', '1', '0');
INSERT INTO `area` VALUES ('4586', '潞西市', '428', '1', '0');
INSERT INTO `area` VALUES ('4587', '瑞丽市', '428', '1', '0');
INSERT INTO `area` VALUES ('4588', '盈江县', '428', '1', '0');
INSERT INTO `area` VALUES ('4589', '陇川县', '428', '1', '0');
INSERT INTO `area` VALUES ('4590', '德钦县', '430', '1', '0');
INSERT INTO `area` VALUES ('4591', '维西傈僳族自治县', '430', '1', '0');
INSERT INTO `area` VALUES ('4592', '香格里拉县', '430', '1', '0');
INSERT INTO `area` VALUES ('4593', '城关区', '431', '1', '0');
INSERT INTO `area` VALUES ('4594', '堆龙德庆县', '431', '1', '0');
INSERT INTO `area` VALUES ('4595', '墨竹工卡县', '431', '1', '0');
INSERT INTO `area` VALUES ('4596', '尼木县', '431', '1', '0');
INSERT INTO `area` VALUES ('4597', '当雄县', '431', '1', '0');
INSERT INTO `area` VALUES ('4598', '曲水县', '431', '1', '0');
INSERT INTO `area` VALUES ('4599', '林周县', '431', '1', '0');
INSERT INTO `area` VALUES ('4600', '达孜县', '431', '1', '0');
INSERT INTO `area` VALUES ('4601', '丁青县', '432', '1', '0');
INSERT INTO `area` VALUES ('4602', '八宿县', '432', '1', '0');
INSERT INTO `area` VALUES ('4603', '察雅县', '432', '1', '0');
INSERT INTO `area` VALUES ('4604', '左贡县', '432', '1', '0');
INSERT INTO `area` VALUES ('4605', '昌都县', '432', '1', '0');
INSERT INTO `area` VALUES ('4606', '江达县', '432', '1', '0');
INSERT INTO `area` VALUES ('4607', '洛隆县', '432', '1', '0');
INSERT INTO `area` VALUES ('4608', '类乌齐县', '432', '1', '0');
INSERT INTO `area` VALUES ('4609', '芒康县', '432', '1', '0');
INSERT INTO `area` VALUES ('4610', '贡觉县', '432', '1', '0');
INSERT INTO `area` VALUES ('4611', '边坝县', '432', '1', '0');
INSERT INTO `area` VALUES ('4612', '乃东县', '433', '1', '0');
INSERT INTO `area` VALUES ('4613', '加查县', '433', '1', '0');
INSERT INTO `area` VALUES ('4614', '扎囊县', '433', '1', '0');
INSERT INTO `area` VALUES ('4615', '措美县', '433', '1', '0');
INSERT INTO `area` VALUES ('4616', '曲松县', '433', '1', '0');
INSERT INTO `area` VALUES ('4617', '桑日县', '433', '1', '0');
INSERT INTO `area` VALUES ('4618', '洛扎县', '433', '1', '0');
INSERT INTO `area` VALUES ('4619', '浪卡子县', '433', '1', '0');
INSERT INTO `area` VALUES ('4620', '琼结县', '433', '1', '0');
INSERT INTO `area` VALUES ('4621', '贡嘎县', '433', '1', '0');
INSERT INTO `area` VALUES ('4622', '错那县', '433', '1', '0');
INSERT INTO `area` VALUES ('4623', '隆子县', '433', '1', '0');
INSERT INTO `area` VALUES ('4624', '亚东县', '434', '1', '0');
INSERT INTO `area` VALUES ('4625', '仁布县', '434', '1', '0');
INSERT INTO `area` VALUES ('4626', '仲巴县', '434', '1', '0');
INSERT INTO `area` VALUES ('4627', '南木林县', '434', '1', '0');
INSERT INTO `area` VALUES ('4628', '吉隆县', '434', '1', '0');
INSERT INTO `area` VALUES ('4629', '定日县', '434', '1', '0');
INSERT INTO `area` VALUES ('4630', '定结县', '434', '1', '0');
INSERT INTO `area` VALUES ('4631', '岗巴县', '434', '1', '0');
INSERT INTO `area` VALUES ('4632', '康马县', '434', '1', '0');
INSERT INTO `area` VALUES ('4633', '拉孜县', '434', '1', '0');
INSERT INTO `area` VALUES ('4634', '日喀则市', '434', '1', '0');
INSERT INTO `area` VALUES ('4635', '昂仁县', '434', '1', '0');
INSERT INTO `area` VALUES ('4636', '江孜县', '434', '1', '0');
INSERT INTO `area` VALUES ('4637', '白朗县', '434', '1', '0');
INSERT INTO `area` VALUES ('4638', '聂拉木县', '434', '1', '0');
INSERT INTO `area` VALUES ('4639', '萨嘎县', '434', '1', '0');
INSERT INTO `area` VALUES ('4640', '萨迦县', '434', '1', '0');
INSERT INTO `area` VALUES ('4641', '谢通门县', '434', '1', '0');
INSERT INTO `area` VALUES ('4642', '嘉黎县', '435', '1', '0');
INSERT INTO `area` VALUES ('4643', '安多县', '435', '1', '0');
INSERT INTO `area` VALUES ('4644', '尼玛县', '435', '1', '0');
INSERT INTO `area` VALUES ('4645', '巴青县', '435', '1', '0');
INSERT INTO `area` VALUES ('4646', '比如县', '435', '1', '0');
INSERT INTO `area` VALUES ('4647', '班戈县', '435', '1', '0');
INSERT INTO `area` VALUES ('4648', '申扎县', '435', '1', '0');
INSERT INTO `area` VALUES ('4649', '索县', '435', '1', '0');
INSERT INTO `area` VALUES ('4650', '聂荣县', '435', '1', '0');
INSERT INTO `area` VALUES ('4651', '那曲县', '435', '1', '0');
INSERT INTO `area` VALUES ('4652', '噶尔县', '436', '1', '0');
INSERT INTO `area` VALUES ('4653', '措勤县', '436', '1', '0');
INSERT INTO `area` VALUES ('4654', '改则县', '436', '1', '0');
INSERT INTO `area` VALUES ('4655', '日土县', '436', '1', '0');
INSERT INTO `area` VALUES ('4656', '普兰县', '436', '1', '0');
INSERT INTO `area` VALUES ('4657', '札达县', '436', '1', '0');
INSERT INTO `area` VALUES ('4658', '革吉县', '436', '1', '0');
INSERT INTO `area` VALUES ('4659', '墨脱县', '437', '1', '0');
INSERT INTO `area` VALUES ('4660', '察隅县', '437', '1', '0');
INSERT INTO `area` VALUES ('4661', '工布江达县', '437', '1', '0');
INSERT INTO `area` VALUES ('4662', '朗县', '437', '1', '0');
INSERT INTO `area` VALUES ('4663', '林芝县', '437', '1', '0');
INSERT INTO `area` VALUES ('4664', '波密县', '437', '1', '0');
INSERT INTO `area` VALUES ('4665', '米林县', '437', '1', '0');
INSERT INTO `area` VALUES ('4666', '临潼区', '438', '1', '0');
INSERT INTO `area` VALUES ('4667', '周至县', '438', '1', '0');
INSERT INTO `area` VALUES ('4668', '户县', '438', '1', '0');
INSERT INTO `area` VALUES ('4669', '新城区', '438', '1', '0');
INSERT INTO `area` VALUES ('4670', '未央区', '438', '1', '0');
INSERT INTO `area` VALUES ('4671', '灞桥区', '438', '1', '0');
INSERT INTO `area` VALUES ('4672', '碑林区', '438', '1', '0');
INSERT INTO `area` VALUES ('4673', '莲湖区', '438', '1', '0');
INSERT INTO `area` VALUES ('4674', '蓝田县', '438', '1', '0');
INSERT INTO `area` VALUES ('4675', '长安区', '438', '1', '0');
INSERT INTO `area` VALUES ('4676', '阎良区', '438', '1', '0');
INSERT INTO `area` VALUES ('4677', '雁塔区', '438', '1', '0');
INSERT INTO `area` VALUES ('4678', '高陵县', '438', '1', '0');
INSERT INTO `area` VALUES ('4679', '印台区', '439', '1', '0');
INSERT INTO `area` VALUES ('4680', '宜君县', '439', '1', '0');
INSERT INTO `area` VALUES ('4681', '王益区', '439', '1', '0');
INSERT INTO `area` VALUES ('4682', '耀州区', '439', '1', '0');
INSERT INTO `area` VALUES ('4683', '凤县', '440', '1', '0');
INSERT INTO `area` VALUES ('4684', '凤翔县', '440', '1', '0');
INSERT INTO `area` VALUES ('4685', '千阳县', '440', '1', '0');
INSERT INTO `area` VALUES ('4686', '太白县', '440', '1', '0');
INSERT INTO `area` VALUES ('4687', '岐山县', '440', '1', '0');
INSERT INTO `area` VALUES ('4688', '扶风县', '440', '1', '0');
INSERT INTO `area` VALUES ('4689', '渭滨区', '440', '1', '0');
INSERT INTO `area` VALUES ('4690', '眉县', '440', '1', '0');
INSERT INTO `area` VALUES ('4691', '金台区', '440', '1', '0');
INSERT INTO `area` VALUES ('4692', '陇县', '440', '1', '0');
INSERT INTO `area` VALUES ('4693', '陈仓区', '440', '1', '0');
INSERT INTO `area` VALUES ('4694', '麟游县', '440', '1', '0');
INSERT INTO `area` VALUES ('4695', '三原县', '441', '1', '0');
INSERT INTO `area` VALUES ('4696', '干县', '441', '1', '0');
INSERT INTO `area` VALUES ('4697', '兴平市', '441', '1', '0');
INSERT INTO `area` VALUES ('4698', '彬县', '441', '1', '0');
INSERT INTO `area` VALUES ('4699', '旬邑县', '441', '1', '0');
INSERT INTO `area` VALUES ('4700', '杨陵区', '441', '1', '0');
INSERT INTO `area` VALUES ('4701', '武功县', '441', '1', '0');
INSERT INTO `area` VALUES ('4702', '永寿县', '441', '1', '0');
INSERT INTO `area` VALUES ('4703', '泾阳县', '441', '1', '0');
INSERT INTO `area` VALUES ('4704', '淳化县', '441', '1', '0');
INSERT INTO `area` VALUES ('4705', '渭城区', '441', '1', '0');
INSERT INTO `area` VALUES ('4706', '礼泉县', '441', '1', '0');
INSERT INTO `area` VALUES ('4707', '秦都区', '441', '1', '0');
INSERT INTO `area` VALUES ('4708', '长武县', '441', '1', '0');
INSERT INTO `area` VALUES ('4709', '临渭区', '442', '1', '0');
INSERT INTO `area` VALUES ('4710', '华县', '442', '1', '0');
INSERT INTO `area` VALUES ('4711', '华阴市', '442', '1', '0');
INSERT INTO `area` VALUES ('4712', '合阳县', '442', '1', '0');
INSERT INTO `area` VALUES ('4713', '大荔县', '442', '1', '0');
INSERT INTO `area` VALUES ('4714', '富平县', '442', '1', '0');
INSERT INTO `area` VALUES ('4715', '潼关县', '442', '1', '0');
INSERT INTO `area` VALUES ('4716', '澄城县', '442', '1', '0');
INSERT INTO `area` VALUES ('4717', '白水县', '442', '1', '0');
INSERT INTO `area` VALUES ('4718', '蒲城县', '442', '1', '0');
INSERT INTO `area` VALUES ('4719', '韩城市', '442', '1', '0');
INSERT INTO `area` VALUES ('4720', '吴起县', '443', '1', '0');
INSERT INTO `area` VALUES ('4721', '子长县', '443', '1', '0');
INSERT INTO `area` VALUES ('4722', '安塞县', '443', '1', '0');
INSERT INTO `area` VALUES ('4723', '宜川县', '443', '1', '0');
INSERT INTO `area` VALUES ('4724', '宝塔区', '443', '1', '0');
INSERT INTO `area` VALUES ('4725', '富县', '443', '1', '0');
INSERT INTO `area` VALUES ('4726', '延川县', '443', '1', '0');
INSERT INTO `area` VALUES ('4727', '延长县', '443', '1', '0');
INSERT INTO `area` VALUES ('4728', '志丹县', '443', '1', '0');
INSERT INTO `area` VALUES ('4729', '洛川县', '443', '1', '0');
INSERT INTO `area` VALUES ('4730', '甘泉县', '443', '1', '0');
INSERT INTO `area` VALUES ('4731', '黄陵县', '443', '1', '0');
INSERT INTO `area` VALUES ('4732', '黄龙县', '443', '1', '0');
INSERT INTO `area` VALUES ('4733', '佛坪县', '444', '1', '0');
INSERT INTO `area` VALUES ('4734', '勉县', '444', '1', '0');
INSERT INTO `area` VALUES ('4735', '南郑县', '444', '1', '0');
INSERT INTO `area` VALUES ('4736', '城固县', '444', '1', '0');
INSERT INTO `area` VALUES ('4737', '宁强县', '444', '1', '0');
INSERT INTO `area` VALUES ('4738', '汉台区', '444', '1', '0');
INSERT INTO `area` VALUES ('4739', '洋县', '444', '1', '0');
INSERT INTO `area` VALUES ('4740', '留坝县', '444', '1', '0');
INSERT INTO `area` VALUES ('4741', '略阳县', '444', '1', '0');
INSERT INTO `area` VALUES ('4742', '西乡县', '444', '1', '0');
INSERT INTO `area` VALUES ('4743', '镇巴县', '444', '1', '0');
INSERT INTO `area` VALUES ('4744', '佳县', '445', '1', '0');
INSERT INTO `area` VALUES ('4745', '吴堡县', '445', '1', '0');
INSERT INTO `area` VALUES ('4746', '子洲县', '445', '1', '0');
INSERT INTO `area` VALUES ('4747', '定边县', '445', '1', '0');
INSERT INTO `area` VALUES ('4748', '府谷县', '445', '1', '0');
INSERT INTO `area` VALUES ('4749', '榆林市榆阳区', '445', '1', '0');
INSERT INTO `area` VALUES ('4750', '横山县', '445', '1', '0');
INSERT INTO `area` VALUES ('4751', '清涧县', '445', '1', '0');
INSERT INTO `area` VALUES ('4752', '神木县', '445', '1', '0');
INSERT INTO `area` VALUES ('4753', '米脂县', '445', '1', '0');
INSERT INTO `area` VALUES ('4754', '绥德县', '445', '1', '0');
INSERT INTO `area` VALUES ('4755', '靖边县', '445', '1', '0');
INSERT INTO `area` VALUES ('4756', '宁陕县', '446', '1', '0');
INSERT INTO `area` VALUES ('4757', '岚皋县', '446', '1', '0');
INSERT INTO `area` VALUES ('4758', '平利县', '446', '1', '0');
INSERT INTO `area` VALUES ('4759', '旬阳县', '446', '1', '0');
INSERT INTO `area` VALUES ('4760', '汉滨区', '446', '1', '0');
INSERT INTO `area` VALUES ('4761', '汉阴县', '446', '1', '0');
INSERT INTO `area` VALUES ('4762', '白河县', '446', '1', '0');
INSERT INTO `area` VALUES ('4763', '石泉县', '446', '1', '0');
INSERT INTO `area` VALUES ('4764', '紫阳县', '446', '1', '0');
INSERT INTO `area` VALUES ('4765', '镇坪县', '446', '1', '0');
INSERT INTO `area` VALUES ('4766', '丹凤县', '447', '1', '0');
INSERT INTO `area` VALUES ('4767', '商南县', '447', '1', '0');
INSERT INTO `area` VALUES ('4768', '商州区', '447', '1', '0');
INSERT INTO `area` VALUES ('4769', '山阳县', '447', '1', '0');
INSERT INTO `area` VALUES ('4770', '柞水县', '447', '1', '0');
INSERT INTO `area` VALUES ('4771', '洛南县', '447', '1', '0');
INSERT INTO `area` VALUES ('4772', '镇安县', '447', '1', '0');
INSERT INTO `area` VALUES ('4773', '七里河区', '448', '1', '0');
INSERT INTO `area` VALUES ('4774', '城关区', '448', '1', '0');
INSERT INTO `area` VALUES ('4775', '安宁区', '448', '1', '0');
INSERT INTO `area` VALUES ('4776', '榆中县', '448', '1', '0');
INSERT INTO `area` VALUES ('4777', '永登县', '448', '1', '0');
INSERT INTO `area` VALUES ('4778', '皋兰县', '448', '1', '0');
INSERT INTO `area` VALUES ('4779', '红古区', '448', '1', '0');
INSERT INTO `area` VALUES ('4780', '西固区', '448', '1', '0');
INSERT INTO `area` VALUES ('4781', '嘉峪关市', '449', '1', '0');
INSERT INTO `area` VALUES ('4782', '永昌县', '450', '1', '0');
INSERT INTO `area` VALUES ('4783', '金川区', '450', '1', '0');
INSERT INTO `area` VALUES ('4784', '会宁县', '451', '1', '0');
INSERT INTO `area` VALUES ('4785', '平川区', '451', '1', '0');
INSERT INTO `area` VALUES ('4786', '景泰县', '451', '1', '0');
INSERT INTO `area` VALUES ('4787', '白银区', '451', '1', '0');
INSERT INTO `area` VALUES ('4788', '靖远县', '451', '1', '0');
INSERT INTO `area` VALUES ('4789', '张家川回族自治县', '452', '1', '0');
INSERT INTO `area` VALUES ('4790', '武山县', '452', '1', '0');
INSERT INTO `area` VALUES ('4791', '清水县', '452', '1', '0');
INSERT INTO `area` VALUES ('4792', '甘谷县', '452', '1', '0');
INSERT INTO `area` VALUES ('4793', '秦安县', '452', '1', '0');
INSERT INTO `area` VALUES ('4794', '秦州区', '452', '1', '0');
INSERT INTO `area` VALUES ('4795', '麦积区', '452', '1', '0');
INSERT INTO `area` VALUES ('4796', '凉州区', '453', '1', '0');
INSERT INTO `area` VALUES ('4797', '古浪县', '453', '1', '0');
INSERT INTO `area` VALUES ('4798', '天祝藏族自治县', '453', '1', '0');
INSERT INTO `area` VALUES ('4799', '民勤县', '453', '1', '0');
INSERT INTO `area` VALUES ('4800', '临泽县', '454', '1', '0');
INSERT INTO `area` VALUES ('4801', '山丹县', '454', '1', '0');
INSERT INTO `area` VALUES ('4802', '民乐县', '454', '1', '0');
INSERT INTO `area` VALUES ('4803', '甘州区', '454', '1', '0');
INSERT INTO `area` VALUES ('4804', '肃南裕固族自治县', '454', '1', '0');
INSERT INTO `area` VALUES ('4805', '高台县', '454', '1', '0');
INSERT INTO `area` VALUES ('4806', '华亭县', '455', '1', '0');
INSERT INTO `area` VALUES ('4807', '崆峒区', '455', '1', '0');
INSERT INTO `area` VALUES ('4808', '崇信县', '455', '1', '0');
INSERT INTO `area` VALUES ('4809', '庄浪县', '455', '1', '0');
INSERT INTO `area` VALUES ('4810', '泾川县', '455', '1', '0');
INSERT INTO `area` VALUES ('4811', '灵台县', '455', '1', '0');
INSERT INTO `area` VALUES ('4812', '静宁县', '455', '1', '0');
INSERT INTO `area` VALUES ('4813', '敦煌市', '456', '1', '0');
INSERT INTO `area` VALUES ('4814', '玉门市', '456', '1', '0');
INSERT INTO `area` VALUES ('4815', '瓜州县（原安西县）', '456', '1', '0');
INSERT INTO `area` VALUES ('4816', '肃北蒙古族自治县', '456', '1', '0');
INSERT INTO `area` VALUES ('4817', '肃州区', '456', '1', '0');
INSERT INTO `area` VALUES ('4818', '金塔县', '456', '1', '0');
INSERT INTO `area` VALUES ('4819', '阿克塞哈萨克族自治县', '456', '1', '0');
INSERT INTO `area` VALUES ('4820', '华池县', '457', '1', '0');
INSERT INTO `area` VALUES ('4821', '合水县', '457', '1', '0');
INSERT INTO `area` VALUES ('4822', '宁县', '457', '1', '0');
INSERT INTO `area` VALUES ('4823', '庆城县', '457', '1', '0');
INSERT INTO `area` VALUES ('4824', '正宁县', '457', '1', '0');
INSERT INTO `area` VALUES ('4825', '环县', '457', '1', '0');
INSERT INTO `area` VALUES ('4826', '西峰区', '457', '1', '0');
INSERT INTO `area` VALUES ('4827', '镇原县', '457', '1', '0');
INSERT INTO `area` VALUES ('4828', '临洮县', '458', '1', '0');
INSERT INTO `area` VALUES ('4829', '安定区', '458', '1', '0');
INSERT INTO `area` VALUES ('4830', '岷县', '458', '1', '0');
INSERT INTO `area` VALUES ('4831', '渭源县', '458', '1', '0');
INSERT INTO `area` VALUES ('4832', '漳县', '458', '1', '0');
INSERT INTO `area` VALUES ('4833', '通渭县', '458', '1', '0');
INSERT INTO `area` VALUES ('4834', '陇西县', '458', '1', '0');
INSERT INTO `area` VALUES ('4835', '两当县', '459', '1', '0');
INSERT INTO `area` VALUES ('4836', '宕昌县', '459', '1', '0');
INSERT INTO `area` VALUES ('4837', '康县', '459', '1', '0');
INSERT INTO `area` VALUES ('4838', '徽县', '459', '1', '0');
INSERT INTO `area` VALUES ('4839', '成县', '459', '1', '0');
INSERT INTO `area` VALUES ('4840', '文县', '459', '1', '0');
INSERT INTO `area` VALUES ('4841', '武都区', '459', '1', '0');
INSERT INTO `area` VALUES ('4842', '礼县', '459', '1', '0');
INSERT INTO `area` VALUES ('4843', '西和县', '459', '1', '0');
INSERT INTO `area` VALUES ('4844', '东乡族自治县', '460', '1', '0');
INSERT INTO `area` VALUES ('4845', '临夏县', '460', '1', '0');
INSERT INTO `area` VALUES ('4846', '临夏市', '460', '1', '0');
INSERT INTO `area` VALUES ('4847', '和政县', '460', '1', '0');
INSERT INTO `area` VALUES ('4848', '广河县', '460', '1', '0');
INSERT INTO `area` VALUES ('4849', '康乐县', '460', '1', '0');
INSERT INTO `area` VALUES ('4850', '永靖县', '460', '1', '0');
INSERT INTO `area` VALUES ('4851', '积石山保安族东乡族撒拉族自治县', '460', '1', '0');
INSERT INTO `area` VALUES ('4852', '临潭县', '461', '1', '0');
INSERT INTO `area` VALUES ('4853', '卓尼县', '461', '1', '0');
INSERT INTO `area` VALUES ('4854', '合作市', '461', '1', '0');
INSERT INTO `area` VALUES ('4855', '夏河县', '461', '1', '0');
INSERT INTO `area` VALUES ('4856', '玛曲县', '461', '1', '0');
INSERT INTO `area` VALUES ('4857', '碌曲县', '461', '1', '0');
INSERT INTO `area` VALUES ('4858', '舟曲县', '461', '1', '0');
INSERT INTO `area` VALUES ('4859', '迭部县', '461', '1', '0');
INSERT INTO `area` VALUES ('4860', '城东区', '462', '1', '0');
INSERT INTO `area` VALUES ('4861', '城中区', '462', '1', '0');
INSERT INTO `area` VALUES ('4862', '城北区', '462', '1', '0');
INSERT INTO `area` VALUES ('4863', '城西区', '462', '1', '0');
INSERT INTO `area` VALUES ('4864', '大通回族土族自治县', '462', '1', '0');
INSERT INTO `area` VALUES ('4865', '湟中县', '462', '1', '0');
INSERT INTO `area` VALUES ('4866', '湟源县', '462', '1', '0');
INSERT INTO `area` VALUES ('4867', '乐都县', '463', '1', '0');
INSERT INTO `area` VALUES ('4868', '互助土族自治县', '463', '1', '0');
INSERT INTO `area` VALUES ('4869', '化隆回族自治县', '463', '1', '0');
INSERT INTO `area` VALUES ('4870', '平安县', '463', '1', '0');
INSERT INTO `area` VALUES ('4871', '循化撒拉族自治县', '463', '1', '0');
INSERT INTO `area` VALUES ('4872', '民和回族土族自治县', '463', '1', '0');
INSERT INTO `area` VALUES ('4873', '刚察县', '464', '1', '0');
INSERT INTO `area` VALUES ('4874', '海晏县', '464', '1', '0');
INSERT INTO `area` VALUES ('4875', '祁连县', '464', '1', '0');
INSERT INTO `area` VALUES ('4876', '门源回族自治县', '464', '1', '0');
INSERT INTO `area` VALUES ('4877', '同仁县', '465', '1', '0');
INSERT INTO `area` VALUES ('4878', '尖扎县', '465', '1', '0');
INSERT INTO `area` VALUES ('4879', '河南蒙古族自治县', '465', '1', '0');
INSERT INTO `area` VALUES ('4880', '泽库县', '465', '1', '0');
INSERT INTO `area` VALUES ('4881', '共和县', '466', '1', '0');
INSERT INTO `area` VALUES ('4882', '兴海县', '466', '1', '0');
INSERT INTO `area` VALUES ('4883', '同德县', '466', '1', '0');
INSERT INTO `area` VALUES ('4884', '贵南县', '466', '1', '0');
INSERT INTO `area` VALUES ('4885', '贵德县', '466', '1', '0');
INSERT INTO `area` VALUES ('4886', '久治县', '467', '1', '0');
INSERT INTO `area` VALUES ('4887', '玛多县', '467', '1', '0');
INSERT INTO `area` VALUES ('4888', '玛沁县', '467', '1', '0');
INSERT INTO `area` VALUES ('4889', '班玛县', '467', '1', '0');
INSERT INTO `area` VALUES ('4890', '甘德县', '467', '1', '0');
INSERT INTO `area` VALUES ('4891', '达日县', '467', '1', '0');
INSERT INTO `area` VALUES ('4892', '囊谦县', '468', '1', '0');
INSERT INTO `area` VALUES ('4893', '曲麻莱县', '468', '1', '0');
INSERT INTO `area` VALUES ('4894', '杂多县', '468', '1', '0');
INSERT INTO `area` VALUES ('4895', '治多县', '468', '1', '0');
INSERT INTO `area` VALUES ('4896', '玉树县', '468', '1', '0');
INSERT INTO `area` VALUES ('4897', '称多县', '468', '1', '0');
INSERT INTO `area` VALUES ('4898', '乌兰县', '469', '1', '0');
INSERT INTO `area` VALUES ('4899', '冷湖行委', '469', '1', '0');
INSERT INTO `area` VALUES ('4900', '大柴旦行委', '469', '1', '0');
INSERT INTO `area` VALUES ('4901', '天峻县', '469', '1', '0');
INSERT INTO `area` VALUES ('4902', '德令哈市', '469', '1', '0');
INSERT INTO `area` VALUES ('4903', '格尔木市', '469', '1', '0');
INSERT INTO `area` VALUES ('4904', '茫崖行委', '469', '1', '0');
INSERT INTO `area` VALUES ('4905', '都兰县', '469', '1', '0');
INSERT INTO `area` VALUES ('4906', '兴庆区', '470', '1', '0');
INSERT INTO `area` VALUES ('4907', '永宁县', '470', '1', '0');
INSERT INTO `area` VALUES ('4908', '灵武市', '470', '1', '0');
INSERT INTO `area` VALUES ('4909', '西夏区', '470', '1', '0');
INSERT INTO `area` VALUES ('4910', '贺兰县', '470', '1', '0');
INSERT INTO `area` VALUES ('4911', '金凤区', '470', '1', '0');
INSERT INTO `area` VALUES ('4912', '大武口区', '471', '1', '0');
INSERT INTO `area` VALUES ('4913', '平罗县', '471', '1', '0');
INSERT INTO `area` VALUES ('4914', '惠农区', '471', '1', '0');
INSERT INTO `area` VALUES ('4915', '利通区', '472', '1', '0');
INSERT INTO `area` VALUES ('4916', '同心县', '472', '1', '0');
INSERT INTO `area` VALUES ('4917', '盐池县', '472', '1', '0');
INSERT INTO `area` VALUES ('4918', '青铜峡市', '472', '1', '0');
INSERT INTO `area` VALUES ('4919', '原州区', '473', '1', '0');
INSERT INTO `area` VALUES ('4920', '彭阳县', '473', '1', '0');
INSERT INTO `area` VALUES ('4921', '泾源县', '473', '1', '0');
INSERT INTO `area` VALUES ('4922', '西吉县', '473', '1', '0');
INSERT INTO `area` VALUES ('4923', '隆德县', '473', '1', '0');
INSERT INTO `area` VALUES ('4924', '中宁县', '474', '1', '0');
INSERT INTO `area` VALUES ('4925', '沙坡头区', '474', '1', '0');
INSERT INTO `area` VALUES ('4926', '海原县', '474', '1', '0');
INSERT INTO `area` VALUES ('4927', '东山区', '475', '1', '0');
INSERT INTO `area` VALUES ('4928', '乌鲁木齐县', '475', '1', '0');
INSERT INTO `area` VALUES ('4929', '天山区', '475', '1', '0');
INSERT INTO `area` VALUES ('4930', '头屯河区', '475', '1', '0');
INSERT INTO `area` VALUES ('4931', '新市区', '475', '1', '0');
INSERT INTO `area` VALUES ('4932', '水磨沟区', '475', '1', '0');
INSERT INTO `area` VALUES ('4933', '沙依巴克区', '475', '1', '0');
INSERT INTO `area` VALUES ('4934', '达坂城区', '475', '1', '0');
INSERT INTO `area` VALUES ('4935', '乌尔禾区', '476', '1', '0');
INSERT INTO `area` VALUES ('4936', '克拉玛依区', '476', '1', '0');
INSERT INTO `area` VALUES ('4937', '独山子区', '476', '1', '0');
INSERT INTO `area` VALUES ('4938', '白碱滩区', '476', '1', '0');
INSERT INTO `area` VALUES ('4939', '吐鲁番市', '477', '1', '0');
INSERT INTO `area` VALUES ('4940', '托克逊县', '477', '1', '0');
INSERT INTO `area` VALUES ('4941', '鄯善县', '477', '1', '0');
INSERT INTO `area` VALUES ('4942', '伊吾县', '478', '1', '0');
INSERT INTO `area` VALUES ('4943', '哈密市', '478', '1', '0');
INSERT INTO `area` VALUES ('4944', '巴里坤哈萨克自治县', '478', '1', '0');
INSERT INTO `area` VALUES ('4945', '吉木萨尔县', '479', '1', '0');
INSERT INTO `area` VALUES ('4946', '呼图壁县', '479', '1', '0');
INSERT INTO `area` VALUES ('4947', '奇台县', '479', '1', '0');
INSERT INTO `area` VALUES ('4948', '昌吉市', '479', '1', '0');
INSERT INTO `area` VALUES ('4949', '木垒哈萨克自治县', '479', '1', '0');
INSERT INTO `area` VALUES ('4950', '玛纳斯县', '479', '1', '0');
INSERT INTO `area` VALUES ('4951', '米泉市', '479', '1', '0');
INSERT INTO `area` VALUES ('4952', '阜康市', '479', '1', '0');
INSERT INTO `area` VALUES ('4953', '博乐市', '480', '1', '0');
INSERT INTO `area` VALUES ('4954', '温泉县', '480', '1', '0');
INSERT INTO `area` VALUES ('4955', '精河县', '480', '1', '0');
INSERT INTO `area` VALUES ('4956', '博湖县', '481', '1', '0');
INSERT INTO `area` VALUES ('4957', '和硕县', '481', '1', '0');
INSERT INTO `area` VALUES ('4958', '和静县', '481', '1', '0');
INSERT INTO `area` VALUES ('4959', '尉犁县', '481', '1', '0');
INSERT INTO `area` VALUES ('4960', '库尔勒市', '481', '1', '0');
INSERT INTO `area` VALUES ('4961', '焉耆回族自治县', '481', '1', '0');
INSERT INTO `area` VALUES ('4962', '若羌县', '481', '1', '0');
INSERT INTO `area` VALUES ('4963', '轮台县', '481', '1', '0');
INSERT INTO `area` VALUES ('4964', '乌什县', '482', '1', '0');
INSERT INTO `area` VALUES ('4965', '库车县', '482', '1', '0');
INSERT INTO `area` VALUES ('4966', '拜城县', '482', '1', '0');
INSERT INTO `area` VALUES ('4967', '新和县', '482', '1', '0');
INSERT INTO `area` VALUES ('4968', '柯坪县', '482', '1', '0');
INSERT INTO `area` VALUES ('4969', '沙雅县', '482', '1', '0');
INSERT INTO `area` VALUES ('4970', '温宿县', '482', '1', '0');
INSERT INTO `area` VALUES ('4971', '阿克苏市', '482', '1', '0');
INSERT INTO `area` VALUES ('4972', '阿瓦提县', '482', '1', '0');
INSERT INTO `area` VALUES ('4973', '乌恰县', '483', '1', '0');
INSERT INTO `area` VALUES ('4974', '阿克陶县', '483', '1', '0');
INSERT INTO `area` VALUES ('4975', '阿合奇县', '483', '1', '0');
INSERT INTO `area` VALUES ('4976', '阿图什市', '483', '1', '0');
INSERT INTO `area` VALUES ('4977', '伽师县', '484', '1', '0');
INSERT INTO `area` VALUES ('4978', '叶城县', '484', '1', '0');
INSERT INTO `area` VALUES ('4979', '喀什市', '484', '1', '0');
INSERT INTO `area` VALUES ('4980', '塔什库尔干塔吉克自治县', '484', '1', '0');
INSERT INTO `area` VALUES ('4981', '岳普湖县', '484', '1', '0');
INSERT INTO `area` VALUES ('4982', '巴楚县', '484', '1', '0');
INSERT INTO `area` VALUES ('4983', '泽普县', '484', '1', '0');
INSERT INTO `area` VALUES ('4984', '疏勒县', '484', '1', '0');
INSERT INTO `area` VALUES ('4985', '疏附县', '484', '1', '0');
INSERT INTO `area` VALUES ('4986', '英吉沙县', '484', '1', '0');
INSERT INTO `area` VALUES ('4987', '莎车县', '484', '1', '0');
INSERT INTO `area` VALUES ('4988', '麦盖提县', '484', '1', '0');
INSERT INTO `area` VALUES ('4989', '于田县', '485', '1', '0');
INSERT INTO `area` VALUES ('4990', '和田县', '485', '1', '0');
INSERT INTO `area` VALUES ('4991', '和田市', '485', '1', '0');
INSERT INTO `area` VALUES ('4992', '墨玉县', '485', '1', '0');
INSERT INTO `area` VALUES ('4993', '民丰县', '485', '1', '0');
INSERT INTO `area` VALUES ('4994', '洛浦县', '485', '1', '0');
INSERT INTO `area` VALUES ('4995', '皮山县', '485', '1', '0');
INSERT INTO `area` VALUES ('4996', '策勒县', '485', '1', '0');
INSERT INTO `area` VALUES ('4997', '伊宁县', '486', '1', '0');
INSERT INTO `area` VALUES ('4998', '伊宁市', '486', '1', '0');
INSERT INTO `area` VALUES ('4999', '奎屯市', '486', '1', '0');
INSERT INTO `area` VALUES ('5000', '察布查尔锡伯自治县', '486', '1', '0');
INSERT INTO `area` VALUES ('5001', '尼勒克县', '486', '1', '0');
INSERT INTO `area` VALUES ('5002', '巩留县', '486', '1', '0');
INSERT INTO `area` VALUES ('5003', '新源县', '486', '1', '0');
INSERT INTO `area` VALUES ('5004', '昭苏县', '486', '1', '0');
INSERT INTO `area` VALUES ('5005', '特克斯县', '486', '1', '0');
INSERT INTO `area` VALUES ('5006', '霍城县', '486', '1', '0');
INSERT INTO `area` VALUES ('5007', '乌苏市', '487', '1', '0');
INSERT INTO `area` VALUES ('5008', '和布克赛尔蒙古自治县', '487', '1', '0');
INSERT INTO `area` VALUES ('5009', '塔城市', '487', '1', '0');
INSERT INTO `area` VALUES ('5010', '托里县', '487', '1', '0');
INSERT INTO `area` VALUES ('5011', '沙湾县', '487', '1', '0');
INSERT INTO `area` VALUES ('5012', '裕民县', '487', '1', '0');
INSERT INTO `area` VALUES ('5013', '额敏县', '487', '1', '0');
INSERT INTO `area` VALUES ('5014', '吉木乃县', '488', '1', '0');
INSERT INTO `area` VALUES ('5015', '哈巴河县', '488', '1', '0');
INSERT INTO `area` VALUES ('5016', '富蕴县', '488', '1', '0');
INSERT INTO `area` VALUES ('5017', '布尔津县', '488', '1', '0');
INSERT INTO `area` VALUES ('5018', '福海县', '488', '1', '0');
INSERT INTO `area` VALUES ('5019', '阿勒泰市', '488', '1', '0');
INSERT INTO `area` VALUES ('5020', '青河县', '488', '1', '0');
INSERT INTO `area` VALUES ('5021', '石河子市', '489', '1', '0');
INSERT INTO `area` VALUES ('5022', '阿拉尔市', '490', '1', '0');
INSERT INTO `area` VALUES ('5023', '图木舒克市', '491', '1', '0');
INSERT INTO `area` VALUES ('5024', '五家渠市', '492', '1', '0');

-- ----------------------------
-- Table structure for `authassignment`
-- ----------------------------
DROP TABLE IF EXISTS `authassignment`;
CREATE TABLE `authassignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of authassignment
-- ----------------------------

-- ----------------------------
-- Table structure for `authitem`
-- ----------------------------
DROP TABLE IF EXISTS `authitem`;
CREATE TABLE `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of authitem
-- ----------------------------
INSERT INTO `authitem` VALUES ('abnormalbalance_add', '2', '创建了 abnormalbalance_add 许可', null, null, '1465556114', '1465556114');
INSERT INTO `authitem` VALUES ('abnormalbalance_addorupdate', '2', '创建了 abnormalbalance_addorupdate 许可', null, null, '1465556985', '1465556985');
INSERT INTO `authitem` VALUES ('abnormalbalance_index', '2', '创建了 abnormalbalance_index 许可', null, null, '1465556027', '1465556027');
INSERT INTO `authitem` VALUES ('abnormalbalance_info', '2', '创建了 abnormalbalance_info 许可', null, null, '1465558001', '1465558001');
INSERT INTO `authitem` VALUES ('adminlog_index', '2', '创建了 adminlog_index 许可', null, null, '1463401486', '1463401486');
INSERT INTO `authitem` VALUES ('admin_create', '2', '创建了 admin_create 许可', null, null, '1464020295', '1464020295');
INSERT INTO `authitem` VALUES ('admin_form', '2', '创建了 admin_form 许可', null, null, '1463407055', '1463407055');
INSERT INTO `authitem` VALUES ('admin_index', '2', '创建了 admin_index 许可', null, null, '1463383680', '1463383680');
INSERT INTO `authitem` VALUES ('admin_update', '2', '创建了 admin_update 许可', null, null, '1464020770', '1464020770');
INSERT INTO `authitem` VALUES ('businessremind_confirm', '2', '创建了 businessremind_confirm 许可', null, null, '1465106846', '1465106846');
INSERT INTO `authitem` VALUES ('businessremind_index', '2', '创建了 businessremind_index 许可', null, null, '1465105797', '1465105797');
INSERT INTO `authitem` VALUES ('computer_create', '2', '创建了 computer_create 许可', null, null, '1463808126', '1463808126');
INSERT INTO `authitem` VALUES ('computer_form', '2', '创建了 computer_form 许可', null, null, '1463392227', '1463392227');
INSERT INTO `authitem` VALUES ('computer_index', '2', '创建了 computer_index 许可', null, null, '1463392224', '1463392224');
INSERT INTO `authitem` VALUES ('config_create', '2', '创建了 config_create 许可', null, null, '1465024291', '1465024291');
INSERT INTO `authitem` VALUES ('config_delete', '2', '创建了 config_delete 许可', null, null, '1465025282', '1465025282');
INSERT INTO `authitem` VALUES ('config_form', '2', '创建了 config_form 许可', null, null, '1465024281', '1465024281');
INSERT INTO `authitem` VALUES ('config_index', '2', '创建了 config_index 许可', null, null, '1465024279', '1465024279');
INSERT INTO `authitem` VALUES ('department_ajaxdepartmentbalance', '2', '创建了 department_ajaxdepartmentbalance 许可', null, null, '1465556752', '1465556752');
INSERT INTO `authitem` VALUES ('department_create', '2', '创建了 department_create 许可', null, null, '1463382621', '1463382621');
INSERT INTO `authitem` VALUES ('department_delete', '2', '创建了 department_delete 许可', null, null, '1465028889', '1465028889');
INSERT INTO `authitem` VALUES ('department_form', '2', '创建了 department_form 许可', null, null, '1463382560', '1463382560');
INSERT INTO `authitem` VALUES ('department_index', '2', '创建了 department_index 许可', null, null, '1463381822', '1463381822');
INSERT INTO `authitem` VALUES ('department_update', '2', '创建了 department_update 许可', null, null, '1463382676', '1463382676');
INSERT INTO `authitem` VALUES ('flowcondition_create', '2', '创建了 flowcondition_create 许可', null, null, '1464018679', '1464018679');
INSERT INTO `authitem` VALUES ('flowcondition_form', '2', '创建了 flowcondition_form 许可', null, null, '1464018652', '1464018652');
INSERT INTO `authitem` VALUES ('flowcondition_index', '2', '创建了 flowcondition_index 许可', null, null, '1463834884', '1463834884');
INSERT INTO `authitem` VALUES ('flowcondition_update', '2', '创建了 flowcondition_update 许可', null, null, '1464021175', '1464021175');
INSERT INTO `authitem` VALUES ('flowconfig_create', '2', '创建了 flowconfig_create 许可', null, null, '1463834906', '1463834906');
INSERT INTO `authitem` VALUES ('flowconfig_form', '2', '创建了 flowconfig_form 许可', null, null, '1463834890', '1463834890');
INSERT INTO `authitem` VALUES ('flowconfig_index', '2', '创建了 flowconfig_index 许可', null, null, '1463834879', '1463834879');
INSERT INTO `authitem` VALUES ('flowconfig_update', '2', '创建了 flowconfig_update 许可', null, null, '1464020851', '1464020851');
INSERT INTO `authitem` VALUES ('invoicing_check', '2', '创建了 invoicing_check 许可', null, null, '1465103369', '1465103369');
INSERT INTO `authitem` VALUES ('invoicing_checksale', '2', '创建了 invoicing_checksale 许可', null, null, '1465443402', '1465443402');
INSERT INTO `authitem` VALUES ('invoicing_product', '2', '创建了 invoicing_product 许可', null, null, '1465113683', '1465113683');
INSERT INTO `authitem` VALUES ('invoicing_realtime', '2', '创建了 invoicing_realtime 许可', null, null, '1465103041', '1465103041');
INSERT INTO `authitem` VALUES ('invoicing_stock', '2', '创建了 invoicing_stock 许可', null, null, '1465103235', '1465103235');
INSERT INTO `authitem` VALUES ('product_ajaxproductlist', '2', '创建了 product_ajaxproductlist 许可', null, null, '1463994592', '1463994592');
INSERT INTO `authitem` VALUES ('product_index', '2', '创建了 product_index 许可', null, null, '1463834817', '1463834817');
INSERT INTO `authitem` VALUES ('pstock_ajaxproductlist', '2', '创建了 pstock_ajaxproductlist 许可', null, null, '1464502468', '1464502468');
INSERT INTO `authitem` VALUES ('pstock_check', '2', '创建了 pstock_check 许可', null, null, '1465017408', '1465017408');
INSERT INTO `authitem` VALUES ('pstock_checkout', '2', '创建了 pstock_checkout 许可', null, null, '1465017364', '1465017364');
INSERT INTO `authitem` VALUES ('pstock_index', '2', '创建了 pstock_index 许可', null, null, '1464502454', '1464502454');
INSERT INTO `authitem` VALUES ('pstock_print', '2', '创建了 pstock_print 许可', null, null, '1464777951', '1464777951');
INSERT INTO `authitem` VALUES ('pstock_transfer', '2', '创建了 pstock_transfer 许可', null, null, '1464502459', '1464502459');
INSERT INTO `authitem` VALUES ('pstock_wastage', '2', '创建了 pstock_wastage 许可', null, null, '1464502493', '1464502493');
INSERT INTO `authitem` VALUES ('role_create', '2', '创建了 role_create 许可', null, null, '1464019528', '1464019528');
INSERT INTO `authitem` VALUES ('role_form', '2', '创建了 role_form 许可', null, null, '1464019185', '1464019185');
INSERT INTO `authitem` VALUES ('role_index', '2', '创建了 role_index 许可', null, null, '1464019183', '1464019183');
INSERT INTO `authitem` VALUES ('stats_bussiness', '2', '创建了 stats_bussiness 许可', null, null, '1463403974', '1463403974');
INSERT INTO `authitem` VALUES ('stats_index', '2', '创建了 stats_index 许可', null, null, '1463380949', '1463380949');
INSERT INTO `authitem` VALUES ('stats_product', '2', '创建了 stats_product 许可', null, null, '1465102664', '1465102664');
INSERT INTO `authitem` VALUES ('stats_purchase', '2', '创建了 stats_purchase 许可', null, null, '1465102584', '1465102584');
INSERT INTO `authitem` VALUES ('stats_realtime', '2', '创建了 stats_realtime 许可', null, null, '1463404160', '1463404160');
INSERT INTO `authitem` VALUES ('stats_supply', '2', '创建了 stats_supply 许可', null, null, '1465110780', '1465110780');
INSERT INTO `authitem` VALUES ('stats_test', '2', '创建了 stats_test 许可', null, null, '1463403830', '1463403830');
INSERT INTO `authitem` VALUES ('supplierproduct_addproduct', '2', '创建了 supplierproduct_addproduct 许可', null, null, '1464011929', '1464011929');
INSERT INTO `authitem` VALUES ('supplierproduct_ajaxproductlist', '2', '创建了 supplierproduct_ajaxproductlist 许可', null, null, '1465038381', '1465038381');
INSERT INTO `authitem` VALUES ('supplierproduct_create', '2', '创建了 supplierproduct_create 许可', null, null, '1464011926', '1464011926');
INSERT INTO `authitem` VALUES ('supplierproduct_form', '2', '创建了 supplierproduct_form 许可', null, null, '1464011915', '1464011915');
INSERT INTO `authitem` VALUES ('supplierproduct_index', '2', '创建了 supplierproduct_index 许可', null, null, '1463834818', '1463834818');
INSERT INTO `authitem` VALUES ('supplier_create', '2', '创建了 supplier_create 许可', null, null, '1463381519', '1463381519');
INSERT INTO `authitem` VALUES ('supplier_delete', '2', '创建了 supplier_delete 许可', null, null, '1463381592', '1463381592');
INSERT INTO `authitem` VALUES ('supplier_export', '2', '创建了 supplier_export 许可', null, null, '1463392645', '1463392645');
INSERT INTO `authitem` VALUES ('supplier_form', '2', '创建了 supplier_form 许可', null, null, '1463381513', '1463381513');
INSERT INTO `authitem` VALUES ('supplier_import', '2', '创建了 supplier_import 许可', null, null, '1463392881', '1463392881');
INSERT INTO `authitem` VALUES ('supplier_index', '2', '创建了 supplier_index 许可', null, null, '1463380953', '1463380953');
INSERT INTO `authitem` VALUES ('supplier_update', '2', '创建了 supplier_update 许可', null, null, '1463403622', '1463403622');
INSERT INTO `authitem` VALUES ('system_auth', '2', '创建了 system_auth 许可', null, null, '1465026339', '1465026339');
INSERT INTO `authitem` VALUES ('system_company', '2', '创建了 system_company 许可', null, null, '1463392223', '1463392223');
INSERT INTO `authitem` VALUES ('system_default', '2', '创建了 system_default 许可', null, null, '1465023551', '1465023551');
INSERT INTO `authitem` VALUES ('system_logo', '2', '创建了 system_logo 许可', null, null, '1463380951', '1463380951');
INSERT INTO `authitem` VALUES ('warehouseplanning_index', '2', '创建了 warehouseplanning_index 许可', null, null, '1463404529', '1463404529');
INSERT INTO `authitem` VALUES ('warehouse_ajaxdepartmentwarehouselist', '2', '创建了 warehouse_ajaxdepartmentwarehouselist 许可', null, null, '1465576396', '1465576396');
INSERT INTO `authitem` VALUES ('warehouse_create', '2', '创建了 warehouse_create 许可', null, null, '1464016785', '1464016785');
INSERT INTO `authitem` VALUES ('warehouse_form', '2', '创建了 warehouse_form 许可', null, null, '1464016544', '1464016544');
INSERT INTO `authitem` VALUES ('warehouse_index', '2', '创建了 warehouse_index 许可', null, null, '1464015985', '1464015985');
INSERT INTO `authitem` VALUES ('wplanning_add', '2', '创建了 wplanning_add 许可', null, null, '1464009933', '1464009933');
INSERT INTO `authitem` VALUES ('wplanning_create', '2', '创建了 wplanning_create 许可', null, null, '1464010115', '1464010115');
INSERT INTO `authitem` VALUES ('wplanning_form', '2', '创建了 wplanning_form 许可', null, null, '1463994585', '1463994585');
INSERT INTO `authitem` VALUES ('wplanning_index', '2', '创建了 wplanning_index 许可', null, null, '1463404612', '1463404612');
INSERT INTO `authitem` VALUES ('wplanning_info', '2', '创建了 wplanning_info 许可', null, null, '1464014060', '1464014060');
INSERT INTO `authitem` VALUES ('南山管理1', '1', '南山管理1', null, null, '1464025272', '1464025272');
INSERT INTO `authitem` VALUES ('超级管理员', '1', 'index.php?r=auth%2Findex', null, null, '1464025138', '1464025138');

-- ----------------------------
-- Table structure for `authitemchild`
-- ----------------------------
DROP TABLE IF EXISTS `authitemchild`;
CREATE TABLE `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of authitemchild
-- ----------------------------

-- ----------------------------
-- Table structure for `authrule`
-- ----------------------------
DROP TABLE IF EXISTS `authrule`;
CREATE TABLE `authrule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of authrule
-- ----------------------------

-- ----------------------------
-- Table structure for `businessall`
-- ----------------------------
DROP TABLE IF EXISTS `businessall`;
CREATE TABLE `businessall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL COMMENT '业务ID',
  `business_type` varchar(100) NOT NULL COMMENT '业务类型',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='所有流程历史表';

-- ----------------------------
-- Records of businessall
-- ----------------------------
INSERT INTO `businessall` VALUES ('1', '1', 'wplanning', '计划任务1', '980093', '1', '1', '2016-06-22 00:00:00', '1', '2016-06-07 00:00:00', '1', '2016-06-30 00:00:00', '1', '2016-06-05 00:00:00');

-- ----------------------------
-- Table structure for `businesslog`
-- ----------------------------
DROP TABLE IF EXISTS `businesslog`;
CREATE TABLE `businesslog` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL COMMENT '业务ID',
  `business_type` varchar(40) NOT NULL COMMENT '业务类型 对应表名',
  `content` varchar(1000) NOT NULL COMMENT '操作内容',
  `status` tinyint(1) NOT NULL COMMENT '0 无效 1有效 99删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `admin_id` int(11) NOT NULL COMMENT '操作人用户ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='业务日志表';

-- ----------------------------
-- Records of businesslog
-- ----------------------------

-- ----------------------------
-- Table structure for `businessremind`
-- ----------------------------
DROP TABLE IF EXISTS `businessremind`;
CREATE TABLE `businessremind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL COMMENT '业务ID',
  `business_type` varchar(40) NOT NULL COMMENT '业务类型',
  `status` tinyint(1) NOT NULL COMMENT '0 无效 1有效 99删除',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `admin_id` int(11) NOT NULL COMMENT '接受人用户ID',
  `content` varchar(255) DEFAULT NULL COMMENT '提醒内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='业务流程表';

-- ----------------------------
-- Records of businessremind
-- ----------------------------
INSERT INTO `businessremind` VALUES ('1', '1', 'wplanning', '1', '2016-06-01 00:00:00', '1', '采购计划审核');
INSERT INTO `businessremind` VALUES ('2', '19', 'wplanning', '1', '2016-06-05 16:38:04', '3', '3333需要您的审核');
INSERT INTO `businessremind` VALUES ('3', '19', 'wplanning', '1', '2016-06-05 16:38:04', '4', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('4', '19', 'wplanning', '1', '2016-06-05 16:38:04', '2', '3333需要您的执行');
INSERT INTO `businessremind` VALUES ('6', '23', 'wplanning', '0', '2016-06-08 17:26:05', '3', '南山采购20160608需要您的审核');
INSERT INTO `businessremind` VALUES ('9', '23', 'wplanning', '0', '2016-06-08 18:35:12', '2', '南山采购20160608需要您的批准');
INSERT INTO `businessremind` VALUES ('12', '23', 'wplanning', '0', '2016-06-08 21:39:30', '4', '南山采购20160608需要您的执行');
INSERT INTO `businessremind` VALUES ('13', '1', '采购下订', '0', '2016-06-08 21:57:22', '3', '南山采购20160608需要您的审核');
INSERT INTO `businessremind` VALUES ('15', '6', '采购下订', '0', '2016-06-08 22:14:11', '3', '南山采购20160608需要您的审核');
INSERT INTO `businessremind` VALUES ('16', '6', 'wprocurement', '0', '2016-06-08 22:51:58', '4', '南山采购20160608需要您的批准');
INSERT INTO `businessremind` VALUES ('17', '3', '订单入库', '0', '2016-06-09 10:42:55', '2', '南山采购20160608需要您的批准');
INSERT INTO `businessremind` VALUES ('18', '7', '物料销售', '0', '2016-06-09 14:31:54', '3', '南山仓库-2016-06-09的销售列表需要您的审核');
INSERT INTO `businessremind` VALUES ('19', '7', 'wplanning', '0', '2016-06-10 00:30:38', '2', '南山仓库-2016-06-09的销售列表需要您的批准');
INSERT INTO `businessremind` VALUES ('20', '7', 'wplanning', '0', '2016-06-10 00:31:08', '4', '南山仓库-2016-06-09的销售列表需要您的执行');
INSERT INTO `businessremind` VALUES ('21', '8', 'wsale', '0', '2016-06-10 14:28:00', '3', '南山仓库-2016-06-10的销售列表需要您的审核');
INSERT INTO `businessremind` VALUES ('22', '1', '盘点申请', '0', '2016-06-10 14:46:57', '3', '南山仓库盘点20160610需要您的批准');
INSERT INTO `businessremind` VALUES ('23', '1', 'wcheck', '0', '2016-06-10 14:50:06', '4', '南山仓库盘点20160610需要您的执行');
INSERT INTO `businessremind` VALUES ('24', '2', 'wplanning', '0', '2016-06-10 15:12:20', '3', '南山到福田出库记录20160610需要您的审核');
INSERT INTO `businessremind` VALUES ('25', '2', 'wcheckout', '0', '2016-06-10 15:13:29', '2', '南山到福田出库记录20160610需要您的批准');
INSERT INTO `businessremind` VALUES ('26', '2', 'wcheckout', '0', '2016-06-10 15:13:49', '4', '南山到福田出库记录20160610需要您的执行');
INSERT INTO `businessremind` VALUES ('27', '1', '调仓申请', '0', '2016-06-10 15:46:11', '2', '福田-南山-20160610需要您的批准');
INSERT INTO `businessremind` VALUES ('28', '1', 'wtransfer', '0', '2016-06-10 15:51:29', '4', '福田-南山-20160610需要您的执行');
INSERT INTO `businessremind` VALUES ('29', '1', 'wtransfer', '0', '2016-06-10 16:12:22', '2', '南山转货需要您的批准');
INSERT INTO `businessremind` VALUES ('30', '1', 'wtransferdep', '0', '2016-06-10 16:16:24', '4', '南山转货需要您的执行');
INSERT INTO `businessremind` VALUES ('31', '3', 'wbuying', '0', '2016-06-10 16:24:49', '4', '南山采购20160608需要您的执行');
INSERT INTO `businessremind` VALUES ('32', '3', 'wbuying', '0', '2016-06-10 16:26:02', '4', '南山采购20160608需要您的执行');
INSERT INTO `businessremind` VALUES ('33', '3', '物料耗损申请', '0', '2016-06-10 16:41:29', '2', '南山耗损20160610需要您的批准');
INSERT INTO `businessremind` VALUES ('34', '3', 'wwastage', '0', '2016-06-10 16:58:06', '4', '南山耗损20160610需要您的执行');
INSERT INTO `businessremind` VALUES ('35', '6', 'departmentbalancelog', '0', '2016-06-10 16:58:32', '4', '耗损出款-20160610需要您的执行');
INSERT INTO `businessremind` VALUES ('36', '1', 'departmentbalancelog', '0', '2016-06-10 19:24:14', '4', '看快速的离开了需要您的执行');
INSERT INTO `businessremind` VALUES ('37', '3', 'abnormalbalance', '0', '2016-06-10 19:26:32', '3', '啊时代发生需要您的审核');
INSERT INTO `businessremind` VALUES ('38', '3', 'abnormalbalance', '0', '2016-06-10 19:32:07', '2', '啊时代发生需要您的批准');
INSERT INTO `businessremind` VALUES ('39', '3', 'abnormalbalance', '0', '2016-06-10 19:32:23', '4', '啊时代发生需要您的执行');
INSERT INTO `businessremind` VALUES ('40', '3', 'wcheck', '0', '2016-06-11 14:49:52', '3', '测试测试测试-南山仓库盘点需要您的批准');
INSERT INTO `businessremind` VALUES ('44', '7', 'wcheck', '0', '2016-06-11 15:08:35', '3', '测试测试测试-南山仓库盘点需要您的批准');
INSERT INTO `businessremind` VALUES ('46', '15', 'wcheck', '0', '2016-06-11 15:13:56', '3', '测试测试测试-南山仓库盘点需要您的批准');
INSERT INTO `businessremind` VALUES ('47', '15', 'wcheck', '0', '2016-06-11 15:18:26', '4', '测试测试测试-南山仓库盘点需要您的执行');
INSERT INTO `businessremind` VALUES ('48', '19', 'wplanning', '0', '2016-06-11 17:10:58', '2', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('49', '19', 'wplanning', '0', '2016-06-11 17:11:31', '2', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('52', '19', 'wplanning', '0', '2016-06-11 17:13:54', '2', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('53', '19', 'wplanning', '0', '2016-06-11 17:14:16', '4', '3333需要您的执行');
INSERT INTO `businessremind` VALUES ('54', '7', 'wprocurement', '0', '2016-06-11 17:15:17', '3', '3333需要您的审核');
INSERT INTO `businessremind` VALUES ('55', '7', 'wprocurement', '0', '2016-06-11 17:18:28', '4', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('56', '4', 'wbuying', '0', '2016-06-11 17:20:35', '2', '3333需要您的批准');
INSERT INTO `businessremind` VALUES ('57', '4', 'wbuying', '0', '2016-06-11 17:22:22', '4', '3333需要您的执行');
INSERT INTO `businessremind` VALUES ('58', '1', '物料退货申请', '0', '2016-06-11 18:40:40', '4', '3333退货需要您的执行');
INSERT INTO `businessremind` VALUES ('59', '7', 'departmentbalancelog', '0', '2016-06-11 20:16:07', '4', '退货进账-20160611需要您的执行');
INSERT INTO `businessremind` VALUES ('60', '5', 'wmaterial', '0', '2016-06-11 20:43:17', '4', '阿斯蒂芬需要您的执行');
INSERT INTO `businessremind` VALUES ('61', '8', 'departmentbalancelog', '0', '2016-06-11 20:43:35', '4', '退货进账-20160611需要您的执行');
INSERT INTO `businessremind` VALUES ('62', '3', 'wcheckout', '0', '2016-06-11 20:51:26', '2', '啊沙发上需要您的批准');

-- ----------------------------
-- Table structure for `checkplanning`
-- ----------------------------
DROP TABLE IF EXISTS `checkplanning`;
CREATE TABLE `checkplanning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '表单名',
  `sn` varchar(50) NOT NULL COMMENT '单号',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `create_admin_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建人',
  `status` tinyint(2) NOT NULL COMMENT '状态 1：盘点中 2：数据校验 3：盘点完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='盘点计划表';

-- ----------------------------
-- Records of checkplanning
-- ----------------------------
INSERT INTO `checkplanning` VALUES ('1', '测试测试测试', 'CP21120160611010951', '3', '1', '2016-06-11 01:24:15', '3');

-- ----------------------------
-- Table structure for `checkplanningcondition`
-- ----------------------------
DROP TABLE IF EXISTS `checkplanningcondition`;
CREATE TABLE `checkplanningcondition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_planning_id` int(11) NOT NULL COMMENT '盘点计划表',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `check_admin_id` int(11) NOT NULL COMMENT '盘点人',
  `check_time` datetime NOT NULL COMMENT '盘点时间',
  `supplier_id` int(11) DEFAULT '0' COMMENT '供应商ID 0为全部',
  `material_type` tinyint(2) DEFAULT '0' COMMENT '物料类型 0：全部',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态 1：盘点中 2：数据校验 3：盘点完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='盘点计划物料条件表';

-- ----------------------------
-- Records of checkplanningcondition
-- ----------------------------
INSERT INTO `checkplanningcondition` VALUES ('1', '1', '1', '4', '2016-06-19 00:00:00', '1', '1', '3');

-- ----------------------------
-- Table structure for `combination`
-- ----------------------------
DROP TABLE IF EXISTS `combination`;
CREATE TABLE `combination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `payment` tinyint(2) NOT NULL COMMENT '支付方式',
  `deposit` float(10,2) NOT NULL COMMENT '定金',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `approval_time` date NOT NULL COMMENT '批准时间',
  `operation_time` date NOT NULL COMMENT '验收时间',
  `operation_cause` varchar(255) DEFAULT NULL COMMENT '验收说明',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `common` varchar(255) DEFAULT NULL COMMENT '用途说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='组合物料模板';

-- ----------------------------
-- Records of combination
-- ----------------------------
INSERT INTO `combination` VALUES ('1', '大米', 'OT82420160611204910', '368.00', '1', '100.00', '1', '4', '2016-06-11 20:50:05', '2016-06-12', '2016-06-13', '撒旦法阿斯顿', '0', '大法师发生');

-- ----------------------------
-- Table structure for `combinationproduct`
-- ----------------------------
DROP TABLE IF EXISTS `combinationproduct`;
CREATE TABLE `combinationproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `order_template_id` int(11) NOT NULL COMMENT '订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` varchar(20) NOT NULL COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='组合模板物料表';

-- ----------------------------
-- Records of combinationproduct
-- ----------------------------
INSERT INTO `combinationproduct` VALUES ('1', '4', '1', '泰国香米25KG', '0.00', '128.00', '0.00', '1', '128.00', '1', '4', '006', '25KG', '包', '1', '1');
INSERT INTO `combinationproduct` VALUES ('2', '1', '1', '大米50kg', '0.00', '120.00', '0.00', '2', '240.00', '1', '1', '001', '50kg', '包', '0', '1');

-- ----------------------------
-- Table structure for `computer`
-- ----------------------------
DROP TABLE IF EXISTS `computer`;
CREATE TABLE `computer` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL COMMENT '计算机名称',
  `mac` varchar(20) NOT NULL COMMENT 'mac地址',
  `type` tinyint(1) DEFAULT NULL,
  `position` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0无效 1有效 99删除',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计算机表';

-- ----------------------------
-- Records of computer
-- ----------------------------

-- ----------------------------
-- Table structure for `config`
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `set_name` varchar(100) NOT NULL COMMENT '设置名称',
  `set_value` varchar(1000) NOT NULL COMMENT '设置值',
  `set_desc` varchar(1000) NOT NULL COMMENT '描述',
  `group_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分组 1 logo 2名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES ('1', 'zhaomu_wenzi', 'http://baidu.com', '城市人招募', '0');
INSERT INTO `config` VALUES ('2', 'zhaomu_wenzi2', '33', '33333', '0');
INSERT INTO `config` VALUES ('3', 'admin_ver_password', 'e10adc3949ba59abbe56e057f20f883e', '', '3');
INSERT INTO `config` VALUES ('4', 'admin_business_password', 'e10adc3949ba59abbe56e057f20f883e', '超级管理员业务密码', '3');
INSERT INTO `config` VALUES ('5', 'business_business_password', 'e10adc3949ba59abbe56e057f20f883e', '业务管理员业务密码', '3');
INSERT INTO `config` VALUES ('6', 'flow_business_password', 'e10adc3949ba59abbe56e057f20f883e', '流程管理员业务密码', '3');

-- ----------------------------
-- Table structure for `department`
-- ----------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '名称',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级部门ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1有效 99删除',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `number` varchar(40) DEFAULT NULL COMMENT '部门编号',
  `acronym` varchar(20) NOT NULL COMMENT '部门缩写',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='部门表';

-- ----------------------------
-- Records of department
-- ----------------------------
INSERT INTO `department` VALUES ('1', '总部1', null, '1', '2016-06-04 15:35:17', '3', '3');
INSERT INTO `department` VALUES ('3', '南山店', null, '1', '2016-06-04 16:28:42', '002', 'nanshan');
INSERT INTO `department` VALUES ('4', '福田店', null, '1', '2016-06-04 16:29:00', '003', 'futian');

-- ----------------------------
-- Table structure for `departmentbalance`
-- ----------------------------
DROP TABLE IF EXISTS `departmentbalance`;
CREATE TABLE `departmentbalance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `income_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '进项总额',
  `expenses_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '出项总额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='部门余额表';

-- ----------------------------
-- Records of departmentbalance
-- ----------------------------
INSERT INTO `departmentbalance` VALUES ('1', '3', '10220.00', '220.00', '0.00');
INSERT INTO `departmentbalance` VALUES ('2', '1', '1000.00', '0.00', '0.00');

-- ----------------------------
-- Table structure for `departmentbalancelog`
-- ----------------------------
DROP TABLE IF EXISTS `departmentbalancelog`;
CREATE TABLE `departmentbalancelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `business_id` int(11) NOT NULL COMMENT '业务ID',
  `business_type` tinyint(2) NOT NULL COMMENT '业务类型 对应表名',
  `balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `current_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前金额',
  `mod` tinyint(1) NOT NULL DEFAULT '1' COMMENT '进出：1 进 2出',
  `content` varchar(1000) NOT NULL COMMENT '操作内容',
  `status` tinyint(1) NOT NULL COMMENT '0 无效 1有效 99删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_admin_id` int(11) NOT NULL COMMENT '创建用户ID',
  `verify_admin_id` int(11) DEFAULT NULL COMMENT '审核用户ID',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT NULL COMMENT '批准用户ID',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) DEFAULT NULL COMMENT '完成用户ID',
  `operation_time` datetime DEFAULT NULL COMMENT '完成时间',
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='资金流水日志表';

-- ----------------------------
-- Records of departmentbalancelog
-- ----------------------------
INSERT INTO `departmentbalancelog` VALUES ('5', '销售进账-20160610', '3', '7', '1', '10000.00', '120.00', '1', '库存销售', '3', '2016-06-10 01:01:43', '4', '0', '2016-06-10 01:01:43', '0', '2016-06-10 01:01:43', '4', '2016-06-10 01:01:43', '6', null);
INSERT INTO `departmentbalancelog` VALUES ('6', '耗损出款-20160610', '3', '3', '2', '10120.00', '100.00', '2', '物料耗损', '2', '2016-06-10 16:58:32', '4', '0', '2016-06-10 16:58:32', '0', '2016-06-10 16:58:32', '4', '2016-06-10 16:58:32', '6', null);
INSERT INTO `departmentbalancelog` VALUES ('7', '退货进账-20160611', '1', '1', '4', '1000.00', '100.00', '1', '物料退货', '2', '2016-06-11 20:16:07', '4', '0', '2016-06-11 20:16:07', '0', '2016-06-11 20:16:07', '4', '2016-06-11 20:16:07', '6', null);
INSERT INTO `departmentbalancelog` VALUES ('8', '退货进账-20160611', '3', '5', '4', '10220.00', '120.00', '1', '物料退货', '2', '2016-06-11 20:43:35', '4', '0', '2016-06-11 20:43:35', '0', '2016-06-11 20:43:35', '4', '2016-06-11 20:43:35', '6', null);

-- ----------------------------
-- Table structure for `flowcondition`
-- ----------------------------
DROP TABLE IF EXISTS `flowcondition`;
CREATE TABLE `flowcondition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 价格范围 2时间范围 3空间位置4 供应商范围 5商品类别',
  `name` varchar(255) NOT NULL,
  `upper_limit` varchar(100) DEFAULT NULL COMMENT '上限',
  `lower_limit` varchar(100) DEFAULT NULL COMMENT '下线',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='流程条件设置';

-- ----------------------------
-- Records of flowcondition
-- ----------------------------
INSERT INTO `flowcondition` VALUES ('1', '1', '1', '南山价格1', '1000', '800', '1');
INSERT INTO `flowcondition` VALUES ('2', '1', '1', '南山价格2', '300', '100', '1');
INSERT INTO `flowcondition` VALUES ('3', '1', '1', '33', '10', '8', '1');
INSERT INTO `flowcondition` VALUES ('4', '2', '1', '采购总价', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('5', '4', '1', '入库金额', '10000', '0', '1');
INSERT INTO `flowcondition` VALUES ('6', '5', '1', '总金额范围', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('7', '6', '1', '金额变动提交I', '10000', '0', '1');
INSERT INTO `flowcondition` VALUES ('8', '7', '1', '缺失金额', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('9', '8', '1', '出库总金额', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('10', '9', '1', '调动总价', '5000', '0', '1');
INSERT INTO `flowcondition` VALUES ('11', '10', '1', '总价值', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('12', '11', '1', '耗损总价值', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('13', '12', '1', '金额范围', '20000', '0', '1');
INSERT INTO `flowcondition` VALUES ('14', '13', '1', '退货总金额', '20000', '0', '1');

-- ----------------------------
-- Table structure for `flowconfig`
-- ----------------------------
DROP TABLE IF EXISTS `flowconfig`;
CREATE TABLE `flowconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '流程名称',
  `type` varchar(20) NOT NULL COMMENT 'purchase,checkout等',
  `operation_role_id` int(11) DEFAULT NULL COMMENT '操作角色',
  `operation_name` varchar(40) DEFAULT NULL COMMENT '执行操作名',
  `operation_department_id` int(11) DEFAULT NULL COMMENT '操作部门',
  `verify_role_id` int(11) DEFAULT NULL COMMENT '审核角色',
  `verify_name` varchar(40) DEFAULT NULL COMMENT '审核操作名',
  `verify_department_id` int(11) DEFAULT NULL COMMENT '审核部门',
  `approval_role_id` int(11) DEFAULT NULL COMMENT '批准角色',
  `approval_name` varchar(40) DEFAULT NULL COMMENT '批准操作名',
  `approval_department_id` int(11) DEFAULT NULL COMMENT '批准部门',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='流程配置表';

-- ----------------------------
-- Records of flowconfig
-- ----------------------------
INSERT INTO `flowconfig` VALUES ('1', '采购流程-南山', '1', '6', '采购下定', '3', '4', '南山审核', '3', '5', '批准3', '3', '1');
INSERT INTO `flowconfig` VALUES ('2', '下定流程-南山', '2', null, '', null, '4', '审核', '3', '6', '执行', '3', '1');
INSERT INTO `flowconfig` VALUES ('4', '订单入库-南山', '3', '6', '确定入库', '3', null, '', null, '5', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('5', '销售流程-南山', '13', '6', '确定', '3', '4', '审核', '3', '5', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('6', '资金流程-南山', '14', '6', '确定', '3', null, '', null, null, '', null, '1');
INSERT INTO `flowconfig` VALUES ('7', '盘点流程-南山', '10', '6', '确定', '3', null, null, null, '4', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('8', '出库流程-南山', '5', '6', '执行', '3', null, null, null, '5', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('9', '调仓流程-南山', '7', '6', '执行', '3', null, null, null, '5', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('10', '转货流程-南山', '6', '6', '转货', '3', null, null, null, '5', '批准', '3', '1');
INSERT INTO `flowconfig` VALUES ('11', '耗损流程-南山', '9', '6', '确定耗损', '3', null, null, null, '5', '批准1', '3', '1');
INSERT INTO `flowconfig` VALUES ('12', '非常态资金流程-南山', '15', '6', '确定3', '3', '4', '审核1', '3', '5', '批准2', '3', '1');
INSERT INTO `flowconfig` VALUES ('13', '退货流程-南山', '8', '6', '确定退货', '3', null, null, null, null, '', null, '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '0', '1', '0', '查询统计', 'stats/realtime', 'id=1', 'icon-1', '', '0', '1', '0');
INSERT INTO `menu` VALUES ('2', '0', '1', '0', '销存管理', 'invoicing/realtime', '', 'icon-2', 'invoicing_realtime', '1', '1', '0');
INSERT INTO `menu` VALUES ('3', '0', '1', '0', '业务操作', 'wplanning/index', '', 'icon-3', 'warehouseplanning', '1', '1', '0');
INSERT INTO `menu` VALUES ('4', '0', '1', '0', '业务设置', 'flowconfig/index', '', 'icon-4', 'flowconfig_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('5', '0', '1', '0', '部门基础数据', 'department/index', '', 'icon-5', '', '0', '1', '0');
INSERT INTO `menu` VALUES ('6', '0', '1', '0', '业务基础数据', 'supplier/index', '', 'icon-6', '', '0', '1', '0');
INSERT INTO `menu` VALUES ('7', '0', '1', '0', '系统基础数据', 'system/logo', '', 'icon-7', '', '1', '1', '0');
INSERT INTO `menu` VALUES ('8', '1', '2', '0,1', '实时库存统计', 'stats/realtime', '', '', 'stats_realtime', '1', '1', '0');
INSERT INTO `menu` VALUES ('9', '1', '2', '0,1', '历史表单统计', 'stats/index', '', '', '', '0', '1', '0');
INSERT INTO `menu` VALUES ('13', '6', '2', '0,6', '供应商设置', 'supplier/index', '', '', 'supplier_index', '1', '1', '1');
INSERT INTO `menu` VALUES ('14', '7', '2', '0,7', 'logo设置', 'system/logo', '', '', 'system_logo', '1', '1', '0');
INSERT INTO `menu` VALUES ('15', '7', '2', '0,7', '公司名称', 'system/company', '', '', 'system_company', '1', '1', '0');
INSERT INTO `menu` VALUES ('16', '7', '2', '0,7', '业务计算机设置', 'computer/index', '', '', ' computer_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('17', '5', '2', '0,5', '部门管理', 'department/index', '', '', 'department_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('18', '5', '2', '0,5', '角色管理', 'role/index', '', '', 'role_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('19', '5', '2', '0,5', '员工管理', 'admin/index', '', '', 'admin_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('20', '7', '2', '0,7', '事件日志', 'adminlog/index', '', '', 'adminlog_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('21', '1', '2', '0,1', '业务表单统计查询', 'stats/purchase', '', '', 'stats_bussiness', '1', '1', '0');
INSERT INTO `menu` VALUES ('22', '3', '2', '0,3', '采购计划', 'wplanning/index', '', '', 'wplanning_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('23', '3', '2', '0,3', '采购下定', 'wprocurement/index', '', '', 'wprocurement', '1', '1', '0');
INSERT INTO `menu` VALUES ('24', '3', '2', '0,3', '库存管理', 'pstock/index', '', '', 'pstock_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('25', '24', '3', '0,3,24', '库存管理', 'pstock/index', '', '', 'pstock_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('26', '7', '2', '0,7', '数据库备份及恢复', 'import/index', '', '', 'database', '1', '1', '0');
INSERT INTO `menu` VALUES ('27', '26', '3', '0,7,26', '还原数据', 'import/index', '', '', 'import_ndex', '1', '1', '0');
INSERT INTO `menu` VALUES ('28', '26', '3', '0,7,26', '备份数据', 'export/index', '', '', 'export_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('29', '6', '2', '0,6', '物料管理', 'product/index', '', '', 'product_index', '1', '1', '3');
INSERT INTO `menu` VALUES ('30', '6', '2', '0,6', '供应商出品列表', 'supplierproduct/index', '', '', 'supplierproduct_index', '1', '1', '2');
INSERT INTO `menu` VALUES ('31', '4', '2', '0,4', '业务流程列表', 'flowconfig/index', '', '', 'flowconfig_index', '1', '1', '1');
INSERT INTO `menu` VALUES ('32', '4', '2', '0,4', '业务流程条件列表', 'flowcondition/index', '', '', 'flowcondition_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('33', '6', '2', '0,6', '仓库分区', 'warehouse/index', '', '', 'warehouse_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('34', '6', '2', '0,6', '地区列表', 'area/index', '', '', 'area_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('35', '24', '3', '0,3,24', '库存入库记录', 'wbuying/index', '', '', 'wbuying_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('36', '24', '3', '0,3,24', '库存盘点记录', 'wcheck/index', '', '', 'wcheck_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('37', '24', '3', '0,3,24', '库存出库记录', 'wcheckout/index', '', '', 'wcheckout_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('38', '24', '3', '0,3,24', '库存调仓记录', 'wtransfer/index', '', '', 'wtransfer_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('39', '24', '3', '0,3,24', '库存转货记录', 'wtransferdep/index', '', '', 'wtransferdep_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('40', '24', '3', '0,3,24', '库存耗损记录', 'wwastage/index', '', '', 'wwastage_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('41', '24', '3', '0,3,24', '库存退货记录', 'wmaterial/index', '', '', 'wmaterial_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('42', '24', '3', '0,3,24', '库存出入库日志', 'wgateway/index', '', '', 'wgateway_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('43', '4', '2', '0,4', '订单模版管理', 'ordertemplate/index', '', '', 'ordertemplate_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('44', '7', '2', '0,7', '开业清库', 'system/default', '', '', 'system_default', '1', '1', '0');
INSERT INTO `menu` VALUES ('45', '7', '2', '0,7', '配置数据', 'config/index', '', '', 'system_config', '1', '1', '0');
INSERT INTO `menu` VALUES ('46', '7', '2', '0,7', '管理员权限', 'system/auth', '', '', 'system_auth', '1', '1', '0');
INSERT INTO `menu` VALUES ('47', '21', '3', '0,1,21', '采购入库报表', 'stats/purchase', '', '', 'stats_purchase', '1', '1', '0');
INSERT INTO `menu` VALUES ('48', '21', '3', '0,1,21', '物料出入报表', 'stats/product', '', '', 'stats_product', '1', '1', '0');
INSERT INTO `menu` VALUES ('49', '21', '3', '0,1,21', '供应商供货', 'stats/supply', '', '', 'stats_supply', '1', '1', '0');
INSERT INTO `menu` VALUES ('50', '21', '3', '0,1,21', '供应商结算', 'stats/settlement', '', '', 'stats_settlement', '1', '1', '0');
INSERT INTO `menu` VALUES ('52', '21', '3', '0,1,21', '毛利', 'stats/grossProfit', '', '', 'stats_grossProfit', '1', '1', '0');
INSERT INTO `menu` VALUES ('53', '21', '3', '0,1,21', '利润报表', 'stats/profit', '', '', 'stats_profit', '1', '1', '0');
INSERT INTO `menu` VALUES ('54', '2', '2', '0,2', '实时销存管理', 'invoicing/realtime', '', '', 'invoicing_realtime', '1', '1', '0');
INSERT INTO `menu` VALUES ('55', '2', '2', '0,2', '销存商品管理', 'invoicing/product', '', '', 'invoicing_product', '1', '1', '0');
INSERT INTO `menu` VALUES ('56', '2', '2', '0,2', '销存库存管理', 'invoicing/stock', '', '', 'invoicing_stock', '1', '1', '0');
INSERT INTO `menu` VALUES ('57', '2', '2', '0,2', '部门盘点管理', 'invoicing/check', '', '', 'invoicing_check', '1', '1', '0');
INSERT INTO `menu` VALUES ('58', '3', '2', '0,3', '盘点管理', 'checkplanning/index', '', '', 'checkplanning_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('59', '3', '2', '0,3', '资金管理', 'departmentbalancelog/index', '', '', 'departmentbalancelog_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('60', '59', '3', '0,3,59', '销存核实', 'wsale/index', '', '', 'wsale_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('61', '59', '3', '0,3,59', '订单支付', 'oprocurement/index', '', '', 'oprocurement_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('62', '59', '3', '0,3,59', '资金流水日志', 'departmentbalancelog/index', '', '', 'departmentbalancelog_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('63', '59', '3', '0,3,59', '非常态资金流水', 'abnormalbalance/index', '', '', 'abnormalbalance_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('64', '58', '3', '0,3,58', '盘点计划管理', 'checkplanning/index', '', '', 'checkplanning_index', '1', '1', '0');
INSERT INTO `menu` VALUES ('65', '4', '2', '0,4', '组合出库模版管理', 'combination/index', '', '', 'combination_index', '1', '1', '0');

-- ----------------------------
-- Table structure for `orderprocurement`
-- ----------------------------
DROP TABLE IF EXISTS `orderprocurement`;
CREATE TABLE `orderprocurement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `procurement_id` int(11) NOT NULL COMMENT '采购计划id',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `order_sn` varchar(40) NOT NULL COMMENT '订单编号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `planning_date` date NOT NULL COMMENT '计划下单时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 预付 2定金 3后付',
  `deposit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '定金',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `payment_term` date DEFAULT NULL COMMENT '付款期',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `operation_admin_id` int(11) NOT NULL COMMENT '下单操作人',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL,
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='采购下定支付表';

-- ----------------------------
-- Records of orderprocurement
-- ----------------------------
INSERT INTO `orderprocurement` VALUES ('1', '南山采购20160608', '6', 'OP23420160609104255', 'Onanshan20160608172605001', '1', '1', '2016-06-10', '1', '200.00', '900.00', '2016-06-10', '4', '0', '0', '0', '0', '2016-06-09 10:42:55', '0', null);
INSERT INTO `orderprocurement` VALUES ('2', '3333', '7', 'OP12520160611172035', 'Onanshan20160605163804001', '1', '1', '2016-06-01', '1', '100.00', '200.00', '2016-06-12', '4', '0', '0', '0', '0', '2016-06-11 17:20:35', '0', null);

-- ----------------------------
-- Table structure for `orderprocurementproduct`
-- ----------------------------
DROP TABLE IF EXISTS `orderprocurementproduct`;
CREATE TABLE `orderprocurementproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `order_procurement_id` int(11) NOT NULL COMMENT '采购下定ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '采购数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='采购下定支付物料表';

-- ----------------------------
-- Records of orderprocurementproduct
-- ----------------------------
INSERT INTO `orderprocurementproduct` VALUES ('1', '2', '1', '来浓香25kg', '100.00', '100.00', '9', '900.00', '1', '0', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `orderprocurementproduct` VALUES ('2', '2', '2', '来浓香25kg', '100.00', '100.00', '2', '200.00', '1', '0', '003', ' 	50kg', '包', '0', '1');

-- ----------------------------
-- Table structure for `ordertemplate`
-- ----------------------------
DROP TABLE IF EXISTS `ordertemplate`;
CREATE TABLE `ordertemplate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `payment` tinyint(2) NOT NULL COMMENT '支付方式',
  `deposit` float(10,2) NOT NULL COMMENT '定金',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `approval_time` date NOT NULL COMMENT '批准时间',
  `operation_time` date NOT NULL COMMENT '验收时间',
  `operation_cause` varchar(255) DEFAULT NULL COMMENT '验收说明',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `common` varchar(255) DEFAULT NULL COMMENT '用途说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单模板';

-- ----------------------------
-- Records of ordertemplate
-- ----------------------------

-- ----------------------------
-- Table structure for `ordertemplateproduct`
-- ----------------------------
DROP TABLE IF EXISTS `ordertemplateproduct`;
CREATE TABLE `ordertemplateproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `order_template_id` int(11) NOT NULL COMMENT '订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单模板物料表';

-- ----------------------------
-- Records of ordertemplateproduct
-- ----------------------------

-- ----------------------------
-- Table structure for `paymentorder`
-- ----------------------------
DROP TABLE IF EXISTS `paymentorder`;
CREATE TABLE `paymentorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2 完成',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款方式 1 现金 2 预付 3后付 4定金',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付订单表';

-- ----------------------------
-- Records of paymentorder
-- ----------------------------

-- ----------------------------
-- Table structure for `paymentorderproduct`
-- ----------------------------
DROP TABLE IF EXISTS `paymentorderproduct`;
CREATE TABLE `paymentorderproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `payment_order_id` int(11) NOT NULL COMMENT '订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '物料退货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单支付物料表';

-- ----------------------------
-- Records of paymentorderproduct
-- ----------------------------

-- ----------------------------
-- Table structure for `product`
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `product_category_id` int(11) NOT NULL COMMENT '物料分类ID',
  `barcode` varchar(255) NOT NULL COMMENT '条形码',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '进货参考价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商物料ID',
  `num` varchar(40) NOT NULL COMMENT '物料编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL COMMENT '物料类别',
  `inventory_warning` int(11) NOT NULL DEFAULT '0' COMMENT '库存警告',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 录入操作；2、录入审核；3、录入批准【用于历史物料数据记录表】；4、修改操作；5、修改审核；6、修改批准【用于历史物料数据记录表】 99删除',
  `create_admin_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `verfiy_admin_id` int(11) DEFAULT NULL COMMENT '审核人ID',
  `verfiy_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT NULL COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `modify_status` tinyint(1) NOT NULL COMMENT '1 录入操作；2、录入审核；3、录入批准【用于历史物料数据记录表】；4、修改操作；5、修改审核；6、修改批准【用于历史物料数据记录表】',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成操作人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='物料表';

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES ('1', '大米50kg', '0', 'code2651', '120.00', '120.00', '1', '0', '001', '50kg', '包', '1', '0', '1', '1', '2016-06-04 16:36:27', '0', '2016-06-04 08:36:27', '0', '2016-06-04 08:36:27', '1', '0', '0000-00-00 00:00:00', '0', null);
INSERT INTO `product` VALUES ('2', '来浓香25kg', '0', 'code5404', '100.00', '100.00', '1', '0', '003', ' 	50kg', '包', '1', '0', '1', '1', '2016-06-04 16:37:26', '0', '2016-06-04 08:37:26', '0', '2016-06-04 08:37:26', '1', '0', '0000-00-00 00:00:00', '0', null);
INSERT INTO `product` VALUES ('3', '佳佳酱油', '0', 'code9529', '40.00', '40.00', '2', '0', '005', '2L', '瓶', '1', '0', '1', '1', '2016-06-04 16:37:28', '0', '2016-06-04 08:37:28', '0', '2016-06-04 08:37:28', '1', '0', '0000-00-00 00:00:00', '0', null);
INSERT INTO `product` VALUES ('4', '佳佳酱油', '0', 'code5198', '40.00', '40.00', '2', '0', '005', '2L', '瓶', '1', '0', '1', '1', '2016-06-04 17:23:17', '0', '2016-06-04 17:23:17', '0', '2016-06-04 17:23:17', '1', '0', '0000-00-00 00:00:00', '0', null);
INSERT INTO `product` VALUES ('7', '泰国香米25KG', '0', 'code5082', '128.00', '128.00', '1', '4', '006', '25KG', '包', '1', '0', '0', '1', '2016-06-11 15:34:29', '0', '2016-06-11 15:34:29', '0', '2016-06-11 15:34:29', '-1', '0', '2016-06-11 15:34:29', '0', null);

-- ----------------------------
-- Table structure for `productcategory`
-- ----------------------------
DROP TABLE IF EXISTS `productcategory`;
CREATE TABLE `productcategory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类 ID',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级分类 ID',
  `name` varchar(60) NOT NULL COMMENT '分类名称',
  `slug` varchar(60) NOT NULL DEFAULT '' COMMENT '分类缩写',
  `factor` tinyint(1) unsigned NOT NULL DEFAULT '100' COMMENT '定价系数100 计算公式：进价 * 系数 /100',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` bigint(20) unsigned NOT NULL DEFAULT '99' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parent_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物料分类表';

-- ----------------------------
-- Records of productcategory
-- ----------------------------

-- ----------------------------
-- Table structure for `productlog`
-- ----------------------------
DROP TABLE IF EXISTS `productlog`;
CREATE TABLE `productlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT '物料ID',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `product_category_id` int(11) NOT NULL COMMENT '物料分类ID',
  `barcode` varchar(255) NOT NULL COMMENT '条形码',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '进货参考价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商物料ID',
  `num` varchar(40) NOT NULL COMMENT '物料编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` varchar(20) NOT NULL COMMENT '物料类别',
  `inventory_warning` int(11) NOT NULL DEFAULT '0' COMMENT '库存警告',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' 录入操作；2、录入审核；3、录入批准【用于历史物料数据记录表】；4、修改操作；5、修改审核；6、修改批准【用于历史物料数据记录表】 99删除',
  `create_admin_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `verfiy_admin_id` int(11) NOT NULL COMMENT '审核人ID',
  `verfiy_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `modify_status` tinyint(1) NOT NULL COMMENT '1 录入操作；2、录入审核；3、录入批准【用于历史物料数据记录表】；4、修改操作；5、修改审核；6、修改批准【用于历史物料数据记录表】',
  `next_step` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 下一步操作：1录入 2 修改 3审核 4 批准 99删除',
  `memo` varchar(40) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物料日志表';

-- ----------------------------
-- Records of productlog
-- ----------------------------

-- ----------------------------
-- Table structure for `productstock`
-- ----------------------------
DROP TABLE IF EXISTS `productstock`;
CREATE TABLE `productstock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batches` varchar(40) NOT NULL COMMENT '批次号',
  `product_id` int(11) NOT NULL COMMENT '物料编号',
  `number` int(11) NOT NULL COMMENT '数量',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='物料库存表';

-- ----------------------------
-- Records of productstock
-- ----------------------------
INSERT INTO `productstock` VALUES ('1', '12233', '1', '0', '1', '1');
INSERT INTO `productstock` VALUES ('2', 'CK46620160610151043', '1', '2', '2', '1');
INSERT INTO `productstock` VALUES ('4', 'RK000320160610162813', '2', '9', '1', '1');

-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '角色名称',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '总部管理', '1', '1');
INSERT INTO `role` VALUES ('2', '南山采购', '3', '1');
INSERT INTO `role` VALUES ('3', '福田采购', '4', '1');
INSERT INTO `role` VALUES ('4', '南山审核', '4', '1');
INSERT INTO `role` VALUES ('5', '南山批准', '3', '1');
INSERT INTO `role` VALUES ('6', '南山执行', '3', '1');

-- ----------------------------
-- Table structure for `roleauth`
-- ----------------------------
DROP TABLE IF EXISTS `roleauth`;
CREATE TABLE `roleauth` (
  `role_id` int(11) NOT NULL COMMENT 'roleID',
  `auth` varchar(40) NOT NULL COMMENT '权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of roleauth
-- ----------------------------

-- ----------------------------
-- Table structure for `supplier`
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL COMMENT '供应商名称',
  `num` varchar(40) NOT NULL COMMENT '供应商编号',
  `level` varchar(2) NOT NULL DEFAULT 'D' COMMENT '分级',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='供应商表';

-- ----------------------------
-- Records of supplier
-- ----------------------------
INSERT INTO `supplier` VALUES ('1', '深圳大米公用有限公司', 'N099', 'A', '1', '2016-06-04 16:35:14');
INSERT INTO `supplier` VALUES ('2', '深圳市肉类皮肤公司', 'N0098', 'A', '1', '2016-06-04 16:35:34');

-- ----------------------------
-- Table structure for `supplierproduct`
-- ----------------------------
DROP TABLE IF EXISTS `supplierproduct`;
CREATE TABLE `supplierproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '进货参考价格',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='供应商出品表';

-- ----------------------------
-- Records of supplierproduct
-- ----------------------------
INSERT INTO `supplierproduct` VALUES ('1', '大米50kg', '120.00', '1', '001', '50kg', '包', '0', '1');
INSERT INTO `supplierproduct` VALUES ('2', '来浓香25kg', '100.00', '1', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `supplierproduct` VALUES ('3', '佳佳酱油', '40.00', '2', '005', '2L', '瓶', '0', '1');
INSERT INTO `supplierproduct` VALUES ('4', '泰国香米25KG', '128.00', '1', '006', '25KG', '包', '1', '1');

-- ----------------------------
-- Table structure for `verification`
-- ----------------------------
DROP TABLE IF EXISTS `verification`;
CREATE TABLE `verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款方式 1 现金 2 预付 3后付 4定金',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销存核实表';

-- ----------------------------
-- Records of verification
-- ----------------------------

-- ----------------------------
-- Table structure for `verificationproduct`
-- ----------------------------
DROP TABLE IF EXISTS `verificationproduct`;
CREATE TABLE `verificationproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `verification_id` int(11) NOT NULL COMMENT '订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '物料退货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销存核实物料表';

-- ----------------------------
-- Records of verificationproduct
-- ----------------------------

-- ----------------------------
-- Table structure for `warehouse`
-- ----------------------------
DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE `warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 存储 2 存取销售 3销售',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  `area_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属地区',
  `is_sale` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可销售库区',
  `num` varchar(40) DEFAULT NULL COMMENT '编号',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `department_id` int(11) NOT NULL DEFAULT '1' COMMENT '部门ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='仓库分区表';

-- ----------------------------
-- Records of warehouse
-- ----------------------------
INSERT INTO `warehouse` VALUES ('1', '南山仓库', '1', '1', '3058', '0', '004', '2016-06-04 16:43:17', '3');
INSERT INTO `warehouse` VALUES ('2', '福田仓库', '1', '1', '3058', '1', '002', '2016-06-04 16:43:44', '4');

-- ----------------------------
-- Table structure for `warehouseback`
-- ----------------------------
DROP TABLE IF EXISTS `warehouseback`;
CREATE TABLE `warehouseback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款方式 1 现金 2 预付 3后付 4定金',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退库表';

-- ----------------------------
-- Records of warehouseback
-- ----------------------------

-- ----------------------------
-- Table structure for `warehousebackproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousebackproduct`;
CREATE TABLE `warehousebackproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `back_id` int(11) NOT NULL COMMENT '退库订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '退库数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退库物料表';

-- ----------------------------
-- Records of warehousebackproduct
-- ----------------------------

-- ----------------------------
-- Table structure for `warehousebuying`
-- ----------------------------
DROP TABLE IF EXISTS `warehousebuying`;
CREATE TABLE `warehousebuying` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `order_sn` varchar(40) NOT NULL COMMENT '订单编号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `planning_date` date NOT NULL COMMENT '计划下单时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 预付 2定金 3后付',
  `deposit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '定金额',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `payment_term` date DEFAULT NULL COMMENT '付款期',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '入库人',
  `operation_time` datetime NOT NULL COMMENT '执行时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL,
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='入库表';

-- ----------------------------
-- Records of warehousebuying
-- ----------------------------
INSERT INTO `warehousebuying` VALUES ('3', '南山采购20160608', 'CG37320160608172314', 'Onanshan20160608172605001', '1', '1', '2016-06-10', '1', '200.00', '900.00', '2016-06-10', '4', '0', '0000-00-00 00:00:00', '2', '2016-06-10 16:26:02', '4', '0000-00-00 00:00:00', '3', '2016-06-09 10:42:54', '4', null);
INSERT INTO `warehousebuying` VALUES ('4', '3333', 'CG83420160605163604', 'Onanshan20160605163804001', '1', '1', '2016-06-01', '1', '100.00', '200.00', '2016-06-12', '4', '0', '0000-00-00 00:00:00', '2', '2016-06-11 17:22:22', '4', '0000-00-00 00:00:00', '99', '2016-06-11 17:20:35', '4', null);

-- ----------------------------
-- Table structure for `warehousebuyingproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousebuyingproduct`;
CREATE TABLE `warehousebuyingproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `buying_id` int(11) NOT NULL COMMENT '入库订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '采购数量 ',
  `buying_number` int(11) NOT NULL COMMENT '入库数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='采购下定物料表';

-- ----------------------------
-- Records of warehousebuyingproduct
-- ----------------------------
INSERT INTO `warehousebuyingproduct` VALUES ('1', '2', '3', '来浓香25kg', '100.00', '100.00', '9', '9', '900.00', '1', '0', '003', ' 	50kg', '包', '0', '1', '1');
INSERT INTO `warehousebuyingproduct` VALUES ('2', '2', '4', '来浓香25kg', '100.00', '100.00', '2', '2', '200.00', '1', '0', '003', ' 	50kg', '包', '0', '1', '1');

-- ----------------------------
-- Table structure for `warehousecheck`
-- ----------------------------
DROP TABLE IF EXISTS `warehousecheck`;
CREATE TABLE `warehousecheck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `total_purchase_amount` float(10,2) NOT NULL COMMENT '总进货价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  `check_planning_id` int(11) DEFAULT NULL COMMENT '盘点计划ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='盘点表';

-- ----------------------------
-- Records of warehousecheck
-- ----------------------------
INSERT INTO `warehousecheck` VALUES ('1', '南山仓库盘点20160610', 'PD24120160610144614', '240.00', '360.00', '0', '1', '1', '0', null, '3', null, '4', '2016-06-10 14:46:57', '2', '2016-06-10 14:46:57', '7', null, null);
INSERT INTO `warehousecheck` VALUES ('15', '测试测试测试-南山仓库盘点', 'PD28020160611151254', '920.00', '920.00', '0', '1', '4', '0', null, '3', '2016-06-11 15:18:26', '4', '2016-06-11 15:20:43', '3', '2016-06-11 15:13:56', '7', null, '1');

-- ----------------------------
-- Table structure for `warehousecheckout`
-- ----------------------------
DROP TABLE IF EXISTS `warehousecheckout`;
CREATE TABLE `warehousecheckout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `receive_warehouse_id` int(11) NOT NULL COMMENT '接收仓库',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='出库表';

-- ----------------------------
-- Records of warehousecheckout
-- ----------------------------
INSERT INTO `warehousecheckout` VALUES ('2', '南山到福田出库记录20160610', 'CK46620160610151043', '1', '2', '240.00', '1', '3', '2016-06-10 15:13:29', '2', '2016-06-10 15:13:49', '4', '2016-06-10 15:15:54', '3', '2016-06-10 15:12:20', '8', null);
INSERT INTO `warehousecheckout` VALUES ('3', '啊沙发上', 'CK19420160611205112', '1', '2', '340.00', '4', '0', null, '2', null, '4', '2016-06-11 20:51:26', '1', '2016-06-11 20:51:26', '8', null);

-- ----------------------------
-- Table structure for `warehousecheckoutproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousecheckoutproduct`;
CREATE TABLE `warehousecheckoutproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `checkout_id` int(11) NOT NULL COMMENT '入库订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '入库数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='出库物料表';

-- ----------------------------
-- Records of warehousecheckoutproduct
-- ----------------------------
INSERT INTO `warehousecheckoutproduct` VALUES ('2', '1', '2', '大米50kg', '120.00', '120.00', '120.00', '3', '2', '240.00', '1', '0', '001', '50kg', '包', '0', '1', '1');
INSERT INTO `warehousecheckoutproduct` VALUES ('3', '2', '3', '来浓香25kg', '100.00', '100.00', '100.00', '9', '1', '100.00', '1', '0', '003', ' 	50kg', '包', '1', '1', '1');
INSERT INTO `warehousecheckoutproduct` VALUES ('4', '1', '3', '大米50kg', '120.00', '120.00', '120.00', '0', '2', '240.00', '1', '0', '001', '50kg', '包', '1', '1', '1');

-- ----------------------------
-- Table structure for `warehousecheckproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousecheckproduct`;
CREATE TABLE `warehousecheckproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `check_id` int(11) NOT NULL COMMENT '转货订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='盘点物料表';

-- ----------------------------
-- Records of warehousecheckproduct
-- ----------------------------
INSERT INTO `warehousecheckproduct` VALUES ('1', '1', '1', '大米50kg', '120.00', '120.00', '120.00', '3', '2', '240.00', '1', '0', '001', '50kg', '包', '0', '1', '1');

-- ----------------------------
-- Table structure for `warehousegateway`
-- ----------------------------
DROP TABLE IF EXISTS `warehousegateway`;
CREATE TABLE `warehousegateway` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `product_id` int(11) NOT NULL COMMENT '商品ID',
  `type` int(2) NOT NULL COMMENT '类型（1：入库 2：出库）',
  `stock` int(11) NOT NULL DEFAULT '0' COMMENT '当前库存',
  `num` int(11) NOT NULL COMMENT '数量',
  `gateway_no` int(11) NOT NULL COMMENT '出入库单号',
  `gateway_type` int(2) NOT NULL COMMENT '出入库类型',
  `create_time` datetime NOT NULL,
  `comment` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='仓库商品出入库记录表';

-- ----------------------------
-- Records of warehousegateway
-- ----------------------------
INSERT INTO `warehousegateway` VALUES ('1', '1', '1', '2', '3', '2', '2', '3', '2016-06-10 15:15:54', '');
INSERT INTO `warehousegateway` VALUES ('2', '2', '1', '1', '0', '2', '2', '3', '2016-06-10 15:15:54', '');
INSERT INTO `warehousegateway` VALUES ('3', '2', '1', '2', '2', '1', '1', '5', '2016-06-10 15:52:08', '');
INSERT INTO `warehousegateway` VALUES ('4', '1', '1', '1', '1', '1', '1', '5', '2016-06-10 15:52:08', '');
INSERT INTO `warehousegateway` VALUES ('5', '1', '1', '2', '2', '1', '1', '6', '2016-06-10 16:16:56', '');
INSERT INTO `warehousegateway` VALUES ('6', '2', '1', '1', '1', '1', '1', '6', '2016-06-10 16:16:56', '');
INSERT INTO `warehousegateway` VALUES ('8', '1', '2', '1', '0', '9', '3', '1', '2016-06-10 16:30:44', '');
INSERT INTO `warehousegateway` VALUES ('9', '1', '2', '2', '9', '1', '3', '7', '2016-06-10 16:58:32', '');
INSERT INTO `warehousegateway` VALUES ('10', '1', '1', '1', '1', '1', '15', '2', '2016-06-11 15:20:43', '');
INSERT INTO `warehousegateway` VALUES ('11', '1', '2', '1', '8', '8', '15', '2', '2016-06-11 15:20:43', '');
INSERT INTO `warehousegateway` VALUES ('13', '1', '2', '1', '8', '1', '1', '1', '2016-06-11 20:16:07', '');
INSERT INTO `warehousegateway` VALUES ('14', '1', '1', '2', '1', '1', '5', '4', '2016-06-11 20:43:35', '');

-- ----------------------------
-- Table structure for `warehousematerialreturn`
-- ----------------------------
DROP TABLE IF EXISTS `warehousematerialreturn`;
CREATE TABLE `warehousematerialreturn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `department_id` int(11) NOT NULL COMMENT '部门ID',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `operation_admin_id` int(11) DEFAULT NULL COMMENT ' 执行人',
  `operation_time` datetime DEFAULT NULL COMMENT '执行时间',
  `config_id` int(11) DEFAULT NULL COMMENT '流程ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  `common` varchar(255) DEFAULT NULL COMMENT '退货原因',
  `buying_id` int(11) DEFAULT NULL COMMENT '入库ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='物料退货';

-- ----------------------------
-- Records of warehousematerialreturn
-- ----------------------------
INSERT INTO `warehousematerialreturn` VALUES ('1', '3333退货', 'TH38120160611184011', '1', '1', '1', '100.00', '4', '0', null, '0', null, '3', '2016-06-11 18:40:40', '4', '2016-06-11 20:16:07', '13', null, '已损坏', '4');
INSERT INTO `warehousematerialreturn` VALUES ('5', '阿斯蒂芬', 'TH10620160611204132', '1', '1', '3', '120.00', '4', '0', null, '0', null, '3', '2016-06-11 20:43:17', '4', '2016-06-11 20:43:35', '13', null, '啊沙发上发大水发', null);

-- ----------------------------
-- Table structure for `warehousematerialreturnproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousematerialreturnproduct`;
CREATE TABLE `warehousematerialreturnproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `material_return_id` int(11) NOT NULL COMMENT '物料退货订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '物料退货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='物料退货物料表';

-- ----------------------------
-- Records of warehousematerialreturnproduct
-- ----------------------------
INSERT INTO `warehousematerialreturnproduct` VALUES ('1', '2', '1', '来浓香25kg', '100.00', '100.00', '100.00', '2', '1', '100.00', '1', '0', '003', ' 	50kg', '包', '1', '1', '1');
INSERT INTO `warehousematerialreturnproduct` VALUES ('2', '1', '5', '大米50kg', '120.00', '120.00', '120.00', '1', '1', '120.00', '1', '0', '001', '50kg', '包', '1', '1', '1');

-- ----------------------------
-- Table structure for `warehouseplanning`
-- ----------------------------
DROP TABLE IF EXISTS `warehouseplanning`;
CREATE TABLE `warehouseplanning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `order_sn` varchar(100) NOT NULL COMMENT '订单号',
  `planning_date` date NOT NULL COMMENT '计划下单时间',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '下定执行人',
  `operation_time` datetime NOT NULL COMMENT '执行时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程ID',
  `total_money` decimal(8,2) NOT NULL COMMENT '采购总价',
  `failCause` varchar(250) DEFAULT NULL COMMENT '驳回理由',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型 1：正常 2：例行 3：例外',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `payment` tinyint(2) DEFAULT NULL COMMENT '预定付款方式',
  `deposit` float(11,0) DEFAULT NULL COMMENT '定金',
  `payment_term` date DEFAULT NULL COMMENT '付款时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='采购计划表';

-- ----------------------------
-- Records of warehouseplanning
-- ----------------------------
INSERT INTO `warehouseplanning` VALUES ('1', '南山采购计划1', 'CG92920160604084425', '1', 'Onanshan20160604084513001', '2016-06-08', '1', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '2016-06-04 16:45:13', '0', '260.00', null, '1', '0', null, null, null);
INSERT INTO `warehouseplanning` VALUES ('2', '南山采购测试2', 'CG73720160604165409', '1', 'Onanshan20160604165442001', '2016-06-05', '1', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '2016-06-04 16:54:42', '0', '140.00', null, '1', '0', null, null, null);
INSERT INTO `warehouseplanning` VALUES ('10', '33', 'CG82220160604165610', '1', 'Onanshan20160604170057001', '2016-06-04', '1', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '2016-06-04 17:00:57', '0', '120.00', null, '1', '0', null, null, null);
INSERT INTO `warehouseplanning` VALUES ('15', '333', 'CG95620160604170627', '1', 'Onanshan20160604170828001', '2016-06-04', '1', '3', '0000-00-00 00:00:00', '2', '0000-00-00 00:00:00', '4', '0000-00-00 00:00:00', '3', '2016-06-04 17:08:28', '1', '240.00', null, '1', '0', null, null, null);
INSERT INTO `warehouseplanning` VALUES ('16', '采购1', 'CG88620160605163459', '1', 'Onanshan20160605163515001', '2016-06-03', '1', '3', '0000-00-00 00:00:00', '2', '0000-00-00 00:00:00', '4', '0000-00-00 00:00:00', '0', '2016-06-05 16:35:15', '1', '160.00', null, '1', '2', null, null, null);
INSERT INTO `warehouseplanning` VALUES ('19', '3333', 'CG83420160605163604', '1', 'Onanshan20160605163804001', '2016-06-01', '1', '3', '2016-06-11 17:13:54', '2', '2016-06-11 17:14:16', '4', '2016-06-11 17:15:16', '3', '2016-06-05 16:38:04', '1', '200.00', null, '1', '1', '1', '100', '2016-06-12');
INSERT INTO `warehouseplanning` VALUES ('23', '南山采购20160608', 'CG37320160608172314', '1', 'Onanshan20160608172605001', '2016-06-10', '1', '3', '0000-00-00 00:00:00', '2', '0000-00-00 00:00:00', '4', '0000-00-00 00:00:00', '3', '2016-06-08 17:26:05', '1', '900.00', '', '1', '1', '1', '200', '2016-06-10');

-- ----------------------------
-- Table structure for `warehouseplanningproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehouseplanningproduct`;
CREATE TABLE `warehouseplanningproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `planning_id` int(11) NOT NULL COMMENT '采购计划id',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '采购数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='采购计划物料表';

-- ----------------------------
-- Records of warehouseplanningproduct
-- ----------------------------
INSERT INTO `warehouseplanningproduct` VALUES ('1', '3', '1', '佳佳酱油', '40.00', '40.00', '1', '40.00', '2', '0', '005', '2L', '瓶', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('2', '2', '1', '来浓香25kg', '100.00', '100.00', '1', '100.00', '1', '0', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('3', '1', '1', '大米50kg', '120.00', '120.00', '1', '120.00', '1', '0', '001', '50kg', '包', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('4', '3', '2', '佳佳酱油', '40.00', '40.00', '1', '40.00', '2', '0', '005', '2L', '瓶', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('5', '2', '2', '来浓香25kg', '100.00', '100.00', '1', '100.00', '1', '0', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('13', '3', '10', '佳佳酱油', '40.00', '40.00', '3', '120.00', '2', '0', '005', '2L', '瓶', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('18', '1', '15', '大米50kg', '120.00', '120.00', '2', '240.00', '1', '0', '001', '50kg', '包', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('19', '3', '16', '佳佳酱油', '40.00', '40.00', '4', '160.00', '2', '0', '005', '2L', '瓶', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('22', '2', '19', '来浓香25kg', '100.00', '100.00', '2', '200.00', '1', '0', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `warehouseplanningproduct` VALUES ('26', '2', '23', '来浓香25kg', '100.00', '100.00', '9', '900.00', '1', '0', '003', ' 	50kg', '包', '0', '1');

-- ----------------------------
-- Table structure for `warehouseprocurement`
-- ----------------------------
DROP TABLE IF EXISTS `warehouseprocurement`;
CREATE TABLE `warehouseprocurement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `procurement_planning_id` date NOT NULL COMMENT '采购计划id',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `order_sn` varchar(40) NOT NULL COMMENT '订单编号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `planning_date` date NOT NULL COMMENT '计划下单时间',
  `payment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 预付 2定金 3后付',
  `deposit` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '定金',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `payment_term` date DEFAULT NULL COMMENT '付款期',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime NOT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '下单操作人',
  `operation_time` datetime NOT NULL COMMENT '执行时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL,
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='采购下定表';

-- ----------------------------
-- Records of warehouseprocurement
-- ----------------------------
INSERT INTO `warehouseprocurement` VALUES ('6', '南山采购20160608', '0000-00-00', 'CG37320160608172314', 'Onanshan20160608172605001', '1', '1', '2016-06-10', '1', '200.00', '900.00', '2016-06-10', '4', '3', '0000-00-00 00:00:00', '4', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '3', '2016-06-08 22:14:10', '2', null);
INSERT INTO `warehouseprocurement` VALUES ('7', '3333', '0000-00-00', 'CG83420160605163604', 'Onanshan20160605163804001', '1', '1', '2016-06-01', '1', '100.00', '200.00', '2016-06-12', '4', '3', '2016-06-11 17:18:28', '4', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '3', '2016-06-11 17:15:16', '2', null);

-- ----------------------------
-- Table structure for `warehouseprocurementproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehouseprocurementproduct`;
CREATE TABLE `warehouseprocurementproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `procurement_id` int(11) NOT NULL COMMENT '采购下定ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `product_number` int(11) NOT NULL COMMENT '采购数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='采购下定物料表';

-- ----------------------------
-- Records of warehouseprocurementproduct
-- ----------------------------
INSERT INTO `warehouseprocurementproduct` VALUES ('6', '2', '6', '来浓香25kg', '100.00', '100.00', '9', '900.00', '1', '0', '003', ' 	50kg', '包', '0', '1');
INSERT INTO `warehouseprocurementproduct` VALUES ('7', '2', '7', '来浓香25kg', '100.00', '100.00', '2', '200.00', '1', '0', '003', ' 	50kg', '包', '0', '1');

-- ----------------------------
-- Table structure for `warehousesale`
-- ----------------------------
DROP TABLE IF EXISTS `warehousesale`;
CREATE TABLE `warehousesale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='仓库销售表';

-- ----------------------------
-- Records of warehousesale
-- ----------------------------
INSERT INTO `warehousesale` VALUES ('7', '南山仓库-2016-06-09的销售列表', 'XS85720160609143154', '120.00', '3', '1', '1', '3', '2016-06-09 14:31:54', '2', '2016-06-09 14:31:54', '4', '2016-06-09 14:31:54', '3', '2016-06-09 14:31:54', '5', null);
INSERT INTO `warehousesale` VALUES ('8', '南山仓库-2016-06-10的销售列表', 'XS70520160610142800', '120.00', '3', '1', '4', '3', '2016-06-10 14:28:00', '2', '2016-06-10 14:28:00', '4', '2016-06-10 14:28:00', '0', '2016-06-10 14:28:00', '5', null);

-- ----------------------------
-- Table structure for `warehousesaleproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousesaleproduct`;
CREATE TABLE `warehousesaleproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `sale_id` int(11) NOT NULL COMMENT '销售ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '库存数量 ',
  `buying_number` int(11) NOT NULL COMMENT '销售数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='仓库销售物料表';

-- ----------------------------
-- Records of warehousesaleproduct
-- ----------------------------
INSERT INTO `warehousesaleproduct` VALUES ('5', '1', '7', '大米50kg', '120.00', '120.00', '3', '1', '120.00', '1', '0', '001', '50kg', '包', '0', '1', '1');
INSERT INTO `warehousesaleproduct` VALUES ('6', '1', '8', '大米50kg', '120.00', '120.00', '3', '1', '120.00', '1', '0', '001', '50kg', '包', '0', '1', '1');

-- ----------------------------
-- Table structure for `warehousetransfer`
-- ----------------------------
DROP TABLE IF EXISTS `warehousetransfer`;
CREATE TABLE `warehousetransfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `receive_warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '接受仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='调仓表';

-- ----------------------------
-- Records of warehousetransfer
-- ----------------------------
INSERT INTO `warehousetransfer` VALUES ('1', '福田-南山-20160610', 'DC27720160610154542', '120.00', '0', '2', '1', '4', '0', null, '2', '2016-06-10 15:51:29', '4', '2016-06-10 15:52:08', '3', '2016-06-10 15:46:11', '9', null);

-- ----------------------------
-- Table structure for `warehousetransferdep`
-- ----------------------------
DROP TABLE IF EXISTS `warehousetransferdep`;
CREATE TABLE `warehousetransferdep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `warehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID',
  `receive_warehouse_id` int(11) NOT NULL COMMENT '接收仓库',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='转货';

-- ----------------------------
-- Records of warehousetransferdep
-- ----------------------------
INSERT INTO `warehousetransferdep` VALUES ('1', '南山转货', 'CK82120160610161120', '1', '2', '120.00', '0', '4', '0', null, '2', '2016-06-10 16:16:24', '4', '2016-06-10 16:16:56', '3', '2016-06-10 16:12:22', '9', null);

-- ----------------------------
-- Table structure for `warehousetransferdepproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousetransferdepproduct`;
CREATE TABLE `warehousetransferdepproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `transfer_dep_id` int(11) NOT NULL COMMENT '转货订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='转货物料表';

-- ----------------------------
-- Records of warehousetransferdepproduct
-- ----------------------------
INSERT INTO `warehousetransferdepproduct` VALUES ('1', '1', '1', '大米50kg', '120.00', '120.00', '120.00', '2', '1', '120.00', '1', '0', '001', '50kg', '包', '0', '1', '1');

-- ----------------------------
-- Table structure for `warehousetransferproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousetransferproduct`;
CREATE TABLE `warehousetransferproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `transfer_id` int(11) NOT NULL COMMENT '转货订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='调仓物料表';

-- ----------------------------
-- Records of warehousetransferproduct
-- ----------------------------
INSERT INTO `warehousetransferproduct` VALUES ('1', '1', '1', '大米50kg', '120.00', '120.00', '120.00', '2', '1', '120.00', '1', '0', '001', '50kg', '包', '0', '2', '1');

-- ----------------------------
-- Table structure for `warehousewastage`
-- ----------------------------
DROP TABLE IF EXISTS `warehousewastage`;
CREATE TABLE `warehousewastage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `sn` varchar(40) NOT NULL COMMENT '表单号',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库ID',
  `create_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '制表人',
  `verify_admin_id` int(11) DEFAULT '0' COMMENT '审核人',
  `verify_time` datetime DEFAULT NULL COMMENT '审核时间',
  `approval_admin_id` int(11) DEFAULT '0' COMMENT '批准人',
  `approval_time` datetime DEFAULT NULL COMMENT '批准时间',
  `operation_admin_id` int(11) NOT NULL COMMENT '完成人',
  `operation_time` datetime NOT NULL COMMENT '完成时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 待审核 1 待批准 2待下定 9批准驳回 10挂起',
  `create_time` datetime NOT NULL COMMENT '制表时间',
  `config_id` int(11) NOT NULL COMMENT '流程规则ID',
  `failCause` varchar(255) DEFAULT NULL COMMENT '驳回理由',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='耗损表';

-- ----------------------------
-- Records of warehousewastage
-- ----------------------------
INSERT INTO `warehousewastage` VALUES ('1', '6666', 'HS15520160605172425', '360.00', '0', '1', '1', '0', null, '0', null, '0', '2016-06-05 17:25:12', '0', '2016-06-05 17:25:12', '0', null);
INSERT INTO `warehousewastage` VALUES ('2', '333', 'HS42020160605172531', '480.00', '0', '1', '1', '0', null, '0', null, '0', '2016-06-05 17:25:43', '0', '2016-06-05 17:25:43', '0', null);
INSERT INTO `warehousewastage` VALUES ('3', '南山耗损20160610', 'HS76520160610164104', '100.00', '0', '1', '4', '0', null, '2', '2016-06-10 16:58:06', '4', '2016-06-10 16:58:32', '3', '2016-06-10 16:41:29', '11', null);

-- ----------------------------
-- Table structure for `warehousewastageproduct`
-- ----------------------------
DROP TABLE IF EXISTS `warehousewastageproduct`;
CREATE TABLE `warehousewastageproduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '物料id ',
  `wastage_id` int(11) NOT NULL COMMENT '转货订单ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '货品价格',
  `purchase_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `sale_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `product_number` int(11) NOT NULL COMMENT '数量 ',
  `buying_number` int(11) NOT NULL COMMENT '转货数量 ',
  `total_amount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_product_id` int(11) NOT NULL COMMENT '供应商货品ID',
  `num` varchar(40) NOT NULL COMMENT '供应商出品编码',
  `spec` varchar(40) DEFAULT NULL COMMENT '规格',
  `unit` varchar(40) DEFAULT NULL COMMENT '单位',
  `material_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '物料类别',
  `warehouse_id` int(11) NOT NULL COMMENT '存放库区ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 无效 1有效 99删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='耗损物料表';

-- ----------------------------
-- Records of warehousewastageproduct
-- ----------------------------
INSERT INTO `warehousewastageproduct` VALUES ('1', '1', '1', '大米50kg', '120.00', '120.00', '120.00', '3', '3', '360.00', '1', '0', '001', '50kg', '包', '0', '1', '1');
INSERT INTO `warehousewastageproduct` VALUES ('2', '1', '2', '大米50kg', '120.00', '120.00', '120.00', '3', '4', '480.00', '1', '0', '001', '50kg', '包', '0', '1', '1');
INSERT INTO `warehousewastageproduct` VALUES ('3', '2', '3', '来浓香25kg', '100.00', '100.00', '100.00', '9', '1', '100.00', '1', '0', '003', ' 	50kg', '包', '0', '1', '1');
