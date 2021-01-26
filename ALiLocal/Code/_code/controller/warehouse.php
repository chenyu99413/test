<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';

class Controller_Warehouse extends Controller_Abstract {
	/**
	 * 包裹入库
	 */
	function actionIn() {
		
	}
	function actionInScan() {
		header ( 'Content-Type:application/json' );
		$ret = array (
			'code' => '1000',
			'msg' => '',
			'sound' => 'cuowu.mp3',
			'data' => '',
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
					//查询订单信息
					$ali_no_list=Alireference::find('tb_ali_reference.reference_no=? ',request ( 'scan_no' ))
					->joinLeft('tb_order', '*','tb_ali_reference.order_id=tb_order.order_id')
					->joinLeft('tb_order_package', '*','tb_ali_reference.order_id=tb_order_package.order_id')
					->group('tb_order.ali_order_no')->sum('quantity','package_count')->columns('tb_order.ali_order_no')->asArray()->getAll();
					$ret['info']=$ali_no_list;
					$ret['sum']['package_count']=Helper_Array::sumBy($ali_no_list,'package_count');
					$ret['sum']['order_count']=$count2;
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
			$ret ['sound'] = 'qingdengjiwuzhujian.mp3';
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
			13 => '已结束'
		);
		if (array_key_exists ( $order->order_status, $err_status )) {
			if($order->order_status=='2'){
				$ret ['msg'] = '订单已取消';
				$ret ['sound'] = 'qingkoujian.mp3';
			}else{
				$ret ['msg'] = '订单状态为【' . $err_status [$order->order_status] . '】';
			}
			return json_encode ( $ret );
		}
		$order_packages = Orderpackage::find ( 'order_id = ?', $order->order_id )->getAll ();
		$far_packages = Farpackage::find ( 'order_id = ?', $order->order_id )->getAll ();
		$product = Product::find('product_name=?',$order->service_code)->getOne();
		if($product->isNewRecord()){
			$ret ['msg'] = $order->service_code.'此产品无服务';
			$ret ['sound'] = 'cichanpingwufuwu.mp3';
			return json_encode ( $ret );
		}
		//检查应收燃油
		if($product->check_fuel=='1'){
			$productfuel = Productfuel::find ( "product_id = ?", $product->product_id )
			->where("effective_date <= ? and fail_date >= ?",time(),time())->getOne ();
			if($productfuel->isNewRecord()){
				$ret ['msg'] = '没有设置燃油';
				$ret ['sound'] = 'meiyoushezhiranyou.mp3';
				return json_encode ( $ret );
			}
		}
		/* if(in_array($order->service_code, array('Express_Standard_Global','US-FY'))){
		 $poboxs = array('POBOX','PO BOX','P.O. BOX','P.O BOX');
		 foreach ($poboxs as $pobox){
		 if(strpos(strtoupper($order->consignee_street1), $pobox) !==false || strpos(strtoupper($order->consignee_street2), $pobox) !==false){
		 $ret ['msg'] = 'POBOX商业型快递无法送达';
		 $ret ['sound'] = 'poboxwufadisong.mp3';
		 return json_encode ( $ret );
		 }
		 }
		 } */
		$ret ['code'] = '0';
		if($order->service_code=='EMS-FY') {
			$ret ['msg'] = 'EMS，扫描成功，共 ' . Helper_Array::sumBy ( $order_packages->toArray (), 'quantity' ) . ' 个包裹';
			//判断是否是阿里单号 不是则播报特殊语音
			if(request ( 'scan_no' ) == $order->ali_order_no){
				$ret ['sound'] = 'emschenggongqingkaishichengzhong.mp3';
			}else{
				$ret ['sound'] = 'emschenggongqingkaishichengzhongals.mp3';
			}
		}else {
			$ret ['msg'] = '扫描成功，共 ' . Helper_Array::sumBy ( $order_packages->toArray (), 'quantity' ) . ' 个包裹';
			//判断是否是阿里单号 不是则播报特殊语音
			//
			if(request ( 'scan_no' ) == $order->ali_order_no){
				$ret ['sound'] = 'chenggongqingkaishichengzhong.mp3';
			}else{
				$ret ['sound'] = 'chenggongqingkaishichengzhongals.mp3';
			}
		}
		
		$receivable_formula = Receivableformula::find('product_id=? and package_type=?',$product->product_id,$order->packing_type)->setColumns('fee_name')->getAll();
		$receivable_formula = Helper_Array::getCols($receivable_formula,'fee_name');
		$ret ['data'] = array (
			'order' => $order->toArray (),
			'order_package' => $order_packages->toArray (),
			'far_package' => $far_packages->toArray (),
			'product' => $product->toArray(),
			'formula'=>$receivable_formula,
		);
		$ret ['status'] = true;
		
		return json_encode ( $ret );
		exit ();
	}
	/**
	 * 入库 保存
	 */
	function actionInSave() {
		header ( 'Content-Type:application/json' );
		$arr = json_decode ( request ( 'jsonstr' ), true );
		QLog::log ( print_r ( $arr, true ) );
		$ret = array (
			'code' => '1000',
			'msg' => '',
			'sound' => 'rukushibai.mp3',
			'data' => '',
			'status' => false
		);
		try {
			$order = Order::find ( 'order_id = ?', $arr ['order_id'] )->getOne ();
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
				13 => '已结束'
			);
			if (array_key_exists ( $order->order_status, $err_status )) {
				$ret ['msg'] = '订单状态为【' . $err_status [$order->order_status] . '】';
				return json_encode ( $ret );
			}
			Farpackage::meta ()->deleteWhere ( 'order_id=?', $order->order_id );
			$package_total_in=0;
			foreach ( $arr ['package_list'] as $value ) {
				$far_package = new Farpackage ();
				$far_package->order_id = $order->order_id;
				$far_package->weight = $value ['weight'];
				$far_package->length = $value ['length'];
				$far_package->width = $value ['width'];
				$far_package->height = $value ['height'];
				$far_package->quantity = $value ['quantity'];
				$far_package->save ();
				$package_total_in += $value ['quantity'];
			}
			$weightarr = Helper_Quote::getweightarr($order, 1);
			
			//入库扫描单号
			$order->scan_no_in = request('scan_no');
			
			//是否计泡:1代表本票货物是泡货，只要有一个包裹是泡货，这里就填1
			$order->volumn_chargeable = $weightarr['is_jipao'];
			
			//入库的包裹总计费重，用于计算应收
			$order->weight_income_in = $weightarr['total_cost_weight'];
			
			//入库的包裹总实重
			$order->weight_actual_in = $weightarr['total_real_weight'];
			
			//入库的包裹总体积重
			$order->total_volumn_weight = $weightarr['total_volumn_weight'];
			$order->package_total_in=$package_total_in;
			$order->packagenum = $arr ['EX0002'];
			$order->boxnum = $arr ['EX0003'];
			$order->specialpackagenum = $arr ['EX0034'];
			$order->save ();
			if(count($arr ['package_list'])==1){
				if($arr ['package_list']['package0']['length']==22 && $arr ['package_list']['package0']['width']==22 && $arr ['package_list']['package0']['height']==2.22 && $arr ['package_list']['package0']['quantity']==1){
					if($order->service_code=='Express_Standard_Global'){
						$order->packing_type='PAK';
					}
				}
			}
			$order->order_status = '5'; // 5 入库
			$productcodes=Product::getprodutcode(2);
			if(in_array($order->service_code,$productcodes)){
				$order->add_data_status='1';
			}
			
			$order->department_id = MyApp::currentUser('department_id');
			$order->warehouse_in_department_id = MyApp::currentUser('department_id');
			$order->far_warehouse_in_time=time();
			$order->far_warehouse_in_operator=MyApp::currentUser('staff_name');
			$order->save ();
			$event_location = '';
			$department = Department::find ( 'department_id=?', MyApp::currentUser ( 'department_id' ) )->getOne ();
			if ($department->department_name == '杭州仓') {
				$event_location = '杭州';
			} elseif ($department->department_name == '义乌仓') {
				$event_location = '义乌';
			} elseif ($department->department_name == '上海仓') {
				$event_location = '上海';
			}elseif ($department->department_name == '广州仓') {
				$event_location = '广州';
			}elseif ($department->department_name == '青岛仓') {
				$event_location = '青岛';
			}elseif ($department->department_name == '深圳仓') {
				$event_location = '深圳';
			}elseif ($department->department_name == '南京仓') {
				$event_location = '南京';
			}elseif ($department->department_name == '连云港仓') {
				$event_location = '连云港';
			}
			$event_time = time()- rand(5, 7)*60+rand(1, 59);
			$helper_event = new Helper_Event();
			if($order->customer->customs_code=='ALPL'){
				$in=Event::find('order_id= ? and event_code="SORTING_CENTER_INBOUND_CALLBACK"',$order->order_id)->getOne();
				if($in->isNewRecord()){
					$event_code = 'SORTING_CENTER_INBOUND_CALLBACK';
					$helper_event->saveEvent($order->customer_id, $order->order_id, $event_code,$event_time , $event_location );
				}
			}else{
				$in=Event::find('order_id= ? and event_code="WAREHOUSE_INBOUND"',$order->order_id)->getOne();
				if($in->isNewRecord()){
					$this->eventSave ( $order->order_id, 'WAREHOUSE_INBOUND',$event_time , $event_location ,$order->customer_id);
				}
			}
			$order->warehouse_in_time =$event_time;
			$order->save();
		} catch ( Exception $e ) {
			$ret ['msg'] = '发生异常，请稍后重新操作此单【' . $order->ali_order_no . '】';
			return json_encode ( $ret );
		}
		//创建小标签PDF
		Helper_Common::createfarlittlelabel($order);
		// 		if ($order->declaration_type == 'DL' || $order->total_amount > 700 || $order->weight_actual_in > 70) {
		// 			$ret ['msg'] = '报关件，请操作下一单';
		// 			$ret ['sound'] = 'baoguanjian.mp3';
		// 		} else {
		// 
		$ret ['msg'] = '入库成功，请操作下一单';
		$ret ['sound'] = 'rukuchenggong.mp3';
		// 		}
		$ret ['code'] = '0';
		
		$ret ['data'] = array ();
		$ret ['status'] = true;
		$ret ['weight_income_in']=$weightarr['total_cost_weight'];
		$ret ['order_no']=$order->order_no;
		return json_encode ( $ret );
		exit ();
	}
	/**
	 * 入库打印
	 */
	function actionInFarLabel() {
		header ( 'Content-Type:application/json' );
		$order = Order::find ( 'order_id = ?', request ( 'orderid' ) )->getOne ();
		$country = Country::find('code_word_two = ?',$order->consignee_country_code)->getOne();
		$json_arr = array ();
		$json_arr ['ali_order_no'] = $order->ali_order_no;
		$fofn='';
		if ($order->declaration_type == 'DL') {
			$fofn = '/FO';
		} elseif ($order->total_amount > 700 || $order->weight_actual_in > 70) {
			$fofn = '/FN';
		}
		$json_arr ['SP'] = $order->volumn_chargeable ? 'P'.$fofn : 'S'.$fofn;
		$product='';
		//产品小标签
		switch ($order->service_code){
			case 'Express_Standard_Global':
				$product='普货';
				break;
			case 'EMS-FY':
				$product='EMS';
				break;
			case 'WIG-FY':
				$product='假发';
				break;
			case 'US-FY':
				$product='美空';
				break;
			case 'OCEAN-FY':
				$product='美海';
				break;
			case 'CNUS-FY':
				$product='无忧';
				break;
			case 'EUUS-FY':
				$product='欧美';
				break;
			case 'ePacket-FY':
				$product='EUB';
				break;
			case 'CNUSBJ-FY':
				$product='包机';
				break;
		}
		$json_arr ['product'] = $product;
		$json_arr ['country'] = $country->chinese_name;
		return json_encode ( $json_arr );
		exit ();
	}
	/**
	 * confirm
	 */
	function actionConfirm() {
		$in_nos = Order::find ( 'order_status=5 and department_id=?',MyApp::currentUser('department_id') )->setColumns ( 'ali_order_no' )
		->getAll ()
		->getCols ( 'ali_order_no' );
		$this->_view ['in_nos'] = $in_nos;
	}
	/**
	 * confirm save
	 */
	function actionConfirmSave() {
		header ( 'Content-Type:application/json' );
		$arr = json_decode ( request ( 'jsonstr' ), true );
		QLog::log ( print_r ( $arr, true ) );
		$ret = array (
			'code' => '1000',
			'msg' => '错误',
			'sound' => 'cuowu.mp3',
			'data' => '',
			'status' => false
		);
		try {
			$event_location = '';
			$department = Department::find ( 'department_id=?', MyApp::currentUser ( 'department_id' ) )->getOne ();
			if ($department->department_name == '杭州仓') {
				$event_location = '杭州';
			} elseif ($department->department_name == '义乌仓') {
				$event_location = '义乌';
			} elseif ($department->department_name == '上海仓') {
				$event_location = '上海';
			}elseif ($department->department_name == '广州仓') {
				$event_location = '广州';
			}elseif ($department->department_name == '青岛仓') {
				$event_location = '青岛';
			}elseif ($department->department_name == '深圳仓') {
				$event_location = '深圳';
			}elseif ($department->department_name == '南京仓') {
				$event_location = '南京';
			}elseif ($department->department_name == '连云港仓') {
				$event_location = '连云港';
			}
			// WAREHOUSE_INBOUND (CONFIRM-300s)\CHECK_WEIGHT (CONFIRM-150s)\CONFIRM
			foreach ( $arr ['reason_nos'] as $value ) {
				$order = Order::find ( 'ali_order_no = ?', $value ['no'] )->getOne ();
				if (! $order->isNewRecord ()) {
					$event_time = time ();
					$helper_event = new Helper_Event();
					if ($order->customer->customs_code=='ALPL'){
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'SORTING_CENTER_INBOUND_CALLBACK',$event_time - 300 , $event_location );
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'CHECK_WEIGHT_CALLBACK',$event_time - 150 , $event_location );
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'CONFIRM_CALLBACK',$event_time , $event_location ,$value ['reason'] );
					}else{
						$this->eventSave ( $order->order_id, 'WAREHOUSE_INBOUND', $event_time - 300, $event_location ,$order->customer_id);
						$this->eventSave ( $order->order_id, 'CHECK_WEIGHT', $event_time - 150, $event_location ,$order->customer_id);
						$this->eventSave ( $order->order_id, 'CONFIRM', $event_time, $event_location,$order->customer_id,$value ['reason'] );
					}
				}
				//已查验
				$order->order_status='11';
				$order->save();
			}
			foreach ( $arr ['in_nos'] as $value ) {
				$order = Order::find ( 'ali_order_no = ?', $value )->getOne ();
				if (! $order->isNewRecord ()) {
					$event_time = time ();
					$helper_event = new Helper_Event();
					if ($order->customer->customs_code=='ALPL'){
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'SORTING_CENTER_INBOUND_CALLBACK',$event_time - 300 , $event_location );
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'CHECK_WEIGHT_CALLBACK',$event_time - 150 , $event_location );
						$helper_event->saveEvent($order->customer_id, $order->order_id, 'CONFIRM_CALLBACK',$event_time , $event_location );
					}else{
						$this->eventSave ( $order->order_id, 'WAREHOUSE_INBOUND', $event_time - 300, $event_location ,$order->customer_id);
						$this->eventSave ( $order->order_id, 'CHECK_WEIGHT', $event_time - 150, $event_location ,$order->customer_id);
						$this->eventSave ( $order->order_id, 'CONFIRM', $event_time, $event_location ,$order->customer_id);
					}
				}
				//待退货
				$order->order_status='10';
				$order->save();
			}
		} catch ( Exception $e ) {
			$ret ['msg'] = '系统异常，请稍后重试';
			return json_encode ( $ret );
			exit ();
		}
		
		$ret ['code'] = '0';
		$ret ['msg'] = '完成';
		$ret ['sound'] = 'chayanxinxibaocunwancheng.mp3';
		$ret ['data'] = array ();
		$ret ['status'] = true;
		
		return json_encode ( $ret );
		exit ();
	}
	/**
	 * event
	 */
	function eventSave($order_id, $event_code, $event_time, $event_location,$customer_id,$reason=NULL) {
		$event = Event::find ( 'order_id=? and event_code="CONFIRM"', $order_id )->getOne ();
		$event->customer_id = $customer_id;
		$event->order_id = $order_id;
		$event->event_code = $event_code;
		$event->event_time = $event_time;
		$event->event_location = $event_location;
		$event->reason = $reason;
		$event->timezone = '8';
		$event->confirm_flag = '1';
		$event->operator =MyApp::currentUser('staff_name');
		$event->save ();
	}
	/**
	 * 判断订单是否含有泡货和电池
	 */
	function actionCheckorder(){
		//判断退回已支付的原末端单号打印
		if (request('is_print_oldtrackingno')){
			$return_paid = ReturnPaidTrackingno::find('old_tracking_no=?',request('ali_order_no'))->getOne();
			if ($return_paid->isNewRecord()){
				$data['message']='aliordernonotexist';
				echo json_encode($data);
				exit();
			}else{
				//order_id
				$order=Order::find('order_id=?',$return_paid->order_id)->getOne();
				if ($order->tracking_no || $order->order_status != '4'){
					$data['message']='aliordernocannotprint';
					echo json_encode($data);
					exit();
				}else{
					//Order表里面的ali_order_no
					$ali_order_no = $order->ali_order_no;
				}
			}
		}else{
			$ali_order_no = request('ali_order_no');
		}
		$order=Order::find('ali_order_no=?',$ali_order_no)->getOne();
		$data=array();
		if($order->isNewRecord()){
			$data['message']='notexist';
		}else{
			//CNUSBJ-FY
			//中美无忧-包机专线 暂时禁止标签打印出库操作
			if ($order->service_code=='CNUSBJ-FY'){
				$data['message']='cnusbjfyforbidcheckout';
				echo json_encode($data);
				exit;
			}
			
			$cuu_bool = Helper_Currency::isCurreny($order);
			//判断币种
			if(!$cuu_bool){
				$data['message']='cuufalse';
				$data['channel_id']=$order->channel_id;
				echo json_encode($data);
				exit();
			}
			$data['product']=$order->service_product->product_chinese_name.' '.$order->service_code;
			$data['label_remark']=str_replace(array("\r\n","\n","\r","\n\r"), '<br/>', $order->service_product->label_remark);
			$flag=true;
			if(!in_array($order->department_id, RelevantDepartment::relateddepartmentids())){
				$data['message']='notsamewarehouse';
				echo json_encode($data);
				exit;
			}
			if(MyApp::currentUser('department_id')=='23' && !in_array($order->service_code, array("WIG-FY","US-FY","Express_Standard_Global","CNUS-FY","OCEAN-FY"))){
				$data['message']='qingdaocangerror';
				echo json_encode($data);
				exit;
			}
			//燃油
			$product=Product::find('product_name=?',$order->service_code)->getOne();
			if(!$product->isNewRecord() && in_array($order->service_code, array('Express_Standard_Global','WIG-FY'))){
				$fuel=Networkfuel::find('network_id=? and effective_date<=? and fail_date>=?',$product->network_id,time(),time())->getOne();
				if($fuel->isNewRecord()){
					$data['message']='fuelnotexist';
					echo json_encode($data);
					exit;
				}
			}
			if ($order->channel_id){
				$channel_c=Channel::find("channel_id=?",$order->channel_id)->getOne();
				//申报总价限制
// 				if($channel_c->declare_threshold && $order->total_amount > $channel_c->declare_threshold){
// 					$reason= '申报总价超'.$channel_c->declare_threshold.'USD/'.$channel_c->channel_name.'无服务';
// 					$data['message']='overdeclarethreshold';
// 					$data['reason']=$reason;
// 					echo json_encode($data);
// 					exit;
// 				}
				//新·申报总价限制 
				$declare_th = Channeldeclarethreshold::find('channel_id=?',$order->channel_id)->getAll();
				foreach ($declare_th as $dt){
					//如果在国家组
					$country_group = CodeCountryGroup::find ('id=?',$dt->country_group_id)->getOne ();
					$country = explode(',', $country_group->country_codes);
					if(in_array($order->consignee_country_code, $country)){
						//判断是否在区间内
						if($order->total_amount >= $dt->front && $order->total_amount <= $dt->after){
							$reason= '申报总价在区间'.$dt->front.'-'.$dt->after.'USD/'.$channel_c->channel_name.'无服务';
							$data['message']='overdeclarethreshold';
							$data['reason']=$reason;
							echo json_encode($data);
							exit;
						}
					}
				}
				
				$hold_flag = '';
				$i=1;
				foreach ($order->faroutpackages as $faroutpackage){
					$arr = array($faroutpackage->length_out,$faroutpackage->width_out,$faroutpackage->height_out);
					sort($arr);
					//最长边限制
					if($channel_c->length && $arr[2]>=$channel_c->length){
						$hold_flag .= '最长边<'.$channel_c->length.' ';
					}
					//第二长边限制
					if ($channel_c->width && $arr[1]>=$channel_c->width){
						$hold_flag .= '第二长边<'.$channel_c->width.' ';
					}
					//高限制
					if ($channel_c->height && $arr[0]>=$channel_c->height){
						$hold_flag .= '高<'.$channel_c->height.' ';
					}
					//周长限制
					if ($channel_c->perimeter && 4*($arr[2]+$arr[1]+$arr[0])>=$channel_c->perimeter){
						$hold_flag .= '周长<'.$channel_c->perimeter.' ';
					}
					//围长限制
					if ($channel_c->girth && $arr[2]+2*($arr[1]+$arr[0])>=$channel_c->girth){
						$hold_flag .= '围长<'.$channel_c->girth.' ';
					}
					//单个包裹实重限制
					if ($channel_c->weight && $faroutpackage->weight_out>=$channel_c->weight){
						$hold_flag .= '单个包裹实重限制<'.$channel_c->weight.' ';
					}
					$common_flag = '第'.$i.'条产品规格错误'.$channel_c->channel_name.'服务范围：';
					if(strlen($hold_flag)>0){
						$hold_flag = $common_flag.$hold_flag;
						$data['message']='overholdflag';
						$data['reason']=$hold_flag;
						echo json_encode($data);
						exit;
					}
					$i++;
				}
				if ($channel_c->total_cost_weight && $order->weight_cost_out>=$channel_c->total_cost_weight){
					$reason = '整票计费重限制<'.$channel_c->total_cost_weight.' ';
					$data['message']='overtotalcostweight';
					$data['reason']=$reason;
					echo json_encode($data);
					exit;
				}
				
			}
			//检查邮编城市国家无服务
			// 			if($product->check_zip=='1'){
			// 			   $noservice_zip = Noserivcezipcode::find("zip_code = ? and service_code = ?",$order->consignee_postal_code,$order->service_code)->getOne();
			// 			   if(!$noservice_zip->isNewRecord()){
			// 			       $data['message']='youbianwfw';
			// 			       echo json_encode($data);
			// 			       exit;
			// 			   }
			// 			   $noservice_city = Noserivcezipcode::find("city = ? and service_code = ?",$order->consignee_city,$order->service_code)->getOne();
			// 			   if(!$noservice_city->isNewRecord()){
			// 			       $data['message']='chengshiwfw';
			// 			       echo json_encode($data);
			// 			       exit;
			// 			   }
			// 			   $noservice_city = Noserivcezipcode::find("country_code = ? and service_code = ?",$order->consignee_country_code,$order->service_code)->getOne();
			// 			   if(!$noservice_city->isNewRecord()){
			// 			       $data['message']='guojiawfw';
			// 			       echo json_encode($data);
			// 			       exit;
			// 			   }
			// 			}
			// 	        foreach ($order->product as $product){
			// 	            if($product->has_battery=='1'){
			// 	                $flag=false;
			// 	            }
			// 	        }
			// 	        if($flag){
			if($order->order_status=='6' || $order->order_status =='7' || $order->order_status =='8' || $order->order_status =='9'){
				$data['message']='checkout';
			}else{
				if($order->order_status=='12' && !empty($order->payment_time) && $order->payment_time>0){
					$data['message']='koujian';
				}elseif($order->order_status!='4' || empty($order->payment_time)){
					$data['message']='notpay';
				}else{
					$package_sum=Faroutpackage::find('order_id=?',$order->order_id)->getAll();
					//无忧不用补充数据的
					if(count($package_sum)<1 && $order->service_code != 'CNUS-FY'){
						$data['message']='inbgcomplete';
					}
				}
			}
			// 	        }else{
			// 	            $data['message']='hasbattery';
			// 	        }
		}
		//阿里订单号
		$data['ali_order_no']=$order->ali_order_no;
		$data['channel_id']=$order->channel_id;
		echo json_encode($data);
		exit();
	}
	
	/**
	 * @todo   包裹出库
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return
	 * @link
	 */
	function actionCheckout() {
		if(request_is_post()){
			//监控时间的开始时间
			$datetime1 = time();
			//搜索订单
			$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
			//根据退回已支付的原末端单号打印，删除原来的方法，现在传过来的是阿里订单号
			$data=array();
			//获取订单总重
			$total_weight='';
			$package_sum=Faroutpackage::find('order_id=?',$order->order_id)->getAll();
			foreach ($package_sum as $v){
				$total_weight+=$v->weight_out*$v->quantity_out;
			}
			//将重量存入order中
			$order->weight_actual_out=($order->service_code == 'ePacket-FY')?sprintf('%.3f',$total_weight):sprintf('%.2f',$total_weight);
			$order->save();
			//验证是否补充数据操作
			$productcode=array();
			$productcode2=array();
			$channel_ids=array();
			$productcode=Product::getprodutcode(1);//需要补充数据的产品code
			$productcode2=product::getprodutcode(3);//通过渠道判断
			$channel_ids=Channel::channelids(1);//渠道需要验证的ID
			
			//初始化时间判断需要用的渠道id
			$t_channel_id = 0;
			
			//获取产品
			$product=Product::find('product_name=?',$order->service_code)->getOne();
			if($product->isNewRecord()){
				$data['message']='productnotexist';
			}else{
				if($order->order_status=='6' || $order->order_status =='7' || $order->order_status =='8' || $order->order_status =='9'){
					$data['account']=$order->account;
					$data['message']='true';
				}else {
					//填写了渠道和运单号
					if(strlen($order->channel_id)>0 && strlen($order->tracking_no)>0){
						$data['account']='';
						$data['message']='true';
						//计算应付
						//获取异形包装费
						$special_fee_c_t=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
						$special_count_c_t=0;
						if(!$special_fee_c_t->isNewRecord()){
							$special_count_c_t=$special_fee_c_t->quantity;
						}
						//查找渠道成本
						$channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
						if(!$channelcost_c_t->isNewRecord()){
							$channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,time(),time())->getOne();
							if(!$channelcostppr_c_t->isNewRecord()){
								$network_c_t=Network::find("network_code=? ",$order->channel->network_code)->getOne();
								$quote= new Helper_Quote();
								if ($order->customer->customs_code=='ALCN'){
									$cainiaofee = new Helper_CainiaoFee();
									$price_c_t = $cainiaofee->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t);
								}else{
									$price_c_t=$quote->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t);
								}
								//存在生效费用项无法计算
								if(@$price_c_t['success']=='formulaerror'){
									$data['message']=$price_c_t['success'];
									echo json_encode($data);
									exit();
								}
								if (count($price_c_t)&&$price_c_t['total_single_weight']){
									//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
									$order->total_single_weight = $price_c_t['total_single_weight'];
									$order->save();
								}
								//删除应付费用
								Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
								if(count($price_c_t) && count($price_c_t['price_info'])){
									foreach ($price_c_t['price_info']['fee_item'] as $key=>$fee_item){
										//判断费用项中是否存在费用
										if($fee_item['fee']!='0'){
											//币种
											if(@$fee_item['currency_code']){
												$currency_code = $fee_item['currency_code'];
												$rate = $fee_item['rate'];
											}else{
												$currency_code = 'CNY';
												$rate = 1;
											}
											//获取fee_sub_code
											$fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
											$fee= new Fee();
											$fee->changeProps(array(
												'order_id'=>$order->order_id,
												'fee_type'=>'2',
												'fee_item_code'=>$fee_sub_code->sub_code,
												'fee_item_name'=>$fee_sub_code->item_name,
												'quantity'=>$fee_item['quantity'],
												'amount'=>$fee_item['fee'],
												'currency'=>$currency_code,
												'rate'=>$rate,
												'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $order->channel->supplier_id
											));
											$fee->save();
										}
									}
									//保存计费重量
									if($price_c_t['price_info']['total_weight']){
										//出库的包裹总计费重，用于计算成本
										$order->weight_cost_out=$price_c_t['price_info']['total_weight'];
										//标签重量
										$order->weight_label=$price_c_t['price_info']['weight_label'];
										$order->save();
									}
								}
							}
						}
						$sub_code=Subcode::find('sub_code=? and order_id=?',$order->tracking_no,$order->order_id)->getOne();
						if($sub_code->isNewRecord()){
							$sub=new Subcode();
							$sub->order_id=$order->order_id;
							$sub->sub_code=$order->tracking_no;
							$sub->save();
						}
						if($order->customer->customs_code=='ALCN'){
							self::saveOutCaiNiao($order);
						}else{
							self::saveoutevents($order);
						}
						//只选择了渠道，没有填写末端单号
					}elseif($order->channel_id >0 && !$order->tracking_no){
						//赋值
						$t_channel_id = $order->channel_id;
						//判断渠道是否有权限
						//1
						if(!in_array($order->channel_id, Channeldepartmentavailable::availablechannelids($order->customer_id))){
							$data['message']="channel_id_no";
							echo json_encode($data);
							exit();
						}
						//判断渠道带电与否是否正确
						$channel_c1 = Channel::find('channel_id=?',$order->channel_id)->getOne();
						if($order->has_battery==1 && $channel_c1->has_battery!=1) {
							$data['message']="has_battery";
							echo json_encode($data);
							exit();
						}
						//是否支持PDA品类
						if($order->is_pda==1){
							if ($channel_c1->is_pda!=1){
								$data['message']="nopda";
								echo json_encode($data);
								exit();
							}
						}
						//是否支持申报
						if($order->declaration_type=='DL'){
							if ($channel_c1->is_declaration!=1){
								$data['message']="nobaoguan";
								echo json_encode($data);
								exit();
							}
						}
						//判断需要渠道来验证数据完整的订单,不在要验证的渠道内的修改成完整
						//1
						if($order->add_data_status!=1){
							if(in_array($order->service_code, $productcode)){
								$data['message']="incomplete";
								echo json_encode($data);
								exit();
							}else if(in_array($order->service_code,$productcode2)){
								if(in_array($order->channel_id,$channel_ids)){
									$data['message']="incomplete";
									echo json_encode($data);
									exit();
								}
							}
						}
						//判断所选仓库是否有渠道权限
						$channel=Helper_Array::getCols(Channel::find('channel_id in (?)',Channeldepartmentavailable::availablechannelids())->asArray()->getAll(),'channel_id');
						if(!in_array($order->channel_id, $channel)){
							$data['message']="nousechannel";
							echo json_encode($data);
							exit();
						}
						//新·申报总价限制
						$declare_th = Channeldeclarethreshold::find('channel_id=?',$order->channel_id)->getAll();
						foreach ($declare_th as $dt){
							//如果在国家组
							$country_group = CodeCountryGroup::find ('id=?',$dt->country_group_id)->getOne ();
							$country = explode(',', $country_group->country_codes);
							if(in_array($order->consignee_country_code, $country)){
								//判断是否在区间内
								if($order->total_amount >= $dt->front && $order->total_amount <= $dt->after){
									$data['message']="申报总价在指定渠道总价阀值区间";
									echo json_encode($data);
									exit();
								}
							}
						}
						//判断是否是禁运国家
						$disabled_country = ChannelCountryDisabled::find ( 'channel_id = ? and effect_time<=? and failure_time>=?', $order->channel_id, time(), time() )->getOne();
						$countries = explode(',', $disabled_country->country_code_two);
						if (count ( $countries ) > 0) {
							//判断
							if (in_array ( $order->consignee_country_code, $countries )) {
								$data['message']="目的国在指定渠道为禁运国家";
								echo json_encode($data);
								exit();
							}
						}
						//#83619
						//当系统最终优选到的渠道为中美空派USPS
						//当单个产品数量超过20个时，进行打单报错
						//测试渠道是60
						//正式渠道是56
						if ($order->channel_id==56){
							$order_product = Orderproduct::find('order_id=? and product_quantity>20',$order->order_id)->getOne();
							if (!$order_product->isNewRecord()){
								//订单含产品数量超过20个的产品，请联系客服处理。
								$data['message']="productnumovertwenty";
								echo json_encode($data);
								exit();
							}
						}
						//首先判断渠道里的网络是不是UPS
						$channel_c=Channel::find("channel_id=?",$order->channel_id)->getOne();
						if(!$channel_c->isNewRecord() && ($channel_c->network_code=='UPS' || $channel_c->network_code=='EMS' || $channel_c->network_code=='FEDEX' || $channel_c->network_code=='US-FY' || $channel_c->network_code=='DHL' || $channel_c->network_code=='DHLE' || $channel_c->network_code=='USPS')){
							//查找渠道对应的渠道成本
							$channelcost_c=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
							if (!$channelcost_c->isNewRecord()){
								$channelcostppr_c=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c->channel_cost_id,time(),time())->getOne();
								if(!$channelcostppr_c->isNewRecord()){
									$weight_outarr = Helper_Quote::getweightarr($order, 2);
									//渠道重量
									$weight_cost_out = $weight_outarr['total_cost_weight'];
									//标签重
									$label_weight = $weight_outarr['total_label_weight'];
									//出库总体积重
									$total_out_volumn_weight = $weight_outarr['total_volumn_weight'];
									$limit_ids = array();
									$department_id = $order->department_id;
									if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $order->department_id=='23' && MyApp::currentUser('department_id') <> '23'){
										$department_id=MyApp::currentUser('department_id');
									}
									$limit_amounts = ChannelLimitationAmount::find('channel_id = ? and effect_time <= ? and failure_time >= ?',$channel_c->channel_id,time(),time())->getAll();
									foreach ($limit_amounts as $value){
										if($value->department_id && $value->department_id<>$department_id){
											continue;
										}
										if($value->country_group_id){
											$country = CodeCountryGroup::find('id = ? and country_codes like ?',$value->country_group_id,"%".$order->consignee_country_code."%")->getOne();
											if($country -> isNewRecord()){
												continue;
											}
											$country_code = explode(',', $country->country_codes);
										}
										$weight_sum = 0;
										$time = '';
										if($value->cycle == '0'){//每日
											$time =  date ( 'Y-m-d' );
										}elseif ($value->type == '1'){//每周
											$theday = date('N', time());//获取当前第几天
											$time = date('Y-m-d',strtotime('-'.($theday-1).'day'));//获取周一
										}elseif ($value->type == '2'){//每月
											$time = date('Y-m-01');//获取当月第一天
										}
										$order_ids = Order::find ( 'warehouse_out_time >= ? and channel_id = ?', strtotime ( $time . '00:00:00' ), $channel_c->channel_id);
										if($value->department_id){
											$order_ids->where('department_id = ?',$value->department_id);
										}
										if(isset($country_code) && !empty($country_code)){
											$order_ids->where('consignee_country_code in (?)',$country_code);
										}
										$order_ids = $order_ids->setColumns('order_id')->asArray()->getAll();
										$order_ids = Helper_Array::getCols($order_ids, 'order_id');
										if(count($order_ids)==0){
											$value->used_value = 0;
											$value->save();
										}
										if ($value->type == '0') {//票数
											if(count($order_ids)>0 && $value->used_value == 0){
												$order_count = Order::find ( 'order_id in (?)', $order_ids )->getCount ();
												$value->used_value = $order_count;
												$value->save();
											}
											if ($value->used_value+1 > $value->max_value) {
												$data['message']="pricenotexist";
												echo json_encode($data);
												exit();
											}
										} elseif($value->type == '1') {//实重
											if(count($order_ids)>0 && $value->used_value == 0){
												$value->used_value = Order::find('order_id in (?)',$order_ids)->getSum('weight_actual_out');
												$value->save();
											}
											if ($value->used_value+$total_weight > $value->max_value) {
												$data['message']="pricenotexist";
												echo json_encode($data);
												exit();
											}
										} else if($value->type == '2'){//计费重
											if(count($order_ids)>0 && $value->used_value == 0){
												$value->used_value = Order::find('order_id in (?)',$order_ids)->getSum('weight_cost_out');
												$value->save();
											}
											if ($value->used_value+$weight_cost_out > $value->max_value) {
												$data['message']="pricenotexist";
												echo json_encode($data);
												exit();
											}
										}
										$limit_ids[] = $value->limitation_amount_id;
									}
									
									//先查找分区
									$partition_code='';
									$partition_code2='';
									$partion_c=Partition::find("partition_manage_id=? and country_code_two=?",$channelcostppr_c->partition_manage_id,$order->consignee_country_code)->getAll();
									foreach ($partion_c as $p){
										if(strlen($p->postal_code)>0 && (substr($p->postal_code, 0,strlen($order->consignee_postal_code))==$order->consignee_postal_code || substr($order->consignee_postal_code, 0,strlen($p->postal_code))==$p->postal_code)){
											$partition_code=$p->partition_code;
										}
										if(!$p->postal_code){
											$partition_code2=$p->partition_code;
										}
									}
									if(!$partition_code){
										$partition_code=$partition_code2;
									}
									
									$packing=$order->packing_type=='PAK'?"BOX":$order->packing_type;
									if($order->packing_type=='PAK' && $order->service_code=='WIG-FY'){
										$packing="PAK";
									}
									$price_c=Price::find("price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=? ",$channelcostppr_c->price_manage_id,$partition_code,$packing,$weight_cost_out,$weight_cost_out)->getOne();
									
									if(!$price_c->isNewRecord()){
										$account_sync_c=Accountsync::find("product_code=?",$price_c->account)->getOne();
										//print_r($account_sync_c);exit;
										if(!$account_sync_c->isNewRecord()){
											$account_c=$account_sync_c->account;
										}
										//调用打单方法
										$order->weight_label=$label_weight;
										//获取异形包装费
										$special_fee_c=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
										$special_count_c=0;
										if(!$special_fee_c->isNewRecord()){
											$special_count_c=$special_fee_c->quantity;
										}
										$network_c=Network::find("network_code=? ",$order->channel->network_code)->getOne();
										$quote= new Helper_Quote();
										if ($order->customer->customs_code=='ALCN'){
											$cainiaofee = new Helper_CainiaoFee();
											$fees_c=$cainiaofee->payment($order, $channelcostppr_c,$network_c->network_id,$special_count_c);
										}else{
											$fees_c=$quote->payment($order, $channelcostppr_c,$network_c->network_id,$special_count_c);
										}
										//存在生效费用项无法计算
										if(@$fees_c['success']=='formulaerror'){
											$data['message']=$fees_c['success'];
											echo json_encode($data);
											exit();
										}
										//--
										//修改Getlabel为getLabel
										$view=Helper_Common::getLabel($order, @$account_c ,$order->channel_id);
										if(!isset($view['errormessage']) || $view['errormessage']!=''){
											//渠道获取面单失败
											$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
										}else{
											if (count($fees_c)&&$fees_c['total_single_weight']){
												//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
												$order->total_single_weight = $fees_c['total_single_weight'];
												$order->save();
											}
											//删除原有费用
											Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
											if(count($fees_c) && count($fees_c['price_info'])){
												foreach ($fees_c['price_info']['fee_item'] as $key=>$fee_item){
													//判断费用项中是否存在费用
													if($fee_item['fee']!='0'){
														//币种
														if(@$fee_item['currency_code']){
															$currency_code = $fee_item['currency_code'];
															$rate = $fee_item['rate'];
														}else{
															$currency_code = 'CNY';
															$rate = 1;
														}
														//获取fee_sub_code
														$fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
														$fee= new Fee();
														$fee->changeProps(array(
															'order_id'=>$order->order_id,
															'fee_type'=>'2',
															'fee_item_code'=>$fee_sub_code->sub_code,
															'fee_item_name'=>$fee_sub_code->item_name,
															'quantity'=>$fee_item['quantity'],
															'amount'=>$fee_item['fee'],
															'currency'=>$currency_code,
															'rate'=>$rate,
															'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $order->channel->supplier_id
														));
														$fee->save();
													}
												}
											}
											if(count($limit_ids)>0){
												$limit_ids = array_unique($limit_ids);
												$limit_lists = ChannelLimitationAmount::find('channel_id = ? and limitation_amount_id in (?)',$channel_c->channel_id,$limit_ids)->getAll();
												foreach ($limit_lists as $list){
													if($list->type == 0){
														$list->used_value = $list->used_value+1;
													}elseif($list->type == 1){
														$list->used_value = $list->used_value+$total_weight;
													}elseif($list->type == 2){
														$list->used_value = $list->used_value+$weight_cost_out;
													}
													$list->save();
												}
											}
											//保存计费重量
											$order->weight_cost_out=$weight_cost_out;
											//保存标签重
											$order->weight_label=$label_weight;
											//存入打单账号
											$order->account=$view['account_number'];
											$order->add_data_status=1;
											$order->save();
											if($order->customer->customs_code=='ALCN'){
												self::saveOutCaiNiao($order);
											}else{
												self::saveoutevents($order);
											}
											$data['account']=$view['account'];
											$data['message']='true';
										}
									}else{
										$data['message']="pricenotexist";
									}
								}else{
									$data['message']="channelnotexist";
								}
							}else{
								$data['message']="channelnotexist";
							}
							//非UPS渠道,只计算费用，不生成面单
						}else{
							//获取异形包装费
							$special_fee_c=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
							$special_count_c=0;
							if(!$special_fee_c->isNewRecord()){
								$special_count_c=$special_fee_c->quantity;
							}
							$network_c=Network::find("network_code=? ",$order->channel->network_code)->getOne();
							$channelcost_c=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
							if (!$channelcost_c->isNewRecord()){
								$channelcostppr_c=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c->channel_cost_id,time(),time())->getOne();
								$quote= new Helper_Quote();
								if ($order->customer->customs_code=='ALCN'){
									$cainiaofee = new Helper_CainiaoFee();
									$fees_c=$cainiaofee->payment($order, $channelcostppr_c,$network_c->network_id,$special_count_c);
								}else{
									$fees_c=$quote->payment($order, $channelcostppr_c,$network_c->network_id,$special_count_c);
								}
								//存在生效费用项无法计算
								if(@$fees_c['success']=='formulaerror'){
									$data['message']=$fees_c['success'];
									echo json_encode($data);
									exit();
								}
								if (count($fees_c)&&$fees_c['total_single_weight']){
									//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
									$order->total_single_weight = $fees_c['total_single_weight'];
									$order->save();
								}
								//删除原有应付费用
								Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
								if(count($fees_c) && count($fees_c['price_info'])){
									//存入成本费用
									foreach ($fees_c['price_info']['fee_item'] as $key=>$fee_item){
										//判断费用项中是否存在费用
										if($fee_item['fee']!='0'){
											//币种
											if(@$fee_item['currency_code']){
												$currency_code = $fee_item['currency_code'];
												$rate = $fee_item['rate'];
											}else{
												$currency_code = 'CNY';
												$rate = 1;
											}
											//获取fee_sub_code
											$fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
											$fee= new Fee();
											$fee->changeProps(array(
												'order_id'=>$order->order_id,
												'fee_type'=>'2',
												'fee_item_code'=>$fee_sub_code->sub_code,
												'fee_item_name'=>$fee_sub_code->item_name,
												'quantity'=>$fee_item['quantity'],
												'amount'=>$fee_item['fee'],
												'currency'=>$currency_code,
												'rate'=>$rate,
												'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $order->channel->supplier_id
											));
											$fee->save();
										}
									}
									//保存计费重量
									if($fees_c['price_info']['total_weight']){
										$order->weight_cost_out=$fees_c['price_info']['total_weight'];
										$order->weight_label=$fees_c['price_info']['weight_label'];
										$order->save();
									}
								}
							}
							if($order->customer->customs_code=='ALCN'){
								self::saveOutCaiNiao($order);
							}else{
								self::saveoutevents($order);
							}
							$data['account']='';
							$data['message']='true';
						}
					}else{
						//获取渠道成本
						$channelcost=ChannelCost::find('product_id=?',$product->product_id)->getAll();
						$channel_all = Channeldepartmentavailable::availablechannelids($order->customer_id);
						//判断产品下的渠道是否有权限
						$cc_channel = 0;
						foreach ($channelcost as $cc){
							if(in_array($cc->channel_id, $channel_all)){
								$cc_channel = 1;
							}
						}
						//判断
						if(!$cc_channel){
							$data['message']='wuyouhuaqudao';
							echo json_encode($data);
							exit;
						}
						if(count($channelcost)<=0){
							$data['message']='channelnotexist';
						}else{
							//计算成本价格
							$price_array=array();
							$price_info_array=array();
							$limit_ids = array();
							$flag = 0;
							foreach ($channelcost as $temp){
								//判断渠道可用部门和禁用部门
								$available_department_ids=Helper_Array::getCols(Channeldepartmentavailable::find('channel_id=?',$temp->channel_id)->getAll(), 'department_id');
								$disabled_department=Channeldepartmentdisable::find('channel_id=? and department_id=? and effect_time <= ? and failure_time >= ?',$temp->channel_id,$order->department_id,time(),time())->getOne();
								if((count($available_department_ids)>0 && !in_array($order->department_id, $available_department_ids)) || !$disabled_department->isNewRecord()){
									continue;
								}
								//判断渠道权限
								if(!in_array($temp->channel_id, $channel_all)){
									continue;
								}
								//判断是否是禁运国家
								$disabled_country = ChannelCountryDisabled::find ( 'channel_id = ? and effect_time<=? and failure_time>=?', $temp->channel_id, time(), time() )->getOne();
								$countries = explode(',', $disabled_country->country_code_two);
								if (count ( $countries ) > 0) {
									//判断
									if (in_array ( $order->consignee_country_code, $countries )) {
										continue;
									}
								}
// 								//判断所选仓库是否有渠道权限
// 								$channel=Helper_Array::getCols(Channel::find('channel_id in (?)',Channeldepartmentavailable::availablechannelids())->asArray()->getAll(),'channel_id');
// 								if(!in_array($temp->channel_id, $channel)){
// 									continue;
// 								}
								//获取价格-偏派-分区表
								$channelcostppr=Channelcostppr::find('channel_cost_id=? and effective_time<=? and invalid_time>=?',$temp->channel_cost_id,time(),time())->getOne();
								if($channelcostppr->isNewRecord()){
									continue;
								}
								$temp_channel = Channel::find('channel_id=?',$temp->channel_id)->getOne();
								//判断偏远邮编
								if($temp_channel->postcode_verify == 1){
									//取出渠道偏远邮编数据
									$zipcode = ChannelZipCode::find('channel_id = ? and zip_code=?',$temp_channel->channel_id,$order->consignee_postal_code)->getOne();
									//如果没有数据则跳过
									if($zipcode->isNewRecord()){
										continue;
									}
								}
								//是否支持带电
								if($order->has_battery==1){
									if ($temp_channel->has_battery!=1){
										continue;
									}
								}
								//是否支持PDA品类
								if($order->is_pda==1){
									if ($temp_channel->is_pda!=1){
										continue;
									}
								}
								//是否支持申报
								if($order->declaration_type=='DL'){
									if ($temp_channel->is_declaration!=1){
										continue;
									}
								}
								//申报总价阈值
								foreach ($order->faroutpackages as $faroutpackage){
									
									$arr = array($faroutpackage->length_out,$faroutpackage->width_out,$faroutpackage->height_out);
									sort($arr);
									//最长边限制
									if($temp_channel->length && $arr[2]>=$temp_channel->length){
										//不选此渠道
										continue 2;
									}
									//第二长边限制
									if ($temp_channel->width && $arr[1]>=$temp_channel->width){
										//不选此渠道
										continue 2;
									}
									//高限制
									if ($temp_channel->height && $arr[0]>=$temp_channel->height){
										continue 2;
									}
									//周长限制
									if ($temp_channel->perimeter && 4*($arr[2]+$arr[1]+$arr[0])>=$temp_channel->perimeter){
										continue 2;
									}
									//围长限制
									if ($temp_channel->girth && $arr[2]+2*($arr[1]+$arr[0])>=$temp_channel->girth){
										continue 2;
									}
									//单个包裹实重限制
									if ($temp_channel->weight && $faroutpackage->weight_out>=$temp_channel->weight){
										continue 2;
									}
								}
								//申报总价阈值
// 								if ($temp_channel->declare_threshold){
// 									if($order->total_amount>$temp_channel->declare_threshold){
// 										continue;
// 									}
// 								}
								//新·申报总价限制
								$declare_th = Channeldeclarethreshold::find('channel_id=?',$temp_channel->channel_id)->getAll();
								foreach ($declare_th as $dt){
									//如果在国家组
									$country_group = CodeCountryGroup::find ('id=?',$dt->country_group_id)->getOne ();
									$country = explode(',', $country_group->country_codes);
									if(in_array($order->consignee_country_code, $country)){
										//判断是否在区间内
										if($order->total_amount >= $dt->front && $order->total_amount <= $dt->after){
											continue 2;
										}
									}
								}
								
								//整票计费重
								if ($temp_channel->total_cost_weight){
									if($order->weight_cost_out>$temp_channel->total_cost_weight){
										continue;
									}
								}
								$department_id = $order->department_id;
								if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $order->department_id=='23' && MyApp::currentUser('department_id') <> '23'){
									$department_id=MyApp::currentUser('department_id');
								}
								
								$limit_amounts = ChannelLimitationAmount::find('channel_id = ? and effect_time <= ? and failure_time >= ?',$temp->channel_id,time(),time())->getAll();
								foreach ($limit_amounts as $value){
									if($value->department_id && $value->department_id<>$department_id){
										continue;
									}
									if($value->country_group_id){
										$country = CodeCountryGroup::find('id = ? and country_codes like ?',$value->country_group_id,"%".$order->consignee_country_code."%")->getOne();
										if($country -> isNewRecord()){
											continue;
										}
										$country_code = explode(',', $country->country_codes);
									}
									$weight_sum = 0;
									$time = '';
									if($value->cycle == '0'){//每日
										$time =  date ( 'Y-m-d' );
									}elseif ($value->type == '1'){//每周
										$theday = date('N', time());//获取当前第几天
										$time = date('Y-m-d',strtotime('-'.($theday-1).'day'));//获取周一
									}elseif ($value->type == '2'){//每月
										$time = date('Y-m-01');//获取当月第一天
									}
									$order_ids = Order::find ( 'warehouse_out_time >= ? and channel_id = ?', strtotime ( $time . '00:00:00' ), $temp->channel_id);
									if($value->department_id){
										$order_ids->where('department_id = ?',$value->department_id);
									}
									if(isset($country_code) && !empty($country_code)){
										$order_ids->where('consignee_country_code in (?)',$country_code);
									}
									$order_ids = $order_ids->setColumns('order_id')->asArray()->getAll();
									$order_ids = Helper_Array::getCols($order_ids, 'order_id');
									if(count($order_ids)==0){
										$value->used_value = 0;
										$value->save();
									}
									if ($value->type == '0') {//票数
										if(count($order_ids)>0 && $value->used_value == 0){
											$order_count = Order::find ( 'order_id in (?)', $order_ids )->getCount ();
											$value->used_value = $order_count;
											$value->save();
										}
										if ($value->used_value+1 > $value->max_value) {
											continue 2;
										}
									} elseif($value->type == '1') {//实重
										if(count($order_ids)>0 && $value->used_value == 0){
											$value->used_value = Order::find('order_id in (?)',$order_ids)->getSum('weight_actual_out');
											$value->save();
										}
										if ($value->used_value+$total_weight > $value->max_value) {
											continue 2;
										}
									} elseif($value->type == '2') {//计费重
										$weight_outarr3 = Helper_Quote::getweightarr($order, 2);
										//渠道重量
										$weight_cost_out = $weight_outarr3['total_cost_weight'];
										if(count($order_ids)>0 && $value->used_value == 0){
											$value->used_value = Order::find('order_id in (?)',$order_ids)->getSum('weight_cost_out');
											$value->save();
										}
										if ($value->used_value+$weight_cost_out > $value->max_value) {
											continue 2;
										}
									}
									$limit_ids[] = $value->limitation_amount_id;
								}
								
								//获取异形包装费
								$special_fee=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
								if($special_fee->isNewRecord()){
									$special_count=0;
								}else{
									$special_count=$special_fee->quantity;
								}
								$network=Network::find("network_code=? ",$temp->channel->network_code)->getOne();
								$quote= new Helper_Quote();
								if ($order->customer->customs_code=='ALCN'){
									$cainiaofee = new Helper_CainiaoFee();
									$price=$cainiaofee->payment($order, $channelcostppr,$network->network_id,$special_count);
								}else{
									$price=$quote->payment($order, $channelcostppr,$network->network_id,$special_count);
								}
								//存在生效费用项无法计算
								if(@$fees_c['success']=='formulaerror'){
									$data['message']=$fees_c['success'];
									echo json_encode($data);
									exit();
								}
								if (count($price)&&$price['total_single_weight']){
									//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
									$order->total_single_weight = $price['total_single_weight'];
									$order->save();
								}
								if(count($price)<=0){
									continue;
								}
								if(!$price['public_price']){
									continue;
								}
								
								//如果设置阈值
								if ($product->threshold){
									//计算应收
									$total_receivable = Fee::find("fee_type= '1' and order_id=?",$order->order_id)->getAll();
									$public_price = 0;
									foreach ($total_receivable as $tot_r){
										if($tot_r->currency != 'CNY'){
											$public_price += Helper_Quote::exchangeRate($order->warehouse_confirm_time,$tot_r->amount, $tot_r->currency,0,'',$tot_r->rate);
										}else{
											$public_price += $tot_r->amount;
										}
									}
									//应收-应付
									$maoli = $public_price-$price['public_price'];
									if ($maoli<$product->threshold){
										$flag = 1;
										continue;
									}
								}
								$price_array[$channelcostppr->channel_cost_p_p_r_id]=$price['public_price'];
								$price_info_array[$channelcostppr->channel_cost_p_p_r_id]=$price['price_info'];
							}
							//判断是否有查询失败的报价
							if(count($price_array)==0 || max($price_array)==0){
								if ($flag==1){
									//渠道需优化
									$data['message']='channelcostoverthreshold';
								}else{
									//无可用渠道
									$data['message']='nousechannel';
								}
							}else{
								//获取最小的价格和价格表id
								$channel_cost_p_p_r_id=array_search(min($price_array), $price_array);
								$channel_cost_p_p_r=Channelcostppr::find('channel_cost_p_p_r_id=?',$channel_cost_p_p_r_id)->getOne();
								$channel_cost=ChannelCost::find('channel_cost_id=?',$channel_cost_p_p_r->channel_cost_id)->getOne();
								//实际此时是产品代码proudct_code
								$account_name=$price_info_array[$channel_cost_p_p_r_id]['account'];
								$account_sync=Accountsync::find('product_code=?',$account_name)->getOne();
								$channel=Channel::find('channel_id = ?',$channel_cost->channel_id)->getOne();
								if(!$account_sync->isNewRecord()){
									$account_name=$account_sync->account;
								}
								
								if($order->add_data_status!=1){
									if(in_array($order->service_code, $productcode)){
										$data['message']="incomplete";
										echo json_encode($data);
										exit();
									}else if(in_array($order->service_code,$productcode2)){
										if(in_array($order->channel_id,$channel_ids)){
											$data['message']="incomplete";
											echo json_encode($data);
											exit();
										}
									}
								}
								//#83619
								//当系统最终优选到的渠道为中美空派USPS
								//当单个产品数量超过20个时，进行打单报错
								//正式渠道是56,中美空派USPS
								if ($channel->channel_id==56){
									$order_product = Orderproduct::find('order_id=? and product_quantity>20',$order->order_id)->getOne();
									if (!$order_product->isNewRecord()){
										//订单含产品数量超过20个的产品，请联系客服处理。
										$data['message']="productnumovertwenty";
										echo json_encode($data);
										exit();
									}
								}
								//将计费重存入order中
								$order->weight_cost_out=$price_info_array[$channel_cost_p_p_r_id]['total_weight'];
								//将标签重存入order中
								$order->weight_label=$price_info_array[$channel_cost_p_p_r_id]['weight_label'];
								$order->save();
								//赋值
								$t_channel_id = $channel_cost->channel_id;
								//调用打单方法
								//修改Getlabel为getLabel
								$view=Helper_Common::getLabel($order, $account_name,$channel_cost->channel_id);
								if(!isset($view['errormessage']) || $view['errormessage']!=''){
									//渠道获取面单失败
									$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
								}else{//结束
									//删除原有费用
									Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
									//存入成本费用
									foreach ($price_info_array[$channel_cost_p_p_r_id]['fee_item'] as $key=>$fee_item){
										//判断费用项中是否存在费用
										if($fee_item['fee']!='0'){
											//币种
											if(@$fee_item['currency_code']){
												$currency_code = $fee_item['currency_code'];
												$rate = $fee_item['rate'];
											}else{
												$currency_code = 'CNY';
												$rate = 1;
											}
											//获取fee_sub_code
											$fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
											$fee= new Fee();
											$fee->changeProps(array(
												'order_id'=>$order->order_id,
												'fee_type'=>'2',
												'fee_item_code'=>$fee_sub_code->sub_code,
												'fee_item_name'=>$fee_sub_code->item_name,
												'quantity'=>$fee_item['quantity'],
												'currency'=>$currency_code,
												'rate'=>$rate,
												'amount'=>$fee_item['fee'],
												'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $channel_cost->channel->supplier_id
											));
											$fee->save();
										}
									}
									//保存出库渠道
									$order->channel_id=$channel_cost->channel_id;
									$order->add_data_status=1;
									if(count($limit_ids)>0){
										$limit_ids = array_unique($limit_ids);
										$limit_lists = ChannelLimitationAmount::find('channel_id = ? and limitation_amount_id in (?)',$channel_cost->channel_id,$limit_ids)->getAll();
										foreach ($limit_lists as $list){
											if($list->type == 0){
												$list->used_value = $list->used_value+1;
											}elseif($list->type == 1){
												$list->used_value = $list->used_value+$total_weight;
											}elseif($list->type == 2){
												$list->used_value = $list->used_value+$order->weight_cost_out;
											}
											$list->save();
										}
									}
									//存入打单账号
									$order->account=$view['account_number'];
									if($order->customer->customs_code=='ALCN'){
										self::saveOutCaiNiao($order);
									}else{
										self::saveoutevents($order);
									}
									$data['account']=$view['account'];
									$data['message']='true';
								}
							}
						}
					}
					self::changqdtosh($order);
					$fee_in=Fee::find('order_id=? and fee_type= "1"',$order->order_id)->getSum('amount');
					$fee_out=Fee::find('order_id=? and fee_type= "2"',$order->order_id)->getSum('amount');
					$order->profit=$fee_in-$fee_out;
					$order->save();
				}
			}
			//出库打单成功
			if ($data['message']=='true'){
				$weight_outarr = Helper_Quote::getweightarr($order, 2);
				//出库包裹总体积重
				$order->total_out_volumn_weight = $weight_outarr['total_volumn_weight'];
				$order->save();
			}
			//泛远面单
			if ($order->customer_id==1){
				//ali客户的
				Helper_Common::getfarlabeltoali($order);
			}else{
				//其他客户的
				Helper_Common::getfarlabel($order);
			}
			if($t_channel_id > 0){
				//监控时间判断
				$datetime2 = time();
				$channel = Channel::find('channel_id=?',$t_channel_id)->getOne();
				if(!$channel->isNewRecord()){
					//记录数量的字段放在第一个使用打单方式的渠道里面
					$channel2 = Channel::find('print_method=?',$channel->print_method)->order('channel_id')->getOne();
					if(($datetime2 - $datetime1) > 30){
						//超过30秒就记录
						$channel2->overtime = $channel2->overtime+1;
						$channel2->save();
						//连续10单超过30秒发邮件并清空记录
						if($channel2->overtime >= 10){
							$title = '打单超时警告';
							$content = $channel->print_method.'打单连续10个订单反应时间超30秒';
							//$email_response=Helper_Mailer::send('xujy@far800.com', $title, $content);
							$email_response=Helper_Mailer::send('bbop@far800.com', $title, $content);
							//$email_response=Helper_Mailer::send('liujian@far800.com', $title, $content);
						}
					}else{
						//未超过就清空
						$channel2->overtime = 0;
						$channel2->save();
					}
				}
			}
			$data['pdf_count']=$order->dhl_pdf_type;
			$data['hasbattery']=$order->has_battery;
			$data['declaration_type']=$order->declaration_type;
			//标签打印页面，打印成功，显示，例如，“物流单号:EA921007057CN”
			$data['tracking_no']=$order->tracking_no;
			$data['channel_name']=Channel::find('channel_id=?',$order->channel_id)->getOne()->channel_name;
			echo json_encode($data);
			exit();
		}
	}
	/**
	 * 国家
	 */
	function actioncodewordtwotree(){
		$arr=array();
		$checkeds = array ();
		$country=Country::find()->asArray()->getAll();
		if (request ( "checked" ) != null) {
			$checkeds = explode ( ",", request ( "checked" ) );
		}
		foreach ($country as $c){
			$arr[]=array(
				"id" => $c['code_word_two'],
				"text" => $c['chinese_name'],
				"checked" => in_array ( $c['code_word_two'], $checkeds ) ? "checked" : "",
				"attributes" => ""
			);
		}
		echo json_encode($arr);
		exit();
	}
	
	/**
	 * 包裹比对
	 */
	function actionComparison() {
		$select = Order::find ( 'order_status in (?) and LENGTH(trim(total_list_no))=0 ', array('6','8') )->Joinright ( 'tb_sub_code', 'sub_code', 'tb_order.order_id=tb_sub_code.order_id' );
		$account = Order::channelgroup();
		$first_key='';
		
		foreach ($account as $key=>$value){
			$first_key=$key;
			break;
		}
		
		//echo request ( 'account');exit;
		$account=@$account[request ( 'account',$first_key)];
		$select->where ( 'channel_id in (?)', $account );
		if(request('code_word_two')){
			$code_word_two=explode(',', request('code_word_two'));
			$select->where ( 'consignee_country_code in (?)', $code_word_two );
		}
		$staffrole=StaffRole::find('staff_id = ? and role_id in (?)',MyApp::currentUser('staff_id'),array("1","7"))->getOne();
		if($staffrole->isNewRecord()){
			if(MyApp::currentUser('department_id')=='7'){
				$select->where ( 'department_id in (?)', array(MyApp::currentUser('department_id'),'23') );
			}elseif (MyApp::currentUser('department_id')=='24' ){
				$select->where ( 'department_id in (?)', array(MyApp::currentUser('department_id'),'23','22') );
			}else {
				$select->where ( 'department_id = ?', MyApp::currentUser('department_id'));
			}
		}
		$order = $select->asArray ()
		->getAll ();
		//提交修改
		$order_id = array ();
		if (request ( 'sub_code' )) {
			$sub_code = explode ( "\r\n", request ( 'sub_code' ) );
			foreach ( $order as $v ) {
				$orders [] = $v ['order_id'];
			}
			//保存总单
			$channel_id = implode(',', $account);
			if(!request('total_list_no')){
				$total_list = new Totallist();
				$now='FR'.date('Ymd');
				$seq = Helper_Seq::nextVal ( $now );
				if ($seq < 1) {
					Helper_Seq::addSeq ( $now );
					$seq = 1;
				}
				$far_no=$now.sprintf("%03d",$seq);
				$total_list->total_list_no = $far_no;
				$channel_group = Channelgroup::find('channel_group_name = ?',request ( 'account',$first_key))->getOne();
				$total_list->channel_group_id = $channel_group->channel_group_id;
				$total_list->country_code = request('code_word_two');
				$total_list->record_order_date = strtotime(request ( 'record_order_date' ));
				$total_list->department_id = MyApp::currentUser('department_id');
				$total_list->operation_name = MyApp::currentUser('staff_name');
				$total_list->operation_time = time();
				$total_list->sort = request ( 'sort' );
				$total_list->save();
			}
			$orders = array_unique ( $orders );
			foreach ( $orders as $v1 ) {
				$code = Subcode::find ( 'order_id=?', $v1 )->getAll ();
				foreach ( $code as $v2 ) {
					if (! in_array ( strtoupper($v2->sub_code), $sub_code )) {
						$order_id [] = $v2->order_id;
						continue 2;
					}
				}
				$o = Order::find ( 'order_id=?', $v1 )->getOne ();
				$o->order_status = '7';
				$o->sort = request ( 'sort' );
				$o->record_order_date = strtotime(request ( 'record_order_date' ));
				$o->total_list_no = request('total_list_no')?request('total_list_no'):$far_no;
				$o->save ();
			}
			if (count ( $order_id )) {
				$select = Order::find ( 'tb_order.order_id in (?)', $order_id )->Joinright ( 'tb_sub_code', 'sub_code', 'tb_order.order_id=tb_sub_code.order_id' );
				$order_id = $select->asArray ()
				->getAll ();
				$this->_view ['order_id'] = $order_id;
			} else {
				
				return $this->_redirectMessage ( '核对成功', '成功', url ( '/comparison' ), 3 );
			}
		}
		$this->_view ['order'] = $order;
	}
	/**
	 * 判断一票多件是否全部选择
	 */
	function actionCheckonetomany(){
		$error['message'] = 'success';
		$sub_code = request ( 'sub_code' );
		$flag = 0;
		if (count($sub_code) >0) {
			foreach ( $sub_code as $v ) {
				$orid = Subcode::find('sub_code=?',$v)->getOne()->order_id;
				$orders [] = $orid;
			}
			$orders = array_unique ( $orders );
			foreach ( $orders as $v1 ) {
				$order = Order::find ( 'order_id=?', $v1 )->getOne ();
				$code = Subcode::find ( 'order_id=?', $v1 )->getAll ();
				$type = 0;
				foreach ( $code as $v3 ) {
					if($v3->sub_code){
						if (! in_array ( strtoupper($v3->sub_code), $sub_code )){
							$result = '<span style="color:red">'.$v3->sub_code.'</span>';
							$flag=1;
							$type=1;
						}else{
							$result = $v3->sub_code;
						}
						if ($type==1){
							$error['sub_code'][$order->tracking_no][] = $result;
						}
					}
				}
			}
			if ($flag==1){
				$error['message'] = 'payattentiononetomany';
			}
		}
		echo json_encode($error);
		exit;
	}
	/**
	 * 批量修改
	 *
	 */
	function actionBatchupdate(){
		if (request ( 'ali_order_no' )) {
			$ali_order_no = explode ( "\r\n", request ( 'ali_order_no' ) );
			if (count ( $ali_order_no )) {
				Order::meta ()->updateDbWhere ( array (
					'packing_type' => request ( 'packing_type' )
				), 'ali_order_no in (?)', $ali_order_no );
			}
			return $this->_redirectMessage ( '修改成功', '成功', url ( '/batchupdate' ), 2 );
		}
	}
	
	/**
	 * 获取物流单号
	 */
	function actionGettrackingno(){
		//--
		$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
		$channel=Channel::find('channel_id=?',$order->channel_id)->getOne();
		//获取sub_code个数
		$sub_code=Subcode::find('order_id=?',$order->order_id)->getAll();
		//是否打印fda发票
		$flag = false;
		if ($order->consignee_country_code=='US'&&$order->fda_company&&$order->fda_address&&$order->fda_city&&$order->fda_post_code){
			$flag = true;
		}
		$data=array(
			'tracking_no'=>$order->tracking_no,
			'country'=>$order->consignee_country_code,
			'sub_code_count'=>count($sub_code),
			'network_code'=>$channel->network_code,
			'flag'=>$flag,
			'dhl_pdf_type'=>$order->dhl_pdf_type,
			//客户单号
			'order_no'=>$order->order_no
		);
		echo json_encode($data);
		exit();
	}
	/**
	 * @todo fda发票json数据
	 * @author stt
	 * @since June 22th 2020
	 */
	function actionGetInvoiceData(){
		$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
		$channel=Channel::find('channel_id=?',$order->channel_id)->getOne();
		$invoice=array('items'=>array(),'total'=>'');
		foreach ($order->product as $v){
			$invoice['items'][]=array(
				//英文品名
				'description'=>$v->product_name_en_far?$v->product_name_en_far:$v->product_name_en,
				//产品数量
				'quantity'=>$v->product_quantity,
				//材质用途信息
				'material'=>$v->material_use,
				//HS 编码
				'hscode'=>$v->hs_code_far,
				//Origin固定
				'origin'=>'China',
				//申报单价
				'price'=>$v->declaration_price,
				//申报总价
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$invoice['total']+=round($v->product_quantity*$v->declaration_price,2);
		}
		$sender_name=$order->sender_name2;
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_city=$order->sender_city;
		$sender_country_code=$order->sender_country_code;
		if($channel->network_code=="UPS"){
			$account=UPSAccount::find("account = ?",$order->account)->getOne();
			if(!$account->isNewRecord()){
				$sender_name=$account->name;
				$sender_city=$account->city;
				$sender_address=$account->address;
				$sender_zip_code=$account->postcode;
			}
		}else{
			if($channel->sender_id>0){
				$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
				if(!$sender->isNewRecord()){
					$sender_name=$sender->sender_company;
					$sender_city=$sender->sender_city;
					$sender_address=$sender->sender_address;
					$sender_zip_code=$sender->sender_zip_code;
				}
			}
		}
		
		$shipper = array(
			'name' => $sender_name,
			'city' => $sender_city,
			'address' => $sender_address,
			'postcode' => $sender_zip_code,
		);
		$data=array(
			//收货人
			'consignee_company'=>$order->consignee_name2?$order->consignee_name2:$order->consignee_name1,
			'consignee_address'=>$order->consignee_street1.' '.$order->consignee_street2,
			'consignee_city'=>$order->consignee_city,
			'consignee_postal_code'=>$order->consignee_postal_code,
			'consignee_country_code'=>$order->consignee_country_code,
			'consignee_state'=>$order->consignee_state_region_code,
			'consignee_name'=>$order->consignee_name1,
			'consignee_phone'=>$order->consignee_mobile,
			//fda数据
			'fda_company'=>$order->fda_company,
			'fda_address'=>$order->fda_address,
			'fda_city'=>$order->fda_city,
			'fda_post_code'=>$order->fda_post_code,
			//包含产品数据
			'invoice'=>$invoice,
			//发货人
			'shipper'=>$shipper,
			//末端单号
			'tracking_no'=>$order->tracking_no
			
		);
		echo json_encode($data);
		QLog::log('stt:'.json_encode($data));
		exit();
	}
	function confirmhold($ali_order_no,$reason,$reason_type){
		//#83763
		//同一个订单，已有同类核查异常类型问题件时，且问题件是开启状态
		$abnormalpardel = Abnormalparcel::find('parcel_flag=1 and issue_type=2 and checkabnormal_type=? and ali_order_no=?',$reason_type,$ali_order_no)->getOne();
		if (!$abnormalpardel->isNewRecord()){
			return 'repeat';
		}
		$order=Order::find("ali_order_no = ?",$ali_order_no)->getOne();
		if($order->order_status!='12'){
			$order->order_status_copy=$order->order_status;
		}
		$order->order_status='12';
		$order->save();
		$quantity = Farpackage::find('order_id = ?',$order->order_id)->getSum('quantity');
		//存入一条问题件记录
		$now='ISSUE'.date('Ym');
		$seq = Helper_Seq::nextVal ( $now );
		if ($seq < 1) {
			Helper_Seq::addSeq ( $now );
			$seq = 1;
		}
		$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
		$abnormal_parcel_no=date('Ym').$seq;
		$abnormal= new Abnormalparcel();
		$abnormal->changeProps(array(
			'ali_order_no'=>$ali_order_no,
			'abnormal_parcel_no'=>$abnormal_parcel_no,
			'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
			'checkabnormal_type'=>$reason_type,
			'issue_type'=>'2',
			'issue_content'=>$reason
		));
		$abnormal->save();
		//存入最新跟进
		$abnormal_history= new Abnormalparcelhistory();
		$abnormal_history->changeProps(array(
			'abnormal_parcel_id'=>$abnormal->abnormal_parcel_id,
			'follow_up_content'=>$reason,
			'follow_up_operator'=>MyApp::currentUser('staff_name')
		));
		$abnormal_history->save();
		$dpt = Department::find('department_id = ?',MyApp::currentUser('department_id'))->getOne();
		$location = Helper_Chinese::toPinYin(substr($dpt->department_name,0,8));
		$tracking = new Tracking();
		//修改code语言
		$tracking->changeProps(array(
			'order_id'=>$order->order_id,
			'customer_id'=>$order->customer_id,
			'far_no'=>$order->far_no,
			'tracking_code'=>'F_CHECK_5067',
			'location'=>$location,
			'timezone'=>'8',
			'trace_desc_en'=>'Inspecting Exception: Please check the notice or contact: 400-085-7988 or declaration@far800.com',
			'trace_desc_cn'=>'包裹核查异常，请查询通知或联系：400-085-7988 或 declaration@far800.com',
			'operator_name'=>MyApp::currentUser('starff_name'),
			'confirm_flag'=>1,
			'quantity'=>$quantity,
			'trace_time'=>time()
		));
		$tracking->save();
		return 'hold';
	}
	/**
	 * 单票核查
	 */
	function actionconfirmone(){
		if(request("ali_order_no")){
			$data=array();
			$reason=array();
			$order=Order::find("ali_order_no=?",request("ali_order_no"))->getOne();
			
			$cuu_bool = Helper_Currency::isCurreny($order);
			//判断币种
			if(!$cuu_bool){
				$data['message']='cuufalse';
				echo json_encode($data);
				exit();
			}
			$service_product = Product::find('product_name=?',$order->service_code)->getOne();
			if($order->isNewRecord()){
				$data['message']='notexists';
			}else{
				$data['service_product']=$service_product->toArray();
				$country=Country::find("code_word_two=?",$order->consignee_country_code)->getOne();
				$data['destination']=$order->consignee_country_code;
				$data['country']=$country->chinese_name;
				$data['weight_income_in']=$order->weight_income_in;
				$data['has_battery']=$order->has_battery;
				$data['has_battery_num']=$order->has_battery_num;
				$data['amount']=$order->total_amount;
				$data['is_pda']=$order->is_pda;
				$data['fda_company']=$order->fda_company;
				$data['fda_address']=$order->fda_address;
				$data['fda_city']=$order->fda_city;
				$data['fda_post_code']=$order->fda_post_code;
				$data['is_declaration']='';
				$data['must_declaration']='';
				if($order->declaration_type){
					if($order->declaration_type=='DL'){
						$data['is_declaration']='是';
					}else{
						$data['is_declaration']='否';
					}
					if($order->declaration_type!='DL'&&($order->total_amount>700 || $order->weight_actual_in>70)){
						$data['must_declaration']='是';
					}else{
						$data['must_declaration']='否';
					}
				}else{
					$data['is_declaration']='否';
				}
				if(!in_array($order->department_id, RelevantDepartment::relateddepartmentids())){
					$data['message']='notsamewarehouse';
				}elseif($order->order_status=='10'){
					//已核查
					$data['message']='confirmed';
				}elseif ($order->order_status=='12'){
					//已扣件
					$data['message']='issued';
				}else if ($order->order_status=='5'){
					$issues_count = Abnormalparcel::find('ali_order_no = ? and issue_type = "5" and parcel_flag != "2"',request("ali_order_no"))->getCount();
					if($issues_count>0){
						$data['message']='hasissue';
						echo json_encode($data);
						exit();
					}
					$data['success_message']='';
					
					if($order->total_amount > 700 || $order->weight_actual_in > 70){
						//强制报关件
						if($order->service_code == 'Express_Standard_Global' && $order->declaration_type=='QT'){
							$reason[] = '无FDA/税号/报关资料';
						}
					}
					//EMS专线,当申报总价高于400.00USD时，限制入库，并提示：EMS超400USD，无服务
					// 					if($order->service_code == 'EMS-FY'){
					// 						if($order->total_amount>400){
					// 							$reason[]= '超400美金/EMS无服务';
					// 						}
					// 					}
					// 					//中美专线，订单总价超800USD时，限制入库，并提示：超800美金无服务
					// 					if($order->service_code == 'US-FY'){
					// 						if($order->total_amount>800){
					// 							$reason[]= '超800美金/中美无服务';
					// 						}
					// 					}
					//申报总价限制
					if($service_product->declare_threshold && $order->total_amount > $service_product->declare_threshold){
						$reason[]= '申报总价超'.$service_product->declare_threshold.'USD/'.$order->service_code.'无服务';
					}
					
					if ($order->declaration_type=='DL'){
						//报关件
						if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'WIG-FY'){
							$data['success_message']='declaration';
						}else if($order->service_code == 'US-FY' || $order->service_code == 'EUUS-FY' || $order->service_code == 'EMS-FY'){
							$reason[]= '无报关服务';
						}
					}
					$info=array();
					$flag=false;
					$flag1=false;
					foreach ($order->product as $product){
						// 					    if(strstr($product->product_name_far, '电') || strstr($product->product_name_far, '磁') || strstr($product->product_name_far, '液') || strstr($product->product_name_far, '粉')){
						// 					        $reason[]= '涉电/磁/液/粉类问题';
						// 					    }
						if($order->consignee_country_code=='US'){
							if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'EUUS-FY' || $order->service_code == 'WIG-FY'){
								if(strlen($product->product_name_far)){
									if(strstr($product->product_name_far, '眼镜') || strstr($product->product_name_far, '太阳镜')){
										$flag=true;
									}
									if(strstr($product->product_name_far, '睫毛') || strstr($product->product_name_far, '假睫毛') || strstr($product->product_name_far, '眼睫毛')){
										$flag1=true;
									}
								}else{
									if(strstr($product->product_name, '眼镜') || strstr($product->product_name, '太阳镜')){
										$flag=true;
									}
									if(strstr($product->product_name, '睫毛') || strstr($product->product_name, '假睫毛') || strstr($product->product_name, '眼睫毛')){
										$flag1=true;
									}
								}
							}
						}
						$info[]=array(
							"product_name"=>$product->product_name_far?$product->product_name_far:$product->product_name,
							"product_name_en"=>$product->product_name_en_far?$product->product_name_en_far:$product->product_name_en,
							"product_quantity"=>$product->product_quantity,
							"declaration_price"=>$product->declaration_price
						);
					}
					if($flag){
						//核查FDA证书
						$data['success_message']='checkfda';
					}
					if($flag1){
						//核查FDA证书
						$data['success_message']='checkfda1';
					}
					if($order->consignee_country_code=='BR'){
						//检查税号
						if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'EUUS-FY' || $order->service_code == 'WIG-FY'){
							$tax_payer_id = preg_replace( '/[^0-9]/', '', $order->tax_payer_id);
							if(strlen($tax_payer_id) <> 11 && strlen($tax_payer_id) <> 14){
								$reason[]= '无FDA/税号/报关资料';
							}
						}
					}
					$quote = new Helper_Quote ();
					//计算应收
					if ($order->customer->customs_code=='ALCN'){
						$cainiaofee = new Helper_CainiaoFee();
						$receivable = $cainiaofee->receivable ( $order, $order->weight_income_in, $order->boxnum, $order->packagenum, $order->specialpackagenum );
					}else{
						$receivable = $quote->receivable ( $order, $order->weight_income_in, $order->boxnum, $order->packagenum, $order->specialpackagenum );
					}
					//公式存在错误
					if(@$receivable['success']=='formulaerror'){
						//存在生效费用项无法计算
						$data['message']=$receivable['success'];
						echo json_encode($data);
						exit();
					}
					$country=Country::find("code_word_two=?",$order->consignee_country_code)->getOne();
					$data['message']='success';
					$data['product']=$info;
					$data['country']=$country->chinese_name;
					$data['weight_income_in']=$order->weight_income_in;
					$data['amount']=$order->total_amount;
					if($order->service_code=='WIG-FY'){
						if(strlen($order->consignee_street1.' '.$order->consignee_street2)>70){
							$data['message']='fedexerror';
						}else {
							$address=Order::splitAddressfedex($order->consignee_street1.' '.$order->consignee_street2);
							if(count($address)>2){
								$data['message']='fedexerror';
							}
						}
					}
					$farpackages = Farpackage::find('order_id = ?',$order->order_id)->getAll();
					$hold_flag = '';
					$i=1;
					foreach ($farpackages as $farpackage){
						$arr = array($farpackage->length,$farpackage->width,$farpackage->height);
						sort($arr);
						//最长边限制
						if($service_product->length && $arr[2]>=$service_product->length){
							$hold_flag .= '最长边<'.$service_product->length.' ';
						}
						//第二长边限制
						if ($service_product->width && $arr[1]>=$service_product->width){
							$hold_flag .= '第二长边<'.$service_product->width.' ';
						}
						//高限制
						if ($service_product->height && $arr[0]>=$service_product->height){
							$hold_flag .= '高<'.$service_product->height.' ';
						}
						//周长限制
						if ($service_product->perimeter && 4*($arr[2]+$arr[1]+$arr[0])>=$service_product->perimeter){
							$hold_flag .= '周长<'.$service_product->perimeter.' ';
						}
						//围长限制
						if ($service_product->girth && $arr[2]+2*($arr[1]+$arr[0])>=$service_product->girth){
							$hold_flag .= '围长<'.$service_product->girth.' ';
						}
						//单个包裹实重限制
						if ($service_product->weight && $farpackage->weight>=$service_product->weight){
							$hold_flag .= '单个包裹实重限制<'.$service_product->weight.' ';
						}
						$common_flag = '第'.$i.'条产品规格错误'.$service_product->product_chinese_name.'服务范围：';
						if(strlen($hold_flag)>0){
							$hold_flag = $common_flag.$hold_flag;
							break;
						}
						$i++;
					}
					if ($service_product->total_cost_weight && $order->weight_income_in>=$service_product->total_cost_weight){
						$hold_flag .= '整票计费重限制<'.$service_product->total_cost_weight.' ';
					}
					if(strlen($hold_flag)>0){
						$reason[] = '产品规格不支持';
						$reason[] = $hold_flag;
					}
					if($service_product->check_zip=='1'){
						$noservice_zip = Noserivcezipcode::find("zip_code = ? and service_code = ? and city = '' and country_code = ''",$order->consignee_postal_code,$order->service_code)->getOne();
						if(!$noservice_zip->isNewRecord()){
							$reason[]= '邮编/城市/国家无服务';
						}else{
							$noservice_city = Noserivcezipcode::find("city = ? and service_code = ? and zip_code = '' and country_code = ''",$order->consignee_city,$order->service_code)->getOne();
							if(!$noservice_city->isNewRecord()){
								$reason[]= '邮编/城市/国家无服务';
							}else{
								$noservice_country = Noserivcezipcode::find("country_code = ? and service_code = ? and zip_code = '' and city = ''",$order->consignee_country_code,$order->service_code)->getOne();
								if(!$noservice_country->isNewRecord()){
									$reason[]= '邮编/城市/国家无服务';
								}else{
									$noservice_country = Noserivcezipcode::find("zip_code = ? and country_code = ? and service_code = ? and city = ''",$order->consignee_postal_code,$order->consignee_country_code,$order->service_code)->getOne();
									if(!$noservice_country->isNewRecord()){
										$reason[]= '邮编/城市/国家无服务';
									}else{
										$noservice_country = Noserivcezipcode::find("zip_code = ? and city = ? and service_code = ? and country_code = ''",$order->consignee_postal_code,$order->consignee_city,$order->service_code)->getOne();
										if(!$noservice_country->isNewRecord()){
											$reason[]= '邮编/城市/国家无服务';
										}else{
											$noservice_country = Noserivcezipcode::find("country_code = ? and city = ? and service_code = ? and zip_code = ''",$order->consignee_country_code,$order->consignee_city,$order->service_code)->getOne();
											if(!$noservice_country->isNewRecord()){
												$reason[]= '邮编/城市/国家无服务';
											}else{
												$noservice_country = Noserivcezipcode::find("zip_code = ? and country_code = ? and city = ? and service_code = ?",$order->consignee_postal_code,$order->consignee_country_code,$order->consignee_city,$order->service_code)->getOne();
												if(!$noservice_country->isNewRecord()){
													$reason[]= '邮编/城市/国家无服务';
												}
											}
										}
									}
								}
							}
						}
					}
					if(!count($receivable)){
						$reason[]= '产品规格不支持1';
					}
					if(count($reason)>0){
						$rea = array_unique($reason);
						$rea = array_filter($rea);
						$rea = implode(',', $rea);
						$rea=str_replace('产品规格不支持1', '无法计算费用', $rea);
						$hold = self::confirmhold(request("ali_order_no"), $rea,str_replace('1','',$reason[0]));
						//该订单已有同类核查异常类型，请查询问题件！
						if($hold == 'repeat'){
							$data['message']='repeat';
							echo json_encode($data);
							exit();
						}
						if($hold == 'hold'){
							$data['message']='issued';
							echo json_encode($data);
							exit();
						}
						if($hold == 'buckle'){
							$data['message']='buckle';
							echo json_encode($data);
							exit();
						}
					}
				}else{
					$data['message']='error';
				}
			}
			echo json_encode($data);
			exit();
		}
	}
	/**
	 * 核查成功
	 */
	function actionSaveconfirmone(){
		$order=Order::find("ali_order_no=? and order_status =?",request("ali_order_no"),Order::STATUS_IN)->getOne();
		$data = array();
		if ($order->isNewRecord()){
			$data['msg']='noorder';
			echo json_encode($data);
			exit();
		}
		$customer = Customer::find('customer_id=?',$order->customer_id)->getOne();
		//客户管理->客户类型1:线上  客户管理->支付类型->订单支付规则1：支付通知 才会照片验证
		if ($customer->customer_type==1 && $customer->payment_rule==1){
			//是否有图片
			$image = File::find('order_id=?',$order->order_id)->getOne();
			$uploadoss = new Helper_AlipicsOss();
			//file表不存在 并且快手不存在
			if ($image->isNewRecord() && !$uploadoss->doesExistkuaishou($order->order_no.'.jpg')){
				$data['message']='nophoto';
				echo json_encode($data);
				exit();
			}
		}
		$fee_item_code = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'item_code,sub_code,item_name' )
			->asArray ()
			->getAll (), 'item_code' );
		$quote= new Helper_Quote();
		if ($order->customer->customs_code=='ALCN'){
			$cainiaofee = new Helper_CainiaoFee();
			$receivable = $cainiaofee->receivable ( $order, $order->weight_income_in, $order->boxnum, $order->packagenum, $order->specialpackagenum );
		}else{
			$receivable = $quote->receivable ( $order, $order->weight_income_in, $order->boxnum, $order->packagenum, $order->specialpackagenum );
		}
		
		//print_r($receivable);exit;
		Fee::meta ()->deleteWhere ( 'fee_type=1 and order_id=?', $order->order_id );
		QLog::log ( print_r ( $receivable, true ) );
		foreach ( $receivable as $key => $value ) {
			if ($value ['fee']) {
				//币种
				if(@$value['currency_code']){
					$currency_code = $value['currency_code'];
					$rate = $value['rate'];
				}else{
					$currency_code = 'CNY';
					$rate = 1;
				}
				$fee = new Fee ( array (
					'order_id' => $order->order_id,
					'btype_id' => $order->customer_id,
					'fee_type' => 1,
					'fee_item_code' => $fee_item_code [$key] ['sub_code'],
					'fee_item_name' => $fee_item_code [$key] ['item_name'],
					'quantity' => $value ['quantity'],
					'currency'=>$currency_code,
					'rate'=>$rate,
					'amount' => $value ['fee']
				) );
				$fee->save ();
			}
		}
		$event_location = '';
		$department = Department::find ( 'department_id=?', MyApp::currentUser ( 'department_id' ) )->getOne ();
		if ($department->department_name == '杭州仓') {
			$event_location = '杭州';
		} elseif ($department->department_name == '义乌仓') {
			$event_location = '义乌';
		} elseif ($department->department_name == '上海仓') {
			$event_location = '上海';
		}elseif ($department->department_name == '广州仓') {
			$event_location = '广州';
		}elseif ($department->department_name == '青岛仓') {
			$event_location = '青岛';
		}elseif ($department->department_name == '深圳仓') {
			$event_location = '深圳';
		}elseif ($department->department_name == '南京仓') {
			$event_location = '南京';
		}elseif ($department->department_name == '连云港仓') {
			$event_location = '连云港';
		}
		$event_time = time ();
		if($order->customer->customs_code=='ALCN'){
			$in=CaiNiao::find('order_id= ? and cainiao_code="FEE"',$order->order_id)->getOne();
			if($in->isNewRecord()){
				$in->order_id = $order->order_id;
				$in->cainiao_code = 'FEE';
				$in->cainiao_time = $event_time;
				$in->confirm_flag = '1';
				$in->operator =MyApp::currentUser('staff_name');
				$in->save ();
			}
			$ins=CaiNiao::find('order_id= ? and cainiao_code="WAREHOUSE_INBOUND"',$order->order_id)->getOne();
			if($ins->isNewRecord()){
				$ins->order_id = $order->order_id;
				$ins->cainiao_code = 'WAREHOUSE_INBOUND';
				$ins->cainiao_time = $event_time+600;
				$ins->confirm_flag = '1';
				$ins->operator =MyApp::currentUser('staff_name');
				$ins->save ();
			}
		}else{
			$helper_event = new Helper_Event();
			if($order->customer->customs_code=='ALPL'){
				$in=Event::find('order_id= ? and (event_code="CHECK_WEIGHT_CALLBACK" or event_code="CONFIRM_CALLBACK")',$order->order_id)->getOne();
				if($in->isNewRecord()){
					$helper_event->saveEvent($order->customer_id, $order->order_id, 'CHECK_WEIGHT_CALLBACK',$event_time- rand(2, 3)*60+rand(1, 59) , $event_location );
					$helper_event->saveEvent($order->customer_id, $order->order_id, 'CONFIRM_CALLBACK',$event_time , $event_location );
				}
			}else{
				$in=Event::find('order_id= ? and (event_code="CHECK_WEIGHT" or event_code="CONFIRM")',$order->order_id)->getOne();
				if($in->isNewRecord()){
					$this->eventSave ( $order->order_id, 'CHECK_WEIGHT', $event_time- rand(2, 3)*60+rand(1, 59), $event_location,$order->customer_id);
					$this->eventSave ( $order->order_id, 'CONFIRM', $event_time, $event_location ,$order->customer_id);
				}
			}
		}
		//已查验
		$order->order_status='10';
		//核查时间
		$order->warehouse_confirm_time =$event_time;
		
		//入库包裹总体积重
		$weightarr = Helper_Quote::getweightarr($order, 1);
		$order->total_volumn_weight = $weightarr['total_volumn_weight'];
		
		//#83246
		//客户预报重量
		$forecast_weight_arr = Helper_Quote::getweightarr($order, 1, null, $order->packages);
		
		$flag = 1;
		//客户管理->支付类型->订单支付规则2：无支付通知
		if ($customer->payment_rule==2){
			///删除收货计费重不超过客重
			//收货计费重不超过客户预报计费重（用客户预报的长宽高进行计算后的计费重）
			if ($customer->check_ruletwo=='1'){
				if ($weightarr['total_cost_weight']>$forecast_weight_arr['total_cost_weight']){
					$flag = 2;
				}
			}
			//符合条件的订单状态变为已支付
			if ($flag==1){
				//已支付
				$order->order_status='4';
				//支付时间
				$order->payment_time =$event_time;
				//复制far_package FAR入库包裹信息
				$far_out_packages = Farpackage::find('order_id = ?',$order->order_id)->getAll();
				//删除tb_far_out_package对应渠道包裹信息
				Faroutpackage::meta()->destroyWhere('order_id=?',$order->order_id);
				foreach ($far_out_packages as $far_out_package){
					//添加新的渠道包裹信息数据
					$new_far_out_package = new Faroutpackage(array(
						'order_id'=>$order->order_id,
						'far_id'=>$far_out_package->far_package_id,
						'quantity_out'=>$far_out_package->quantity,
						'length_out'=>$far_out_package->length,
						'width_out'=>$far_out_package->width,
						'height_out'=>$far_out_package->height,
						'weight_out'=>$far_out_package->weight,
					));
					//保存
					$new_far_out_package->save();
				}
			}
			
			//客户管理->支付类型->订单支付规则3：核查成功
		}elseif ($customer->payment_rule==3){
			//已支付
			$order->order_status='4';
			//支付时间
			$order->payment_time =$event_time;
			//复制far_package FAR入库包裹信息
			$far_out_packages = Farpackage::find('order_id = ?',$order->order_id)->getAll();
			//删除tb_far_out_package对应渠道包裹信息
			Faroutpackage::meta()->destroyWhere('order_id=?',$order->order_id);
			foreach ($far_out_packages as $far_out_package){
				//添加新的渠道包裹信息数据
				$new_far_out_package = new Faroutpackage(array(
					'order_id'=>$order->order_id,
					'far_id'=>$far_out_package->far_package_id,
					'quantity_out'=>$far_out_package->quantity,
					'length_out'=>$far_out_package->length,
					'width_out'=>$far_out_package->width,
					'height_out'=>$far_out_package->height,
					'weight_out'=>$far_out_package->weight,
				));
				$new_far_out_package->save();
			}
		}
		
		//入库包裹总数
		$package_total_in=Farpackage::find('order_id=?',$order->order_id)->getSum('quantity');
		$order->package_total_in=$package_total_in;
		
		$order->save();
		//发送照片
		//客户管理->客户类型1:线上 客户管理->支付类型->订单支付规则1：支付通知 才会发送照片
		if ($customer->customer_type==1 && $customer->payment_rule==1){
			//Helper_Common::sendcheckweightphoto($order);
		}
		$data['msg']='success';
		echo json_encode($data);
		exit();
	}
	/**
	 * 扣件
	 */
	function actionSavehold(){
		$order=Order::find("ali_order_no=?",request("ali_order_no"))->getOne();
		//#83763
		$data['message']="";
		//同一个订单，已有同类核查异常类型问题件时，且问题件是开启状态
		$abnormalpardel = Abnormalparcel::find('parcel_flag=1 and issue_type=2 and checkabnormal_type=? and ali_order_no=?',request('reason_type'),request('ali_order_no'))->getOne();
		if (!$abnormalpardel->isNewRecord()){
			//该订单已有同类核查异常类型，请查询问题件！
			$data['message']="repeat";
			echo json_encode($data);
			exit();
		}
		$order->order_status='12';
		$order->order_status_copy='5';
		$order->save();
		$quantity = Farpackage::find('order_id = ?',$order->order_id)->getSum('quantity');
		//存入一条问题件记录
		$now='ISSUE'.date('Ym');
		$seq = Helper_Seq::nextVal ( $now );
		if ($seq < 1) {
			Helper_Seq::addSeq ( $now );
			$seq = 1;
		}
		$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
		$abnormal_parcel_no=date('Ym').$seq;
		$abnormal= new Abnormalparcel();
		$abnormal->changeProps(array(
			'ali_order_no'=>request('ali_order_no'),
			'abnormal_parcel_no'=>$abnormal_parcel_no,
			'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
			'checkabnormal_type'=>request('reason_type'),
			'issue_type'=>'2',
			'issue_content'=>request('reason')
		));
		$abnormal->save();
		//存入最新跟进
		$abnormal_history= new Abnormalparcelhistory();
		$abnormal_history->changeProps(array(
			'abnormal_parcel_id'=>$abnormal->abnormal_parcel_id,
			'follow_up_content'=>request('reason'),
			'follow_up_operator'=>MyApp::currentUser('staff_name')
		));
		$abnormal_history->save();
		//核查异常
		if(request('reason_type')!='其他'){
			$dpt = Department::find('department_id = ?',MyApp::currentUser('department_id'))->getOne();
			$location = Helper_Chinese::toPinYin(substr($dpt->department_name,0,8));
			$tracking = new Tracking();
			$tracking->changeProps(array(
				'order_id'=>$order->order_id,
				'customer_id'=>$order->customer_id,
				'far_no'=>$order->far_no,
				'tracking_code'=>'F_CHECK_5067',
				'location'=>$location,
				'timezone'=>'8',
				'trace_desc_en'=>'Inspecting Exception: Please check the notice or contact: 400-085-7988 or declaration@far800.com',
				'trace_desc_cn'=>'包裹核查异常，请查询通知或联系：400-085-7988 或 declaration@far800.com',
				'operator_name'=>MyApp::currentUser('starff_name'),
				'confirm_flag'=>1,
				'quantity'=>$quantity,
				'trace_time'=>time()
			));
			$tracking->save();
		}
		echo json_encode($data);
		exit();
	}
	/**
	 * 保存订单是否带电
	 */
	function actionSaveHasBattery(){
		$order=Order::find("ali_order_no=?",request("ali_order_no"))->getOne();
		$order->has_battery=request("has_battery");
		$order->is_pda=request("is_pda");
		if (request("has_battery")==1){
			$order->has_battery_num=request("has_battery_num");
		}
		if(request("is_pda") ==1){
			$order->fda_company=request("fda_company");
			$order->fda_address=request("fda_address");
			$order->fda_city=request("fda_city");
			$order->fda_post_code=request("fda_post_code");
		}
		$order->save();
		exit();
	}
	function actionsaveissue(){
		if(request('scan_no')&&request('reson')){
			$now='ISSUE'.date('Ym');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
			$abnormal_parcel_no=date('Ym').$seq;
			$no_own_order = new Abnormalparcel(array(
				'reference_no'=>request('scan_no'),
				'location'=>MyApp::currentUser('department_id'),
				'abnormal_parcel_no'=>$abnormal_parcel_no,
				'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
				'issue_type'=>'4',
				'issue_content'=>request('reson'),
			));
			$no_own_order->save();
			$history=new Abnormalparcelhistory();
			$history->abnormal_parcel_id=$no_own_order->abnormal_parcel_id;
			$history->follow_up_content=$no_own_order->issue_content;
			$history->follow_up_operator=MyApp::currentUser("staff_name");
			$history->save();
			return 'success';
		}else{
			return 'f';
		}
	}
	/**
	 * 重量对比表
	 */
	function actionWeighttable(){
		$order=Order::find('ali_order_no = ?',request('ali_order_no'))->getOne();
		$sub_codes=Subcode::find("order_id=? and sub_code!= ?",$order->order_id,$order->tracking_no)->getAll();
		$first_sub_code=Subcode::find("order_id=? and sub_code= ?",$order->order_id,$order->tracking_no)->getOne();
		$data=array();
		$first_volume_weight=$first_sub_code['length']*$first_sub_code['width']*$first_sub_code['height']/5000;
		$first_width=$first_sub_code['width']<=0?'      ':$first_sub_code['width'];
		$data[1]=array(
			'barcode_url'=>'http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$order->tracking_no,
			'tracking_no'=>$order->tracking_no,
			'actual_weight'=>$first_sub_code['weight'],
			'size'=>$first_sub_code['length'].' X '.$first_width.' X '.$first_sub_code['height'],
			'volume_weight'=>$first_volume_weight<=0?'':$first_volume_weight,
			'max_weight'=>($first_volume_weight>$first_sub_code['weight'])?$first_volume_weight:$first_sub_code['weight'],
		);
		$page=request('page');
		$key='2';
		for ($j=($page-1)*14;$j<$page*14;$j++){
			if(isset($sub_codes[$j])){
				$volume_weight=$sub_codes[$j]['length']*$sub_codes[$j]['width']*$sub_codes[$j]['height']/5000;
				$width=$sub_codes[$j]['width']<=0?'      ':$sub_codes[$j]['width'];
				$data[$key]=array(
					'barcode_url'=>'http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$sub_codes[$j]['sub_code'],
					'tracking_no'=>$sub_codes[$j]['sub_code'],
					'actual_weight'=>$sub_codes[$j]['weight'],
					'size'=>$sub_codes[$j]['length'].' X '.$width.' X '.$sub_codes[$j]['height'],
					'volume_weight'=>$volume_weight<=0?'':$first_volume_weight,
					'max_weight'=>($volume_weight>$sub_codes[$j]['weight'])?$volume_weight:$sub_codes[$j]['weight'],
				);
			}else{
				$data[$key]=array(
					'barcode_url'=>'',
					'tracking_no'=>'',
					'actual_weight'=>'',
					'size'=>'',
					'volume_weight'=>'',
					'max_weight'=>'',
				);
			}
			$key++;
		}
		echo json_encode($data);die();
	}
	/**
	 * 打印重量对比表
	 */
	function actionprintweighttable(){
		if(request_is_post()){
			$data=array();
			$order=Order::find('ali_order_no=?',request('order_no'))->getOne();
			if($order->isNewRecord()){
				//查询子单信息
				$subcode=Subcode::find('sub_code=?',request('order_no'))->getOne();
				if($subcode->isNewRecord()){
					$data['message']='notexist';
					$data['sub_code_count']='';
					$data['ali_order_no']='';
				}else{
					$order=Order::find('order_id=?',$subcode->order_id)->getOne();
					$data['message']='';
					$data['sub_code_count']=Subcode::find('order_id=?',$order->order_id)->getAll()->count();
					$data['ali_order_no']=$order->ali_order_no;
				}
			}else{
				$data['message']='';
				$data['sub_code_count']=Subcode::find('order_id=?',$order->order_id)->getAll()->count();
				$data['ali_order_no']=$order->ali_order_no;
			}
			echo json_encode($data);
			exit();
		}
	}
	/**
	 * 托盘列表
	 */
	function actionPalletlist(){
		$palletlist=Pallet::find();
		//日期
		if(request("start_date")){
			$palletlist->where("create_time >=?",strtotime(request("start_date").' 00:00:00'));
		}
		if (request("end_date")){
			$palletlist->where("create_time <=?",strtotime(request("end_date").' 23:59:59'));
		}
		//阿里单号
		if(request('pallet_no')){
			$palletlist->where('pallet_no=?',request('pallet_no'));
		}
		if(request('action')=='new_pallet'){
			//生成托盘号
			$now='PALLET'.date('Ymd');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
			$pallet_no='T'.date('ymd').$seq;
			//托盘表生成新纪录
			$pallet=new Pallet(array(
				'pallet_no'=>$pallet_no,
				'channel_name'=>request('channel_name'),
				'operator'=>MyApp::currentUser('staff_name')
			));
			$pallet->save();
			return $this->_redirect(url('/Palletlist'));
		}
		$pagination = null;
		$palletlist=$palletlist->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('pallet_id desc')->getAll();
		$this->_view['palletlist']=$palletlist;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * 出库打托
	 */
	function actionPallet(){
		$pallet=Pallet::find('pallet_id=?',request('pallet_id'))->getOne();
		$this->_view['pallet']=$pallet;
	}
	/**
	 * 判断运单号数量以及与上一个单号是否属于同一个订单
	 */
	function actiongetpackagecount(){
		$subcode=Subcode::find('sub_code=?',request('tracking_code'))->getOne();
		$data=array();
		if($subcode->isNewRecord()){
			$data['status']='error';
			$data['count']='';
			$data['message']='notexits';
			$data['same']='false';
		}else if($subcode->pallet_no){
			$data['status']='error';
			$data['count']='';
			$data['message']='scanned';
			$data['same']='false';
		}else{
			//获取打单账号
			$channel_group=order::channelgroup();
			$order=Order::find('order_id=?',$subcode->order_id)->getOne();
			$pallet=Pallet::find('pallet_no=?',request('pallet_no'))->getOne();
			if(!in_array($order->channel_id, $channel_group[$pallet->channel_name])){
				$data['status']='error';
				$data['count']='';
				$data['message']='channel_wrong';
				$data['same']='false';
			}else{
				//根据order_id判断订单是否是一票多件
				$subcode_all=Subcode::find('order_id=?',$subcode->order_id)->getAll();
				if(count($subcode_all)>1){//一票多件
					$data['status']='success';
					$data['count']=count($subcode_all);
					//判断与上一个运单是否属于一个订单
					$last_tracking_code=Subcode::find('sub_code=?',request('last_tracking_code'))->getOne();
					if($last_tracking_code->order_id==$subcode->order_id){//同一订单
						$data['message']='录入结果：成功';
						$data['same']='true';
						//判断是否勾选锁定选项
						if(request('status')=='checked'){
							//写入上一个单号长宽高
							$subcode->weight=$last_tracking_code->weight;
							$subcode->length=$last_tracking_code->length;
							$subcode->width=$last_tracking_code->width;
							$subcode->height=$last_tracking_code->height;
							$subcode->save();
						}
					}else{//不同订单
						$data['message']='';
						$data['same']='false';
					}
				}else{
					$data['status']='success';
					$data['count']='1';
					$data['message']='';
					$data['same']='false';
				}
			}
		}
		echo json_encode($data);
		exit();
	}
	/**
	 * 保存托盘号和包裹长宽高
	 */
	function actionsavepackageinfo(){
		$subcode=Subcode::find('sub_code=?',request('tracking_code'))->getOne();
		$subcode->weight=request('weight');
		$subcode->length=request('length');
		$subcode->width=request('width');
		$subcode->height=request('height');
		$subcode->save();
		$data['status']='success';
		$data['message']='';
		echo json_encode($data);
		exit();
	}
	/**
	 * 保存托盘号
	 */
	function actionsavepalletno(){
		$pallet_nos=explode(';', trim(request('tracking_codes'),";"));
		//清除子单中原有托盘数据
		$old_subcodes=Subcode::find('pallet_no=?',request('pallet_no'))->getAll();
		foreach ($old_subcodes as $old_subcode){
			$old_subcode->pallet_no='';
			$old_subcode->save();
		}
		//写入新数据
		foreach ($pallet_nos as $temp){
			$subcode=Subcode::find('sub_code=?',$temp)->getOne();
			$subcode->pallet_no=request('pallet_no');
			$subcode->save();
		}
		exit();
	}
	/**
	 * 打印托盘标签
	 */
	function actionprintpallet(){
		$data=array();
		$subcodes=Subcode::find('pallet_no=?',request('pallet_no'))->getAll();
		$pallet=Pallet::find('pallet_no=?',request('pallet_no'))->getOne();
		$data['pallet_no']=request('pallet_no');
		$data['quantity']=count($subcodes);
		$data['channel_name']=$pallet->channel_name;
		echo json_encode($data);
		die();
	}
	/**
	 * PE和VG国家invoice
	 */
	function actionPeinvoice(){
		$order=Order::find('tracking_no=?',request('tracking_no'))->getOne();
		$channel=Channel::find('channel_id=?',$order->channel_id)->getOne();
		$warhouse_info=array();
		if($channel->network_code !='UPS' || !$order->account ){
			//杭州仓
			if($order->department_id==6){
				$warhouse_info['name']="Far's warehouse in Hangzhou";
				$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District';
				$warhouse_info['city']='Hangzhou';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='310022';
				$warhouse_info['phone']='0571-87834076';
				//上海仓
			}elseif ($order->department_id==7){
				$warhouse_info['name']="Far's warehouse in Shanghai";
				$warhouse_info['address']='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area';
				$warhouse_info['city']='Shanghai';
				$warhouse_info['state']='Shanghai';
				$warhouse_info['postcode']='201323';
				$warhouse_info['phone']='021-58590952';
				//义乌仓
			}elseif ($order->department_id==8){
				$warhouse_info['name']="Far's warehouse in Yiwu";
				$warhouse_info['address']='No.675-2 Airport Road';
				$warhouse_info['city']='Yiwu';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='322000';
				$warhouse_info['phone']='0579-85119351';
				//广州仓
			}elseif ($order->department_id==22){
				$warhouse_info['name']="Far's warehouse in Guangzhou";
				$warhouse_info['address']='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District';
				$warhouse_info['city']='Guangzhou';
				$warhouse_info['state']='Guangdong';
				$warhouse_info['postcode']='510080';
				$warhouse_info['phone']='020-36301839';
				//青岛仓
			}elseif ($order->department_id==23){
				$warhouse_info['name']="Far's warehouse in Qingdao";
				$warhouse_info['address']='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District';
				$warhouse_info['city']='Qingdao';
				$warhouse_info['state']='Shandong';
				$warhouse_info['postcode']='266108';
				$warhouse_info['phone']='18661786160';
				//深圳仓
			}elseif ($order->department_id==24){
				$warhouse_info['name']="FAR's warehouse in Shenzhen Longhua";
				$warhouse_info['address']='Unit 5, No.8 Non-bonded Warehouse, South China International Logistics Center, No.1 Mingkang Road, Minzhi Street, Longhua New District';
				$warhouse_info['city']='shenzhen';
				$warhouse_info['state']='guangdong';
				$warhouse_info['postcode']='518000';
				$warhouse_info['phone']='4000857988';
				//南京仓
			}elseif ($order->department_id==25){
				$warhouse_info['name']="Far's warehouse in Hangzhou";
				$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District';
				$warhouse_info['city']='Hangzhou';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='310022';
				$warhouse_info['phone']='0571-87834076';
			}else{
				$warhouse_info['name']="";
				$warhouse_info['address']='';
				$warhouse_info['city']='';
				$warhouse_info['state']='';
				$warhouse_info['postcode']='';
				$warhouse_info['phone']='';
			}
		}
		if ($channel->network_code == 'DHL' && in_array($order->service_code, array('Express_Standard_Global','US-FY'))){
			$warhouse_info ['name'] = "MR LI HUI";
			$warhouse_info ['company'] = "FAR";
			$warhouse_info ['address'] = 'GUANG SHENG XIANG XIA SHI WE IF RTO,PLS RTN TO HKG FOR SHPR INST';
			$warhouse_info ['city'] = 'TSING YI';
			$warhouse_info ['state'] = 'HONG KONG';
			$warhouse_info ['postcode'] = '';
			$warhouse_info ['phone'] = '31746198';
			if ($channel->sender_id > 0) {
				$sender = Sender::find ( 'sender_id = ?', $channel->sender_id )->getOne ();
				if(!$sender-> isNewRecord()){
					$warhouse_info ['name'] = $sender->sender_name;
					$warhouse_info ['company'] = $sender->sender_company;
					$warhouse_info ['address'] = $sender->sender_address;
					$warhouse_info ['city'] = $sender->sender_city;
					$warhouse_info ['state'] = $sender->sender_province;
					$warhouse_info ['postcode'] = $sender->sender_zip_code;
					$warhouse_info ['phone'] = $sender->sender_phone;
				}
			}
		}
		$account=UPSAccount::find('account=?',$order->account)->getOne();
		$invoice=array();
		$total_value=0;
		$order_product=Orderproduct::find('order_id=?',$order->order_id)->getOne();
		foreach ($order->product as $v){
			$material_use = $v->material_use;
			if($channel->network_code =='UPS' && strpos(strtolower($v->product_name_en_far), 'mask') !== false){
				if(!$material_use){
					$material_use = 'Dust protection Civil non-woven';
				}
				$brand = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
				$material_use .= ' // brand:'.$brand.' // type no:'.rand(10, 100);
			}
			$invoice['items'][]=array(
				'desc'=>$v->product_name_en_far.' HS Code:'.$v->hs_code_far.' '.$material_use,
				'quantity'=>$v->product_quantity,
				'price'=>$v->declaration_price,
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$total_value+=round($v->product_quantity*$v->declaration_price,2);
		}
		$data=array(
			'sender'=>$channel->network_code !='UPS'?$warhouse_info:$account->toArray(),
			'consignee_name'=>$order->consignee_name1,
			'consignee_company'=>$order->consignee_name2?$order->consignee_name2:$order->consignee_name1,
			'consignee_phone'=>$order->consignee_mobile,
			'consignee_city'=>$order->consignee_city,
			'consignee_country_code'=>$order->consignee_country_code,
			'consignee_country_name'=>Country::find('code_word_two=?',$order->consignee_country_code)->getOne()->english_name,
			'consignee_state'=>$order->consignee_state_region_code,
			'consignee_postal_code'=>$order->consignee_postal_code,
			'consignee_address'=>$order->consignee_street1.' '.$order->consignee_street2,
			'tax'=>$order->tax_payer_id,
			'invoice'=>$invoice,
			'total_value'=>$total_value,
			'tracking_no'=>$order->tracking_no
		);
		echo json_encode($data);
		exit();
	}
	/**
	 * 保存3个出库事件
	 */
	function saveoutevents($order){
		//将订单状态改为已出库
		$order->order_status='6';
		$order->print_time=time();
		//判断三个出库事件
		//设置默认状态
		$confirm_flag=1;
		$events_out=Event::find('order_id=? and event_code in ("PALLETIZE","WAREHOUSE_OUTBOUND","CARRIER_PICKUP")',$order->order_id)->getAll();
		if(!count($events_out)){
			//存入3个事件
			//设置包机业务不发送事件
			if($order->service_code=='CNUSBJ-FY'){
				$confirm_flag=5;
			}
			$department=Department::find('department_id=?',MyApp::currentUser('department_id'))->getOne();
			if($department->department_name=='杭州仓'){
				$location='杭州';
			}elseif ($department->department_name=='义乌仓'){
				$location='义乌';
			}elseif ($department->department_name=='上海仓'){
				$location='上海';
			}elseif ($department->department_name=='广州仓'){
				$location='广州';
			}elseif ($department->department_name=='青岛仓'){
				$location='青岛';
			}elseif ($department->department_name=='深圳仓'){
				$location='深圳';
			}elseif ($department->department_name=='南京仓'){
				$location='南京';
			}elseif ($department->department_name == '连云港仓') {
				$location = '连云港';
			}
			$checkout_time=time();
			// 			if($location=='义乌'){
			// 			    $arr=array('US','CA','MX','PR');
			// 			    $date=date("H:i:s",$checkout_time);
			// 			    if(in_array($order->consignee_country_code, $arr) && '09:00:00'<=$date && $date<='12:00:00' && $order->channel->channelgroup->channel_group_name=='义乌OGP'){
			// 			        $checkout_time=$checkout_time-12*60*60;
			// 		        }
			// 			}
			//中美海运专线取消打托
			if($order->service_code!='OCEAN-FY' || $order->customer->customs_code!='ALPL'){
				$palletize_event= new Event();
				$palletize_event->changeProps(array(
					'order_id'=>$order->order_id,
					'customer_id'=>$order->customer_id,
					'event_code'=>'PALLETIZE',
					'event_time'=>$checkout_time- rand(45, 55)*60+rand(1, 59),
					'event_location'=>$location,
					'timezone'=>'8',
					'confirm_flag'=>$confirm_flag,
					'operator'=>MyApp::currentUser('staff_name')
				));
				$palletize_event->save();
			}
			//出库事件
			$outbound_event= new Event();
			if($order->customer->customs_code=='ALPL'){
				$outbound_event_code = 'SORTING_CENTER_OUTBOUND_CALLBACK';
			}else{
				$outbound_event_code = 'WAREHOUSE_OUTBOUND';
			}
			$outbound_event->changeProps(array(
				'order_id'=>$order->order_id,
				'customer_id'=>$order->customer_id,
				'event_code'=>$outbound_event_code,
				'event_time'=>$checkout_time,
				'event_location'=>$location,
				'timezone'=>'8',
				'confirm_flag'=>$confirm_flag,
				'operator'=>MyApp::currentUser('staff_name')
			));
			$outbound_event->save();
			//承运商取件事件
			$Hour=date("H",$checkout_time);
			$carrier_time='';
			if($Hour<22){
				$carrier_time=$checkout_time+(30+rand(1, 7))*60+4*(rand(1, 7)+2);
			}else{
				$carrier_time=$checkout_time+24*60*60+(30+rand(1, 7))*60+4*(rand(1, 7)+2);
			}
			//EMS-FY:去掉承运商已取件事件，添加：S_TH_OUT轨迹
			if($order->service_code!='EMS-FY' && $order->service_code!='WIG-FY' && $order->service_code!='ePacket-FY' && $order->service_code!='OCEAN-FY'){
				$pickup_event= new Event();
				if($order->customer->customs_code=='ALPL'){
					$pickup_event_code = 'SORTING_CENTER_HO_OUT_CALLBACK';
				}else{
					$pickup_event_code = 'CARRIER_PICKUP';
				}
				$pickup_event->changeProps(array(
					'order_id'=>$order->order_id,
					'customer_id'=>$order->customer_id,
					'event_code'=>$pickup_event_code,
					'event_time'=>$carrier_time,
					'location'=>$location,
					'event_location'=>$location,
					'timezone'=>'8',
					'confirm_flag'=>$confirm_flag
				));
				$pickup_event->save();
				//承运商取件时间
				$order->carrier_pick_time=$carrier_time;
			}elseif ($order->service_code=='EMS-FY' || $order->service_code=='ePacket-FY'){
				$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
				$trace=new Tracking();
				$trace->changeProps(array(
					'order_id'=>$order->order_id,
					'customer_id'=>$order->customer_id,
					'far_no'=>$order->far_no,
					'tracking_code'=>'S_TH_OUT',
					'location'=>$location,
					'trace_desc_en'=>'In Transit to Export Port',
					'trace_desc_cn'=>'货物中转清关口岸',
					'timezone'=>8,
					'confirm_flag'=>'1',
					'quantity'=>$quantity['sum_quantity'],
					'trace_time'=>strtotime(date("Y-m-d",$checkout_time))+19*60*60+rand(0, 30)*60+rand(0, 30)//出库当天的 19：X分Y秒（X，Y在0-30之间随机）
				));
				$trace->save();
			}
			// 			出库时间
			$order->warehouse_out_time=$checkout_time;
		}
		$feeall=Fee::find('order_id = ?',$order->order_id)->getAll();
		foreach ($feeall as $fee){
			$fee->account_date=isset($checkout_time)?$checkout_time:$order->warehouse_out_time;
			$fee->save();
		}
		$order->save();
	}
	/**
	 * @todo   菜鸟入库事件
	 * @author 许杰晔
	 * @since  2020-8-19 10:32:54
	 * @param  object $order
	 * @return
	 * @link   #81740
	 */
	function saveOutCaiNiao($order){
		$order->order_status='6';
		$order->print_time=time();
		$in=CaiNiao::find('order_id= ? and cainiao_code="WAREHOUSE_OUTBOUND"',$order->order_id)->getOne();
		$cainiao_time = time ();
		if($in->isNewRecord()){
			$in->order_id = $order->order_id;
			$in->cainiao_code = 'WAREHOUSE_OUTBOUND';
			$in->cainiao_time = $cainiao_time;
			$in->confirm_flag = '1';
			$in->operator =MyApp::currentUser('staff_name');
			$in->save ();
		}
		$order->warehouse_out_time=$cainiao_time;
		$order->save();
	}
	/**
	 * @todo   文件上传
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return
	 * @link   #81740
	 */
	function actionUpload(){
		if (file_get_contents('php://input')) {
			//创建文件路径
			$filepath = Q::ini ( "upload_tmp_dir" ) . "/".request('name');
			file_put_contents($filepath, file_get_contents('php://input'));
		}
		exit();
	}
	/**
	 * @todo   交货数据导出
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return
	 * @link   #81740
	 */
	function actionComparisonexport(){
		require_once _INDEX_DIR_ .'/_library/phpexcel/PHPExcel/Shared/PCLZip/pclzip.lib.php';
		$channel_group = Order::channelgroup();
		$first_key='';
		foreach ($channel_group as $key=>$value){
			$first_key=$key;
			break;
		}
		$channel_group=$channel_group[request ( 'channel_group',$first_key)];
		//发件日
		$record_order_date=request('record_order_date',date('Y-m-d',time()));
		$order_list=Order::find("order_status = '7'")
		->where ( 'channel_id in (?) and record_order_date>=? and record_order_date<=? and sort=?', $channel_group ,strtotime($record_order_date.' 00:00:00'),strtotime($record_order_date.' 23:59:59'),request('sort','D3'))
		->getAll();
		if(request('export')=='exportlist'){
			set_time_limit(0);
			if(count($order_list)){
				$i=1;
				$tracking_nos=array();
				$no_list=array();
				foreach ($order_list as $v){
					$no_list[]=$v->tracking_no;
					if(floor($i/10)==($i/10)){
						$tracking_nos[]=$no_list;
						$no_list=array();
					}
					$i++;
				}
				if(count($no_list)){
					$tracking_nos[]=$no_list;
				}
				//合并invoice 和 copy
				$dir=Q::ini('upload_tmp_dir');
				foreach ($tracking_nos as $tracking_no){
					$filenames=array();
					foreach ($tracking_no as $v){
						$filenames[]=$dir.DS.$v.'_invoice.pdf';
						$filenames[]=$dir.DS.$v.'_copy_1.pdf';
						if(file_exists($dir.DS.$v.'_copy_2.pdf')){
							$filenames[]=$dir.DS.$v.'_copy_2.pdf';
						}
					}
					Helper_PDF::merge($filenames,$dir.DS.$tracking_no[0].'_all.pdf','file');
					$target=$dir.DS.$tracking_no[0].'_all.pdf';
					//将pdf转为jpg格式
					exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$target} -append {$target}.tiff");
					$zip_file_names[]=$target.'.tiff';
				}
				//压缩文件
				$zip = new PclZip($zip_file_names[0].'.zip');
				$rs = $zip->create(implode(',', $zip_file_names), PCLZIP_OPT_REMOVE_PATH, $dir);
				if ($rs == 0) {
					return $this->_redirectMessage('导出tiff压缩包', "Error : " . $zip->errorInfo(true), url('/Comparisonexport'));
				}
				header('Content-Type:text/html;charset=utf-8');
				header('Content-disposition:attachment;filename='.request ( 'channel_group',$first_key).'_'.request('record_order_date',date('Y-m-d',time())).'_'.request('sort','D3').'.zip');
				$filesize = filesize($zip_file_names[0].'.zip');
				header('Content-length:'.$filesize);
				readfile($zip_file_names[0].'.zip');
			}else{
				return $this->_redirectMessage('导出tiff压缩包', '失败，数据为空', url('/Comparisonexport'));
			}
		}
		$this->_view['orders']=$order_list;
	}
	/**
	 * 生成泛远面单
	 */
	function actionFarbillpdf(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$warhouse_info=array();
		//杭州仓
		if($order->department_id==6){
			$warhouse_info['name']="Far's warehouse in Hangzhou";
			$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022';
			$warhouse_info['contact']='Miss.Zhang';
			$warhouse_info['phone']='0571-87834076';
			//上海仓
		}elseif ($order->department_id==7){
			$warhouse_info['name']="Far's warehouse in Shanghai";
			$warhouse_info['address']='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323';
			$warhouse_info['contact']='Mr.Gu';
			$warhouse_info['phone']='021-58590952';
			//义乌仓
		}elseif ($order->department_id==8){
			$warhouse_info['name']="Far's warehouse in Yiwu";
			$warhouse_info['address']='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000';
			$warhouse_info['contact']='Mr.Yang';
			$warhouse_info['phone']='0579-85119351';
			//广州仓
		}elseif ($order->department_id==22){
			$warhouse_info['name']="Far's warehouse in Guangzhou";
			$warhouse_info['address']='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080';
			$warhouse_info['contact']='Miss.Li';
			$warhouse_info['phone']='020-36301839';
			//青岛仓
		}elseif ($order->department_id==23){
			$warhouse_info['name']="Far's warehouse in Qingdao";
			$warhouse_info['address']='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108';
			$warhouse_info['contact']='Mr Wang';
			$warhouse_info['phone']='18661786160';
		}elseif ($order->department_id==24){
			$warhouse_info['name']="FAR's warehouse in Shenzhen Longhua";
			$warhouse_info['address']='Unit 5, No.8 Non-bonded Warehouse, South China International Logistics Center, No.1 Mingkang Road, Minzhi Street, Longhua New District, Shenzhen City,China 518000';
			$warhouse_info['contact']='Mr Wang';
			$warhouse_info['phone']='4000857988';
			//南京仓
		}elseif($order->department_id==25){
			$warhouse_info['name']="Far's warehouse in Hangzhou";
			$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022';
			$warhouse_info['contact']='Miss.Zhang';
			$warhouse_info['phone']='0571-87834076';
		}else{
			$warhouse_info['name']="";
			$warhouse_info['address']='';
			$warhouse_info['contact']='';
			$warhouse_info['phone']='';
		}
		$invoice=array(); //产品清单
		$total_value=0;
		$i=1;
		foreach ($order->product as $v){
			if($i>=4){
				break;
			}
			$invoice['items'][]=array(
				'row'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
				'quantity'=>$v->product_quantity,
				'price'=>$v->declaration_price,
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$total_value += round($v->product_quantity*$v->declaration_price,2);
			$i++;
		}
		$total_packages=Farpackage::find('order_id=?',request('order_id'))->getSum('quantity');//包裹总数
		$data=array(
			'ali_order_no'=>$order->ali_order_no,
			'far_no'=>$order->far_no,
			'sender'=>$warhouse_info,
			'consignee_company'=>$order->consignee_name2,
			'consignee_name'=>$order->consignee_name1,
			'consignee_address1'=>$order->consignee_street1,
			'consignee_address2'=>$order->consignee_street2,
			'consignee_city'=>$order->consignee_city,
			'consignee_postal_code'=>$order->consignee_postal_code,
			'consignee_state'=>$order->consignee_state_region_code,
			'consignee_phone'=>$order->consignee_mobile,
			'consignee_country_code'=>$order->consignee_country_code,
			'consignee_country_name'=>Country::find('code_word_two=?',$order->consignee_country_code)->getOne()->english_name,
			'invoice'=>$invoice,
			'total_value'=>$total_value,
			'total_packages'=>$total_packages,
			'weight_income_in'=>$order->weight_income_in,
		);
		echo json_encode($data);
		exit();
	}
	/**
	 * EMS数据
	 */
	function actionEms(){
		if(request_is_post()){
			$order=Order::find("service_code = 'EMS-FY' ")
			->joinLeft('tb_order_product',null,'tb_order.order_id=tb_order_product.order_id')
			->joinLeft('tb_country',null,'tb_order.consignee_country_code=tb_country.code_word_two');
			//日期查询
			if(request("start_date")){
				$order->where("tb_order.warehouse_out_time >= ?",strtotime(request("start_date").' 00:00:00'));
			}
			if(request("end_date")){
				$order->where("tb_order.warehouse_out_time <= ?",strtotime(request("end_date").' 23:59:59'));
			}
			//仓库
			if(request('department_id')){
				$order->where('tb_order.department_id = ?',request('department_id'));
			}
			$order->columns('order_id','tb_order');
			$order->columns('customs_country_code','tb_country');
			$order->columns('product_name_far,product_quantity,declaration_price,hs_code','tb_order_product');
			$list=$order->asArray()->order('order_id desc')->getAll();
			$this->_view['order']=$list;
			//导出文件
			if (request('export')=='export') {
				//设置导出表单head
				$sheet = array ( array('电商平台代码','电商平台名称','原始订单编号','物流企业代码','物流企业运单号','订单商品运费',
					'收货人所在国家代码','企业商品货号','企业商品名称','商品数量（成交数量）','计量单位(成交单位)','币制代码','商品单价','HS编码','商品名称',
					'商品规格类型','商品条形码','总重量（单位：千克）','错误信息','仓库'
				));
				$i=1;
				foreach ($list as $value){
					$product_quantity_sum=Orderproduct::find('order_id=?',$value['order_id'])->getSum('product_quantity');
					$check_hs=Hs::find('HSCode=?',$value['hs_code'])->getOne();
					if($check_hs->isNewRecord()){
						$msg='HS编码错误';
					}else{
						$msg='';
					}
					$warehouse='';
					$department=Department::find('department_id=?',$value['department_id'])->getOne();
					if(!$department->isNewRecord()){
						$warehouse=$department->department_name;
					}
					$sheet [] = array (
						'3201962D61',
						'南京欣惠捷电子商务有限公司',
						$value['ali_order_no'],
						'320198Z006',
						$value['tracking_no'],
						'0',
						$value['customs_country_code'],
						"'".date('Ymd').sprintf("%06d",$i++),
						$value['product_name_far'],
						$value['product_quantity'],
						"'".'011',
						'502',
						sprintf('%.2f',$value['declaration_price']),
						"'".$value['hs_code'],
						$value['product_name_far'],
						'无规格型号',
						'无',
						sprintf('%.2f',$value['weight_actual_out']*$value['product_quantity']/$product_quantity_sum),
						$msg,
						$warehouse
					);
				}
				//日期+阿里9610 申报数据（2019.06.25 阿里9610申报数据）
				//Helper_Excel::array2xls(数据表，导出表名)
				Helper_Excel::array2xls ( $sheet,date('Y.m.d',time()).' 阿里9610申报数据'.'.xlsx' );
				exit();
			}
		}
	}
	/**
	 * 中美专线数据
	 */
	function actionus(){
		if(request_is_post()){
			$order=Order::find();
			//日期查询
			if(request('timetype')=='1'){
				if(request("start_date",date('Y-m-d').' 00:00')){
					$order->where("tb_order.print_time >= ?",strtotime(request("start_date").':00'));
				}
				if(request("end_date")){
					$order->where("tb_order.print_time <= ?",strtotime(request("end_date").':59'));
				}
			}elseif (request('timetype')=='2'){
				if(request("start_date",date('Y-m-d').' 00:00')){
					$order->where("tb_order.warehouse_out_time >= ?",strtotime(request("start_date").':00'));
				}
				if(request("end_date")){
					$order->where("tb_order.warehouse_out_time <= ?",strtotime(request("end_date").':59'));
				}
			}
			//SKU数量查询
			$orderproduct=Orderproduct::find();
			if(request('start_product_quantity',15)){
				$orderproduct->where("product_quantity >= ?",request('start_product_quantity',15));
			}
			if(request('end_product_quantity')){
				$orderproduct->where("product_quantity <= ?",request('end_product_quantity'));
			}
			$id=$orderproduct->asArray()->setColumns('order_id')->getAll();
			$id=Helper_Array::getCols($id, 'order_id');
			if(count($id)>0){
				$order->where("order_id in (?)",$id);
			}
			//仓库
			if(request('department_id')){
				$order->where('tb_order.department_id = ?',request('department_id'));
			}
			//产品
			if(request('service_code')){
				$order->where('tb_order.service_code = ?',request('service_code'));
			}
			//阿里单号
			if(request('ali_order_no')){
				$ali_order_no=explode("\r\n", request('ali_order_no'));
				$ali_order_no=array_filter($ali_order_no);
				$ali_order_no=array_unique($ali_order_no);
				$order->where('tb_order.ali_order_no in (?)',$ali_order_no);
			}
			//末端运单号
			if(request('tracking_no')){
				$tracking_no=explode("\r\n", request('tracking_no'));
				$tracking_no=array_filter($tracking_no);
				$tracking_no=array_unique($tracking_no);
				$order->where('tb_order.tracking_no in (?)',$tracking_no);
			}
			$ordercl=clone $order;
			$list=$order->asArray()->order('order_id desc')->getAll();
			$this->_view['order']=$list;
			//导出文件
			if (request('export')=='export') {
				set_time_limit(0);//不限制超时时间
				ini_set('memory_limit', '-1');//不限制内存
				//设置导出表单head
				$sheet = array ( array('平台名称','订单号','收件人姓名','收件人州二字码','收件人城市','收件人邮编',
					'收件人电话','收件人地址','收件人地址2','发件人姓名','发件人电话','发件人邮编','发件人省','发件人市','发件人地址','箱号',
					'产品','包裹总重量','带电池','备注','SellerID','Prop:Size','Sort Code','SKU1','单价1','数量1','英文品名1','中文品名1','单件毛重1','套装数量1',
					'SKU2','单价2','数量2','英文品名2','中文品名2','单件毛重2','套装数量2','SKU3','单价3','数量3','英文品名3',
					'中文品名3','单件毛重3','套装数量3','SKU4','单价4','数量4','英文品名4','中文品名4','单件毛重4','套装数量4',
					'SKU5','单价5','数量5','英文品名5','中文品名5','单件毛重5','套装数量5','SKU6','单价6','数量6','英文品名6',
					'中文品名6','单件毛重6','套装数量6','SKU7','单价7','数量7','英文品名7','中文品名7','单件毛重7','套装数量7'
				));
				$lists=$ordercl->asArray()->order('order_id desc')->getAll();
				foreach ($lists as $value){
					$msg='';
					if(strlen($value['consignee_street1'].' '.$value['consignee_street2'])>225){
						$msg.='收件人地址超长';
					}
					if(strlen($value['consignee_postal_code'])<>'5' && strlen($value['consignee_postal_code'])<>'9'){
						$msg.=empty($msg)?'收件人邮编错误':',收件人邮编错误';
					}
					//                     if(!strpos(trim($value['consignee_name1']), ' ')){
					//                         $msg.=empty($msg)?'收件人姓名格式不正确':',收件人姓名格式不正确';
					//                     }
					$state='';
					$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$value['consignee_state_region_code'])),strtoupper(str_replace(' ','',$value['consignee_state_region_code'])))->getOne();
					if($states->isNewRecord()){
						$msg.=empty($msg)?'收件人州错误':',收件人州错误';
					}else{
						$state=$states->province_code_two;
					}
					$product_quantity=Orderproduct::find('order_id=?',$value['order_id'])->getCount();
					if($product_quantity>7){
						$msg.=empty($msg)?'SKU超7种':',SKU超7种';
					}
					$product=Orderproduct::find('order_id=?',$value['order_id'])->getAll();
					$prod=array();
					$product_quantity_sum=0;
					$i=1;
					foreach ($product as $p){
						$prod[]=$p;
						$product_quantity_sum += $p->product_quantity;
						if($i>6){
							break;
						}
						$i++;
					}
					$package=Faroutpackage::find('order_id=?',$value['order_id'])->getOne();
					$size=$package->length_out.'*'.$package->width_out.'*'.$package->height_out;
					
					/* if ($value['service_code']=='CNUS-FY'){
					 $product_name = 'GREENCLEARANCE';
					 }else{
					 $product_name = 'GREENCLEARANCEOCEAN';
					 } */
					$ib_procut=CodeProductRelationShip::find('ali_product=?',$value['service_code'])->getOne();
					$channel=Channel::find('channel_id=?',$value['channel_id'])->getOne();
					$row= array (
						'FAR CO',
						$value['tracking_no'],
						strpos(trim($value['consignee_name1']), ' ')?$value['consignee_name1']:$value['consignee_name1'].' '.$value['consignee_name1'],
						$state,
						$value['consignee_city'],
						
						"'".$value['consignee_postal_code'],
						"'".$value['consignee_mobile'].($value['consignee_telephone']?'/'.$value['consignee_telephone']:''),
						substr($value['consignee_street1'].' '.$value['consignee_street2'], 0,225),
						'',
						'JENNY',
						
						'17794536178',
						'310022',
						'ZHEJIANG PROVINCE',
						'HANGZHOU CITY',
						'NO. 22 GREAT WALL STREET',
						
						$value['ali_order_no'],
						$ib_procut->ib_product,
						"'".$value['weight_label'],
						'否',
						$msg,
						'ALXM',
						$size,
						$channel->sort_code?$channel->sort_code:''
					);
					foreach ($prod as $pr){
						$row[]="'".date('Ymd').rand(100000, 999999);
						$row[]="'".$pr['declaration_price'];
						$row[]="'".$pr['product_quantity'];
						$row[]="'".$pr['product_name_en_far'];
						$row[]="'".$pr['product_name_far'];
						$row[]="'".sprintf('%.3f',$value['weight_label']/$product_quantity_sum);
						$row[]='';
					}
					$sheet []=$row;
				}
				Helper_Excel::array2xls ( $sheet,date('Y.m.d',time()).'-FSP-AL-US送货预报数据'.'.xlsx' );
				exit();
			}
		}
	}
	/**
	 *  上海快件报关
	 */
	function actionsh(){
		if(request_is_post()){
			$order=Order::find();
			//日期查询
			if(request("start_date")){
				$order->where("tb_order.warehouse_out_time >= ?",strtotime(request("start_date").':00'));
			}
			if(request("end_date")){
				$order->where("tb_order.warehouse_out_time <= ?",strtotime(request("end_date").':59'));
			}
			//仓库
			if(request('department_id')){
				$order->where('tb_order.department_id = ?',request('department_id'));
			}
			//产品
			if(request('service_code')){
				$order->where('tb_order.service_code = ?',request('service_code'));
			}
			//阿里单号
			if(request('ali_order_no')){
				$ali_order_no=explode("\r\n", request('ali_order_no'));
				$ali_order_no=array_filter($ali_order_no);
				$ali_order_no=array_unique($ali_order_no);
				$order->where('tb_order.ali_order_no in (?)',$ali_order_no);
			}
			//末端运单号
			if(request('tracking_no')){
				$tracking_no=explode("\r\n", request('tracking_no'));
				$tracking_no=array_filter($tracking_no);
				$tracking_no=array_unique($tracking_no);
				$order->where('tb_order.tracking_no in (?)',$tracking_no);
			}
			$list=$order->asArray()->order('order_id desc')->getAll();
			$this->_view['order']=$list;
			//导出文件
			if (request('export')=='export') {
				set_time_limit(0);//不限制超时时间
				ini_set('memory_limit', '-1');//不限制内存
				//设置导出表单head
				$quantity_out=0;
				$weight_out=0;
				foreach ($list as $l){
					$faroutpackage=Faroutpackage::find('order_id = ?',$l['order_id'])->getAll();
					foreach ($faroutpackage as $far){
						$quantity_out +=$far->quantity_out;
						$weight_out += ($far->quantity_out * $far->weight_out);
					}
				}
				$objExcel = new PHPExcel();
				$sheet = $objExcel->getActiveSheet();
				$sheet->setCellValue('A1', 'MV1809');
				$sheet->mergeCells('A2:P2');
				$sheet->getStyle('A2:P2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('A2', '进出口报关数据');
				$sheet->getStyle('A2:P2')->getFont()->setSize(20);
				$sheet->setCellValue('A3', '总单号');
				$sheet->setCellValue('C3', '航班');
				$sheet->setCellValue('E3','航班日期');
				$sheet->setCellValue('G3','总单件数');
				$sheet->setCellValue('H3',$quantity_out);
				$sheet->setCellValue('I3','总单重量');
				$sheet->setCellValue('J3',$weight_out);
				$sheet->setCellValue('K3','进出口标志');
				$sheet->setCellValue('L3','E');
				$sheet->setCellValue('M3','经停城市');
				$sheet->setCellValue('A4','序号');
				$sheet->setCellValue('B4','运单号码');
				$sheet->setCellValue('C4','件数');
				$sheet->setCellValue('D4','重量(KG)');
				$sheet->mergeCells('E4:M4');
				$sheet->getStyle('E4:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('E4', '商品信息');
				$sheet->setCellValue('N4','通关方式');
				$sheet->setCellValue('O4','经营单位代码');
				$sheet->setCellValue('P4','经营单位名称');
				$sheet->setCellValue('Q4','经营单位统一社会信用代码');
				$sheet->setCellValue('R4','尺寸(CM)');
				$sheet->setCellValue('S4','装袋号');
				$sheet->mergeCells('T4:AA4');
				$sheet->getStyle('T4:AA4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('T4', '收货人资料');
				$sheet->mergeCells('AB4:AI4');
				$sheet->getStyle('AB4:AI4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('AB4', '发货人资料');
				$sheet->mergeCells('AJ4:AQ4');
				$sheet->getStyle('AJ4:AQ4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('AJ4', '英文收发货人资料（进口为发件人，出口为收件人）');
				$sheet->setCellValue('E5','中文品名');
				$sheet->setCellValue('F5','英文品名');
				$sheet->setCellValue('G5','HS税号');
				$sheet->setCellValue('H5','商品数量');
				$sheet->setCellValue('I5','计量单位');
				$sheet->setCellValue('J5','申报要素');
				$sheet->setCellValue('K5','商品重量(KG)');
				$sheet->setCellValue('L5','总价值');
				$sheet->setCellValue('M5','币种');
				$sheet->setCellValue('T5','收件人');
				$sheet->setCellValue('U5','电话');
				$sheet->setCellValue('V5','社会信用代码');
				$sheet->setCellValue('W5','公司');
				$sheet->setCellValue('X5','地址');
				$sheet->setCellValue('Y5','城市');
				$sheet->setCellValue('Z5','国家');
				$sheet->setCellValue('AA5','邮编');
				$sheet->setCellValue('AB5','发件人');
				$sheet->setCellValue('AC5','电话');
				$sheet->setCellValue('AD5','社会信用代码');
				$sheet->setCellValue('AE5','公司');
				$sheet->setCellValue('AF5','地址');
				$sheet->setCellValue('AG5','城市');
				$sheet->setCellValue('AH5','国家');
				$sheet->setCellValue('AI5','邮编');
				$sheet->setCellValue('AJ5','收发货人');
				$sheet->setCellValue('AK5','电话');
				$sheet->setCellValue('AL5','社会信用代码');
				$sheet->setCellValue('AM5','公司');
				$sheet->setCellValue('AN5','地址');
				$sheet->setCellValue('AO5','城市');
				$sheet->setCellValue('AP5','国家');
				$sheet->setCellValue('AQ5','邮编');
				$i=1;
				$j=6;
				foreach ($list as $li){
					$quantity=0;$weight=0;
					$package=Faroutpackage::find('order_id = ?',$li['order_id'])->getAll();
					foreach ($package as $pa){
						$quantity +=$pa->quantity_out;
						$weight += ($pa->quantity_out * $pa->weight_out);
					}
					$orderproduct=Orderproduct::find('order_id = ?',$li['order_id'])->order('product_quantity')->getAll();
					$price=0;
					foreach ($orderproduct as $product){
						$price += ($product->product_quantity * $product->declaration_price);
					}
					$quantity=Faroutpackage::find('order_id = ?',$li['order_id'])->getSum('quantity_out');
					$sheet->setCellValue("A$j",$i);
					$sheet->setCellValue("B$j",$li['tracking_no']);
					$sheet->setCellValue("C$j",$quantity);
					$sheet->setCellValue("D$j",sprintf('%.3f',$li['weight_label']));
					$sheet->setCellValue("E$j",$product->product_name_far);
					$sheet->setCellValue("F$j",$product->product_name_en_far);
					$sheet->setCellValue("G$j","'".$product->hs_code_far);
					$sheet->setCellValue("H$j",$product->product_quantity);
					$sheet->setCellValue("K$j",sprintf('%.3f',$li['weight_label']));
					$sheet->setCellValue("L$j",sprintf('%.2f',$price));
					$sheet->setCellValue("M$j",'USD');
					$sheet->setCellValue("N$j",'C');
					$sheet->setCellValue("O$j",'3122460CG1');
					$sheet->setCellValue("P$j",'上海网兴国际贸易有限公司');
					$sheet->setCellValue("Q$j",'91310000743796249L');
					$sheet->setCellValue("R$j",'null');
					$subcode=Subcode::find('sub_code = ?',$li['tracking_no'])->getOne();
					$sheet->setCellValue("S$j",!$subcode->isNewRecord()?$subcode->pallet_no:'');
					$sheet->setCellValue("T$j",$li['consignee_name1']);
					$consignee_mobile=str_replace(array('*','.','-','"','+'), '', $li['consignee_mobile']);
					$sheet->setCellValue("U$j","'".$consignee_mobile);
					$sheet->setCellValue("V$j",'null');
					$sheet->setCellValue("W$j",trim($li['consignee_name2'])?$li['consignee_name2']:$li['consignee_name1'].' CO., LTD.');
					$sheet->setCellValue("X$j",$li['consignee_street1'].' '.$li['consignee_street2']);
					$sheet->setCellValue("Y$j",$li['consignee_city']);
					$sheet->setCellValue("Z$j",$li['consignee_country_code']);
					$sheet->setCellValue("AA$j","'".$li['consignee_postal_code']);
					$sender_name1=Helper_Chinese::toPinYin($li['sender_name1']);
					$sender_name1=strtoupper(substr($sender_name1, 0,1)).substr($sender_name1, 1);
					$sheet->setCellValue("AB$j",$sender_name1);
					$sender_mobile=str_replace(array('*','.','-','"','+'), '', $li['sender_mobile']);
					$sheet->setCellValue("AC$j","'".$sender_mobile);
					$sheet->setCellValue("AD$j",'NULL');
					$sheet->setCellValue("AE$j",$li['sender_name2']);
					$sheet->setCellValue("AF$j",$li['sender_street1'].' '.$li['sender_street2']);
					$sheet->setCellValue("AG$j",$li['sender_city']);
					$sheet->setCellValue("AH$j",$li['sender_country_code']);
					$sheet->setCellValue("AI$j","'".$li['sender_postal_code']);
					$sheet->setCellValue("AJ$j",$li['consignee_name1']);
					$sheet->setCellValue("AK$j","'".$consignee_mobile);
					$sheet->setCellValue("AL$j",'NULL');
					$sheet->setCellValue("AM$j",trim($li['consignee_name2'])?$li['consignee_name2']:$li['consignee_name1'].' CO., LTD.');
					$sheet->setCellValue("AN$j",$li['consignee_street1'].' '.$li['consignee_street2']);
					$sheet->setCellValue("AO$j",$li['consignee_city']);
					$sheet->setCellValue("AP$j",$li['consignee_country_code']);
					$sheet->setCellValue("AQ$j","'".$li['consignee_postal_code']);
					$i++;$j++;
				}
				header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel2007' );
				header('Content-Disposition: attachment;filename=上海快件出口预报数据.xlsx');
				header('Cache-Control: max-age=0');
				try {
					@$objWriter->save('php://output');
				} catch (PHPExcel_Writer_Exception $ex) {
					$tmpF = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time() . '.xlsx';
					@$objWriter->save($tmpF);
					readfile($tmpF);
					unlink($tmpF);
				}
				exit();
			}
		}
	}
	
	//如果是青岛的普货，出库之后部门修改为上海
	function changqdtosh($order){
		if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $order->department_id=='23' && MyApp::currentUser('department_id') <> '23'){
			$order->department_id=MyApp::currentUser('department_id');
		}
	}
	//获取件数
	function actionCodeprint(){
		if(request("ali_order_no")){
			$data=array();
			$order=Order::find("ali_order_no=?",request("ali_order_no"))->getOne();
			$package_number = Orderpackage::find("order_id = ?",$order->order_id)->getSum('quantity');
			if($order->isNewRecord()){
				$data['message']='nooder';
			}else{
				if($package_number>0){
					$data['message']='success';
					$data['quantity']=$package_number;
				}else{
					$data['message']='nopackage';
				}
			}
			echo json_encode($data);
			exit();
		}
	}
	//条码打印
	function actionQuantityprint(){
		if(request("ali_order_no") && request("quantity")){
			$data=array();
			if(request("quantity")<=0){
				$data['message']='errorpackage';
			}else{
				$order = Order::find('ali_order_no = ?',request("ali_order_no"))->getOne();
				if($order->isNewRecord()){
					$data['message']='nooder';
				}
				$package_number=request("quantity");
				$dir=Q::ini('upload_tmp_dir');
				for ($i=1;$i<=$package_number;$i++){
					$ali_order_no=request("ali_order_no");
					if($i>1){
						$ali_order_no=$ali_order_no.'-'.$i;
					}
					$jsonFile=$dir.DS.$ali_order_no.'.json';
					file_put_contents($jsonFile, json_encode(array(
						'ali_order_no'=>$ali_order_no,
					)));
				}
				$data['message']='success';
			}
		}else{
			$data['message']='missingmsg';
		}
		echo json_encode($data);
		exit();
	}
	//总单列表
	function actionTotallist(){
		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
		$staffrole=StaffRole::find('staff_id = ? and role_id in (?)',MyApp::currentUser('staff_id'),array("1","7"))->getOne();
		if($staffrole->isNewRecord()){
			$total_list = Totallist::find('department_id = ?',MyApp::currentUser('department_id'));
		}else {
			$total_list = Totallist::find();
		}
		if(request('timetype')=='1'){
			//创建时间
			if(request('start_date',date('Y-m-d'))){
				$total_list->where('record_order_date >= ?',strtotime(request('start_date',date('Y-m-d')).'00:00:00'));
			}
			if(request('end_date')){
				$total_list->where('record_order_date <= ?',strtotime(request('end_date').'23:59:59'));
			}
		}else{
			//创建时间
			if(request('start_date',date('Y-m-d'))){
				$total_list->where('create_time >= ?',strtotime(request('start_date',date('Y-m-d')).'00:00:00'));
			}
			if(request('end_date')){
				$total_list->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
			}
		}
		//仓库
		if(request('department_id')){
			$total_list->where('department_id = ?',request('department_id'));
		}
		//总单单号
		if(request('total_list_no')){
			$total_list_no=explode("\r\n", request('total_list_no'));
			$total_list_no=array_filter($total_list_no);//去空
			$total_list_no=array_unique($total_list_no);//去重
			$total_list->where('total_list_no in (?)',$total_list_no);
		}
		$cp_total_list = clone $total_list;
		$cp_total_list_no = $cp_total_list->setColumns('total_list_no')->asArray()->getAll();
		$cp_nos = Helper_Array::getCols($cp_total_list_no, 'total_list_no');
		$order_count=0;$weight_cost_out=0;$far_out_package=0;
		if(count($cp_total_list_no)>0){
			$order = Order::find('total_list_no in (?)',$cp_nos)->setColumns('order_id');
			$orders = $order->asArray()->getAll();
			$order_id = Helper_Array::getCols($orders, 'order_id');
			$order_count = count($order_id);
			$weight_cost_out = $order->getSum('weight_cost_out');
			if($order_count>0){
				$far_out_package = Faroutpackage::find('order_id in (?)',$order_id)->getSum('quantity_out');
			}
		}
		if(request('exp')=='exp'){
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '-1');
			set_time_limit(0);
			$list=clone $total_list;
			$lists=$list->getAll();
			$header = array (
				'发件人',
				'仓库',
				'总单单号',
				'渠道分组',
				'国家',
				'总票数',
				'总件数',
				'总计费重',
				'操作人',
				'操作日期'
			);
			$sheet = array (
				$header
			);
			foreach ($lists as $value){
				$order = Order::find('total_list_no = ?',$value->total_list_no)->setColumns('order_id');
				$orders = $order->asArray()->getAll();
				$order_id = Helper_Array::getCols($orders, 'order_id');
				$order_count = count($order_id);
				$weight_cost_out = $order->getSum('weight_cost_out');
				$far_out_package = 0;
				if($order_count>0){
					$far_out_package = Faroutpackage::find('order_id in (?)',$order_id)->getSum('quantity_out');
				}
				$row =array(
					Helper_Util::strDate('Y-m-d H:i:s', $value->record_order_date),
					$value->department_id?$dpms[$value->department_id]:'',
					$value->total_list_no,
					$value->channel_group->channel_group_name,
					$value->country_code,
					$order_count,
					$far_out_package,
					$weight_cost_out,
					$value->operation_name,
					Helper_Util::strDate('Y-m-d H:i:s', $value->operation_time)
				);
				$sheet [] = $row;
			}
			Helper_ExcelX::array2xlsx ( $sheet, '交货核查导出列表' );
			exit ();
		}
		$pagination = null;
		$total_list=$total_list->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['total_list']=$total_list;
		$this->_view['pagination']=$pagination;
		$this->_view['dpms']= $dpms;
		$this->_view['order_count']=$order_count;
		$this->_view['weight_cost_out']=$weight_cost_out;
		$this->_view['far_out_package']=$far_out_package;
	}
	//完成操作
	function actionFinished(){
		$total_list = Totallist::find('total_list_no = ?',request('total_list_no'))->getOne();
		if(!$total_list->isNewRecord()){
			$total_list->status = '1';
			$total_list->save();
			return $this->_redirect(url('warehouse/totallist'));
		}else{
			return $this->_redirectMessage('数据缺失', '', url('warehouse/totallist'));
		}
	}
	//删除
	function actionDeltotal(){
		$totallist = Totallist::find('total_list_no = ?',request('total_list_no'))->getOne();
		if($totallist->isNewRecord()){
			return false;
		}else{
			$totallist->destroy();
			return 'delsuccess';
		}
	}
	function actionTotaldetail(){
		if (substr(request('total_list_no'),0,2)=='FR'){
			$total = Totallist::find('total_list_no = ?',request('total_list_no'))->getOne();
			if ($total->isNewRecord() ){
				return $this->_redirect('错误','未找到相关单号',url('warehouse/totallist'));
			}
			$orders = Order::find('total_list_no = ?',request('total_list_no'));
			//订单号、末端单号
			if(request('order_no')){
				$orders->where('ali_order_no = ? or tracking_no = ?',request('order_no'),request('order_no'));
			}
			//时间
			if(request('start_date')){
				$orders->where('warehouse_out_time >= ?',strtotime(request('start_date').':00'));
			}
			if(request('end_date')){
				$orders->where('warehouse_out_time <= ?',strtotime(request('end_date').':00'));
			}
			//产品
			if(request('service_code')){
				$orders->where('service_code = ?',request('service_code'));
			}
			//国建
			if(request('consignee_country_code')){
				$orders->where('consignee_country_code = ?',request('consignee_country_code'));
			}
			//网络
			if(request('network_code')){
				$orders->joinLeft('tb_channel', '' ,'tb_channel.channel_id=tb_order.channel_id')->where('tb_channel.network_code=?',request('network_code'));
			}
			//渠道
			if(request('channel_id')){
				$orders->where('channel_id=?',request('channel_id'));
			}
			//导出
			if(request('export')=='exportlist'){
				ini_set('max_execution_time', '0');
				ini_set('memory_limit', '2G');
				set_time_limit(0);
				$list=clone $orders;
				$lists=$list->getAll();
				$i = 0;
				$weight_cost = 0;
				$total_quantity = 0;
				$total_actual_weight = 0;
				$total_cost_weight = 0;
				$header = array (
					'No',
					'总单单号',
					'订单号',
					'末端单号',
					'件数',
					'实重',
					'长*宽*高',
					'计费重',
					'销售产品',
					'渠道',
					'国家',
					'交货日期',
					'状态',
					'订单轨迹',
					'P/S',
					'包裹类型'
				);
				$sheet = array (
					$header
				);
				$status=array('1'=>'未入库','2'=>'已取消','3'=>'已退货','4'=>'已支付','5'=>'已入库','6'=>'已打印','7'=>'已出库','8'=>'已提取','9'=>'已签收','10'=>'已查验','11'=>'待退货','12'=>'已扣件','13'=>'已结束','14'=>'已分派','15'=>'已取件','16'=>'已网点已入库');
				foreach ($lists as $value){
					$route=Route::find('tracking_no=?',$value->tracking_no)->order('time desc')->getOne();
					$package_quantity = Faroutpackage::find('order_id = ?',$value->order_id)->getSum('quantity_out');
					$sub_code = Subcode::find('order_id = ?',$value->order_id)->setColumns('sub_code')
					->getAll()->getCols('sub_code');
					//             	    dump($sub_code);exit();
					$j=0;
					foreach ($value->faroutpackages as $v){
						$weight_v=floor($v->length_out)*floor($v->width_out)*floor($v->height_out)/5000;
						if($v->weight_out>$weight_v){
							$weight_cost = $v->weight_out;
						}else{
							$weight_cost = $weight_v;
						}
						$actual_ceil_weight = ceil($v->weight_out/0.5)*0.5;
						//实重取预报重 #81476
						if ((floor ( $v->weight_out / 0.5 ) * 0.5 + 0.1) >=  $v->weight_out) {
							$label_weight = floor ( $v->weight_out / 0.5 ) * 0.5;
						} else {
							$label_weight = ceil ( $v->weight_out / 0.5 ) * 0.5;
						}
						for($a=0;$a<$v->quantity_out;$a++){
							$row =array(
								"'".++$i,
								$value->total_list_no,
								$value->ali_order_no,
								isset($sub_code[$j])?"'".$sub_code[$j]:'',
								$package_quantity,
								$label_weight,
								floor($v->length_out).'*'.floor($v->width_out).'*'.floor($v->height_out),
								round($weight_cost,4),
								$value->service_product->product_chinese_name,
								$value->channel->channel_name,
								$value->consignee_country_code,
								Helper_Util::strDate('Y-m-d', $value->record_order_date),
								$status[$value->order_status],
								$route->description,
								$weight_cost-$actual_ceil_weight>0?'P':'S',
								$value->packing_type
							);
							$j++;
							$sheet[] = $row;
							$total_actual_weight += $v->weight_out;
							$total_cost_weight += $weight_cost;
						}
					}
					$total_quantity += $package_quantity;
				}
				$sheet [] = array('','','','总计',$total_quantity,$total_actual_weight,'',round($total_cost_weight,4));
				Helper_ExcelX::array2xlsx ( $sheet, request('total_list_no').'导出列表' );
				exit ();
			}
			$pagination = null;
			$orders=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
			->fetchPagination ( $pagination )->getAll();
			$this->_view['total']=$total;
			$this->_view['orders']=$orders;
			$this->_view['pagination']=$pagination;
		}else{
			exit('error total_list_no');
		}
	}
	
	/**
	 * 华磊订单导入
	 */
	function actionhlimport(){
		set_time_limit(0);
		ini_set('memory_limit', '-1');//不限制内存
		if(request_is_post ()){
			$errors = array ();
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('warehouse/hlimport'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('warehouse/hlimport'));
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
			foreach ($sheet as $key => $row){
				if(!strlen($row ['原单号'])){
					continue;
				}
				$order = Order::find('ali_order_no = ?',$row ['原单号'])->getOne();
				if(!$order->isNewRecord()){
					$arr[$key]['原单号']=$row ['原单号'];
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='订单已存在';
					continue;
				}
				if(!strlen($row ['国家名称'])){
					$arr[$key]['原单号']=$row ['原单号'];
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='国家名称必填';
					continue;
				}
				$country=Country::find('chinese_name = ?',$row ['国家名称'])->getOne();
				if(!strlen($row ['客户代码'])){
					$arr[$key]['原单号']=$row ['原单号'];
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='客户代码必填';
					continue;
				}
				$customer=Customer::find('customs_code = ?',$row ['客户代码'])->getOne();
				if($customer->isNewRecord()){
					$arr[$key]['原单号']=$row ['原单号'];
					$arr[$key]['结果']='失败';
					$arr[$key]['信息']='客户代码不存在';
					continue;
				}
				// 	            if(!isset($row['应收金额']) || ($row['应收金额']==0 || $row['应收金额']=='')){
				// 	                $arr[$key]['原单号']=$row ['原单号'];
				// 	                $arr[$key]['结果']='失败';
				// 	                $arr[$key]['信息']='应收金额不能为0';
				// 	                continue;
				// 	            }
				// 	            if(!isset($row['应付金额']) || ($row['应付金额']==0 || $row['应付金额']=='')){
				// 	                $arr[$key]['原单号']=$row ['原单号'];
				// 	                $arr[$key]['结果']='失败';
				// 	                $arr[$key]['信息']='应付金额不能为0';
				// 	                continue;
				// 	            }
				//将数据存入数据库
				$order->ali_order_no=$row['原单号'];
				$order->tracking_no=$row['转单号'];
				$order->service_code=request('service_code');
				$order->consignee_country_code=$country->code_word_two;
				$order->consignee_postal_code=$row['邮编'];
				$order->weight_income_in=@$row['收计费重'];
				$order->weight_actual_in=@$row['收货实重'];
				$order->weight_cost_out=@$row['出计费重'];
				$order->weight_actual_out=@$row['出货实重'];
				$order->department_id='20';
				$order->channel_id=request('channel_id');
				$order->customer_id=$customer->customer_id;
				$order->warehouse_out_time=time();
				$order->order_status='9';
				$order->save();
				Fee::meta()->destroyWhere('order_id =?',$order->order_id);
				if(isset($row ['应收金额'])){
					$fee1 = new Fee();
					$fee1->order_id = $order->order_id;
					$fee1->fee_type = 1;
					$fee1->fee_item_code = 'logisticsExpressASP_EX0001';
					$fee1->fee_item_name = '基础运费';
					$fee1->quantity = 1;
					$fee1->amount = $row ['应收金额'];
					$fee1->account_date = time();
					$fee1->btype_id = $customer->customer_id;
					$fee1->save();
				}
				if(isset($row ['应付金额'])){
					$fee2 = new Fee();
					$fee2->order_id = $order->order_id;
					$fee2->fee_type = 2;
					$fee2->fee_item_code = 'logisticsExpressASP_EX0001';
					$fee2->fee_item_name = '基础运费';
					$fee2->quantity = 1;
					$fee2->amount = $row ['应付金额'];
					$fee2->account_date = time();
					$fee2->btype_id = $order->channel->supplier_id;
					$fee2->save();
				}
				$arr[$key]['原单号']=$row ['原单号'];
				$arr[$key]['结果']='成功';
				$arr[$key]['信息']='';
			}
			$this->_view['sheet']=$arr;
		}
	}
	/**
	 * 包裹启程扫描
	 */
	function actionTotaloutlist(){
		$staffrole=StaffRole::find('staff_id = ? and role_id in (?)',MyApp::currentUser('staff_id'),array("1","7"))->getOne();
		if($staffrole->isNewRecord()){
			$total_out_list = Totaloutlist::find('out_department_id = ?',MyApp::currentUser('department_id'));
		}else{
			$total_out_list = Totaloutlist::find();
		}
		
		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
		//创建时间
		if(request('start_date',date('Y-m-d'))){
			$total_out_list->where('create_time >= ?',strtotime(request('start_date',date('Y-m-d')).'00:00:00'));
		}
		if(request('end_date')){
			$total_out_list->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//抵达仓
		if(request('in_department_id')){
			$total_out_list->where('in_department_id = ?',request('in_department_id'));
		}
		//启程仓
		if(request('out_department_id')){
			$total_out_list->where('out_department_id = ?',request('out_department_id'));
		}
		//总单单号
		if(request('total_no')){
			$total_no=explode("\r\n", request('total_no'));
			$total_no=array_filter($total_no);//去空
			$total_no=array_unique($total_no);//去重
			$total_out_list->where('total_no in (?)',$total_no);
		}
		if(request('export')=='export'){
			$list=clone $total_out_list;
			//设置导出表单head
			$sheet = array ( array('总单号','启程仓','抵达仓','产品','总票数','总件数','总实重','总计费重',
				'转运方式','转运单号','收件人','电话','地址','操作人','操作日期'));
			$details=$list->getAll ();
			foreach ($details  as $v ) {
				$order_count=0;$weight_cost=0;$weight_actual=0;$quantity=0;$quantity_sum=0;
				foreach ($v->totalorderout as $value){
					$order_count++;
					if($value->order->order_id){
						$faoutpackage = Faroutpackage::find('order_id = ?',$value->order->order_id)->asArray()->getAll();
						if(count($faoutpackage)>0){
							$weight_cost+=$value->order->weight_cost_out;
							$weight_actual+=$value->order->weight_actual_out;
							$quantity = Faroutpackage::find('order_id = ?',$value->order->order_id)->getSum('quantity_out');
						}else{
							$weight_cost+=$value->order->weight_income_in;
							$weight_actual+=$value->order->weight_actual_in;
							$quantity = Farpackage::find('order_id = ?',$value->order->order_id)->getSum('quantity');
						}
						$quantity_sum+=$quantity;
					}
				}
				$service_cn = Helper_Array::toHashmap(Product::find()->asArray()->getAll(),'product_name','product_chinese_name');
				$service_name = array();
				foreach (explode(',', $v->service_code) as $value){
					$service_name[] = @$service_cn[$value];
				}
				$service_name_cn = implode(',', $service_name);
				$sheet [] = array (
					$v->total_no,
					$dpms[$v->out_department_id],
					$dpms[$v->in_department_id],
					$service_name_cn,
					$order_count,
					$quantity_sum,
					$weight_actual,
					$weight_cost,
					$v->express_company,
					$v->express_no,
					$v->consignee_name,
					$v->consignee_phone,
					$v->consignee_address,
					$v->operation_name,
					Helper_Util::strDate('Y-m-d H:i:s', $v->operation_time),
				);
			}
			Helper_Excel::array2xls ( $sheet,'包裹启程扫描导出'.date('Ymd',time()).'.xlsx' );
			exit();
		}
		$pagination = null;
		$total_out_list=$total_out_list->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['total_out_list']=$total_out_list;
		$this->_view['pagination']=$pagination;
		$this->_view['dpms']= $dpms;
	}
	/**
	 * @todo   删除
	 * @author 吴开龙
	 * @since  2020-8-17 10:16:17
	 * @param
	 * @return ajaxjson
	 * @link   #81788
	 */
	function actionDeltotalout(){
		$totallist = Totaloutlist::find('total_id = ?',request('total_id'))->getOne();
		if($totallist->isNewRecord()){
			return false;
		}else{
			$totalorderout = Totalorderout::find('total_no = ?',$totallist->total_no)->getCount();
			if($totalorderout==0){
				$totallist->destroy();
				return 'delsuccess';
			}elseif($totalorderout>0){
				return 'delfalse';
			}
		}
	}
	/**
	 * @todo   启程确认
	 * @author 吴开龙
	 * @since  2020-8-17 15:24:17
	 * @param
	 * @return ajaxjson
	 * @link   #81788
	 */
	function actionTrueTotalout(){
		$totallist = Totaloutlist::find('total_id = ?',request('total_id'))->getOne();
		if($totallist->isNewRecord()){
			return false;
		}else{
			$totallist->type = 1;
			$totallist->update_type_name = MyApp::currentUser('staff_id');
			$totallist->update_type_time = time();
			$totallist->save();
			echo 1;
			exit;
		}
	}
	/**
	 * 产品树
	 */
	function actionproducttree(){
		$products=Product::find('product_id in (?)',Productdepartmentavailable::availableproductids())->getAll();
		$checkeds = array ();
		if (request ( "checked" ) != null) {
			$checkeds = explode ( ",", request ( "checked" ) );
		}
		$array = array ();
		foreach($products as $product){
			$array[]=array(
				"id" => $product->product_name,
				"text" => $product->product_chinese_name,
				"checked" => in_array ( $product->product_name, $checkeds ) ? "checked" : "",
				"attributes" => "",
			);
		}
		echo json_encode($array);
		exit();
	}
	/**
	 * 新增、修改包裹启程扫描
	 */
	function actionEdittatolout(){
		if (strlen(request ( "total_id" ))>0) {
			$totalout = Totaloutlist::find ( "total_id = ?", request ( "total_id" ) )->getOne ();
		}else{
			$totalout = new Totaloutlist();
			$now='DF'.date('Ymd');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$far_no=$now.sprintf("%03d",$seq);
			$totalout->total_no = $far_no;
		}
		
		if (request_is_post ()) {
			$conn = QDB::getConn ();
			$conn->startTrans ();
			$totalout->out_department_id = MyApp::currentUser('department_id');
			$totalout->in_department_id = request ( "in_department_id" );
			if(request ( "product" )){
				$product = request ( "product" );
				sort($product);
				$totalout->service_code = implode(',',$product);
			}
			$totalout->consignee_name = request('consignee_name');
			$totalout->consignee_phone = request('consignee_phone');
			$totalout->consignee_address = request('consignee_address');
			$totalout->express_company = request('express_company');
			$totalout->express_no = request('express_no');
			$totalout->operation_name = MyApp::currentUser('staff_name');
			$totalout->operation_time = time();
			$totalout->save ();
			$conn->completeTrans ();
			return $this->_redirectMessage ( "包裹启程扫描保存", "保存成功", url ( "warehouse/edittatolout",array(
				'total_id'=>$totalout->total_id
			)) );
		}
		$this->_view ["totalout"] = $totalout;
		$this->_view ["order_count"] = count($totalout->totalorderout);;
		$this->_view['dpms']= Department::find()->getAll()->toHashMap('department_id','department_name');
	}
	//完成
	function actionOutfinished(){
		$total_out_list = Totaloutlist::find('total_no = ?',request('total_no'))->getOne();
		if(!$total_out_list->isNewRecord()){
			$total_out_list->status = '1';
			$total_out_list->save();
			return $this->_redirect(url('warehouse/totaloutlist'));
		}else{
			return $this->_redirectMessage('数据缺失', '', url('warehouse/totaloutlist'));
		}
	}
	/**
	 * @todo   抵达扫描
	 * @author 许杰晔
	 * @since  2020-8-17 15:24:17
	 * @param
	 * @return
	 * @link
	 */
	function actionTotalinlist(){
		$staffrole=StaffRole::find('staff_id = ? and role_id in (?)',MyApp::currentUser('staff_id'),array("1","7"))->getOne();
		if($staffrole->isNewRecord()){
			$total_in_list = Totalinlist::find('in_department_id = ?',MyApp::currentUser('department_id'));
		}else{
			$total_in_list = Totalinlist::find();
		}
		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
		//创建时间
		if(request('start_date',date('Y-m-d'))){
			$total_in_list->where('create_time >= ?',strtotime(request('start_date',date('Y-m-d')).'00:00:00'));
		}
		if(request('end_date')){
			$total_in_list->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//启程仓
		if(request('out_department_id')){
			$total_in_list->where('out_department_id = ?',request('out_department_id'));
		}
		//抵达仓
		if(request('in_department_id')){
			$total_in_list->where('in_department_id = ?',request('in_department_id'));
		}
		//总单单号
		if(request('total_no')){
			$total_no=explode("\r\n", request('total_no'));
			$total_no=array_filter($total_no);//去空
			$total_no=array_unique($total_no);//去重
			$total_in_list->where('total_no in (?)',$total_no);
		}
		if(request('export')=='export'){
			$list=clone $total_in_list;
			//设置导出表单head
			$sheet = array ( array('总单号','启程仓','抵达仓','启程总单号','总票数','总件数','总实重','总计费重','操作人','操作日期'));
			$details=$list->getAll ();
			foreach ($details  as $v ) {
				$order_count=0;$weight_cost=0;$weight_actual=0;$quantity=0;$quantity_sum=0;
				foreach ($v->totalorderin as $value){
					$order_count++;
					if($value->order->order_id){
						$faoutpackage = Faroutpackage::find('order_id = ?',$value->order->order_id)->asArray()->getAll();
						if(count($faoutpackage)>0){
							$weight_cost+=$value->order->weight_cost_out;
							$weight_actual+=$value->order->weight_actual_out;
							$quantity = Faroutpackage::find('order_id = ?',$value->order->order_id)->getSum('quantity_out');
						}else{
							$weight_cost+=$value->order->weight_income_in;
							$weight_actual+=$value->order->weight_actual_in;
							$quantity = Farpackage::find('order_id = ?',$value->order->order_id)->getSum('quantity');
						}
						$quantity_sum+=$quantity;
					}
				}
				$sheet [] = array (
					$v->total_no,
					$dpms[$v->out_department_id],
					$dpms[$v->in_department_id],
					$v->service_code,
					$order_count,
					$quantity_sum,
					$weight_actual,
					$weight_cost,
					$v->operation_name,
					Helper_Util::strDate('Y-m-d H:i:s', $v->operation_time),
				);
			}
			Helper_Excel::array2xls ( $sheet,'包裹抵达扫描导出'.date('Ymd',time()).'.xlsx' );
			exit();
		}
		$pagination = null;
		$total_in_list=$total_in_list->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['total_in_list']=$total_in_list;
		$this->_view['pagination']=$pagination;
		$this->_view['dpms']= $dpms;
	}
	function actionDeltotalin(){
		$totallist = Totalinlist::find('total_no = ?',request('total_no'))->getOne();
		if($totallist->isNewRecord()){
			return false;
		}else{
			$totalorderin = Totalorderin::find('total_no = ?',$totallist->total_no)->getCount();
			if($totalorderin>0){
				return 'delfalse';
			}elseif ($totalorderin==0){
				$totallist->destroy();
				return 'delsuccess';
			}
		}
	}
	function actionEdittatolin(){
		if (strlen(request ( "total_id" ))>0) {
			$totalin = Totalinlist::find ( "total_id = ?", request ( "total_id" ) )->getOne ();
		}else{
			$totalin = new Totalinlist();
			$now='AF'.date('Ymd');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$far_no=$now.sprintf("%03d",$seq);
			$totalin->total_no = $far_no;
		}
		$totallist = array();
		if(request('out_department_id')){
			$totalout=Totaloutlist::find('out_department_id = ? and in_department_id=?',request('out_department_id'),MyApp::currentUser('department_id'))
			->joinLeft('tb_total_orderout', '','tb_total_out.total_no=tb_total_orderout.total_no');
			$totalout->where('tb_total_orderout.state = "0"');
			$totalout_no=$totalout->group('total_no')->getAll();
			foreach ($totalout_no as $v){
				$totalinservice = Totalinlist::find("service_code = ?",$v->total_no)->getOne();
				if($totalinservice->isNewRecord()){
					$totallist[] = array(
						"id" => $v->total_no,
						"text" => $v->total_no
					);
				}else{
					continue;
				}
			}
		}
		if (request('save')=='save') {
			$totalin->in_department_id = MyApp::currentUser('department_id');
			$totalin->out_department_id = request ( "out_department_id" );
			$totalin->service_code = request ( "service_code" );
			$totalin->operation_name = MyApp::currentUser('staff_name');
			$totalin->operation_time = time();
			$totalin->save ();
			return $this->_redirectMessage ( "包裹抵达扫描保存", "保存成功", url ( "warehouse/totalinlist" ) );
		}
		$this->_view ["totalin"] = $totalin;
		$this->_view ["totalout"] = $totallist;
		$this->_view ["order_count"] = count($totalin->totalorderin);
		$this->_view['dpms']= Department::find()->getAll()->toHashMap('department_id','department_name');
	}
	function actioninfinished(){
		$total_in_list = Totalinlist::find('total_no = ?',request('total_no'))->getOne();
		if(!$total_in_list->isNewRecord()){
			$orderout = Totalorderout::find('total_no = ? and state = "0"',$total_in_list->service_code)->asArray()->getAll();
			if(count($orderout)>0){
				return $this->_redirectMessage('存在未扫描订单', '', url('warehouse/totalinlist'));
			}else{
				$total_in_list->status = '1';
				$total_in_list->save();
				return $this->_redirect(url('warehouse/totalinlist'));
			}
		}else{
			return $this->_redirectMessage('数据缺失', '', url('warehouse/totalinlist'));
		}
	}
	function actionTotalindetail(){
		$totalorderin = Totalorderin::find('total_no = ?',request('total_no'));
		if(request('order_no')){
			$totalorderin->where('ali_order_no = ? or tracking_no = ?',request('order_no'),request('order_no'));
		}
		if(request('export')=='exportlist'){
			$list=clone $totalorderin;
			//设置导出表单head
			$sheet = array ( array('总单单号','阿里单号','末端单号','产品','件数','实重','目的国家'));
			$details=$list->getAll ();
			foreach ($details  as $v ) {
				$weight = 0; $sum = 0;
				if($v->order->order_id){
					$quantity = Faroutpackage::find('order_id = ?',$v->order->order_id)->asArray()->getAll();
					if(count($quantity) > 0){
						$weight = $v->order->weight_actual_out;
						$sum = Faroutpackage::find('order_id = ?',$v->order->order_id)->getSum('quantity_out');
					}else{
						$weight = $v->order->weight_actual_in;
						$sum = Farpackage::find('order_id = ?',$v->order->order_id)->getSum('quantity');
					}
				}
				$sheet [] = array (
					$v->total_no,
					$v->ali_order_no,
					$v->tracking_no,
					$v->order->service_code,
					$sum,
					$weight,
					$v->order->consignee_country_code
				);
			}
			Helper_Excel::array2xls ( $sheet,'包裹抵达扫描详情导出'.date('Ymd',time()).'.xlsx' );
			exit();
		}
		$pagination = null;
		$totalorderin=$totalorderin->limitPage ( (request_is_post () ? 1 : request ( "page", 1 )), 30 )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['lists']=$totalorderin;
		$this->_view['total_no']=request('total_no');
		$this->_view['pagination']=$pagination;
	}
	function actionTotaloutdetail(){
		$totalorderout = Totalorderout::find('total_no = ?',request('total_no'));
		if(request('order_no')){
			$totalorderout->where('ali_order_no = ? or tracking_no = ?',request('order_no'),request('order_no'));
		}
		if(request('export')=='exportlist'){
			$state = array('0'=>'未抵达','1'=>'已抵达');
			$list=clone $totalorderout;
			//设置导出表单head
			$sheet = array ( array('状态','总单单号','阿里单号','末端单号','产品','件数','实重','目的国家'));
			$details=$list->getAll ();
			foreach ($details  as $v ) {
				$weight = 0; $sum = 0;
				if($v->order->order_id){
					$quantity = Faroutpackage::find('order_id = ?',$v->order->order_id)->asArray()->getAll();
					if(count($quantity) > 0){
						$weight = $v->order->weight_actual_out;
						$sum = Faroutpackage::find('order_id = ?',$v->order->order_id)->getSum('quantity_out');
					}else{
						$weight = $v->order->weight_actual_in;
						$sum = Farpackage::find('order_id = ?',$v->order->order_id)->getSum('quantity');
					}
				}
				$sheet [] = array (
					$state[$v->state],
					$v->total_no,
					$v->ali_order_no,
					$v->tracking_no,
					$v->order->service_code,
					$sum,
					$weight,
					$v->order->consignee_country_code
				);
			}
			Helper_Excel::array2xls ( $sheet,'包裹启程扫描详情导出'.date('Ymd',time()).'.xlsx' );
			exit();
		}
		$pagination = null;
		$totalorderout=$totalorderout->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['lists']=$totalorderout;
		$this->_view['total_no']=request('total_no');
		$this->_view['pagination']=$pagination;
	}
	/*
	 * 获取轨迹
	 */
	function route($order_id,$location,$context){
		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
		$log = new OrderLog();
		$log->order_id = $order_id;
		$log->staff_id = MyApp::currentUser('staff_id');
		$log->staff_name = MyApp::currentUser('staff_name');
		$route = array(
			'location'=>$dpms[$location],
			'context'=>$context,
			'time'=>time(),
			'timezone'=>8
		);
		$log->comment = print_r($route,true);
		$log->save();
		return;
	}
	/*
	 * 包裹启程扫描
	 */
	function actionpackageout(){
		$orderout=Totalorderout::find('total_no = ?',request('total_no'))->getAll();
		if(request_is_post()){
			$data=array();
			//阿里/末端/交货核查总单/抵达总单
			$order_no=request('ali_order_no');
			$total_no=request('total_no');
			$total=Totaloutlist::find('total_no = ?',$total_no)->getOne();
			$order= Order::find()->where('ali_order_no=? and department_id=? and service_code in (?)',$order_no,$total->out_department_id,explode(',', $total->service_code))->getOne();
			//阿里单号查订单不存在 已出库订单必须使用末端单号查询
			if($order -> isNewRecord()){
				$order= Order::find()->where('tracking_no=? and department_id=? and service_code in (?)',$order_no,$total->out_department_id,explode(',', $total->service_code))->getOne();
				//末端单号查订单不存在
				if($order -> isNewRecord()){
					$order= Order::find()->where('total_list_no=? and department_id=? and service_code in (?)',$order_no,$total->out_department_id,explode(',', $total->service_code))->getAll();
					//交货核查总单查订单不存在
					if(count($order)==0){
						$total_in=Totalinlist::find('total_no = ? and in_department_id=?',$order_no,$total->out_department_id)->getOne();
						if($total_in->isNewRecord() || !$total_in->service_code){
							$data['message']='notexists';
							echo json_encode($data);
							exit();
						}
						if($total_in->status=='0'){
							$data['message']='wancheng';
							echo json_encode($data);
							exit();
						}
						$total_out=Totalorderout::find('total_no = ? ',$total_in->service_code)->getAll();
						if(count($total_out)==0){
							$data['message']='notexists';
							echo json_encode($data);
							exit();
						}
						foreach ($total_out as $ou){
							$out=Totalorderout::find('total_no = ? and ali_order_no=?',$total_no,$ou->ali_order_no)->getOne();
							if($out -> isNewRecord()){
								$out=new Totalorderout(array(
									'total_no'=>$total_no,
									'ali_order_no'=>$ou->ali_order_no,
									'tracking_no'=>$ou->tracking_no,
									'flag'=>$ou->flag
								));
								$out->save();
								$order=Order::find('ali_order_no = ?',$ou->ali_order_no)->getOne();
								self::route($order->order_id,MyApp::currentUser('department_id'),'Departed warehouse');
							}
						}
					}else {
						//交货核查总单单号扫描
						$to=Totallist::find('total_list_no = ?',$order_no)->getOne();
						if($to->status=='0'){
							$data['message']='wancheng';
							echo json_encode($data);
							exit();
						}
						$count=Order::find('total_list_no = ? ',$order_no)->getCount();
						if(count($order) <> $count){
							$data['message']='ckerror';
							echo json_encode($data);
							exit();
						}
						foreach ($order as $o){
							//订单状态必须是已出库
							if(!in_array($o->order_status,array('7','8'))){
								$data['message']='stateeeror';
								echo json_encode($data);
								exit();
							}
						}
						foreach ($order as $o){
							$out=Totalorderout::find('total_no = ? and ali_order_no=?',$total_no,$o->ali_order_no)->getOne();
							if($out -> isNewRecord()){
								$out=new Totalorderout(array(
									'total_no'=>$total_no,
									'ali_order_no'=>$o->ali_order_no,
									'tracking_no'=>$o->tracking_no,
									'flag'=>'1'
								));
								$out->save();
								self::route($o->order_id,MyApp::currentUser('department_id'),'Departed warehouse');
							}
						}
					}
				}else {
					if(!in_array($order->order_status, array('6','7','8'))){
						$data['message']='stateeeror';
						echo json_encode($data);
						exit();
					}
					$out=Totalorderout::find('total_no = ? and ali_order_no=?',$total_no,$order->ali_order_no)->getOne();
					if($out -> isNewRecord()){
						$out=new Totalorderout(array(
							'total_no'=>$total_no,
							'ali_order_no'=>$order->ali_order_no,
							'tracking_no'=>$order->tracking_no,
							'flag'=>'1'
						));
						$out->save();
						self::route($order->order_id,MyApp::currentUser('department_id'),'Departed warehouse');
					}
				}
			}else {
				$err_status = array (
					1 => '未入库',
					3 => '已退货',
					6 => '已打印',
					7 => '已出库',
					8 => '已提取',
					9 => '已签收',
					13 => '已结束'
				);
				if (array_key_exists ( $order->order_status, $err_status )) {
					$data['message']='stateeeror';
					echo json_encode($data);
					exit();
				}
				$out=Totalorderout::find('total_no = ? and ali_order_no=?',$total_no,$order->ali_order_no)->getOne();
				if($out -> isNewRecord()){
					$out=new Totalorderout(array(
						'total_no'=>$total_no,
						'ali_order_no'=>$order->ali_order_no,
						'tracking_no'=>$order->tracking_no,
						'flag'=>'0'
					));
					$out->save();
					self::route($order->order_id,MyApp::currentUser('department_id'),'Departed warehouse');
				}
				$err_status2 = array (
					2 => '已取消',
					11 => '待退货',
					12 => '已扣件',
				);
				if (array_key_exists ( $order->order_status, $err_status2 )) {
					$data['message']=$err_status2[$order->order_status];
					echo json_encode($data);
					exit();
				}
			}
			$data['message']='success';
			echo json_encode($data);
			exit();
		}
		$this->_view['order']=$orderout;
	}
	/*
	 * 包裹抵达扫描
	 */
	function actionpackagein(){
		$total_no=request('total_no');
		$totalin=Totalinlist::find('total_no = ?',$total_no)->getOne();
		$select = Totalorderout::find ( 'state = ?', '0' );
		$account=request('service_code');
		$select->where ( 'total_no = ?', $account );
		$order = $select->asArray ()
		->getAll ();
		//提交修改
		if (request ( 'sub_code' )) {
			$sub_code = explode ( "\r\n", request ( 'sub_code' ) );
			foreach ($sub_code as $sub){
				$orderin=Totalorderin::find('total_no = ? and (ali_order_no=? or tracking_no = ?)',$total_no,$sub,$sub)->getOne();
				if($orderin -> isNewRecord()){
					$tborder=Order::find('ali_order_no = ? or tracking_no = ?',$sub,$sub)->getOne();
					$orderin=new Totalorderin(array(
						'total_no'=>$total_no,
						'ali_order_no'=>$tborder->ali_order_no,
						'tracking_no'=>$tborder->tracking_no
					));
					$orderin->save();
					self::route($tborder->order_id,MyApp::currentUser('department_id'),'Received at warehouse');
				}
				$orderout=Totalorderout::find('total_no = ? and (ali_order_no=? or tracking_no = ?)',$account,$sub,$sub)->getOne();
				$orderout->state=1;
				$orderout->save();
			}
			return $this->_redirectMessage ( '核对成功', '成功', url ( '/packagein' ,array('total_no'=>$total_no,'service_code'=>$account)), 3 );
		}
		$this->_view['order']=$order;
		$this->_view['totalin']=$totalin;
	}
	/**
	 * @todo   抵达确认
	 * @author 吴开龙
	 * @since  2020-8-17 15:50:17
	 * @param
	 * @return ajaxjson
	 * @link   #81907
	 */
	function actionTrueTotalin(){
		$totallist = Totalinlist::find('total_no = ?',request('total_no'))->getOne();
		if($totallist->isNewRecord()){
			return false;
		}else{
			$totallist->type = 1;
			$totallist->update_type_name = MyApp::currentUser('staff_id');
			$totallist->update_type_time = time();
			$totallist->save();
			echo 1;
			exit;
		}
	}
	//批量设置轨迹
	function actionTotaltrack(){
		$total_list = TotalTrack::find();
		//创建时间
		if(request('start_date')){
			$total_list->where('create_time >= ?',strtotime(request('start_date').'00:00:00'));
		}
		if(request('end_date')){
			$total_list->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//总单单号
		if(request('total_list_no')){
			$total_list_no=explode("\r\n", request('total_list_no'));
			$total_list_no=array_filter($total_list_no);//去空
			$total_list_no=array_unique($total_list_no);//去重
			$total_list->where('total_list_no in (?)',$total_list_no);
		}
		if(request('addnewtotal') == 'addnewtotal'){
			$new_totaltrack = new TotalTrack();
			$new_totaltrack->total_list_no = date('YmdHis');
			$new_totaltrack->operation_name = MyApp::currentUser('staff_name');
			$new_totaltrack->operation_time = time();
			$new_totaltrack->save();
			return $this->_redirect(url('/totaltrack'));
		}
		$pagination = null;
		$total_list=$total_list->limitPage ( request ( "page", 1 ), 30 )
		->fetchPagination ( $pagination )->order('create_time desc')->getAll();
		$this->_view['total_list']=$total_list;
		$this->_view['pagination']=$pagination;
	}
	//轨迹总单详情
	function actionTotaltrackdetail(){
		$totalorder = Totalordertrack::find('total_no = ?',request('total_list_no'));
		$pagination = null;
		$totalorder=$totalorder->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['totalorder']=$totalorder;
		$this->_view['pagination']=$pagination;
	}
	//绑定订单
	function actionBindorder(){
		$bindorder=Totalordertrack::find('total_no = ?',request('total_list_no'));
		$total_list_no = request('total_list_no');
		$data = array();
		if(request('ali_order_no')){//阿里单号
			$ali_order_nos=explode("\r\n", request('ali_order_no'));
			$ali_order_nos=array_filter($ali_order_nos);//去空
			$ali_order_nos=array_unique($ali_order_nos);//去重
			foreach ($ali_order_nos as $ali_order_no){
				$order = Order::find('ali_order_no = ?',$ali_order_no)->getOne();
				if($order->isNewRecord()){//订单不存在
					continue;
				}else{
					$totalordertrack = Totalordertrack::find('total_no = ? and ali_order_no = ?',$total_list_no,$ali_order_no)->getOne();
					if(!$totalordertrack->isNewRecord()){//已绑定
						continue;
					}else{
						$totalordertrack = new Totalordertrack();
						$totalordertrack->total_no = $total_list_no;
						$totalordertrack->ali_order_no = $ali_order_no;
						$totalordertrack->tracking_no = $order->tracking_no;
						$totalordertrack->save();
					}
				}
			}
		}
		if(request('tracking_no')){//末端单号
			$tracking_nos=explode("\r\n", request('tracking_no'));
			$tracking_nos=array_filter($tracking_nos);//去空
			$tracking_nos=array_unique($tracking_nos);//去重
			foreach ($tracking_nos as $tracking_no){
				$order = Order::find('tracking_no = ?',$tracking_no)->getOne();
				if($order->isNewRecord()){//订单不存在
					continue;
				}else{
					$totalordertrack = Totalordertrack::find('total_no = ? and tracking_no = ?',$total_list_no,$tracking_no)->getOne();
					if(!$totalordertrack->isNewRecord()){//已绑定
						continue;
					}else{
						$totalordertrack = new Totalordertrack();
						$totalordertrack->total_no = $total_list_no;
						$totalordertrack->ali_order_no = $order->ali_order_no;
						$totalordertrack->tracking_no = $tracking_no;
						$totalordertrack->save();
					}
				}
			}
		}
		if(request('comparison_total_no')){//核查总单号
			$comparison_total_no = request('comparison_total_no');
			$orders = Order::find('total_list_no = ?',$comparison_total_no)->getAll();
			if(count($orders)>0){
				foreach($orders as $order){
					$totalordertrack = Totalordertrack::find('total_no = ? and ali_order_no = ?',$total_list_no,$order->ali_order_no)->getOne();
					if(!$totalordertrack->isNewRecord()){
						continue;
					}else{
						$totalordertrack = new Totalordertrack();
						$totalordertrack->total_no = $total_list_no;
						$totalordertrack->ali_order_no = $order->ali_order_no;
						$totalordertrack->tracking_no = $order->tracking_no;
						$totalordertrack->save();
					}
				}
			}
		}
		if(request('out_total_no')){//启程总单
			$out_total_no = request('out_total_no');
			$totalouts = Totalorderout::find('total_no = ?',$out_total_no)->getAll();
			if(count($totalouts)>0){
				foreach($totalouts as $totalout){
					$totalordertrack = Totalordertrack::find('total_no = ? and ali_order_no = ?',$total_list_no,$totalout->ali_order_no)->getOne();
					if(!$totalordertrack->isNewRecord()){
						continue;
					}else{
						$order = Order::find('ali_order_no = ?',$totalout->ali_order_no)->getOne();
						$totalordertrack = new Totalordertrack();
						$totalordertrack->total_no = $total_list_no;
						$totalordertrack->ali_order_no = $order->ali_order_no;
						$totalordertrack->tracking_no = $order->tracking_no;
						$totalordertrack->save();
					}
				}
			}
		}
		if(request('in_total_no')){//抵达总单
			$in_total_no = request('in_total_no');
			$totalins = Totalorderin::find('total_no = ?',$in_total_no)->getAll();
			if(count($totalins)>0){
				foreach($totalins as $totalin){
					$totalordertrack = Totalordertrack::find('total_no = ? and ali_order_no = ?',$total_list_no,$totalin->ali_order_no)->getOne();
					if(!$totalordertrack->isNewRecord()){
						continue;
					}else{
						$order = Order::find('ali_order_no = ?',$totalin->ali_order_no)->getOne();
						$totalordertrack = new Totalordertrack();
						$totalordertrack->total_no = $total_list_no;
						$totalordertrack->ali_order_no = $order->ali_order_no;
						$totalordertrack->tracking_no = $order->tracking_no;
						$totalordertrack->save();
					}
				}
			}
		}
		$pagination = null;
		$this->_view['order']=$bindorder->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['pagination']=$pagination;
	}
	function actionTotaltracking(){
		$trace_code=array_combine(array_keys(Tracking::$trace_code_cn),array_keys(Tracking::$trace_code_cn));
		$trace_code_cn=Tracking::$trace_code_cn;
		$totaltracking = Totaltracking::find('total_no = ?',request('total_list_no'))->getAll();
		if(request_is_post()){
			$lasttracking = Totaltracking::find('total_no = ?',request('total_list_no'))->order('tracking_id desc')->getOne();
			$lasttime =$lasttracking->trace_time + (8-$lasttracking->timezone )*3600;
			$requesttime = strtotime(request('trace_time'))+(8-request('timezone'))*3600;
			if($requesttime<$lasttime){
				return $this->_redirectMessage('失败','新增轨迹时间应不小于最近一条轨迹时间',url('warehouse/totaltracking',array('total_list_no'=>request('total_list_no'))));
			}else{
				$tracking = new Totaltracking();
				$tracking->changeProps(array(
					'total_no'=>request('total_list_no'),
					'tracking_code'=>request('tracking_code'),
					'location'=>request('location'),
					'trace_desc_cn'=>$trace_code_cn[request('tracking_code')],
					'operator_name'=>MyApp::currentUser('staff_name'),
					'timezone'=>request('timezone'),
					'trace_time'=>strtotime(request('trace_time'))
				));
				$tracking->save();
				$totalordertracks = Totalordertrack::find('total_no = ?',request('total_list_no'))->getAll();
				foreach($totalordertracks as $track){
					$order = Order::find('ali_order_no = ?',$track->ali_order_no)->getOne();
					$last_track = Tracking::find('order_id = ?',$order->order_id)->order('tracking_id desc')->getOne();
					$last_track_time = $last_track->trace_time + (8-$last_track->timezone )*3600;
					$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
					if($requesttime<$last_track_time){
						continue;
					}else{
						$trace=new Tracking();
						$trace->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'far_no'=>$order->far_no,
							'tracking_code'=>request('tracking_code'),
							'location'=>request('location'),
							'trace_desc_cn'=>$trace_code_cn[request('tracking_code')],
							'operator_name'=>MyApp::currentUser('staff_name'),
							'timezone'=>request('timezone'),
							'quantity'=>$quantity['sum_quantity'],
							'trace_time'=>strtotime(request('trace_time')),
							'total_no'=>$tracking->tracking_id
						));
						//EMS-FY并且轨迹代码是F_CARRIER_PICKUP_RT_5035
						if(request('tracking_code')=='F_CARRIER_PICKUP_RT_5035'){
							if($order->service_code=='EMS-FY'){
								$trace->changeProps(array(
									'trace_desc_cn'=>'包裹重新安排转运,转【'.$order->tracking_no.'】',
									'trace_desc_en'=>'Reschedule transshipment to EMS['.$order->tracking_no.'].Track in:http://www.ems.com.cn/english.html or https://www.17track.net/en'
								));
							}else {
								$trace->changeProps(array(
									'trace_desc_cn'=>'包裹重新安排转运,转【'.$order->tracking_no.'】',
									'trace_desc_en'=>'Reschedule transshipment'
								));
							}
						}
						$trace->save();
						//修改订单物流轨迹状态1（手动添加数据）
						$order->abnormal_time_flag = 1;
						$order->save();
						//保存日志
						$delierylog = new DeliveryLog();
						$delierylog->order_id = $order->order_id;
						$delierylog->tracking_no = $order->tracking_no;
						$delierylog->tracking_id = $trace->tracking_id;
						$delierylog->staff_id = MyApp::currentUser('staff_id');
						$delierylog->staff_name = MyApp::currentUser('staff_name');
						$delierylog->comment = '轨迹代码：'.$trace->tracking_code.';描述信息：'.$trace->trace_desc_cn.';轨迹时间：'.date('Y-m-d H:i:s',$trace->trace_time);
						$delierylog->save();
						if(request('tracking_code')=='S_DELIVERY_SIGNED'){
							$trace->status='1';
							$trace->save();
							$event=new Event();
							if($order->customer->customs_code=='ALPL'){
								$event_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
							}else{
								$event_code = 'DELIVERY';
							}
							$event->changeProps(array(
								'order_id'=>request('order_id'),
								'customer_id'=>$order->customer_id,
								'event_code'=>$event_code,
								'event_time'=>strtotime(request('trace_time')),
								'event_location'=>request('location'),
								'timezone'=>request('timezone'),
								'confirm_flag'=>'1'
							));
							$event->save();
							//订单状态转为签收的规则为：订单的签收事件有创建且发送结果为成功时，才转移#83287
							// 							$order->order_status='9';
							// 							$order->save();
						}
					}
				}
				return $this->_redirectMessage('成功','保存成功',url('warehouse/totaltracking',array('total_list_no'=>request('total_list_no'))));
			}
		}
		$this->_view['trace_code']=$trace_code;
		$this->_view['list']=$totaltracking;
	}
	/**
	 * 轨迹确认和忽略
	 */
	function actiontrackconfirm(){
		$trace=Totaltracking::find('tracking_id=?',request('tracking_id'))->getOne();
		if($trace->timezone==-19 || $trace->isNewRecord()){
			return $this->_redirectMessage('时区错误或记录不存在', '请修正时区后再继续', url('warehouse/totaltracking',array('total_list_no'=>$trace->total_no)));
		}else{
			$trace->confirm_flag='1';
			$trace->save();
			$trackings = Tracking::find('total_no = ? and confirm_flag = "0"',$trace->tracking_id)->getAll();
			if(count($trackings)>0){
				foreach($trackings as $tracking){
					$tracking->confirm_flag='1';
					$tracking->save();
				}
			}
		}
		return $this->_redirect(  url('warehouse/totaltracking',array('total_list_no'=>$trace->total_no)));
	}
	function actionIgnore(){
		$trace=Totaltracking::find('tracking_id=?',request('tracking_id'))->getOne();
		if (!$trace->isNewRecord()){
			$trace->confirm_flag=2;
			$trace->save();
			$trackings = Tracking::find('total_no = ? and confirm_flag = "0"',$trace->tracking_id)->getAll();
			if(count($trackings)>0){
				foreach($trackings as $tracking){
					$tracking->confirm_flag=2;
					$tracking->save();
				}
			}
		}
		return $this->_redirect(  url('warehouse/totaltracking',array('total_list_no'=>$trace->total_no)));
	}
	function actionAllcheck(){
		$trace_check = Totaltracking::find('total_no = ? and confirm_flag = "0"',request('total_list_no'))->getAll();
		foreach ($trace_check as $check){
			$check->confirm_flag = '1';
			$check->save();
			$trackings = Tracking::find('total_no = ? and confirm_flag = "0"',$check->tracking_id)->getAll();
			if(count($trackings)>0){
				foreach($trackings as $tracking){
					$tracking->confirm_flag = '1';
					$tracking->save();
				}
			}
		}
		return $this->_redirect(url('warehouse/totaltracking',array('total_list_no'=>request('total_list_no'))));
	}
	
	/**
	 * 抵达扫描判断是否是美东美西
	 */
	function actioncheckchannel(){
		$sub_code = request('sub_code');
		$order = Order::find('ali_order_no = ? or tracking_no = ?',$sub_code,$sub_code)->getOne();
		if($order->channel->label_sign == 'WWW'){
			$data['message'] = 'WWW';
		}else if($order->channel->label_sign == 'EEE'){
			$data['message'] = 'EEE';
		}else {
			$data['message'] = 'WU';
		}
		echo json_encode($data);
		exit();
	}
	/**
	 * 扫描国内快递单号
	 */
	function actionScanTotalList(){
		$select = ScanTotalList::find ();
		$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
		$department = RelevantDepartment::relateddepartments();
		$logistics = Helper_Array::toHashmap ( CodeLogistics::find()->asArray()->getAll(), 'id', 'name' );
		if (request ( 'operation_name' )) {
			$select->where ( 'operation_name like ?', '%' . request ( 'operation_name' ) . '%' );
		}
		if (request ( 'department_id' )) {
			$select->where ( 'department_id = ?', request ( 'department_id' ) );
		}elseif($relevant_departments){
			$select->where ( 'department_id in (?)', $relevant_departments );
		}
		
		if (request ( 'total_no' )) {
			$select->where ( 'total_no = ?', request ( 'total_no' ) );
		}
		
		if (request ( 'logistics_id' )) {
			$select->where ( 'logistics_id = ?', request ( 'logistics_id' ) );
		}
		if (request ( 'start_date')) {
			$select->where ( 'scan_no_time >= ?', strtotime ( request ( 'start_date') ) );
		}
		if (request ( 'end_date' )) {
			$select->where ( 'scan_no_time <= ?', strtotime ( request ( 'end_date' ) ) );
		}
		if (request ( 'reference_no' )) {
			$reference_no_arr = explode ( "\n", request ( 'reference_no' ) );
			$arr = array_filter ( $reference_no_arr );
			$arr = array_unique ( $arr );
			$ids = array ();
			if (count ( $arr ) > 0) {
				$ids = ScanTotalDetail::find ( 'reference_no in (?)', $arr )->setColumns ( 'total_id' )
				->group('total_id')
				->getAll ()
				->getCols ( 'total_id' );
			}
			if (count ( $ids ) > 0) {
				$select->where ( 'id in (?)', $ids );
			}else{
				$select->where('1!=1');
			}
		}
		$pagination = null;
		$sacnlist = $select->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('create_time desc')
		->getAll ();
		
		$this->_view ['department'] = $department;
		$this->_view ['pagination'] = $pagination;
		$this->_view ['sacnlist'] = $sacnlist;
		$this->_view ['logistics'] = $logistics;
	}
	/**
	 * @todo 国内快递导出
	 * @author 许杰晔
	 * @since 2020.6.5
	 * @param $total_no
	 * @link #80211
	 */
	function actionScanTotalExport(){
		set_time_limit(0);//不限制超时时间
		ini_set('memory_limit', '-1');//不限制内存
		if (request ( 'total_no' )) {
			$all_list = ScanTotalList::find ('total_no=?',request ( 'total_no' ))->getOne();
			$logis=CodeLogistics::find('id=?',$all_list->logistics_id)->getOne();
			$department = Helper_Array::toHashmap ( Department::departmentlist (), 'department_id', 'department_name' );
			$objexcel = new PHPExcel();
			$sheet = $objexcel->getActiveSheet();
			// 			$objExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);#设置单元格行高
			// 			$objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);#设置单元格宽度
			$objexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$sheet->mergeCells('B1:G1');
			$sheet->getStyle('B1:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('B1', Helper_Util::strDate('Y-m-d H:i:s', $all_list->scan_no_time).$logis->name.'送货到仓交接清单');
			$sheet->getStyle('B1:G1')->getFont()->setSize(11);
			$sheet->getStyle('B1:G1')->getFont()->setBold(true);
			$sheet->setCellValue('B2', '日期：');
			$sheet->setCellValue('F2', '总件数：');
			$sheet->setCellValue('B3', '序号');
			$sheet->setCellValue('C3', '仓库');
			$sheet->setCellValue('D3', '到仓总单号');
			$sheet->setCellValue('E3', '快递单号');
			$sheet->setCellValue('F3', '扫描时间');
			$sheet->setCellValue('G3', '订单预报');
			$sheet->setCellValue('H3', '备注');
			$count = 0;
			$i = 1;
			$j = 4;
			//Support #84243
			$details = ScanTotalDetail::find('total_id = ?',$all_list->id)->getAll();
			if(count($details)>0){
				$count = count($details);
				foreach ($details as $detail){
					$sheet->setCellValue("B$j", $i);
					$sheet->getStyle("B$j:G$j")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->setCellValue("C$j", $department[$all_list->department_id]);
					$sheet->setCellValue("D$j", ' '.$all_list->total_no);
					$sheet->setCellValue("E$j", ' '.$detail->reference_no);
					$sheet->setCellValue("F$j", Helper_Util::strDate('Y-m-d H:i:s', $detail->scan_no_time));
					$sheet->setCellValue("G$j", ScanTotalDetail::$s_status[$detail->status]);
					$sheet->setCellValue("H$j", '');
					$i++;
					$j++;
				}
			}
			$h = $j+1;
			$sheet->setCellValue("F$j", '送货人签字：');
			$sheet->setCellValue("F$h", '交接人签字：');
			$sheet->setCellValue('C2', date('Y-m-d'));
			$sheet->setCellValue('G2', $count);
			$sheet->setTitle('交接清单');
			//快递公司列表
			// 			$list_copy = clone $select;
			// 			$logistics_arr = $list_copy->group ( 'logistics_id' )
			// 				->setColumns ( 'logistics_id' )
			// 				->getAll ()
			// 				->getCols ( 'logistics_id' );
			/* $sheet2Array = array (
			 array('服务商列表','操作'),
			 array('吉通','开始扫描'),
			 array('FAR','开始扫描'),
			 array('德邦','开始扫描'),
			 array('顺丰','开始扫描'),
			 array('申通','开始扫描'),
			 array('圆通','开始扫描'),
			 array('中通','开始扫描'),
			 array('百世','开始扫描'),
			 array('汇通','开始扫描'),
			 array('韵达','开始扫描'),
			 array('中国邮政','开始扫描'),
			 array('优速','开始扫描'),
			 array('天天','开始扫描'),
			 array('宅急送','开始扫描'),
			 array('全峰','开始扫描'),
			 array('跨越','开始扫描'),
			 array('京东','开始扫描'),
			 array('佳吉','开始扫描'),
			 array('速尔','开始扫描'),
			 array('快捷','开始扫描'),
			 array('安能','开始扫描'),
			 array('如风达','开始扫描'),
			 array('联昊通','开始扫描'),
			 array('全一','开始扫描'),
			 array('其它','开始扫描'),
			 array('增益全日通','开始扫描'),
			 array('信丰物流','开始扫描'),
			 array('天地华宇','开始扫描'),
			 array('58速运','开始扫描'),
			 array('快狗','开始扫描'),
			 array('同城','开始扫描'),
			 );
			 $sheet2name = '快递公司列表';
			 $sheet2 = $objexcel->addSheet ( new PHPExcel_Worksheet () );
			 if (! is_null ( $sheet2name )) {
			 $sheet2->setTitle ( $sheet2name );
			 }
			 @$sheet2->fromArray ( $sheet2Array, null, 'A1', true ); */
			header('Content-Type: application/vnd.ms-excel');
			@$objwriter = PHPExcel_IOFactory::createWriter($objexcel, 'Excel5');
			header('Content-Disposition: attachment;filename='.Helper_Util::strDate('Y-m-d', $all_list->scan_no_time).$logis->name.'送货到仓交接清单.xls');
			header('Cache-Control: max-age=0');
			try {
				@$objwriter->save('php://output');
			} catch (PHPExcel_Writer_Exception $ex) {
				$tmpf = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time() . '.xlsx';
				@$objwriter->save($tmpf);
				readfile($tmpf);
				unlink($tmpf);
			}
			
		}
		exit();
	}
	function actionScanTotalDetail(){
		$pagination = null;
		$department = Helper_Array::toHashmap ( Department::departmentlist (), 'department_id', 'department_name' );
		$logistics_code = Helper_Array::toHashmap ( CodeLogistics::find()->asArray()->getAll(), 'id', 'name' );
		$list_single = ScanTotalList::find ( 'id = ?', request ( 'id' ) )->getOne ();
		$details = ScanTotalDetail::find ( 'total_id = ?', request ( 'id' ) )->order('scan_no_time desc')
		->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->getAll ();
		$this->_view ['list_single'] = $list_single;
		$this->_view ['details'] = $details;
		$this->_view ['department'] = $department;
		$this->_view ['pagination'] = $pagination;
		$this->_view ['logistics_code'] = $logistics_code;
	}
	/**
	 * 扫描
	 */
	function actionScanReferenceNo(){
		if(request('reference_no')){
			$data = array();
			$detail = ScanTotalDetail::find('reference_no = ?',request('reference_no'))->order('create_time desc')->getOne();
			if(!$detail->isNewRecord()){
				$data['message'] = 'repetition';
				echo json_encode($data);
				exit();
			}
			$order = Order::find('reference_no like ?','%'.request('reference_no').'%')->order('create_time desc')->getOne();
			if($order->isNewRecord()){
				$data['message'] = 'noreferenceno';
				echo json_encode($data);
				exit();
			}
			$data['message'] = 'success';
			echo json_encode($data);
			exit();
		}
	}
	
	/**
	 * @todo 修改国内物流方式
	 * @author 许杰晔
	 * @since 2020.6.12
	 * @param
	 * @return view
	 * @link #80322
	 */
	function actionChangeScan(){
		if(request('scan_id')){
			$scan = ScanTotalList::find ('id=?',request('scan_id'))->getOne();
			if(!$scan->isNewRecord()){
				$scan->logistics_id=request('lgt_id');
				$scan->save();
			}
		}
		return $this->_redirectMessage ( '成功', '', url ( "warehouse/scantotallist" ) );
	}
	
	/**
	 * 保存
	 */
	function actionScanReferenceNoSave(){
		if(request('jsonstr')){
			$data=array();
			$str_arr = json_decode(request('jsonstr'),true);
			$logistics_id = $str_arr['logistics_id'];
			$fail_arr = $str_arr['fail-list'];
			$success_arr = $str_arr['success-list'];
			if(count($success_arr)==0 && count($fail_arr)==0){
				$data['message'] = 'nodata';
				echo json_encode($data);
				exit();
			}
			$scanlist_single = new ScanTotalList();
			$now=date('Ymd');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$list_no=$now.sprintf("%03d",$seq);
			$scanlist_single->total_no = $list_no;
			$scanlist_single->department_id = MyApp::currentUser('department_id');
			$scanlist_single->scan_no_time = time();
			$scanlist_single->logistics_id = $logistics_id;
			$scanlist_single->operation_name = MyApp::currentUser('staff_name');
			$scanlist_single->save();
			
			if(count($success_arr)>0){
				foreach ($success_arr as $value){
					$scandetail = new ScanTotalDetail();
					$scandetail->total_id = $scanlist_single->id;
					$scandetail->reference_no = $value['reference_no'];
					$scandetail->scan_no_time = strtotime($value['time']);
					$scandetail->status = 1;
					$scandetail->save();
					$order=Order::find('reference_no=?',$value['reference_no'])->getOne();
					if($order->customer->customs_code=='ALCN'){
						$cainiao=CaiNiao::find('cainiao_code=? and order_id=?','ARRIVE',$order->order_id)->getOne();
						if($cainiao->isNewRecord()){
							$cainiao->changeProps(array(
								'order_id'=>$order->order_id,
								'cainiao_code'=>'ARRIVE',
								'cainiao_time'=>strtotime($value['time']),
								'confirm_flag'=>1,
								'operator'=>MyApp::currentUser('staff_name')
							));
							$cainiao->save();
						}
					}
				}
			}
			if(count($fail_arr)>0){
				foreach ($fail_arr as $value){
					$scandetail = new ScanTotalDetail();
					$scandetail->total_id = $scanlist_single->id;
					$scandetail->reference_no = $value['reference_no'];
					$scandetail->scan_no_time = strtotime($value['time']);
					$scandetail->save();
				}
			}
			$data['message'] = 'success';
			echo json_encode($data);
			exit();
		}
	}
	
	/**
	 * 轨迹批量导入
	 */
	function actionBatchtraceImport(){
		set_time_limit ( 0 );
		ini_set ( "memory_limit", "3072M" );
		if (request_is_post ()) {
			$errors = array ();
			$uploader = new Helper_Uploader ();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage ( '未上传文件', '', url ( 'warehouse/batchtraceimport' ) );
			}
			$file = $uploader->file ( 'file' ); //获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage ( '文件格式不正确：xls、xlsx', '', url ( 'warehouse/batchtraceimport' ) );
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' ); //缓存路径
			$filename = date ( 'YmdHis' ) . 'batchtraceimport.' . $file->extname ();
			$file_route = $des_dir . DS . $filename;
			$file->move ( $file_route );
			$xls = Helper_Excel::readFile ( $file_route, true );
			$sheet = $xls->toHeaderMap ();
			//导入的表中有数据
			//必填字段
			$required_fields = array (
				'阿里订单号',
				'轨迹代码',
				'轨迹时间',
				'地点',
				'时区号',
			);
			$result = array();
			
			if (! empty ( $sheet )) {
				foreach ( $sheet as $k => $row ) {
					
					//判断基础信息不得为空
					foreach ( $required_fields as $field ) {
						if (! isset ( $row [$field] )) {
							return $this->_redirectMessage ( '失败', '模板字段缺失，请检查', url ( "warehouse/batchtraceimport" ) );
						}
						if (! strlen ( $row [$field] )) {
							$result[] = '【' . $field . '】不能为空';
							continue 2;
						}
					}
					
					$order = Order::find ( 'ali_order_no = ?', $row ['阿里订单号'] )->getOne ();
					if ($order->isNewRecord ()) {
						$result[] = $row ['阿里订单号'] . '订单号不存在';
						continue;
					}
					if (strtotime($row ['轨迹时间'])<=0){
						$result[] = $row ['阿里订单号'] . '时间格式有误';
						continue;
					}
					$is_exist = Tracking::find('order_id=? and tracking_code=? and confirm_flag<>2',$order->order_id,$row ['轨迹代码'])->order('tracking_id asc')->getOne();
					if (!$is_exist->isNewRecord()){
						$result[] = $row ['阿里订单号'] . '该轨迹已存在';
						continue;
					}
					$trace_code=array_combine(array_keys(Tracking::$trace_code_cn),array_keys(Tracking::$trace_code_cn));
					$trace_code_cn=Tracking::$trace_code_cn;
					
					$select=Tracking::find('order_id=?',$order->order_id)->order('tracking_id asc')->getAll();
					$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
					
					$trace=new Tracking();
					$trace->changeProps(array(
						'order_id'=>$order->order_id,
						'customer_id'=>$order->customer_id,
						'far_no'=>$order->far_no,
						'tracking_code'=>$row ['轨迹代码'],
						'location'=>$row ['地点'],
						'trace_desc_cn'=>$trace_code_cn[$row ['轨迹代码']],
						'operator_name'=>MyApp::currentUser('staff_name'),
						'timezone'=>$row ['时区号'],
						'quantity'=>$quantity['sum_quantity'],
						'trace_time'=>strtotime($row ['轨迹时间'])
					));
					//EMS-FY并且轨迹代码是F_CARRIER_PICKUP_RT_5035
					if($row ['轨迹代码']=='F_CARRIER_PICKUP_RT_5035'){
						if($order->service_code=='EMS-FY'){
							$trace->changeProps(array(
								'trace_desc_cn'=>'包裹重新安排转运,转【'.$order->tracking_no.'】',
								'trace_desc_en'=>'Reschedule transshipment to EMS['.$order->tracking_no.'].Track in:http://www.ems.com.cn/english.html or https://www.17track.net/en'
							));
						}else {
							$trace->changeProps(array(
								'trace_desc_cn'=>'包裹重新安排转运,转【'.$order->tracking_no.'】',
								'trace_desc_en'=>'Reschedule transshipment'
							));
						}
					}
					$trace->confirm_flag='1';
					$trace->save();
					//修改订单物流轨迹状态1（手动添加数据）
					$order->abnormal_time_flag = 1;
					$order->save();
					//保存日志
					$delierylog = new DeliveryLog();
					$delierylog->order_id = $order->order_id;
					$delierylog->tracking_no = $order->tracking_no;
					$delierylog->tracking_id = $trace->tracking_id;
					$delierylog->staff_id = MyApp::currentUser('staff_id');
					$delierylog->staff_name = MyApp::currentUser('staff_name');
					$delierylog->comment = '轨迹代码：'.$trace->tracking_code.';描述信息：'.$trace->trace_desc_cn.';轨迹时间：'.date('Y-m-d H:i:s',$trace->trace_time);
					$delierylog->save();
					if($row ['轨迹代码']=='S_DELIVERY_SIGNED'){
						$trace->status='1';
						$trace->save();
						$event=new Event();
						if($order->customer->customs_code=='ALPL'){
							$event_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
						}else{
							$event_code = 'DELIVERY';
						}
						$event->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'event_code'=>$event_code,
							'event_time'=>strtotime($row ['轨迹时间']),
							'event_location'=>$row ['地点'],
							'timezone'=>$row ['时区号'],
							'confirm_flag'=>'0',
							'operator'=>MyApp::currentUser('staff_name')
						));
						$event->save();
					}
					$result[] = '成功';
				}
				$this->_view ['result'] = $result;
			} else {
				return $this->_redirectMessage ( '失败', '请修改表格填写内容', url ( "warehouse/batchtraceimport" ) );
			}
		}
	}
	/**
	 * 事件导入
	 */
	function actionEventImport(){
		set_time_limit ( 0 );
		if (request_is_post ()) {
			$errors = array ();
			$uploader = new Helper_Uploader ();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage ( '未上传文件', '', url ( 'warehouse/eventimport' ) );
			}
			$file = $uploader->file ( 'file' ); //获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage ( '文件格式不正确：xls、xlsx', '', url ( 'warehouse/eventimport' ) );
			}
			if(request('event_code')=="LOAD" && !request('sailling_date')){
				return $this->_redirectMessage ( '导入失败', request('event_code').'事件下请填写船期', url ( 'warehouse/eventimport' ),5 );
			}
			
			if(!request('event_code')){
				return $this->_redirectMessage ( '导入失败', '请填写事件代码', url ( 'warehouse/eventimport' ),5 );
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' ); //缓存路径
			$filename = date ( 'YmdHis' ) . 'ExpenseImport.' . $file->extname ();
			$file_route = $des_dir . DS . $filename;
			$file->move ( $file_route );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $file_route, true );
			$sheet = $xls->toHeaderMap ();
			//导入的表中有数据
			//必填字段
			$required_fields = array (
				'阿里单号',
				'事件位置',
				'时区号'
			);
			$result = array();
			if (! empty ( $sheet )) {
				foreach ( $sheet as $k => $row ) {
					//print_r(PHPExcel_Shared_Date::ExcelToPHP($row['船期']));exit;
					//判断基础信息不得为空
					foreach ( $required_fields as $field ) {
						if (! isset ( $row [$field] )) {
							return $this->_redirectMessage ( '失败', '模板字段缺失，请检查', url ( "warehouse/eventimport" ) );
						}
						if (! strlen ( $row [$field] )) {
							$result[] = '【' . $field . '】不能为空';
							continue;
						}
					}
					if($row ['事件时间']){
						if(!strtotime($row ['事件时间'])){
							$result[] = $row ['阿里单号'] . '时间格式不正确，请设置单元格格式为文本类型';
							continue;
						}
					}else{
						if(!request('event_time')){
							$result[] = $row ['阿里单号'] . '时间不能为空';
							continue;
						}
					}
					$order = Order::find ( 'ali_order_no = ?', $row ['阿里单号'] )->getOne ();
					if ($order->isNewRecord ()) {
						$result[] = $row ['阿里单号'] . '订单号不存在';
						continue;
					}
					if($order->service_code !="OCEAN-FY" && in_array(request('event_code'),array('LOAD','SET_SAIL','ARRIVAL_PORT'))){
						$result[] = $row ['阿里单号'] . '事件不属于该产品';
						continue;
					}
					if($order->service_code =="OCEAN-FY"){
						if(request('event_code')=="LOAD"){
							if(!$order->warehouse_out_time){
								$result[] = request('event_code').'事件必须是已出库订单';
								continue;
							}
							if (! strlen ( $row ['柜号'] )||! strlen ( $row ['提单号'] )) {
								$result[] = request('event_code').'事件下柜号、提单号不能为空';
								continue;
							}
						}else if(request('event_code')=="SET_SAIL"){
							$eventc = Event::find ( 'order_id = ? and event_code = ? and send_flag=1', $order->order_id,'LOAD' )->getOne ();
							if($eventc->isNewRecord()){
								$result[] = request('event_code').'事件前必须先LOAD';
								continue;
							}
						}else if (request('event_code')=="ARRIVAL_PORT"){
							$eventc = Event::find ( 'order_id = ? and event_code = ? and send_flag=1', $order->order_id,'SET_SAIL' )->getOne ();
							if($eventc->isNewRecord()){
								$result[] = request('event_code').'事件前必须先SET_SAIL';
								continue;
							}
						}
					}
					/* if($order->order_status !="7" && in_array(request('event_code'),array('LOAD','SET_SAIL','ARRIVAL_PORT'))){
					 $result[] = $row ['阿里单号'] . '事件必须是已出库订单';
					 continue;
					 } */
					
					$event = Event::find ( 'order_id = ? and event_code = ?', $order->order_id,request('event_code') )->getOne ();
					if (! $event->isNewRecord ()) {
						$result[] = request('event_code').'事件已存在';
						continue;
					}
					
					if(request('fail')==1){
						if (! strlen ( $row ['失败原因'] )) {
							$result[] = '失败原因不能为空';
							continue;
						}
						if((request('event_code')=="WAREHOUSE_OUTBOUND"||request('event_code')=="SORTING_CENTER_OUTBOUND_CALLBACK") && (!$order->payment_time || $order->warehouse_out_time > 0)){
							$result[] = request('event_code').'事件必须是已支付未出库订单';
							continue;
						}
						
						
					}
					
					if(request('event_code')=="DELIVERY_TO_FLIGHT" || request('event_code')=="LINEHAUL_HO_AIRLINE_CALLBACK"){
						if (! strlen ( $row ['失败原因'] )) {
							$result[] = request('event_code').'事件失败原因不能为空';
							continue;
						}
						if(!$order->warehouse_out_time || $order->delivery_time){
							$result[] = request('event_code').'事件必须是已出库未签收订单';
							continue;
						}
						
					}
					
					/* if (!PHPExcel_Shared_Date::ExcelToPHP($row ['事件时间'])){
					 $result[] = request('event_code') . '时间格式有误';
					 continue;
					 } */
					$event->order_id = $order->order_id;
					$event->customer_id = $order->customer_id;
					$event->event_code = request('event_code');
					$event->event_time =strtotime($row ['事件时间']) ? strtotime($row ['事件时间']) : strtotime(request('event_time'));
					$event->event_location = $row ['事件位置'];
					$event->timezone = $row ['时区号'];
					$event->confirm_flag = '1';
					$event->reason = $row ['失败原因'];
					
					if($order->service_code =="OCEAN-FY" && request('event_code')=="LOAD"){
						$event->sailling_date = strtotime(request('sailling_date'));
						$event->container_no = $row ['柜号'];
						$event->bill_no = $row ['提单号'];
					}
					$event->operator = MyApp::currentUser('staff_name');
					$event->save ();
					
					if(request('event_code')=="ARRIVAL_PORT" && $order->service_code =="OCEAN-FY" && !$event->isNewRecord()){
						$citytimezone = CityTimezone::find('city=?',$row ['事件位置'])->getOne();
						if($citytimezone->isNewRecord()){
							$country = CityTimezone::find('code_word_two=?',$order->consignee_country_code)->getOne();
							$timezone = $country->timezone;
						}else{
							$timezone = $citytimezone->timezone;
						}
						$datetime = strtotime($row ['事件时间']) ? strtotime($row ['事件时间']) : strtotime(request('event_time'));
						$event_time=strtotime('+60hour',$datetime)+ rand ( 0, 10 ) * 60 + rand ( 0, 10 );
						$pickup_event= new Event();
						if (request('customs_code')=='ALPL'){
							$event_code = 'SORTING_CENTER_HO_OUT_CALLBACK';
						}else{
							$event_code = 'CARRIER_PICKUP';
						}
						$pickup_event->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'event_code'=>$event_code,
							'event_time'=>$event_time,
							'location'=>$row ['事件位置'],
							'event_location'=>$row ['事件位置'],
							'timezone'=>$timezone,
							'confirm_flag'=>'1',
							'operator' => MyApp::currentUser('staff_name')
						));
						$pickup_event->save();
						//承运商取件时间
						$order->carrier_pick_time=$event_time;
					}
					$result[] = '成功';
				}
				$this->_view ['result'] = $result;
			} else {
				return $this->_redirectMessage ( '失败', '请填写修改内容', url ( "warehouse/eventimport" ) );
			}
		}
	}
	function actionEditModal(){
		$totaltrack = TotalTrack::find("total_list_id = ?",request("total_list_id"))->getOne();
		$this->_view ['totaltrack'] = $totaltrack;
	}
	function actionEditSave() {
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		$totaltrack = TotalTrack::find("total_list_id = ?",request("total_list_id"))->getOne();
		$totaltrack->remark = request("remark");
		$totaltrack->save();
		return $this->_redirectAjax ( true, '保存成功' );
	}
	function actionDownloadbatchtraceTemp(){
		return $this->_redirect ( QContext::instance ()->baseDir () . 'public/download/轨迹批量导入模板.xlsx' );
	}
	function actionDownloadTemp(){
		return $this->_redirect ( QContext::instance ()->baseDir () . 'public/download/事件批量导入模板.xlsx' );
	}
	/**
	 * @todo 随货单证核查
	 * @author 许杰晔
	 * @since 2020.6.2
	 * @param $ali_order_no
	 * @return json
	 * @link #80103
	 */
	function actionNewComparison(){
		if (substr(request('total_list_no'),0,2)=='FR'){
			$total = Totallist::find('total_list_no = ?',request('total_list_no'))->getOne();
			if ($total->isNewRecord() ){
				return $this->_redirect('错误','未找到相关单号',url('warehouse/totallist'));
			}
			$orders = Order::find('total_list_no = ?',request('total_list_no'))->setColumns('tracking_no')->getAll();
			
			if(request_is_post ()){
				Totalcheck::meta()->deleteWhere('total_list_no=?',request('total_list_no'));
				//核对成功的
				if(request('id1')){
					$id1=explode("\r\n", request('id1'));
					foreach ($id1 as $d1){
						$totalche= new Totalcheck(
							array(
								'total_list_no'=>request('total_list_no'),
								'tracking_no'=>$d1,
								'state'=>1
							)
							);
						$totalche->save();
					}
				}
				//有单无货
				if(request('id2')){
					$id2=explode("\r\n", request('id2'));
					foreach ($id2 as $d2){
						$totalche= new Totalcheck(
							array(
								'total_list_no'=>request('total_list_no'),
								'tracking_no'=>$d2,
								'state'=>2
							)
							);
						$totalche->save();
					}
				}
				//有货无单
				if(request('id3')){
					$id3=explode("\r\n", request('id3'));
					foreach ($id3 as $d3){
						$totalche= new Totalcheck(
							array(
								'total_list_no'=>request('total_list_no'),
								'tracking_no'=>$d3,
								'state'=>3
							)
							);
						$totalche->save();
					}
				}
				return $this->_redirectMessage ( '保存成功', '成功', url ( '/totallist' ), 3 );
			}
			$this->_view['goods']=Totalcheck::find('state in (1,2) and total_list_no=?',request('total_list_no'))->getAll();
			$this->_view['orders1']=Totalcheck::find('state in (1) and total_list_no=?',request('total_list_no'))->getAll();
			$this->_view['orders2']=Totalcheck::find('state in (2) and total_list_no=?',request('total_list_no'))->getAll();
			$this->_view['orders3']=Totalcheck::find('state in (3) and total_list_no=?',request('total_list_no'))->getAll();
			$this->_view['orders']=$orders;
			
		}else{
			exit('error total_list_no');
		}
		
		
	}
	/**
	 * @todo 库位管理列表
	 * @author 吴开龙
	 * @since 2020.6.3
	 * @param
	 * @return view
	 * @link #80101
	 */
	function actionPosList(){
		//获取当前用户仓位权限信息，进行权限数据判断
		$relevants = RelevantDepartment::find ( "staff_id = ?", MyApp::currentUser('staff_id') )->setColumns ( 'department_id' )
		->asArray ()
		->getAll ();
		$relevants = Helper_Array::getCols ( $relevants, 'department_id' );
		//判断有仓库权限的才显示在列表
		$pos = Pos::find('department_id in (?)', $relevants);
		if(request('warehouse_name')){
			$pos->where('warehouse_name=?',request('warehouse_name'));
		}
		if(request('warehouse_code')){
			$pos->where('warehouse_code=?',request('warehouse_code'));
		}
		if(request('frame_code')){
			$pos->where('frame_code=?',request('frame_code'));
		}
		$page = request_is_post () ? 1 : request ( 'page' );
		$pos=$pos->limitPage ( $page, 30 )
		->fetchPagination ( $this->_view ['pagination'] )
		->order('id desc')->getAll();
		$this->_view['pos']=$pos;
	}
	/**
	 * @todo 库位管理修改添加界面
	 * @author 吴开龙
	 * @since 2020.6.3
	 * @param
	 * @return view
	 * @link #80101
	 */
	function actionPosEdit(){
		//获取当前用户仓位权限信息，进行权限数据判断
		$relevants = RelevantDepartment::find ( "staff_id = ?", MyApp::currentUser('staff_id') )->setColumns ( 'department_id' )
		->asArray ()
		->getAll ();
		$relevants = Helper_Array::getCols ( $relevants, 'department_id' );
		$pos = Pos::find ( "id = ? and department_id in (?)", request ( "id" ), $relevants )->getOne ();
		//添加数据
		if (request_is_post ()) {
			//判断数据重复，仓库代码
			$pos_data = request ( "pos" );
			$warehouse_code = Pos::find ( "warehouse_code = ?", $pos_data['warehouse_code'] );
			//如果有id则是修改数据，判断的时候排除掉自己
			if(request ( "id" )){
				$warehouse_code->where('id != ?',request ( "id" ));
			}
			$warehouse_code = $warehouse_code->getOne();
			if(!$warehouse_code->isNewRecord()){
				return $this->_redirectMessage ( "库位",'保存失败，仓库代码已被使用', url ( 'warehouse/posedit', array (
					'id' => $pos->id,
					"active_tab" => "0"
				) ), 3 );
			}
			//添加/修改数据
			$pos->changeProps ( request ( "pos" ) );
			$pos->save ();
			return $this->_redirectMessage ( "库位", "保存成功", url ( "warehouse/posedit", array (
				"id" => $pos->id,
				"active_tab" => "0"
			) ) );
		}
		$this->_view ["tabs"] = $this->createTabs ( $pos );
		$this->_view ["pos"] = $pos;
		//部门下拉列表,只显示有权限进入的部门
		$dep = array();
		foreach (Department::find ('department_id in (?)', $relevants)->getAll () as $d){
			$dep[$d->department_id] = $d->department_name;
		}
		$this->_view ["dep"] = $dep;
	}
	/**
	 * @todo 库位管理ajax删除
	 * @author 吴开龙
	 * @since 2020.6.3
	 * @param $id 库位表主键
	 * @return view
	 * @link #80101
	 */
	function actionPosDel(){
		$return_data = array(
			'code' => 1,
			'msg' => ''
		);
		$pos = Pos::find ( "id = ?", request ( "id" ) )->getOne ();
		if($pos->isNewRecord()){
			$return_data['code'] = 0;
			$return_data['msg'] = '数据不存在';
			return json_encode($return_data);
		}
		$pos->destroy();
		return json_encode($return_data);
	}
	/**
	 * @todo 库位管理导入功能
	 * @author 吴开龙
	 * @since 2020.6.3
	 * @param
	 * @return view
	 * @link #80101
	 */
	function actionPosImport(){
		//获取当前用户仓位权限信息，进行权限数据判断
		$relevants = RelevantDepartment::find ( "staff_id = ?", MyApp::currentUser('staff_id') )->setColumns ( 'department_id' )
		->asArray ()
		->getAll ();
		$relevants = Helper_Array::getCols ( $relevants, 'department_id' );
		
		$url = url ( "warehouse/poslist");
		//读取数据
		$uploader = new Helper_Uploader ();
		if (Controller_Common::getFileExtName ( $uploader ) != "xls") {
			return $this->_redirectMessage ( "分区导入", "导入失败,文件类型不正确,请选择 .xls 类型的文件", $url, 5 );
		}
		$data = Helper_Excel::readFile ( Controller_Common::readFile ( $uploader ) )->toHeaderMap ();
		//检查内容是否为空或格式是否正确
		if (empty ( $data )) {
			return $this->_redirectMessage ( "批量导入库位", "导入失败,请添加数据", $url, 5 );
		}
		//数据判断
		$warehouse_code = array();
		foreach ($data as $k => $v){
			$line = $k+2;
			//判断必填数据不可为空
			if($v['部门'] == '' || $v['仓库名'] == '' || $v['仓库代码'] == '' || $v['库号'] == '' || $v['架号'] == ''){
				return $this->_redirectMessage ( "批量导入库位", "导入失败,".$line."行必填数据不可为空", $url, 5 );
			}
			//判断部门是否存在或是否有权限
			$dep = Department::find ('department_name=? and department_id in (?)',trim($v['部门']), $relevants)->getOne ();
			if($dep->isNewRecord()){
				return $this->_redirectMessage ( "批量导入库位", "导入失败,".$line."行部门不存在或无访问此部门权限", $url, 5 );
			}
			//判断仓库代码是否重复
			$pos = Pos::find ( "warehouse_code = ?", trim($v['仓库代码']) )->getOne ();
			if(!$pos->isNewRecord()){
				return $this->_redirectMessage ( "批量导入库位", "导入失败,".$line."行仓库代码重复", $url, 5 );
			}
			$warehouse_code[] = trim($v['仓库代码']);
		}
		//判断文件中是否存在仓库代码重复值
		if (count($warehouse_code) != count(array_unique($warehouse_code))) {
			return $this->_redirectMessage ( "批量导入库位", "导入失败,文件中仓库代码有重复", $url, 5 );
		}
		//保存数据
		$conn = QDB::getConn ();
		$conn->startTrans ();
		foreach ($data as $k => $v){
			//获取部门id
			$dep = Department::find ('department_name=?',trim($v['部门']))->getOne ();
			$pos = new Pos();
			$pos->department_id = $dep->department_id;
			$pos->warehouse_name = trim($v['仓库名']);
			$pos->warehouse_code = trim($v['仓库代码']);
			$pos->warehouse_no = trim($v['库号']);
			$pos->area_code = trim($v['区号']);
			$pos->frame_code = trim($v['架号']);
			$pos->floor_code = trim($v['层号']);
			$pos->tag_code = trim($v['位号']);
			$pos->note = trim($v['备注']);
			$pos->save();
		}
		$conn->completeTrans ();
		
		return $this->_redirectMessage ( "分区导入", "导入成功", $url, 3 );
	}
	/**
	 * 创建标签
	 */
	function createTabs($pos) {
		return array (
			array (
				"id" => "0",
				"title" => "基本信息",
				"href" => ""
			)
		);
	}
	/**
	 * @todo 货件定位扫描
	 * @author 吴开龙
	 * @since 2020.6.29
	 * @param
	 * @return view
	 * @link #80105
	 */
	function actionPosScan(){
		if (request_is_post ()) {
			$data = array(
				'code' => 0,
				'msg' => ''
			);
			//扫描库位
			if(request('type') == 1){
				$pos = Pos::find('warehouse_code=?',request('warehouse_code'))->getOne();
				if($pos->isNewRecord()){
					$data['msg'] = '库位不存在';
					return json_encode($data);
				}
				$data['code'] = 1;
				$data['msg'] = '成功';
				return json_encode($data);
			}
			//扫描单号
			if(request('type') == 2){
				$order = Order::find('ali_order_no=? or tracking_no=? or reference_no=?',request('reference_no'),request('reference_no'),request('reference_no'))->getOne();
				if($order->isNewRecord()){
					$data['msg'] = '订单不存在';
					return json_encode($data);
				}
				$data['code'] = 1;
				$data['msg'] = '成功';
				return json_encode($data);
			}
			//保存
			if(request('type') == 3){
				$json = json_decode(request('jsonstr'),true);
				if(!count($json['success-list'])){
					$data['msg'] = '数据为空';
					return json_encode($data);
				}
				foreach($json['success-list'] as $v){
					$order = Order::find('ali_order_no=? or tracking_no=? or reference_no=?',$v['reference_no'],$v['reference_no'],$v['reference_no'])->getOne();
					$posscan = new PosScan();
					$posscan->warehouse_code = $json['warehouse_code'];
					$posscan->order_id = $order->order_id;
					$posscan->save();
				}
				$data['code'] = 1;
				$data['msg'] = '成功';
				return json_encode($data);
			}
		}
	}
	/**
	 * @todo 随货单证核查
	 * @author 吴开龙
	 * @since 2020-6-15 10:32:54
	 * @return view
	 * @link #80271
	 */
	function actionGoodsCheck(){
		$dpms = Department::find()->getAll()->toHashMap('department_id','department_name');
		$staffrole=StaffRole::find('staff_id = ? and role_id in (?)',MyApp::currentUser('staff_id'),array("1","7"))->getOne();
		if($staffrole->isNewRecord()){
			$goods_check = GoodsCheck::find('department_id = ?',MyApp::currentUser('department_id'));
		}else {
			$goods_check = GoodsCheck::find();
		}
		//创建时间
		if(request('start_date',date('Y-m-d'))){
			$goods_check->where('create_time >= ?',strtotime(request('start_date',date('Y-m-d')).'00:00:00'));
		}
		if(request('end_date')){
			$goods_check->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//总单单号
		if(request('total_list_no')){
			$total_list_no=explode("\r\n", request('total_list_no'));
			$total_list_no=array_filter($total_list_no);//去空
			$total_list_no=array_unique($total_list_no);//去重
			$goods_check->where('goods_check_no in (?)',$total_list_no);
		}
		
		
		$pagination = null;
		$goods_check=$goods_check->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		$this->_view['goods_check']=$goods_check;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * @todo 随货单证核查扫描页面1
	 * @author 吴开龙
	 * @since 2020-6-15 10:32:54
	 * @return tracking_no1 不存在单号 tracking_no2 一票多件未扫全 tracking_no3 成功
	 * @link #80271
	 */
	function actionGoodsCheckEdit(){
		if(request_is_post()){
			$sub_code = explode("\n", request('sub_code'));
			$sub_code = array_filter($sub_code);
			$tra = array();
			foreach ($sub_code as $k => $s){
				//去空格 为空 跳过
				$s = trim($s);
				if(!$s){
					continue;
				}
				//获取主单号
				$tb_sub_code = Subcode::find('sub_code = ?', $s)->getOne();
				$order = Order::find('order_id = ?',$tb_sub_code->order_id)->getOne();
				//保存主单号
				$tra[] = $order->tracking_no;
			}
			$tra = array_unique($tra);
			$ch_group = Channelgroup::find('channel_group_name=?',request('account'))->getOne();
			$check = new GoodsCheck();
			$rand = mt_rand(1000,9999);
			$goods_check_no='SH'.date('YmdHis').$rand;
			$check->goods_check_no = $goods_check_no;
			$check->channel_group_id = $ch_group->channel_group_id;
			$check->department_id = MyApp::currentUser('department_id');
			$check->operation_name = MyApp::currentUser('staff_name');
			$check->save();
			foreach ($tra as $v){
				$check_item = GoodsCheckItem::find('tracking_no=?',$v)->getOne();
				if($check_item->isNewRecord()){
					$check_item->goods_check_id = $check->goods_check_id;
					$check_item->tracking_no = $v;
					$check_item->save();
				}
			}
			return $this->_redirectMessage('保存成功', '', url('warehouse/goodscheck'));
		}
	}
	/**
	 * @todo 随货单证核查扫描页面ajax返回
	 * @author 吴开龙
	 * @since 2020-6-15 10:32:54
	 * @return tracking_no1 不存在单号 tracking_no2 一票多件未扫全 tracking_no3 成功
	 * @link #80271
	 */
	function actionGoodsCheckEditAjax(){
		$sub_code = explode("\n", request('sub_code'));
		$sub_code = array_filter($sub_code);
		//取出渠道id array
		$ch_group = Channelgroup::find('channel_group_name=?',request('account'))->getOne();
		$ch = Channel::find('channel_group_id=?',$ch_group->channel_group_id)->getAll();
		foreach ($ch as $c){
			$account[] = $c->channel_id;
		}
		$data = array(
			'tracking_no1' => array(),
			'tracking_no2' => array(),
			'tracking_no3' => array()
		);
		$tra = array();
		foreach ($sub_code as $k => $s){
			//去空格 为空 跳过
			$s = trim($s);
			if(!$s){
				continue;
			}
			//末端单号第一位是B,去掉
			// 			$s = ltrim($s, "B");
			//最后一位是D,去掉
			// 			$s = rtrim($s, "D");
			$tb_sub_code = Subcode::find('sub_code = ?', $s)->getOne();
			//包裹号不存在
			if($tb_sub_code->isNewRecord()){
				$data['tracking_no1'][$k]['code'] = $s;
				$data['tracking_no1'][$k]['msg'] = '包裹不存在';
				continue;
			}
			//判断单号不存在的
			$order = Order::find('order_id = ?',$tb_sub_code->order_id)->getOne();
			if($order->isNewRecord()){
				$data['tracking_no1'][$k]['code'] = $s;
				$data['tracking_no1'][$k]['msg'] = '主单不存在';
				continue;
			}
			//保存主单号
			$tra[] = $order->tracking_no;
			//判断是否存在已扫描过
			$c_item = GoodsCheckItem::find('tracking_no=?',$order->tracking_no);
			//     			if(request('goods_check_id')){
			//     				$c_item->where('goods_check_id != ?', request('goods_check_id'));
			//     			}
			$c_item = $c_item->getOne();
			if(!$c_item->isNewRecord()){
				$data['tracking_no1'][$k]['code'] = $s;
				$data['tracking_no1'][$k]['msg'] = '主单已被扫描';
				continue;
			}
			//判断渠道
			if(!in_array($order->channel_id, $account)){
				$ch1 = Channel::find('channel_id=?',$order->channel_id)->getOne();
				$data['tracking_no1'][$k]['code'] = $s;
				$data['tracking_no1'][$k]['msg'] = '渠道不正确('.$ch1->channel_name.')';
				continue;
			}
			$sub_code_count = Subcode::find('order_id = ?', $tb_sub_code->order_id)->getAll();
			//成功
			if(count($sub_code_count) == 1){
				$data['tracking_no3'][$k]['code'] = $s;
			}else if(count($sub_code_count) > 1){
				//判断一票多件
				$type = 1;
				foreach ($sub_code_count as $code){
					if(!in_array($code->sub_code, $sub_code)){
						$type = 0;
					}
				}
				//0：未全部匹配 1：全部匹配
				if($type){
					$data['tracking_no3'][$k]['code'] = $s;
				}else{
					$data['tracking_no2'][$k]['code'] = $s;
					$data['tracking_no2'][$k]['msg'] = '子单号不全';
					continue;
				}
			}
			if($order->declaration_type=='DL'){
				//报关状态
				$data['tracking_no3'][$k]['drs'] = '1';
			}
		}
		return json_encode($data);
	}
	/**
	 * @todo 随货单证核查验证 扫描单证页面
	 * @author 吴开龙
	 * @since 2020-6-16 10:32:54
	 * @param sub_code 单号 换行分割
	 * @return tracking_no1 不存在单号 tracking_no2 一票多件未扫全 tracking_no3 成功
	 * @link #80271
	 */
	function actionGoodsCheckEdit2(){
		$check = GoodsCheck::find('goods_check_id=?',request('goods_check_id'))->getOne();
		$c_item = GoodsCheckItem::find('goods_check_id=?',$check->goods_check_id)->getAll();
		if(request_is_post()){
			$id1 = explode("\n", request('id1'));
			$id1 = array_filter($id1);
			//     		$id2 = explode("\n", request('id2'));
			//     		$id2 = array_filter($id2);
			$id3 = explode("\n", request('id3'));
			$id3 = array_filter($id3);
			//有货无单
			if(count($id1)){
				foreach ($id1 as $i1){
					$i1 = trim($i1);
					$check_item = GoodsCheckItem::find('tracking_no=?',$i1)->getOne();
					$check_item->status = 2;
					$check_item->save();
				}
			}
			//成功
			if(count($id3)){
				foreach ($id3 as $i3){
					$i3 = trim($i3);
					$check_item = GoodsCheckItem::find('tracking_no=?',$i3)->getOne();
					$check_item->status = 3;
					$check_item->save();
				}
			}
			return $this->_redirectMessage ( '保存成功', '成功', url ( '/goodscheck' ), 3 );
		}
		$id1 = array();
		$id2 = array();
		$id3 = array();
		$id4 = array();
		foreach ($c_item as $item){
			if($item->status == 1){
				$id4[] = $item->tracking_no;
			}
			if($item->status == 2){
				$id1[] = $item->tracking_no;
				$id4[] = $item->tracking_no;
			}
			if($item->status == 3){
				$id3[] = $item->tracking_no;
				$id4[] = $item->tracking_no;
			}
		}
		$this->_view['check']=$check;
		$this->_view['c_item']=$c_item;
		$this->_view['id1']=$id1;
		$this->_view['id3']=$id3;
		$this->_view['id4']=$id4;
	}
	/**
	 * @todo 随货单证核查验证 完成
	 * @author 吴开龙
	 * @since 2020-6-16 10:32:54
	 * @param
	 * @return fool
	 * @link #80271
	 */
	function actionfinished2(){
		$goods_check = GoodsCheck::find('goods_check_id = ?',request('goods_check_id'))->getOne();
		if(!$goods_check->isNewRecord()){
			$goods_check->status = '1';
			$goods_check->save();
			return $this->_redirect(url('warehouse/goodscheck'));
		}else{
			return $this->_redirectMessage('数据缺失', '', url('warehouse/goodscheck'));
		}
	}
	/**
	 * @todo 随货单核查明细页面
	 * @author 吴开龙
	 * @since 2020-6-19 10:32:54
	 * @param
	 * @return
	 * @link #80271
	 */
	function actionGoodsItemList(){
		$goods_check = GoodsCheckItem::find('goods_check_id = ?',request('goods_check_id'))->getAll();
		$goods_check_arr = array();
		foreach ($goods_check as $v){
			$goods_check_arr[] = $v->tracking_no;
		}
		$orders = Order::find('tracking_no in (?)',$goods_check_arr);
		//主单单号
		if(request('tracking_no')){
			$tracking_no=explode("\r\n", request('tracking_no'));
			$tracking_no=array_filter($tracking_no);//去空
			$tracking_no=array_unique($tracking_no);//去重
			$orders->where('tracking_no in (?)',$tracking_no);
		}
		//阿里单号
		if(request('ali_order_no')){
			$ali_order_no=explode("\r\n", request('ali_order_no'));
			$ali_order_no=array_filter($ali_order_no);//去空
			$ali_order_no=array_unique($ali_order_no);//去重
			$orders->where('ali_order_no in (?)',$ali_order_no);
		}
		$pagination = null;
		$list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * @todo   无主件扫描
	 * @author 吴开龙
	 * @since  2020-7-17 10:32:54
	 * @param
	 * @return
	 * @link   #81227
	 */
	function actionNoIdScan(){
		if(request_is_post()){
			$order = Order::find('reference_no=?',request('reference_no'))->getOne();
			if($order->isNewRecord()){
				$data = 0;
			}else{
				$data = 1;
			}
			echo $data;
			exit;
		}
	}
	/**
	 * @todo   PDF文件是否存在
	 * @author stt
	 * @since  2020-09-29
	 * @param
	 * @return
	 * @link   #81897
	 */
	function actionpdfisexist(){
		if(request_is_post()){
			$data = Helper_PDF::pdfisexist(request('filename'));
			echo json_encode($data);
			exit;
		}
	}
	/**
	 * @todo   同时判断面单和发票PDF文件是否存在
	 * @author stt
	 * @since  2020年12月18日11:42:12
	 * @param
	 * @return
	 * @link   #81897
	 */
	function actionallpdfisexist(){
		if(request_is_post()){
			//面单
			$miandandata = Helper_PDF::pdfisexist(request('filename').'.pdf');
			//发票
			$fapiaodata = Helper_PDF::pdfisexist(request('filename').'_others.pdf');
			//面单url
			$return['miandanurl'] = $miandandata['url'];
			//发票url
			$return['url'] = $fapiaodata['url'];
			echo json_encode($return);
			exit;
		}
	}
	
	function actionOverTimeTable(){
		$time_config = Config::find('k=?','OverTimeTable')->getOne();
		//如果没有列则添加
		if($time_config->isNewRecord()){
			$time_config->k = 'OverTimeTable';
			$time_config->save();
		}
		if(request('do') == 'shezhi'){
			$time_config->v = request('time_config');
			$time_config->save();
		}
		$this->_view['time_config']=$time_config;
		//搜索
		$orders = Order::find('order_status in (?)',array('4','5','10'));
		//判断设置存在 条件执行
		if($time_config->v){
			//天数转化为时间戳
			$time = $time_config->v*24*60*60;
			$orders->where(' warehouse_in_time + ? < ?',$time,time());
		}
		if(request('time')){
			//天数转化为时间戳
			$time = request('time')*24*60*60;
			$orders->where(' warehouse_in_time + ? < ?',$time,time());
		}
		//如果不存在设置就不查询
		if(!$time_config->v){
			$orders->where('1=2');
		}
		$pagination = null;
		$list=$orders->limitPage(request('page',1),request( 'page_size', 25 ))
		->order('order_id')
		->fetchPagination($pagination)
		->getAll();
		$this->_view['pagination']=$pagination;
		$this->_view['list']=$list;
	}
}