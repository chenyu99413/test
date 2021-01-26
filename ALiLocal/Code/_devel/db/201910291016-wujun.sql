CREATE TABLE `tb_total_ordertrack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `ali_order_no` varchar(20) DEFAULT NULL COMMENT '阿里订单号，必填',
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='轨迹总单订单明细表';