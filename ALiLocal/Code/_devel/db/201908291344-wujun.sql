ALTER TABLE `tb_order`
ADD COLUMN `is_send` int(1) DEFAULT '0' COMMENT '发送快件系统标识，0:未发送，1:已发送' AFTER `customer_id`;