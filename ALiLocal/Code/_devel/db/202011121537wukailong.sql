/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-11-16 15:55:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_return_channel
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel`;
CREATE TABLE `tb_return_channel` (
  `channel_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(10) DEFAULT NULL COMMENT '渠道名称',
  `network_code` varchar(32) NOT NULL COMMENT '网络代码',
  `trace_network_code` varchar(32) DEFAULT '' COMMENT '末端网络代码',
  `channel_group_id` int(11) DEFAULT NULL COMMENT '渠道分组ID',
  `sender_id` text COMMENT '发件人ID',
  `account` varchar(20) DEFAULT NULL COMMENT '渠道账号',
  `supplier_id` int(11) NOT NULL COMMENT '供应商id',
  `label_sign` varchar(30) DEFAULT NULL COMMENT '标签标记',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `send_kj` int(2) DEFAULT '2' COMMENT '是否推送三免 1：是 2 否',
  `has_battery` int(2) NOT NULL DEFAULT '2' COMMENT '是否支持带电 1：是 2 否',
  `is_declaration` int(2) NOT NULL DEFAULT '2' COMMENT '是否支持报关 1：是 2 否',
  `declare_threshold` decimal(8,2) DEFAULT NULL COMMENT '申报总价阈值，两位小数',
  `type` int(2) DEFAULT NULL COMMENT '渠道类型:1快件类-UPS,2快件类-DHL,3快件类-FedEx,4EMS类,5小包类',
  `forecast_type` int(2) DEFAULT NULL COMMENT '数据预报规则:1向下取整回调,2实重减3g,3以出库原数据',
  `length` decimal(8,2) DEFAULT NULL COMMENT '最长边限制,单位CM',
  `width` decimal(8,2) DEFAULT NULL COMMENT '第二长边限制,单位CM',
  `height` decimal(8,2) DEFAULT NULL COMMENT '高限制,单位CM',
  `perimeter` decimal(8,2) DEFAULT NULL COMMENT '周长限制,单位CM',
  `girth` decimal(8,2) DEFAULT NULL COMMENT '围长限制,单位CM',
  `weight` decimal(8,3) DEFAULT NULL COMMENT '单个包裹实重限制，单位KG',
  `total_cost_weight` decimal(8,3) DEFAULT NULL COMMENT '整票计费重限制，单位KG',
  `check_complete` int(2) DEFAULT '1' COMMENT '验证数据是否完整  1：是 2 否',
  `sort_code` varchar(50) DEFAULT NULL COMMENT '分拣路由码',
  `is_pda` tinyint(2) DEFAULT '0' COMMENT '是否支持无FDA出货0：否1：是',
  `print_method` varchar(10) DEFAULT NULL COMMENT '打印方式',
  `postcode_verify` tinyint(2) DEFAULT '0' COMMENT '是否验证偏派邮编0：否 1：是',
  PRIMARY KEY (`channel_id`),
  KEY `channel_group_id` (`channel_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='退货渠道';

-- ----------------------------
-- Table structure for tb_return_channel_department_available
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_department_available`;
CREATE TABLE `tb_return_channel_department_available` (
  `available_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '渠道可用部门ID',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`available_id`),
  KEY `channel_id` (`channel_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1334 DEFAULT CHARSET=utf8 COMMENT='退件渠道可用部门';

-- ----------------------------
-- Table structure for tb_return_channel_zip_code
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_zip_code`;
CREATE TABLE `tb_return_channel_zip_code` (
  `id` int(11) NOT NULL,
  `channel_id` int(11) DEFAULT NULL COMMENT '主表id',
  `zip_code` varchar(20) DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退件渠道偏派邮编表';

-- ----------------------------
-- Table structure for tb_return_channel_zip_code_id_seq
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_zip_code_id_seq`;
CREATE TABLE `tb_return_channel_zip_code_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `return_total_id` int(10) DEFAULT NULL COMMENT '总单表id',
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='退货订单表';

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
  `sub_code` varchar(30) DEFAULT '' COMMENT '子单号',
  PRIMARY KEY (`return_package_id`),
  KEY `order_id` (`return_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8 COMMENT='退货入库包裹信息';

-- ----------------------------
-- Table structure for tb_return_total
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_total`;
CREATE TABLE `tb_return_total` (
  `return_total_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `return_total_no` varchar(30) DEFAULT NULL COMMENT '总单号',
  `operate_name` varchar(30) DEFAULT NULL COMMENT '操作人',
  `operate_id` int(11) DEFAULT NULL COMMENT '操作人id',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态0:操作中，1已结束',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_total_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='退货总单表';
