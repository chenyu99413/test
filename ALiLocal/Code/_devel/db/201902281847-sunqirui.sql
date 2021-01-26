ALTER TABLE `tb_order`
ADD COLUMN `warehouse_confirm_time`  int NULL COMMENT '核查时间' AFTER `warehouse_in_time`,
ADD COLUMN `warehouse_out_time`  int NULL COMMENT '出库时间' AFTER `warehouse_confirm_time`,
ADD COLUMN `carrier_pick_time`  int NULL COMMENT '承运商取件时间' AFTER `warehouse_out_time`,
ADD COLUMN `delivery_time`  int NULL COMMENT '签收时间' AFTER `carrier_pick_time`;
