ALTER TABLE `tb_order`
MODIFY COLUMN `order_status`  varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '订单状态：1未入库2已取消3已退货4已支付5已入库6已出库7待发送8已发送9已签收10已查验11待退货12已扣件13已结束14已分派15已取件16网点入库17其他' AFTER `remarks`;
ALTER TABLE `tb_order`
MODIFY COLUMN `get_trace_flag`  int(10) NULL DEFAULT 1 COMMENT 'ems、eub订阅trackingmore轨迹，1:未订阅；2已订阅;3已取消' AFTER `specialpackagenum`;


