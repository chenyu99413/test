ALTER TABLE `tb_order` ADD COLUMN `related_ali_order_no`  varchar(20) NULL COMMENT '关联阿里单号' AFTER `warning_handled`;