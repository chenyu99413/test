ALTER TABLE `tb_order`
ADD COLUMN `sender_id`  int(11) NULL DEFAULT NULL COMMENT 'tb_sender表id' AFTER `black_reason`;

