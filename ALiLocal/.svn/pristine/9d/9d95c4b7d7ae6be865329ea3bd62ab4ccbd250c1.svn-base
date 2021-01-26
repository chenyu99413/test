ALTER TABLE `tb_order`
MODIFY COLUMN `department_id`  int(11) NULL DEFAULT NULL COMMENT '部门ID,根据阿里仓库分配的部门' AFTER `tracking_no`,
ADD COLUMN `warehouse_in_department_id`  int NULL COMMENT '订单入库部门' AFTER `fedex_form_id`,
ADD INDEX `warehouse_in_department_id` (`warehouse_in_department_id`) ;
ALTER TABLE `tb_product`
ADD COLUMN `remark`  text NULL COMMENT '产品备注' AFTER `update_time`;
