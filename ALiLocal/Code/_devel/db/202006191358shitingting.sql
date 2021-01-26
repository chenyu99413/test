ALTER TABLE `tb_order`
ADD COLUMN `fda_company` varchar(84) DEFAULT NULL COMMENT 'FDA公司名',
ADD COLUMN `fda_address` varchar(128) DEFAULT NULL COMMENT 'FDA地址',
ADD COLUMN `fda_city` varchar(84) DEFAULT NULL COMMENT 'FDA城市',
ADD COLUMN `fda_post_code` varchar(35) DEFAULT NULL COMMENT 'FDA邮编';