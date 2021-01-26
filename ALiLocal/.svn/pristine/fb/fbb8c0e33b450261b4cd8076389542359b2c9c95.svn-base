CREATE TABLE `tb_totaltracking` (
  `tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `tracking_code` varchar(30) DEFAULT NULL COMMENT '物流代码',
  `location` varchar(32) DEFAULT '' COMMENT '位置，必填',
  `timezone` varchar(3) DEFAULT NULL COMMENT '时区号，东区录入正数，西区录入负数',
  `confirm_flag` varchar(1) DEFAULT '0' COMMENT '1代表轨迹信息确认了，不能够编辑，并且可以发给阿里,0代表未确认',
  `trace_desc_en` varchar(256) DEFAULT '' COMMENT '轨迹英文信息',
  `trace_desc_cn` varchar(128) DEFAULT '' COMMENT '轨迹中文描述信息',
  `operator_name` varchar(16) DEFAULT '' COMMENT '操作员',
  `trace_time` int(11) DEFAULT NULL COMMENT '轨迹发生时间，ISO8601，必填',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`tracking_id`),
  KEY `tracking_code` (`tracking_code`)
) ENGINE=InnoDB AUTO_INCREMENT=106732 DEFAULT CHARSET=utf8 COMMENT='轨迹信息';