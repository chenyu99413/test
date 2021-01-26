ALTER TABLE `tb_event`
ADD COLUMN `return_reason`  varchar(255) NULL COMMENT '阿里失败回执' AFTER `send_times`;