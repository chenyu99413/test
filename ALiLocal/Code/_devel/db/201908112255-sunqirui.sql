ALTER TABLE `tb_product`
ADD COLUMN `check_fuel`  varchar(1) NULL DEFAULT '' COMMENT '检查应收燃油标记，如果是1，入库的时候要优先检查product里面有没有设置燃油' AFTER `confirm_remark`;