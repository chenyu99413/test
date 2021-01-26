CREATE TABLE `tb_postalbook` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `code_word_two` varchar(2) NOT NULL COMMENT '国家二字码',
  `servicetel` text COMMENT '客服电话',
  `servicesch` text COMMENT '客服作息时间',
  `customtel` text COMMENT '海关电话',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮政通讯录';