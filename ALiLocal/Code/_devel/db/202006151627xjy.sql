ALTER TABLE `tb_total_check`
MODIFY COLUMN `state`  int(2) NULL DEFAULT 0 COMMENT '状态：0:未核验 1 核对成功 2 有单无货 3 有货无单' AFTER `tracking_no`;
ALTER TABLE `tb_total_list`
ADD COLUMN `sort`  varchar(10) NULL DEFAULT 'D3' COMMENT '航次类型' AFTER `update_time`;

