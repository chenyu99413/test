CREATE TABLE `tb_channel_group_department` (
  `available_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '渠道分组部门ID',
  `channel_group_id` int(11) DEFAULT NULL COMMENT '渠道分组ID',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`available_id`),
  KEY `department_id` (`department_id`),
  KEY `product_id` (`channel_group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='渠道分组部门';