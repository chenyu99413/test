ALTER TABLE `tb_channel_limitation_amount`
MODIFY COLUMN `type`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '0:票数;1:实重;2:计费重' AFTER `cycle`;