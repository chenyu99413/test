CREATE TABLE `tb_ems_zip_format` (
  `zip_format_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '邮编格式',
  `country_code_two` varchar(2) DEFAULT NULL COMMENT '国家二字码',
  `zip_format` varchar(100) DEFAULT NULL COMMENT '邮编格式规则',
  `zip_format_preg_match` varchar(255) DEFAULT NULL COMMENT '邮编的正则匹配格式',
  `create_time` int(11) DEFAULT NULL COMMENT '生成时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`zip_format_id`),
  KEY `country_code_two` (`country_code_two`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
