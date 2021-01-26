ALTER TABLE `tb_total_out`
DROP COLUMN `del_type`,
ADD COLUMN `type`  tinyint NULL DEFAULT 0 COMMENT '是否确认0：否 1：是' AFTER `status`,
ADD COLUMN `update_type_name`  varchar(50) NULL COMMENT '修改类型用户' AFTER `type`,
ADD COLUMN `update_type_time`  int NULL COMMENT '修改类型时间' AFTER `update_type_name`;
