ALTER TABLE `tb_route_matchrules`
ADD COLUMN `sort`  int(10) NULL DEFAULT 0 COMMENT '序号',
ADD COLUMN `is_priority`  int(2) NULL DEFAULT '2' COMMENT '是否优先匹配1：是 2 否';