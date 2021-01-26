<?php

/**
 * User 封装来自 tb_user 数据表的记录及领域逻辑
 */
class Staff extends QDB_ActiveRecord_Abstract {
	
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
			'table_name' => 'tb_staff',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
				'staff_id' => array (
					'readonly' => true 
				),
			    "password" => array (
			        'setter' => '_password_setter'
			    ),
// 				N_用户:1_部门
				'department' => array (
					QDB::BELONGS_TO => 'Department',
					'source_key' => 'department_id',
					'target_key' => 'department_id',
					'skip_empty' => true 
				),
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
			'attr_protected' => 'staff_id' 
		);
	}
	
	/**
	 * password Setter 修改模型password值自动加密
	 *
	 * @param string $val        	
	 */
	function _password_setter($val) {
		$this->_props ['password'] = self::passwordEncode ( $val );
		$this->willChanged ( 'password' );
	}
	
	/**
	 * 密码加密实现函数
	 *
	 * @param string $val        	
	 * @return string
	 */
	static function passwordEncode($val) {
		return md5 ( $val );
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
	 * 检查指定的密码是否与当前用户的密码相符
	 *
	 * @param string $password        	
	 * @return boolean
	 */
	function checkPassword($password) {
		return self::passwordEncode ( $password ) == $this->password;
	}
	
	/**
	 * 获取权限列表
	 *
	 * @param unknown $user_id        	
	 */
	static function purviews($staff_id) {
		static $cache;
		$result=array();
		if ($staff_id != null && !isset($cache[$staff_id])) {
			$roles = Helper_Array::getCols ( StaffRole::find ( "staff_id = ?", $staff_id )->getAll (), "role_id" );
			if (! empty ( $roles )) $result = Helper_Array::getCols ( RolePurview::find ( "role_id in (?)", $roles )->group ( "purview_path" )->getAll (), "purview_path" );
			$cache[$staff_id]=$result;
		}
		return $cache[$staff_id];
	}
}
class StaffException extends QException {}