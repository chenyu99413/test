ALTER TABLE `tb_order` 
ADD `has_battery_num` INT(2) NOT NULL DEFAULT 0 COMMENT '带电产品数量：1：不超过2个，2：2个以上';