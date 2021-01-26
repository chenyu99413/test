<?php
/**
 * 渠道
 * Channel 封装来自 tb_channel 数据表的记录及领域逻辑
 */
class Channel extends QDB_ActiveRecord_Abstract {
	static $method= array(
		'aliups'=>'上海UPS',
		'dhl'=>'DHL',
		'ems'=>'EMS',
		'eub'=>'EUB',
		'fedex'=>'FEDEX',
		'hlt'=>'麦链',
		'hualei'=>'华磊UPS',
		'kingspeed'=>'俄速通',
		'runfeng'=>'润峯',
		'ups'=>'快件UPS',
		'ib'=>'泛远中美专线',
		'abcsp'=>'ABC-SP',
		'shuncheng'=>'上海UPS-YQ'
	);
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
			'table_name' => 'tb_channel',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'channel_id' => array (
					'readonly' => true 
				),
			    'channelgroup' => array (
			        QDB::BELONGS_TO => 'Channelgroup','source_key' => 'channel_group_id','target_key' => 'channel_group_id','skip_empty' => true
			    ),
			    'supplier'=>array(
			        QDB::BELONGS_TO => 'Supplier','source_key' => 'supplier_id','target_key' => 'supplier_id','skip_empty' => true
			    ),
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
			
			// 不允许通过构造函数给 serial_number 属性赋值
			'attr_protected' => 'channel_id' 
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
	 * 渠道列表
	 *
	 * @return multitype:multitype:string NULL
	 */
	static function channelList2($selected = null) {
		$channel = array ();
		$channel ["0"] = "当前渠道";
		foreach ( self::find ()->getAll () as $value ) {
			$channel [$value->channel_id] = $value->channel_name;
		}
		return $channel;
	}
	
	/**
	 * @todo 渠道id
	 * @author 许杰晔
	 * @since 2020-9-9
	 * @param $check_complete 需要验证数据完整性 1:是 2:否 
	 * @return
	 * @link #82427
	 */
	static function channelids($check_complete=1){
		return Helper_Array::getCols(self::find('check_complete=?',$check_complete)->asArray()->getAll(), 'channel_id');
	}
}
class ChannelException extends QException {}