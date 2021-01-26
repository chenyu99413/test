<?php

class CodeCurrency extends QDB_ActiveRecord_Abstract {
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
			'table_name' => 'tb_code_currency',
				
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'event_id' => array (
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
	function _before_update(){
		$changes=array($this->code);
	    foreach ($this->changes() as $field){
	        if (!in_array($field,array('create_time','update_time'))){
	            $desc=$this->getMeta()->table_meta[$field]['desc'];
	            if (empty($desc)){
	                $desc=$field;
	            }
	            if (in_array($field,array('start_date','end_date'))){
	                if ($this->_changed_prop_old_values[$field]){
	                    $changes[]=$desc.': '.date('Y-m-d',$this->_changed_prop_old_values[$field]).' > '.date('Y-m-d',$this[$field]);
	                }else{
	                    $changes[]=$desc.': > '.date('Y-m-d',$this[$field]);
	                }
	            }else {
	                $changes[]=$desc.': '.$this->_changed_prop_old_values[$field].' > '.$this[$field];
	            }
	            
	        }
	    }
	    if (!empty($changes)){
	        $log=new CodeCurrencyLog(array(
	            'code_id'=>$this->id,
	            'staff_id'=>MyApp::currentUser('staff_id'),
	            'staff_name'=>MyApp::currentUser('staff_name'),
	            'comment'=>implode(', ',$changes)
	        ));
	        $log->save();
	    }
	}
	/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */
	/**
	 * 获取币种列表
	 *
	 * @return multitype:string
	 */
	static function getCurrencyList() {
		$result = array ();
		foreach ( CodeCurrency::find ()->group('code')->getAll () as $value ) {
			$result [] = array (
				"id" => $value->code,"text" => $value->code
			);
		}
		return $result;
	}
}
class CodeCurrencyException extends QException {}