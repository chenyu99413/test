ALTER TABLE `tb_order`
ADD COLUMN `cainiao_order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '菜鸟订单号' AFTER `order_no`;

CREATE TABLE `tb_sub_parcel` (
  `sub_parcel_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `sub_parcel_code` varchar(50) DEFAULT NULL COMMENT '子单号',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`sub_parcel_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='菜鸟子单号';

ALTER TABLE `tb_order_product`
ADD COLUMN `currency_code` varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '申报币种' AFTER `declaration_price`;