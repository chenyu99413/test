CREATE TABLE `tb_receivable_formula` (
  `receivable_formula_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `package_type` varchar(5) DEFAULT '' COMMENT '包裹类型',
  `fee_name` varchar(50) DEFAULT NULL COMMENT '费用名称',
  `formula` text COMMENT '公式',
  `remark` varchar(60) DEFAULT NULL COMMENT '备注',
  `calculation_flag` varchar(1) DEFAULT NULL COMMENT '自动计算（1表示自动计算）',
  `effective_time` int(11) DEFAULT NULL COMMENT '生效时间',
  `fail_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`receivable_formula_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应收公式' ROW_FORMAT=COMPACT;