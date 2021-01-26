ALTER TABLE `tb_order`
ADD COLUMN `weight_after_optimization`  decimal(10,3) NULL COMMENT '优化后的计费重量' AFTER `profit`,
ADD COLUMN `channel_id_after_optimization`  int(11) NULL COMMENT '优化后的渠道' AFTER `weight_after_optimization`,
ADD COLUMN `amount_after_optimization`  decimal(10,3) NULL COMMENT '优化后的总成本金额' AFTER `channel_id_after_optimization`,
ADD COLUMN `weight_before_optimization`  decimal(10,3) NULL COMMENT '优化前的计费重量' AFTER `amount_after_optimization`,
ADD COLUMN `channel_id_before_optimization`  int(11) NULL COMMENT '优化前的渠道' AFTER `weight_before_optimization`,
ADD COLUMN `amount_before_optimization`  decimal(10,3) NULL COMMENT '优化前的成本金额' AFTER `channel_id_before_optimization`;
ALTER TABLE `tb_order` MODIFY COLUMN `weight_label`  decimal(10,3) NULL DEFAULT NULL COMMENT '标签重，现在修改为预报重' AFTER `weight_actual_out`;
