CREATE TABLE `tb_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `file_name` varchar(100) DEFAULT NULL COMMENT '文件名称',
  `file_path` text COMMENT '文件路径',
  `operator` varchar(10) DEFAULT NULL COMMENT '操作员',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`file_id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT '文件';
