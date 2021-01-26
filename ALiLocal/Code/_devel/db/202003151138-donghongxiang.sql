CREATE TABLE `tb_channel_limitation_amount` (
`limitation_amount_id`  int(11) NOT NULL AUTO_INCREMENT ,
`channel_id` int(11) NULL DEFAULT NULL COMMENT '渠道ID' ,
`cycle`  varchar(1) NULL DEFAULT NULL COMMENT '0:每日；1:每周；2:每月；' ,
`type`  varchar(1) NULL DEFAULT NULL COMMENT '0:票数;1:实重;' ,
`department_id`  int(11) NULL DEFAULT NULL COMMENT '部门id' ,
`max_value`  varchar(20) NULL DEFAULT NULL COMMENT '最大额度' ,
`create_time`  int(11) NULL DEFAULT NULL COMMENT '创建时间' ,
`update_time`  int(11) NULL DEFAULT NULL COMMENT '更新时间' ,
PRIMARY KEY (`limitation_amount_id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限制额度'
;