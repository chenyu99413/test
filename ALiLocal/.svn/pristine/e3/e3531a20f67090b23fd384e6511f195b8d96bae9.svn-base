/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-06-29 16:25:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_pos_scan
-- ----------------------------
DROP TABLE IF EXISTS `tb_pos_scan`;
CREATE TABLE `tb_pos_scan` (
  `pos_scan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(100) DEFAULT NULL COMMENT '仓库代码',
  `order_id` int(11) DEFAULT NULL COMMENT '订单id',
  `scan_name` varchar(20) DEFAULT NULL COMMENT '扫描人',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`pos_scan_id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='货件定位扫描表';
