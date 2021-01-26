ALTER TABLE `tb_order`
ADD COLUMN `dhl_pdf_type`  tinyint(2) NULL DEFAULT 0 COMMENT 'dhl是否为有纸化：1：无纸化0：有纸化' AFTER `is_pda`;

