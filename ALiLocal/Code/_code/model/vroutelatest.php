<?php
/**
 */
class VRouteLatest extends QDB_ActiveRecord_View {
	
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
			'table_name' => 'v_route_latest',
			'props'=>array(
				'order'=>array(QDB::BELONGS_TO=>'Order','source_key'=>'ali_order_no','target_key'=>'ali_order_no','skip_empty'=>true)	
			),
			
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
	static $country_1=array(//45
	  'MO','TW','HK','KP','KR','JP','PH','KH','MY','MN','TH','SG','ID','VN','AU',
	  'PG','NZ','US','IE','AT','BE','DK','DE','FR','FI','NL','CA','LU','MT','ZA',
	  'NO','PT','SE','CH','ES','GR','IT','GB','PK','LA','BD','NP','LK','TR','IN'
	  );
	static $country_2 = array(//61
	  'AR','AE','PA','BR','BY','PL','RU','CO','CU','GY','CZ','PE','MX','UA','HU',
	  'IL','JO','UY','LB','OM','EG','ET','AZ','EE','BH','BG','BW','BF','CG','CD',
	  'KZ','DJ','GN','GH','GA','QA','KY','CI','KW','HR','KE','LV','RW','RO','MG',
	  'ML','MA','MZ','NE','NG','SN','CY','SA','TN','UZ','UG','SY','IR','IQ','TD','DZ'
	);
}
