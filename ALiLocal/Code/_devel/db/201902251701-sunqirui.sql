CREATE TABLE `tb_abnormal_parcel` (
  `abnormal_parcel_id` int(11) NOT NULL AUTO_INCREMENT,
  `ali_order_no` varchar(20) DEFAULT NULL COMMENT '阿里单号',
  `parcel_flag` varchar(1) DEFAULT '1' COMMENT '问题件状态：1默认开启，2关闭',
  `abnormal_parcel_no` varchar(20) DEFAULT NULL COMMENT '问题件编号,自动生成，年月+4位自增',
  `abnormal_parcel_operator` varchar(4) DEFAULT NULL COMMENT '问题发起人',
  `issue_type` varchar(1) DEFAULT '' COMMENT '问题类型：1仓内异常件2取件异常件3渠道异常件',
  `issue_content` text COMMENT '问题详情',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`abnormal_parcel_id`),
  KEY `ali_order_no` (`ali_order_no`),
  KEY `parcel_flag` (`parcel_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题件';

CREATE TABLE `tb_abnormal_parcel_history` (
  `abnormal_parcel_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `abnormal_parcel_id` int(11) DEFAULT NULL COMMENT '异常件ID',
  `follow_up_content` text COMMENT '跟进内容',
  `follow_up_operator` varchar(4) DEFAULT NULL COMMENT '跟进人姓名',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`abnormal_parcel_history_id`),
  KEY `abnormal_parcel_id` (`abnormal_parcel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题件跟进记录';
