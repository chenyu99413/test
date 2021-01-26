DROP TABLE IF EXISTS `tb_abnormal_parcel_file`;
CREATE TABLE `tb_abnormal_parcel_file` (
  `abnormal_parcel_file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `abnormal_parcel_id` int(11) DEFAULT NULL,
  `file_path` text COMMENT '文件路径',
  `file_name` varchar(50) DEFAULT '' COMMENT '文件名',
  `operator` varchar(50) DEFAULT '' COMMENT '操作人',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`abnormal_parcel_file_id`),
  KEY `abnormal_parcel_id` (`abnormal_parcel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;