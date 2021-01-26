<?php
class Controller_Product extends Controller_Abstract {
	/**
	 */
	function actionIndex() {
	}
	
	/**
	 * 产品一览
	 */
	function actionSearch() {
		$select = Product::find ()->order ( 'product_name' );
		$this->_view ['products'] = $select->getAll ();
	}
	
	/**
	 * 产品显示
	 */
	function actionEdit() {
		//产品信息
		$product = Product::find ( 'product_id = ?', request ( "id" ) )->getOne ();
		
		//产品保存
		if (request_is_post ()) {
			$conn = QDB::getConn ();
			$conn->startTrans ();
			//产品
			if (request ( "product" ) != null || strlen ( request ( "product" ) ) > 0) {
				$product->changeProps ( request ( "product" ) );
				$product->save ();
			}
			//产品-价格-分区-偏派列表
			if (request ( "productppr" ) != null || strlen ( request ( "productppr" ) ) > 0) {
				Productppr::meta ()->destroyWhere ( "product_id = ?", $product->product_id );
				foreach ( json_decode ( request ( "productppr" ) ) as $value ) {
					$productprp = new Productppr ();
					$value->effective_time=strtotime($value->effective_time."00:00:00");
					$value->invalid_time=strtotime($value->invalid_time."23:59:59");
					$productprp->changeProps ( $value );
					$productprp->product_id=$product->product_id;
					$productprp->save ();
				}
			}
			
			//保存可用部门
			Productdepartmentavailable::find('product_id=?',$product->product_id)->getAll()->destroy();
			foreach (explode(',', request('department_hidden')) as $department_id){
				if(!$department_id){
					continue;
				}
				$available= new Productdepartmentavailable();
				$available->changeProps(array(
					'product_id'=>$product->product_id,
					'department_id'=>$department_id
				));
				$available->save();
			}
			
			$conn->completeTrans ();
			return $this->_redirectMessage ( "产品信息", "保存成功", url ( "product/edit", array (
				"id" => $product->id ()
			) ) );
		}
		$type = request('type')?request('type'):'BOX';
		$customs_code = request('customs_code')?request('customs_code'):'FARA00001';
		$receivable_formulas = Receivableformula::find('product_id=? and package_type=? and customs_code=?',$product->product_id,$type,$customs_code)->getAll();
		$this->_view ["tabs"] = $this::createTabs ( $product );
		$this->_view ["tabs_customer"] = $this->createTabsCustomerId ( $product );
		$this->_view ["tabs_type"] = $this->createTabsType ( $product );
		$this->_view ["receivable_formulas"] = $receivable_formulas;
		$this->_view ["networks"] = Network::networks ();
		$this->_view ["product"] = $product;
		
		$available_department=Productdepartmentavailable::find('product_id=?',$product->product_id)->getAll();
		$this->_view ["department"] = array (
			"state" => true,
			"checked" => implode ( ",", Helper_Array::getCols ( $available_department, "department_id" ) )
		);
	}
	
	/**
	 * 费用公式保存
	 */
	function actionsaveoperate(){
		//保存
		if (request_is_post ()) {
			//操作费
			if (request ( "formula" ) != null || strlen ( request ( "formula" ) ) > 0) {
				$value=json_decode ( request ( "formula" ),true);
				$formula =  Receivableformula::find("receivable_formula_id=?",$value[0]["receivable_formula_id"])->getOne();
				if (request ( "delete_flag" ) == "true") {
					if (! $formula->isNewRecord ()) {
						$formula->destroy ();
					}
				}else{
					//编辑或新增的生效日期
					$start_time = strtotime($value[0]['effective_time']."00:00:00");
					//编辑或新增的失效日期
					$end_time = strtotime($value[0]['fail_time']."23:59:59");
					//生效日期大于失效日期报错
					if ($start_time>$end_time){
						echo 'timeerror';
						exit;
					}
					//除编辑数据本身之外的公式
					$old_formula = Receivableformula::find('fee_name=? and product_id=? and package_type=? and customs_code=? and receivable_formula_id!=?',$value[0]['fee_name'],$value[0]['product_id'],$value[0]['package_type'],$value[0]['customs_code'],$value[0]['receivable_formula_id'])
					//查找是否有时间段重叠的数据
					->where('(effective_time>=? and effective_time<=?) or (effective_time<=? and fail_time>=?) or (fail_time>=? and fail_time<=?)',$start_time,$end_time,$start_time,$end_time,$start_time,$end_time)
					->getOne();
					//有时间段重叠,报错
					if (!$old_formula->isNewRecord()){
						echo 'formularepeat';
						exit;
					}
					$value[0]['effective_time']=strtotime($value[0]['effective_time']."00:00:00");
					$value[0]['fail_time']=strtotime($value[0]['fail_time']."23:59:59");
					$formula->changeProps ( $value[0] );
					$formula->save ();
				}
				echo ($formula->receivable_formula_id);
			}
			exit;
		}
	}
	/**
	 * @todo   创建标签 客户
	 * @author stt
	 * @since  August 18th 2020
	 * @return 标签列
	 */
	function createTabsCustomerId($product) {
		$result = array ();
		$customers = Customer::find()->getAll();
		foreach ($customers as $cus){
			$result [] = array (
				"id" => $cus->customs_code,
				"title" => $cus->customer,
				"href" => url ( "product/edit", array (
					"id" => $product->product_id,
					"customs_code" => $cus->customs_code
				) )
			);
		}
		return $result;
	}
	
	/**
	 * 创建标签 包装类型
	 *
	 * @param 产品 $product
	 * @return 标签列
	 */
	function createTabsType($product) {
		$customs_code = request ( "customs_code", "FARA00001" );
	    return array (
	        array (
	            "id" => "BOX",
	            "title" => "BOX",
	            "href" => url ( "product/edit", array (
	                "id" => $product->product_id,
	                "type" => "BOX",
	            	"customs_code" => $customs_code
	            ) )
	        ),
	        array (
	            "id" => "PAK",
	            "title" => "PAK",
	            "href" => url ( "product/edit", array (
	                "id" => $product->product_id,
	                "type" => "PAK",
	            	"customs_code" => $customs_code
	            ) )
	        ),
	        array (
	            "id" => "DOC",
	            "title" => "DOC",
	            "href" => url ( "product/edit", array (
	                "id" => $product->product_id,
	                "type" => "DOC",
	            	"customs_code" => $customs_code
	            ) )
	        )
	    );
	}
	/**
	 * 创建标签
	 *
	 * @param 产品 $product        	
	 * @return 标签列
	 */
	static function createTabs($product) {
		if ($product->isNewRecord ()) {
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
					"href" => url ( "product/edit", array (
						"id" => $product->product_id 
					) ) 
				),
				array (
					"id" => "4",
					"title" => "渠道成本",
					"href" => url ( "channelcost/search", array (
						"id" => $product->product_id 
					) ) 
				) 
			);
		}
	}
	/**
	 * 保存燃油
	 */
	function actionProductfuelsave(){
	    //燃油附加费
	    if (request('productfuel')){
	        $p = request ( "productfuel" );
	        $productfuel = Productfuel::find ( "product_fuel_id = ?", $p ["product_fuel_id"] )->getOne ();
	        $productfuel->product_id = request('product_id');
	        $p['effective_date']=strtotime($p['effective_date']."00:00:00");
	        $p['fail_date']=strtotime($p['fail_date']."23:59:59");
	        $productfuel->changeProps ( $p );
	        $productfuel->save ();
	        echo ($productfuel->product_fuel_id);
	    }
	    exit();
	}
	
	/**
	 * 燃油删除
	 */
	function actionFueldelete() {
	    if (request ( "product_fuel_id" )) {
	        $productfuel = Productfuel::find ( "product_fuel_id = ?", request ( "product_fuel_id" ) )->getOne ();
	        $productfuel->destroy ();
	    }
	    exit();
	}
	/**
	 * 无服务邮编
	 */
	function actionNoserivcezipcode() {
	    $noserivce=Noserivcezipcode::find();
	    //邮编,精确查询
	    if(request('zip_code')){
	        $noserivce->where('zip_code = ?',request('zip_code'));
	    }
	    //城市,模糊查询
	    if(request('city')){
	        $noserivce->where('city like ?','%'.request('city').'%');
	    }
	    //国家，精确查询
	    if(request('country_code')){
	        $noserivce->where('country_code = ?',request('country_code'));
	    }
	    //产品查询
	    if(request('service_code')){
	        $noserivce->where('service_code = ?',request('service_code'));
	    }
	    if(request('do') == '导出'){
	    	$noserivce = $noserivce->getAll();
	    	$header = array (
	    		'邮编','城市','国家','产品'
	    	);
	    	$sheet = array (
	    		$header
	    	);
	    	foreach ($noserivce as $no){
	    		$sheet [] =array(
	    			//例如，导出邮编00601
	    			"'".$no->zip_code,
	    			$no->city,
	    			$no->country_code,
	    			$no->service_code
	    		);
	    	}
	    	Helper_ExcelX::array2xlsx ( $sheet, '无服务邮编' );
	    	exit();
	    }
	    $pagination = null;
	    $noserivcelist=$noserivce->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
	    ->fetchPagination ( $pagination )->order('zip_code_id desc')->getAll();
	    $this->_view['noserivcelist']=$noserivcelist;
	    $this->_view['pagination']=$pagination;
	}
	/**
	 * 无服务邮编导入
	 */
	function actionImport() {
	    set_time_limit(0);
	    if(request_is_post ()){
	        $errors = array ();
	        $uploader = new Helper_Uploader();
	        //检查指定名字的上传对象是否存在
	        if (! $uploader->existsFile ( 'file' )) {
	            return $this->_redirectMessage('未上传文件','',url('product/import'));
	        }
	        $file = $uploader->file ( 'file' );//获得文件对象
	        if (! $file->isValid ( 'xls,xlsx' )) {
	            return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('product/import'));
	        }
	        
	        $des_dir = Q::ini('upload_tmp_dir');//缓存路径
	        $filename = $des_dir.DS.date ( 'YmdHis' ).'ExpenseImport.'.$file->extname ();
	        $file->move ( $filename );
	        ini_set ( "memory_limit", "3072M" );
	        $xls = Helper_Excel::readFile ( $filename,true);
	        $sheet =$xls->toHeaderMap ();
	        
	        //删除空单元
// 	        Helper_Array::removeEmpty ( $sheet );
	        Noserivcezipcode::meta()->deleteWhere();
	        //导入的表中有数据
            foreach ($sheet as &$row){
                if(isset($row ['邮编'])&&isset($row ['城市'])&&isset($row ['国家'])&&isset($row ['产品'])){
                   if(strlen($row ['产品'])==0){
                      $row ['状态']='失败';
                      $row ['信息']='未填写产品';
                      continue;
                   }else{
                      $product = Product::find("product_name = ?",$row ['产品'])->getOne();
                      if($product->isNewRecord()){
                         $row ['状态']='失败';
                         $row ['信息']='产品不存在';
                         continue;
                      }
                      if(strlen($row ['邮编'])==0&&strlen($row ['城市'])==0&&strlen($row ['国家'])==0){
                         $row ['状态']='失败';
                         $row ['信息']='邮编、城市、国家必填一个';
                         continue;
                      }else{
                         if(strlen($row ['邮编'])>0){
                            if(strlen($row ['邮编'])!=5 && strlen($row ['邮编'])!=9){
                               $row ['状态']='失败';
                               $row ['信息']='邮编错误';
                               continue;
                            }
                         }
                         if(strlen($row ['国家'])>0){
                            $country = Country::find("code_word_two = ?",$row ['国家'])->getOne();
                            if($country->isNewRecord()){
                               $row ['状态']='失败';
                               $row ['信息']='国家二字码错误';
                               continue;
                            }
                         }
                         $noservice = Noserivcezipcode::find("zip_code = ? and city = ? and country_code = ? and service_code = ?",$row ['邮编'],$row ['城市'],$row ['国家'],$row ['产品'])->getOne();
                         if(!$noservice->isNewRecord()){
                            $row ['状态']='失败';
                            $row ['信息']='已存在';
                            continue;
                         }else{
                            $noservice_code = new Noserivcezipcode();
                            $noservice_code->zip_code = $row ['邮编'];
                            $noservice_code->city  = $row ['城市'];
                            $noservice_code->country_code = $row ['国家'];
                            $noservice_code->service_code = $row ['产品'];
                            $noservice_code->save();
                            $row['状态']='成功';
                            $row['信息']='';
                         }
                      }
                   }
                }else{
                   return $this->_redirectMessage('缺少字段','',url('product/import'));
                }
            }
            $this->_view['sheet']=$sheet;
	    }
	}
	/**
	 * 删除
	 */
	function actionDelete(){
	    $zip_code_id = request('zip_code_id');
	    $del_zip_code = Noserivcezipcode::find('zip_code_id = ?',$zip_code_id)->getOne()->destroy();
	    return $this->_redirect(url('product/noserivcezipcode'));
	}
	/**
	 * 下载
	 */
	function actiondownload(){
	    return $this->_redirect(QContext::instance()->baseDir(). 'public/download/无服务邮编、城市、国家导入模板.xlsx');
	}
	
	/*
	 * 黑名单列表
	 */
	function actionblacklist(){
	    $blacklist=blacklist::find();
	    //邮编,精确查询
	    if(request('consignee_postal_code')){
	        $blacklist->where('consignee_postal_code = ?',request('consignee_postal_code'));
	    }
	    //城市,模糊查询
	    if(request('consignee_city')){
	        $blacklist->where('consignee_city like ?','%'.request('consignee_city').'%');
	    }
	    //省州,模糊查询
	    if(request('consignee_state_region_code')){
	        $blacklist->where('consignee_state_region_code like ?','%'.request('consignee_state_region_code').'%');
	    }
	    //发件人,模糊查询
	    if(request('sender_name1')){
	        $blacklist->where('sender_name1 like ?','%'.request('sender_name1').'%');
	    }
	    //国家，精确查询
	    if(request('consignee_country_code')){
	        $blacklist->where('consignee_country_code = ?',request('consignee_country_code'));
	    }
	    //产品查询
	    if(request('product_id')){
	        $blacklist->where('product_id = ?',request('product_id'));
	    }
	    if(request("export")=='exportlist'){
	    	ini_set('max_execution_time', '0');
	    	ini_set('memory_limit', '2G');
	    	set_time_limit(0);
	    	$list=clone $blacklist;
	    	$lists=$list->getAll();
	    	$header = array (
	    		'国家二字码',
	    		'省州',
	    		'城市',
	    		'邮编',
	    		'发件人',
	    		'发件公司',
	    		'地址',
	    		'品名',
	    		'产品',
	    	);
	    	$sheet = array (
	    		$header
	    	);
	    	foreach ($lists as $value){
	    		$row =array(
	    			$value->consignee_country_code,
	    			$value->consignee_state_region_code,
	    			$value->consignee_city,
	    			$value->consignee_postal_code,
	    			$value->sender_name1,
	    			$value->sender_name2,
	    			$value->sender_street1,
	    			$value->product_name,
	    			$value->product->product_chinese_name,
	    		);
	    		$sheet [] = $row;
	    	}
	    	Helper_ExcelX::array2xlsx ( $sheet, '黑名单列表' );
	    	exit ();
	    }
	    $pagination = null;
	    $list=$blacklist->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
	    ->fetchPagination ( $pagination )->order('create_time desc')->getAll();
	    $this->_view['list']=$list;
	    $this->_view['pagination']=$pagination;
	}
	
	/*
	 * 黑名单详情页
	 */
	function actionblackedit(){
	    $black=new blacklist();
	    if(request('blacklist_id')){
	        $black=blacklist::find('blacklist_id = ?',request('blacklist_id'))->getOne();
	    }
	    if(request_is_post()){
	    	if(!trim(request('consignee_country_code')) && !trim(request('consignee_postal_code'))&& !trim(request('consignee_city'))&& !trim(request('consignee_state_region_code'))&& !trim(request('sender_name1'))&& !trim(request('sender_name2'))&& !trim(request('sender_street1'))&& !trim(request('product_name'))){
	    		return $this->_redirectMessage('保存失败','至少填一项',url('product/blacklist'));
	    	}
	        $black->product_id=request('product_id');
	        $black->consignee_country_code=request('consignee_country_code');
	        $black->consignee_postal_code=request('consignee_postal_code');
	        $black->consignee_city=request('consignee_city');
	        $black->consignee_state_region_code=request('consignee_state_region_code');
	        $black->product_name=request('product_name');
	        $black->sender_name1=request('sender_name1');
	        $black->sender_name2=request('sender_name2');
	        $black->sender_street1=request('sender_street1');
	        $black->save();
	        return $this->_redirectMessage('保存成功','',url('product/blacklist'));
	    }
	    $this->_view['black']=$black;
	}
	
	/*
	 * 黑名单删除
	 */
	function actionbdelete(){
	    $blacklist_id = request('blacklist_id');
	    blacklist::meta()->destroyWhere('blacklist_id = ? ',$blacklist_id);
	    return 'success';
	}
	
	/**
	 * 下载
	 */
	function actiondownloadblack(){
		return $this->_redirect(QContext::instance()->baseDir(). 'public/download/黑名单导入模板.xlsx');
	}
	
	/**
	 * 导入黑名单
	 */
	function actionBimport(){
		set_time_limit(0);
		if(request_is_post ()){
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('product/bimport'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('product/bimport'));
			}
			 
			$des_dir = Q::ini('upload_tmp_dir');//缓存路径
			$filename = $des_dir.DS.date ( 'YmdHis' ).'ExpenseImport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheet =$xls->toHeaderMap ();
			 
			//删除空单元
// 	        Helper_Array::removeEmpty ( $sheet );
	        if(empty($sheet)){
	        	return $this->_redirectMessage('文件内容不能为空','',url('product/bimport'));
	        }
			//导入的表中有数据
			$error = array ();
			$required_fields = array (
				'邮编',
				'城市',
				'国家二字码',
				'产品',
				'省州',
				'发件人',
				'发件公司',
				'地址',
				'品名',
			);
			foreach ($sheet as $k => $row){
				foreach ( $required_fields as $field ) {
					if (!isset ( $row [$field] )) {
						$error [$k] [$field] = '字段不存在';
					}
				}
				if(strlen($row ['产品'])==0){
					$error [$k] ['产品'] = '不能为空';
				}else {
					$product = Product::find("product_chinese_name = ?",$row ['产品'])->getOne();
					if($product->isNewRecord()){
						$error [$k] ['产品'] = $row ['产品'].'不存在';
					}
				}
				if(strlen($row ['国家二字码'])>0){
					$country = Country::find("code_word_two = ?",$row ['国家二字码'])->getOne();
					if($country -> isNewRecord()){
						$error [$k] ['国家二字码'] = $row ['国家二字码'].'错误';
					}
				}
				if(!trim($row ['国家二字码']) && !trim($row ['邮编'])&& !trim($row ['城市'])&& !trim($row ['省州'])&& !trim($row ['品名'])&& !trim($row ['发件人'])&& !trim($row ['发件公司'])&& !trim($row ['地址'])){
					$error [$k] ['黑名单'] = '内容至少一项';
				}
			}
			
			$this->_view ['errors'] = $error;
			if (empty ( $error )) {
				blacklist::meta()->destroyWhere();
				foreach ( $sheet as $row ) {
					$product = Product::find("product_chinese_name = ?",$row ['产品'])->getOne();
					$black = new blacklist(array(
						'product_id' => $product->product_id,
						'consignee_country_code' => $row ['国家二字码'],
						'consignee_postal_code' => $row ['邮编'],
						'consignee_city' => $row ['城市'],
						'consignee_state_region_code' => $row ['省州'],
						'product_name' => $row ['品名'],
						'sender_name1' => $row ['发件人'],
						'sender_name2' => $row ['发件公司'],
						'sender_street1' => $row ['地址'],
					));
					$black->save();
				}
				return $this->_redirectMessage('导入成功','',url('product/bimport'));
			}
		}
	}
	
	/**
	 * 邮政通讯录
	 */
	function actionbook(){
	    $postalbook=postalbook::find();
	    //渠道
	    if(request('channel_id')){
	    	$postalbook->where('channel_id = ?',request('channel_id'));
	    }
	    //国家,精确查询
	    if(request('code_word_two')){
	        $postalbook->where('code_word_two = ?',request('code_word_two'));
	    }
	    $pagination = null;
	    $list=$postalbook->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
	    ->fetchPagination ( $pagination )->order('create_time desc')->getAll();
	    $this->_view['list']=$list;
	    $this->_view['pagination']=$pagination;
	}
	
	/*
	 * 邮政通讯录详情
	 */
	function actionbookedit(){
	    $postalbook=new postalbook();
	    if(request('book_id')){
	        $postalbook=postalbook::find('book_id = ?',request('book_id'))->getOne();
	    }
	    if(request_is_post()){
	    	$postalbook->channel_id=request('channel_id');
	        $postalbook->code_word_two=request('code_word_two');
	        $postalbook->servicetel=request('servicetel');
	        $postalbook->servicesch=request('servicesch');
	        $postalbook->customtel=request('customtel');
	        $postalbook->save();
	        return $this->_redirectMessage('保存成功','',url('product/book'));
	    }
	    $this->_view['postalbook']=$postalbook;
	}
	
	function actionbookdelete(){
	    $book_id = request('book_id');
	    postalbook::meta()->destroyWhere('book_id = ? ',$book_id);
	    return $this->_redirect(url('product/book'));
	}
	/**
	 * 渠道通讯录导入
	 */
	function actionBookImport(){
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');//不限制内存
	    if(request_is_post ()){
	        $errors = array ();
	        $uploader = new Helper_Uploader();
	        //检查指定名字的上传对象是否存在
	        if (! $uploader->existsFile ( 'file' )) {
	            return $this->_redirectMessage('未上传文件','',url('product/bookimport'));
	        }
	        $file = $uploader->file ( 'file' );//获得文件对象
	        if (! $file->isValid ( 'xls,xlsx' )) {
	            return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('product/bookimport'));
	        }
	        $des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
	        $filename = date ( 'YmdHis' ).'ExpenseImport.'.$file->extname ();
	        $file_route = $des_dir.DS.$filename;
	        $file->move ( $file_route );
	        ini_set ( "memory_limit", "3072M" );
	        $xls = Helper_Excel::readFile ($file_route,true);
	        $sheet =$xls->toHeaderMap ();
	        //删除空单元
// 	        Helper_Array::removeEmpty ( $sheet );
	        //导入的表中有数据
	        $arr=array();
	        if(count($sheet)<1){
	        	return $this->_redirectMessage('内容不能为空','',url('product/bookimport'));
	        }
	        foreach ($sheet as $key => $row){
	            if(!strlen($row ['渠道名称'])){
	                $arr[$key]['结果']='失败';
	                $arr[$key]['信息']='渠道名称必填';
	                continue;
	            }
	            if(!strlen($row ['国家二字码'])){
	            	$arr[$key]['结果']='失败';
	            	$arr[$key]['信息']='国家二字码必填';
	            	continue;
	            }
	            $channel = Channel::find('channel_name = ?',$row ['渠道名称'])->getOne();
	            if($channel->isNewRecord()){
	            	$arr[$key]['结果']='失败';
	            	$arr[$key]['信息']='渠道名称不存在';
	            	continue;
	            }
	            $country = Country::find('code_word_two = ?',strtoupper($row ['国家二字码']))->getOne();
	            if($country->isNewRecord()){
	                $arr[$key]['结果']='失败';
	                $arr[$key]['信息']='国家二字码不存在';
	                continue;
	            }
	            //将数据存入数据库
	            $postalbook = postalbook::find('channel_id = ? and code_word_two = ?',$channel->channel_id,$row ['国家二字码'])->getOne();
	            if($postalbook->isNewRecord()){
	            	$postalbook = new postalbook();
	            }
	            $postalbook->channel_id = $channel->channel_id;
	            $postalbook->code_word_two = $row ['国家二字码'];
	            $postalbook->servicetel = $row ['目的地联系电话'];
	            $postalbook->servicesch = $row ['目的地作息时间'];
	            $postalbook->customtel = $row ['目的地其他联系信息'];
	            $postalbook->save();
                $arr[$key]['结果']='成功';
                $arr[$key]['信息']='';
	        }
	        $this->_view['sheet']=$arr;
	    }
	}
	/**
	 * 下载模板
	 * 
	 */
	function actionDownloadTemp(){
		return $this->_redirect(QContext::instance()->baseDir(). 'public/download/product_book_import.xlsx');
	}
	function actionEmailTemplate(){
	    $emailtemplate=EmailTemplate::find();
	    $pagination = null;
	    //模板属性搜索条件
	    if(request('template_type')){
	    	$emailtemplate->where('template_type=?',request('template_type'));
	    }
	    //模板产品 
	    if(is_numeric(request('product_id'))){
	    	$emailtemplate->where('product_id = ?',request('product_id'));
	    }
	    //模板标题 
	    //echo request('product_id');exit;
	    if(request('template_title')){
	    	$emailtemplate->where('template_title like ?','%'.request('template_title').'%');
	    }
	    
	    $list=$emailtemplate->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
	    ->fetchPagination ( $pagination )->order('create_time desc')->getAll();
	    $this->_view['list']=$list;
	    $this->_view['pagination']=$pagination;
	}
	function actionTemplateEdit(){
	    $template = EmailTemplate::find('id = ?',request('id'))->getOne();
	    if(request_is_post()){
	        //判断模板名称是否存在
	        $template_check=EmailTemplate::find('id = ?',request('id'))->getOne();
	        if(!$template_check->isNewRecord() && ($template_check->id!=request('id'))){
	            return $this->_redirectMessage('编辑失败', '模板已存在', url('/templateedit',array('id'=>$template->id)),2);
	        }else{
	            $template = EmailTemplate::find('id = ?',request('id'))->getOne();
	            $template->template_name = request('template_name');
	            $template->template_text = request('template_text');
	            $template->template_title = request('template_title');
	            $template->product_id = request('product_id');
	            $template->template_type = request('template_type');
	            $template->save();
	            return $this->_redirectMessage('邮件模板编辑', '编辑成功', url('product/emailtemplate'));
	        }
	    }
	    $this->_view['template']=$template;
	}
	function actionTemplateDel(){
	    $template = EmailTemplate::find('id = ?',request('id'))->getOne();
	    if($template->isNewRecord()){
	        return false;
	    }else{
	        $template->destroy();
	        return 'delsuccess'; 
	    }
	}
	function actionTemplateInfo(){
	    if(request('ali_order_no') && request('id')){
	       $data = array();
	       $template = EmailTemplate::find('id = ?',request('id'))->getOne();
	       if($template->isNewRecord()){
	           $data['error'] = 'notemplate';
	           return json_encode($data);
	       }
	       $order = Order::find('ali_order_no = ?',request('ali_order_no'))->getOne();
	       if($order->isNewRecord()){
	           $data['error'] = 'noorder';
	           return json_encode($data);
	       }
	       $ordergoods = Orderproduct::find('order_id = ?',$order->order_id)->order('order_product_id asc')->getOne();
	       
	       $postalbook = postalbook::find('code_word_two = ? and channel_id = ?',$order->consignee_country_code,$order->channel_id)->getOne();
	       $track = $this->getTracking($order);
	       
	       $template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $template->template_title);
	       $template_title = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_title);
	       $template_title = preg_replace('/tracking_no/',$order->tracking_no, $template_title);
	       $template_title = preg_replace('/reference_no/',$order->reference_no, $template_title);
	       if(strlen($order->channel->trace_network_code)>0){
	       	$template_title = preg_replace('/trace_network_code/',$order->channel->trace_network_code, $template_title);
	       }else{
	       	$template_title = preg_replace('/trace_network_code/',$order->channel->network_code, $template_title);
	       }
	       $template_title = preg_replace('/network_code/',$order->channel->network_code, $template_title);
	       $template_title = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_title);
	       $template_title = preg_replace('/servicetel/',$postalbook->servicetel, $template_title);
	       $template_title = preg_replace('/servicesch/',$postalbook->servicesch, $template_title);
	       $template_title = preg_replace('/customtel/',$postalbook->customtel, $template_title);
	       //产品匹配
	       $template_title = preg_replace('/good_name/',$ordergoods->product_name, $template_title);
	       $template_title = preg_replace('/track1/',@$track[0], $template_title);
	       $template_title = preg_replace('/track2/',@$track[1], $template_title);
	       $template_title = preg_replace('/track3/',@$track[2], $template_title);
	       $deprtment_name = $order->department_id?$order->department->department_name:'';
	       $template_title = preg_replace('/warehouse/',$deprtment_name,$template_title);
	       
	       $template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $template->template_text);
	       $template_info = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_info);
	       $template_info = preg_replace('/tracking_no/',$order->tracking_no, $template_info);
	       $template_info = preg_replace('/reference_no/',$order->reference_no, $template_info);
	       if(strlen($order->channel->trace_network_code)>0){
	       	$template_info = preg_replace('/trace_network_code/',$order->channel->trace_network_code, $template_info);
	       }else{
	       	$template_info = preg_replace('/trace_network_code/',$order->channel->network_code, $template_info);
	       }
           $template_info = preg_replace('/network_code/',$order->channel->network_code, $template_info);
	       $template_info = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_info);
	       $template_info = preg_replace('/servicetel/',$postalbook->servicetel, $template_info);
	       $template_info = preg_replace('/servicesch/',$postalbook->servicesch, $template_info);
	       $template_info = preg_replace('/customtel/',$postalbook->customtel, $template_info);
	       $template_info = preg_replace('/track1/',@$track[0], $template_info);
	       $template_info = preg_replace('/track2/',@$track[1], $template_info);
	       $template_info = preg_replace('/track3/',@$track[2], $template_info);
	       $template_info = preg_replace('/warehouse/',$deprtment_name,$template_info);
	       //产品匹配
	       $template_info = preg_replace('/good_name/',$ordergoods->product_name, $template_info);
	       $data['title'] = $template_title;
	       $data['message'] = $template_info;
	       return json_encode($data);
	    }else{
	        $data['error'] = 'nodata';
	        return json_encode($data);
	    }
	}
	
	static function getTracking($order){
		$url='http://1688.far800.com/api/FarTrack?num='.$order->far_no;
		$ret=Helper_Curl::get($url);
		$ret=json_decode($ret,TRUE);
		$track=array();
		if (isset($ret['data']) && count($ret['data'])){
			$ret['data']=array_reverse($ret['data']);
	    	foreach ($ret['data'] as $key => &$row){
	    		$track[$key]=$row['timeFormat'].' '.$row['location'].' '.$row['context'];
	    		if($key == 2){
	    			break;
	    		}
	    	}
		}
		return $track;
	}
	
	function actionSendTemplate(){
	    if(request('id')&&request('ali_order_no')&&request('message')){
	       $order = Order::find('ali_order_no = ?',request('ali_order_no'))->getOne();
	       if($order->isNewRecord()){
	          $data['error'] = 'nodata';
	          return json_encode($data);
	       }
	       $title = nl2br(request('title'));
	       $msg = nl2br(request('message'));
	       $email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
	       QLog::log($email_response);
	       if($email_response == 'email_success'){
		      if(request('abnormal_parcel_id')){
		       	  $history=new Abnormalparcelhistory();
		       	  $history->abnormal_parcel_id=request('abnormal_parcel_id');
		       	  $history->follow_up_content=date('Y-m-d H:i:s').'已发送邮件，标题：'.request('title').' 内容：'.request('message');
		       	  $history->is_mail=1;
		       	  $history->follow_up_operator=MyApp::currentUser("staff_name");
		       	  $history->save();
		      }
	          $data['success'] = 'success';
	          return json_encode($data);
	       }else{
	          $data['errorinfo'] = $email_response;
	          return json_encode($data);
	       }
	    }else{
	       $data['error'] = 'nodata';
	       return json_encode($data);
	    }
	    exit();
	}
	/**
	 * 自动发送规则
	 */
	function actionAutomaticEmailRule(){
	    $rule=AutomaticEmailRule::find();
	    $pagination = null;
	    $list=$rule->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
	    ->fetchPagination ( $pagination )->order('create_time desc')->getAll();
	    $this->_view['list']=$list;
	    $this->_view['pagination']=$pagination;
	}
	function actionRuleEdit(){
		$rule = AutomaticEmailRule::find('id = ?',request('id'))->getOne();
		$trace_code=array_combine(array_keys(Tracking::$trace_code_cn),array_keys(Tracking::$trace_code_cn));
		$trace_code['订单创建'] = '订单创建';
		$trace_code['订单已提取'] = '订单已提取';
		$trace_code['支付超时预警48H'] = '支付超时预警48H';
		$trace_code['支付超时预警24H'] = '支付超时预警24H';
		$emailtemplate = array();
		if(request('product_id')){
			$emailtemplate = Helper_Array::toHashmap(EmailTemplate::find('product_id = ?',request('product_id'))->asArray()->getAll(),'id','template_name');
		}
		if(request_is_post()){
			//判断模板名称是否存在
			$rule_check=AutomaticEmailRule::find('id = ?',request('id'))->getOne();
			if(!$rule_check->isNewRecord() && ($rule_check->id!=request('id'))){
				return $this->_redirectMessage('编辑失败', '模板已存在', url('/ruleedit',array('id'=>$rule->id)),2);
			}else{
				$rule = AutomaticEmailRule::find('id = ?',request('id'))->getOne();
				$rule->automatic_email_rule = request('automatic_email_rule');
				$rule->product_id = request('product_id');
				$rule->tracking_code = request('tracking_code');
				$rule->email_id = request('email_id');
				$rule->save();
				return $this->_redirectMessage('邮件模板编辑', '编辑成功', url('/ruleedit',array('id'=>$rule->id)));
			}
		}
		$this->_view['rule']=$rule;
		$this->_view['trace_code']=$trace_code;
		$this->_view['emailtemplate']=$emailtemplate;
	}
	function actiongetemailtemplate(){
		$emailtemplate = EmailTemplate::find('product_id = ?',request('product_id'))->order('template_name asc')->getAll()->toHashMap('id','template_name');
		$data = array();
		foreach ($emailtemplate as $key => $e){
			$data[] =array(
				'id'=>$key,
				'template_name'=>$e
			);
		}
		return json_encode($data);
	}
	function actionRuleDel(){
	    $rule = AutomaticEmailRule::find('id = ?',request('id'))->getOne();
	    if($rule->isNewRecord()){
	        return false;
	    }else{
	        $rule->destroy();
	        return 'delsuccess'; 
	    }
	}
	/**
	 * @todo   应收公式操作费 导出
	 * @author stt
	 * @since  2020-11-23
	 * @param
	 * @link   #83417
	 */
	function actionformulaExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//产品渠道
		$receivableformulas = Receivableformula::find ( "product_id = ? and customs_code = ?", request ( "id" ), request ( "customs_code" ,'FARA00001'))->getAll();
		$customer = Customer::find('customs_code=?',request ( "customs_code" ,'FARA00001'))->getOne();
		//导出字段
		$header = array (
			'费用名称',
			'公式',
			'备注',
			'自动',
			'生效日期',
			'失效日期',
			'币种',
			'包裹类型',
			'客户'
		);
		$sheet = array (
			$header
		);
		foreach ($receivableformulas as $value){
			if ($value->fail_time>time()){
				$row =array(
					//费用名称
					$value->fee_name,
					//公式
					$value->formula,
					//备注
					$value->remark,
					//自动
					$value->calculation_flag==1?'是':'否',
					//生效日期
					date('Y-m-d',$value->effective_time),
					//失效日期
					date('Y-m-d',$value->fail_time),
					//币种
					$value->currency_code,
					//包裹类型
					$value->package_type,
					//客户
					$customer->customer
				);
				$sheet [] = $row;
			}
		}
		//导出
		Helper_Excel::array2xls ( $sheet, '应收杂费项导出.xlsx' );
		exit ();
	}
	/**
	 * @todo   杂费项管理 导入
	 * @author stt
	 * @since  2020-11-23
	 * @param
	 * @link   #83417
	 */
	function actionformulaImport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//上传文件开始
		$uploader = new Helper_Uploader();
		//检查指定名字的上传对象是否存在
		if (! $uploader->existsFile ( 'file' )) {
			return $this->_redirectMessage('未上传文件','',url( '/edit' ,array('id'=>request('id_import'))), 3 );
		}
		//获得文件对象
		$file = $uploader->file ( 'file' );
		//文件格式
		if (! $file->isValid ( 'xls,xlsx' )) {
			return $this->_redirectMessage('文件格式不正确：xls,xlsx','',url( '/edit' ,array('id'=>request('id_import'))), 3 );
		}
		//缓存路径
		$des_dir = Q::ini ( 'upload_tmp_dir' );
		$filename = $des_dir.DS.date ( 'YmdHis' ).'productformulaimport.'.$file->extname ();
		$file->move ( $filename );
		$xls = Helper_Excel::readFile ( $filename,true);
		$sheets =$xls->toHeaderMap ();
		//必填字段
		$required_fields = array (
			'费用名称',
			'公式',
			'备注',
			'自动',
			'生效日期',
			'失效日期',
			'币种',
			'包裹类型'
		);
		//缺少字段:费用名称、公式、备注、自动、生效日期、失效日期、币种、包裹类型
		if(!isset($sheets[0]['费用名称']) || !isset($sheets[0]['公式'])|| !isset($sheets[0]['备注'])|| !isset($sheets[0]['自动'])|| !isset($sheets[0]['生效日期'])|| !isset($sheets[0]['失效日期'])|| !isset($sheets[0]['币种'])|| !isset($sheets[0]['包裹类型'])){
			return $this->_redirectMessage('缺少字段','缺少字段:费用名称、公式、备注、自动、生效日期、失效日期、币种、包裹类型',url( '/edit' ,array('id'=>request('id_import'))), 10);
		}
		//不能为空
		foreach($sheets as $k=>$row){
			foreach ( $required_fields as $col ) {
				$row [$col] = trim ( $row [$col] );
				if (strlen ( $row [$col] ) == 0) {
					//跳转报错
					return $this->_redirectMessage('不能为空','第'.($k+2).'行'.$col.'不能为空',url( '/edit' ,array('id'=>request('id_import'))), 10 );
				}
				if(!in_array($row['包裹类型'],array('BOX','PAK','DOC'))){
					//跳转报错
					return $this->_redirectMessage('包裹类型不存在','第'.($k+2).'行，包裹类型不存在，包裹类型应填BOX、PAK、DOC',url( 'channelcost/edit' ,array('id'=>request('id_import'))), 10 );
				}
			}
		}
		//所有客户
		$customers = Customer::find()->getAll();
		foreach ($customers as $customer){
			foreach ($sheets as $sheet){
					//导入的生效日期
					$start_time = strtotime($sheet['生效日期']."00:00:00");
					//导入的失效日期
					$end_time = strtotime($sheet['失效日期']."23:59:59");
					//生效日期大于失效日期跳过
					if ($start_time>$end_time){
						continue;
					}
					$receivableformula = Receivableformula::find('product_id=? and customs_code=? and package_type=? and fee_name=?',request('id_import'),$customer->customs_code,$sheet['包裹类型'] ,$sheet['费用名称'])
					//查找是否有时间段重叠的数据
					->where('(effective_time>=? and effective_time<=?) or (effective_time<=? and fail_time>=?) or (fail_time>=? and fail_time<=?)',$start_time,$end_time,$start_time,$end_time,$start_time,$end_time)
					->getOne();
					//有时间段重叠,报错
					if (!$receivableformula->isNewRecord()){
						continue;
					}
					$formula = new Receivableformula();
					$formula->product_id = request('id_import');
					$formula->customs_code = $customer->customs_code;
					$formula->package_type = $sheet['包裹类型'];
					$formula->fee_name = $sheet['费用名称'];
					$formula->formula = $sheet['公式'];
					$formula->remark = $sheet['备注'];
					$formula->calculation_flag = $sheet['自动']=='是'?'1':'';
					//时间
					$formula->effective_time = strtotime($sheet['生效日期']."00:00:00");
					$formula->fail_time = strtotime($sheet['失效日期']."23:59:59");
					$formula->currency_code = $sheet['币种'];
					$formula->save();
			}
		}
		//导入成功
		return $this->_redirectMessage ( '导入成功', '成功', url ( '/edit' ,array('id'=>request('id_import'))),3);
		exit ();
	}
}