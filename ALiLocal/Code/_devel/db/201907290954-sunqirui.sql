ALTER TABLE `tb_order`
ADD COLUMN `fedex_tracking_id_type`  varchar(10) NULL DEFAULT '' COMMENT 'fedex的跟踪ID类型' AFTER `send_times`,
ADD COLUMN `fedex_form_id`  varchar(10) NULL DEFAULT '' COMMENT 'fedex的ID' AFTER `fedex_tracking_id_type`;