CREATE TABLE `tb_channel_group` (
  `channel_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_group_name` varchar(20) DEFAULT NULL COMMENT '渠道分组名',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`channel_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道分组'; 
ALTER TABLE `tb_channel`
ADD COLUMN `channel_group_id`  int NULL COMMENT '渠道分组ID' AFTER `network_code`,
ADD INDEX `channel_group_id` (`channel_group_id`) ;