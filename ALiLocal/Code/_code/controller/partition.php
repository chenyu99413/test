<?php
class Controller_Partition extends Controller_Abstract {
	
	/**
	 * 分区表一览
	 */
	function actionSearch() {
	}
	
	/**
	 * 分区表显示
	 */
	function actionEdit() {
		$partitionManage = PartitionManage::find ( "partition_manage_id = ?", request ( "id" ) )->getOne ();
		if (request_is_post ()) {
			if (request ( "partitionManage" )) {
				if (! $this->checkName ( $partitionManage->partition_manage_id, $partitionManage->partition_name )) {
					return $this->_redirectMessage ( "分区表", "保存失败,分区名称已被抢注", url ( "partition/edit", array (
						"id" => $partitionManage->partition_manage_id,
						"active_tab" => "0" 
					) ) );
				}
				$partitionManage->changeProps ( request ( "partitionManage" ) );
				$partitionManage->save ();
				return $this->_redirectMessage ( "分区表", "保存成功", url ( "partition/edit", array (
					"id" => $partitionManage->partition_manage_id,
					"active_tab" => "0" 
				) ) );
			}
		}
		$this->_view ["tabs"] = $this->createTabs ( $partitionManage );
		$this->_view ["partitionManage"] = $partitionManage;
		$this->_view ["partitions"] = Partition::find ( "partition_manage_id = ?", request ( "id" ) )->getAll ();
	}
	
	/**
	 * 保存
	 */
	function actionSave() {
		$partitionManage = PartitionManage::find ( "partition_manage_id = ?", request ( "id" ) )->getOne ();
		if ($partitionManage->isNewRecord ()) {
			exit ();
		}
		
		if (request ( "partition" )) {
			$p = request ( "partition" );
			$partition = Partition::find ( "partition_id = ?", $p ["partition_id"] )->getOne ();
			$partition->partition_manage_id = $partitionManage->partition_manage_id;
			$partition->changeProps ( $p );
			$partition->save ();
			echo ($partition->partition_id);
		}
		exit ();
	}
	
	/**
	 * 删除分区
	 */
	function actionDel() {
		if (request ( "partition_id" )) {
			$partition = Partition::find ( "partition_id = ?", request ( "partition_id" ) )->getOne ();
			$partition->destroy ();
		}
		exit ();
	}
	
	/**
	 * 验证名称
	 */
	function actionCheckname() {
		if ($this->checkName ( request ( "id" ), request ( "name" ) )) {
			echo ("true");
		} else {
			echo ("分区名称已存在");
		}
		exit ();
	}
	
	/**
	 * 分区表删除
	 */
	function actionDelete() {
		//分区表
		$partitionManage = PartitionManage::find ( "partition_manage_id = ?", request ( "id" ) )->getOne ();
		
		//判断分区表是否存在
		if ($partitionManage->isNewRecord ()) {
			return $this->_redirectMessage ( '分区表', '该分区表不存在，无法删除', url ( 'partition/search' ) );
		}
		
		$partitionManage->destroy ();
		return $this->_redirectMessage ( '分区表删除成功', '', url ( 'partition/search' ) );
	}
	
	/**
	 * 分区导入
	 *
	 * @return QView_Render_PHP
	 */
	function actionImport() {
		$url = url ( "partition/edit", array (
			"id" => request ( "id" ),
			"active_tab" => "2" 
		) );
		
		$partitionManage = PartitionManage::find ( "partition_manage_id = ?", request ( "id" ) )->getOne ();
		//判断产品是否存在
		if ($partitionManage->isNewRecord ()) {
			return $this->_redirectMessage ( "分区导入", "导入失败,分区管理ID错误", $url );
		}
		
		//读取数据
		$uploader = new Helper_Uploader ();
		if (Controller_Common::getFileExtName ( $uploader ) != "xls") {
			return $this->_redirectMessage ( "分区导入", "导入失败,文件类型不正确,请选择 .xls 类型的文件", $url );
		}
		$data = Helper_Excel::readFile ( Controller_Common::readFile ( $uploader ) )->toHeaderMap ();
		
		//检查内容是否为空或格式是否正确
		if (empty ( $data ) || ! isset ( $data [0] ["二字码"] ) || ! isset ( $data [0] ["分区号"] )) {
			return $this->_redirectMessage ( "分区导入", "导入失败,请检查模板是否正确", $url );
		}
		
		//二字码转大写
		for($i = 0; $i < count ( $data ); $i ++) {
			if (empty ( $data [$i] ['二字码'] )) {
				unset ( $data [$i] );
			} else {
				$data [$i] ["二字码"] = strtoupper ( $data [$i] ["二字码"] );
			}
		}
		
		//检查重复
// 		$group = Helper_Array::groupBy ( $data, "二字码" );
// 		foreach ( $group as $key => $value ) {
// 			if (count ( $value ) > 1 && strlen ( $key )) {
// 				return $this->_redirectMessage ( "分区导入", "导入失败,国家二字码  '" . $key . "'  重复", $url );
// 			}
// 		}
		
		//检查分区号是否正确
		$group = Helper_Array::groupBy ( $data, "分区号" );
		foreach ( $group as $key => $value ) {
			if (! empty ( $value ["分区号"] ) && ! is_int ( $key )) {
				return $this->_redirectMessage ( "分区导入", "导入失败,分区号  '" . $key . "'  不正确", $url );
			}
		}
		
		//检查国家是否存在
		foreach ( $data as $value ) {
			$code_word_two = $value ["二字码"];
			if (empty ( $code_word_two )) {
				continue;
			}
			if (Country::find ( "code_word_two = ?", $value ["二字码"] )->getCount () <= 0) {
				return $this->_redirectMessage ( "分区导入", "导入失败,国家二字码  '" . $code_word_two . "'  不存在", $url );
			}
		}
		
		//保存
		$conn = QDB::getConn ();
		$conn->startTrans ();
		Partition::meta ()->deleteWhere ( "partition_manage_id = ?", $partitionManage->partition_manage_id );
		foreach ( $data as $value ) {
			if (empty ( $value ["分区号"] )) {
				continue;
			}
			//分区号
			if (intval ( $value ["分区号"] ) != $value ["分区号"] || $value ["分区号"] <= 0) {
				return $this->_redirectMessage ( "分区导入", "导入失败,分区号 " . $value ["分区号"] . " 不正确", $url );
			}
			
			$partition = new Partition ( array (
				"partition_manage_id" => $partitionManage->partition_manage_id,
			    "postal_code" => $value["邮编"],
				"country_code_two" => $value ["二字码"],
				"partition_code" => $value ["分区号"],
			) );
			$partition->save ();
		}
		$conn->completeTrans ();
		return $this->_redirectMessage ( "分区导入", "导入成功", $url );
	}
	
	/**
	 * 创建标签
	 */
	function createTabs($partitionManage) {
		if ($partitionManage->isNewRecord ()) {
			return array (
				array (
					"id" => "0",
					"title" => "基本信息",
					"href" => "" 
				) 
			);
		} else {
			return array (
				array (
					"id" => "0",
					"title" => "基本信息",
					"href" => url ( "partition/edit", array (
						"id" => request ( "id" ),
						"active_tab" => "0" 
					) ) 
				),
				array (
					"id" => "1",
					"title" => "分区信息",
					"href" => url ( "partition/edit", array (
						"id" => request ( "id" ),
						"active_tab" => "1" 
					) ) 
				) 
			);
		}
	}
	
	/**
	 * 验证名称
	 */
	function checkName($id, $name) {
		$count = PartitionManage::find ( "partition_manage_id != ? AND partition_name = ?", $id, $name )->count ( "partition_name" )->getAll ();
		return $count ["row_count"] == 0;
	}
	/**
	 * 检查国家二字码是否存在
	 *
	 * @return boolean
	 */
	function actionCheckcountryexist() {
	    $flag='true';
	    $countrys = explode ( ",", request ( "code" ) );
	    foreach ( $countrys as $value ) {
	        $country = Country::find ( "code_word_two = ?", $value )->getOne ();
	        if ($country->isNewRecord ()) {
	            $flag='false';
	        }
	    }
	    echo $flag;
	    exit ();
	}
}