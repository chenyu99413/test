CREATE TABLE `tb_sender` (
  `sender_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '发件人ID',
  `sender_code` varchar(20) DEFAULT NULL COMMENT '发件人代码',
  `sender_name` varchar(10) DEFAULT NULL COMMENT '发件人姓名',
  `sender_company` varchar(150) DEFAULT NULL COMMENT '发件人公司',
  `sender_phone` varchar(30) DEFAULT NULL COMMENT '发件人电话',
  `sender_province` varchar(20) DEFAULT NULL COMMENT '发件人省',
  `sender_city` varchar(30) DEFAULT NULL COMMENT '发件人市',
  `sender_area` varchar(50) DEFAULT NULL COMMENT '发件人区县',
  `sender_address` varchar(150) DEFAULT NULL COMMENT '发件人详细地址',
  `sender_zip_code` varchar(10) DEFAULT NULL COMMENT '发件人邮编',
  `sender_email` varchar(30) DEFAULT NULL COMMENT '发件人邮箱',
  `create_time` int(11) DEFAULT NULL,
  `upate_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tb_channel`
ADD COLUMN `sender_id`  int NULL COMMENT '发件人ID' AFTER `channel_group_id`;
