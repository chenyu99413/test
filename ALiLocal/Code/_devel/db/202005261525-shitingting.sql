CREATE TABLE `tb_return_paid_tracking_no` (
  `return_paid_id` int(11) NOT NULL AUTO_INCREMENT,
  `ali_order_no` varchar(30) DEFAULT NULL COMMENT '阿里订单号',
  `old_tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_paid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退回已支付阿里单号与物流单号关系表' ROW_FORMAT=COMPACT;