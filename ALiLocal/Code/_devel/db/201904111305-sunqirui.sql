ALTER TABLE `tb_order` ADD COLUMN `warning_handled`  varchar(1) NULL DEFAULT '0' COMMENT '0代表没有处理，1代表已处理' AFTER `remark`;
ALTER TABLE `tb_order` ADD INDEX `warning_handled` (`warning_handled`) ;