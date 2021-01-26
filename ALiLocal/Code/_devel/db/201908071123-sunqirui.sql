ALTER TABLE `tb_zip_code`
ADD COLUMN `warehouse`  varchar(30) NULL COMMENT '仓库代码' AFTER `pick_company`,
ADD COLUMN `service_code`  varchar(30) NULL COMMENT '产品代码' AFTER `warehouse`,
ADD INDEX `warehouse` (`warehouse`) ;