ALTER TABLE `tb_order`
ADD COLUMN `print_time`  int(11) NULL COMMENT '标签打印时间' AFTER `get_trace_flag`;