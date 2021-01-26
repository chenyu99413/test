ALTER TABLE `tb_order`
ADD COLUMN `auto_send_mail_stu`  tinyint(2) NULL DEFAULT 0 COMMENT '支付超时发送邮件保存状态：0:未发送，1：120小时判断已发送，2：144小时判断已发送';

