ALTER TABLE `tb_order`
ADD COLUMN `is_pda`  tinyint(2) NULL COMMENT '是否为PDA类品' AFTER `package_total_in`;

ALTER TABLE `tb_product`
ADD COLUMN `is_pda`  tinyint(2) NULL COMMENT '是否检测pda类品' AFTER `support_one`;

ALTER TABLE `tb_channel`
ADD COLUMN `is_pda`  tinyint(2) NULL COMMENT '是否支持无FDA出货' AFTER `sort_code`;

ALTER TABLE `tb_order`
MODIFY COLUMN `is_pda`  tinyint(2) NULL DEFAULT 0 COMMENT '是否为PDA类品0:否1：是' AFTER `package_total_in`;

ALTER TABLE `tb_product`
MODIFY COLUMN `is_pda`  tinyint(2) NULL DEFAULT 0 COMMENT '是否检测pda类品0：否1：是' AFTER `support_one`;

ALTER TABLE `tb_channel`
MODIFY COLUMN `is_pda`  tinyint(2) NULL DEFAULT 0 COMMENT '是否支持无FDA出货0：否1：是' AFTER `sort_code`;

