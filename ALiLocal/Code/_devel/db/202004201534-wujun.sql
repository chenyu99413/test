CREATE TABLE `tb_code_currency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT '' COMMENT '代码',
  `name` varchar(30) DEFAULT '' COMMENT '中文名称',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='币种';


ALTER TABLE `tb_fee`
ADD COLUMN `currency`  varchar(11) NULL DEFAULT 'CNY' COMMENT '币种' AFTER `amount`;

INSERT INTO `tb_code_currency` (`id`, `code`, `name`, `create_time`, `update_time`) VALUES ('1', 'CNY', '人民币', '1587369453', '1587369453');

