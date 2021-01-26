<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';
class Controller_Remote extends Controller_Abstract {
	/**
	 */
	function actionIndex() {
	}
	
	/**
	 * 偏派表一览
	 */
	function actionSearch() {
	}
	
	/**
	 * 偏派表显示
	 */
	function actionEdit() {
		$remoteManage = RemoteManage::find ( "remote_manage_id = ?", request ( "id" ) )->getOne ();
		
		if (request_is_post ()) {
			if (request ( "remoteManage" )) {
				if (! $this->checkName ( $remoteManage->remote_manage_id, $remoteManage->remote_name )) {
					return $this->_redirectMessage ( "偏派表", "保存失败,偏派名称已被抢注", url ( "remote/edit", array (
						"id" => $remoteManage->remote_manage_id,
						"active_tab" => "0" 
					) ) );
				}
				$remoteManage->changeProps ( request ( "remoteManage" ) );
				$remoteManage->save ();
				return $this->_redirectMessage ( "偏派表", "保存成功", url ( "remote/edit", array (
					"id" => $remoteManage->remote_manage_id,
					"active_tab" => "0" 
				) ) );
			}
		}
		
		$this->_view ["remoteManage"] = $remoteManage;
		$remotes = Remote::find ( "remote_manage_id = ?", request ( "id" ) );
		if (request ( "country_code_two" )) {
			$remotes->where ( "country_code_two = ?", request ( "country_code_two" ) );
		}
		$remotes->order ( "country_code_two,start_postal_code" );
		$remotes->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) );
		$remotes->fetchPagination ( $this->_view ["pagination"] );
		$this->_view ["remotes"] = $remotes->getAll ();
		$this->_view ["tabs"] = $this->createTabs ( $remoteManage );
	}
	
	/**
	 * 保存
	 */
	function actionSave() {
		$remoteManage = RemoteManage::find ( "remote_manage_id = ?", request ( "id" ) )->getOne ();
		if ($remoteManage->isNewRecord ()) {
			exit ();
		}
		
		if (request ( "remote" )) {
			$p = request ( "remote" );
			//判断币种
			$curr = CodeCurrency::find('code=?',$p["currency_code"])->getOne();
			if($curr->isNewRecord()){
				echo "币种不存在";
				exit;
			}
			$remote = Remote::find ( "remote_id = ?", $p ["remote_id"] )->getOne ();
			$remote->remote_manage_id = $remoteManage->remote_manage_id;
			$remote->changeProps ( $p );
			$remote->save ();
			echo ($remote->remote_id);
		}
		exit ();
	}
	
	/**
	 * 删除分区
	 */
	function actionDel() {
		if (request ( "remote_id" )) {
			$remote = Remote::find ( "remote_id = ?", request ( "remote_id" ) )->getOne ();
			$remote->destroy ();
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
			echo ("偏派名称已存在");
		}
		exit ();
	}
	
	/**
	 * 偏派表删除
	 */
	function actionDelete() {
		//偏派表
		$remoteManage = RemoteManage::find ( "remote_manage_id = ?", request ( "id" ) )->getOne ();
		
		//判断偏派表是否存在
		if ($remoteManage->isNewRecord ()) {
			return $this->_redirectMessage ( '偏派表', '该偏派表不存在，无法删除', url ( 'remote/search' ) );
		}
		
		$remoteManage->destroy ();
		return $this->_redirectMessage ( '偏派表删除成功', '', url ( 'remote/search' ) );
	}
	
	/**
	 * 偏派导入
	 */
	function actionImport() {
		set_time_limit ( 0 );
		ini_set("memory_limit", "2G");
		$url = url ( "remote/edit", array (
			"id" => request ( "id" ),
			"active_tab" => "1" 
		) );
		
		$remoteManage = RemoteManage::find ( "remote_manage_id = ?", request ( "id" ) )->getOne ();
		if ($remoteManage->isNewRecord ()) {
			return $this->_redirectMessage ( "偏派导入", "导入失败,产品ID错误", $url );
		}
		
		//读取数据
		$uploader = new Helper_Uploader ();
		$file = $uploader->file ( 'file' ); //获得文件对象
		if ($file->extname () != "xls" && $file->extname () != "xlsx") {
			return $this->_redirectMessage ( "偏派导入", "导入失败,文件类型不正确,请选择 .xls类型的文件", $url );
		}
// 		$data = Helper_Excel::readFile ( Controller_Common::readFile ( $uploader ) )->toHeaderMap ();
		$des_dir = Q::ini ( 'upload_tmp_dir' ); //缓存路径
		$filename = date ( 'YmdHis' ) . 'remoteImport.' . $file->extname ();
		$file_route = $des_dir . DS . $filename;
		$file->move ( $file_route );
		$xls = Helper_Excel::readFile ( $file_route, true );
		$data = $xls->toHeaderMap ();
		//检查内容是否为空或格式是否正确
		if (empty ( $data ) || ! isset ( $data [0] ["二字码"] ) || ! isset ( $data [0] ["起始邮编"] ) || ! isset ( $data [0] ["结束邮编"] ) || ! isset ( $data [0] ["城市"] ) || ! isset ( $data [0] ["最低费用"] ) || ! isset ( $data [0] ["首重重量(kg)"] ) || ! isset ( $data [0] ["首重费用"] ) || ! isset ( $data [0] ["续重单位(kg)"] ) || ! isset ( $data [0] ["续重费用"] )) {
			return $this->_redirectMessage ( "偏派导入", "导入失败,请检查模板是否正确", $url );
		}
		
		//检查国家是否存在
		$checked_code_word2 = array ();
		foreach ( $data as $k => $value ) {
			//判断币种
			$curr = CodeCurrency::find('code=?',trim($value['币种']))->getOne();
			if($curr->isNewRecord()){
				return $this->_redirectMessage ( "偏派导入", "导入失败,币种不存在", $url, 5);
			}
			//二字码转大写
			$data [$k] ["二字码"] = strtoupper ( $data [$k] ["二字码"] );
			$code_word_two = $value ["二字码"];
			//重复国家检查，减少数据库压力
			if (isset ( $checked_code_word2 [$code_word_two] )) {
				continue;
			}
			if (strlen ( $value ["二字码"] ) < 1 || Country::find ( "code_word_two = ?", $value ["二字码"] )->getCount () <= 0) {
				//忽略不存在的国家二字码
				unset ( $data [$k] );
				continue;
			}
			$checked_code_word2 [$data [$k] ["二字码"]] = true;
		}
		
		//保存
		Remote::meta ()->deleteWhere ( "remote_manage_id = ?", $remoteManage->remote_manage_id );
		foreach ( $data as $value ) {
			$remote = new Remote ( array (
				"remote_manage_id" => request ( "id" ),
				"country_code_two" => $value ["二字码"],
				"start_postal_code" => $value ["起始邮编"],
				"end_postal_code" => $value ["结束邮编"],
			    "remote_city" => $value ["城市"],
				"lowest_fee" => $value ["最低费用"],
				"first_weight" => $value ["首重重量(kg)"],
				"first_fee" => $value ["首重费用"],
				"additional_weight" => $value ["续重单位(kg)"],
				"additional_fee" => $value ["续重费用"],
				"currency_code" => $value ["币种"]
			) );
			$remote->save ();
		}
		return $this->_redirectMessage ( "偏派导入", "导入成功", $url );
	}
	
	/**
	 * 产品偏派导出
	 */
	function actionExport() {
		set_time_limit(0);
		ini_set("memory_limit", "2048M");
		$remoteManage = RemoteManage::find ( "remote_manage_id = ?", request ( "id" ) )->getOne ();
		$remote=Remote::find ( "remote_manage_id = ?", $remoteManage->remote_manage_id )->order ( "country_code_two" )->getAll ();
		$header = array (
			"二字码","城市","起始邮编","结束邮编","最低费用","首重重量(kg)","首重费用","续重单位(kg)","续重费用"
		);
		$sheet = array (
			$header
		);
		foreach (  $remote as $value ) {
			$sheet [] =array($value->country_code_two,$value->remote_city,"'". $value->start_postal_code,"'". $value->end_postal_code,$value->lowest_fee,$value->first_weight,$value->first_fee,$value->additional_weight,$value->additional_fee);
		}
		Helper_Excel::array2xls ( $sheet, "偏派表.xls" );
		exit;
	}
	
	/**
	 * 创建标签
	 */
	function createTabs($remoteManage) {
		if ($remoteManage->isNewRecord ()) {
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
					"href" => url ( "remote/edit", array (
						"id" => request ( "id" ),
						"active_tab" => "0" 
					) ) 
				),
				array (
					"id" => "1",
					"title" => "偏派信息",
					"href" => url ( "remote/edit", array (
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
		$count = RemoteManage::find ( "remote_manage_id != ? AND remote_name = ?", $id, $name )->count ( "remote_name" )->getAll ();
		return $count ["row_count"] == 0;
	}
}