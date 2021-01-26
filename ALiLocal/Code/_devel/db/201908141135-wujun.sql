/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : ali1688

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2019-08-14 11:33:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_noserivce_zipcode
-- ----------------------------
DROP TABLE IF EXISTS `tb_noserivce_zipcode`;
CREATE TABLE `tb_noserivce_zipcode` (
  `zip_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `zip_code` varchar(9) DEFAULT NULL COMMENT '邮编',
  `city` varchar(6) DEFAULT NULL COMMENT '城市',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`zip_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='无服务邮编表';
