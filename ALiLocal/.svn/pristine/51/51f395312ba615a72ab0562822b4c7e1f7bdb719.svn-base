ALTER TABLE `tb_return_order`
ADD COLUMN `channel_id`  int NULL COMMENT '渠道id' AFTER `update_time`;

ALTER TABLE `tb_fee`
ADD COLUMN `is_return`  tinyint NULL DEFAULT 0 COMMENT '是否退件费用0否1是' AFTER `recon_state`;

ALTER TABLE `tb_return_order`
ADD COLUMN `destroy_no`  varchar(30) NULL COMMENT '销毁单号' AFTER `again_time`;

ALTER TABLE `tb_return_order`
ADD COLUMN `ems_order_id`  varchar(30) NULL AFTER `channel_id`;

ALTER TABLE `tb_return_order`
ADD COLUMN `return_out_total_id`  int NULL COMMENT '出库总单id' AFTER `return_total_id`;


SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_return_channel_cost
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_cost`;
CREATE TABLE `tb_return_channel_cost` (
  `channel_cost_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `ratio` int(11) DEFAULT NULL COMMENT '计泡系数，例如：5000,6000',
  `tax` decimal(10,2) DEFAULT NULL COMMENT '税率',
  `fuel_surcharge_flag` varchar(1) DEFAULT '' COMMENT '是否计算燃油(1表示要计算燃油)',
  `fuel_surcharge_dicount` decimal(10,2) DEFAULT NULL COMMENT '燃油附加费折扣，以小数显示',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`channel_cost_id`),
  KEY `channel_id` (`channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='退件渠道成本';

-- ----------------------------
-- Table structure for tb_return_channel_cost_formula
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_cost_formula`;
CREATE TABLE `tb_return_channel_cost_formula` (
  `channel_cost_formula_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_cost_id` int(11) DEFAULT NULL COMMENT '渠道成本id',
  `supplier_id` int(11) DEFAULT NULL COMMENT '供应商id',
  `fee_name` varchar(50) DEFAULT NULL COMMENT '费用名称',
  `formula` text COMMENT '公式',
  `remark` varchar(60) DEFAULT NULL COMMENT '备注',
  `calculation_flag` varchar(1) DEFAULT NULL COMMENT '自动计算（1表示自动计算）',
  `effective_time` int(11) DEFAULT NULL COMMENT '生效时间',
  `fail_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `currency_code` varchar(10) DEFAULT 'CNY' COMMENT '币种',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`channel_cost_formula_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='退件渠道成本公式';

-- ----------------------------
-- Table structure for tb_return_channel_cost_p_p_r
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_channel_cost_p_p_r`;
CREATE TABLE `tb_return_channel_cost_p_p_r` (
  `channel_cost_p_p_r_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_cost_id` int(11) DEFAULT NULL COMMENT '渠道成本ID',
  `price_manage_id` int(11) DEFAULT NULL COMMENT '价格表ID',
  `partition_manage_id` int(11) DEFAULT NULL COMMENT '分区表ID',
  `remote_manage_id` int(11) DEFAULT NULL COMMENT '分区表ID',
  `effective_time` int(11) DEFAULT NULL COMMENT '生效时间',
  `invalid_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `single_lowest_weight` decimal(10,3) DEFAULT NULL COMMENT '单件最低计费重（单位为KG）',
  PRIMARY KEY (`channel_cost_p_p_r_id`),
  KEY `channel_id` (`channel_cost_id`),
  KEY `price_manage_id` (`price_manage_id`),
  KEY `partiton_manage_id` (`partition_manage_id`),
  KEY `remote_manage_id` (`remote_manage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=408 DEFAULT CHARSET=utf8 COMMENT='退件渠道成本价格分区偏派';

-- ----------------------------
-- Table structure for tb_return_out_total
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_out_total`;
CREATE TABLE `tb_return_out_total` (
  `return_out_total_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `return_total_no` varchar(30) DEFAULT NULL COMMENT '总单号',
  `operate_name` varchar(30) DEFAULT NULL COMMENT '操作人',
  `operate_id` int(11) DEFAULT NULL COMMENT '操作人id',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态0:操作中，1已结束',
  `type` tinyint(2) DEFAULT '1' COMMENT '1:重发 2：销毁 3：退货',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_out_total_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='退货出库总单表';

-- ----------------------------
-- Table structure for tb_return_sub_code
-- ----------------------------
DROP TABLE IF EXISTS `tb_return_sub_code`;
CREATE TABLE `tb_return_sub_code` (
  `sub_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `sub_code` varchar(30) DEFAULT NULL COMMENT '子单号',
  `weight` decimal(8,2) DEFAULT NULL COMMENT '实重',
  `length` decimal(8,2) DEFAULT NULL COMMENT '长',
  `width` decimal(8,2) DEFAULT NULL COMMENT '宽',
  `height` decimal(8,2) DEFAULT NULL COMMENT '高',
  `pallet_no` varchar(20) DEFAULT NULL COMMENT '托盘号',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`sub_code_id`),
  KEY `order_id` (`order_id`),
  KEY `pallet_no` (`pallet_no`)
) ENGINE=InnoDB AUTO_INCREMENT=9595 DEFAULT CHARSET=utf8 COMMENT='子单号';
