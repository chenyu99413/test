ALTER TABLE `tb_event`
ADD COLUMN `notice_type`  tinyint(2) NULL DEFAULT 0 COMMENT '是否已通知：0：未通知1：已通知' AFTER `success_time`;

