CREATE TABLE `tb_product_department_available` (
  `available_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '产品可用部门ID',
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `department_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`available_id`),
  KEY `department_id` (`department_id`),
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='产品可用部门';

ALTER TABLE `tb_department`
ADD COLUMN `level`  int(2) NULL DEFAULT 1 COMMENT '等级' AFTER `update_time`;