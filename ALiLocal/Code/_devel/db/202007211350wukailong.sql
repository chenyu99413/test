/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-07-21 13:50:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_order_product_copy
-- ----------------------------
DROP TABLE IF EXISTS `tb_order_product_copy`;
CREATE TABLE `tb_order_product_copy` (
  `order_product_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'product ID',
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `product_name` varchar(32) DEFAULT NULL COMMENT '产品中文名称，必填',
  `product_name_en` varchar(32) DEFAULT NULL COMMENT '产品英文名称，必填',
  `product_quantity` int(11) DEFAULT NULL COMMENT '产品数量，必填',
  `product_unit` varchar(4) DEFAULT NULL COMMENT '产品单位，件、台、套....必填',
  `hs_code` varchar(12) DEFAULT NULL COMMENT '海关编码，必填',
  `declaration_price` decimal(10,4) DEFAULT NULL COMMENT '申报单价，四位小数，必填',
  `has_battery` varchar(10) DEFAULT NULL COMMENT '是否带电，true/false,必填',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `product_name_far` varchar(32) DEFAULT '' COMMENT 'FAR中文品名',
  `product_name_en_far` varchar(32) DEFAULT '' COMMENT 'FAR英文品名',
  `hs_code_far` varchar(12) DEFAULT '' COMMENT 'FAR HS编码',
  `product_quantity1_far` decimal(6,2) DEFAULT NULL COMMENT 'FAR数量1',
  `product_unit1_far` varchar(10) DEFAULT '' COMMENT 'FAR单位1，可以理解为法定单位',
  `product_quantity2_far` decimal(6,2) DEFAULT NULL COMMENT 'FAR数量2,阿里的数量映射到这个字段',
  `product_unit2_far` varchar(10) DEFAULT '' COMMENT 'FAR单位2',
  `material_use` varchar(255) DEFAULT '' COMMENT '材质和用途，用于显示在invoice上',
  PRIMARY KEY (`order_product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177189 DEFAULT CHARSET=utf8 COMMENT='阿里订单产品信息备份表';
