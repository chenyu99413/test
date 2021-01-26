ALTER TABLE `tb_return`
MODIFY COLUMN `return_status`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '退件类型：\"1\"已全退，\"2\"已部分退' AFTER `return_no`,
ADD COLUMN `state`  varchar(1) NULL DEFAULT '1' COMMENT '状态：1:待退，2：已退' AFTER `express_company`,
ADD COLUMN `remark`  text NULL COMMENT '备注' AFTER `state`;