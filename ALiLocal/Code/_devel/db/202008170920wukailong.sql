ALTER TABLE `tb_total_out`
ADD COLUMN `del_type`  tinyint NULL DEFAULT 0 COMMENT '删除状态：0：正常，1：待删除' AFTER `status`;

