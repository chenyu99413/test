<?php

/**
 * Remote 封装来自 tb_remote 数据表的记录及领域逻辑
 */
class Remote extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_remote',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'remote_id' => array (
					'readonly' => true 
				),
				
				// 偏远属于国家
				'country' => array (
					QDB::BELONGS_TO => 'Country',
					'source_key' => 'country_code_two',
					'target_key' => 'code_word_two' 
				) 
			),
			'validations' => array (),
			'create_autofill' => array (
				'create_time' => self::AUTOFILL_TIMESTAMP,
				'update_time' => self::AUTOFILL_TIMESTAMP,
				"modifier" => MyApp::currentUser ( "staff_name" ) 
			),
			'update_autofill' => array (
				'update_time' => self::AUTOFILL_TIMESTAMP,
				"modifier" => MyApp::currentUser ( "staff_name" ) 
			),
			
			// 不允许通过构造函数给 serial_number 属性赋值
			'attr_protected' => 'remote_id' 
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
	 * 获取偏远费
	 *
	 * @param 产品ID $product_id        	
	 * @param 国家二字码 $code_word_two        	
	 * @param 邮编 $postal_code        	
	 */
	static function fee($remote_manage_id, $code_word_two, $postal_code, $weight) {
		if (empty ( $code_word_two )) {
			return null;
		}
		if (empty ( $postal_code )) {
			return 0;
		}
		$post = str_replace(array(' ','-'), '', $postal_code);
		if (is_numeric($post)){
			//$post=intval($post);
			$fee = self::find ( "remote_manage_id = ? and country_code_two = ? and start_postal_code <= ? and end_postal_code >= ?", $remote_manage_id, $code_word_two, $post, $post )->getOne ();
			
		}else {
			//只能针对一部分的情况，不能解决所有问题
			$fee = self::find ( "remote_manage_id = ? and country_code_two = ? and (start_postal_code = ?  or end_postal_code=? )", $remote_manage_id, $code_word_two, $post,$post )->getOne ();
		}
		if ($fee->isNewRecord ()) {
		    $fee = self::find ( "remote_manage_id = ? and country_code_two = ? and left(start_postal_code,".strlen($post).")<=? and left(end_postal_code,".strlen($post).")>=?", $remote_manage_id, $code_word_two, $post, $post )->getOne ();
		    if($fee->isNewRecord ()){
		        $fees = self::find ( "remote_manage_id = ? and country_code_two = ? and ifnull(start_postal_code,'')!=''", $remote_manage_id, $code_word_two)->getAll();
		        if(count($fees)>0){
		            $i=0;
		            foreach ($fees as $fee){
		                $start_postal_code=$fee->start_postal_code;
                        $end_postal_code=$fee->end_postal_code;
                        if(substr($post, 0, strlen($start_postal_code))>=$start_postal_code && substr($post, 0, strlen($end_postal_code))<=$end_postal_code){
                            $i++;
                            break;
                        }
		            }
		            if($i==0){
		                return 0;
		            }
		        }else {
		            return 0;
		        }
		    }	
		}
		// 重量小于首重
		if ($fee->first_weight >= $weight) {
			if ($fee->first_fee >= $fee->lowest) {
				return $fee->first_fee;
			} else {
				return $fee->lowest;
			}
		}
		// 续重单位为空或者为0
		if (! strlen ( $fee->additional_unit_weight ) || $fee->additional_unit_weight == 0) {
			return $fee->lowest>$fee->first_fee?$fee->lowest:$fee->first_fee;
		}
		$ret = round ( $fee->first_fee + ceil ( ($weight - $fee->first_weight) / $fee->additional_unit_weight ) * $fee->additional_fee, 2 );
		if ($ret < $fee->lowest) {
			return $fee->lowest;
		}
		// 封顶值不为空并且小于总费用
		if (strlen ( $fee->capping ) && $fee->capping > 0 && $ret > $fee->capping) {
			return $fee->capping;
		}
		return $ret;
	}
}
class RemoteException extends QException {}
