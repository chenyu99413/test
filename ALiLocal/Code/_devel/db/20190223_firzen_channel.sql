ALTER TABLE `tb_channel` ADD `network_code` VARCHAR(32) NOT NULL COMMENT '网络代码' AFTER `channel_name`;
update `tb_channel` set network_code='UPS'