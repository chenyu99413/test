<?php
class Controller_Pickup extends Controller_Abstract {
    /**
     * 取件订单列表
     */
    function actionSearch(){
        //查询当前登录人员业务相关部门
        $relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
        //获取部门名称
        $relevant_department_names=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_name', 'department_name');
        $relevant_department_names=array_diff($relevant_department_names, array('技术中心','杭州仓','上海仓','义乌仓','广州仓','财务中心','南京仓'));
        $department_names = array();
        foreach ($relevant_department_names as $relevant_department_name){
        	$department_names[$relevant_department_name]= $relevant_department_name;
        }
        $department_names['吉通同城']='吉通同城';
        $department_names['德邦快递']='德邦快递';
        $department_names['申通国际']='申通国际';
        $orders=Order::find("ali_testing_order!= '1'")->where("order_status in ('1','14','15','16') or (order_status='12' and warehouse_in_time is null and order_status_copy in ('1','14','15','16'))");
        if($relevant_department_names){
            $orders->where('pick_company in (?)',$department_names);
        }else{
            $orders->where('1!=1');
        }
        if(request('pick_company')){
            $orders->where('pick_company=?',request('pick_company'));
        }
        //多运单号搜索
        if(request('ali_order_no')){
        	$ali_order_nos=explode("\r\n", request('ali_order_no'));
        	$ali_order_nos=array_filter($ali_order_nos);//去空
        	$ali_order_nos=array_unique($ali_order_nos);//去重
        	$orders->where('ali_order_no in (?)',$ali_order_nos);
        }
        if(request('weight_cost_out_start')){
        	$orders->where('weight_income_ali>=? ',request('weight_cost_out_start'));
        }
        if(request('weight_cost_out_end')){
        	$orders->where('weight_income_ali<=?',request('weight_cost_out_end'));
        }
        if(request('service_code')){
        	$orders->where('service_code=?',request('service_code'));
        }
        if(request('department_id')){
        	$orders->where('department_id=?',request('department_id'));
        }
        if($relevant_departments){
        	$orders->where('department_id in (?)',$relevant_departments);
        }
        $counts = array ();
        // 未分派总数
        $order_count1=clone $orders;
        $order_count1->where('order_status=1 or (order_status="12" and warehouse_in_time is null and order_status_copy =1)');
        $counts[1]=$order_count1->getCount();
        // 已分派总数
        $order_count14=clone $orders;
        $order_count14->where('order_status=14 or (order_status="12" and warehouse_in_time is null and order_status_copy =14)');
        $counts[14]=$order_count14->getCount();
        // 已取件总数
        $order_count15=clone $orders;
        $order_count15->where('order_status=15 or (order_status="12" and warehouse_in_time is null and order_status_copy =15)');
        $counts[15]=$order_count15->getCount();
        // 网点已入库总数
        $order_count16=clone $orders;
        $order_count16->where('order_status=16 or (order_status="12" and warehouse_in_time is null and order_status_copy =16)');
        $counts[16]=$order_count16->getCount();
        
        $counts [0] = array_sum($counts);
		$order_nodevided = clone $orders;
		$order_devided = clone $orders;
		$ids = array ();
		$devided_ids = $order_devided->where ( 'order_status="14"' )
			->getAll ();
		$nodevided_ids = $order_nodevided->where ( 'order_status="1"' )
			->getAll ();
		foreach ( $devided_ids as $devided ) {
			$devided_address = $devided->sender_street1 . ' ' . $devided->sender_street2;
			foreach ( $nodevided_ids as $nodevided ) {
				$nodevided_address = $nodevided->sender_street1 . ' ' . $nodevided->sender_street2;
				if ($devided_address == $nodevided_address) {
					$ids [] = $devided->order_id;
					$ids [] = $nodevided->order_id;
				}
			}
		}
		$ids = array_unique($ids);
		if (count ( $ids ) >= 2) {
			$counts ['together'] = count($ids);
		}
        $active_id = 0;
        // 未入库
        if (request ( "parameters" ) == "no_package") {
            $orders->where('order_status=1 or (order_status="12" and warehouse_in_time is null and order_status_copy =1)');
            $active_id = 1;
        }
        // 可合并
        if (request ( "parameters" ) == "together") {
        	if(count($ids)>=2){
        		$orders->where('order_id in (?)',$ids);
        		$active_id = 5;
        	}
        }
        // 已分派
        if (request ( "parameters" ) == "assign") {
            $orders->where('order_status=14 or (order_status="12" and warehouse_in_time is null and order_status_copy =14)' );
            $active_id = 2;
        }
        // 已取件
        if (request ( "parameters" ) == "take") {
            $orders->where('order_status=15 or (order_status="12" and warehouse_in_time is null and order_status_copy =15)');
            $active_id = 3;
        }
        // 网点入库
        if (request ( "parameters" ) == "network_in") {
            $orders->where('order_status=16 or (order_status="12" and warehouse_in_time is null and order_status_copy =16)');
            $active_id = 4;
        }
        //导出
        if(request('export')=='exportlist'){
            $pick=clone $orders;
            $pick->where("ifnull(need_pick_up,'')='1'");
            $payeds=$pick->getAll();
            //新增参考重
            $header = array (
                '阿里订单号','订单日期','取件网点','省','市','地址','邮编','姓名','手机号','邮箱','件数','产品','报关','强制报关','客重','参考重'
            );
            $sheet = array (
                $header
            );
            foreach ($payeds as $p){
            	$item_count=0;
            	$weight=0;
            	$ckweight = 0;
                foreach ($p->packages as $package){
                	$item_count+=$package->quantity;
                	//客重取阿里传回的实重
                	$weight +=$package->weight;
                	$ckweight += $package->length*$package->width*$package->height/6000;
                }
                $sheet [] =array(
                    "'".$p->ali_order_no,Helper_Util::strDate('Y-m-d H:i', $p->create_time),$p->pick_company,$p->sender_state_region_code,$p->sender_city,$p->sender_street1.' '.$p->sender_street2,"'".$p->sender_postal_code,
                	$p->sender_name1.' '.$p->sender_name2,"'".$p->sender_mobile,$p->sender_email,$item_count,$p->service_product->product_chinese_name,$p->declaration_type=='DL'?'是':'否',($p->declaration_type<>'DL' && ($p->total_amount>700 || $p->weight_actual_in>70))?'是':'否',$weight,round($ckweight,2)
                );
            }
            Helper_ExcelX::array2xlsx ( $sheet, '取件清单' );
            exit ();
        }
        $pagination = null;	
        $list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
        ->fetchPagination ( $pagination )
        ->order('order_id desc')->getAll();
        $parameters=request ( "parameters" );
        $this->_view['orders']=$list;
        $this->_view['pagination']=$pagination;
        $this->_view ["counts"] = $counts;
        $this->_view ["parameters"] = $parameters;
        $this->_view ["active_id"] = $active_id;
        $this->_view ["tabs"] = $this->createTabs ( $counts );
        $this->_view['relevant_department_names']=$department_names;
    }
    /**
     * 创建标签
     */
    function createTabs($counts) {
    	$tab = array (
    		array (
    			"id" => "0","title" => "全部","count" => val ( $counts, 0, 0 ),
    			"href" => "javascript:TabSwitch()"
    		),
    		array (
    			"id" => "1","title" => "未分派","count" => val ( $counts, 1, 0 ),
    			"href" => "javascript:TabSwitch('no_package')"
    		),
    		array (
    			"id" => "2","title" => "已分派","count" => val ( $counts, 14, 0 ),
    			"href" => "javascript:TabSwitch('assign')"
    		),
    		array (
    			"id" => "3","title" => "已取件","count" => val ( $counts, 15, 0 ),
    			"href" => "javascript:TabSwitch('take')"
    		),
    		array (
    			"id" => "4","title" => "网点已入库","count" => val ( $counts, 16, 0 ),
    			"href" => "javascript:TabSwitch('network_in')"
    		)
    	);
    	if (@$counts ['together'] >= 2) {
    		$tab = array (
    			array (
    				"id" => "0","title" => "全部","count" => val ( $counts, 0, 0 ),
    				"href" => "javascript:TabSwitch()"
    			),
    			array (
    				"id" => "1","title" => "未分派","count" => val ( $counts, 1, 0 ),
    				"href" => "javascript:TabSwitch('no_package')"
    			),
    			array (
    				"id" => "5","title" => "可合并","count" => val ( $counts, 'together', 0 ),
    				"href" => "javascript:TabSwitch('together')"
    			),
    			array (
    				"id" => "2","title" => "已分派","count" => val ( $counts, 14, 0 ),
    				"href" => "javascript:TabSwitch('assign')"
    			),
    			array (
    				"id" => "3","title" => "已取件","count" => val ( $counts, 15, 0 ),
    				"href" => "javascript:TabSwitch('take')"
    			),
    			array (
    				"id" => "4","title" => "网点已入库","count" => val ( $counts, 16, 0 ),
    				"href" => "javascript:TabSwitch('network_in')"
    			)
    		);
		}
        return $tab;
    }
    /**
     * 修改状态
     */
    function actionChangestatus(){
        $order_ids=explode(',', trim(request('order_ids',',')));
        $flag=false;
        foreach ($order_ids as $order_id){
            $order=Order::find('order_id=?',$order_id)->getOne();
            if($order->order_status!='1' && $order->order_status!='14' && $order->order_status!='15'){
                $flag=true;
            }else{
                $order->order_status=request('status');
                $order->save();
            }
        }
        if($flag){
            echo 'error';
        }else{
            echo 'success';
        }
        die();
    }
    /**
     * 批量设置取件网点
     */
    function actionSetpickcompany(){
        $order_ids=explode(',', trim(request('order_ids',',')));
        if(count($order_ids) == 0){
        	return 'error';
        }
        $error_sum = 0;
        foreach ($order_ids as $order_id){
            $order=Order::find('order_id=?',$order_id)->getOne();
            if ($order->isNewRecord()){
            	$error_sum++;
            }
            $order->pick_company=request('set_pick_company');
            $order->save();
        }
        if($error_sum > 0){
        	return 'error';
        }
        return 'success';
    }
    /**
     * 网点入库
     */
    function actionPickupnetworkin(){
        if(request_is_post()){
            $ret = array (
                'msg' => '',
                'sound' => 'cuowu.mp3',
                'status' => false
            );
            $order=Order::find();
            $count1 = Order::find ( 'ali_order_no = ?', request ( 'scan_no' ) )->getCount ();
            if($count1==1){
                $order=$order->where('ali_order_no = ?', request ( 'scan_no' ))->getOne();
            }else{
                $alireference=Alireference::find('reference_no=?', request ( 'scan_no' ))->getOne();
                if($alireference->isNewRecord()){
                    $order=$order->where( '1!=1' )->getOne();
                }else{
                    $count2=Alireference::find('reference_no=?', request ( 'scan_no' ))->getCount ();
                    if($count2 > 1){
                        $ret ['msg'] = '请拆包';
                        $ret ['sound'] = 'qingchaibao.mp3';
                        return json_encode ( $ret );
                    }elseif ($count2==1){
                        $count3=Alireference::find('order_id=?', $alireference->order_id)->getCount ();
                        if($count3 > 1){
                            $ret ['msg'] = '请合并包裹';
                            $ret ['sound'] = 'hebingbaoguo.mp3';
                            return json_encode ( $ret );
                        }
                        $order=$order->where( 'reference_no = ? and order_status !="2"', request ( 'scan_no' ) )->getOne();
                    }
                }
            }
            if ($order->isNewRecord ()) {
                $ret ['msg'] = '单号错误，或包裹数据不存在';
                return json_encode ( $ret );
            }
            $err_status = array (
                2 => '已取消',
                3 => '已退货',
                4 => '已付款',
                5 => '已入库',
                6 => '已打印',
                7 => '已出库',
                8 => '已提取',
                9 => '已签收',
                10 => '已查验',
                11 => '待退货',
                12 => '扣件',
                13 => '已结束',
                16=>'网点已入库'
            );
            if (array_key_exists ( $order->order_status, $err_status )) {
                $ret ['msg'] = '订单状态为【' . $err_status [$order->order_status] . '】';
                return json_encode ( $ret );
            }
            if (strlen($order->pick_company)<=0) {
                $ret ['msg'] = '错误';
                return json_encode ( $ret );
            }
            $order->order_status='16';
            $order->save();
            $ret ['msg'] = '网点入库成功';
            $ret ['sound'] = 'wangdianrukuchenggong.mp3';
            $ret ['status'] = true;
            
            return json_encode ( $ret );
            exit ();
        }
    }   
    /**
     * 取件邮编
     */
    function actionZipCode(){
    	//查询当前登录人员业务相关部门
    	$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
    	//获取部门名称
    	$relevant_department_names=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_name', 'department_name');
    	$relevant_department_names=array_diff($relevant_department_names, array('技术中心','杭州仓','上海仓','义乌仓','广州仓','财务中心','南京仓'));
    	$product = Helper_Array::toHashmap(Product::find()->asArray()->getAll(), 'product_name','product_chinese_name');
    	$department_names = array();
    	foreach ($relevant_department_names as $relevant_department_name){
    		$department_names[$relevant_department_name]= $relevant_department_name;
    	}
    	$department_names['吉通同城']='吉通同城';
    	$department_names['德邦快递']='德邦快递';
    	$department_names['申通国际']='申通国际';
    	$zip_code = Zipcode::find();
    	if(request('zip_code_low')){
    		$zip_code = $zip_code->where('zip_code_low >= ?',request('zip_code_low'));
    	}
    	if(request('zip_code_high')){
    		$zip_code = $zip_code->where('zip_code_high <= ?',request('zip_code_high'));
    	}
    	if(request('pick_company')){
    		$zip_code = $zip_code->where('pick_company = ?',request('pick_company'));
    	}
    	if(request('product')){
    		$zip_code = $zip_code->where('service_code = ?',request('product'));
    	}
    	if(request('warehouse_code')){
    		$zip_code = $zip_code->where('warehouse = ?',request('warehouse_code'));
    	}
    	if(request("export")=='exportlist'){
    		$data = clone $zip_code;
    		$zip_code_data = $data->getAll();
    		$header = array (
    			'省份',
    			'城市',
    			'起始邮编',
    			'截止邮编',
    			'取件网点',
    			'仓库代码',
    			'产品代码',
    		);
    		$sheet = array (
    			$header
    		);
    		foreach ($zip_code_data as $value){
    			$row =array(
    				$value->province,
    				$value->area,
    				$value->zip_code_low,
    				$value->zip_code_high,
    				$value->pick_company,
    				$value->warehouse,
    				$value->service_code,
    			);
    			$sheet [] = $row;
    		}
    		Helper_ExcelX::array2xlsx ( $sheet, '取件邮编列表' );
    		exit ();
    		
    	}
    	$pagination = null;
    	$list=$zip_code->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
    	->fetchPagination ( $pagination )->getAll();
    	$this->_view['zip_code']=$list;
    	$this->_view['pagination']=$pagination;
    	$this->_view['relevant_department_names']=$department_names;
    	$this->_view['product']=$product;
    }
    //修改邮编
    function actionEditZipCode(){
    	//查询当前登录人员业务相关部门
    	$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
    	//获取部门名称
    	$relevant_department_names=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_name', 'department_name');
    	$relevant_department_names=array_diff($relevant_department_names, array('技术中心','杭州仓','上海仓','义乌仓','广州仓','财务中心','南京仓'));
    	$product = Helper_Array::toHashmap(Product::find()->asArray()->getAll(), 'product_name','product_chinese_name');
    	$department_names = array();
    	foreach ($relevant_department_names as $relevant_department_name){
    		$department_names[$relevant_department_name]= $relevant_department_name;
    	}
    	$department_names['吉通同城']='吉通同城';
    	$department_names['德邦快递']='德邦快递';
    	$department_names['申通国际']='申通国际';
    	$zipcode = Zipcode::find('zip_code_id = ?',request('zip_code_id'))->getOne();
	    if(request_is_post()){
	        $zipcode_check=Zipcode::find('zip_code_low = ? and zip_code_high = ? and warehouse = ? and service_code = ?',request('zip_code_low'),request('zip_code_high'),request('warehouse_code'),request('product'))->getOne();
	        if(!$zipcode_check->isNewRecord() && $zipcode->isNewRecord()){
	            return $this->_redirectMessage('取件邮编新增失败', '已存在不能重复', url('pickup/zipcode'));
	        }else{
	            $zipcode = Zipcode::find('zip_code_id = ?',request('zip_code_id'))->getOne();
	            $zipcode->zip_code_low = request('zip_code_low');
	            $zipcode->zip_code_high = request('zip_code_high');
	            $zipcode->province = request('province');
	            $zipcode->area = request('area');
	            $zipcode->pick_company = request('pick_company');
	            $zipcode->warehouse = request('warehouse_code');
	            $zipcode->service_code = request('product');
	            $zipcode->save();
	            return $this->_redirectMessage('取件邮编编辑', '成功', url('pickup/zipcode'));
	        }
	    }
	    $this->_view['zipcode']=$zipcode;
	    $this->_view['relevant_department_names']=$department_names;
	    $this->_view['product']=$product;
	}
    //删除邮编
    function actionDelZipCode(){
    	if(request('zip_code_id')){
    	   $zipcode = Zipcode::meta()->destroyWhere('zip_code_id = ?',request('zip_code_id'));
    	   return 'success';
    	}else{
    		return 'error';
    	}
    }
    //保存地址
    function actionSaveAddress(){
    	if (request ( 'order_id' ) && request ( 'address' )) {
			$order = Order::find ( 'order_id = ?', request ( 'order_id' ) )->getOne ();
			if ($order->isNewRecord ()) {
				echo "订单不存在";
				exit ();
			}
			$address_json = json_decode ( request ( 'address' ), true );
			if (isset ( $address_json [0] ['address'] )) {
				$order->sender_street1 = $address_json [0] ['address'];
				$order->sender_street2 = '';
				$order->save ();
				echo 'success';
				exit ();
			}
		}
    }
    /**
     * 取件员管理
     */
    function actionPickUpMember(){
        $select=PickUpMember::find();
        if(request('wechat_id')){
        	$select->where('wechat_id = ?',request('wechat_id'));
        }
        if(request('wechat_no')){
        	$select->where('wechat_no like ?','%'.request('wechat_no').'%');
        }
        if(request('name')){
        	$select->where('name like ?','%'.request('name').'%');
        }
        if(request('gender')){
        	$select->where('gender = ?',request('gender'));
        }
        if(strlen(request('status'))>0){
        	$select->where('status = ?',request('status'));
        }
        $pagination = null;
        $list=$select->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
        ->fetchPagination ( $pagination )
        ->order('id desc')->getAll();
        $this->_view['list']=$list;
        $this->_view['pagination']=$pagination;
    }
    function actionEditPickupMember(){
    	$pickupmember = PickUpMember::find('id = ?',request('id'))->getOne();
    	if(request_is_post()){
	        //判断供应商名称是否存在
	        $member_check = PickUpMember::find('wechat_id = ?',request('wechat_id'))->getOne();
	        if(!$member_check->isNewRecord() && ($member_check->id != request('id'))){
	        	return $this->_redirectMessage('编辑失败', '该微信号已存在', url('/edit',array('supplier_id'=>$supplier->supplier_id)),2);
	        }else{
	            $pickupmember = PickUpMember::find('id = ?',request('id'))->getOne();
	            $pickupmember->wechat_id = request('wechat_id');
	            $pickupmember->wechat_no = request('wechat_no');
	            $pickupmember->name = request('name');
	            $pickupmember->img_url = request('img_url');
	            $pickupmember->gender = request('gender');
	            $pickupmember->status = request('status');
	            $pickupmember->type = request('type');
	            $pickupmember->save();
	            return $this->_redirectMessage('取件员编辑', '取件员编辑成功', url('/PickUpMember'));
	        }
	    }
	    $this->_view['pickupmember']=$pickupmember;
    }
    //删除取件员信息
    function actionDeleteMember(){
    	$pick_up_member_id = request('id');
    	PickUpMember::meta()->destroyWhere('id = ?',$pick_up_member_id);
    	return $this->_redirect(url('pickup/PickUpMember'));
    }
    /**
     * @todo   批量导入取件邮编
     * @author stt
     * @since  June 28th 2020
     */
    function actionBatchZipcodeImport(){
    	set_time_limit ( 0 );
    	if (request_is_post ()) {
    		$errors = array ();
    		$uploader = new Helper_Uploader ();
    		//检查指定名字的上传对象是否存在
    		if (! $uploader->existsFile ( 'file' )) {
    			return $this->_redirectMessage ( '未上传文件','', url ( '/batchzipcodeimport' ) );
    		}
    		//获得文件对象
    		$file = $uploader->file ( 'file' );
    		if (! $file->isValid ( 'xls,xlsx' )) {
    			return $this->_redirectMessage ( '文件格式不正确：xls、xlsx','', url ( '/batchzipcodeimport' ) );
    		}
    		//缓存路径
    		$des_dir = Q::ini ( 'upload_tmp_dir' );
    		$filename = date ( 'YmdHis' ) . 'ZipcodeImport.'.$file->extname ();
    		$file_route = $des_dir . DS . $filename;
    		$file->move ( $file_route );
    		ini_set ( "memory_limit", "3072M" );
    		$xls = Helper_Excel::readFile ( $file_route );
    		$sheets = $xls->toHeaderMap ();
    		//导入的表中有数据
    		Helper_Array::removeEmpty ( $sheets );
    		$errors = array ();
    		foreach($sheets as $k=>$row){
    			foreach ( array (
    				'城市',
    				'起始邮编',
    				'截止邮编',
    				'仓库代码',
    				'产品代码',
    				'取件网点',
    			) as $col ) {
    				@$row [$col] = trim ( @$row [$col] );
    				if (strlen ( @$row [$col] ) == 0) {
    					$errors [$k] [$col] = '不能为空;';
    				}
    			}
    			//重复导入判断
    			$zipcode_check=Zipcode::find('zip_code_low = ? and zip_code_high = ? and warehouse = ? and service_code = ?',$row['起始邮编'],$row['截止邮编'],$row['仓库代码'],$row['产品代码'])->getOne();
    			if(!$zipcode_check->isNewRecord() ){
    				$errors [$k] ['取件邮编'] = '已存在，请勿重复导入';
    			}
    			//取件网点是否存在
    			//查询当前登录人员业务相关部门
    			$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
    			//获取部门名称
    			$relevant_department_names=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_name', 'department_name');
    			$relevant_department_names=array_diff($relevant_department_names, array('技术中心','杭州仓','上海仓','义乌仓','广州仓','财务中心','南京仓'));
    			if (!in_array($row ['取件网点'], $relevant_department_names)){
    				$errors [$k] ['取件网点'] = '不存在';
    			}
    			$warehouse_code=Helper_Array::toHashmap(CodeWarehouse::find()->getAll(), 'warehouse', 'warehouse');
    			if (!in_array($row ['仓库代码'], $warehouse_code)){
    				$errors [$k] ['仓库代码'] = '不存在';
    			}
    			$product = Helper_Array::toHashmap(Product::find()->asArray()->getAll(), 'product_name','product_name');
    			if (!in_array($row ['产品代码'], $product)){
    				$errors [$k] ['产品代码'] = '不存在';
    			}
    		}
    		$this->_view ['errors'] = $errors;
    		if(empty($errors)){
    			foreach ($sheets as $sheet){
    				$zipcode = new Zipcode();
    				$zipcode->zip_code_low = trim($sheet ['起始邮编']);
    				$zipcode->zip_code_high = trim($sheet ['截止邮编']);
    				$zipcode->province = trim($sheet ['省份']);
    				$zipcode->area = trim($sheet ['城市']);
    				$zipcode->pick_company = trim($sheet ['取件网点']);
    				$zipcode->warehouse = trim($sheet ['仓库代码']);
    				$zipcode->service_code = trim($sheet ['产品代码']);
    				$zipcode->save ();
    			}
    			return $this->_redirectMessage ( '导入成功', '',url ( "/batchzipcodeimport" ) );
    		}
    	}
    	
    }
}

