ALTER TABLE `tb_partition`
ADD COLUMN `postal_code` varchar(35) DEFAULT NULL COMMENT '邮编' AFTER `country_code_two`;