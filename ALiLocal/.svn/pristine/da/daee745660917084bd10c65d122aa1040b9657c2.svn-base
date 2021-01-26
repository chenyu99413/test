ALTER TABLE `tb_product`
ADD COLUMN `type` int(2) DEFAULT NULL COMMENT '产品类型:1快件类-UPS,2快件类-DHL,3快件类-FedEx,4EMS类,5小包类',
ADD COLUMN `length` decimal(8,2) DEFAULT NULL COMMENT '最长边限制,单位CM',
ADD COLUMN `width` decimal(8,2) DEFAULT NULL COMMENT '第二长边限制,单位CM',
ADD COLUMN `height` decimal(8,2) DEFAULT NULL COMMENT '高限制,单位CM',
ADD COLUMN `perimeter` decimal(8,2) DEFAULT NULL COMMENT '周长限制,单位CM',
ADD COLUMN `girth` decimal(8,2) DEFAULT NULL COMMENT '围长限制,单位CM',
ADD COLUMN `weight` decimal(8,3) DEFAULT NULL COMMENT '单个包裹实重限制，单位KG',
ADD COLUMN `total_cost_weight` decimal(8,3) DEFAULT NULL COMMENT '整票计费重限制，单位KG',
ADD COLUMN `declare_threshold` decimal(8,2) DEFAULT NULL COMMENT '申报总价限制';

ALTER TABLE `tb_channel`
ADD COLUMN `type` int(2) DEFAULT NULL COMMENT '渠道类型:1快件类-UPS,2快件类-DHL,3快件类-FedEx,4EMS类,5小包类',
ADD COLUMN `forecast_type` int(2) DEFAULT NULL COMMENT '数据预报规则:1向下取整回调,2实重减3g,3以出库原数据',
ADD COLUMN `length` decimal(8,2) DEFAULT NULL COMMENT '最长边限制,单位CM',
ADD COLUMN `width` decimal(8,2) DEFAULT NULL COMMENT '第二长边限制,单位CM',
ADD COLUMN `height` decimal(8,2) DEFAULT NULL COMMENT '高限制,单位CM',
ADD COLUMN `perimeter` decimal(8,2) DEFAULT NULL COMMENT '周长限制,单位CM',
ADD COLUMN `girth` decimal(8,2) DEFAULT NULL COMMENT '围长限制,单位CM',
ADD COLUMN `weight` decimal(8,3) DEFAULT NULL COMMENT '单个包裹实重限制，单位KG',
ADD COLUMN `total_cost_weight` decimal(8,3) DEFAULT NULL COMMENT '整票计费重限制，单位KG';

ALTER TABLE `tb_product_logs`
ADD COLUMN `edit_product_id` int(11) DEFAULT NULL COMMENT '编辑产品ID';

ALTER TABLE `tb_order`
ADD COLUMN `total_out_volumn_weight` decimal(10,3) DEFAULT '0.000' COMMENT '出库的包裹总体积重';