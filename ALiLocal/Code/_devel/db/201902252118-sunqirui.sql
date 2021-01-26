CREATE TABLE `tb_far_out_package` (
  `far_package_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `quantity_out` int(11) DEFAULT NULL COMMENT '出库数量',
  `length_out` decimal(8,2) DEFAULT NULL COMMENT '出库长度',
  `width_out` decimal(8,2) DEFAULT NULL COMMENT '出库宽度',
  `height_out` decimal(8,2) DEFAULT NULL COMMENT '出库高度',
  `weight_out` decimal(8,2) DEFAULT NULL COMMENT '出库实重',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`far_package_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;