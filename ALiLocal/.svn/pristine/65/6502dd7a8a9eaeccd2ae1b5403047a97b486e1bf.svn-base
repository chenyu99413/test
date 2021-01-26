/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-11-12 09:58:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_return_order
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_order`;
CREATE TABLE `tb_return_order` (
  `return_order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '关联订单表id',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID(退货仓库)',
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号（原单号）',
  `ali_order_no` varchar(50) DEFAULT NULL COMMENT '阿里订单号，必填',
  `order_status` varchar(3) DEFAULT '10' COMMENT '退货订单状态：10仓储中，20待重发，30待销毁，40待退回，50已打印，60已重发，70已销毁，80已退回',
  `return_time` int(11) DEFAULT NULL COMMENT '退货时间',
  `storage_time` int(11) DEFAULT NULL COMMENT '仓储时间，单位小时',
  `new_tracking_no` varchar(30) DEFAULT NULL COMMENT '重发单号',
  `again_time` int(11) DEFAULT NULL COMMENT '重发时间',
  `destroy_time` int(11) DEFAULT NULL COMMENT '销毁时间',
  `send_back_no` varchar(30) DEFAULT NULL COMMENT '退回单号',
  `send_back_time` int(11) DEFAULT NULL COMMENT '退回时间',
  `original_num` int(11) DEFAULT NULL COMMENT '原件数',
  `original_weight` decimal(10,3) DEFAULT NULL COMMENT '原重量',
  `return_num` int(11) DEFAULT NULL COMMENT '退货件数',
  `return_weight` decimal(10,3) DEFAULT NULL COMMENT '退货重量',
  `return_spec` varchar(60) DEFAULT NULL COMMENT '退货规格',
  `scan_id` int(11) DEFAULT NULL COMMENT '扫描人id',
  `scan_name` varchar(20) DEFAULT NULL COMMENT '入库操作人',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='退货订单表';

-- ----------------------------
-- Table structure for tb_return_package
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_package`;
CREATE TABLE `tb_return_package` (
  `return_package_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '包裹ID',
  `return_order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `quantity` int(11) DEFAULT NULL COMMENT '数量，必填',
  `length` decimal(8,2) DEFAULT NULL COMMENT '长，必填',
  `width` decimal(8,2) DEFAULT NULL COMMENT '宽，必填',
  `height` decimal(8,2) DEFAULT NULL COMMENT '高，必填',
  `weight` decimal(8,2) DEFAULT NULL COMMENT '单个包裹的实际重量，两位小数，必填',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_package_id`),
  KEY `order_id` (`return_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='退货入库包裹信息';
