ALTER TABLE `tb_customer`
ADD COLUMN `payment_rule`  int(2) NULL DEFAULT NULL COMMENT '订单支付规则:1未支付通知2无支付通知3核查成功',
ADD COLUMN `check_ruleone`  varchar(1) NULL DEFAULT '' COMMENT '收货计费重不超过客重',
ADD COLUMN `check_ruletwo`  varchar(1) NULL DEFAULT '' COMMENT '收货计费重不超过客户预报计费重（用客户预报的长宽高进行计算后的计费重）';
