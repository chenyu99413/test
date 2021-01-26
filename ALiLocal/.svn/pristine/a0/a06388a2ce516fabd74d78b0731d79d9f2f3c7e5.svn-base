<?php

/**
 */
class CityTimezone extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_city_timezone',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
			),
			'validations' => array (),
			'create_autofill' => array (
			),
			'update_autofill' => array (
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
	static function match($countryNameOrCodeWordTwo,$cityName=null){
		static $cache;
		$countryNameOrCodeWordTwo=trim($countryNameOrCodeWordTwo);
		$cityName=trim($cityName);
		
		if (strlen($countryNameOrCodeWordTwo)==2){
			$c=Country::find('code_word_two=?',$countryNameOrCodeWordTwo)->getOne();
		}else {
			$c=Country::find('english_name like ?',$countryNameOrCodeWordTwo)->getOne();
		}
		if ($c->isNewRecord()){
			throw new QException('Country mismatch');
		}
		// 查询+缓存
		if (empty($cache[$countryNameOrCodeWordTwo])){
			$tzs=self::find('code_word_two=?',$c->code_word_two)->order('timezone desc')->getAll();
			$cache[$countryNameOrCodeWordTwo]=$tzs;
		}else {
			$tzs=$cache[$countryNameOrCodeWordTwo];
		}
		if (count($tzs)==0){
			throw new QException('No timezones for this country');
		}
		foreach ($tzs as $t){
			if (strtolower($t->city) == strtolower($cityName)){
				return $t->timezone;
			}
		}
		//无法匹配返回时区最大的，保证时间最早
		return $tzs[0]->timezone;
	}
}
