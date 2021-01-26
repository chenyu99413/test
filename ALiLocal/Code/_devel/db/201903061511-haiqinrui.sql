CREATE TABLE `tb_channel_cost_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_cost_id` int(11) DEFAULT NULL COMMENT '渠道ID',
  `package_type` varchar(5) DEFAULT '' COMMENT '包裹类型',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `tb_channel_cost_formula` (
  `channel_cost_formula_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL COMMENT '渠道包裹类型id',
  `fee_name` varchar(50) DEFAULT NULL COMMENT '费用名称',
  `formula` varchar(100) DEFAULT NULL COMMENT '公式',
  `remark` varchar(60) DEFAULT NULL COMMENT '备注',
  `calculation_flag` varchar(1) DEFAULT NULL COMMENT '自动计算（1表示自动计算）',
  `effective_time` int(11) DEFAULT NULL COMMENT '生效时间',
  `fail_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`channel_cost_formula_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tb_channel_cost`
ADD COLUMN `tax`  decimal(10,2) NULL DEFAULT NULL COMMENT '税率' AFTER `ratio`;

ALTER TABLE `tb_channel_cost`
ADD COLUMN `fuel_surcharge_flag`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '是否计算燃油(1表示要计算燃油)' AFTER `tax`;