ALTER TABLE `tb_order`
ADD COLUMN `again_time`  int NULL COMMENT '已提取轨迹重查时间' AFTER `dhl_pdf_type`;

