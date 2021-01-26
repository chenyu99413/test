ALTER TABLE `tb_fee`
ADD COLUMN `recon_state`  int(2) NULL DEFAULT 0 COMMENT '对账状态 0：未对账 1：已对账' AFTER `update_time`;