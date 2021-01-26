ALTER TABLE `tb_order`
ADD COLUMN `delivery_priority`  varchar(64) NULL COMMENT '发货优先级' AFTER `auto_send_mail_stu`;