CREATE TABLE `tb_config` (
  `k` varchar(32) NOT NULL,
  `v` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`k`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
