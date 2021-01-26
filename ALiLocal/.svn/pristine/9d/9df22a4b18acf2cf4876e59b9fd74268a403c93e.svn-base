ALTER TABLE `tb_channel`
ADD COLUMN `postcode_verify`  tinyint(2) NULL DEFAULT 0 COMMENT '是否验证偏派邮编0：否 1：是' AFTER `print_method`;

DROP TABLE IF EXISTS `tb_channel_zip_code`;
CREATE TABLE `tb_channel_zip_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL COMMENT '主表id',
  `zip_code` varchar(20) DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道偏派邮编表';