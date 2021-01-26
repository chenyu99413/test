CREATE TABLE `tb_code_ali_ib_product` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ali_product` varchar(100) DEFAULT NULL COMMENT '阿里产品ID',
  `ib_product` varchar(100) DEFAULT NULL COMMENT 'IB产品ID',
  `operator` varchar(10) DEFAULT NULL COMMENT '操作人',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ali_product` (`ali_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阿里IB产品对应表';

