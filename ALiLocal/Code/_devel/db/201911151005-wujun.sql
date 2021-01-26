ALTER TABLE `tb_abnormal_parcel`
ADD COLUMN `reference_no` varchar(200) DEFAULT NULL COMMENT '送货到仓物流单号' AFTER `parcel_flag`;
ALTER TABLE `tb_abnormal_parcel`
ADD COLUMN `location` varchar(60) DEFAULT NULL COMMENT '地点' AFTER `reference_no`;