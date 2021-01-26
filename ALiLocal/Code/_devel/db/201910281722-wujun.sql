ALTER TABLE `tb_tracking`
ADD COLUMN `flag` varchar(1) DEFAULT '0' COMMENT '1：无时区忽略,0:人工,2:无匹配' AFTER `send_times`;

ALTER TABLE `tb_tracking`
ADD COLUMN `total_no` varchar(20) DEFAULT NULL COMMENT '总单号' AFTER `flag`;