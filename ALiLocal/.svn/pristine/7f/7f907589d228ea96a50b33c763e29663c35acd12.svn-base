ALTER TABLE `tb_order`
ADD COLUMN `suspected_remote`  varchar(1) NULL COMMENT '\"1\" 代表疑似偏远' AFTER `address_change_info`,
ADD INDEX `suspected_remote` (`suspected_remote`) ;