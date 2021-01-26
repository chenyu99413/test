ALTER TABLE `tb_channel_limitation_amount`
ADD COLUMN `effect_time`  int(11) NULL COMMENT '生效时间' AFTER `max_value`,
ADD COLUMN `failure_time`  int(11) NULL COMMENT '失效时间' AFTER `effect_time`,
ADD COLUMN `country_group_id`  int(11) NULL COMMENT '国家组id' AFTER `failure_time`;