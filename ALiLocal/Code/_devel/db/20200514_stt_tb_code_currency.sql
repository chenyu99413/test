ALTER TABLE `tb_code_currency` CHANGE `start_date` `start_date` INT(11) NOT NULL COMMENT '汇率设置开始时间';
ALTER TABLE `tb_code_currency` ADD `end_date` INT(11) NOT NULL COMMENT '汇率设置结束时间' AFTER `start_date`;
DROP TABLE IF EXISTS `tb_code_currency_logs`;
CREATE TABLE `tb_code_currency_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `staff_name` varchar(32) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `comment` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='币种修改日志' ROW_FORMAT=COMPACT;
