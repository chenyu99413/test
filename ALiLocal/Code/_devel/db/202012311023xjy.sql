ALTER TABLE `tb_product`
ADD COLUMN `product_name_far`  varchar(128) NULL DEFAULT '' COMMENT '泛远产品编码' AFTER `is_pda`;