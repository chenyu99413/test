<?php

/**
 * 渠道成本
 * ChannelCost 封装来自 tb_channel_cost 数据表的记录及领域逻辑
 */
class ChannelCost extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_channel_cost',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'channel_cost_id' => array (
					'readonly' => true 
				),
				// N_渠道成本:1_渠道
				'product' => array (
					QDB::BELONGS_TO => 'Product',
					'source_key' => 'product_id',
					'target_key' => 'product_id',
					'on_delete' => 'skip' 
				),
				
				// N_渠道成本:1_渠道
				'channel' => array (
					QDB::BELONGS_TO => 'Channel',
					'source_key' => 'channel_id',
					'target_key' => 'channel_id',
					'on_delete' => 'skip' 
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
			'attr_protected' => 'channel_cost_id' 
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
	//公式费用名称列表
	static function getFeename($type_id,$formula_id=NULL,$customs_code){
	    //查询已添加过的费用名称
	    $fee_names=ChannelCostformula::find('type_id=?',$type_id);
	    if($formula_id){
	        $fee_names->where('channel_cost_formula_id!=?',$formula_id);
	    }
	    if ($customs_code=='ALCN'){
	    	$fee_item_name_array=array('运费(ALL IN)');
	    }else{
	    	$fee_item_name_array=array('基础运费','包装-包裹袋','包装-纸箱','燃油附加费','偏远地区附加费','税');
	    }
	    $fee_names=Helper_Array::getCols($fee_names->getAll(), 'fee_name');
	    foreach ( FeeItem::find ('customs_code=?',$customs_code)->getAll () as $value ) {
	        if(!in_array($value->item_name, $fee_item_name_array)){
	            $result [] = array (
	                "id" => $value->item_name,"text" => $value->item_name
	            );
	        }
	    }
	    return $result;
	}
}
class ChannelCostException extends QException {}