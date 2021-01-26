ALTER TABLE `tb_order`
ADD COLUMN `pick_up_time`  int(11) NULL DEFAULT NULL COMMENT '取件时间' AFTER `print_time`;

CREATE TABLE `tb_pick_up_member` (
`id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'id' ,
`wechat_id`  varchar(40) NULL DEFAULT NULL COMMENT '微信ID' ,
`wechat_no`  varchar(40) NULL DEFAULT NULL COMMENT '微信号' ,
`name`  varchar(40) NULL DEFAULT NULL COMMENT '姓名' ,
`img_url`  varchar(300) NULL DEFAULT NULL COMMENT '头像url' ,
`gender`  varchar(10) NULL DEFAULT '' COMMENT '性别' ,
`status`  varchar(1) NULL DEFAULT NULL COMMENT '0:未认证  1:已认证' ,
`create_time`  int(11) NULL DEFAULT NULL COMMENT '创建时间' ,
`update_time`  int(11) NULL DEFAULT NULL COMMENT '修改时间' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='取件员'
;