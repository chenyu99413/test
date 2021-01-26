/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-09-11 10:28:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_code_transport
-- ----------------------------
DROP TABLE IF EXISTS `tb_code_transport`;
CREATE TABLE `tb_code_transport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道id',
  `product_id` int(11) DEFAULT NULL COMMENT '产品',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='运输方式';

-- ----------------------------
-- Table structure for tb_code_transport_logs
-- ----------------------------
DROP TABLE IF EXISTS `tb_code_transport_logs`;
CREATE TABLE `tb_code_transport_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `staff_name` varchar(32) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `comment` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='运输方式修改日志';
