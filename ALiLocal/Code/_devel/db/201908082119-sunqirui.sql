ALTER TABLE `tb_product`
MODIFY COLUMN `remark`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '入库要求' AFTER `update_time`,
ADD COLUMN `confirm_remark`  text NULL COMMENT '核查要求' AFTER `remark`;