ALTER TABLE `tb_order`
ADD INDEX `reference_no` (`reference_no`) USING BTREE ;
ALTER TABLE `tb_event`
MODIFY COLUMN `send_flag`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '1代表已发送给阿里' AFTER `reason`;
ALTER TABLE `tb_tracking`
MODIFY COLUMN `send_flag`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '1代表已发送给阿里' AFTER `status`;

