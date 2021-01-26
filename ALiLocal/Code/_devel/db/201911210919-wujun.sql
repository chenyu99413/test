ALTER TABLE `tb_channel`
ADD COLUMN `label_sign`  varchar(30) DEFAULT NULL COMMENT '标签标记' AFTER `supplier_id`;