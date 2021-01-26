ALTER TABLE `tb_fee`
ADD COLUMN `account_date` int(11) DEFAULT NULL COMMENT '登账日' AFTER `remark`;