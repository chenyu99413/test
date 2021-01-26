ALTER TABLE `tb_total_orderout`
ADD COLUMN `flag` varchar(2) DEFAULT '0' COMMENT '0:页面展示订单号，1:页面展示运单号' AFTER `state`;