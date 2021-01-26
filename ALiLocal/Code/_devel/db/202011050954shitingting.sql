DROP TABLE IF EXISTS `tb_country_invoice`;
CREATE TABLE `tb_country_invoice`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_codes` text NULL DEFAULT NULL COMMENT '国家',
  `network_id` int(11) NULL DEFAULT NULL COMMENT '网络ID',
  `invoice_type` int(11) NULL DEFAULT 0 COMMENT '发票模板1PE/VG,2正本发票',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间', 
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 COMMENT = '国家-发票模板';

ALTER TABLE `tb_order` CHANGE `is_pda` `is_pda` tinyint(2) DEFAULT '0' COMMENT '是否为FDA类品0:否1：是';