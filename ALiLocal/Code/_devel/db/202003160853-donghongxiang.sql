ALTER TABLE `tb_channel_limitation_amount`
ADD COLUMN `used_value`  varchar(20) NULL DEFAULT NULL COMMENT '已用额度' AFTER `department_id`;

ALTER TABLE `tb_channel_limitation_amount`
MODIFY COLUMN `used_value`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 COMMENT '已用额度' AFTER `department_id`,
MODIFY COLUMN `max_value`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 COMMENT '最大额度' AFTER `used_value`;
