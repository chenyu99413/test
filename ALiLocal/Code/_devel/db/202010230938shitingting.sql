ALTER TABLE `tb_customer`
ADD COLUMN `customer_type`  int(2) NULL DEFAULT NULL COMMENT '客户类型:1线上2线下';