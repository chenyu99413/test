CREATE TABLE `tb_fee_item_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_item_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `staff_name` varchar(32) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `comment` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='阿里费用修改日志';

