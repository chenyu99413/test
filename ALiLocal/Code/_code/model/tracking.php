<?php

/**
 * Tracking 封装来自 tb_tracking 数据表的记录及领域逻辑
 */
class Tracking extends QDB_ActiveRecord_Abstract {
	static $trace_code_cn= array(
// 	    'WAREHOUSE_INBOUND'=>'仓库收到包裹',
// 	    'CHECK_WEIGHT'=>'仓内货物查验',
// 	    'CONFIRM'=>'待发件方确认',
// 	    'PALLETIZE'=>'开始准备发货',
// 	    'WAREHOUSE_OUTBOUND'=>'已出库待提取',
// 	    'CARRIER_PICKUP'=>'承运商已取件',
// 	    'DELIVERY'=>'快件已签收',
// 	    'CARRIER_INTRANSMIT'=>'货物中转交航',
        'UNKNOWN'=>'万能自定义',
	    'E_CLEARANCE_FLIGHT'=>'出口清关交航中',
		'S_CLEARANCE_START' => '目的地清关开始',
		'S_CLEARANCE_COMPLETE' => '目的地清关完成',
		'S_TH_IN' => '到达转运中心',
	    'S_TH_ARRANGE'=>'安排下一站转运',
		'S_TH_OUT' => '离开转运中心',
		'S_TH_IN_LAST' => '到达最后投递站',
		'S_DELIVERY_SCHEDULED' => '安排投递',
		'S_DELIVERY_SIGNED' => '快件已签收',
		"F_CARRIER_PICKUP_5033"=>"交货失败，包裹退回到物流商",
		"F_CARRIER_PICKUP_RT_5034"=>"承运商收件失败，包裹退回到物流商",
		"F_CARRIER_PICKUP_RT_5035"=>"包裹重新安排转运",
		"F_CLEARANCE_5037"=>"清关异常",
		"F_CLEARANCE_5038"=>"海关没收或销毁，请联络承运商",
		"F_DELIVERY_5043"=>"需要进一步确认收件人信息；请联络承运商",
		"F_DELIVERY_5044"=>"预约派送",
		"F_DELIVERY_5045"=>"收件人联系不上",
		"F_DELIVERY_5046"=>"收件人拒绝签收",
		"F_DELIVERY_5047"=>"包裹退回到发件⼈",
		"F_DELIVERY_5048"=>"寄件人或收件人通知弃件销毁",
		"F_DELIVERY_5049"=>"快件已转交第三方并由其负责派送",
		"F_DELIVERY_5050"=>"等待收件人支付关税",
		"F_DELIVERY_5051"=>"按照客户要求等待自提",
		"F_DELIVERY_5052"=>"部分签收",
		"F_DELIVERY_5053"=>"快件滞留等待中",
	    "F_CLEARANCE_5054"=>"出口清关延迟:等待放行",
	    "F_CLEARANCE_5055"=>"出口清关延迟:安检、技术或系统等不可抗力因素",
	    "F_CLEARANCE_5056"=>"出口清关延迟:申报信息（如HS，价格等）有问题",
	    "F_CLEARANCE_5057"=>"出口清关延迟:查验",
	    "F_CLEARANCE_5058"=>"出口清关异常:包裹即将退运",
	    "F_CLEARANCE_5059"=>"进口清关延迟:查验",
	    "F_CLEARANCE_5060"=>"进口清关异常:包裹含限制产品",
	    "F_DELIVERY_5061"=>"派送延迟:偏远地址，将延迟派送",
	    "F_DELIVERY_5062"=>"派送异常:包裹放置在失物招领处，将退运",
	    "F_DELIVERY_5063"=>"派送异常:收件人未按约定自提，将退运",
	    "F_DELIVERY_5064"=>"派送异常:货物破损或丢失，在联系发件人",
	    "F_DELIVERY_5065"=>"派送异常:等待发件人退运指令",
	    "F_DELIVERY_5066"=>"派送异常:其它不可预见原因",
	    "F_CHECK_5067"=>"核查异常",
	    "F_PICKUP_5068"=> "提取异常",
	    "F_TH_5069"=>"转运异常,正在解决中"
	);
	static $trace_code_en=array(
// 	    'WAREHOUSE_INBOUND' => 'Arrived at Warehouse',
// 	    'CHECK_WEIGHT' => 'Inspection by Warehouse',
// 	    'CONFIRM' => 'Await Release from Sender',
// 	    'PALLETIZE' => 'Ready for Carrier',
// 	    'WAREHOUSE_OUTBOUND' => 'Departed Warehouse',
// 	    'CARRIER_PICKUP' => 'Pickup Scan',
// 	    'DELIVERY' => 'DELIVERED',
// 	    'CARRIER_INTRANSMIT' => 'In Transit to Export Port',
	    'UNKNOWN'=>'Unknown',
	    'E_CLEARANCE_FLIGHT' => 'Export Custom Clearance Processing and Flight Booking',
		'S_CLEARANCE_START' => 'Clearance completed',
		'S_CLEARANCE_COMPLETE' => 'Export clearance completed',
		'S_TH_IN' => 'Arrived at the transfer hopper',
	    'S_TH_ARRANGE' => '',
		'S_TH_OUT' => 'Depart from the transfer hopper',
		'S_TH_IN_LAST' => 'Arrived at the last transfer hopper',
		'S_DELIVERY_SCHEDULED' => 'Out for delivery',
		'S_DELIVERY_SIGNED' => 'Delivered',
		"F_CARRIER_PICKUP_5033"=>"Carrier failed to pickup the package",
		"F_CARRIER_PICKUP_RT_5034"=>"Returned to logistics company",
		"F_CARRIER_PICKUP_RT_5035"=>"Reschedule transshipment",
		"F_CLEARANCE_5037"=>"Clearance declaration failure",
		"F_CLEARANCE_5038"=>"Please contact Carrier",
		"F_DELIVERY_5043"=>"Address information needed; contact Carrier",
		"F_DELIVERY_5044"=>"Scheduled for delivery as agreed",
		"F_DELIVERY_5045"=>"Delivery attempted; recipient not home",
		"F_DELIVERY_5046"=>"Recipient refused delivery",
		"F_DELIVERY_5047"=>"Returned to shipper",
		"F_DELIVERY_5048"=>"Shipper contacted",
		"F_DELIVERY_5049"=>"Delivery arranged no details expected",
		"F_DELIVERY_5050"=>"Shipment held - Available upon receipt of payment",
		"F_DELIVERY_5051"=>"Awaiting collection by recipient as requested",
		"F_DELIVERY_5052"=>"Partial delivery",
		"F_DELIVERY_5053"=>"Shipment on hold",
	    "F_CLEARANCE_5054"=>"Export clearance delay: Awaiting release from customs",
	    "F_CLEARANCE_5055"=>"Export clearance delay: Aviation security/technical/system etc.force majeure issue",
	    "F_CLEARANCE_5056"=>"Export clearance delay: Declaration information issue (HS code, price, etc.)",
	    "F_CLEARANCE_5057"=>"Emport clearance delay: Inspection",
	    "F_CLEARANCE_5058"=>"Emport clearance failure: Package will return to shipper",
	    "F_CLEARANCE_5059"=>"Import clearance delay: Inspection",
	    "F_CLEARANCE_5060"=>"Import clearance failure: The package contains a restricted/prohibits commodity",
	    "F_DELIVERY_5061"=>"Delivery delay: Remote address, delivery will be delayed",
	    "F_DELIVERY_5062"=>"Delivery failure: The package is placed in the Lost and Found, will return",
	    "F_DELIVERY_5063"=>"Delivery failure: The receiver fails to pick up the package as agreed, will return ",
	    "F_DELIVERY_5064"=>"Delivery failure: Package may be damaged or lost,contact the shipper",
	    "F_DELIVERY_5065"=>"Delivery failure: Awaiting return to sender authorization",
	    "F_DELIVERY_5066"=>"Delivery failure: Other unexpected reason",
	    "F_CHECK_5067"=>"Check failure",
	    "F_PICKUP_5068"=>"Pick up failure",
	    "F_TH_5069"=>"ransfer failure"
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
			'table_name' => 'tb_tracking',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'tracking_id' => array (
					'readonly' => true 
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
			'attr_protected' => 'tracking_id' 
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
class TrackingException extends QException {}