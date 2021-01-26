<?php

/**
 * Abnormalparcel 封装来自 tb_abnormal_parcel 数据表的记录及领域逻辑
 */
class Abnormalparcel extends QDB_ActiveRecord_Abstract {
	/**
	 * 取件异常件
	 */
	CONST PICK_ISSUE=1;
	/**
	 * 库内异常件
	 */
	CONST WAREHOUSE_ISSUE=2;
	/**
	 * 渠道异常件
	 */
	CONST CHANNEL_ISSUE=3;
	/**
	 * 无主件
	 */
	CONST OWN_ISSUE=4;
	/**
	 * 港前异常件
	 */
	CONST BEFOREARRIVE_ISSUE=5;
	
	/**
	 * 返回对象的定义
	 *
	 * @static
	 *
	 * @return array
	 */
	static function __define() {
		return array (
			
			// 用什么数据表保存对象
			'table_name' => 'tb_abnormal_parcel',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'abnormal_parcel_id' => array (
					'readonly' => true 
				),
				'history' => array (
					QDB::HAS_MANY => 'Abnormalparcelhistory','target_key' => 'abnormal_parcel_id',
					'source_key' => 'abnormal_parcel_id','skip_empty' => true,'on_delete'=>'skip'
				),
			    'file' => array (
			        QDB::HAS_MANY => 'Abnormalparcelfile','target_key' => 'abnormal_parcel_id',
			        'source_key' => 'abnormal_parcel_id','skip_empty' => true,'on_delete'=>'skip'
			    ),
				// 1_扣件:1_订单
				'order' => array (
					QDB::BELONGS_TO => 'Order','source_key' => 'ali_order_no','target_key' => 'ali_order_no','skip_empty' => true
				)
			),
			'validations' => array (),
			'create_autofill' => array (
				
				// 自动填充修改和创建时间
				'create_time' => self::AUTOFILL_TIMESTAMP,
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			'update_autofill' => array (
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			'attr_protected' => 'abnormal_parcel_id' 
		);
	}
	
	/**
	 * 开启一个查询，查找符合条件的对象或对象集合
	 *
	 * @static
	 *
	 * @return QDB_Select
	 */
	static function find() {
		$args = func_get_args ();
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ )->findByArgs ( $args );
	}
	
	/**
	 * 返回当前 ActiveRecord 类的元数据对象
	 *
	 * @static
	 *
	 * @return QDB_ActiveRecord_Meta
	 */
	static function meta() {
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ );
	}
	
	/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */
}
class AbnormalparcelException extends QException {}