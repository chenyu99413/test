ALTER TABLE `tb_pick_up_member`
ADD COLUMN `type`  tinyint(1) NULL DEFAULT 0 COMMENT '是否能进入上传图片页面0:否1：是' AFTER `status`;
