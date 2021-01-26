<?php
/**
 * 关账日系统配置
 */
class Config extends QDB_ActiveRecord_Abstract{
	
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
			'table_name' => 'tb_config',
			
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
	static function set($k,$v){
		$r=self::find('k=?',$k)->getOne();
		$r->k=$k;
		$r->v=$v;
		return $r->save();
	}
	static function get($k,$default=null){
		$r=self::find('k=?',$k)->getOne();
		if ($r->isNewRecord()){
			return $default;
		}
		return $r->v;
	}
	/**
	 * 返回是否已经超过关账日
	 * @return bool
	 */
	static function closeBalance(){
		return self::get('closeBalanceDay') < date('d');
	}
	/**
	 * 返回已关账的日期
	 * @return Y-m-d
	 */
	static function cbDate(){
		if (self::closeBalance()){
			return date('Y-m-d',strtotime('first day of this month'));
		}else {
			return date('Y-m-d',strtotime('first day of last month'));
		}
	}
}

?>