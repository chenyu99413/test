CREATE TABLE `tb_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '发件人ID',
  `sender_company` varchar(150) DEFAULT NULL COMMENT '发件人公司',
  `comment` longtext,
  `create_time` int(11) DEFAULT NULL,
  `upate_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tb_order`
ADD COLUMN `sender_comment` longtext DEFAULT NULL COMMENT '发件人常用联系信息备注项' AFTER `sender_state_region_code`;