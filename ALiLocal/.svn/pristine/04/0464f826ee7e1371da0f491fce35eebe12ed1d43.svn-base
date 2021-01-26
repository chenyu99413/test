CREATE TABLE `tb_delivery_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `tracking_id` int(11) DEFAULT NULL,
  `tracking_no` varchar(30) DEFAULT NULL COMMENT '末端物流单号',
  `staff_id` int(11) DEFAULT NULL,
  `staff_name` varchar(32) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `comment` longtext,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='订单签收轨迹日志';

ALTER TABLE `tb_routes`
ADD COLUMN `is_delivery` tinyint(2) DEFAULT '0' COMMENT '0:默认 1：签收轨迹';

ALTER TABLE `tb_order`
ADD COLUMN `is_signunusual` tinyint(2) DEFAULT '0' COMMENT '0:默认 1：签收异常';