/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-01-07 16:17:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_channel_declare_threshold
-- ----------------------------
DROP TABLE IF EXISTS `tb_channel_declare_threshold`;
CREATE TABLE `tb_channel_declare_threshold` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `front` int(11) DEFAULT NULL COMMENT '阀值区间开始',
  `after` int(11) DEFAULT NULL COMMENT '阀值区间结束',
  `country_group_id` int(11) DEFAULT NULL COMMENT '国家组ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='渠道申报总价阀值设置区间';
