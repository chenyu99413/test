<?php
/**
 * network 封装来自 tb_network 数据表的记录及领域逻辑
 */
class Network extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_network',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'network_id' => array (
					'readonly' => true 
				),
				
				'networkfuel_rate' => array (
					'getter' => '_baf_getter' 
				),
				'networkfuel' => array (
					QDB::HAS_MANY => 'Networkfuel',
					'source_key' => 'network_id',
					'target_key' => 'network_id' 
				),
				'countryinvoice' => array (
					QDB::HAS_MANY => 'CountryInvoice',
					'source_key' => 'network_id',
					'target_key' => 'network_id'
				) 
			),
			'validations' => array (),
			'create_autofill' => array (
				
				//自动填充修改和创建时间
				'create_time' => self::AUTOFILL_TIMESTAMP,
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			'update_autofill' => array (
				'update_time' => self::AUTOFILL_TIMESTAMP 
			),
			
			// 不允许通过构造函数给 network_number 属性赋值
			'attr_protected' => 'network_id' 
		);
	}
	
	/**
	 * baf Getter 添加值 燃油附加费
	 */
	function _baf_getter() {
		$baf = Networkfuel::find ( "network_id = ? and (effective_date < now()  && (fail_date is null or now() < fail_date))", $this->network_id )->order ( "effective_date DESC" )->getOne ();
		return $baf->rate;
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
	/**
	 * 网络列表
	 *
	 * @return multitype:multitype:string NULL
	 */
	static function networks() {
		$result = array ();
		foreach ( self::find ()->getAll () as $value ) {
			$result [] = array (
				"network_id" => $value->network_id,
				"network_name" => $value->network_name
			);
		}
		return $result;
	}
	
	/**
	 * 网络列表
	 *
	 * @return multitype:multitype:string NULL
	 */
	static function networkCombo($selected) {
		$result = array ();
		foreach ( Network::find ()->getAll () as $value ) {
			$result [] = array (
	
				//部门ID
				"id" => $value->network_code,
	
				//部门简称
				"text" => $value->network_name_en,
	
				//选择项
				"selected" => in_array ( $value->network_code, $selected ) ? true : false
			);
		}
		return $result;
	}
}
class networkException extends QException {}