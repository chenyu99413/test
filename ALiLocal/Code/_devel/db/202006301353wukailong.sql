ALTER TABLE `tb_noserivce_zipcode`
MODIFY COLUMN `zip_code`  varchar(9) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '邮编' AFTER `zip_code_id`,
MODIFY COLUMN `city`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '城市' AFTER `zip_code`;
