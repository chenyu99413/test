ALTER TABLE `tb_order`
ADD COLUMN `present_time` int(11) DEFAULT NULL COMMENT '预派时间' AFTER `delivery_time`;