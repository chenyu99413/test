ALTER TABLE `tb_abnormal_parcel`
ADD COLUMN `checkabnormal_type` varchar(200) DEFAULT NULL COMMENT '核查异常分类' AFTER `issue_type`;


CREATE TABLE `tb_blacklist` (
  `blacklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '产品id',
  `consignee_country_code` varchar(2) DEFAULT NULL COMMENT '收件人国家二字码，必填',
  `consignee_postal_code` varchar(35) DEFAULT NULL COMMENT '收件人邮编',
	`consignee_city` varchar(32) DEFAULT NULL COMMENT '收件人城市',
  `consignee_state_region_code` varchar(40) DEFAULT NULL COMMENT '收件人行政区/州',
	`product_name` varchar(32) DEFAULT NULL COMMENT '商品名称',
	`sender_name1` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称1',
	`sender_name2` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称2',
	`sender_street1` varchar(128) DEFAULT NULL COMMENT '发件人地址',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`blacklist_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品黑名单';

ALTER TABLE `tb_order`
ADD COLUMN `black_flag` varchar(1) DEFAULT NULL COMMENT '"1" 代表订单有黑名单信息' AFTER `bill_amount`;

ALTER TABLE `tb_order`
ADD COLUMN `packagenum` int(11) DEFAULT NULL COMMENT '包裹袋数' AFTER `black_flag`;
ALTER TABLE `tb_order`
ADD COLUMN `boxnum` int(11) DEFAULT NULL COMMENT '纸箱数' AFTER `packagenum`;
ALTER TABLE `tb_order`
ADD COLUMN `specialpackagenum` int(11) DEFAULT NULL COMMENT '异形包装数' AFTER `boxnum`;
ALTER TABLE `tb_order`
ADD COLUMN `zip_flag` varchar(1) DEFAULT NULL COMMENT '1:代表邮编预警' AFTER `black_flag`;
ALTER TABLE `tb_product`
ADD COLUMN `label_remark` text COMMENT '打单要求' AFTER `confirm_remark`;

CREATE TABLE `tb_headline` (
  `headline_id` int(11) NOT NULL AUTO_INCREMENT,
  `headline` varchar(60) DEFAULT NULL COMMENT '标题',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`headline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道异常件标签表';

CREATE TABLE `tb_abnormal_parcel_headline` (
	`abnormal_parcel_headline_id` INT (11) NOT NULL AUTO_INCREMENT,
	`abnormal_parcel_id` INT (11) DEFAULT NULL COMMENT '异常件ID',
	`headline_id` INT (11) DEFAULT NULL COMMENT '渠道异常标签ID',
	`create_time` INT (11) DEFAULT NULL COMMENT '创建时间',
	`update_time` INT (11) DEFAULT NULL COMMENT '更新时间',
	PRIMARY KEY (
		`abnormal_parcel_headline_id`
	),
	KEY `abnormal_parcel_id` (`abnormal_parcel_id`),
	KEY `headline_id` (`headline_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '渠道异常表';