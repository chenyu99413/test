/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-10-16 16:01:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_trail_total
-- ----------------------------
DROP TABLE IF EXISTS `tb_trail_total`;
CREATE TABLE `tb_trail_total` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `total_order` varchar(20) DEFAULT NULL COMMENT '总单号',
  `create_time` int(11) DEFAULT NULL COMMENT '创建日期',
  `update_time` int(11) DEFAULT NULL COMMENT '更新日期',
  `operator` varchar(20) DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `total_order` (`total_order`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_trail_total_detail
-- ----------------------------
DROP TABLE IF EXISTS `tb_trail_total_detail`;
CREATE TABLE `tb_trail_total_detail` (
  `detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `total_id` int(11) DEFAULT NULL COMMENT '主表id',
  `ali_order_no` varchar(50) DEFAULT NULL COMMENT '阿里订单号，必填',
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
