CREATE TABLE `tb_total_track` (
  `total_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `total_list_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `operation_name` varchar(20) DEFAULT NULL COMMENT '操作者姓名',
  `operation_time` int(11) DEFAULT NULL COMMENT '操作日期',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`total_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8 COMMENT='轨迹总单表';