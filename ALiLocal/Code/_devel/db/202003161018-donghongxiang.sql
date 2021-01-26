CREATE TABLE `tb_country_group` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(20) NULL DEFAULT NULL COMMENT '国家组名称' ,
`country_codes`  varchar(200) NULL DEFAULT NULL COMMENT '国家二字码' ,
`create_time`  int(11) NULL DEFAULT NULL ,
`update_time`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='国家组表'
;