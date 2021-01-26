ALTER TABLE `tb_order`
ADD COLUMN `far_warehouse_in_time`  int NULL COMMENT '泛远入库时间' AFTER `delivery_time`,
ADD COLUMN `far_warehouse_in_operator`  varchar(10) NULL COMMENT '泛远入库操作人' AFTER `far_warehouse_in_time`;