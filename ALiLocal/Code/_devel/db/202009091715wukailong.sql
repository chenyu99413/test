ALTER TABLE `tb_event`
ADD COLUMN `status`  tinyint(2) NULL DEFAULT 0 COMMENT '0:正常1：移除' AFTER `customer_id`;

