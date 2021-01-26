ALTER TABLE `tb_order`
ADD COLUMN `iscustomsclearance`  varchar(5) NULL DEFAULT N COMMENT '菜鸟报关状态（Y(正式报关)|N(非正式报关)）' AFTER `total_out_volumn_weight`;
ALTER TABLE `tb_order`
ADD COLUMN `trade_no`  varchar(50) NULL COMMENT '菜鸟信保交易单号' AFTER `iscustomsclearance`;
