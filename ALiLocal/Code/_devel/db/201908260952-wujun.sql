ALTER TABLE `tb_order`
ADD COLUMN `total_list_no`  varchar(20) NULL DEFAULT '' COMMENT '总单号' AFTER `warehouse_in_department_id`;