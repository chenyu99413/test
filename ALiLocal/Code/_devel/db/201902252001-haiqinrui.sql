CREATE TABLE `tb_return` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `ali_order_no` varchar(20) DEFAULT NULL COMMENT '阿里单号',
  `return_no` varchar(20) DEFAULT NULL COMMENT '退件编号，自动生成：R+年月+四位自增',
  `return_status` varchar(1) DEFAULT '1' COMMENT '"1"待退，"2"已退',
  `return_operator` varchar(4) DEFAULT NULL COMMENT '退件人姓名',
  `consignee_name` varchar(4) DEFAULT NULL COMMENT '收件人',
  `consignee_phone` varchar(20) DEFAULT NULL COMMENT '收件电话',
  `consignee_address` varchar(255) DEFAULT NULL COMMENT '收件地址',
  `express_no` varchar(30) DEFAULT NULL COMMENT '快递号',
  `express_company` varchar(20) DEFAULT NULL COMMENT '快递公司，下拉保存的时候直接保存中文',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_id`),
  KEY `ali_order_no` (`ali_order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退件';


