ALTER TABLE `tb_zip_code`
ADD COLUMN `pick_company`  varchar(10) NULL DEFAULT '' COMMENT '取件网点' AFTER `area`;
ALTER TABLE `tb_order`
ADD COLUMN `pick_company`  varchar(10) NULL DEFAULT '' COMMENT '取件网点' AFTER `payment_time`;
