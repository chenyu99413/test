CREATE TABLE `tb_goods_check` (
  `goods_check_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_check_no` varchar(20) DEFAULT NULL COMMENT '随货单证核查单号',
  `channel_group_id` varchar(11) DEFAULT NULL COMMENT '渠道分组id',
  `department_id` int(11) DEFAULT NULL COMMENT '仓库id',
  `operation_name` varchar(20) DEFAULT NULL COMMENT '操作者姓名',
  `status` varchar(1) DEFAULT '0' COMMENT '状态，0：未完成; 1: 已完成',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`goods_check_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `tb_goods_check_item` (
  `goods_check_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_check_id` int(11) DEFAULT NULL COMMENT '主表id',
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '主单号',
  `status` varchar(1) DEFAULT '1' COMMENT '1:有货无单 2有单无货  3：核对成功',
  PRIMARY KEY (`goods_check_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

