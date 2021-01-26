<?php
/**
 * product 封装来自 tb_product 数据表的记录及领域逻辑
 */
class Product extends QDB_ActiveRecord_Abstract {
	static $type= array(
		'1'=>'快件类-UPS',
		'2'=>'快件类-DHL',
		'3'=>'快件类-FedEx',
		'4'=>'EMS类',
		'5'=>'小包类'
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
			'table_name' => 'tb_product',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'product_id' => array (
					'readonly' => true 
				),
			    // 1_产品:1_产品卖家燃油
			    'productfuel' => array (
			        QDB::HAS_MANY => 'Productfuel','source_key' => 'product_id','target_key' => 'product_id','skip_empty' => true
			    ),
			    'network' => array (
			        QDB::BELONGS_TO => 'Network','source_key' => 'network_id','target_key' => 'network_id','skip_empty' => true
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
			
			// 不允许通过构造函数给 product_number 属性赋值
			'attr_protected' => 'product_id' 
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
	function _before_update(){
		$changes=array();
		foreach ($this->changes() as $field){
			if (!in_array($field,array('create_time','update_time'))){
				$desc=$this->getMeta()->table_meta[$field]['desc'];
				if (empty($desc)){
					$desc=$field;
				}
				$changes[]=$desc.': '.$this->_changed_prop_old_values[$field].' > '.$this[$field];
				
			}
		}
		if (!empty($changes)){
			$log=new ProductLog(array(
				'staff_id'=>MyApp::currentUser('staff_id'),
				'staff_name'=>MyApp::currentUser('staff_name'),
				'comment'=>implode(', ',$changes),
				'edit_product_id'=>$this->product_id
			));
			$log->save();
		}
	}
	
	/**
	 * @todo 产品code
	 * @author 许杰晔
	 * @since 2020-9-9
	 * @param $check_complete 需要验证数据完整性 1:是 2:否 3:渠道验证
	 * @return
	 * @link #82427
	 */
	static function getprodutcode($check_complete=1){
		return Helper_Array::getCols(Product::find('check_complete=?',$check_complete)->asArray()->getAll(), 'product_name');
	}
}

class ProductException extends QException {}