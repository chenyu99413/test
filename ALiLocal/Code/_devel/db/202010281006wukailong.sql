ALTER TABLE `tb_routes`
ADD COLUMN `rouets_type`  tinyint(2) NULL DEFAULT 0 COMMENT '0:默认 1：已确认 2：移除' AFTER `code`;

