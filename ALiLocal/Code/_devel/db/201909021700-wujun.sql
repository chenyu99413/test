ALTER TABLE `tb_fee`
ADD COLUMN `waybill_title`  varchar(255) DEFAULT NULL COMMENT '抬头' AFTER `voucher_time`;
ALTER TABLE `tb_fee`
ADD COLUMN `remark`  varchar(255) DEFAULT NULL COMMENT '备注' AFTER `waybill_title`;