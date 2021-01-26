CREATE TABLE `tb_sku_delete_log` (
  `sku_delete_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_sku` varchar(100) DEFAULT NULL COMMENT '客户SKU',
  `shop_sku` varchar(255) DEFAULT NULL COMMENT '平台商品ID',
  `customer_id` int(11) DEFAULT NULL COMMENT '客户ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`sku_delete_log_id`),
  KEY `customer_sku` (`customer_sku`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tb_sku_delete_log`
CHANGE COLUMN `customer_id` `customer_code`  varchar(30) NULL DEFAULT NULL COMMENT '客户代码' AFTER `shop_sku`,
DROP INDEX `customer_id` ,
ADD INDEX `customer_code` (`customer_code`) USING BTREE ;
