ALTER TABLE `tb_far_package`
ADD COLUMN `barcode`  varchar(30) NULL DEFAULT '' COMMENT '阿里订单号' AFTER `order_id`;