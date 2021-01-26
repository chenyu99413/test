CREATE TABLE `tb_pallet` (
  `pallet_id` int(11) NOT NULL AUTO_INCREMENT,
  `pallet_no` varchar(20) DEFAULT NULL COMMENT '托盘号：T年月日+4位自增',
  `operator` varchar(4) DEFAULT NULL COMMENT '托盘创建人名字',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`pallet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tb_sub_code`
ADD COLUMN `weight`  decimal(8,2) NULL COMMENT '实重' AFTER `sub_code`,
ADD COLUMN `length`  decimal(8,2) NULL COMMENT '长' AFTER `weight`,
ADD COLUMN `width`  decimal(8,2) NULL COMMENT '宽' AFTER `length`,
ADD COLUMN `height`  decimal(8,2) NULL COMMENT '高' AFTER `width`;
ALTER TABLE `tb_sub_code`
ADD COLUMN `pallet_no`  varchar(20) NULL COMMENT '托盘号' AFTER `height`,
ADD INDEX `pallet_no` (`pallet_no`) ;
