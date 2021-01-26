<?php

/**
 * 燃油附加费率
 * Networkfuel 封装来自 tb_network_fuel 数据表的记录及领域逻辑
 */
class Networkfuel extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_network_fuel',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'baf_id' => array (
					'readonly' => true 
				),
				
				// 1_网络:1_燃油附加费率
				'network' => array (
					QDB::BELONGS_TO => 'Network',
					'source_key' => 'network_id',
					'target_key' => 'network_id' 
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
			
			// 不允许通过构造函数给 serial_number 属性赋值
			'attr_protected' => 'baf_id' 
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
	
	/**
	 * 获取燃油附加费率
	 *
	 * @param 网络ID $network_id        	
	 */
	static function rates($network_id, $date = null) {
		//日期
		if ($date == null) {
			$date = Helper_Util::strDate ( "Y-m-d", time () );
		}
		//燃油附加费率
		$baf = self::find ( "network_id = ? and effective_date <= ? and fail_date >= ?", $network_id, $date, $date )->order ( "baf_id DESC" )->getOne ();
		if ($baf->isNewRecord ()) {
			return 0;
		}
		return $baf->price_rates;
	}
	static function allBaf(){
		$rows= self::find()->asArray()->getAll();
		$ret=array();
		foreach ($rows as $r){
			$ret[$r['network_id']][]=array(
				'price_rates'=>$r['price_rates'],
				'effective_date'=>strtotime($r['effective_date']),
				'fail_date'=>strtotime($r['fail_date']),
			);
		}
		return $ret;
	}
}
class NetworkfuelException extends QException {}
