ALTER TABLE `tb_channel`
ADD COLUMN `overtime`  int NULL DEFAULT 0 COMMENT '记录超时个数，超过10个发邮件' AFTER `channel_status`;

