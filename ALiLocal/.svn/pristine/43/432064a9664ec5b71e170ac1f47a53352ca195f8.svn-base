<?php
/**
 * ReturnChannelcostppr 封装来自 tb_return_channel_cost_p_p_r 数据表的记录及领域逻辑
 */
class ReturnChannelcostppr extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_return_channel_cost_p_p_r',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'channel_cost_p_p_r_id' => array (
					'readonly' => true 
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
			
			// 不允许通过构造函数给 channelcost_p_p_r_id 属性赋值
			'attr_protected' => 'channel_cost_p_p_r_id' 
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
class ReturnChannelcostprpException extends QException {}