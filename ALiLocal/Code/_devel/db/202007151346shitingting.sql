CREATE TABLE `tb_product_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `staff_name` varchar(32) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `comment` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品修改日志' AUTO_INCREMENT=1;