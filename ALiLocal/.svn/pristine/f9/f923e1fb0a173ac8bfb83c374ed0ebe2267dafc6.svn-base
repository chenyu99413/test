DROP TABLE IF EXISTS `tb_cainiao`;
CREATE TABLE `tb_cainiao` (
  `cainiao_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `cainiao_code` varchar(255) DEFAULT NULL COMMENT '节点CODE',
  `cainiao_time` int(11) DEFAULT NULL COMMENT '回推日期',
  `confirm_flag` int(2) DEFAULT '0' COMMENT '1代表事件信息确认了，不能够编辑，并且可以发给菜鸟,0代表未确认',
  `reason` varchar(100) DEFAULT NULL COMMENT '失败原因',
  `send_flag` int(2) DEFAULT '0' COMMENT '1代表已发送给菜鸟',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `operator` varchar(10) DEFAULT NULL COMMENT '操作人',
  `send_times` int(11) DEFAULT '0' COMMENT '发送次数',
  `return_reason` varchar(255) DEFAULT NULL COMMENT '阿里失败回执',
  PRIMARY KEY (`cainiao_id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
