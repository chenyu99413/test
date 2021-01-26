<?php

class CodeCurrencyItem extends QDB_ActiveRecord_Abstract {
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
			'table_name' => 'tb_code_currency_item',
				
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'item_id' => array (
					'readonly' => true
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
			'attr_protected' => 'id'
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
	* @todo 取出供应商相关汇率
	* @author 吴开龙
	* @since 2020-9-14 16:27:29
	* @return rate汇率
	* @link #82553
	 */
	static function getCurrencyRate($code,$date,$supplier_id = '') {
		$curr = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$code,$date,$date)->getOne();
		if($curr->isNewRecord()){
			return false;
		}
		if($supplier_id){
			$item = CodeCurrencyItem::find('oid=? and supplier_id=? and start_date <= ? and end_date >= ?',$curr->id,$supplier_id,$date,$date)->getOne();			
			if(!$item->isNewRecord()){
				return $item->rate;				
			}
		}	
		return $curr->rate;
	}
}
class CodeCurrencyItemException extends QException {}