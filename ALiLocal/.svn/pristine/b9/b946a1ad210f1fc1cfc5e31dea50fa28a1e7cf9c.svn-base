/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50621
Source Host           : 127.0.0.1:3306
Source Database       : aliexpress

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2019-04-08 13:57:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_channel_department_available
-- ----------------------------
DROP TABLE IF EXISTS `tb_channel_department_available`;
CREATE TABLE `tb_channel_department_available` (
  `available_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '渠道可用部门ID',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`available_id`),
  KEY `channel_id` (`channel_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道可用部门';

-- ----------------------------
-- Table structure for tb_channel_department_disabled
-- ----------------------------
DROP TABLE IF EXISTS `tb_channel_department_disabled`;
CREATE TABLE `tb_channel_department_disabled` (
  `disabled_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `effect_time` int(11) DEFAULT NULL COMMENT '生效时间',
  `failure_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`disabled_id`),
  KEY `channel_id` (`channel_id`),
  KEY `department_id` (`department_id`),
  KEY `effect_time` (`effect_time`),
  KEY `failure_time` (`failure_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道禁止部门';
