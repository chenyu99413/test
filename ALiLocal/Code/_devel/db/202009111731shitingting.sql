ALTER TABLE `tb_customer`
ADD COLUMN `customer_sign`  varchar(100) NULL COMMENT '客户密钥',
ADD COLUMN `sender_mobile` varchar(32) DEFAULT NULL COMMENT '发件联系人手机，[国家码-]电话，必填',
ADD COLUMN `sender_telephone` varchar(32) DEFAULT NULL COMMENT '发件联系人电话：[国家码-][地区码-]电话',
ADD COLUMN `sender_email` varchar(64) DEFAULT NULL COMMENT '发件联系人邮箱',
ADD COLUMN `sender_name1` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称1，必填',
ADD COLUMN `sender_name2` varchar(64) DEFAULT NULL COMMENT '公司名或客户名称2',
ADD COLUMN `sender_street1` varchar(128) DEFAULT NULL COMMENT '街道名或⻔牌号，必填',
ADD COLUMN `sender_street2` varchar(128) DEFAULT NULL COMMENT '街道名',
ADD COLUMN `sender_country_code` varchar(2) DEFAULT NULL COMMENT '发件人国家二字码，必填',
ADD COLUMN `sender_city` varchar(32) DEFAULT NULL COMMENT '发件城市，必填',
ADD COLUMN `sender_postal_code` varchar(35) DEFAULT NULL COMMENT '发件人邮编',
ADD COLUMN `sender_state_region_code` varchar(40) DEFAULT NULL COMMENT '发件人政区/洲';

ALTER TABLE `tb_network`
ADD COLUMN `trace_url` text COMMENT '查询轨迹网址';


ALTER TABLE `tb_order`
ADD COLUMN `package_total_num`  int(11) DEFAULT NULL COMMENT '包裹总件数' AFTER `weight_actual_ali`;

ALTER TABLE `tb_code_transport`
ADD COLUMN `book_type`  int(2) NULL DEFAULT 0 COMMENT '预报方式0预报1预报打单';
