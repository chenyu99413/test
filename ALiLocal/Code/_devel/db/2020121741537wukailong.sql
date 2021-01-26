/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : albb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-12-17 10:39:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_return_order
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_order`;
CREATE TABLE `tb_return_order` (
  `return_order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `ali_order_no` varchar(50) DEFAULT NULL COMMENT '阿里订单号，必填',
  `order_no` varchar(50) DEFAULT NULL COMMENT '客户订单号',
  `reference_no` varchar(200) DEFAULT NULL COMMENT '送货到仓物流单号，多个用英文逗号分开，非必填',
  `far_no` varchar(20) DEFAULT NULL COMMENT '泛远单号',
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID,根据阿里仓库分配的部门',
  `service_code` varchar(128) DEFAULT NULL COMMENT '指定路线编码，必填',
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `account` varchar(10) DEFAULT NULL COMMENT '打单账号',
  `sort` varchar(2) DEFAULT '' COMMENT 'D3和S1两个状态',
  `sender_mobile` varchar(32) DEFAULT NULL COMMENT '发件联系人手机，[国家码-]电话，必填',
  `sender_telephone` varchar(32) DEFAULT NULL COMMENT '发件联系人电话：[国家码-][地区码-]电话',
  `sender_email` varchar(64) DEFAULT NULL COMMENT '发件联系人邮箱',
  `sender_name1` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称1，必填',
  `sender_name2` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称2',
  `sender_street1` varchar(128) DEFAULT NULL COMMENT '街道名或⻔牌号，必填',
  `sender_street2` varchar(128) DEFAULT NULL COMMENT '街道名',
  `sender_country_code` varchar(2) DEFAULT NULL COMMENT '发件人国家二字码，必填',
  `sender_city` varchar(32) DEFAULT NULL COMMENT '发件城市，必填',
  `sender_postal_code` varchar(35) DEFAULT NULL COMMENT '发件人邮编',
  `sender_state_region_code` varchar(40) DEFAULT NULL COMMENT '发件人⾏政区/洲',
  `sender_comment` longtext COMMENT '发件人常用联系信息备注项',
  `consignee_mobile` varchar(32) DEFAULT NULL COMMENT '收件人手机，必填',
  `consignee_telephone` varchar(32) DEFAULT NULL COMMENT '收件联系人电话：[国家码-][地区码-]电话',
  `consignee_email` varchar(64) DEFAULT '' COMMENT '收件联系人邮箱',
  `consignee_name1` varchar(64) DEFAULT NULL COMMENT '收件公司名或客户名，必填',
  `consignee_name2` varchar(64) DEFAULT NULL COMMENT '收件公司名或客户名',
  `consignee_street1` varchar(128) DEFAULT NULL COMMENT '收件人街道名或门牌号1，必填',
  `consignee_street2` varchar(128) DEFAULT '' COMMENT '收件人街道名或门牌号2',
  `consignee_country_code` varchar(2) DEFAULT NULL COMMENT '收件人国家二字码，必填',
  `consignee_city` varchar(32) DEFAULT NULL COMMENT '收件人城市，必填',
  `consignee_postal_code` varchar(35) DEFAULT NULL COMMENT '收件人邮编',
  `consignee_state_region_code` varchar(40) DEFAULT NULL COMMENT '收件人行政区/州',
  `declaration_type` varchar(2) DEFAULT NULL COMMENT '报关类型，参照附表，默认QT,必填',
  `total_amount` decimal(8,2) DEFAULT NULL COMMENT '总申报价值，两位小数，必填',
  `currency_code` varchar(3) DEFAULT NULL COMMENT '申报币种，必填',
  `need_insurance` varchar(10) DEFAULT NULL COMMENT '是否使用保险，默认false,必填',
  `tax_payer_id` varchar(30) DEFAULT NULL COMMENT '收件⼈税号',
  `remarks` varchar(256) DEFAULT NULL COMMENT '订单描述',
  `reason_code` varchar(255) DEFAULT NULL COMMENT '取消或退货原因代码，退货时必填',
  `reason_name` varchar(255) DEFAULT NULL COMMENT '取消或退货原因信息，退货时必填',
  `reason_remark` varchar(255) DEFAULT NULL COMMENT '取消或退货原因备注',
  `return_type` varchar(50) DEFAULT NULL COMMENT '退货方式，"WAREHOUSE_RETURN/SELF_RETURN,退货时必填',
  `return_mobile` varchar(32) DEFAULT NULL COMMENT '退货联系人手机号：[国家码-]电话，必填',
  `return_telephone` varchar(32) DEFAULT NULL COMMENT '退货联系人电话：[国家码-][地区码-]电话',
  `return_email` varchar(64) DEFAULT NULL COMMENT '退货联系人电子邮箱',
  `return_name1` varchar(64) DEFAULT NULL COMMENT '退货公司名或客户名1，必填',
  `return_name2` varchar(64) DEFAULT NULL COMMENT '退货公司名或客户名2',
  `return_street1` varchar(128) DEFAULT NULL COMMENT '退货街道名1，必填',
  `return_street2` varchar(128) DEFAULT NULL COMMENT '退货街道名2',
  `return_country_code` varchar(2) DEFAULT NULL COMMENT '退货人国家二字码，必填',
  `return_city` varchar(32) DEFAULT NULL COMMENT '退货地址，必填',
  `return_postal_code` varchar(35) DEFAULT NULL COMMENT '退货邮编',
  `return_state_region_code` varchar(40) DEFAULT NULL COMMENT '退货行政区/州',
  `need_pick_up` varchar(1) DEFAULT NULL COMMENT '1代表需要上门揽收',
  `warehouse_code` varchar(32) DEFAULT NULL COMMENT '仓库编码',
  `warehouse_name` varchar(32) DEFAULT NULL COMMENT '仓库名称',
  `packing_type` varchar(10) DEFAULT 'BOX' COMMENT '包裹类型，BOX,PAK,DOC',
  `volumn_chargeable` varchar(1) DEFAULT '' COMMENT '1代表本票货物是泡货，只要有一个包裹是泡货，这里就填1',
  `weight_income_ali` decimal(10,3) DEFAULT NULL COMMENT '阿里推送过来的包裹总计费重，总泡重和总实重取大的那个',
  `weight_actual_ali` decimal(10,3) DEFAULT NULL COMMENT '阿里推送过来的包裹的总实重',
  `package_total_num` int(11) DEFAULT NULL COMMENT '包裹总件数',
  `weight_income_in` decimal(10,3) DEFAULT '0.000' COMMENT '入库的包裹总计费重，用于计算应收',
  `weight_actual_in` decimal(10,3) DEFAULT '0.000' COMMENT '入库的包裹总实重',
  `weight_cost_out` decimal(10,3) DEFAULT NULL COMMENT '出库的包裹总计费重，用于计算成本',
  `total_single_weight` decimal(10,3) DEFAULT NULL COMMENT '整票货的计费重（单位为KG）',
  `weight_actual_out` decimal(10,3) DEFAULT NULL COMMENT '出库的包裹总实重',
  `weight_label` decimal(10,3) DEFAULT NULL COMMENT '标签重，现在修改为预报重',
  `weight_bill` decimal(10,3) DEFAULT NULL COMMENT '账单重',
  `consignee_cn` varchar(100) DEFAULT '' COMMENT '收件人（中）',
  `consignee_city_cn` varchar(100) DEFAULT '' COMMENT '收件人（中）',
  `consignee_address_cn` varchar(255) DEFAULT '' COMMENT '收件地址（中）',
  `business_code` varchar(10) DEFAULT '' COMMENT '经营单位编码10位',
  `ali_testing_order` varchar(1) DEFAULT '' COMMENT '1代表是测试订单',
  `commission_code` varchar(30) DEFAULT '' COMMENT '委托书编号',
  `add_data_status` varchar(1) DEFAULT '' COMMENT '1代表已添加完成',
  `error_message` varchar(200) DEFAULT '' COMMENT '发送订单数据给快件返回的报错信息',
  `payment_time` int(11) DEFAULT NULL COMMENT '阿里支付时间',
  `order_status_copy` varchar(2) DEFAULT '' COMMENT '扣件时复制订单状态代码',
  `pick_company` varchar(10) DEFAULT '' COMMENT '取件网点',
  `warehouse_in_time` int(11) DEFAULT NULL COMMENT '入库时间',
  `warehouse_confirm_time` int(11) DEFAULT NULL COMMENT '核查时间',
  `warehouse_out_time` int(11) DEFAULT NULL COMMENT '出库时间',
  `carrier_pick_time` int(11) DEFAULT NULL COMMENT '承运商取件时间',
  `delivery_time` int(11) DEFAULT NULL COMMENT '签收时间',
  `present_time` int(11) DEFAULT NULL COMMENT '预派时间',
  `far_warehouse_in_time` int(11) DEFAULT NULL COMMENT '泛远入库时间',
  `far_warehouse_in_operator` varchar(10) DEFAULT NULL COMMENT '泛远入库操作人',
  `record_order_date` int(11) DEFAULT NULL COMMENT '发件日',
  `remark` text COMMENT '订单备注信息',
  `dwsremarks` text COMMENT 'DWS��������Ϣ',
  `warning_handled` varchar(1) DEFAULT '0' COMMENT '0代表没有处理，1代表已处理',
  `related_ali_order_no` varchar(20) DEFAULT NULL COMMENT '关联阿里单号',
  `profit` decimal(10,2) DEFAULT NULL COMMENT '毛利',
  `weight_after_optimization` decimal(10,3) DEFAULT NULL COMMENT '优化后的计费重量',
  `channel_id_after_optimization` int(11) DEFAULT NULL COMMENT '优化后的渠道',
  `amount_after_optimization` decimal(10,3) DEFAULT NULL COMMENT '优化后的总成本金额',
  `weight_before_optimization` decimal(10,3) DEFAULT NULL COMMENT '优化前的计费重量',
  `channel_id_before_optimization` int(11) DEFAULT NULL COMMENT '优化前的渠道',
  `amount_before_optimization` decimal(10,3) DEFAULT NULL COMMENT '优化前的成本金额',
  `address_change` varchar(1) DEFAULT NULL COMMENT '"1" 代表更改了地址，需要收更改地址费',
  `address_change_info` varchar(255) DEFAULT NULL COMMENT '地址更改信息',
  `suspected_remote` varchar(1) DEFAULT NULL COMMENT '"1" 代表疑似偏远',
  `send_ali_form` varchar(1) DEFAULT '' COMMENT '1:代表已发送面单给阿里',
  `send_ali_form_error` varchar(255) DEFAULT NULL COMMENT '发送阿里单证错误信息',
  `ali_form_exception_info` varchar(255) DEFAULT NULL COMMENT '阿里单证审核失败具体信息',
  `ems_order_id` varchar(30) DEFAULT NULL COMMENT 'EMS response的订单号',
  `create_time` int(11) DEFAULT NULL COMMENT '创建日期',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `send_times` int(11) DEFAULT '0' COMMENT '自动发送面单次数',
  `fedex_tracking_id_type` varchar(10) DEFAULT '' COMMENT 'fedex的跟踪ID类型',
  `fedex_form_id` varchar(10) DEFAULT '' COMMENT 'fedex的ID',
  `warehouse_in_department_id` int(11) DEFAULT NULL COMMENT '订单入库部门',
  `total_list_no` varchar(20) DEFAULT '' COMMENT '总单号',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `is_send` int(1) DEFAULT '0' COMMENT '发送快件系统标识，0:未发送，1:已发送',
  `bill_amount` decimal(8,2) DEFAULT NULL COMMENT '账单金额',
  `black_flag` varchar(1) DEFAULT NULL COMMENT '"1" 代表订单有黑名单信息',
  `zip_flag` varchar(1) DEFAULT NULL COMMENT '1:代表邮编预警',
  `packagenum` int(11) DEFAULT NULL COMMENT '包裹袋数',
  `boxnum` int(11) DEFAULT NULL COMMENT '纸箱数',
  `specialpackagenum` int(11) DEFAULT NULL COMMENT '异形包装数',
  `get_trace_flag` int(10) DEFAULT '1' COMMENT 'ems、eub订阅trackingmore轨迹，1:未订阅；2已订阅;3已取消',
  `print_time` int(11) DEFAULT NULL COMMENT '标签打印时间',
  `pick_up_time` int(11) DEFAULT NULL COMMENT '取件时间',
  `wechat_id` int(11) DEFAULT NULL COMMENT '取件员id',
  `black_reason` text COMMENT '黑名单原因',
  `sender_id` int(11) DEFAULT NULL COMMENT 'tb_sender表id',
  `auto_send_mail_stu` tinyint(2) DEFAULT '0' COMMENT '支付超时发送邮件保存状态：0:未发送，1：120小时判断已发送，2：144小时判断已发送',
  `delivery_priority` varchar(64) DEFAULT NULL COMMENT '发货优先级',
  `fda_company` varchar(84) DEFAULT NULL COMMENT 'FDA公司名',
  `fda_address` varchar(128) DEFAULT NULL COMMENT 'FDA地址',
  `fda_city` varchar(84) DEFAULT NULL COMMENT 'FDA城市',
  `fda_post_code` varchar(35) DEFAULT NULL COMMENT 'FDA邮编',
  `has_battery` int(2) NOT NULL DEFAULT '2' COMMENT '是否支持带电 1：是 2 否',
  `has_battery_num` int(2) NOT NULL DEFAULT '0' COMMENT '带电产品数量：1：不超过2个，2：2个以上',
  `total_volumn_weight` decimal(10,3) DEFAULT '0.000' COMMENT '入库的包裹总体积重',
  `total_out_volumn_weight` decimal(10,3) DEFAULT '0.000' COMMENT '出库的包裹总体积重',
  `transport_id` int(11) DEFAULT NULL COMMENT '运输方式id',
  `package_total_in` int(11) DEFAULT '0' COMMENT '入库总包裹数',
  `is_pda` tinyint(2) DEFAULT '0' COMMENT '是否为PDA类品0:否1：是',
  `dhl_pdf_type` tinyint(2) DEFAULT '0' COMMENT 'dhl是否为有纸化：1：无纸化0：有纸化',
  `return_total_id` int(10) DEFAULT NULL COMMENT '总单表id',
  `return_out_total_id` int(11) DEFAULT NULL COMMENT '出库总单id',
  `order_status` varchar(3) DEFAULT '10' COMMENT '退货订单状态：10仓储中，15已确认，20待重发，30待销毁，40待退回，50已打印，60已重发，70已销毁，80已退回',
  `return_time` int(11) DEFAULT NULL COMMENT '退货时间',
  `storage_time` int(11) DEFAULT NULL COMMENT '仓储时间，单位小时',
  `new_tracking_no` varchar(30) DEFAULT NULL COMMENT '重发单号',
  `again_time` int(11) DEFAULT NULL COMMENT '重发时间',
  `destroy_time` int(11) DEFAULT NULL COMMENT '销毁时间',
  `send_back_time` int(11) DEFAULT NULL COMMENT '退回时间',
  `original_num` int(11) DEFAULT NULL COMMENT '原件数',
  `original_weight` decimal(10,3) DEFAULT NULL COMMENT '原重量',
  `return_num` int(11) DEFAULT NULL COMMENT '退货件数',
  `return_weight` decimal(10,3) DEFAULT NULL COMMENT '退货重量',
  `return_spec` varchar(60) DEFAULT NULL COMMENT '退货规格',
  `scan_id` int(11) DEFAULT NULL COMMENT '扫描人id',
  `scan_name` varchar(20) DEFAULT NULL COMMENT '入库操作人',
  `queren_time` int(11) DEFAULT NULL COMMENT '确认时间',
  PRIMARY KEY (`return_order_id`),
  KEY `ali_order_no` (`ali_order_no`),
  KEY `far_no` (`far_no`),
  KEY `packing_type` (`packing_type`),
  KEY `volumn_chargeable` (`volumn_chargeable`),
  KEY `department_id` (`department_id`),
  KEY `tracking_no` (`tracking_no`),
  KEY `order_status` (`order_status`(2)),
  KEY `warning_handled` (`warning_handled`),
  KEY `record_order_date` (`record_order_date`),
  KEY `channel_id` (`channel_id`),
  KEY `sort` (`sort`),
  KEY `address_change` (`address_change`),
  KEY `suspected_remote` (`suspected_remote`),
  KEY `ems_order_id` (`ems_order_id`),
  KEY `send_ali_form` (`send_ali_form`),
  KEY `send_ali_form_error` (`send_ali_form_error`),
  KEY `send_times` (`send_times`),
  KEY `ali_testing_order` (`ali_testing_order`),
  KEY `warehouse_in_department_id` (`warehouse_in_department_id`),
  KEY `reference_no` (`reference_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='退件订单';

-- ----------------------------
-- Table structure for tb_return_order_product
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_order_product`;
CREATE TABLE `tb_return_order_product` (
  `order_product_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'product ID',
  `return_order_id` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='阿里订单产品信息备份表';

-- ----------------------------
-- Table structure for tb_return_out_package
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_out_package`;
CREATE TABLE `tb_return_out_package` (
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
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8 COMMENT='退货出库包裹信息';
