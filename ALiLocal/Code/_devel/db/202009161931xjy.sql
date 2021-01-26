ALTER TABLE `tb_product`
ADD COLUMN `is_declaration`  int(2) NOT NULL DEFAULT 2 COMMENT '是否支持报关 1：是 2 否' AFTER `check_complete`;
ALTER TABLE `tb_product`
ADD COLUMN `support_one`  int(2) NULL DEFAULT 2 COMMENT '是否支持一票多件1：是 2 否' AFTER `is_declaration`;

