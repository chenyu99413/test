/*
Navicat MySQL Data Transfer

Source Server         : dhx
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : aliexpress

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2019-11-26 10:01:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_email_template
-- ----------------------------
DROP TABLE IF EXISTS `tb_email_template`;
CREATE TABLE `tb_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(128) DEFAULT NULL COMMENT '模板名称',
  `template_text` text COMMENT '模板内容',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='邮件模板';
