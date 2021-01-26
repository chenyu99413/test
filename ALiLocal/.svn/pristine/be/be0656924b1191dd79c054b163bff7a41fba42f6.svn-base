<?php

/**
 * Event 封装来自 tb_event 数据表的记录及领域逻辑
 */
class Event extends QDB_ActiveRecord_Abstract {
	static $pl_event=array(
		'SORTING_CENTER_INBOUND_CALLBACK'=>'SORTING_CENTER_INBOUND_CALLBACK:入库',
		'CHECK_WEIGHT_CALLBACK'=>'CHECK_WEIGHT_CALLBACK:称重',
		'CONFIRM_CALLBACK'=>'CONFIRM_CALLBACK:核查',
// 		'PALLETIZE'=>'PALLETIZE:打托',
		'SORTING_CENTER_OUTBOUND_CALLBACK'=>'SORTING_CENTER_OUTBOUND_CALLBACK:出库',
		'SORTING_CENTER_HO_OUT_CALLBACK'=>'SORTING_CENTER_HO_OUT_CALLBACK:承运商取件',
		'LINEHAUL_HO_AIRLINE_CALLBACK'=>'LINEHAUL_HO_AIRLINE_CALLBACK:交航失败',
		'LAST_MILE_GTMS_SIGNED_CALLBACK'=>'LAST_MILE_GTMS_SIGNED_CALLBACK:派送失败',
// 		'LOAD'=>'LOAD:装柜',
// 		'SET_SAIL'=>'SET_SAIL:开船',
// 		'ARRIVAL_PORT'=>'ARRIVAL_PORT:到港'
	);
    
    
    static $s_event=array(
        'WAREHOUSE_INBOUND'=>'WAREHOUSE_INBOUND:入库',
        'CHECK_WEIGHT'=>'CHECK_WEIGHT:称重',
        'CONFIRM'=>'CONFIRM:核查',
        'PALLETIZE'=>'PALLETIZE:打托',
        'WAREHOUSE_OUTBOUND'=>'WAREHOUSE_OUTBOUND:出库',
        'CARRIER_PICKUP'=>'CARRIER_PICKUP:承运商取件',
        'DELIVERY_TO_FLIGHT'=>'DELIVERY_TO_FLIGHT:交航失败',
    	'DELIVERY'=>'DELIVERY:派送失败',
        'LOAD'=>'LOAD:装柜',
        'SET_SAIL'=>'SET_SAIL:开船',
        'ARRIVAL_PORT'=>'ARRIVAL_PORT:到港'
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
			'table_name' => 'tb_event',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'event_id' => array (
					'readonly' => true 
				),
				'order' => array(
					QDB::HAS_ONE => 'Order','target_key' => 'order_id',
					'source_key' => 'order_id','skip_empty' => true,'on_delete'=>'skip'
				),
				'customer' => array (
					QDB::BELONGS_TO => 'customer','source_key' => 'customer_id','target_key' => 'customer_id','skip_empty' => true
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
			'attr_protected' => 'event_id' 
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
	function _after_save(){
		//自动修改订单状态为已签收
		if ($this->send_flag==1 && $this->event_code =='DELIVERY'){
			if(!$this->reason){
				$order=Order::find('order_id =?',$this->order_id)->getOne();
				if ($order->order_status != Order::STATUS_SIGN ){
					$order->order_status = Order::STATUS_SIGN;
					$order->save();
				}
			}
			
		}
	}
	
	static function unicode2Chinese($str)
	{
		return preg_replace_callback("#\\\u([0-9a-f]{4})#i",
			function ($r) {return iconv('UCS-2BE', 'UTF-8', pack('H4', $r[1]));},
			$str);
	}
}
class EventException extends QException {}