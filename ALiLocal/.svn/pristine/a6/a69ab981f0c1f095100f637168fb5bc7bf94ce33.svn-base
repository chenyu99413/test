<?php
class Controller_Staff extends Controller_Abstract {
	function actionTestFormula(){
		// 下面公式展示例子是，如果目的地是美国，每公斤收取5块，否则超过20kg部分每0.5收3块
		// $r===false 可以判断公式是否正常
		$r=Helper_Formula::parse('IF(country="US",5*CEIL(weight),CEIL((weight-20)/0.5)*3)',array(
			'weight'=>10.5,	//计费重
			'country'=>'US',	//目的地
			'baf'=>0.1,	//燃油费率
			'area'=>6,	//分区号
			'freight'=>120,//基础运费
			'icount'=>3, //件数
		));
		dump($r);
		exit;
	}
	/**
	 * 工作台
	 */
	function actionIndex() {
// 		if( Helper_ismobile::isMobile()){
// 			return $this->_redirect ( url ( 'pda/upload' ) );
// 		}
		if(request("order_no")){
			$order=Order::find('(ali_order_no=? or far_no=? or tracking_no=?) and department_id in (?)',request("order_no"),request("order_no"),request("order_no"),RelevantDepartment::relateddepartmentids())->getOne();
			if(!$order->isNewRecord()){
				return $this->_redirect(url("order/detail",array("order_id"=>$order->order_id)));
			}
		}
		$orders=Order::find("ali_testing_order!= '1' and order_status='1' and need_pick_up='1'");
		//导出取件清单
		if(request("export")=='exportpick'){
			$pick=clone $orders;
			$pick->where("ifnull(need_pick_up,'')='1'");
			$payeds=$pick->getAll();
			$header = array (
				'阿里订单号','订单日期','取件网点','省','市','地址','邮编','姓名','手机号','电话','邮箱','件数'
			);
			$sheet = array (
				$header
			);
			foreach ($payeds as $p){
				$item_count=0;
				foreach ($p->packages as $package){
					$item_count+=$package->quantity;
				}
				$sheet [] =array(
					"'".$p->ali_order_no,Helper_Util::strDate('Y-m-d H:i', $p->create_time),$p->pick_company,$p->sender_state_region_code,$p->sender_city,$p->sender_street1.' '.$p->sender_street2,"'".$p->sender_postal_code,
					$p->sender_name1.' '.$p->sender_name2,"'".$p->sender_mobile,"'".$p->sender_telephone,$p->sender_email,$item_count
				);
			}
			Helper_ExcelX::array2xlsx ( $sheet, '取件清单' );
			exit ();
			 
		}
		
		//审单预警
// 		$before_out_order = Order::find("order_status = '4' || order_status = '5' || order_status = '10' || order_status = '1' || order_status = '14' || order_status = '15' || order_status = '16' ");
// 		if(request('department_id')){
// 		   $before_out_order->where('department_id = ?',request('department_id'));
// 		}
// 		if(request('service_code')){
// 		   $before_out_order->where('service_code = ?',request('service_code'));
// 		}
// 		if(request('order_status')){
// 		    $before_out_order->where('order_status = ?',request('order_status'));
// 		}
// 		$before_out_order = $before_out_order->where('warning_handled="0"')->getAll();
// 		$status = Order::$status;
// 		$warning_order=array();
// 		foreach ($before_out_order as $temp){
// 		    $detail=array(
// 		        'reason'=>array(),
// 		        'order_id'=>$temp->order_id,
// 		        'order_status'=>$temp->order_status,
// 		        'order_create_time'=>$temp->create_time,
// 		        'department_id'=>$temp->department_id,
// 		        'service_code'=>$temp->service_code
// 		    );
// 		    $glasses_flag=false;
// 		    $battery_flag=false;
// 		    $limit_flag=false;
// 		    $FDA_flag=false;
// 		    $all_flag=false;
// 		    foreach ($temp->product as $v){
// 		        if($temp->consignee_country_code=='US'){
// 		            if(strstr($v->product_name_far, '眼镜') || strstr($v->product_name_far, '太阳镜') || strstr($v->product_name, '眼镜') || strstr($v->product_name, '太阳镜')){
// 		                $glasses_flag=true;
// 		            }
// 		            if(strstr($v->product_name_far, '睫毛') || strstr($v->product_name_far, '假睫毛') || strstr($v->product_name, '睫毛') || strstr($v->product_name, '假睫毛')){
// 		                $FDA_flag=true;
// 		            }
// 		        }
// 		        if($v->has_battery){
// 		            $battery_flag=true;
// 		        }
// 		        if($temp->service_code == 'US-FY'){
// 		            if(strstr($v->product_name_far, '车灯') || strstr($v->product_name_far, '大灯') || strstr($v->product_name_far, '头盔') || strstr($v->product_name_far, '刀') || strstr($v->product_name_far, '激光') || strstr($v->product_name, '车灯') || strstr($v->product_name, '大灯') || strstr($v->product_name, '头盔') || strstr($v->product_name, '刀') || strstr($v->product_name, '激光')){
// 		                $limit_flag=true;
// 		            }
// 		        }
// 		        if(strstr($v->product_name_far, '电') || strstr($v->product_name_far, '灯') || strstr($v->product_name_far, '器') || strstr($v->product_name_far, '磁') || strstr($v->product_name, '电') || strstr($v->product_name, '灯') || strstr($v->product_name, '器') || strstr($v->product_name, '磁')){
// 		           $all_flag=true;
// 		        }
// 		    }
// 		    //中美专线，如果品名里含有：“车灯”，“大灯”“头盔”“刀”“激光”
// 		    if($limit_flag){
// 		        $detail['reason'][]='疑似限运品';
// 		    }
// 		    //普货专线、欧美专线、假发专线，目的地为美国的订单，如果品名里含有：“睫毛”，“假睫毛”
// 		    if($temp->service_code == 'Express_Standard_Global' || $temp->service_code == 'EUUS-FY' || $temp->service_code == 'WIG-FY'){
// 		       if($FDA_flag){
// 		          $detail['reason'][]='美国需FDA';
// 		       }
// 		    }
// 		    //EMS专线,当申报总价高于400.00USD时，预警：EMS超400USD
// 		    if($temp->service_code == 'EMS-FY'){
// 		       if($temp->total_amount>400){
// 		          $detail['reason'][]='EMS超400USD';
// 		       }
// 		    }
// 		    //目前所有线路：当品名里含有“电”“灯”“器”“磁”，预警：品名含有#
// 		    if($all_flag){
// 		       $detail['reason'][]='品名含有#';
// 		    }
// 		    //中美专线，订单总价超800USD时，进行预警：超800美金
// 		    if($temp->service_code == 'US-FY'){
// 		        if($temp->total_amount>800){
// 		            $detail['reason'][]='超800美金';
// 		        }
// 		    }
// 		    //欧美专线，目的地为GB的订单，进行预警：GB订单，需要提供交易凭证
// 		    if($temp->service_code == 'EUUS-FY'){
// 		       if($temp->consignee_country_code=='GB'){
// 		           $detail['reason'][]='GB订单，需要提供交易凭证';
// 		       }
// 		    }
// 		    // US 产品品名里含：“眼镜”，“太阳镜”
// 		    if($glasses_flag){
// 		        $detail['reason'][]='US 产品品名里含：“眼镜”，“太阳镜”';
// 		    }
// 		    //检查订单详情里，有产品“带电”
// 		    if($battery_flag){
// 		        $detail['reason'][]='检查订单详情里，有产品“带电”';
// 		    }
// 		    //US 检查电话号码是否不足10位
// 		    if($temp->consignee_country_code=='US'){
// 		        if(strlen($temp->consignee_mobile)<10){
// 		            $detail['reason'][]='US 检查电话号码是否不足10位';
// 		        }
// 		    }
// 		    //BR 检查订单详情里没有提供税号信息
// 		    if($temp->consignee_country_code=='BR'){
// 		        if($temp->tax_payer_id==''){
// 		            $detail['reason'][]='BR 检查订单详情里没有提供税号信息';
// 		        }
// 		    }
// 		    //订单申报总金额超过700.00USD,且报关方式为：QT的订单。
// 		    if($temp->total_amount>700 && $temp->declaration_type=='QT'){
// 		        $detail['reason'][]='订单申报总金额超过700.00USD,且报关方式为：QT的订单';
// 		    }
// 		    //订单申报总金额低于5.00USD的订单
// 		    if($temp->total_amount<1){
// 		        $detail['reason'][]='订单申报总金额低于1.00USD的订单';
// 		    }
// 		    //订单申报方式为：DL
// 		    if($temp->declaration_type=='DL'){
// 		        if($temp->service_code=='EMS-FY'){
// 		            $detail['reason'][]='EMS不提供报关服务';
// 		        }else if($temp->service_code == 'US-FY' || $temp->service_code == 'EUUS-FY'){
// 		            $detail['reason'][]='无报关服务';
// 		        }else{
// 		            $detail['reason'][]='订单申报方式为：DL';
// 		        }
// 		    }
// 		    //地址1+地址2字符总数超过105的订单
// 		    //普货专线和中美专线，地址总字符超过105时，进行预警，EMS不作限制
// 		    if($temp->service_code == 'Express_Standard_Global' || $temp->service_code == 'US-FY'){
// 		        if(strlen($temp->consignee_street1.' '.$temp->consignee_street2)>105){
// 		            $detail['reason'][]='地址1加地址2字符总数超过105的订单';
// 		        } 
// 		    }
// 		    //假发专线地址1+地址2字符总数超过70的订单
// 		    if($temp->service_code=='WIG-FY'){
// 		        if(strlen($temp->consignee_street1.' '.$temp->consignee_street2)>70){
// 		            $detail['reason'][]='全球假发专线地址字符总数超过70的订单';
// 		        }else {
//     		        $address=Order::splitAddressfedex($temp->consignee_street1.' '.$temp->consignee_street2);
//     		        if(count($address)>2){
//     		            $detail['reason'][]='全球假发专线地址字符总数超过70的订单';
//     		        }
// 		        }
// 		    }
// // 		    //判断收件人地址1和地址2 如果包含英文和数字、空格、英文逗号以外的字符
// // 		    if($temp->consignee_street1){
// //     		    if(!preg_match('/^[0-9a-zA-Z,\s\n\r]+$/', $temp->consignee_street1)){
// //     		        $detail['reason'][]='收件人地址1中有特殊字符';
// //     		    }
// // 		    }
// // 		    if($temp->consignee_street2){
// //     		    if(!preg_match('/^[0-9a-zA-Z,\s\n\r]+$/', $temp->consignee_street2)){
// //     		        $detail['reason'][]='收件人地址2中有特殊字符';
// //     		    }
// // 		    }
// 		    //收件人公司里，如果有数字进行预警，预警原因：收件公司信息含有数字
// 		    if($temp->consignee_name1){
// 		        if(preg_match('/\d/',$temp->consignee_name1)){
// 		            $detail['reason'][]='收件公司/客户名称1含有数字';
// 		        }
// 		    }
// 		    if($temp->consignee_name2){
// 		        if(preg_match('/\d/',$temp->consignee_name2)){
// 		            $detail['reason'][]='收件公司/客户名称2含有数字';
// 		        }
// 		    }
// 		    //收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里如有非英文符，进行预警，预警原因：某某字段有非英文字符。
// 		    //地址1、地址2、城市、省州、邮编这五个信息里：英文状态下以下符号不作预警："#" "/" "" "-" "." "," "&" "*" ";" ":" "?" ";" ""
// 		    if($temp->consignee_name1){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$temp->consignee_name1)){
// 		            $detail['reason'][]='收件公司/客户名称1有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_name2){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$temp->consignee_name2)){
// 		            $detail['reason'][]='收件公司/客户名称2有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_street1){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$temp->consignee_street1)){
// 		            $detail['reason'][]='收件人地址1有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_street2){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$temp->consignee_street2)){
// 		            $detail['reason'][]='收件人地址2有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_city){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$temp->consignee_city)){
// 		            $detail['reason'][]='收件人城市有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_state_region_code){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$temp->consignee_state_region_code)){
// 		            $detail['reason'][]='收件人省/州有非英文字符';
// 		        }
// 		    }
// 		    if($temp->consignee_postal_code){
// 		        if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$temp->consignee_postal_code)){
// 		            $detail['reason'][]='收件人邮编有非英文字符';
// 		        }
// 		    }
// 		    if($temp->suspected_remote=='1'){
// 		            $detail['reason'][]='城市疑似偏远，需人工介入';
// 		    }
// 		    if($temp->black_flag=='1'){
// 		        $detail['reason'][]='线路:'.$temp->service_product->product_chinese_name.','.$temp->black_reason.'黑名单';
// 		    }
// 		    if($temp->zip_flag=='1'){
// 		        $detail['reason'][]='邮编异常';
// 		    }
// 		    // EMS订单邮编校验
// 		    if($temp->service_code == 'EMS-FY'){
// 		        $zipFormat = Zipformat::find('country_code_two = ?',$temp->consignee_country_code)->getOne();
// 		        if(!$zipFormat->isNewRecord()){
// 		            if(!preg_match($zipFormat->zip_format_preg_match, trim($temp->consignee_postal_code))){
// 		                $detail['reason'][]='收件人邮编格式不正确,'.$temp->consignee_country_code.'的邮编格式为：'.$zipFormat->zip_format;
// 		            }
// 		        }
// 		    }
		    
// 		    if(count($detail['reason'])>0){
// 		        $detail['ali_order_no']=$temp->ali_order_no;
// 		        $detail['order_id']=$temp->order_id;
		        
// 		        $reason = implode(',', $detail['reason']);
// 		        $issue = Abnormalparcel::find('ali_order_no = ? and issue_type="5" and issue_content=?',$detail['ali_order_no'],$reason)->getOne();
// 		        if ($issue->isNewRecord ()) {
// 					//新建问题件
// 					$now = 'ISSUE' . date ( 'Ym' );
// 					$seq = Helper_Seq::nextVal ( $now );
// 					if ($seq < 1) {
// 						Helper_Seq::addSeq ( $now );
// 						$seq = 1;
// 					}
// 					$seq = str_pad ( $seq, 4, "0", STR_PAD_LEFT );
// 					$abnormal_parcel_no = date ( 'Ym' ) . $seq;
// 					$abnormal_parcel = new Abnormalparcel ( array (
// 						'ali_order_no' => $detail ['ali_order_no'],
// 						'abnormal_parcel_no' => $abnormal_parcel_no,
// 						'abnormal_parcel_operator' => MyApp::currentUser ( 'staff_name' ),
// 						'issue_type' => '5',
// 						'issue_content' => implode ( ',', $detail ['reason'] ) 
// 					) );
// 					$abnormal_parcel->save ();
// 					$history = new Abnormalparcelhistory ();
// 					$history->abnormal_parcel_id = $abnormal_parcel->abnormal_parcel_id;
// 					$history->follow_up_content = implode ( ',', $detail ['reason'] );
// 					$history->follow_up_operator = MyApp::currentUser ( "staff_name" );
// 					$history->save ();
// 				}else{
// 					continue;
// 				}
// 		    }
// 		}
// 		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
// 		$service = Product::find()->getAll()->toHashMap('product_name','product_chinese_name');
// 		$list=$orders->order('create_time desc')->getAll();
// 		$this->_view['warning_orders']=$warning_order;
// 		$this->_view['status']=$status;
// 		$this->_view['orders']=$list;
// 		$this->_view['dpms']=$dpms;
// 		$this->_view['service']=$service;
	}
	/**
	 * 用户登录
	 */
	function actionLogin() {
		if (request_is_post ()) {
			$staff = Staff::find ( 'staff_code=?', request ( 'staff_code' ) )->getOne ();
			session_start ();
			// 将登录用户的信息存入 SESSION，以便应用程序记住用户的登录状态
			$this->_app->changeCurrentUser ( $staff->toArray (), "MEMBER" );
			// 登录成功后，重定向浏览器
			return $this->_redirect ( url ( "staff/index" ) );
		}
		if (MyApp::currentUser ()) {
			// session 信息不为空，则跳至工作台
			return $this->_redirect ( url ( "staff/index" ) );
		}
	}
	/**
	 * 校验用户登录信息
	 */
	function actioncheck(){
	    $message='true';
	    if ((post ( "account" ) == '') || (post ( "password" ) == '')) {
	        $message = "用户名和密码不能为空";
	    }
	    $staff = Staff::find ( "staff_code = ?", request('account') )->getOne ();
	    if ($staff->isNewRecord ()) {
	        $message='用户名不存在';
	    }
	    if ($staff->status!='1') {
	        $message='账号已停用';
	    }
	    if (!$staff->checkPassword ( request('password') )) {
	        $message='密码错误';
	    }
	    echo $message;
	    exit();
	}
	/**
	 * 用户注销
	 */
	function actionLoginout() {
	    session_start();
	    $this->_app->cleanCurrentUser ();
	    return $this->_redirectMessage('注销登录','成功', url ( 'staff/login' ) );
	}
	/**
	 * 员工查询
	 */
	function actionSearch() {
	    $staffs=Staff::find();
	    if(request('staff_name')){
	        $staffs->where('staff_name=?',request('staff_name'));
	    }
	    //新增工号搜索条件
	    if(request('staff_code')){
	    	$staffs->where('staff_code=?',request('staff_code'));
	    }
	    $staffs=$staffs->getAll();
	    $this->_view['staffs']=$staffs;
	}
	/**
	 * 员工编辑
	 */
	function actionEdit(){
		$staff = Staff::find ( 'staff_id=?', request ( 'staff_id' ) )->getOne ();
		$relevants = array ();
		$staffrole = array ();
		$staffroles = array();
		$contect = '';
		$new_diff_str = '';
		$old_diff_str = '';
		$old_staffrole_str = '';
		$new_staffrole_str = '';
		$role_arr = Helper_Array::toHashmap ( Role::find ()->asArray ()
			->getAll (), 'role_id', 'role_name' );
		$department_arr = Helper_Array::toHashmap ( Department::find ()->asArray ()
			->getAll (), 'department_id', 'department_name' );
		if (request ( "staff_id" ) != null) {
			$relevants = RelevantDepartment::find ( "staff_id = ?", $staff->staff_id )->setColumns ( 'department_id' )
				->asArray ()
				->getAll ();
			$relevants = Helper_Array::getCols ( $relevants, 'department_id' );
			$staffrole = StaffRole::find ( "staff_id = ?", $staff->staff_id )->setColumns ( 'role_id' )
				->asArray ()
				->getAll ();
			$staffroles = Helper_Array::getCols ( $staffrole, 'role_id' );
		}
		// 保存
		if (request_is_post ()) {
			$new_relevants = array ();
			$new_staffrole = array ();
			$conn = QDB::getConn ();
			$conn->startTrans ();
			$department = Department::find ( "department_id=?", request ( 'department' ) )->getOne ();
			if ($staff->department_id != request ( 'department' )) {
				$contect .= '员工部门变更：' . $staff->department_name . ' > ' . $department->department_name . ' ; ';
			}
			// 用户
			$staff->staff_code = request ( 'staff_code' );
			$staff->staff_name = request ( 'staff_name' );
			$staff->department_id = request ( 'department' );
			$staff->department_name = $department->department_name;
			if (request ( 'password' ) != $staff->password) {
				$staff->password = request ( 'password' );
			}
			$staff->status = "1";
			$staff->save ();
			
			// 权限角色
			if (request ( "role" ) != null || strlen ( request ( "role" ) ) > 0) {
				StaffRole::meta ()->destroyWhere ( "staff_id = ?", $staff->staff_id );
				foreach ( request ( "role" ) as $value ) {
					$staffrole = new StaffRole ();
					$staffrole->role_id = $value;
					$staffrole->staff_id = $staff->staff_id;
					$staffrole->save ();
					$new_staffrole [] = $value;
				}
			}
			// 相关部门
			if (request ( "relevant" ) != null || strlen ( request ( "relevant" ) ) > 0) {
				RelevantDepartment::meta ()->destroyWhere ( "staff_id = ?", $staff->staff_id );
				foreach ( explode ( ",", request ( "relevant" ) ) as $value ) {
					$relevantDepartment = new RelevantDepartment ();
					$relevantDepartment->staff_id = $staff->staff_id;
					$relevantDepartment->department_id = $value;
					$relevantDepartment->save ();
					$new_relevants [] = $value;
				}
			}
			$new_diff = array_diff ( $new_relevants, $relevants );
			$old_diff = array_diff ( $relevants, $new_relevants );
			$new_roles = array_diff ( $new_staffrole, $staffroles );
			$old_roles = array_diff ( $staffroles, $new_staffrole );
			foreach ( $new_diff as $new ) {
				$new_diff_str .= $department_arr [$new] . ',';
			}
			foreach ( $old_diff as $old ) {
				$old_diff_str .= $department_arr [$old] . ',';
			}
			foreach ( $new_roles as $new_role ) {
				$new_staffrole_str .= $role_arr [$new_role] . ',';
			}
			foreach ( $old_roles as $old_role ) {
				$old_staffrole_str .= $role_arr [$old_role] . ',';
			}
			$contect .= (strlen ( trim ( $new_staffrole_str, ',' ) ) > 0 ? '员工权限角色增加：' . trim ( $new_staffrole_str, ',' ) . ' ; ' : '') . (strlen ( trim ( $old_staffrole_str, ',' ) ) > 0 ? '员工权限角色减少：' . trim ( $old_staffrole_str, ',' ) . ' ; ' : '') . (strlen ( trim ( $new_diff_str, "," ) ) > 0 ? '业务相关部门新增：' . trim ( $new_diff_str, "," ) . ' ; ' : '') . (strlen ( trim ( $old_diff_str, "," ) ) > 0 ? '业务相关部门减少：' . trim ( $old_diff_str, "," ) . ' ; ' : '');
			if (strlen ( $contect ) > 0) {
				$staffeditlog = new StaffEditLog ();
				$staffeditlog->edit_staff_id = $staff->staff_id;
				$staffeditlog->edit_staff_name = $staff->staff_name;
				$staffeditlog->edit_contect = $contect;
				$staffeditlog->edit_time = time ();
				$staffeditlog->operator_name = MyApp::currentUser ( 'staff_name' );
				$staffeditlog->save ();
			}
			$conn->completeTrans ();
			return $this->_redirectMessage ( "员工保存", "保存成功", url ( "staff/edit", array (
				"staff_id" => $staff->staff_id 
			) ) );
		}
		// 显示角色
		$staffrole_id = $staffroles;
		$roles = array ();
		foreach ( Role::find ()->getAll () as $value ) {
			if (in_array ( $value->role_id, $staffrole_id )) {
				$roles [] = array (
					"id" => $value->role_id,
					"name" => $value->role_name,
					"checked" => true 
				);
			} else {
				$roles [] = array (
					"id" => $value->role_id,
					"name" => $value->role_name,
					"checked" => false 
				);
			}
		}
		$this->_view ["roles"] = $roles;
		$this->_view ['staff'] = $staff;
		$this->_view ["relevants"] = array (
			"state" => true,
			"checked" => implode ( ",", $relevants ) 
		);
	}
	/**
	 * 修改日志
	 */
	function actionEditLog(){
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
		$select = StaffEditLog::find ()->order ( 'id desc' );
		if(request("staff_name")){
			$select->where("edit_staff_name like ?",'%'.request("staff_name").'%');
		}
		if(request("start_time")){
			$select->where("edit_time >= ?",strtotime(request("start_time").' 00:00:00'));
		}
		if(request("end_time")){
			$select->where("edit_time <= ?",strtotime(request("end_time").' 23:59:59'));
		}
		if(request("operator")){
			$select->where("operator_name like ?",'%'.request("operator").'%');
		}
		$logs = $select->limitPage ( $page, $page_size )
			->fetchPagination ( $pagination )
			->getAll ();

		$this->_view ['logs'] = $logs;
		$this->_view ['pagination'] = $pagination;
	}
	/**
	 * 停职操作
	 */
	function actionInterdicted() {
		if(request ( "status" )=='1'){
			return $this->_redirectMessage ( "失败", "如需启用请发送钉钉申请", url ( "staff/search" ),5 );
		}else{
		    if (request ( "staff_id" ) != null) {
		    	$contect = '员工状态变更：';
		    	$staff_status = '';
		    	$new_status = (request ( "status" )=='0')?'禁用':'启用';
		        $staff = staff::find ( "staff_id = ?", request ( "staff_id" ) )->getOne ();
		        $staff_status = ($staff->status=='0')?'禁用':'启用';
		        $staff->status = request ( "status" );
		        $staff->save ();
		        if(request ( "status" )=='0'){
		        	$url="http://oa.far800.com/api/CancelProject";
		        	$user=array(
		        		'project_name'=>'阿里专线',
		        		'user_account'=>$staff->staff_code
		        	);
		        	$res=Helper_Curl::post($url, json_encode($user));
		        }
		        $staff_edit_log = new StaffEditLog();
		        $staff_edit_log->edit_staff_id = $staff->staff_id;
		        $staff_edit_log->edit_staff_name = $staff->staff_name;
		        $staff_edit_log->edit_contect = $contect.$staff_status.' > '.$new_status;
		        $staff_edit_log->edit_time = time();
		        $staff_edit_log->operator_name = MyApp::currentUser('staff_name');
		        $staff_edit_log->save();
		    }
		    return $this->_redirectMessage ( "用户状态", "保存成功", url ( "staff/search" ),3 );
		}
	}
	/**
	 * 判断员工工号唯一
	 */
	function actionCodecheck(){
	    $staff=Staff::find('staff_code=?',request('staff_code'))->getOne();
	    if(!$staff->isNewRecord() && $staff->staff_id!=request('staff_id')){
	        echo 'error';
	    }else{
	        echo 'success';
	    }
	    exit();
	}
	/**
	 * 轨迹匹配规则
	 */
	function actionMatchRules(){
		$select=RouteMatchRule::find();
		if (request('network_code')){
		    $select->where('network_code = ?',request('network_code'));//网络
		}
		if (request('ali_code')){
			$select->where('ali_code like ?','%'.request('ali_code').'%');//阿里代码模糊查询
		}
		if (request('keyword')){
		    $select->where('keyword like ?','%'.request('keyword').'%');//关键字模糊查询
		}
		if(request("export")=='exportlist'){
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '2G');
			set_time_limit(0);
			$rus=clone $select;
			$lists=$rus->order('is_priority asc,sort asc, id asc')->getAll();
			//与导入保持一致
			$header = array (
				'网络','自动发送','阿里代码','关键字','中文描述','英文描述','是否优先匹配','排序'
			);
			$sheet = array (
				$header
			);
			foreach ($lists as $l){
				if($l->auto==1){
					$flag='是';
				}else{
					$flag='否';
				}
				//导出增加“排序”
				$row =array(
					$l->network_code,
					$flag,
					$l->ali_code,
					$l->keyword,
					$l->cn_desc,
					$l->en_desc,
					$l->is_priority==1?'是':'否',
					$l->sort,
				);
				$sheet [] = $row;
			}
			Helper_ExcelX::array2xlsx ( $sheet, '轨迹匹配表' );
			exit ();
		}
		
		$rules=$select->limitPage(request('page',1),request( 'page_size', 30 ))
			->fetchPagination($this->_view['pagination'])
			->order('is_priority asc,sort asc, id asc')
			->getAll();
		$this->_view['rules']=$rules;
	}
	/**
	 * 轨迹匹配规则编辑
	 */
	function actionMatchRulesEdit(){
	    $select=RouteMatchRule::find('id = ?',request('route_matchrules_id'))->getOne();
	    if(request_is_post()){
	        $select->network_code=request('network_code');
	        $select->keyword=request('keyword');
	        $select->auto=request('auto')?1:0;
	        $select->is_priority=request('is_priority');
	        $select->sort=request('sort');
	        $select->ali_code=request('ali_code');
	        $select->cn_desc=request('cn_desc');
	        $select->en_desc=request('en_desc');
	        $select->save();
	        return $this->_redirectMessage('轨迹匹配规则编辑', '保存成功', url('/MatchRulesEdit',array('route_matchrules_id'=>$select->id)));
	    }
	    $this->_view['info']=$select;
	}
	/**
	 * 轨迹匹配规则删除
	 */
	function actionMatchRulesDel(){
	    RouteMatchRule::find('id=?',request('route_matchrules_id'))->getOne()->destroy();
	    return $this->_redirectMessage('轨迹匹配规则', '删除成功', url('/MatchRules'));
	}
	/**
	 * @todo   轨迹匹配规则导入
	 * @author stt
	 * @since  2020-10-19
	 * @param
	 * @return
	 * @link   #81897
	 */
	function actionMatchRulesImport(){
		set_time_limit(0);
		ini_set('memory_limit', '-1');//不限制内存
		if(request_is_post ()){
			$errors = array ();
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('/matchrulesimport'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('/matchrulesimport'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = date ( 'YmdHis' ).'MatchRulesImport.'.$file->extname ();
			$file_route = $des_dir.DS.$filename;
			$file->move ( $file_route );
			$xls = Helper_Excel::readFile ($file_route,true);
			$sheet =$xls->toHeaderMap ();
			//导入的表中有数据
			$arr=array();
			if(count($sheet)<1){
				return $this->_redirectMessage('内容不能为空','',url('/matchrulesimport'));
			}
// 			$sort = array_column($sheet,'排序');
// 			array_multisort($sort,SORT_DESC,$sheet);
			foreach ($sheet as $key => $row){
				if(!strlen($row ['网络'])){
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='网络必填';
					continue;
				}
				if(!strlen($row ['阿里代码'])){
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='阿里代码必填';
					continue;
				}
				if(!strlen($row ['关键字'])){
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='关键字必填';
					continue;
				}
				
				$network = Network::find('network_code = ?',$row ['网络'])->getOne();
				if($network->isNewRecord()){
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='网络不存在';
					continue;
				}
			}
			$this->_view['sheet']=$arr;
			//无错误
			if(empty($arr)){
				//现在系统不是全量更新，而是增量更新，调整成全量更新（删除原来所有，按导入的新增所有）
				RouteMatchRule::find()->getAll()->destroy();
				foreach ($sheet as $key => $row){
					//将数据存入数据库
					$postalbook = new RouteMatchRule();
					//网络
					$postalbook->network_code = $row ['网络'];
					$postalbook->auto = $row ['自动发送']=='是'?1:0;
					$postalbook->keyword = $row ['关键字'];
					$postalbook->ali_code = $row ['阿里代码'];
					$postalbook->cn_desc = $row ['中文描述'];
					$postalbook->en_desc = $row ['英文描述'];
					$postalbook->sort = $row ['排序']?$row ['排序']:9999;
					$postalbook->is_priority = $row ['是否优先匹配']=='是'?1:2;
					$postalbook->save();
				}
				//无错误跳转
				return $this->_redirectMessage('导入成功','',url('/matchrulesimport'));
			}
		}
	}
	/*
	 * 未处理预警改未已处理
	 */
	function actionhandled(){
	    $order_id=request('order_id');
	    $order=Order::find('order_id=?',$order_id)->getOne();
	    if($order->isNewRecord()){
	        return false;
	    }else {
	        $order->warning_handled='1';
	        $order->save();
	        return 'success';
	    }
	}
	/**
	 *  发件人管理
	 */
	function actionsender(){
	    $select = Sender::find();
	    if(request('channel_id')){
	    	$channel = Channel::find('channel_id=?',request('channel_id'))->getOne();
	    	$sender_id_arr = explode(',', $channel->sender_id);
	    	$select->where('sender_id in (?)',$sender_id_arr);
	    }
	    //退件渠道进入
	    if(request('return_channel_id')){
	    	//查询退件渠道的信息
	    	$channel = ReturnChannel::find('channel_id=?',request('return_channel_id'))->getOne();
	    	$sender_id_arr = explode(',', $channel->sender_id);
	    	$select->where('sender_id in (?)',$sender_id_arr);
	    }
	    $select = $select->getAll();
	    $this->_view['senders']=$select;
	}
	/**
	 * 新建发件人
	 */
	function actionsenderEdit(){
	    $request_sender=request ( "sender" );
	    if(request('sender_id')){
	        $sender = Sender::find('sender_id = ?',request('sender_id'))->getOne();
	        if($sender->isNewRecord()){
	            return $this->_redirectMessage('获取发件人信息失败', '失败', url('/sender'));
	        }
	        if(request_is_post()){
	            $check = Sender::find ( "sender_code != ? and sender_code = ?", $sender->sender_code, $request_sender['sender_code'] )->getOne ();
	            if (! $check->isNewRecord ()) {
	                return $this->_redirectMessage('失败', '发件人代码已存在', url('/senderEdit',array('sender_id'=>$sender->sender_id)));
	            }
	            //保存信息
	            $sender->changeProps ( request ( "sender" ) );
	            $sender->save();
	            return $this->_redirectMessage('修改成功', '成功', url('/senderEdit',array('sender_id'=>$sender->sender_id)));
	        }else{
	            $this->_view['sender']=$sender;
	        }
	    }else{
	        $sender = new Sender();
	        if(request_is_post()){
	            $check = Sender::find ( "sender_code = ?", $request_sender['sender_code'] )->getOne ();
	            if (! $check->isNewRecord ()) {
	                return $this->_redirectMessage('失败', '发件人代码已存在', url('/senderEdit',array('sender_id'=>$sender->sender_id)));
	            }
	            $sender->changeProps ( request ( "sender" ) );
	            $sender->save();
	            return $this->_redirectMessage('保存成功', '成功', url('/senderEdit',array('sender_id'=>$sender->sender_id)));
	        }else{
	            $this->_view['sender']=$sender;
	        }
	    }
	    
	}
	function actionConfig(){
	    if (request_is_post()){
	        foreach (request('set') as $k => $v){
	            Config::set($k, $v);
	        }
	        return $this->_redirectMessage('成功', '保存成功', url('/config'));
	    }
	}
	
	/* function actionGetInfo(){
		$staff = Staff::find()->getAll();
		foreach ($staff as $s){
			$role_id=StaffRole::find('staff_id=?',$s->staff_id)->getAll();
			$role_id=Helper_Array::getCols($role_id, 'role_id');
			if(!count($role_id))
				$role_id='';
			$role=Role::find('role_id in (?)',$role_id)->setColumns('role_name')->getAll();
			$role_name=Helper_Array::getCols($role, 'role_name');
			$role_name=join(',', $role_name);
			
			echo $s->staff_code.'|'.$s->staff_name.'|'.$s->status.'|'.date('Y-m-d H:i:s',$s->create_time).'|'.$role_name.'<br>';
		}
		exit;
	} */
}