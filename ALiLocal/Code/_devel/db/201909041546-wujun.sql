ALTER TABLE `tb_title`
ADD COLUMN `customer_id` int(11) DEFAULT NULL COMMENT '客户id' AFTER `title_id`;
ALTER TABLE `tb_title`
ADD COLUMN `supplier_id`  int(11) DEFAULT NULL COMMENT '供应商id' AFTER `customer_id`;