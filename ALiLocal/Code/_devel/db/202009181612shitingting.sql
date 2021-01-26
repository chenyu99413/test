ALTER TABLE `tb_order`
ADD COLUMN `order_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '客户订单号' AFTER `ali_order_no`;