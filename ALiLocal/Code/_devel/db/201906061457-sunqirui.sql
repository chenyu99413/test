ALTER TABLE `tb_order`
ADD COLUMN `ems_order_id`  int NULL COMMENT 'EMS response的订单号' AFTER `ali_form_exception_info`,
ADD INDEX `ems_order_id` (`ems_order_id`) ;