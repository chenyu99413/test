ALTER TABLE `tb_supplier`
ADD COLUMN `contract_date` datetime DEFAULT NULL COMMENT '合同签订' AFTER `supplier`;
ALTER TABLE `tb_supplier`
ADD COLUMN `contract_expiration_date` datetime DEFAULT NULL COMMENT '合同到期' AFTER `contract_date`;
ALTER TABLE `tb_supplier`
ADD COLUMN `contract_code` varchar(50) DEFAULT NULL COMMENT '合同号' AFTER `contract_date`;
ALTER TABLE `tb_supplier`
ADD COLUMN `status` varchar(1) NOT NULL COMMENT '状态' AFTER `contract_code`;