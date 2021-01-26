ALTER TABLE `tb_order` ADD COLUMN `address_change`  varchar(1) NULL COMMENT '\"1\" 代表更改了地址，需要收更改地址费' AFTER `amount_before_optimization`;
