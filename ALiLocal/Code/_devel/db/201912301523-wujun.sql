ALTER TABLE `tb_abnormal_parcel_history`
ADD COLUMN `is_mail`  int(2) NULL DEFAULT 0 COMMENT '0不是e-mail;1是e-mail' AFTER `follow_up_operator`;