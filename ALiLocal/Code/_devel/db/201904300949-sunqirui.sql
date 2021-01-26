CREATE TABLE `tb_product_fuel` (
  `product_fuel_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '产品卖价燃油ID',
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `effective_date` int(11) DEFAULT NULL COMMENT '生效日期',
  `fail_date` int(11) DEFAULT NULL COMMENT '失效日期',
  `rate` decimal(10,4) DEFAULT NULL COMMENT '燃油税率',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`product_fuel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='产品卖价燃油';
