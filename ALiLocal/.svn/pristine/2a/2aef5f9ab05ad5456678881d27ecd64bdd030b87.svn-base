ALTER TABLE `tb_order`
ADD COLUMN `send_ali_form`  varchar(1) NULL DEFAULT '' COMMENT '1:代表已发送面单给阿里' AFTER `suspected_remote`,
ADD COLUMN `ali_form_exception_info`  varchar(255) NULL COMMENT '阿里单证审核失败具体信息' AFTER `send_ali_form`;
ALTER TABLE `tb_order`
ADD COLUMN `send_ali_form_error`  varchar(255) NULL COMMENT '发送阿里单证错误信息' AFTER `send_ali_form`;