ALTER TABLE `tb_order`
MODIFY COLUMN `order_status`  varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '订单状态：1未入库2已取消3已退货4已支付5已入库6已出库7待发送8已发送9已签收10已查验11待退货12已扣件' AFTER `remarks`,
ADD COLUMN `order_status_copy`  varchar(2) NULL DEFAULT '' COMMENT '扣件时复制订单状态代码' AFTER `payment_time`;