CREATE TABLE `tb_channel_country_disabled` (
`disabled_country_id`  int(11) NOT NULL AUTO_INCREMENT ,
`channel_id`  int(11) NULL DEFAULT NULL COMMENT '渠道ID' ,
`country_code_two`  varchar(2) NULL COMMENT '国家二字码' ,
`effect_time`  int(11) NULL DEFAULT NULL COMMENT '生效时间' ,
`failure_time`  int(11) NULL DEFAULT NULL COMMENT '失效时间' ,
`create_time`  int(11) NULL DEFAULT NULL COMMENT '创建时间' ,
`update_time`  int(11) NULL DEFAULT NULL COMMENT '更新时间' ,
PRIMARY KEY (`disabled_country_id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='禁运国家'
;
