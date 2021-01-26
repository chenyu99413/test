CREATE TABLE `tb_total_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_list_no` varchar(20) NOT NULL COMMENT '总单号',
  `tracking_no` varchar(30) NOT NULL COMMENT '末端单号',
  `state` int(2) DEFAULT '0' COMMENT '状态：0:未核验 1 核对成功 2 有货无单 3 有单无货',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='随货单证核查';

