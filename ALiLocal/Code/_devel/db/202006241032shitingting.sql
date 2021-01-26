ALTER TABLE `tb_channel` 
ADD `has_battery` INT(2) NOT NULL DEFAULT '2' COMMENT '是否支持带电 1：是 2 否',
ADD `is_declaration` INT(2) NOT NULL DEFAULT '2' COMMENT '是否支持报关 1：是 2 否',
ADD `declare_threshold` decimal(8,2) DEFAULT NULL COMMENT '申报总价阈值，两位小数';

ALTER TABLE `tb_product` 
ADD`check_has_battery` varchar(1) DEFAULT '' COMMENT '检查是否带电，如果是1，订单出库的时候进行检查';

ALTER TABLE `tb_order` 
ADD `has_battery` INT(2) NOT NULL DEFAULT '2' COMMENT '是否支持带电 1：是 2 否';