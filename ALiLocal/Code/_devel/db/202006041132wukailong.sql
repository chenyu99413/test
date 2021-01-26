DROP TABLE IF EXISTS `tb_pos`;
CREATE TABLE `tb_pos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL COMMENT '部门仓库表id',
  `warehouse_name` varchar(100) DEFAULT NULL COMMENT '仓库名',
  `warehouse_code` varchar(100) DEFAULT NULL COMMENT '仓库代码',
  `warehouse_no` varchar(10) DEFAULT NULL COMMENT '库号',
  `area_code` varchar(20) DEFAULT NULL COMMENT '区号',
  `frame_code` varchar(20) DEFAULT NULL COMMENT '架号',
  `floor_code` varchar(20) DEFAULT NULL COMMENT '层号',
  `tag_code` varchar(20) DEFAULT NULL COMMENT '位号',
  `operation_name` varchar(20) DEFAULT NULL COMMENT '创建人',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='库位表';
