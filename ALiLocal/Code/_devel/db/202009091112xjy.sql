ALTER TABLE `tb_product`
ADD COLUMN `check_complete`  int(2) NULL DEFAULT 1 COMMENT '验证数据是否完整' AFTER `declare_threshold`;
ALTER TABLE `tb_product`
MODIFY COLUMN `check_complete`  int(2) NULL DEFAULT 1 COMMENT '验证数据是否完整  1：是 2 ：否 3：渠道验证' AFTER `declare_threshold`;
ALTER TABLE `tb_channel`
ADD COLUMN `check_complete`  int(2) NULL DEFAULT 1 COMMENT '验证数据是否完整' AFTER `total_cost_weight`;
ALTER TABLE `tb_channel`
MODIFY COLUMN `check_complete`  int(2) NULL DEFAULT 1 COMMENT '验证数据是否完整  1：是 2 否' AFTER `total_cost_weight`;