CREATE TABLE `tb_total_out` (
  `total_id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `out_department_id` int(11) DEFAULT NULL COMMENT '启程仓id',
	`in_department_id` int(11) DEFAULT NULL COMMENT '抵达仓id',
	`service_code` varchar(128) DEFAULT NULL COMMENT '产品，必填',
	`consignee_name` varchar(4) DEFAULT NULL COMMENT '收件人',
  `consignee_phone` varchar(20) DEFAULT NULL COMMENT '收件电话',
  `consignee_address` varchar(255) DEFAULT NULL COMMENT '收件地址',
  `express_no` varchar(30) DEFAULT NULL COMMENT '快递号',
	`express_company` varchar(30) DEFAULT NULL COMMENT '快递公司',
	`operation_name` varchar(20) DEFAULT NULL COMMENT '操作者姓名',
  `operation_time` int(11) DEFAULT NULL COMMENT '操作日期',
  `status` varchar(1) DEFAULT '0' COMMENT '状态，0：未完成; 1: 已完成',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`total_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='总单表';

CREATE TABLE `tb_total_in` (
  `total_id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `out_department_id` int(11) DEFAULT NULL COMMENT '启程仓id',
	`in_department_id` int(11) DEFAULT NULL COMMENT '抵达仓id',
	`service_code` varchar(128) DEFAULT NULL COMMENT '产品，必填',
	`operation_name` varchar(20) DEFAULT NULL COMMENT '操作者姓名',
  `operation_time` int(11) DEFAULT NULL COMMENT '操作日期',
  `status` varchar(1) DEFAULT '0' COMMENT '状态，0：未完成; 1: 已完成',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`total_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='总单表';


CREATE TABLE `tb_total_orderout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `ali_order_no` varchar(20) DEFAULT NULL COMMENT '阿里订单号，必填',
	`tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
	`create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='启程总单订单明细表';

CREATE TABLE `tb_total_orderin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_no` varchar(20) DEFAULT NULL COMMENT '总单号',
  `ali_order_no` varchar(20) DEFAULT NULL COMMENT '阿里订单号，必填',
	`tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
	`create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='抵达总单订单明细表';


ALTER TABLE `tb_total_orderout`
ADD COLUMN `state`  varchar(1) DEFAULT '0' COMMENT '0是未抵达，1是已抵达' AFTER `tracking_no`;