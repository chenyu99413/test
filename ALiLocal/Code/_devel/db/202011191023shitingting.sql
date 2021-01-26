ALTER TABLE `tb_order`
ADD COLUMN `is_picture` int(2) NULL DEFAULT 0 COMMENT '默认0未上传照片，1快手订单需要验证照片，2已上传' AFTER `again_time`,
ADD INDEX `is_picture` (`is_picture`) USING BTREE ;