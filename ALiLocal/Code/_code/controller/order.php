<?php

class Controller_Order extends Controller_Abstract{
	/**
	 * 订单管理页面
	 */
	function actionSearch(){
		$orders=Order::find("ali_testing_order!= '1' and department_id in (?)",RelevantDepartment::relateddepartmentids())
		->joinLeft('tb_channel', 'supplier_id' ,'tb_channel.channel_id=tb_order.channel_id');
		
		//订单日期
		if(request('timetype')=='1'){
			if(request("start_date")){
				$orders->where("tb_order.create_time >=?",strtotime(request("start_date").':00'));
			}
			if (request("end_date")){
				$orders->where("tb_order.create_time <=?",strtotime(request("end_date").':59'));
			}
		}elseif (request('timetype')=='2'){
			if(request("start_date")){
				$orders->where("payment_time >=?",strtotime(request("start_date").':00'));
			}
			if (request("end_date")){
				$orders->where("payment_time <=?",strtotime(request("end_date").':59'));
			}
		}elseif (request('timetype')=='3'){
			if(request("start_date")){
				$orders->where("far_warehouse_in_time >=?",strtotime(request("start_date").':00'));
			}
			if (request("end_date")){
				$orders->where("far_warehouse_in_time <=?",strtotime(request("end_date").':59'));
			}
		}elseif (request('timetype')=='4'){
			if(request("start_date")){
				$orders->where("warehouse_out_time >=?",strtotime(request("start_date").':00'));
			}
			if (request("end_date")){
				$orders->where("warehouse_out_time <=?",strtotime(request("end_date").':59'));
			}
		}
		if(request('search_flag')=='0'){
			if(request('ordertype')==1){
				if(request('order_no')){
					$orders->where('ali_order_no=? or far_no=? or tracking_no=? or total_list_no=? or order_no=?',request("order_no"),request("order_no"),request("order_no"),request("order_no"),request("order_no"));
				}
			}elseif(request('ordertype')==3){
				//已退回支付原末端运单号
				if(request('order_no')){
					$return_paid=ReturnPaidTrackingno::find('old_tracking_no=?',request('order_no'))->getOne();
					//order_id
					$orders->where('order_id=?',$return_paid->order_id);
				}
			}else{
				$suborder=Subcode::find('sub_code=?',request('order_no'))->getOne();
				$orders->where('order_id=?',$suborder->order_id);
			}
			if(request('reference_no')){
				$orders->where('reference_no like ?','%'.request('reference_no').'%');
			}
			if(request('consignee_country_code')){
				$orders->where('consignee_country_code=?',request("consignee_country_code"));
			}
		}else{
			//渠道多选
			if(request('channel_id')){
				$channel_id = explode(',', request('channel_id'));
				$this->_view ['channel_id'] = $channel_id;
				$orders->where("tb_channel.channel_id in (?)",$channel_id);
			}
			if(request('need_pick_up')){
				$orders->where('need_pick_up="1"');
			}
			if(request('negative_profit')){
				$orders->where('profit<"0"');
			}
			//产品多选
			if(request('service_code')){
				$service_code = explode(',', request('service_code'));
				$this->_view ['service_code'] = $service_code;
				$orders->where('tb_order.service_code in (?) ',$service_code);
			}
			if(request('weight_cost_out_start')){
				$orders->where('(order_status in (1,2,14,15,16) and weight_income_ali>=?) or (order_status in (3,4,5,6,7,8,9,10,11,12,13) and weight_income_in>=?) ',request('weight_cost_out_start'),request('weight_cost_out_start'));
			}
			if(request('weight_cost_out_end')){
				$orders->where('(order_status in (1,2,14,15,16) and weight_income_ali<=?) or (order_status in (3,4,5,6,7,8,9,10,11,12,13) and weight_income_in<=?) ',request('weight_cost_out_end'),request('weight_cost_out_end'));
			}
			if(request('packing_type')){
				$orders->where('packing_type=?',request('packing_type'));
			}
			if(request('network_code')){
				$orders->where('tb_channel.network_code=?',request('network_code'));
			}
			if(request('declaration_type')){
				$orders->where('declaration_type=?',request('declaration_type'));
			}
			if(request('sender')){
				$orders->where('sender_name1 like "%'.request('sender').'%" or sender_name2 like "%'.request('sender').'%" or sender_mobile like "%'.request('sender').'%" or sender_telephone like "%'.request('sender').'%" or sender_email like "%'.request('sender').'%"');
			}
			//部门多选
			if(request('department_id')){
				$department_id= explode(',', request('department_id'));
				$this->_view ['department_id'] = $department_id;
				$orders->where('department_id in(?)',$department_id);
			}
			//入库部门
			if(request('warehouse_in_department_id')){
				$orders->where('warehouse_in_department_id = ? ',request('warehouse_in_department_id'));
			}
			//是否带电
			if(request('has_battery')){
				$orders->where('tb_order.has_battery = ? ',request('has_battery'));
			}
			//客户多选
			if(request('customer_id1')){
				$checked = explode(',', request('customer_id1'));
				$this->_view ['checked'] = $checked;
				$orders->where('tb_order.customer_id in (?) ',$checked);
			}
			//多运单号搜索
			$waybill_back=array();
			if(request('waybill_codes')){				
				$waybill_codes=explode("\r\n", request('waybill_codes'));
				//去除数组中的空格
				foreach ($waybill_codes as $k=>$code){
					$waybill_codes[$k]=ltrim($code);
				}
				$waybill_codes=array_filter($waybill_codes);//去空				
				$waybill_codes=array_unique($waybill_codes);//去重
				//#83709子单号批量搜索
				$suborder_ids = Helper_Array::getCols ( Subcode::find ( "sub_code in (?)", $waybill_codes )->getAll (), "order_id" );
				//根据子单号搜索到订单
				//增加国内单号搜索
				if (! empty ( $suborder_ids )) {
					$orders->where('order_id in (?) or ali_order_no in (?) or far_no in (?) or tracking_no in (?) or total_list_no in (?) or order_no in (?) or reference_no in (?)',$suborder_ids,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes);
				}else{
					//原来的搜索
					$orders->where('ali_order_no in (?) or far_no in (?) or tracking_no in (?) or total_list_no in (?) or order_no in (?) or reference_no in (?)',$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes);
				}
			}
			if(request('supplier_id')){
				$orders->where('tb_channel.supplier_id=?',request('supplier_id'));
			}
			if(request('consignee_country_code1')){
				$orders->where('tb_order.consignee_country_code=?',request('consignee_country_code1'));
			}
		}
		
		$counts = array ();
		
		// 全部总数		
		/* $order_count=clone $orders;
		$counts [0] = $order_count->getCount (); */
		$order_count=clone $orders;
		$counts=$order_count->group('order_status')->count()->columns('order_status')->asArray()->getAll();		
		$counts=Helper_Array::toHashmap($counts,'order_status','row_count');
		
		$counts[0]=0;
		foreach ($counts as $v){
			$counts[0]+=$v;
		}
		$active_id = 0;
		// 未入库
		if (request ( "parameters" ) == "no_package") {
			$orders->where('order_status in (1,14,15,16)');
			$active_id = 1;
		}
		// 已取消
		if (request ( "parameters" ) == "cancel") {
			$orders->where('order_status=2');
			$active_id = 2;
		}
		// 已退货
		if (request ( "parameters" ) == "returned") {
			$orders->where('order_status=3');
			$active_id = 3;
		}
		// 已支付
		if (request ( "parameters" ) == "paid") {
			$orders->where('order_status=4');
			$active_id = 4;
		}
		// 已入库
		if (request ( "parameters" ) == "warehouse_in") {
			$orders->where('order_status=5');
			$active_id = 5;
		}
		// 已打印
		if (request ( "parameters" ) == "warehouse_out") {
			$orders->where('order_status=6');
			$active_id = 6;
		}
		// 已出库
		if (request ( "parameters" ) == "wait_to_send") {
			$orders->where('order_status=7');
			$active_id = 7;
		}
		// 已提取
		if (request ( "parameters" ) == "sent") {
			$orders->where('order_status=8');
			$active_id = 8;
		}
		// 已签收
		if (request ( "parameters" ) == "sign") {
			$orders->where('order_status=9');
			$active_id = 9;
		}
		// 已核查
		if (request ( "parameters" ) == "prove") {
			$orders->where('order_status=10');
			$active_id = 10;
		}
		// 待退货
		if (request ( "parameters" ) == "wait_to_return") {
			$orders->where('order_status=11');
			$active_id = 11;
		}
		// 已扣件
		if (request ( "parameters" ) == "hold") {
			$orders->where('order_status=12');
			$active_id = 12;
		}
		// 已结束
		if (request ( "parameters" ) == "termination") {
			$orders->where('order_status=13');
			$active_id = 13;
		}
		// 其他
		if (request ( "parameters" ) == "other") {
			$orders->where('order_status=17');
			$active_id = 17;
		}
		
		//导出所有数据
		if(request("export")=='exportlist'){
			ini_set('max_execution_time', '0');
			//内存不设限制
			ini_set('memory_limit', '-1');
			set_time_limit(0);
			$list=clone $orders;
			$lists=$list->getAll();
			//获取登录用户的角色权限
			$role_user = StaffRole::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll();
			//创建一个excel空文件，文件名 应付统计
			Helper_ExcelX::startWriter ( 'order_list'  );
			$role_id = array();
			foreach ($role_user as $ru){
				$role_id[] = $ru->role_id;
			}
			$header = array (
				'状态',
				'部门',
				'订单时间',
				'销售产品',
				'阿里订单号',
				'泛远单号',
				'末端运单号',
				'总单单号',
				'应收偏远',
				'问题',
				'目的国',
				'包裹类型',
				'件数',
				'收入',
				'成本',
				'毛利',
				'毛利率',
				'客重',
				'收货实重',
				'收货体积重',
				'收货计费重',
				'预报实重',
				'网络代码',
				'出货渠道',
				
				'发件抬头',
				'出货实重',
				'账单重量',
				'出货体积重',
				'出货计费重',
				'入库时间',
				'入库人',
				'核查时间',
				'核查人',
				'支付日期',
				'出库时间',
				'出库人',
				'长',
				'宽',
				'高',
				
				'交货核查时间',
				'启程扫描时间',
				'抵达扫描时间',
				'抵达仓',
				'提取时间',
				
				
				'预派时间',
				'签收时间',
				'妥投天数',
				'报关',
				'申报总价值',
				'发件公司',
				'发件人',
				'发件人电话',
				'发件人邮箱',
				'发件地址',
				'发件人邮编',
				'发件备注',
				'收件公司',
				'税号',
				'收件人',
				'收件人电话',
				'收件人邮箱 ',
				'收件人城市',
				'收件人省/州',
				'收件人邮编',
				'收件人地址1',
				'收件人地址2',
				'收件完整地址',
				'最新轨迹',
				'地点',
				'最新轨迹时间',
				'上门取件',
				
				'取件网点',
				'取件员',
				'联系电话',
				
				'国内快递单号',
				'订单备注',
				'是否带电',
				'带电产品数量',
				'库位',
				'扫描人',
				'扫描时间'
			);
			//判断权限
			if(in_array('2', $role_id) || in_array('3', $role_id) || in_array('4', $role_id)){
				$i = 1;
				while ($i <= 15){
					$header[] = '品名'.$i;
					$header[] = '英文名'.$i;
					$header[] = '海关编码'.$i;
					$header[] = '申报单价'.$i;
					$header[] = '申报数量'.$i;
					$i++;
				}
			}
			if(!in_array('3', $role_id) && !in_array('4', $role_id)){
				unset($header[13]);
				unset($header[14]);
				unset($header[15]);
				unset($header[16]);
				if(!in_array('2', $role_id)){
					$i2 = 49;
					while ($i2 <= 66){
						unset($header[$i2]);
						$i2 ++;
					}
				}
			}
			//写入表头 内容为$header,addRow为写入内容
			Helper_ExcelX::addRow ($header);
			// 			$sheet = array (
			// 				$header
			// 			);
			
			foreach ($lists as $value){
				//保存订单信息
				$tmp_order[] = $value;
				//保存订单order_id数组
				$order_ids[] = (int)$value->order_id;
				//保存ali_order_no的数组
				$ali_order_no[] = $value->ali_order_no;
				//保存tracking_no的数组
				$tracking_no[] = $value->tracking_no;
				//每10000条数据执行一次添加
				if (count ( $tmp_order ) == '10000') {
					//写入数据的函数封装
					$this->orderExport($tmp_order,$order_ids,$ali_order_no,$tracking_no,$role_id);
					//重置数组以便循环插入时不重复
					$tmp_order = array ();
					$order_ids = array();
					$ali_order_no = array();
					$tracking_no = array();
				}
				
			}
			//最后不足一万条且还有数据的时候执行一次函数
			if (count ( $tmp_order )) {
				$this->orderExport($tmp_order,$order_ids,$ali_order_no,$tracking_no,$role_id);
			}
			//写入结束
			Helper_ExcelX::closeWriter ();
			exit ();
			// 			Helper_ExcelX::array2xlsx ( $sheet, '订单列表' );
			// 			exit ();
		}
		//导出取件清单
		if(request("export")=='exportpick'){
			$pick=clone $orders;
			$pick->where("ifnull(need_pick_up,'')='1'");
			$payeds=$pick->getAll();
			$header = array (
				'订单日期','省','城市','地址','邮编','姓名','手机','固定电话','邮箱','阿里订单号','件数'
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
					Helper_Util::strDate('Y-m-d H:i', $p->create_time),$p->sender_state_region_code,$p->sender_city,$p->sender_street1.' '.$p->sender_street2,"'".$p->sender_postal_code,
					$p->sender_name1.' '.$p->sender_name2,"'".$p->sender_mobile,"'".$p->sender_telephone,$p->sender_email,"'".$p->ali_order_no,$item_count
				);
			}
			Helper_ExcelX::array2xlsx ( $sheet, '取件清单' );
			exit ();
			
		}
		$pagination = null;
		$list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('customer_id asc,order_id desc')->getAll();
		$parameters=request ( "parameters" );
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
		$this->_view ["counts"] = $counts;
		$this->_view ["parameters"] = $parameters;
		$this->_view ["active_id"] = $active_id;
		$this->_view ["tabs"] = $this->createTabs ( $counts );
		$this->_view['dpms']= Department::find()->getAll()->toHashMap('department_id','department_name');
	}
	/**
	 * 订单轨迹
	 */
	function actionTrace(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		
		$trace_code=array_combine(array_keys(Tracking::$trace_code_cn),array_keys(Tracking::$trace_code_cn));
		$trace_code_cn=Tracking::$trace_code_cn;
		
		$select=Tracking::find('order_id=?',request('order_id'))->order('tracking_id asc')->getAll();
		$trace_info_code=Helper_Array::getCols($select, 'tracking_code');
		foreach ($trace_info_code as $v){
			//             unset($trace_code[$v]);
		}
		$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
		if(request_is_post()){
			$trace=new Tracking();
			$trace->changeProps(array(
				'order_id'=>request('order_id'),
				'customer_id'=>$order->customer_id,
				'far_no'=>$order->far_no,
				'tracking_code'=>request('tracking_code'),
				'location'=>request('location'),
				'trace_desc_cn'=>$trace_code_cn[request('tracking_code')],
				'operator_name'=>MyApp::currentUser('staff_name'),
				'timezone'=>request('timezone'),
				'quantity'=>$quantity['sum_quantity'],
				'trace_time'=>strtotime(request('trace_time'))
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
				//事件加客户
				$event=new Event();
				//事件加客户
				$event->changeProps(array(
					'order_id'=>request('order_id'),
					'customer_id'=>$order->customer_id,
					'event_code'=>'DELIVERY',
					'event_time'=>strtotime(request('trace_time')),
					'event_location'=>request('location'),
					'timezone'=>request('timezone'),
					'confirm_flag'=>'0'
				));
				$event->save();
				//订单状态转为签收的规则为：订单的签收事件有创建且发送结果为成功时，才转移#83287
// 				$order->order_status='9';
// 				$order->save();
			}
			return $this->_redirectMessage('新增轨迹', '成功', url('order/trace',array('order_id'=>request('order_id'))));
		}
		$this->_view['order']=$order;
		$this->_view['trace_code']=$trace_code;
		$this->_view['list']=$select;
		$routes=Route::find('tracking_no =?',$order->tracking_no)->order('id desc')->getAll();
		$logs=OrderLog::find('comment LIKE "%timezone%" and comment LIKE "%context%" and comment LIKE "%time%" and comment LIKE "%location%" and order_id=?',$order->order_id)->order('id desc')->getAll();
		$this->_view['routes']=$routes;
		$this->_view['logs']=$logs;
	}
	/**
	 * @todo   导出添加明细
	 * @author 吴开龙
	 * @since  2020-10-13
	 * @link   #82922
	 */
	function orderExport($lists,$order_ids,$ali_order_no,$tracking_no,$role_id){
		//订单状态
		$status=array('1'=>'未入库','2'=>'已取消','3'=>'已退货','4'=>'已支付','5'=>'已入库','6'=>'已打印','7'=>'已出库','8'=>'已提取','9'=>'已签收','10'=>'已查验','11'=>'待退货','12'=>'已扣件','13'=>'已结束','14'=>'已分派','15'=>'已取件','16'=>'已网点已入库');
		//部门表数据
		$department = Department::find ()->asArray()->getAll ();
		//部门表数据转为HashMap形式
		$department = Helper_Array::toHashmap ( $department, 'department_id','department_name' );
		//产品表数据
		$pr=Product::find ()->asArray()->getAll ();
		//产品表数据转为HashMap形式
		$pr = Helper_Array::toHashmap ( $pr, 'product_name' );
		//渠道陈本表数据
		$ch1=ChannelCost::find()->asArray()->getAll();
		//渠道成本表数据转为HashMap形式
		$ch1 = Helper_Array::toHashmap ( $ch1, 'channel_cost_id' );
		foreach ($ch1 as $ch2){
			$ch[$ch2['product_id'].$ch2['channel_id']] = $ch2;
		}
		//渠道表数据
		$channel=Channel::find()->asArray()->getAll();
		//渠道表数据转为HashMap形式
		$channel = Helper_Array::toHashmap ( $channel, 'channel_id' );
		//发件人表数据
		$sender=Sender::find()->asArray()->getAll();
		//发件人表数据转为HashMap形式
		$sender = Helper_Array::toHashmap ( $sender, 'sender_id' );
		//取件员表数据
		$pickup = PickUpMember::find()->asArray()->getAll();
		//取件员表数据转为HashMap形式
		$pickup = Helper_Array::toHashmap ( $pickup, 'id' );
		//发件人备注表数据
		$send_context = Contact::find()->asArray()->getAll();
		//发件人备注表数据转为HashMap形式
		$send_context = Helper_Array::toHashmap ( $send_context, 'sender_company' );
		//部门表数据
		$in_department_id = Department::find()->asArray()->getAll();
		//部门表数据转为HashMap形式
		$in_department_id = Helper_Array::toHashmap ( $in_department_id, 'department_id' );
		//轨迹表数据
		$route=Route::find('tracking_no in (?)',$tracking_no)->asArray()->getAll();
		//轨迹表数据转为HashMap形式
		$route = Helper_Array::toHashmap ( $route, 'tracking_no' );
		
		//启程总单表数据
		$total_out = Totalorderout::find('ali_order_no in (?)',$ali_order_no)->asArray()->getAll();
		//启程总单表数据转为HashMap形式
		$total_out = Helper_Array::toHashmap ( $total_out, 'ali_order_no' );
		//抵达总单表数据
		$total_in1 = Totalorderin::find('ali_order_no in (?)',$ali_order_no)->asArray()->getAll();
		//库位信息表数据
		$posscan = PosScan::find('order_id in (?)',$order_ids)->asArray()->getAll();
		//库位信息表数据转为HashMap形式
		$posscan = Helper_Array::toHashmap ( $posscan, 'order_id' );
		//阿里包裹原始信息表数据
		//$item_count = Orderpackage::find('order_id = ?',$value->order_id)->getSum('quantity');
		$item_count2 = Orderpackage::find('order_id in (?)',$order_ids)->asArray()->getAll();
		//阿里包裹原始信息表数据转为HashMap形式
		$item_count2 = Helper_Array::toHashmap ( $item_count2, 'order_id','quantity' );
// 		echo "<pre>";
// 		print_r($total_in1);
// 		exit;
		$total_in = array();
		$total_no = array();
		foreach ($total_in1 as $t_in1)
		{
			$total_in[$t_in1['ali_order_no']] = $t_in1;
			$total_no[] = $t_in1['total_no'];
		}
		//$total_in = Helper_Array::toHashmap ( $total_in, 'ali_order_no' );
		//抵达仓
		if(!empty($total_no)){
			$t_in = Totalinlist::find('total_no in (?)',$total_no)->asArray()->getAll();
			$t_in = Helper_Array::toHashmap ( $t_in, 'total_no' );
		}
		
// 					echo "<pre>";
// 					print_r($t_in);
// 					exit;
		//开始循环遍历
		foreach ($lists as $value){
			//部门名称
			$department_name = '';
			if(isset($department[$value->department_id])){
				$department_name = $department[$value->department_id];
			}else {
				$department_name = '';
			}
			$ex='';
			$amounti='';
			$amounto='';
			$rate='';
			$cha='';
			$income_weight='';
			$order_status = $value->order_status;
			if(in_array($order_status, array(1,2,14,15,16))){
				$income_weight = $value->weight_income_ali;
			}else if(in_array($order_status, array(3,4,5,6,7,8,9,10,11,12,13))){
				$income_weight = $value->weight_income_in;
			}
			foreach ($value->fees as $fee){
				if($fee->fee_item_code=='logisticsExpressASP_EX0020'){
					$ex='有';
				}
				if($fee->fee_type=="1"){
					//转换币种求和
					$amounti+=Helper_Quote::exchangeRate($fee->account_date,$fee->amount,$fee->currency);
				}
				if($fee->fee_type=="2"){
					//转换币种求和
					$amounto+=Helper_Quote::exchangeRate($fee->account_date,$fee->amount,$fee->currency);
				}
			}
			if($amounti>0 && $amounto>0){
				$cha=$amounti-$amounto;
				$rate=round($cha/$amounti,4)*100;
				$rate=$rate.'%';
			}
			
			//$pr=Product::find('product_name=?',$value->service_code)->getOne();
			$wi='';
			$item_count=0;
			foreach ($value->farpackages as $p){
				$item_count+=$p->quantity;
				//产品类型
				if(($pr[$value->service_code]['type']=='4' && ceil($p->length)<=60 && ceil($p->width)<=60 && ceil($p->height)<=60) || $pr[$value->service_code]['type']=='5'){
					$wi=0;
				}else {
					if ($pr[$value->service_code]['ratio']){
						$wi+=ceil($p->length)*ceil($p->width)*ceil($p->height)/$pr[$value->service_code]['ratio']*$p->quantity;
					}else{
						$wi=0;
					}
				}
			}
			$wo='';
			// 				$ch=ChannelCost::find('channel_id=? and product_id=?',$value->channel_id,$pr[$value->service_code]['product_id'])->getOne();
			// 				$channel=Channel::find('channel_id=?',$value->channel_id)->getOne();
			$ratio = @$ch[$value->channel_id.$pr[$value->service_code]['product_id']]['ratio'];
// 			echo "<pre>";
// 			print_r($value);
// 			exit;
			$length_out=0;
			$width_out=0;
			$height_out=0;
			$faroutpackages = $value->faroutpackages;
			foreach ($faroutpackages as $p){
				//产品类型
				if(($channel[$value->channel_id]['type']=='4' && ceil($p->length_out)<=60 && ceil($p->width_out)<=60 && ceil($p->height_out)<=60) || $channel[$value->channel_id]['type']=='5'){
					$wo=0;
				}else {
					if ($ratio){
						$wo+=ceil($p->length_out)*ceil($p->width_out)*ceil($p->height_out)/$ratio*$p->quantity_out;
					}else{
						$wo=0;
					}
				}
				
				if(count($faroutpackages)==1){
					$length_out=$p->length_out;
					$width_out=$p->width_out;
					$height_out=$p->height_out;
				}
			}
			
			$operatorc='';$operatoro='';
			foreach ($value->events as $eve){
				if($eve->event_code=='CONFIRM'){
					$operatorc=$eve->operator;
				}
				if($eve->event_code=='WAREHOUSE_OUTBOUND'){
					$operatoro=$eve->operator;
				}
			}
			
			$timett='';
			if($value->delivery_time && $value->carrier_pick_time){
				$timett=round(($value->delivery_time-$value->carrier_pick_time)/86400,1);
			}
			//$route=Route::find('tracking_no=?',$value->tracking_no)->order('time desc')->getOne();
			
			//发件人抬头
			//$sender=Sender::find('sender_id = ?',$value->sender_id)->getOne();
			if(!isset($sender[$value->sender_id])){
				$sender_company = '';
			}else{
				$sender_company = $sender[$value->sender_id]['sender_company'];
			}
// 			//启程总单
// 			$total_out = Totalorderout::find('ali_order_no=?',$value->ali_order_no)->getOne();
// 			//抵达总单
// 			$total_in = Totalorderin::find('ali_order_no=?',$value->ali_order_no)->getOne();
			//抵达仓
// 			$t_in = Totalinlist::find('total_no=?',$total_in->total_no)->getOne();
			//$in_department_id = Department::find('department_id=?',$t_in->in_department_id)->getOne();
			//取件员
			//$pickup = PickUpMember::find('id=?',$value->wechat_id)->getOne();
			//发件人备注
			//$send_context = Contact::find('sender_company = ?',$value->sender_name2)->getOne();
			//库位信息
// 			$posscan = PosScan::find('order_id=?',$value->order_id)->order('pos_scan_id desc')->getOne();
			if($item_count == 0){
				//$item_count = Orderpackage::find('order_id = ?',$value->order_id)->getSum('quantity');
				$item_count = $item_count2[$value->order_id];
			}
			//出货实重
			$weight_actual_out = 0;
			if($value->weight_actual_out){
				$weight_actual_out = $value->weight_actual_out;
			}else{
				$weightarr = Helper_Quote::getweightarr($value,2);
				if($weightarr['package']){
					foreach ($weightarr['package'] as $weightar){
						$weight_actual_out += $weightar['weight_out'];
					}
				}
			}
			
			$has_battery = 0;
			foreach ($value->product as $pro){
				if($pro->has_battery){
					$has_battery += $pro->product_quantity;
				}
			}
			//开始组装数据
			$row =array(
				$status[$value->order_status],
				$department_name,
				Helper_Util::strDate('Y-m-d H:i', $value->create_time),
				$value->service_product->product_chinese_name,
				$value->ali_order_no,
				$value->far_no,
				"'".$value->tracking_no,
				"'".$value->total_list_no,
				$ex,
				$value->getACount()=='0'?'':$value->getACount(),
				$value->consignee_country_code,
				$value->packing_type,
				$item_count=='0'?'':$item_count,
				$amounti?round($amounti,2):'',
				$amounto?round($amounto,2):'',
				$cha?round($cha,2):'',
				$rate,
				$income_weight,
				$value->weight_actual_in?round($value->weight_actual_in,2):'',
				$value->total_volumn_weight?$value->total_volumn_weight:($wi?$wi:''),
				$value->weight_income_in?round($value->weight_income_in,2):'',
				$value->weight_label?round($value->weight_label,2):'',
				$value->channel->network_code,
				$value->channel->channel_name,
				
				$sender_company,
				round($weight_actual_out,2),
				$value->weight_bill?round($value->weight_bill,2):'',
				$value->total_out_volumn_weight?$value->total_out_volumn_weight:($wo?$wo:''),
				$value->weight_cost_out?round($value->weight_cost_out,3):'',
				Helper_Util::strDate('Y-m-d H:i', $value->far_warehouse_in_time),
				$value->far_warehouse_in_operator,
				Helper_Util::strDate('Y-m-d H:i', $value->warehouse_confirm_time),
				$operatorc,
				Helper_Util::strDate('Y-m-d H:i', $value->payment_time),
				Helper_Util::strDate('Y-m-d H:i', $value->warehouse_out_time),
				$operatoro,
				$length_out,
				$width_out,
				$height_out,
				
				Helper_Util::strDate('Y-m-d H:i', $value->warehouse_confirm_time),
				@Helper_Util::strDate('Y-m-d H:i', $total_out[$value->ali_order_no]['create_time']),
				@Helper_Util::strDate('Y-m-d H:i', $total_in[$value->ali_order_no]['create_time']),
				@$in_department_id[$t_in[$total_in[$value->ali_order_no]['total_no']]['in_department_id']]['department_name'],
				Helper_Util::strDate('Y-m-d H:i', $value->pick_up_time),
				
				
				Helper_Util::strDate('Y-m-d H:i', $value->present_time),
				Helper_Util::strDate('Y-m-d H:i', $value->delivery_time),
				$timett,
				$value->declaration_type=='DL'?$value->declaration_type:(($value->total_amount>700 || $value->weight_actual_in>70)?'强制':$value->declaration_type),
				$value->total_amount?round($value->total_amount,2):0,
				$value->sender_name2,
				$value->sender_name1,
				$value->sender_mobile?"'".$value->sender_mobile:"'".$value->sender_telephone,
				$value->sender_email,
				$value->sender_state_region_code.$value->sender_city.$value->sender_street1.$value->sender_street2,
				$value->sender_postal_code,
				isset($send_context[$value->sender_name2])?$send_context[$value->sender_name2]['comment']:'',
				$value->consignee_name2?$value->consignee_name2:$value->consignee_name1,
				$value->tax_payer_id,
				$value->consignee_name1?$value->consignee_name1:$value->consignee_name2,
				$value->consignee_mobile?"'".$value->consignee_mobile:"'".$value->consignee_telephone,
				$value->consignee_email,
				$value->consignee_city,
				$value->consignee_state_region_code,
				"'".$value->consignee_postal_code,
				$value->consignee_street1,
				$value->consignee_street2,
				$value->consignee_street1.' '.$value->consignee_street2.' '.$value->consignee_city.' '.$value->consignee_state_region_code.' '.$value->consignee_postal_code.' '.$value->consignee_country_code,
				@$route[$value->tracking_no]['description'],
				@$route[$value->tracking_no]['location'],
				@Helper_Util::strDate('m-d H:i', $route[$value->tracking_no]['time']),
				$value->need_pick_up?"是":"",
				$value->pick_company, //取件网点
				@$pickup[$value->wechat_id]['name'],
				'',
				"'".$value->reference_no,
				$value->remark,
				$value->has_battery == 1 ? '是' : '否',
				$has_battery,
				@$posscan[$value->order_id]['warehouse_code'],
				@$posscan[$value->order_id]['scan_name'],
				@date('Y-m-d H:i:s',$posscan[$value->order_id]['create_time'])
				
			);
			
			
			//判断权限
			if(in_array('2', $role_id) || in_array('3', $role_id) || in_array('4', $role_id)){
				//产品
				$product = Orderproduct::find('order_id=?',$value->order_id)->getAll();
				foreach ($product as $p){
					$row[] = $p->product_name_far;
					$row[] = $p->product_name_en;
					$row[] = $p->hs_code;
					$row[] = $p->declaration_price;
					$row[] = $p->product_quantity;
				}
			}
			if(!in_array('3', $role_id) && !in_array('4', $role_id)){
				unset($row[13]);
				unset($row[14]);
				unset($row[15]);
				unset($row[16]);
				if(!in_array('2', $role_id)){
					$i2 = 49;
					while ($i2 <= 66){
						unset($row[$i2]);
						$i2 ++;
					}
				}
			}
			
			//子单号
			$sub_code = Subcode::find('order_id=?',$value->order_id)->getAll();
			$list_key = 152;
			foreach($sub_code as $k => $sub){
				if(!isset($sheet[0][$list_key])){
					$k1 = $k + 1;
					$sheet[0][$list_key] = '子单号'.$k1;
				}
				if(in_array('2', $role_id) || in_array('3', $role_id) || in_array('4', $role_id)){
					end($row);
					$key = key($row) + 1;
					while ($key < $list_key){
						$row[$key] = '';
						$key ++;
					}
				}
				$row[$list_key] = $sub->sub_code;
				$list_key++;
			}
// 			echo "<pre>";
// 			print_r($row);
// 			exit;
			Helper_ExcelX::addRow ( $row );
			//$sheet [] = $row;
		}
	}
	/**
	 * @todo   批量重查
	 * @author stt
	 * @since  2020-09-01
	 * @link   #82252
	 */
	function actionbatchgettrace(){
		$ids = request('batchIds');
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		//开启事务
		$conn = QDB::getConn();
		$conn->startTrans();
		try {
			foreach ($orders as $order){
				if ($order->get_trace_flag!='1'){
					$order->get_trace_flag='4';
					$order->save();
				}
			}
			$conn->completeTrans( true );
		}catch (Exception $e){
			$conn->completeTrans( false );
			echo json_encode('failed');
			exit();
		}
		echo json_encode('success');
		exit();
	}
	/**
	 * @todo   批量一键确认
	 * @author 吴开龙
	 * @since  2020-10-27
	 * @link   #83415
	 */
	function actionBatchAffirm(){
		//获取前台传过来的订单id
		$ids = request('batchIds');
		//取出所有订单
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		//循环遍历订单
		foreach ($orders as $order){
			//取出每个订单的所有轨迹
			$trace_check = Tracking::find('order_id = ? and confirm_flag = "0"',$order->order_id)->getAll();
			//循环遍历修改状态
			foreach ($trace_check as $check){
				$check->confirm_flag = '1';
				$check->save();
			}
		}
		//成功返回success
		echo json_encode('success');
		exit();
	}
	/**
	 * 一键确认
	 */
	function actionallcheck(){
		$trace_check = Tracking::find('order_id = ? and confirm_flag = "0"',request('order_id'))->getAll();
		foreach ($trace_check as $check){
			$check->confirm_flag = '1';
			$check->save();
		}
		return $this->_redirect(url('order/trace',array('order_id'=>request('order_id'))));
	}
	/**
	 * @todo   重新发送最新的一条失败轨迹
	 * @author stt
	 * @since  2020-09-08
	 * @link   #82387
	 */
	function actionallsend(){
		$trace_check = Tracking::find('order_id = ? and (send_flag != "1") and send_times>1',request('order_id'))->order('trace_time desc')->getOne();
		$trace_check->send_times = '0';
		$trace_check->save();
		return $this->_redirect(url('order/trace',array('order_id'=>request('order_id'))));
	}
	/**
	 * @todo   获取far面单
	 * @author 吴开龙
	 * @since  2020-12-04
	 * @link   #84247
	 */
	function actionGetFarLabel(){
		//获取订单信息
		$order = Order::find('order_id=?',request('order_id'))->getOne();
		//打印far面单
		Helper_Common::getfarlabel($order);
		//判断名字
		$pdfisexist = Helper_PDF::pdfisexist($order->order_id.'.pdf');
		if ($pdfisexist['message']!='noexist'){
			$data['url'] = $pdfisexist['url'];
			return json_encode($data);
		}else{
			$pdfisexist_no = Helper_PDF::pdfisexist($order->order_no.'.pdf');
			$data['url'] = $pdfisexist_no['url'];
			return json_encode($data);
		}
	}
	/**
	 * 订单事件
	 */
	function actionEvent(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$event_code=array(
			'WAREHOUSE_INBOUND'=>'WAREHOUSE_INBOUND:入库',
			'CHECK_WEIGHT'=>'CHECK_WEIGHT:称重',
			'CONFIRM'=>'CONFIRM:核查',
			'PALLETIZE'=>'PALLETIZE:打托',
			'WAREHOUSE_OUTBOUND'=>'WAREHOUSE_OUTBOUND:出库',
			'CARRIER_PICKUP'=>'CARRIER_PICKUP:承运商取件',
			'DELIVERY_TO_FLIGHT'=>'DELIVERY_TO_FLIGHT:交航失败',
			'DELIVERY'=>'DELIVERY:派送失败'
		);
		if($order->service_code=="OCEAN-FY"){
			$event_code['LOAD']='LOAD:装柜';
			$event_code['SET_SAIL']='SET_SAIL:开船';
			$event_code['ARRIVAL_PORT']='ARRIVAL_PORT:到港';
		}
		//         $event_code=Helper_Array::toHashmap(Eventcode::find()->getAll(), 'event_code', 'event_code');
		$select=Event::find('order_id=?',request('order_id'))->getAll();
		$event_info_code=Helper_Array::getCols($select, 'event_code');
		foreach ($event_info_code as $v){
			unset($event_code[$v]);
		}
		if(request_is_post()){
			if(request('event_code')=='WAREHOUSE_INBOUND' || request('event_code')=='SORTING_CENTER_INBOUND_CALLBACK'){
				$order->order_status='5';
				$order->save();
			}
			if(request('event_code')=='WAREHOUSE_OUTBOUND' || request('event_code')=='SORTING_CENTER_OUTBOUND_CALLBACK'){
				$order->order_status='6';
				$order->save();
			}
			$event=new Event();
			//事件加客户
			$event->changeProps(array(
				'order_id'=>request('order_id'),
				'customer_id'=>$order->customer_id,
				'event_code'=>request('event_code'),
				'event_time'=>strtotime(request('event_time')),
				'event_location'=>request('event_location'),
				'timezone'=>request('timezone'),
				'reason'=>request('event_code')=='DELIVERY_TO_FLIGHT'?request('failed_reason'):'',
				'operator'=>MyApp::currentUser('staff_name')
			));
			if(request('event_code') == 'LOAD'){
				//船期时间戳保存
				$event->sailling_date = strtotime(request('sailling_date'));
				$event->container_no = request('container_no');
				$event->bill_no = request('bill_no');
			}
			$event->save();
			
			if(isset($_POST['package'])){
				$packages=request('package');
				if($packages['reason_name']){//有失败原因,无论package是否存在都不保存到数据库中
					$event->reason=$packages['reason_name'];
					$event->save();
				}else{
					$items=array();
					foreach ($packages['quantity_far'] as $i => $v){
						$items[]=array(
							'quantity_far'=>$v,
							'length_far'=>$packages['length_far'][$i],
							'width_far'=>$packages['width_far'][$i],
							'height_far'=>$packages['height_far'][$i],
							'weight_far'=>$packages['weight_far'][$i],
						);
					}
					if(count($items)>0){
						foreach ($items as $key=>$value){
							$far_package=new Farpackage();
							$far_package->changeProps(array(
								'order_id'=>request('order_id'),
								'length'=>$value['length_far'],
								'width'=>$value['width_far'],
								'height'=>$value['height_far'],
								'weight'=>$value['weight_far'],
								'quantity'=>$value['quantity_far'],
							));
							$far_package->save();
						}
						$order->save();
					}
				}
			}
			if(isset($_POST['fee'])){
				$fee_info=request('fee');
				if($fee_info['reason_name']){//有失败原因,无论费用项名称是否存在都不保存到数据库中
					$event->reason=$packages['reason_name'];
					$event->save();
				}else{
					foreach ($fee_info['fee_code'] as $f){
						$fee_item=FeeItem::find('item_code=?',$f)->getOne();
						//存入fee表中（费用数量必须大于0）
						if ($fee_info['quantity'][$f]>0){
							$fee= new Fee();
							$fee->changeProps(array(
								'order_id'=>$order->order_id,
								'fee_item_code'=>$fee_item->sub_code,
								'fee_item_name'=>$fee_item->item_name,
								'fee_type'=>'1',
								'quantity'=>$fee_info['quantity'][$f],
								'btype_id'=>$order->customer_id
							));
							$fee->save();
						}
					}
				}
			}
			if(isset($_POST['carrier'])){
				$carrier=request('carrier');
				if($carrier['reason_name']){
					$event->reason=$carrier['reason_name'];
					$event->save();
				}else{
					$department=Department::find('department_id=?',MyApp::currentUser('department_id'))->getOne();
					//EMS的产品的仓库定义为南京
					if($order->service_code=='EMS-FY'){
						$event->location='南京';
					}elseif ($department->department_name=='杭州仓'){
						$event->location='杭州';
					}elseif ($department->department_name=='义乌仓'){
						$event->location='义乌';
					}elseif ($department->department_name=='上海仓'){
						$event->location='上海';
					}elseif ($department->department_name=='广州仓'){
						$event->location='广州';
					}elseif ($department->department_name=='青岛仓'){
						$event->location='青岛';
					}elseif ($department->department_name=='深圳仓'){
						$event->location='深圳';
					}elseif ($department->department_name=='南京仓'){
						$event->location='南京';
					}else{
						//承运商取件，与事件位置一致
						$event->location=request('event_location');
					}
					$event->save();
				}
			}
			return $this->_redirectMessage('新增事件', '成功', url('order/event',array('order_id'=>request('order_id'))));
		}
		$this->_view['order']=$order;
		$this->_view['event_code']=$event_code;
		$this->_view['list']=$select;
	}
	/**
	 * @todo   菜鸟订单事件
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return string
	 * @link   #81740
	 */
	function actionCaiNiaoEvent(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$cainiao_code=array(
			'ARRIVE'=>'ARRIVE:抵达',
			'FEE'=>'FEE:费用反馈',
			'WAREHOUSE_INBOUND'=>'WAREHOUSE_INBOUND:入库',
			'WAREHOUSE_OUTBOUND'=>'WAREHOUSE_OUTBOUND:出库',
		);
		
		$select=CaiNiao::find('order_id=?',request('order_id'))->getAll();
		$cainiao_info_code=Helper_Array::getCols($select, 'cainiao_code');
		foreach ($cainiao_info_code as $v){
			unset($cainiao_code[$v]);
		}
		if(request_is_post()){
			if(request('cainiao_code')=='WAREHOUSE_INBOUND'){
				$order->order_status='5';
				$order->save();
			}
			if(request('cainiao_code')=='WAREHOUSE_OUTBOUND'){
				$order->order_status='6';
				$order->save();
			}
			$event=new CaiNiao();
			$event->changeProps(array(
				'order_id'=>request('order_id'),
				'cainiao_code'=>request('cainiao_code'),
				'cainiao_time'=>strtotime(request('cainiao_time')),
				'operator'=>MyApp::currentUser('staff_name')
			));
			$event->save();
			
			return $this->_redirectMessage('新增事件', '成功', url('order/event',array('order_id'=>request('order_id'))));
		}
		$this->_view['order']=$order;
		$this->_view['cainiao_code']=$cainiao_code;
		$this->_view['list']=$select;
	}
	/**
	 * @todo   fee_item多选
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return string
	 */
	function actionfeeitemtree(){
		$fee_items=FeeItem::find()->getAll();
		$checked_fee_code=array('EX0001','EX0019','EX0020','EX0035','EX0012');
		$checked='';
		foreach ($fee_items as $fee_item){
			if(in_array($fee_item->item_code, $checked_fee_code)){
				$checked='checked';
			}else{
				$checked='';
			}
			$array [] = array (
				"id" => $fee_item->item_code,
				"text" => $fee_item->item_name,
				"checked" => $checked,
				"attributes" => ""
			);
		}
		echo (json_encode ( $array ));
		exit ();
	}
	/**
	 * 事件页面显示具体信息
	 */
	function actionOrderinfo(){
		switch (request('event_code')){
			case 'CHECK_WEIGHT':
				//获取package信息
				$package=Farpackage::find('order_id=?',request('order_id'))->asArray()->getAll();
				echo json_encode($package);
				break;
			case 'CONFIRM':
				$fee=Fee::find('order_id=? and fee_type="1"',request('order_id'))->asArray()->getAll();
				echo json_encode($fee);
				break;
			case 'CARRIER_PICKUP':
				$event=Event::find("order_id=? and event_code='CARRIER_PICKUP'",request('order_id'))->asArray()->getOne();
				echo json_encode($event);
				break;
			case 'FEE':
				$fee=Fee::find('order_id=? and fee_type="1"',request('order_id'))->asArray()->getAll();
				echo json_encode($fee);
				break;
		}
		exit();
	}
	/**
	 *订单明细
	 */
	function actionDetail(){
		if (substr(request('order_id'),0,2)=='AL'){
			$order=Order::find('ali_order_no=?',request('order_id'))->getOne();
			return $this->_redirect(url('/detail',array('order_id'=>$order->order_id)));
		}
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		if ($order->isNewRecord() ){
			exit('error order_id');
		}
		
		if(request_is_post()){
			//解扣订单
			if(request("release")=='release' && $order->order_status=="12"){
				//存在关联单号
				if($order->related_ali_order_no){
					//本单状态修改为:已取消
					$order->order_status='2';
					//修改本单的毛利为 0
					$order->profit = 0;
					//删除ali_reference表里的快递号信息
					Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
					//删除所有相关费用
					Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
				}else{
					$order->order_status=$order->order_status_copy;
				}
				$order->save();
				return $this->_redirectMessage('解扣订单', '解扣成功', url('order/detail',array('order_id'=>$order->order_id)));
			}
			if($order->tracking_no<>request('tracking_no')){
				if( $order->get_trace_flag == '2' || $order->get_trace_flag == '3'){
					$order->get_trace_flag='1';
				}
				if($order->is_send == '1'){
					$order->is_send = '0';
				}
			}
			if(in_array($order->order_status,array(6,7,8,9))){
				return $this->_redirectMessage('失败','订单状态已变更',url('/detail',array('order_id'=>$order->order_id)),3);
			}
			
			if($order->channel_id<>request('channel_id')){
				$productcode2=array();
				$productcode2=product::getprodutcode(3);//通过渠道判断
				if(in_array($order->service_code,$productcode2)){
					$order->add_data_status='';
				}
			}
			$order->tracking_no=request('tracking_no');
			$order->channel_id=request('channel_id');
			$order->declaration_type=request('declaration_type');
			$order->consignee_name1=request('consignee_name1');
			$order->consignee_name2=request('consignee_name2');		
			//修改保存手机逻辑
			$order->consignee_mobile=request('consignee_mobile');
			$order->consignee_telephone=request('consignee_mobile');						
			$order->tax_payer_id=request('tax_payer_id');
			$order->consignee_state_region_code=trim(request('consignee_state_region_code'));
			if(request('consignee_city')){
				$order->consignee_city=request('consignee_city');
			}
			if(request('consignee_postal_code')){
				$order->consignee_postal_code=request('consignee_postal_code');
			}
			$order->consignee_street1=request('consignee_street1');
			$order->consignee_street2=request('consignee_street2');
			$order->remark=request('remark');
			if(request('reference_no')!= $order->reference_no){
				Alireference::meta()->destroyWhere('order_id=?',$order->order_id);
				if(request('reference_no')){
					$new_reference_no = request('reference_no');
					$new_reference_no = preg_replace('/[.。，、　 ]/',',', $new_reference_no);
					$reference_nos=explode(',', $new_reference_no);
					$reference_no=array_filter($reference_nos);
					foreach ($reference_no as $r){
						$re=new Alireference(array(
							'order_id'=>$order->order_id,
							'reference_no'=>$r
						));
						$re->save();
					}
					$order->reference_no=implode(',', $reference_no);
				}
			}
			if(isset($_POST['need_pick_up'])){
				$order->need_pick_up=request('need_pick_up');
			}
			if(isset($_POST['pick_company'])){
				$order->pick_company=request('pick_company');
			}
			$order->related_ali_order_no=request('related_ali_order_no');
			$order->ali_order_no=request('ali_order_no');
			
			$order->is_pda=request('is_pda');
			//FDA制造商信息保存
			$order->fda_company=request('fda_company');
			$order->fda_address=request('fda_address');
			$order->fda_city=request('fda_city');
			$order->fda_post_code=request('fda_post_code');
			
			//订单内件是否带电保存
			$order->has_battery=request('has_battery');
			if (request("has_battery")==1){
				$order->has_battery_num=request("has_battery_num");
			}
			$order->save();
			//已出库、待发送、已发送
			if(in_array($order->order_status, array('6','7','8')) && request('tracking_no')){
				$sub_code=Subcode::find('sub_code=? and order_id=?',request('tracking_no'),$order->order_id)->getOne();
				if($sub_code->isNewRecord()){
					$sub=new Subcode();
					$sub->order_id=$order->order_id;
					$sub->sub_code=request('tracking_no');
					$sub->save();
				}
			}
			if(request("order_product_id")){
				$total_amount=0;
				$product_name_far=request("product_name_far");
				$product_name_en_far=request("product_name_en_far");
				$hs_code_far=request("hs_code_far");
				$material_use=request("material_use");
				$product_quantity=request("product_quantity");
				$declaration_price=request("declaration_price");
				$order_product_id=request("order_product_id");
				for($i=0;$i<count(request("order_product_id"));$i++){
					$product=Orderproduct::find("order_product_id=?",$order_product_id[$i])->getOne();
					if(!$product->isNewRecord()){
						$product->product_name_far=$product_name_far[$i];
						$product->product_name_en_far=$product_name_en_far[$i];
						$product->hs_code_far=trim($hs_code_far[$i]);
						$product->material_use=$material_use[$i];
						$product->product_quantity=$product_quantity[$i];
						$product->declaration_price=$declaration_price[$i];
						$product->save();
						$total_amount+=$product->product_quantity*$product->declaration_price;
					}
				}
				$order->total_amount=$total_amount;
				$order->save();
				$flag=false;
				foreach ($order->product as $temp){
					if($temp->product_name_far=='' || $temp->product_name_en_far=='' || $temp->hs_code_far=='' || $temp->material_use==''){
						$flag=true;
					}
				}
				//低价非港澳台，检查数据是否完整，若数据完整则无需再补充数据
				if($order->consignee_country_code!='HK' && $order->consignee_country_code!='MO' && $order->consignee_country_code!='TW' && $order->declaration_type!='DL' && $order->total_amount <= '700' && $order->weight_actual_in <= '70'){
					if(!$flag){//信息补充完整
						$order->add_data_status='1';
						$order->save();
					}
				}
			}
			return $this->_redirectMessage('订单编辑', '保存成功', url('order/detail',array('order_id'=>request('order_id'))));
		}
		$this->_view['order']=$order;
	}
	/**
	 * 解扣
	 */
	function actionRelease() {
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		//是否存在
		if (!$order->isNewRecord()) {
			//存在关联单号
			if($order->related_ali_order_no){
				//本单状态修改为:已取消
				$order->order_status='2';
				//修改本单的毛利为 0
				$order->profit = 0;
				//删除ali_reference表里的快递号信息
				Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
				//删除所有相关费用
				Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
			}else{
				$order->order_status=$order->order_status_copy;
			}
			$order->save();
			return $this->_redirectMessage('解扣订单', '解扣成功', url('order/search',array("parameters" => request ( "parameters" ))));
		}else{
			return $this->_redirectMessage('解扣失败', '该订单不存在', url('order/search',array("parameters" => request ( "parameters" ))));
		}
	}
	/**
	 * 批量解扣
	 */
	function actionBatchrelease(){
		$ids = request('batchIds');
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		//开启事务
		$conn = QDB::getConn();
		$conn->startTrans();
		try {
			foreach ($orders as $order){
				if ($order->order_status=='12'){
					//存在关联单号
					if($order->related_ali_order_no){
						//本单状态修改为:已取消
						$order->order_status='2';
						//修改本单的毛利为 0
						$order->profit = 0;
						//删除ali_reference表里的快递号信息
						Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
						//删除所有相关费用
						Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
					}else{
						$order->order_status=$order->order_status_copy;
					}
					$order->save();
				}
			}
			$conn->completeTrans( true );
		}catch (Exception $e){
			$conn->completeTrans( false );
			echo json_encode('failed');
			exit();
		}
		echo json_encode('success');
		exit();
	}
	/**
	 * 显示详细信息
	 */
	function actionEditdetail(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		
		//其他关联订单号
		$order_reference_nos=explode(',', $order->reference_no);
		$other_ali_arr = array();
		foreach ($order_reference_nos as $orn){
			if ($orn){
				//tb_scan_total_detail国内快递扫描详情表
				$scandetail = ScanTotalDetail::find('reference_no = ?',$orn)->getOne();
				if (!$scandetail->isNewRecord()){
					//tb_ali_reference国内快递单号与阿里订单号关系表
					$ali_ids = Alireference::find('reference_no=?',$orn)->setColumns('order_id')->asArray()->getAll();
					foreach ($ali_ids as $ai){
						$other_no =Order::find('order_id=?',$ai['order_id'])->getOne()->ali_order_no;
						if ($other_no!=$order->ali_order_no){
							$other_ali_arr[] = $other_no;
						}
					}
				}
			}
		}
		if (count($other_ali_arr)>0){
			$other_ali_str=implode(',', $other_ali_arr);
		}else{
			$other_ali_str='';
		}
		//获取订单中费用信息
		$data=array();
		//应收包装袋数量
		$data['in_pak']=$order->packagenum;
		//应收箱子数量
		$data['in_box']=$order->boxnum;
		//应收异形包装数量
		$data['in_special']=$order->specialpackagenum;
		//应付异形包装数量
		$data['out_special']=0;
		if(count($order->fees)>0){
			foreach ($order->fees as $temp){
				//                 if($temp->fee_item_code=='logisticsExpressASP_EX0002' && $temp->fee_type=='1'){
				//                     $data['in_pak']=$temp->quantity;
				//                 }
				//                 if($temp->fee_item_code=='logisticsExpressASP_EX0003' && $temp->fee_type=='1'){
				//                     $data['in_box']=$temp->quantity;
				//                 }
				//                 if($temp->fee_item_code=='logisticsExpressASP_EX0034' && $temp->fee_type=='1'){
				//                     $data['in_special']=$temp->quantity;
				//                 }
				if($temp->fee_item_code=='logisticsExpressASP_EX0034' && $temp->fee_type=='2'){
					$data['out_special']=$temp->quantity;
				}
			}
		}
		//查询当前登录人员业务相关部门
		$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
		//获取部门名称
		$relevant_department_names=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_name', 'department_name');
		$relevant_department_names=array_diff($relevant_department_names, array('技术中心','杭州仓','上海仓','义乌仓','广州仓','财务中心','南京仓'));
		$department_names = array();
		foreach ($relevant_department_names as $relevant_department_name){
			$department_names[$relevant_department_name]= $relevant_department_name;
		}
		$date_time = '';
		$reference_nos = explode(',', $order->reference_no);
		foreach ($reference_nos as $nos){
			$scandetail = ScanTotalDetail::find('reference_no = ?',$nos)->getOne();
			$date_time .= Helper_Util::strDate('m-d H:i', $scandetail->scan_no_time).';';
		}
		$department_names['吉通同城']='吉通同城';
		$department_names['德邦快递']='德邦快递';
		$this->_view['fee']=$data;
		$this->_view['order']=$order;
		$product_copy = OrderproductCopy::find('order_id=?',$order->order_id)->getAll();
		if(!count($product_copy)){
			$this->_view['product_copy']='';
		}else{
			$this->_view['product_copy']=$product_copy;
		}
		$this->_view['relevant_department_names']=$department_names;
		$this->_view['date_time']=$date_time;
		//其他关联订单号
		$this->_view['other_ali_order_no']=$other_ali_str;
	}
	/* 保存订单明细信息
	 @author 吴开龙
	 * @since 2020-7-22 09:53:38
	 * @param
	 * @return json
	 * @link #80976
	 */
	function actionProductSave() {
		if (request ( "price" )) {
			$order = Order::find ( "order_id = ?", request ( "order_id" ) )->getOne ();
			self::productCopy(request ( "order_id" ));
			$p1 = request ( "price" );
			$price = Orderproduct::find ( "order_product_id = ?", $p1 ["order_product_id"] )->getOne ();
			$price->order_id = $order->order_id;
			$p1['product_name'] = $p1['product_name_far'];
			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
			$p1['product_name_en'] = preg_replace('/[　\s]+/u',' ',$p1['product_name_en_far']);
			$p1['hs_code'] = $p1['hs_code_far'];
			$p1['product_unit'] = 'pcs';
			$price->changeProps ( $p1 );
			$price->save ();
			//修改申报总价值
			$o_product = Orderproduct::find("order_id = ?", request ( "order_id" ))->getAll();
			$total_amount = 0;
			foreach ($o_product as $v){
				$total_amount += $v->product_quantity * $v->declaration_price;
			}
			$order->total_amount = $total_amount;
			$order->save();
			echo ($price->order_product_id);
		}
		exit ();
	}
	/**
	 * 删除订单明细信息
	 @author 吴开龙
	 * @since 2020-7-22 09:53:38
	 * @param
	 * @return json
	 * @link #80976
	 */
	function actionProductDel() {
		if (request ( "order_product_id" )) {
			$price = Orderproduct::find ( "order_product_id = ?", request ( "order_product_id" ) )->getOne ();
			$order = Order::find ( "order_id = ?", $price->order_id )->getOne ();
			self::productCopy($price->order_id);
			$price->destroy ();
			//修改申报总价值
			$o_product = Orderproduct::find("order_id = ?", $order->order_id)->getAll();
			$total_amount = 0;
			foreach ($o_product as $v){
				$total_amount += $v->product_quantity * $v->declaration_price;
			}
			$order->total_amount = $total_amount;
			$order->save();
		}
		exit ();
	}
	/**
	 * 在进行增删改动作时备份原数据
	 @author 吴开龙
	 * @since 2020-7-21 09:53:38
	 * @param
	 * @return json
	 * @link #80976
	 */
	static function productCopy($order_id){
		$productcopy = OrderproductCopy::find('order_id=?',$order_id)->getCount();
		if(!$productcopy){
			$product = Orderproduct::find('order_id = ?',$order_id)->asArray()->getAll();
			foreach ($product as $v){
				$productcopy = new OrderproductCopy();
				$productcopy->changeProps($v);
				$productcopy->save();
			}
		}
	}
	/**
	 * 收付信息
	 */
	function actionEditbalance(){
		$fee_receivable=Fee::find("fee_type= '1' and order_id=?",request('order_id'))->getAll();
		$fee_payment=Fee::find("fee_type= '2' and order_id=?",request('order_id'))->getAll();
		$this->_view['receivable']=$fee_receivable;
		$this->_view['payment']=$fee_payment;
	}
	/**
	 * @todo 订单明细页面计算应收应付金额合计
	 * @author 吴开龙
	 * @since 2020-6-10 18:06:16
	 * @param
	 * @return json
	 * @link #
	 */
	function actionSetIncomeAmount(){
		$order = Order::find('order_id=?',request('order_id'))->getOne();
		$tb_fee = Fee::find('order_id=? and fee_type=?',request('order_id'),request('type'))->getAll();
		$fee = 0;
		bcscale(2);
		foreach ($tb_fee as $v){
			if($v->amount!=0){
				$currency = $v->currency ? $v->currency :'CNY';
				if($currency != 'CNY'){
					//转换币种
					$fee = bcadd(Helper_Quote::exchangeRate($v->account_date, $v->amount, $currency,0,'',$v->rate),$fee);
				}else{
					$fee = bcadd($v->amount,$fee);
				}
			}
		}
		return $fee;
	}
	/**
	 * 应收应付保存
	 */
	function actionSavebalance() {
		$order = order::find ( "order_id = ?", request ( "order_id" ) )->getOne ();
		if(request("a_balance_amount_gross")){
			$order->profit=request("a_balance_amount_gross");
			$order->save();
			echo "success";
			exit();
		}
		if ($order->isNewRecord ()) {
			echo ("订单不存在");
			exit ();
		}
		$conn = QDB::getConn ();
		$conn->startTrans ();
		
		//应收应付
		$json = json_decode ( request ( "balance" ), true );
		if (! $json) {
			echo ("数据不存在");
			exit ();
		}
		
		if (count ( $json ) > 1) {
			foreach ( $json as $value ) {
				$fee = Fee::find ( "fee_id = ?", $value ["id"] )->getOne ();
				if($fee->isNewRecord ()){
					if(!Config::closeBalance() && $order->warehouse_out_time){
						$fee->account_date=$order->warehouse_out_time;
					}else {
						$fee->account_date=time();
					}
				}
				$fee->changeProps ( $value );
				$fee->order_id = $order->order_id;
				$fee->quantity = 1;
				$fee_item=FeeItem::find('item_name=?',$value['fee_item_name'])->getOne();
				$fee->fee_item_code=$fee_item->sub_code;
				if($value['fee_type']=='1'){
					$customer = Customer::find('customer = ?',$value['btype_name'])->getOne();
					$fee->btype_id=$customer->customer_id;
					$rate = CodeCurrencyItem::getCurrencyRate($value['currency'],$fee->account_date, '');
					if(!$rate){
						$fee->rate = 1;
						$fee->remark = '币种不存在或已过期';
					}else{
						$fee->rate = $rate;
					}
				}elseif($value['fee_type']=='2') {
					$channel = Supplier::find('supplier = ?',$value['btype_name'])->getOne();
					$fee->btype_id=$channel->supplier_id;
					$rate = CodeCurrencyItem::getCurrencyRate($value['currency'],$fee->account_date, $channel->supplier_id);
					if(!$rate){
						$fee->rate = 1;
						$fee->remark = '币种不存在或已过期';
					}else{
						$fee->rate = $rate;
					}
				}
// 				$curr = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$value['currency'],$fee->account_date,$fee->account_date)->getOne();
// 				$fee->rate = $curr->rate;
				QLog::log ( print_r ( $value, true ) );
				$fee->save ();
			}
		} else {
			$fee = Fee::find ( "fee_id = ?", $json [0] ["id"] )->getOne ();
			if (request ( "delete_flag" ) == "true") {
				if (! $fee->isNewRecord ()) {
					$fee->destroy ();
				}
			} else {
				if($fee->isNewRecord ()){
					if(!Config::closeBalance() && $order->warehouse_out_time){
						$fee->account_date=$order->warehouse_out_time;
					}else {
						$fee->account_date=time();
					}
				}
				$fee->changeProps ( $json [0] );
				$fee->order_id = $order->order_id;
				$fee->quantity = 1;
				$fee_item=FeeItem::find('item_name=?',$json [0]['fee_item_name'])->getOne();
				$fee->fee_item_code=$fee_item->sub_code;
				if($json [0]['fee_type']=='1'){
					$customer = Customer::find('customer = ?',$json [0]['btype_name'])->getOne();
					$fee->btype_id=$customer->customer_id;
					$rate = CodeCurrencyItem::getCurrencyRate($json [0]['currency'],$fee->account_date, '');
					if(!$rate){
						$fee->rate = 1;
						$fee->remark = '币种不存在或已过期';
					}else{
						$fee->rate = $rate;
					}
				}elseif($json [0]['fee_type']=='2') {
					$channel = Supplier::find('supplier = ?',$json [0]['btype_name'])->getOne();
					$fee->btype_id=$channel->supplier_id;
					$rate = CodeCurrencyItem::getCurrencyRate($json [0]['currency'],$fee->account_date, $channel->supplier_id);
					if(!$rate){
						$fee->rate = 1;
						$fee->remark = '币种不存在或已过期';
					}else{
						$fee->rate = $rate;
					}
				}
// 				$curr = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$json [0]['currency'],$fee->account_date,$fee->account_date)->getOne();
// 				$fee->rate = $curr->rate;
				$fee->save ();
			}
		}
		$conn->completeTrans ();
		echo ($fee->fee_id);
		exit ();
	}
	/**
	 * 获取轨迹信息
	 */
	function actionGettrace(){
		$trace=Tracking::find('tracking_id=?',request('tracking_id'))->asArray()->getOne();
		echo json_encode($trace);
		exit();
	}
	/**
	 * 编辑轨迹信息
	 */
	function actionTracedetail(){
		$trace=Tracking::find('tracking_id=?',request('tracking_id'))->getOne();
		$trace->location=request('location');
		$trace->trace_desc_cn=request('trace_desc_cn');
		$trace->trace_desc_en=request('trace_desc_en');
		$trace->timezone=request('timezone');
		$trace->trace_time=strtotime(request('edit_trace_time'));
		$trace->save();
		return $this->_redirectMessage('轨迹编辑', '编辑成功', url('order/trace',array('order_id'=>$trace->order_id)));
	}
	/**
	 * 获取事件信息
	 */
	function actionGetevent(){
		$event=Event::find('event_id=?',request('event_id'))->asArray()->getOne();
		if($event['event_code']=='CHECK_WEIGHT'){
			$far_package=Farpackage::find('order_id=?',$event['order_id'])->asArray()->getAll();
			$event['packages']=$far_package;
		}else if($event['event_code']=='CONFIRM'){
			$fee=Fee::find('order_id=?',$event['order_id'])->asArray()->getAll();
			foreach ($fee as $key=>$temp){
				$fee_item=FeeItem::find('sub_code=?',$temp['fee_item_code'])->getOne();
				$fee[$key]['fee_item_code']=$fee_item->item_code;
			}
			$event['fee']=$fee;
		}
		echo json_encode($event);
		exit();
	}
	/**
	 * 编辑事件
	 */
	function actionEventdetail(){
		$event=Event::find('event_id=?',request('event_id'))->getOne();
		$event->event_time=strtotime(request('event_time'));
		$event->event_location=request('detail_event_location');
		//承运商取件
		if ($event->event_code=='CARRIER_PICKUP'){
			$event->location=request('detail_event_location');
		}
		$event->timezone=request('timezone');
		$event->save();
		if(isset($_POST['package'])){
			$packages=request('package');
			$items=array();
			foreach ($packages['quantity_far'] as $i => $v){
				$items[]=array(
					'quantity_far'=>$v,
					'length_far'=>$packages['length_far'][$i],
					'width_far'=>$packages['width_far'][$i],
					'height_far'=>$packages['height_far'][$i],
					'weight_far'=>$packages['weight_far'][$i],
				);
			}
			if(count($items)>0){
				//删除原有package信息
				$order=Order::find('order_id=?',$event->order_id)->getOne();
				Farpackage::find('order_id=?',$event->order_id)->getAll()->destroy();
				foreach ($items as $key=>$value){
					$far_package=new Farpackage();
					$far_package->changeProps(array(
						'order_id'=>$event->order_id,
						'length'=>$value['length_far'],
						'width'=>$value['width_far'],
						'height'=>$value['height_far'],
						'weight'=>$value['weight_far'],
						'quantity'=>$value['quantity_far'],
					));
					$far_package->save();
				}
				$order->save();
			}
		}
		if(isset($_POST['fee'])){
			$fee_info=request('fee');
			//删除fee表原有信息
			Fee::find('order_id=?',$event->order_id)->getAll()->destroy();
			$order = Order::find('order_id = ?',$event->order_id)->getOne();
			foreach ($fee_info['fee_code'] as $f){
				$fee_item=FeeItem::find('item_code=?',$f)->getOne();
				//存入fee表中
				$fee= new Fee();
				$fee->changeProps(array(
					'order_id'=>$event->order_id,
					'fee_item_code'=>$fee_item->sub_code,
					'fee_item_name'=>$fee_item->item_name,
					'fee_type'=>'1',
					'quantity'=>strlen($fee_info['quantity'][$f])>0?$fee_info['quantity'][$f]:'1',
					'btype_id'=>$order->customer_id
				));
				$fee->save();
			}
		}
		return $this->_redirectMessage('事件编辑', '编辑成功', url('order/event',array('order_id'=>$event->order_id)));
	}
	/**
	 * 轨迹和事件确认
	 */
	function actionConfirm(){
		if(request('code')=='trace'){
			$trace=Tracking::find('tracking_id=?',request('tracking_id'))->getOne();
			if ($trace->timezone==-19 || $trace->isNewRecord()){
				return $this->_redirectMessage('时区错误或记录不存在', '请修正时区后再继续', url('order/trace',array('order_id'=>$trace->order_id)));
			}
			$trace->confirm_flag='1';
			$trace->save();
			return $this->_redirect(  url('order/trace',array('order_id'=>$trace->order_id)));
			return $this->_redirectMessage('轨迹确认', '确认成功', url('order/trace',array('order_id'=>$trace->order_id)));
		}
		if(request('code')=='event'){
			$event=Event::find('event_id=?',request('event_id'))->getOne();
			$event->confirm_flag='1';
			$event->save();
			return $this->_redirectMessage('事件确认', '确认成功', url('order/event',array('order_id'=>$event->order_id)));
		}
	}
	function actionIgnore(){
		$trace=Tracking::find('tracking_id=?',request('tracking_id'))->getOne();
		if (!$trace->isNewRecord()){
			$trace->confirm_flag=2;
			$trace->save();
		}
		return $this->_redirect(  url('order/trace',array('order_id'=>$trace->order_id)));
		return $this->_redirectMessage('轨迹忽略', '成功', url('order/trace',array('order_id'=>$trace->order_id)));
	}
	/**
	 * 支付验证
	 */
	function actionPaymentverify(){
		$json_data=array();
		if(request_is_post()){
			$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
			$return_or=Returned::find('ali_order_no=?',request('ali_order_no'))->order("return_id desc")->getOne();
			if($order->isNewRecord()){
				$json_data['status']='notexist';
			}else{
				if ($order->order_status=='10'){//已查验
					//从核查日期算起，超过8天报已超期
					$event=Event::find("event_code='CONFIRM' and order_id=?",$order->order_id)->getOne();
					if(!$event->isNewRecord()){
						if($event->event_time+691200< time()){
							$json_data['status']="chaoqi";
						}else{
							$json_data['status']='false';
						}
					}else{
						$json_data['status']='false';
					}
				}else if($order->order_status=='11'){//待退货
					if(empty($order->payment_time)){
						if($return_or->isNewRecord()){
							$json_data['status']='false';
						}elseif($return_or->cargo_direction=='2'){
							$json_data['related_ali_order_no']=$order->related_ali_order_no;
							$json_data['status']='huandanchongfa';
						}else{
							$json_data['status']='waitreturn';
						}
					}else{
						$json_data['status']='waitreturn';
					}
				}else if ($order->order_status=='5'){//未支付
					$json_data['status']='false';
				}else if ($order->order_status=='4'){//已支付
					//4.28计算最便宜渠道
					//                     $product=Product::find('product_name=?',$order->service_code)->getOne();
					//                     $channelcost=ChannelCost::find('product_id=?',$product->product_id)->getAll();
					//                     if(count($channelcost)<=0){
					//                         $json_data['statue']='true';
					//                     }else{
					//                     //计算成本价格
					//                     $price_array=array();
					//                     //获取异形包装费
					//                     $special_fee=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
					//                     if($special_fee->isNewRecord()){
					//                         $special_count=0;
					//                     }else{
					//                         $special_count=$special_fee->quantity;
					//                     }
					//                     foreach ($channelcost as $temp){
					//                         $network=Network::find("network_code=? ",$temp->channel->network_code)->getOne();
					//                         //判断渠道可用部门和禁用部门
					//                         $available_department_ids=Helper_Array::getCols(Channeldepartmentavailable::find('channel_id=?',$temp->channel_id)->getAll(), 'department_id');
					//                         $disabled_department=Channeldepartmentdisable::find('channel_id=? and department_id=? and effect_time <= ? and failure_time >= ?',$temp->channel_id,$order->department_id,time(),time())->getOne();
					//                         if((count($available_department_ids)>0 && !in_array($order->department_id, $available_department_ids)) || !$disabled_department->isNewRecord()){
					//                             continue;
					//                         }
					//                         //获取重量数组
					//                         $weight_array=Helper_Quote::getweightlist($order, $temp);
					//                         if(count($weight_array)<=0){
					//                             continue;
					//                         }
					//                         foreach ($weight_array as $key=>$value){
					//                             $price_array[]=Helper_Quote::cheap_quote($order, $temp, $network->network_id, $value,$key,$special_count);
					//                         }
					//                     }
					//                     if(count($price_array)<=0){
					//                         $json_data['statue']='true';
					//                     }else{
					//                         $price_array=array_filter($price_array);
					//                         $price_cost_array=array();
					//                         //判断是否有查询失败的报价
					//                         foreach ($price_array as $v){
					//                             $price_cost_array[]=$v['public_price'];
					//                         }
					//                         if(max($price_cost_array)==0){
					//                             $json_data['directionmsg']='false';
					//                         }else{
					//                             $price_array=Helper_Array::sortByCol($price_array,'public_price');
					//                             $data=reset($price_array);
					//                             //如果是n则不用优化
					//                             if($data['direction']=='n'){
					//                                 $json_data['directionmsg']='false';
					//                             }else{
					//                                 $json_data['directionmsg']='true';
					//                                 $json_data['data']=$data;
					//                                 //记录优化数据
					//                                 $order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
					//                                 //优化后的数据
					//                                 $order->amount_after_optimization=$data['public_price'];
					//                                 $order->channel_id_after_optimization=$data['channel_id'];
					//                                 $order->weight_after_optimization=$data['weight_label'];
					//                                 //优化前的数据
					//                                 $price_array_n=Helper_Array::groupBy($price_array,'direction')['n'];
					//                                 $price_array_n=Helper_Array::sortByCol($price_array_n,'public_price');
					//                                 $price_array_old=reset($price_array_n);
					//                                 $order->amount_before_optimization=$price_array_old['public_price'];
					//                                 $order->channel_id_before_optimization=$price_array_old['channel_id'];
					//                                 $order->weight_before_optimization=$price_array_old['weight_label'];
					//                                 $order->save();
					//                             }
					//                         }
					//                     }
					//                   }
					//判断时间 
					 $q_time = time() - 86400;
					//早于一天以上的报优先处理 
					if($order->payment_time < $q_time){
						$json_data['status']='youxianchuli';
					}else{ 
						$json_data['status']='true';
					}
				}else if($order->order_status=='12'){//已扣件
					$json_data['status']='hold';
				}else{
					if(empty($order->payment_time)){//未支付
						if($return_or->isNewRecord()){
							$json_data['status']='false';
						}else{
							if($return_or->cargo_direction=='2'){
								$json_data['related_ali_order_no']=$order->related_ali_order_no;
								$json_data['status']='huandanchongfa';
							}elseif($return_or->state=='1'){
								$json_data['status']='waitreturn';
							}
						}
					}else{//异常
						$json_data['status']='abnormal';
					}
				}
			}
			echo json_encode($json_data);
			exit();
		}
	}
	/**
	 * 下载pdf组合文件
	 */
	function actionDownloadpdf(){
		$dir=Q::ini('upload_tmp_dir');
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename='.$order->tracking_no.'_combi.pdf');
		if(!file_exists($dir.DS.$order->tracking_no.'_combi.pdf')){
			//判断账号是否为润峯，合并pdf文件，保存在本地
			if($order->account=='4F1R24'){
				$filenames=array($dir.DS.$order->tracking_no.'.pdf',$dir.DS.$order->tracking_no.'_invoice.pdf');
			}else{
				//ups账号
				$filenames=array($dir.DS.$order->tracking_no.'_copy_1.pdf');
				//获取sub_code个数
				$sub_code=Subcode::find('order_id=?',$order->order_id)->getAll();
				if(count($sub_code)>'11'){
					$filenames[]=$dir.DS.$order->tracking_no.'_copy_2.pdf';
				}
				$filenames[]=$dir.DS.$order->tracking_no.'_invoice.pdf';
			}
			@Helper_PDF::merge($filenames,$dir.DS.$order->tracking_no.'_combi.pdf','file');
		}
		return file_get_contents($dir.DS.$order->tracking_no.'_combi.pdf');
	}
	/**
	 * 补充三免数据
	 */
	function actionPaddingdata(){
		//已支付或者已入库的订单
		$productcode=Product::getprodutcode(1);//需要补充数据的产品code
		$productcode2=product::getprodutcode(3);//通过渠道判断
		$channel_ids=Channel::channelids(1);//渠道需要验证的ID
		
		$orders=Order::find("ali_testing_order!= '1' and department_id in (?)",RelevantDepartment::relateddepartmentids());
		
		if(count($productcode)>0){
			if(count($productcode2)>0 && count($channel_ids)>0 ){
				$orders->Where('service_code in (?) or (service_code in (?) and channel_id in (?))',$productcode,$productcode2,$channel_ids);
			}else{
				$orders->Where('service_code in (?)',$productcode);
			}
		}else{
			if(count($productcode2)>0 ){
				if(!count($channel_ids)){
					$channel_ids=array('-1');
				}
				$orders->Where('service_code in (?) and channel_id in (?)',$productcode2,$channel_ids);
			}
		}
		
		//订单日期
		if(request("start_date")){
			$orders->where("create_time >=?",strtotime(request("start_date").' 00:00:00'));
		}
		if (request("end_date")){
			$orders->where("create_time <=?",strtotime(request("end_date").' 23:59:59'));
		}
		//阿里订单号
		if(request('ali_order_no')){
			$orders->where('ali_order_no=?',request('ali_order_no'));
		}
		//泛远单号
		if(request('far_no')){
			$orders->where('far_no=?',request('far_no'));
		}
		//低价非港澳台
		if(request('type','1')=='1'){
			$orders->where("declaration_type!='DL' and total_amount <= '700' and weight_actual_in <= '70' and consignee_country_code != 'HK' and consignee_country_code != 'MO' and consignee_country_code != 'TW'");
		}
		// 低价港澳台
		if (request('type','1')=='2'){
			$orders->where("declaration_type!='DL' and total_amount <= '700' and weight_actual_in <= '70' and (consignee_country_code = 'HK' or consignee_country_code = 'MO' or consignee_country_code = 'TW') ");
		}
		//高价
		if(request('type','1')=='3'){
			$orders->where("declaration_type='DL' or total_amount > '700' or weight_actual_in > '70'");
		}
		//未补充数据
		if(request('status','1')=='1'){
			$orders->where("add_data_status != '1' ");
		}
		// 已补充数据
		if (request('status','1')=='2'){
			$orders->where("add_data_status = '1' ");
		}
		//订单状态
		if(request('order_status')){
			$orders->where("order_status= ?",request('order_status'));
		}else{
			$orders->where("order_status in (?)",array(10,4,5,7,8));
		}
		$pagination = null;
		$list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 50 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * 保存港澳台收件人信息
	 */
	function actionSaveconsignee(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$order->consignee_cn=request('consignee_name_cn');
		$order->consignee_address_cn=request('consignee_address_cn');
		$order->save();
		//判断订单中数据是否添加完整
		$flag=false;
		foreach ($order->product as $temp){
			if($temp->product_name_far=='' || $temp->product_name_en_far=='' || $temp->hs_code_far=='' || $temp->material_use==''){
				$flag=true;
			}
		}
		if(!$flag && $order->consignee_cn != '' && $order->consignee_address_cn!=''){
			$order->add_data_status='1';
			$order->save();
		}
		exit();
	}
	/**
	 * 保存产品信息
	 */
	function actionSaveproduct(){
		$check_hs=Hs::find('HSCode=?',request('hs_code'))->getOne();
		if(!$check_hs->isNewRecord()){
			$orderproduct=Orderproduct::find('order_product_id=?',request('order_product_id'))->getOne();
			$orderproduct->product_name_far=request('product_name_cn');
			$orderproduct->product_name_en_far=request('product_name_en');
			$orderproduct->hs_code_far=request('hs_code');
			$orderproduct->material_use=request('material');
			$orderproduct->product_quantity=request('product_quantity');
			$orderproduct->save();
			//判断订单中数据是否添加完整
			$order=Order::find('order_id=?',$orderproduct->order_id)->getOne();
			$flag=false;
			foreach ($order->product as $temp){
				if($temp->product_name_far=='' || $temp->product_name_en_far=='' || $temp->hs_code_far=='' || $temp->material_use==''){
					$flag=true;
				}
			}
			//判断收件地址是否是港澳台地区
			if($order->consignee_country_code=='HK' || $order->consignee_country_code=='MO' || $order->consignee_country_code=='TW'){
				if(!$flag && $order->consignee_cn != '' && $order->consignee_address_cn!=''){
					$order->add_data_status='1';
					$order->save();
				}
			}else{
				if(!$flag){//信息补充完整
					$order->add_data_status='1';
					$order->save();
				}
			}
			$total_amount=0;
			foreach ($order->product as $temp){
				$total_amount+=$temp->product_quantity*$temp->declaration_price;
			}
			$order->total_amount=$total_amount;
			$order->save();
			echo 'success';
		}else{
			echo 'hs_error';
		}
		exit();
	}
	/**
	 * 保存经营单位编码和委托书编号
	 */
	function actionSavebusinesscode(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$order->business_code=request('business_code');
		$order->commission_code=request('commission_code');
		$order->save();
		//判断订单中数据是否添加完整
		foreach ($order->product as $temp){
			if($temp->material_use==''){
				$flag=true;
			}
		}
		if(!$flag){
			$order->add_data_status='1';
			$order->save();
		}
		exit();
	}
	/**
	 * 保存材质用途
	 */
	function actionSavematerial(){
		$orderproduct=Orderproduct::find('order_product_id=?',request('order_product_id'))->getOne();
		$orderproduct->material_use=request('material');
		$orderproduct->save();
		//判断订单中数据是否添加完整
		$order=Order::find('order_id=?',$orderproduct->order_id)->getOne();
		$flag=false;
		foreach ($order->product as $temp){
			if($temp->material_use==''){
				$flag=true;
			}
		}
		if(!$flag && $order->business_code != '' && $order->commission_code!=''){
			$order->add_data_status='1';
			$order->save();
		}
		exit();
	}
	/**
	 * 推送订单检查
	 */
	function actionShowerrormessage(){
		$pagination = null;
		$order=Order::find("error_message != '' ")
		->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$order;
		$this->_view['pagination']=$pagination;
	}
	//重新打泛远面单的时候，判断件数
	function actiongetpackageamout(){
		$amount=0;
		if(request("order_id")){
			$packages=Orderpackage::find('order_id=?',request("order_id"))->getAll();
			foreach ($packages as $p){
				$amount+=$p->quantity;
			}
		}
		echo $amount;
		exit;
	}
	/**
	 * 创建标签
	 */
	function createTabs($counts) {
		return array (
			array (
				"id" => "0","title" => "全部","count" => val($counts,0,0),
				"href" => "javascript:TabSwitch()"
			),
			array (
				"id" => "1","title" => "未入库","count" => val($counts,1,0)+val($counts,14,0)+val($counts,15,0)+val($counts,16,0),
				"href" => "javascript:TabSwitch('no_package')"
			),
			array (
				"id" => "5","title" => "已入库","count" => val($counts,5,0),
				"href" => "javascript:TabSwitch('warehouse_in')"
			),
			array (
				"id" => "10","title" => "已核查","count" => val($counts,10,0),
				"href" => "javascript:TabSwitch('prove')"
			),
			array (
				"id" => "4","title" => "已支付","count" => val($counts,4,0),
				"href" => "javascript:TabSwitch('paid')"
			),
			array (
				"id" => "6","title" => "已打印","count" => val($counts,6,0),
				"href" => "javascript:TabSwitch('warehouse_out')"
			),
			array (
				"id" => "7","title" => "已出库","count" => val($counts,7,0),
				"href" => "javascript:TabSwitch('wait_to_send')"
			),
			array (
				"id" => "8","title" => "已提取","count" => val($counts,8,0),
				"href" => "javascript:TabSwitch('sent')"
			),
			array (
				"id" => "9","title" => "已签收","count" => val($counts,9,0),
				"href" => "javascript:TabSwitch('sign')"
			),
			array (
				"id" => "12","title" => "已扣件","count" => val($counts,12,0),
				"href" => "javascript:TabSwitch('hold')"
			),
			array (
				"id" => "2","title" => "已取消","count" => val($counts,2,0),
				"href" => "javascript:TabSwitch('cancel')"
			),
			array (
				"id" => "11","title" => "待退货","count" => val($counts,11,0),
				"href" => "javascript:TabSwitch('wait_to_return')"
			),
			array (
				"id" => "3","title" => "已退货","count" => val($counts,3,0),
				"href" => "javascript:TabSwitch('returned')"
			),
			array (
				"id" => "13","title" => "已结束","count" => val($counts,13,0),
				"href" => "javascript:TabSwitch('termination')"
			),
			array (
				"id" => "17","title" => "其他","count" => val($counts,17,0),
				"href" => "javascript:TabSwitch('other')"
			)
		);
	}
	/**
	 * 保存包裹信息
	 */
	function actionSavepackages(){
		$packages=json_decode(request('packages'),true);
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$quote= new Helper_Quote();
		$data=array();
		if(request('type')=='in'){//应收
			$order->packagenum=$packages['pak'];
			//应收箱子数量
			$order->boxnum=$packages['box'];
			//应收异形包装数量
			$order->specialpackagenum=$packages['special'];
			$order->save();
			$weight_arr = Helper_Quote::getweightarr($order, 1, null, $packages['packages']);
			
			//总计费重
			$cost_weight = $weight_arr['total_cost_weight'];
			
			//总实重
			$actual_weight = $weight_arr['total_real_weight'];
			
			//入库总体积重
			$total_volumn_weight = $weight_arr['total_volumn_weight'];
			
			//1代表本票货物是泡货，只要有一个包裹是泡货，$volumn_chargeable就是1
			$volumn_chargeable = $weight_arr['is_jipao'];
			
			//计算应收价格
			if ($order->customer->customs_code=='ALCN'){
				$cainiaofee = new Helper_CainiaoFee();
				$receivable=$cainiaofee->receivable($order, $cost_weight,$packages['box'],$packages['pak'],$packages['special']);
			}else{
				$receivable=$quote->receivable($order, $cost_weight,$packages['box'],$packages['pak'],$packages['special']);
			}
			if(count($receivable)>0){
				//删除原有应收
				$orderfee=Fee::find("order_id=? and (LENGTH(voucher_no)>0 or account_date<?) and ifnull(account_date,'')!=''  and fee_type = '1'",$order->order_id,strtotime(Config::cbDate()))->getOne();
				if(!$orderfee->isNewRecord()){
					$data['status']='false';
					$data['msg']='有已销账费用或已关账，无法修改';
				}else {
					Fee::find("order_id=? and fee_type = '1' ",$order->order_id)->getAll()->destroy();
					$fee_item_code = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'item_code,sub_code,item_name' )
						->asArray ()
						->getAll (), 'item_code' );
					QLog::log ( print_r ( $receivable, true ) );
					//存入新费用
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
								'fee_type' => 1,
								'fee_item_code' => $fee_item_code [$key] ['sub_code'],
								'fee_item_name' => $fee_item_code [$key] ['item_name'],
								'quantity' => $value ['quantity'],
								'amount' => $value ['fee'],
								'currency'=>$currency_code,
								'rate'=>$rate,
								'account_date' => $order->warehouse_out_time ? $order->warehouse_out_time:time(),
								'btype_id' => $order->customer_id
							) );
							$fee->save ();
						}
					}
					//总计费重
					$order->weight_income_in=$cost_weight;
					//总实重
					$order->weight_actual_in=$actual_weight;
					//1代表本票货物是泡货，只要有一个包裹是泡货，$volumn_chargeable就是1
					$order->volumn_chargeable=$volumn_chargeable;
					//入库总体积重
					$order->total_volumn_weight=$total_volumn_weight;
					//                 $order->related_ali_order_no=request('related_ali_order_no');
					$order->save();
					//记录日志
					$farpackage_old_info=Farpackage::find('order_id=?',$order->order_id)->getAll();
					$log_string='';
					$log=array();
					foreach ($farpackage_old_info as $k=>$temp){
						$log[$k]['FAR']=$temp ['quantity'].'*'.$temp ['length'].'*'.$temp ['width'].'*'.$temp ['height'].'*'.$temp ['weight'];
					}
					foreach ($packages['packages'] as $k=>$temp){
						$log[$k]['FARNEW']=$temp ['quantity'].'*'.$temp ['length'].'*'.$temp ['width'].'*'.$temp ['height'].'*'.$temp ['weight'];
					}
					$num=1;
					foreach ($log as $k=>$v){
						$v['FAR']=isset($v['FAR'])?$v['FAR']:'新增包裹';
						$v['FARNEW']=isset($v['FARNEW'])?$v['FARNEW']:'删除包裹';
						$log_string.=" 第".$num."条数据：".$v['FAR'].'->'.$v['FARNEW'];
						$num++;
					}
					$order_log= new OrderLog();
					$order_log->changeProps(array(
						'order_id'=>$order->order_id,
						'staff_id'=>MyApp::currentUser('staff_id'),
						'staff_name'=>MyApp::currentUser('staff_name'),
						'comment'=>"泛远包裹:".$log_string
					));
					$order_log->save();
					//删除原有包裹信息
					Farpackage::find('order_id=?',$order->order_id)->getAll()->destroy();
					//存入包裹信息
					$package_total_in=0;
					foreach ($packages['packages'] as $temp){
						$far_package = new Farpackage ();
						$far_package->order_id = $order->order_id;
						$far_package->weight = $temp ['weight'];
						$far_package->length = $temp ['length'];
						$far_package->width = $temp ['width'];
						$far_package->height = $temp ['height'];
						$far_package->quantity = $temp ['quantity'];
						$far_package->save ();
						$package_total_in += $value ['quantity'];
					}
					$order->package_total_in=$package_total_in;
					$order->save();
					$data['status']='true';
					$data['msg']='包裹信息修改成功';
				}
			}else{
				$data['status']='false';
				$data['msg']='无法计算价格';
			}
		}else if(request('type')=='out'){//应付
			//记录日志
			$farpackage_old_info=Faroutpackage::find('order_id=?',$order->order_id)->getAll();
			$log_string='';
			$log=array();
			foreach ($farpackage_old_info as $k=>$temp){
				$log[$k]['FAR']=$temp ['quantity_out'].'*'.$temp ['length_out'].'*'.$temp ['width_out'].'*'.$temp ['height_out'].'*'.$temp ['weight_out'];
			}
			foreach ($packages['packages'] as $k=>$temp){
				$log[$k]['FARNEW']=$temp ['quantity'].'*'.$temp ['length'].'*'.$temp ['width'].'*'.$temp ['height'].'*'.$temp ['weight'];
			}
			$num=1;
			foreach ($log as $k=>$v){
				$v['FAR']=isset($v['FAR'])?$v['FAR']:'新增包裹';
				$v['FARNEW']=isset($v['FARNEW'])?$v['FARNEW']:'删除包裹';
				$log_string.=" 第".$num."条数据：".$v['FAR'].'->'.$v['FARNEW'];
				$num++;
			}
			$order_log= new OrderLog();
			$order_log->changeProps(array(
				'order_id'=>$order->order_id,
				'staff_id'=>MyApp::currentUser('staff_id'),
				'staff_name'=>MyApp::currentUser('staff_name'),
				'comment'=>"渠道包裹:".$log_string
			));
			$order_log->save();
			//删除原有包裹信息
			Faroutpackage::find("order_id=?",$order->order_id)->getAll()->destroy();
			$actual_weight_out=0;
			//存入包裹信息
			foreach ($packages['packages'] as $temp){
				$far_out_package = new Faroutpackage();
				$far_out_package->order_id = $order->order_id;
				$far_out_package->weight_out = $temp ['weight'];
				$far_out_package->length_out = $temp ['length'];
				$far_out_package->width_out = $temp ['width'];
				$far_out_package->height_out = $temp ['height'];
				$far_out_package->quantity_out = $temp ['quantity'];
				$far_out_package->save ();
				$actual_weight_out+=$temp ['quantity']*$temp ['weight'];
			}
			//写入计费重
			if($packages['weight_cost_out']>0){
				$weight_outarr = Helper_Quote::getweightarr($order, 2);
				$order->weight_cost_out = $weight_outarr['total_cost_weight'];
				$order->weight_actual_out = $weight_outarr['total_real_weight'];
				$order->total_out_volumn_weight = $weight_outarr['total_volumn_weight'];
				//                 $order->related_ali_order_no=request('related_ali_order_no');
				$order->save();
				$special_fee=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
				if(!$special_fee->isNewRecord()){
					//存入操作日志
					$order_log= new OrderLog();
					$order_log->changeProps(array(
						'order_id'=>$order->order_id,
						'staff_id'=>MyApp::currentUser('staff_id'),
						'staff_name'=>MyApp::currentUser('staff_name'),
						'comment'=>'渠道异形包装费数量：'.$special_fee->quantity.' > '.$packages['special']
					));
					$order_log->save();
					$special_fee->quantity=$packages['special'];
					$special_fee->save();
				}else {
					if($packages['special']>0){
						$fee = new Fee ( array (
							'order_id' => $order->order_id,
							'fee_type' => 2,
							'fee_item_code' => 'logisticsExpressASP_EX0034',
							'fee_item_name' => '异形包装费',
							'quantity' => $packages['special'],
							'account_date' => $order->warehouse_out_time ?  $order->warehouse_out_time:time(),
							'btype_id' => $order->channel->supplier_id
						) );
						$fee->save ();
						//存入操作日志
						$order_log= new OrderLog();
						$order_log->changeProps(array(
							'order_id'=>$order->order_id,
							'staff_id'=>MyApp::currentUser('staff_id'),
							'staff_name'=>MyApp::currentUser('staff_name'),
							'comment'=>'渠道异形包装费数量：0 > '.$packages['special']
						));
						$order_log->save();
					}
				}
			}else{
				//判断是否存在异形包装费。如果存在先存入一条应付异型包装费用
				//删除原有的异形包装费
				$orderfee=Fee::find("order_id=? and (LENGTH(voucher_no)>0 or account_date<?) and ifnull(account_date,'')!='' and fee_type = '2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id,strtotime(Config::cbDate()))->getOne();
				if(!$orderfee->isNewRecord()){
					$data['status']='false';
					$data['msg']='有已销账费用或已关账，无法修改';
					echo json_encode($data);
					exit();
				}
				Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getAll()->destroy();
				if($packages['special']>0){
					$fee = new Fee ( array (
						'order_id' => $order->order_id,
						'fee_type' => 2,
						'fee_item_code' => 'logisticsExpressASP_EX0034',
						'fee_item_name' => '异形包装费',
						'quantity' => $packages['special'],
						'account_date' => $order->warehouse_out_time ? $order->warehouse_out_time:time(),
						'btype_id' => $order->channel->supplier_id
					) );
					$fee->save ();
				}
			}
			$data['status']='true';
			$data['msg']='包裹信息修改成功';
		}
		echo json_encode($data);
		exit();
	}
	/**
	 * 扣件扫描
	 */
	function actionIssuepackagescan(){
		if(request_is_post()){
			//判断阿里单号是否存在
			$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
			if($order->isNewRecord()){
				return $this->_redirectMessage('问题件扫描', '保存失败，阿里单号不存在', url('/issuepackagescan'));
			}
			if($order->order_status=='12'){
				return $this->_redirectMessage('问题件扫描', '保存失败，已扣件', url('/issuepackagescan'));
			}
			$now='ISSUE'.date('Ym');
			$seq = Helper_Seq::nextVal ( $now );
			if ($seq < 1) {
				Helper_Seq::addSeq ( $now );
				$seq = 1;
			}
			$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
			$abnormal_parcel_no=date('Ym').$seq;
			$abnormal_parcel=new Abnormalparcel( array (
				'ali_order_no'=>request('ali_order_no'),
				'abnormal_parcel_no'=>$abnormal_parcel_no,
				'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
				'issue_type'=>request('issue_type'),
				'issue_content'=>request('detail')
			));
			if (request('deadline')){
				$abnormal_parcel->deadline=strtotime(request('deadline'));
			}
			$abnormal_parcel->save();
			//写入订单状态
			$order->order_status_copy=$order->order_status;
			$order->order_status='12';
			$order->save();
			$history=new Abnormalparcelhistory();
			$history->abnormal_parcel_id=$abnormal_parcel->abnormal_parcel_id;
			$history->follow_up_content=request('detail');
			$history->follow_up_operator=MyApp::currentUser("staff_name");
			$history->save();
			return $this->_redirectMessage('问题件扫描', '保存成功', url('/issuepackagescan'));
		}
	}
	/**
	 * 批量扣件
	 */
	function actionBatchissue(){
		
		$ids = request('batchIds');
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		//开启事务
		$conn = QDB::getConn();
		$conn->startTrans();
		try {
			foreach ($orders as $order){
				if($order->order_status=='12'||$order->order_status=='9'){
				}else{
					$now='ISSUE'.date('Ym');
					$seq = Helper_Seq::nextVal ( $now );
					if ($seq < 1) {
						Helper_Seq::addSeq ( $now );
						$seq = 1;
					}
					$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
					$abnormal_parcel_no=date('Ym').$seq;
					$abnormal_parcel=new Abnormalparcel( array (
						'ali_order_no'=>$order->ali_order_no,
						'abnormal_parcel_no'=>$abnormal_parcel_no,
						'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
						'issue_type'=>request('issue_type'),
						'issue_content'=>request('detail')
					));
					if (request('deadline')){
						$abnormal_parcel->deadline=strtotime(request('deadline'));
					}
					$abnormal_parcel->save();
					//写入订单状态
					$order->order_status_copy=$order->order_status;
					$order->order_status='12';
					$order->save();
					$history=new Abnormalparcelhistory();
					$history->abnormal_parcel_id=$abnormal_parcel->abnormal_parcel_id;
					$history->follow_up_content=request('detail');
					$history->follow_up_operator=MyApp::currentUser("staff_name");
					$history->save();
				}
			}
			$conn->completeTrans( true );
		}catch (Exception $e){
			$conn->completeTrans( false );
			echo json_encode('failed');
			exit();
		}
		echo json_encode('success');
		exit();
		
	}
	/**
	 * @todo 批量发送邮件
	 * @author 吴开龙
	 * @since 2020/06/10
	 * @param batchIds:订单ids，emil_id：邮件模板id
	 * @return json
	 * @link #80288
	 */
	function actionBatchEmil(){
		ob_end_clean ();
		$ids = request('batchIds');
		$ids = explode(',',$ids);
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		$email_template = EmailTemplate::find('id = ?',request('emil_id'))->getOne();
		if($email_template->isNewRecord()){
			echo '邮件模板不存在';
			exit;
		}
		echo "正在发送邮件：<br>";
		flush ();
		foreach ( $orders as $order ) {
			if(!$order->sender_email){
				echo '订单：'.$order->ali_order_no.'无收件人邮箱<br>';
				continue;
			}
			$title = $email_template->template_title;
			$email_info = $email_template->template_text;
			
			$postalbook = postalbook::find ( 'code_word_two = ? and channel_id = ?', $order->consignee_country_code, $order->channel_id )->getOne ();
			$track = Controller_Product::getTracking ( $order );
			//标题
			$template_title = preg_replace ( '/ali_order_no/', $order->ali_order_no, $title );
			$template_title = preg_replace ( '/service_name/', $order->service_product->product_chinese_name, $template_title );
			$template_title = preg_replace ( '/tracking_no/', $order->tracking_no, $template_title );
			$template_title = preg_replace ( '/reference_no/', $order->reference_no, $template_title );
			if (strlen ( $order->channel->trace_network_code ) > 0) {
				$template_title = preg_replace ( '/trace_network_code/', $order->channel->trace_network_code, $template_title );
			} else {
				$template_title = preg_replace ( '/trace_network_code/', $order->channel->network_code, $template_title );
			}
			$template_title = preg_replace ( '/network_code/', $order->channel->network_code, $template_title );
			$template_title = preg_replace ( '/consignee_country_code/', $order->consignee_country_code, $template_title );
			$template_title = preg_replace ( '/servicetel/', $postalbook->servicetel, $template_title );
			$template_title = preg_replace ( '/servicesch/', $postalbook->servicesch, $template_title );
			$template_title = preg_replace ( '/customtel/', $postalbook->customtel, $template_title );
			$template_title = preg_replace ( '/track1/', @$track [0], $template_title );
			$template_title = preg_replace ( '/track2/', @$track [1], $template_title );
			$template_title = preg_replace ( '/track3/', @$track [2], $template_title );
			$deprtment_name = $order->department_id?$order->department->department_name:'';
			$template_title = preg_replace('/warehouse/',$deprtment_name,$template_title);
			//内容
			$template_info = preg_replace ( '/ali_order_no/', $order->ali_order_no, $email_info );
			$template_info = preg_replace ( '/service_name/', $order->service_product->product_chinese_name, $template_info );
			$template_info = preg_replace ( '/tracking_no/', $order->tracking_no, $template_info );
			$template_info = preg_replace ( '/reference_no/', $order->reference_no, $template_info );
			if (strlen ( $order->channel->trace_network_code ) > 0) {
				$template_info = preg_replace ( '/trace_network_code/', $order->channel->trace_network_code, $template_info );
			} else {
				$template_info = preg_replace ( '/trace_network_code/', $order->channel->network_code, $template_info );
			}
			$template_info = preg_replace ( '/network_code/', $order->channel->network_code, $template_info );
			$template_info = preg_replace ( '/consignee_country_code/', $order->consignee_country_code, $template_info );
			$template_info = preg_replace ( '/servicetel/', $postalbook->servicetel, $template_info );
			$template_info = preg_replace ( '/servicesch/', $postalbook->servicesch, $template_info );
			$template_info = preg_replace ( '/customtel/', $postalbook->customtel, $template_info );
			$template_info = preg_replace ( '/track1/', @$track [0], $template_info );
			$template_info = preg_replace ( '/track2/', @$track [1], $template_info );
			$template_info = preg_replace ( '/track3/', @$track [2], $template_info );
			$template_info = preg_replace('/warehouse/',$deprtment_name,$template_info);
			QLog::log ( $template_title );
			QLog::log ( $template_info );
			$title = nl2br ( $template_title );
			$msg = nl2br ( $template_info );
			$email_response = Helper_Mailer::sendtemplate ( $order->sender_email, $title, $msg );
			QLog::log ( $email_response );
			if ($email_response == 'email_success') {
				$order_log = new OrderLog ();
				$order_log->order_id = $order->order_id;
				$order_log->staff_name = '系统';
				$order_log->comment = '已发送邮件，标题：' . $template_title . '  内容：' . $template_info;
				$order_log->save ();
				echo '订单：'.$order->ali_order_no.'邮件发送成功<br>';
			}else{
				echo '订单：'.$order->ali_order_no.'邮件发送失败:'.$email_response.'<br>';
			}
			flush ();
		}
		echo "<br>结束";
		exit();
	}
	/**
	 * @todo 批量转入其他
	 * @author 吴开龙
	 * @since 2020/06/12
	 * @param batchIds:订单ids
	 * @return json
	 * @link #80106
	 */
	function actionOther(){
		ob_end_clean ();
		$ids = request('batchIds');
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		foreach ( $orders as $order ) {
			$order->order_status = '17';
			$order->get_trace_flag = '3';
			$order->save();
		}
		echo json_encode('success');
		exit;
	}
	/**
	 * @todo   批量转成已支付
	 * @author stt
	 * @since  2020-10-22
	 * @param  batchIds:订单ids
	 * @return json
	 * @link   #83292
	 */
	function actionTransferpaid(){
		ob_end_clean ();
		$ids = request('batchIds');
		//批量操作的订单
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		foreach ( $orders as $order ) {
			//订单不是阿里订单
			if ($order->customer_id!=1){
				//4已支付
				$order->order_status = '4';
				//支付时间
				$order->payment_time = time();
				$order->save();
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
		}
		echo json_encode('success');
		exit;
	}
	/**
	 * @todo   批量退回已核查
	 * @author stt
	 * @since  2020-10-22
	 * @param  batchIds:订单ids
	 * @return json
	 * @link   #83292
	 */
	function actionReturnchecked(){
		ob_end_clean ();
		$ids = request('batchIds');
		//批量操作的订单
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		foreach ( $orders as $order ) {
			//订单不是阿里订单
			if ($order->customer_id!=1){
				//10已核查
				$order->order_status = '10';
				//清空支付时间
				$order->payment_time = null;
				$order->save();
			}
		}
		echo json_encode('success');
		exit;
	}
	/**
	 * @todo   批量转签收
	 * @author stt
	 * @since  2020-11-16
	 * @param  batchIds:订单ids
	 * @return json
	 * @link   #83831
	 */
	function actionTransfersign(){
		ob_end_clean ();
		$ids = request('batchIds');
		//批量操作的订单
		$orders=Order::find('order_id in (?)',$ids)->getAll();
		foreach ( $orders as $order ) {
			//9已签收
			$order->order_status = '9';
			//签收时间
			$order->delivery_time = time();
			$order->save();
		}
		echo json_encode('success');
		exit;
	}
	
	/**
	 * 退件列表
	 */
	function actionReturnlist(){
		$returnlist=Orderreturn::find()
		->joinleft('tb_order','','tb_return.ali_order_no=tb_order.ali_order_no')
		->where('tb_order.department_id in (?)',RelevantDepartment::relateddepartmentids());
		//日期
		if(request("start_date")){
			$returnlist->where("tb_return.create_time >=?",strtotime(request("start_date").' 00:00:00'));
		}
		if (request("end_date")){
			$returnlist->where("tb_return.create_time <=?",strtotime(request("end_date").' 23:59:59'));
		}
		//阿里单号
		if(request('ali_order_no')){
			$returnlist->where('tb_return.ali_order_no=?',request('ali_order_no'));
		}
		if(request('return_no')){
			$returnlist->where('tb_return.return_no=?',request('return_no'));
		}
		//入库仓
		if(request('department_id')){
			$returnlist->where('tb_order.department_id = ?',request('department_id'));
		}
		//订单状态
		if(request('return_status')){
			$returnlist->where('return_status=?',request('return_status'));
		}
		//状态
		if(request('state')){
			$returnlist->where('state=?',request('state'));
		}
		//导出退件单
		if(request('export')=='exportlist'){
			$select=clone $returnlist;
			//设置导出表单head
			$sheet = array ( array("退件编号","阿里单号","取件网点","入库仓","退件范围","退件状态","货物流向","发起人","收件人","收件人电话","承运商","单号","发起时间"));
			$details=$select->getAll ();
			foreach ($details as $value){
				$cargo_direction='';
				switch ($value->cargo_direction){
					case 1 :
						$cargo_direction='快递退货';
						break;
					case 2 :
						$cargo_direction='换单重发';
						break;
					case 3 :
						$cargo_direction='班车退回';
						break;
					case 4 :
						$cargo_direction='客户自取';
						break;
				};
				$sheet [] = array (
					$value->return_no,
					$value->ali_order_no,
					$value->order->pick_company,
					$value->order->department->department_name,
					$value->return_status=='1'?"全部退":"部分退",
					$value->state=='1'?"待退货":"已退货",
					$cargo_direction,
					$value->return_operator,
					$value->consignee_name,
					$value->consignee_phone,
					$value->express_company,
					"'".$value->express_no,
					$value->create_time?date('Y-m-d',$value->create_time):''
				);
			}
			//Helper_Excel::array2xls(数据表，导出表名)
			Helper_Excel::array2xls ( $sheet,'退件列表导出'.'~'.date('Ymd',time()).'.xlsx' );
			exit();
		}
		
		//查询当前登录人员业务相关部门
		$relevant_departments=Helper_Array::getCols(RelevantDepartment::find('staff_id=?',MyApp::currentUser('staff_id'))->getAll(), 'department_id');
		//获取部门名称
		$departments=Helper_Array::toHashmap(Department::find('department_id in (?)',$relevant_departments)->getAll(), 'department_id', 'department_name');
		
		$pagination = null;
		$returnlist=$returnlist->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('return_id desc')->getAll();
		$this->_view['returnlist']=$returnlist;
		$this->_view['departments']=$departments;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * 退件
	 */
	function actionOrderreturn(){
		$return_id=request('return_id');
		$order_return=Orderreturn::find('return_id=?',$return_id)->getOne();
		$order=Order::find('ali_order_no=?',request("ali_order_no"))->getOne();
		if(request_is_post()){
			if ($order_return->isNewRecord()){
				$now='RETURN'.date('Ym');
				$seq = Helper_Seq::nextVal ( $now );
				if ($seq < 1) {
					Helper_Seq::addSeq ( $now );
					$seq = 1;
				}
				$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
				$return_parcel_no='R'.date('Ym').$seq;
				$order_return->ali_order_no=request('ali_order_no');
				$order_return->return_no=$return_parcel_no;
				$order_return->return_operator=MyApp::currentUser('staff_name');
			}
			$order_return->consignee_name=request('consignee_name');
			$order_return->return_status=request('return_status');
			$order_return->consignee_phone=request('consignee_phone');
			$order_return->consignee_address=request('consignee_address');
			$order_return->express_no=request('express_no');
			$order_return->express_company=request('express_company');
			//状态和备注保存
			$order_return->state=request('state');
			$order_status = $order->order_status;
			if(request('return_status')=='1'){
				if(request('state')=='1'){
					$order->order_status='11';
				}else{
					$order->order_status='3';
				}
			}
			$order_return->cargo_direction=request('cargo_direction');
			$order_return->remark=request('remark');
			$order_return->save();
			if(request('related_ali_order_no')){
				$order->related_ali_order_no=request('related_ali_order_no');
				$order->save();
			}
			if (request('state')=='2'&&$order_status=='3'){
				$log=new OrderLog();
				$log->order_id=$order->order_id;
				$log->comment='订单状态:已退货 > 已退货';
				$log->save();
			}
			if(request('flag')=='1'){
				//已全退
				if(request('return_status')=='1' && $order_status<>'3'){
					//没有核查的订单删除所有费用
					if (!$order->warehouse_confirm_time) {
						$shou='删除收入';
						$fu='成本';
						foreach ($order->fees as $fee){
							if($fee->fee_type=='1'){
								$shou.=$fee->fee_item_code.'*'.$fee->quantity.';';
							}else{
								$fu.=$fee->fee_item_code.'*'.$fee->quantity.';';
							}
						}
						if(strlen($shou)>8){
							$log=new OrderLog();
							$log->order_id=$order->order_id;
							$log->comment=$fu=='成本'?$shou:$shou.$fu;
							$log->save();
						}
						//删除所有相关费用
						Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
					}else{
						//核查过的订单删除成本
						$feeouts = Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll();
						foreach ($feeouts as $feeout){
							if($feeout->invoice_no || $feeout->voucher_no){
								$f = new Fee(array(
									'order_id' => $feeout->order_id,
									'btype_id' => $feeout->btype_id,
									'fee_type' => $feeout->fee_type,
									'fee_item_code' => $feeout->fee_item_code,
									'fee_item_name' => $feeout->fee_item_name,
									'quantity' => $feeout->quantity,
									'amount' => $feeout->amount*-1,
									'currency' => $feeout->currency,
									'account_date' => time(),
								));
								$f->save();
								$log=new OrderLog();
								$log->order_id=$order->order_id;
								$log->comment='新增成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
								$log->save();
							}else {
								if(!$feeout->account_date || $feeout->account_date > strtotime(Config::cbDate())){
									Fee::meta()->destroyWhere('fee_id = ?',$feeout->fee_id);
									$log=new OrderLog();
									$log->order_id=$order->order_id;
									$log->comment='删除成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
									$log->save();
								}else {
									$f = new Fee(array(
										'order_id' => $feeout->order_id,
										'btype_id' => $feeout->btype_id,
										'fee_type' => $feeout->fee_type,
										'fee_item_code' => $feeout->fee_item_code,
										'fee_item_name' => $feeout->fee_item_name,
										'quantity' => $feeout->quantity,
										'amount' => $feeout->amount*-1,
										'currency' => $feeout->currency,
										'account_date' => time(),
									));
									$f->save();
									$log=new OrderLog();
									$log->order_id=$order->order_id;
									$log->comment='新增成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
									$log->save();
								}
							}
						}
						//核查过的订单存入负应收
						$shouCon = Fee::find("order_id=? and fee_type='1'",$order->order_id)->asArray()->getAll();
						foreach ($shouCon as $k => $v) {
							$v['amount']=-1*$v['amount'];
							$feedata = new fee($v);
							$feedata->save();
							$log=new OrderLog();
							$log->order_id=$order->order_id;
							$log->comment='新增收入'.$feedata->fee_item_code.'*'.$feedata->quantity;
							$log->save();
						}
					}
				}
			}
			
			$order->save();
			return $this->_redirectMessage('退件', '保存成功', url('/orderreturn',array('return_id'=>$order_return->return_id,'ali_order_no'=>$order->ali_order_no)));
		}
		$this->_view['return']=$order_return;
		$this->_view['order']=$order;
	}
	/**
	 * @todo 批量退件
	 * @author 吴开龙
	 * @since 2020-6-18 14:30:05
	 * @param
	 * @return json
	 * @link #80362
	 */
	function actionOrderReturnBatch(){
		if(request_is_post()){
			ini_set ( 'max_execution_time', '0' );
			set_time_limit ( 0 );
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('order/orderreturnbatch'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('order/orderreturnbatch'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheets =$xls->toHeaderMap ();
			// 					echo "<pre>";
			// 					print_r($sheets);
			// 					exit;
			$error = array();
			foreach ($sheets as $k => $s){
				$order=Order::find("ali_order_no=?",$s['阿里单号'])->getOne();
				if($order->isNewRecord()){
					$error[$k+2]['阿里单号'] = '阿里单号['.$s['阿里单号'].']不存在';
				}
				if(request('cargo_direction') == '2'){
					if(!$s['新ALS订单号']){
						$error[$k+2]['新ALS订单号'] = '换单重发须填写新ALS订单号';
					}
				}
				if(request('cargo_direction') == '1'){
					if(!$s['收件人'] || !$s['收件人手机号'] || !$s['收件人地址'] || !$s['快递单号'] || !$s['快递公司']){
						$error[$k+2]['收件人信息'] = '快递退货必须填写收件人信息';
					}
				}
			}
			$this->_view ['error'] = $error;
			if (empty ( $error )) {
				foreach ($sheets as $k => $sheet){
					//$order_return=Orderreturn::find('return_id=?',$return_id)->getOne();
					$order=Order::find('ali_order_no=?',$sheet['阿里单号'])->getOne();
					$order_return = new Orderreturn();
					$now='RETURN'.date('Ym');
					$seq = Helper_Seq::nextVal ( $now );
					if ($seq < 1) {
						Helper_Seq::addSeq ( $now );
						$seq = 1;
					}
					$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
					$return_parcel_no='R'.date('Ym').$seq;
					$order_return->ali_order_no=$sheet['阿里单号'];
					$order_return->return_no=$return_parcel_no;
					$order_return->return_operator=MyApp::currentUser('staff_name');
					$order_return->consignee_name=$sheet['收件人'];
					$order_return->return_status=request('return_status');
					$order_return->consignee_phone=$sheet['收件人手机号'];
					$order_return->consignee_address=$sheet['收件人地址'];
					$order_return->express_no=$sheet['快递单号'];
					$order_return->express_company=$sheet['快递公司'];
					$order_return->state=request('state');
					$order_return->cargo_direction=request('cargo_direction');
					$order_return->remark=$sheet['备注'];
					$order_return->save();
					//状态
					$order_return->state=request('state');
					$order_status = $order->order_status;
					if(request('return_status')=='1'){
						if(request('state')=='1'){
							$order->order_status='11';
						}else{
							$order->order_status='3';
						}
					}
					//换单重发保存新单号
					if(request('cargo_direction') == '2'){
						$order->related_ali_order_no=$sheet['新ALS订单号'];
					}
					$order->save();
					if (request('state')=='2'&&$order_status=='3'){
						$log=new OrderLog();
						$log->order_id=$order->order_id;
						$log->comment='订单状态:已退货 > 已退货';
						$log->save();
					}
					//国内退件
					if(request('flag')=='1'){
						//已全退
						if(request('return_status')=='1' && $order_status<>'3'){
							//没有核查的订单删除所有费用
							if (!$order->warehouse_confirm_time) {
								$shou='删除收入';
								$fu='成本';
								foreach ($order->fees as $fee){
									if($fee->fee_type=='1'){
										$shou.=$fee->fee_item_code.'*'.$fee->quantity.';';
									}else{
										$fu.=$fee->fee_item_code.'*'.$fee->quantity.';';
									}
								}
								if(strlen($shou)>8){
									$log=new OrderLog();
									$log->order_id=$order->order_id;
									$log->comment=$fu=='成本'?$shou:$shou.$fu;
									$log->save();
								}
								//删除所有相关费用
								Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
							}else{
								//核查过的订单删除成本
								$feeouts = Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll();
								foreach ($feeouts as $feeout){
									if($feeout->invoice_no || $feeout->voucher_no){
										$f = new Fee(array(
											'order_id' => $feeout->order_id,
											'btype_id' => $feeout->btype_id,
											'fee_type' => $feeout->fee_type,
											'fee_item_code' => $feeout->fee_item_code,
											'fee_item_name' => $feeout->fee_item_name,
											'quantity' => $feeout->quantity,
											'amount' => $feeout->amount*-1,
											'currency' => $feeout->currency,
											'account_date' => time(),
										));
										$f->save();
										$log=new OrderLog();
										$log->order_id=$order->order_id;
										$log->comment='新增成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
										$log->save();
									}else {
										if(!$feeout->account_date || $feeout->account_date > strtotime(Config::cbDate())){
											Fee::meta()->destroyWhere('fee_id = ?',$feeout->fee_id);
											$log=new OrderLog();
											$log->order_id=$order->order_id;
											$log->comment='删除成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
											$log->save();
										}else {
											$f = new Fee(array(
												'order_id' => $feeout->order_id,
												'btype_id' => $feeout->btype_id,
												'fee_type' => $feeout->fee_type,
												'fee_item_code' => $feeout->fee_item_code,
												'fee_item_name' => $feeout->fee_item_name,
												'quantity' => $feeout->quantity,
												'amount' => $feeout->amount*-1,
												'currency' => $feeout->currency,
												'account_date' => time(),
											));
											$f->save();
											$log=new OrderLog();
											$log->order_id=$order->order_id;
											$log->comment='新增成本'.$feeout->fee_item_code.'*'.$feeout->quantity;
											$log->save();
										}
									}
								}
								//核查过的订单存入负应收
								$shouCon = Fee::find("order_id=? and fee_type='1'",$order->order_id)->asArray()->getAll();
								foreach ($shouCon as $k => $v) {
									$v['amount']=-1*$v['amount'];
									$feedata = new fee($v);
									$feedata->save();
									$log=new OrderLog();
									$log->order_id=$order->order_id;
									$log->comment='新增收入'.$feedata->fee_item_code.'*'.$feedata->quantity;
									$log->save();
								}
							}
						}
					}
					
				}
				return $this->_redirectMessage ( '批量退件成功', '成功', url ( 'order/returnlist' ), 3 );
			}
		}
	}
	/**
	 * 问题件列表
	 */
	function actionIssue(){
		$parcles=Abnormalparcel::find()
		->joinLeft('tb_order', '', 'tb_order.ali_order_no = tb_abnormal_parcel.ali_order_no')
		->where('tb_order.department_id in (?) or location in (?)',RelevantDepartment::relateddepartmentids(),RelevantDepartment::relateddepartmentids());
		
		//日期
		if(request("start_date")){
			$parcles->where("tb_abnormal_parcel.create_time >=?",strtotime(request("start_date").' 00:00:00'));
		}
		if (request("end_date")){
			$parcles->where("tb_abnormal_parcel.create_time <=?",strtotime(request("end_date").' 23:59:59'));
		}
		if(request('department_id')){
			$parcles->where("tb_order.department_id =? or tb_abnormal_parcel.location = ?",request('department_id'),request('department_id'));
		}
		if(request('service_code')){
			$parcles->where("tb_order.service_code =? ",request('service_code'));
		}
		if(request('order_status')){
			$parcles->where("tb_order.order_status =? ",request('order_status'));
		}
		//阿里单号、末端单号
		if(request('ali_order_no')){
			$ali_order_no=explode("\r\n", request('ali_order_no'));
			$ali_order_no=array_filter($ali_order_no);//去空
			$ali_order_no=array_unique($ali_order_no);//去重
			$parcles->where('tb_order.ali_order_no in (?) or tb_order.tracking_no in (?)',$ali_order_no,$ali_order_no);
		}
		//国内快递单号
		if(request('reference_no')){
			$parcles->where('tb_order.reference_no like ? or tb_abnormal_parcel.reference_no like ?',"%".request('reference_no')."%","%".request('reference_no')."%");
		}
		//订单状态
		if(request('parcel_flag',1)){
			$parcles->where('parcel_flag=?',request('parcel_flag',1));
		}
		//网络
		if(request('network')){
			$parcles->joinLeft('tb_channel','', 'tb_channel.channel_id = tb_order.channel_id')
			->where('tb_channel.network_code=?',request('network'));
		}
		//发件人信息
		if(request('sender')){
			$parcles->where('tb_order.sender_name1 like "%'.request('sender').'%" or tb_order.sender_name2 like "%'.request('sender').'%" or tb_order.sender_mobile like "%'.request('sender').'%" or tb_order.sender_telephone like "%'.request('sender').'%" or tb_order.sender_email like "%'.request('sender').'%"');
		}
		//渠道异常
		if(request('headlinetype')){
			$parcles->joinLeft('tb_abnormal_parcel_headline', '','tb_abnormal_parcel_headline.abnormal_parcel_id=tb_abnormal_parcel.abnormal_parcel_id')
			->where('tb_abnormal_parcel_headline.headline_id in (?)',request('headlinetype'));
		}
		$counts = array ();
		// 全部总数
		$order_count=clone $parcles;
		$counts [0] = count($order_count->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		// 取件异常件
		$order_count=clone $parcles;
		$counts [1] = count($order_count->where('issue_type=?',Abnormalparcel::PICK_ISSUE)->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		//库内异常件
		$order_count=clone $parcles;
		$counts [2] = count($order_count->where('issue_type=?',Abnormalparcel::WAREHOUSE_ISSUE)->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		// 渠道异常件
		$order_count=clone $parcles;
		$counts [3] = count($order_count->where('issue_type=?',Abnormalparcel::CHANNEL_ISSUE)->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		// 无主件
		$order_count=clone $parcles;
		$counts [4] = count($order_count->where('issue_type=?',Abnormalparcel::OWN_ISSUE)->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		// 港前异常件
		$order_count=clone $parcles;
		$counts [5] = count($order_count->where('issue_type=?',Abnormalparcel::BEFOREARRIVE_ISSUE)->group('tb_abnormal_parcel.abnormal_parcel_id')->getAll());
		$active_id = 0;
		// 取件异常件
		if (request ( "parameters" ) == "pick_issue") {
			$parcles->where('issue_type=?',Abnormalparcel::PICK_ISSUE);
			$active_id = 1;
		}
		// 库内异常件
		if (request ( "parameters" ) == "warehouse_issue") {
			$parcles->where('issue_type=?',Abnormalparcel::WAREHOUSE_ISSUE);
			$active_id = 2;
		}
		// 渠道异常件
		if (request ( "parameters" ) == "channel_issue") {
			$parcles->where('issue_type=?',Abnormalparcel::CHANNEL_ISSUE);
			$active_id = 3;
		}
		// 无主件
		if (request ( "parameters" ) == "own_issue") {
			$parcles->where('issue_type=?',Abnormalparcel::OWN_ISSUE);
			$active_id = 4;
		}
		// 港前异常件
		if (request ( "parameters" ) == "beforearrive_issue") {
			$parcles->where('issue_type=?',Abnormalparcel::BEFOREARRIVE_ISSUE);
			$active_id = 5;
		}
		
		// 导出
		if(request('export') == 'export'){
			$status=array('1'=>'取件','2'=>'库内','3'=>'渠道','4'=>'无主件','5'=>'港前');
			$export = clone $parcles;
			$list = $export->order('abnormal_parcel_id desc')->group('abnormal_parcel_id')->getAll();
			$header = array (
				'问题件编号',
				'阿里单号',
				'订单状态',
				request ( "parameters" ) == "own_issue"?'国内快递单号':'末端运单号',
				'问题类型',
				'状态',
				'异常',
				'位置',
				'发起人',
				'发起时间',
				'最后跟进',
				'最后备注'
			);
			$sheet = array (
				$header
			);
			foreach ($list as $v){
				$position = '';
				if($v->issue_type=='1' || $v->issue_type=='2' || $v->issue_type=='5'){
					$position = Department::find('department_id=?',$v->order->department_id)->getOne()->department_name;
				}elseif( $v->issue_type=='3'){
					$position = $v->order->channel->channel_name;
				}elseif( $v->issue_type=='4'){
					$position = Department::find('department_id=?',$v->location)->getOne()->department_name;
				}
				$history=Abnormalparcelhistory::find("abnormal_parcel_id=?",$v->abnormal_parcel_id)->order("create_time desc")->getOne();
				$line='';
				if(request ( "parameters" ) == "channel_issue"){
					$head=abnormalparcelheadline::find('abnormal_parcel_id =?',$v->abnormal_parcel_id)->getAll();
					foreach ($head as $h){
						$ab=headline::find('headline_id =?',$h->headline_id)->getOne();
						if(!$ab->isNewRecord()){
							$line .= ','.$ab->headline;
						}
					}
				}
				$sheet[] = array(
					$v->abnormal_parcel_no,
					$v->ali_order_no,
					Order::$status[$v->order->order_status],
					request ( "parameters" ) == "own_issue"?"'".$v->reference_no:"'".$v->order->tracking_no,
					$status[$v->issue_type],
					$v->parcel_flag=='1'?"开启":($v->parcel_flag=='2'?"关闭":"延置处理"),
					trim($line,','),
					$position,
					$v->abnormal_parcel_operator,
					Helper_Util::strDate('Y-m-d H:i', $v->create_time),
					$history->follow_up_operator,
					'['.date('Y-m-d H:i',$history->create_time).'] '. $history->follow_up_content
				);
			}
			$name = '问题件-';
			if(request ( "parameters" ) == "pick_issue"){
				$name .= '取件异常件';
			}elseif(request ( "parameters" ) == "warehouse_issue"){
				$name .= '库内异常件';
			}elseif(request ( "parameters" ) == "channel_issue"){
				$name .= '渠道异常件';
			}elseif(request ( "parameters" ) == "own_issue"){
				$name .= '无主件';
			}elseif(request ( "parameters" ) == "beforearrive_issue"){
				$name .= '港前异常件';
			}else{
				$name .= '全部';
			}
			Helper_Excel::array2xls ( $sheet, $name.'.xls' );
			exit();
		}
		$now = strtotime(date('Y-m-d'));
		$fiveday  = $now+4*24*3600;
		
		$pagination = null;
		if (request ( "parameters" ) == 'channel_issue'){
			$list=$parcles->limitPage ( request ( "page", 1 ), request ( 'page_size', 25 ) )
			->fetchPagination ( $pagination )
			->order("case when (deadline >= {$now} and deadline <= {$fiveday})then 0 else 1 end ,abnormal_parcel_id desc")
			->group('abnormal_parcel_id')->getAll();
		}else{
			$list=$parcles->limitPage ( request ( "page", 1 ), request ( 'page_size', 25 ) )
			->fetchPagination ( $pagination )
			->order('abnormal_parcel_id desc')
			->group('abnormal_parcel_id')->getAll();
		}
		$parameters=request ( "parameters" );
		$this->_view['parcels']=$list;
		$this->_view['pagination']=$pagination;
		$this->_view ["counts"] = $counts;
		$this->_view ["parameters"] = $parameters;
		$this->_view ["active_id"] = $active_id;
		$this->_view ["tabs"] = $this->createIssueTabs ( $counts );
	}
	/**
	 * 创建异常件标签
	 */
	function createIssueTabs($counts) {
		if(MyApp::currentUser('department_id')=='23'){
			return array (
				array (
					"id" => "2","title" => "库内异常件","count" => $counts [2],
					"href" => "javascript:TabSwitch('warehouse_issue')"
				)
			);
		}
		return array (
			array (
				"id" => "0","title" => "全部","count" => $counts [0],
				"href" => "javascript:TabSwitch()"
			),
			array (
				"id" => "1","title" => "取件异常件","count" => $counts [1],
				"href" => "javascript:TabSwitch('pick_issue')"
			),
			array (
				"id" => "2","title" => "库内异常件","count" => $counts [2],
				"href" => "javascript:TabSwitch('warehouse_issue')"
			),
			array (
				"id" => "3","title" => "渠道异常件","count" => $counts [3],
				"href" => "javascript:TabSwitch('channel_issue')"
			),
			array (
				"id" => "4","title" => "无主件","count" => $counts [4],
				"href" => "javascript:TabSwitch('own_issue')"
			),
			array (
				"id" => "5","title" => "港前异常件","count" => $counts [5],
				"href" => "javascript:TabSwitch('beforearrive_issue')"
			)
		);
	}
	
	/**
	 *
	 * 异常件跟进
	 */
	function actionIssueHistory(){
		if(request_is_post()){
			$abnormal=Abnormalparcel::find("abnormal_parcel_id=?",request("abnormal_parcel_id"))->getOne();
			if(!$abnormal->isNewRecord()){
				if(request("parcel_flag")){
					if(request("parcel_flag")=='1'){
						$abnormal->parcel_flag='2';
					}elseif (request("parcel_flag")=='4'){
						$abnormal->parcel_flag='3';
					}elseif (request("parcel_flag")=='3'){
						//关闭并解扣
						$abnormal->parcel_flag='2';
						$order=Order::find('ali_order_no=?',$abnormal->ali_order_no)->getOne();
						if($order->order_status=="12"){
							//存在关联单号
							if($order->related_ali_order_no){
								//本单状态修改为:已取消
								$order->order_status='2';
								//修改本单的毛利为 0
								$order->profit = 0;
								//删除ali_reference表里的快递号信息
								Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
								//删除所有相关费用
								Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
							}else{
								$order->order_status=$order->order_status_copy;
							}
							$order->save();
						}
					}else{
						$abnormal->parcel_flag='1';
					}
				}
				if(request('reason_type')){
					$abnormal->checkabnormal_type = request('reason_type');
				}
				if(request('deadline')){
					$abnormal->deadline = strtotime(request('deadline'));
				}
				$abnormal->issue_type=request("issue_type");
				$abnormal->save();
				$history=new Abnormalparcelhistory();
				$history->abnormal_parcel_id=$abnormal->abnormal_parcel_id;
				$history->follow_up_content=request("follow_up_content");
				$history->follow_up_operator=MyApp::currentUser("staff_name");
				$history->save();
				return $this->_redirectMessage('异常件保存', '成功', url('order/issuehistory',array('abnormal_parcel_id'=>request("abnormal_parcel_id"))));
			}else{
				return $this->_redirectMessage("异常件问题不存在","请仔细核对", url('order/issue'));
			}
		}
		if(request("abnormal_parcel_id")){
			$abnormal_parcel=Abnormalparcel::find("abnormal_parcel_id=?",request("abnormal_parcel_id"))->getOne();
			if(!$abnormal_parcel->isNewRecord()){
				$this->_view['abnormal_parcel']=$abnormal_parcel;
			}else{
				return $this->_redirectMessage("异常件问题不存在","请仔细核对", url('order/issue'));
			}
			
		}else{
			return $this->_redirectMessage("异常件问题不存在","请仔细核对", url('order/issue'));
		}
	}
	
	/**
	 * @todo 批量关闭问题件
	 * @author 吴开龙
	 * @since 2020-6-17 15:47:53
	 * @param
	 * @return json
	 * @link #80281
	 */
	function actionIssueBatchClose(){
		if(request_is_post()){
			ini_set ( 'max_execution_time', '0' );
			set_time_limit ( 0 );
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('order/issuebatchclose'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('order/issuebatchclose'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheets =$xls->toHeaderMap ();
			// 		echo "<pre>";
			// 		print_r($sheets);
			// 		exit;
			$error = array();
			foreach ($sheets as $k => $s){
				$abnormal=Abnormalparcel::find("ali_order_no=? and parcel_flag=1",$s['阿里单号'])->getOne();
				if($abnormal->isNewRecord()){
					$error[$k+2]['阿里单号'] = '阿里单号['.$s['阿里单号'].']问题单不存在';
				}
				if(!$s['跟进内容']){
					$error[$k+2]['跟进内容'] = '阿里单号['.$s['阿里单号'].']跟进内容不能为空';
				}
			}
			$this->_view ['error'] = $error;
			if (empty ( $error )) {
				foreach ($sheets as $sheet){
					$abnormal=Abnormalparcel::find("ali_order_no=? and parcel_flag=1",$sheet['阿里单号'])->getOne();
					$abnormal->parcel_flag='2';
					$abnormal->save();
					$history=new Abnormalparcelhistory();
					$history->abnormal_parcel_id=$abnormal->abnormal_parcel_id;
					$history->follow_up_content= $sheet['跟进内容'];
					$history->follow_up_operator=MyApp::currentUser("staff_name");
					$history->save();
				}
				return $this->_redirectMessage ( '批量关闭成功', '成功', url ( 'order/issue' ), 3 );
			}
		}
	}
	/**
	 * 解扣验证
	 */
	function actionreleaseverify(){
		if(request_is_post()){
			$order=Order::find('ali_order_no=?',request('ali_order_no'))->getOne();
			$return= Returned::find('ali_order_no=?',request('ali_order_no'))->order('return_id DESC')->getOne();
			$jsoncode=array();
			$weiruku=array(1,14,15,16);
			$jsoncode['related_ali_order_no']='';
			if($order->isNewRecord()){
				$jsoncode['code']='notexist';
			}else{
				if($order->order_status=='12'){//扣件
					$jsoncode['code']=12;
				}else if(!$return->isNewRecord()){//存在于退件列表
					if($return->cargo_direction=='2'){
						$jsoncode['related_ali_order_no']=$order->related_ali_order_no;
						$jsoncode['code']='huandan';
					}else if($return->state=='1'){
						$jsoncode['code']='daituihuoqingchuli';
					}else{
						$jsoncode['code']='abnormal';
					}
				}else if(!empty($order->related_ali_order_no)){//关联账号
					$jsoncode['related_ali_order_no']=$order->related_ali_order_no;
					$jsoncode['code']='related_ali_order_no';
				}else if (in_array($order->order_status, $weiruku)){//未入库
					$jsoncode['code']=1;
				}else if ($order->order_status=='2'){//已取消
					$jsoncode['code']=2;
				}else if ($order->order_status=='3'){//已退货
					$jsoncode['code']=3;
				}else if ($order->order_status=='4'){//已支付
					$jsoncode['code']=4;
				}else if ($order->order_status=='5'){//已入库
					$jsoncode['code']=5;
				}else if ($order->order_status=='6'){//已打印
					$jsoncode['code']=6;
				}else if ($order->order_status=='7'){//已出库
					$jsoncode['code']=7;
				}else if ($order->order_status=='8'){//已提取
					$jsoncode['code']=8;
				}else if ($order->order_status=='9'){//已签收
					$jsoncode['code']=9;
				}else if ($order->order_status=='10'){//已核查
					$jsoncode['code']=10;
				}else if ($order->order_status=='11'){//待退货
					$jsoncode['code']=11;
				}else if ($order->order_status=='13'){//已结束
					$jsoncode['code']=13;
				}else{//异常
					$jsoncode['code']='abnormal';
				}
				$msg="";
				//获取问题件最新的备注信息
				$abnormalparcel=Abnormalparcel::find('ali_order_no=?',request('ali_order_no'))->getOne();
				if(!$abnormalparcel->isNewRecord()){
					$abnormalparcelhis=Abnormalparcelhistory::find('abnormal_parcel_id=?',$abnormalparcel->abnormal_parcel_id)->order('create_time desc')->getOne();
					if(!$abnormalparcelhis->isNewRecord()){
						$msg=$abnormalparcelhis->follow_up_content;
					}
				}
				$jsoncode['msg']=$msg;
			}
			echo json_encode($jsoncode);
			exit();
		}
	}
	/**
	 * 照片压缩包上传
	 */
	function actionPictures() {
		if (request_is_post ()) {
			$uploader = new Helper_Uploader ();
			$file = $uploader->existsFile ( "file" ) ? $uploader->file ( "file" ) : null;
			if ($file) {
				$f = new File ();
				$seq = Helper_Seq::nextVal ( 'file_seq' );
				if ($seq < 1) {
					Helper_Seq::addSeq ( 'file_seq' );
					$seq = 1;
				}
				$now = date ( 'Ymd' ) . $seq;
				//创建文件路径
				$filepath = Q::ini ( "upload_file_dir" ) . "/" . $now . "." . $file->extname ();
				//移动新文件
				$file->move ( $filepath );
				$f->changeProps ( array (
					"file_name" => $file->filename (),"file_path" => $filepath,"operator" => MyApp::currentUser ( "staff_name" )
				) );
				$f->save ();
				//解析zip压缩包
				$file_names=self::unzip_file($filepath, Q::ini ( "upload_file_dir" ),$now);
				return $this->_redirectMessage('照片上传', '上传成功', url('/pictures'));
			}
		}
	}
	/**
	 * 解压文件
	 * @param unknown $filepath
	 * @param unknown $destination
	 */
	static function unzip_file($filepath, $destination,$filename){
		require_once _INDEX_DIR_ .'/_library/phpexcel/PHPExcel/Shared/PCLZip/pclzip.lib.php';
		$archive = new PclZip($filepath);
		if ($archive->extract(PCLZIP_OPT_PATH,$destination.DS.$filename) == 0) {
			die("Error : " . $archive->errorInfo(true));
		}
		$arr_file=array();
		self::readfile($arr_file,$destination.DS.$filename.DS);
		return $arr_file;
	}
	/**
	 * 读取解压后文件名
	 * @param unknown $arr_file
	 * @param unknown $dirname
	 */
	static function readfile(&$arr_file,$dirname){
		$handler = opendir($dirname);//当前目录中的文件夹下的文件夹
		while( ($filename = readdir($handler)) !== false ) {
			if($filename != ".." && $filename != ".") {
				if(is_dir($dirname.$filename)){
					self::readfile($arr_file,$dirname.$filename.DS);
				}else{
					$ali_order_no=substr($filename, 0,14);
					$order=Order::find('ali_order_no=?',$ali_order_no)->getOne();
					if(!$order->isNewRecord()){
						$filepath=str_replace( '\\','/', $dirname.$filename);
						//将文件路径存入file表中
						$f = new File (array (
							"order_id" => $order->order_id,
							"file_name" => $filename,
							"file_path" => $filepath,
							"operator" => MyApp::currentUser ( "staff_name" )
						) );
						$f->save();
						$arr_file[]=$filepath;
					}
				}
			}
		}
	}
	/**
	 * 打印条码
	 */
	function actionBarcode(){
		$wcode=request('wcode');
		if (request_is_post()){
			$new=array();
			foreach (explode("\n", $wcode) as $line){
				$line=str_replace("\r", '', trim($line));
				$new[]=$line;
			}
			$wcode=implode("\r\n", $new);
		}
		$this->_view['wcode']=$wcode;
	}
	
	/**
	 * barcode json
	 */
	function actionBC() {
		$arr = explode ( ',', request ( 'code' ) );
		$ret = array ();
		foreach ( $arr as $row ) {
			$ret [] = array (
				'code' => $row
			);
		}
		echo json_encode ( $ret );
		exit ();
	}
	/**
	 * 检查hscode
	 */
	function actionCheckhs(){
		$hs_code=explode(',', trim(request('hs_code'),','));
		$return_hscode='';
		$flag=false;
		foreach ($hs_code as $temp){
			$check_hs=Hs::find('HSCode=?',$temp)->getOne();
			if($check_hs->isNewRecord()){
				$return_hscode=$temp;
				$flag=true;
				break;
			}
		}
		if($flag){
			echo $return_hscode;
		}else{
			echo 'success';
		}
		exit();
	}
	/**
	 * 检查关联的阿里单号（related_ali_order_no）存不存在
	 */
	function actionCheckrelatedaliorderno(){
		$order=Order::find('ali_order_no=?',request('related_ali_order_no'))->getOne();
		if($order->ali_order_no){
			echo 'success';
		}else{
			echo 'false';
		}
		exit();
	}
	/**
	 * 检查换单重发关联的阿里单号（related_ali_order_no）存不存在
	 */
	function actionCheckrelatedaliordernonew(){
		$order=Order::find('ali_order_no=? and order_status in (1,14,15,16)',request('related_ali_order_no'))->getOne();
		if($order->ali_order_no){
			echo 'success';
		}else{
			echo 'false';
		}
		exit();
	}
	/**
	 * 录入关联的阿里单号（related_ali_order_no）
	 */
	function actionInsertrelatedaliorderno(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		if(request('related_ali_order_no')){
			$order->related_ali_order_no=request('related_ali_order_no');
			$order->save();
			echo 'success';
		}
		exit();
	}
	/**
	 * 问题件上传附件
	 */
	function actionsaveissuefile(){
		$uploader = new Helper_Uploader ();
		$file = $uploader->existsFile ( "file" ) ? $uploader->file ( "file" ) : null;
		if ($file) {
			$now='issue_file'.date('Ym');
			$seq = Helper_Seq::nextVal ( $now);
			if ($seq < 1) {
				Helper_Seq::addSeq ($now);
				$seq = 1;
			}
			$str = 'issue_'.date ( 'Ymd' ) . $seq;
			//创建文件路径
			$filepath = Q::ini ( "upload_file_dir" ) . "/" . $str . "." . $file->extname ();
			//移动新文件
			$file->move ( $filepath );
			$f = new Abnormalparcelfile();
			$f->changeProps ( array (
				"abnormal_parcel_id"=>request('abnormal_parcel_id'),
				"file_name" => $file->filename (),
				"file_path" => $filepath,
				"operator" => MyApp::currentUser ( "staff_name" )
			) );
			$f->save ();
			return $this->_redirectMessage('附件上传', '上传成功', url('/issuehistory',array('abnormal_parcel_id'=>request('abnormal_parcel_id'))));
		}else{
			return $this->_redirectMessage('附件上传', '文件为空', url('/issuehistory',array('abnormal_parcel_id'=>request('abnormal_parcel_id'))));
		}
	}
	/**
	 * 删除问题件附件
	 */
	function actiondelissuefile(){
		$file=Abnormalparcelfile::find('abnormal_parcel_file_id=?',request('abnormal_parcel_file_id'))->getOne();
		//删除文件
		unlink($file->file_path);
		//删除数据库记录
		$file->destroy();
		exit();
	}
	/**
	 * 新建问题件
	 */
	function actionnewissueparcel(){
		if(request_is_post()){
			//获取sqe值 
			$now='ISSUE'.date('Ym');
			
			if(request('waybill_codes')){
				$waybill_codes=explode("\r\n", request('waybill_codes'));
				//去空 
				$waybill_codes=array_filter($waybill_codes);
				//去重 
				$waybill_codes=array_unique($waybill_codes);
				//去除数组中的空格 
				foreach ($waybill_codes as $k=>$code){
					$order=Order::find('ali_order_no=?',trim($code))->getOne();
					if($order->isNewRecord()){
						continue;
					}
					//获取seq
					$seq = Helper_Seq::nextVal ( $now );
					if ($seq < 1) {
						Helper_Seq::addSeq ( $now );
						$seq = 1;
					}
					//保存数据
					$seq=str_pad($seq,4,"0",STR_PAD_LEFT);
					$abnormal_parcel_no=date('Ym').$seq;
					$abnormal_parcel=new Abnormalparcel( array (
						'ali_order_no'=>trim($code),
						'abnormal_parcel_no'=>$abnormal_parcel_no,
						'abnormal_parcel_operator'=>MyApp::currentUser('staff_name'),
						'issue_type'=>request('issue_type'),
						'issue_content'=>request('detail')
					));
					if (request('issue_type')==3){
						$abnormal_parcel->deadline = strtotime(request("deadline"));
					}
					//保存历史数据
					$abnormal_parcel->save();
					$history=new Abnormalparcelhistory();
					$history->abnormal_parcel_id=$abnormal_parcel->abnormal_parcel_id;
					$history->follow_up_content=request('detail');
					$history->follow_up_operator=MyApp::currentUser("staff_name");
					$history->save();
				}				
			}		
			return $this->_redirectMessage('新建问题件', '完成', url('/newissueparcel'));
		}
	}
	/**
	 * 验证ali单号是否存在
	 */
	 function actioncheckissueparcelalino(){
	 	//获取数据
	 	if(request('waybill_codes')){
	 		$ali_order_no='';
	 		//默认数据
	 		$data=array(
	 			'message'=>'',
	 			'code'=>'true'
	 		);	 		
	 		//去空 
	 		$waybill_codes=array_filter(request('waybill_codes'));
	 		//去重 
	 		$waybill_codes=array_unique($waybill_codes);
	 		//重组数据
	 		foreach ($waybill_codes as $k=>$code){
	 			$order=Order::find('ali_order_no=?',trim($code))->getOne();
	 			if($order->isNewRecord()){
	 				$ali_order_no .=$code.',';
	 			}
	 		} 	
	 		//错误信息
	 		if($ali_order_no){
	 			$data['code']= 'false';
	 			$data['message']= $ali_order_no.'订单号不存在'; 			
	 		}
	 	}else{
	 		$data['code']= 'false';
	 		$data['message']= '缺少数据'; 	
	 	}
	 	
	 	return json_encode($data);
	} 
	/**
	 * @todo   批量跟进问题件
	 * @author xjy
	 * @since  2021年1月25日16:05:07
	 * @return
	 * @link   #85565
	 */
	function actionsavemanyhistory(){
		//获取ID
		$abnormal_parcel_ids=request('abnormal_parcel_ids');
		foreach ($abnormal_parcel_ids as $id){
			//记录
			$abnormal=Abnormalparcel::find("abnormal_parcel_id=?",$id)->getOne();
			//保存跟进记录
			if(!$abnormal->isNewRecord()){
				$history=new Abnormalparcelhistory();
				$history->abnormal_parcel_id=$abnormal->abnormal_parcel_id;
				$history->follow_up_content=request("follow_up_content");
				$history->follow_up_operator=MyApp::currentUser("staff_name");
				$history->save();
			}
			
		}
		return 'success';
	}
	/**
	 * 手动出库
	 */
	function actionManualout(){
		$order=Order::find("order_id=? and order_status='4'",request('order_id'))->getOne();
		if($order->isNewRecord()){
			return $this->_redirectMessage('手动出库', '订单状态不正确或订单不存在', url('/detail',array('order_id'=>$order->order_id)),2);
		}
		$order->order_status='6';
		$order->save();
		if(strlen($order->tracking_no)>0){
			$sub_code=Subcode::find('sub_code=? and order_id=?',$order->tracking_no,$order->order_id)->getOne();
			if($sub_code->isNewRecord()){
				$sub=new Subcode();
				$sub->order_id=$order->order_id;
				$sub->sub_code=$order->tracking_no;
				$sub->save();
			}
		}
		
		if(strlen($order->channel_id)>0 && strlen($order->tracking_no)>0){
			//计算应付
			//获取异形包装费
			$product = Product::find('product_name = ?',$order->service_code)->getOne();
			$special_fee_c_t=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$order->order_id)->getOne();
			$special_count_c_t=0;
			if(!$special_fee_c_t->isNewRecord()){
				$special_count_c_t=$special_fee_c_t->quantity;
			}
			//删除应付费用
			Fee::find("order_id=? and fee_type='2'",$order->order_id)->getAll()->destroy();
			//查找渠道成本
			$channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
			if(!$channelcost_c_t->isNewRecord()){
				$channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,time(),time())->getOne();
				if(!$channelcostppr_c_t->isNewRecord()){
					$network_c_t=Network::find("network_code=? ",$order->channel->network_code)->getOne();
					$quote= new Helper_Quote();
					if ($order->customer->customs_code=='ALCN'){
						$cainiaofee = new Helper_CainiaoFee();
						$price_c_t=$cainiaofee->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t);
					}else{
						$price_c_t=$quote->payment($order, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t);
					}
					if (count($price_c_t)&&$price_c_t['total_single_weight']){
						//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
						$order->total_single_weight = $price_c_t['total_single_weight'];
						$order->save();
					}
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
						$feeall=Fee::find('order_id = ?',$order->order_id)->getAll();
						foreach ($feeall as $fee){
							$fee->account_date=$order->warehouse_out_time;
							$fee->save();
						}
						//保存计费重量
						if($price_c_t['price_info']['total_weight']){
							//出库的包裹总计费重，用于计算成本
							$order->weight_cost_out=$price_c_t['price_info']['total_weight'];
							//标签重量
							$order->weight_label=$price_c_t['price_info']['weight_label'];
							//出库的包裹总体积重
							$order->total_out_volumn_weight=$price_c_t['price_info']['total_out_volumn_weight'];
							$order->save();
						}
					}
				}
			}
		}
		
		$events=Event::find('order_id=? and event_code in ("PALLETIZE","WAREHOUSE_OUTBOUND","CARRIER_PICKUP")',$order->order_id)->getAll();
		//         $events=Event::find('order_id=? and event_code in ("PALLETIZE")',$order->order_id)->getAll();
		if (count($events)>0){
			return $this->_redirectMessage('手动出库', '成功，状态更新为已打印', url('/detail',array('order_id'=>$order->order_id)),2);
		}
		//设置默认状态
		$confirm_flag=1;
		//设置包机业务不发送事件 
		if($order->service_code=='CNUSBJ-FY'){
			$confirm_flag=5;
		}
		//存入事件信息
		//打托事件
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
		}
		$checkout_time=time();
		//事件加客户
		$palletize_event= new Event();
		//事件加客户
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
		//出库事件
		$outbound_event= new Event();
		//事件加客户
		$outbound_event->changeProps(array(
			'order_id'=>$order->order_id,
			'customer_id'=>$order->customer_id,
			'event_code'=>'WAREHOUSE_OUTBOUND',
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
		
		//         EMS-FY:去掉承运商已取件事件，添加：S_TH_OUT轨迹
		if($order->service_code!='EMS-FY' && $order->service_code!='WIG-FY' && $order->service_code!='ePacket-FY'){
			//事件加客户
			$pickup_event= new Event();
			//事件加客户
			$pickup_event->changeProps(array(
				'order_id'=>$order->order_id,
				'customer_id'=>$order->customer_id,
				'event_code'=>'CARRIER_PICKUP',
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
		//         出库时间
		$order->warehouse_out_time=$checkout_time;
		$order->save();
		$feeall=Fee::find('order_id = ?',$order->order_id)->getAll();
		foreach ($feeall as $fee){
			$fee->account_date=$order->warehouse_out_time;
			$fee->save();
		}
		return $this->_redirectMessage('手动出库', '成功，状态更新为已打印', url('/detail',array('order_id'=>$order->order_id)),2);
	}
	/**
	 * 自动填写单位1 单位2
	 */
	function actiongethsinfo(){
		$check_hs=Hs::find('HSCode=?',request('hs_code'))->getOne();
		$data=array();
		if($check_hs->isNewRecord()){
			$data['status']='false';
		}else{
			$data['status']='true';
			$data['unit1']=$check_hs->unit1;
			$data['unit2']=$check_hs->unit2;
		}
		echo json_encode($data);
		exit();
	}
	
	/**
	 *预报订单
	 */
	function actionprealert(){
		if(request_is_post()){
			$is_ogp = 0;
			$orders=Order::find("ali_testing_order!= '1'");
			//发件日期
			if(request("start_date")){
				$orders->where("record_order_date >=?",strtotime(request("start_date").' 00:00:00'));
			}
			if (request("end_date")){
				$orders->where("record_order_date <=?",strtotime(request("end_date").' 23:59:59'));
			}
			if(request("sort")){
				$orders->where("sort=?",request("sort"));
			}
			if(request("channel")){
				$channel_group=order::channelgroup();
				if (strpos ( request ( 'channel'), 'OGP' ) !== false ){
					$is_ogp = 1;
				}
				$orders->where("channel_id in (?)",$channel_group [request ( 'channel')]);
			}
			if(request("export")=='exportprealert'){
				$export=clone $orders;
				$export_orders=$export->getAll();
				$header = array (
					'业务日期','运单号','件数','实重','计费重','目的地','尺寸','服务类型1P/2','分单号','是否报关','是否装UPS奶白袋','备注'
				);
				$sheet = array (
					$header
				);
				foreach ($export_orders as $value){
					$item_count=Faroutpackage::find("order_id=?",$value->order_id)->getSum("quantity_out");
					//报关
					$baoguan="否";
					if($value->declaration_type=='DL' || $value->total_amount > 700 || $value->weight_actual_in > 70){
						$baoguan="是";
					}
					$weight_arr = Helper_Quote::getweightarr($value, 3);
					if ($is_ogp){
						$actual_out = $weight_arr['total_label_weight'];
						$cost_out = $weight_arr['total_cost_weight'];
					}else{
						$actual_out = $value->weight_actual_out;
						$cost_out = $value->weight_cost_out;
					}
					if($item_count==1){
						//1件
						$faroutpackage=Faroutpackage::find("order_id=?",$value->order_id)->getOne();
						//尺寸
						$chicun="";
						//装奶白袋
						$naibaidai="否";
						if($value->packing_type=='PAK'){
							$chicun="PAK";
							$naibaidai="是";
						}elseif ($value->packing_type=='DOC'){
							$chicun="DOC";
							$naibaidai="是";
						}else {
							//长宽高降序排列
							$l_w_h=array(floor($faroutpackage->length_out),floor($faroutpackage->width_out),floor($faroutpackage->height_out));
							rsort($l_w_h);
							$chicun=$l_w_h[0]."*".$l_w_h[1]."*".$l_w_h[2];
							if($faroutpackage->weight_out<=3.7){
								if($l_w_h[0]<=40 && $l_w_h[1]<=40 && $l_w_h[2]<=17 ){
									$naibaidai="是";
								}
							}
						}
						$sheet [] =array(
							Helper_Util::strDate('Y-m-d', $value->record_order_date),$value->tracking_no,$item_count,$actual_out,
							$cost_out,$value->consignee_country_code,$chicun,"1P","",$baoguan,$naibaidai,$value->sort
						);
					}else{
						//多件
						$faroutpackages=Faroutpackage::find("order_id=?",$value->order_id)->getAll();
						$packages=array();
						foreach ($faroutpackages as $out){
							for ($j=0;$j<$out->quantity_out;$j++){
								$packages[]=array(floor($out->length_out),floor($out->width_out),floor($out->height_out));
							}
						}
						//子单号
						$subcodes=Subcode::find("order_id=?",$value->order_id)->asArray()->getAll();
						$subcodes=Helper_Array::getCols($subcodes, "sub_code");
						for($i=0;$i<$item_count; $i++){
							//尺寸
							$chicun="";
							//装奶白袋
							$naibaidai="否";
							//长宽高降序排列
							$l_w_h=$packages[$i];
							rsort($l_w_h);
							$chicun=$l_w_h[0]."*".$l_w_h[1]."*".$l_w_h[2];
							if($faroutpackage->weight_out<=3.7){
								if($l_w_h[0]<=40 && $l_w_h[1]<=40 && $l_w_h[2]<=17 ){
									$naibaidai="是";
								}
							}
							if($i=='0'){
								$sheet [] =array(
									Helper_Util::strDate('Y-m-d', $value->record_order_date),$value->tracking_no,$item_count,$actual_out,
									$cost_out,$value->consignee_country_code,$chicun,"1P","",$baoguan,$naibaidai,$value->sort
								);
							}else{
								$sheet [] =array(
									"","","","","",$value->consignee_country_code,$chicun,"1P",$subcodes[$i],$baoguan,$naibaidai,$value->sort
								);
							}
							
						}
					}
					
				}
				Helper_ExcelX::array2xlsx ( $sheet, '预报清单' );
				exit ();
			}
			$this->_view['orders']=$orders->getAll();
			$this->_view['is_ogp']=$is_ogp;
		}
	}
	/**
	 * 结束订单、订单无法再被签收，无需再跟进
	 */
	function actionTermination(){
		$order=Order::find("order_id=? and order_status='8'",request('order_id'))->getOne();
		if($order->isNewRecord()){
			return $this->_redirectMessage('结束订单', '订单状态不正确或订单不存在', url('/detail',array('order_id'=>$order->order_id)),2);
		}
		$order->order_status='13';
		$order->save();
		return $this->_redirectMessage('结束订单', '成功', url('/detail',array('order_id'=>$order->order_id)),2);
	}
	/**
	 * 手动设置已发送状态
	 */
	function actionartificialsend(){
		$order=Order::find("order_id=? and order_status='6'",request('order_id'))->getOne();
		if($order->isNewRecord()){
			return $this->_redirectMessage('设置为已发送状态', '订单状态不正确或订单不存在', url('/detail',array('order_id'=>$order->order_id)),2);
		}
		$order->is_send='1';
		$order->save();
		return $this->_redirectMessage('设置为已发送状态', '成功', url('/detail',array('order_id'=>$order->order_id)),2);
	}
	/**
	 * 替换末端单号
	 */
	function actionReplacetrackingno(){
		$order=Order::find('ali_order_no=?',request('order_no'))->getOne();
		if(request('order_no') && $order->isNewRecord()){
			return $this->_redirectMessage('替换末端单号', '阿里单号不存在', url('/Replacetrackingno'));
		}
		if(request('order_no') && $order->order_status!='6' && $order->order_status!='7' && $order->order_status!='8'){
			return $this->_redirectMessage('替换末端单号', '订单状态不是已打印或已出库或已提取状态', url('/Replacetrackingno'),2);
		}
		$subcodes=Helper_Array::getCols(Subcode::find('order_id=?',$order->order_id)->asArray()->getAll(),'sub_code');
		$this->_view['subcodes']=$subcodes;
		$this->_view['order']=$order;
	}
	/**
	 * 保存替换单号
	 */
	function actionSavereplace(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$subcodes=array_filter(explode("\r\n", request('new_subcode_no')));
		//判断包裹数是否正确
		$package_count=Subcode::find('order_id=?',request('order_id'))->getAll();
		if(count($subcodes)!=count($package_count)){
			return $this->_redirectMessage('替换末端单号', '替换失败，包裹数量不正确', url('/Replacetrackingno'),2);
		}
		//开启事务
		$conn = QDB::getConn ();
		$conn->startTrans ();
		//替换order订单中的运单号
		$order->tracking_no=request("new_tracking_no");
		if($order->tracking_no<>request('new_tracking_no')){
			if( $order->get_trace_flag == '2' || $order->get_trace_flag == '3'){
				$order->get_trace_flag='1';
			}
		}
		if($order->is_send == '1'){
			$order->is_send = '0';
		}
		$order->save();
		//删除subcode表中原有单号
		Subcode::find('order_id=?',request('order_id'))->getAll()->destroy();
		//添加新单号
		foreach ($subcodes as $v){
			$new_code= new Subcode();
			$new_code->changeProps(array(
				'order_id'=>request('order_id'),
				'sub_code'=>$v,
			));
			$new_code->save();
		}
		//关闭事务
		$conn->completeTrans ();
		return $this->_redirectMessage('替换末端单号', '替换成功', url('/Replacetrackingno'));
	}
	
	/*
	 * 退回已支付
	 */
	function actiontuipay(){
		$order=Order::find("order_id=? and order_status in ('6','7','8')",request('order_id'))->getOne();
		if($order->isNewRecord()){
			return $this->_redirectMessage('设置退回已支付状态', '订单状态不正确或订单不存在', url('/detail',array('order_id'=>$order->order_id)),2);
		}
		//退回已支付之前保存阿里订单号与末端物流单号之间的关系
		$sub_codes = Subcode::find('order_id=?',request('order_id'))->getAll();
		if (count($sub_codes)){
			foreach ($sub_codes as $sub_code){
				$trackingno = new ReturnPaidTrackingno();
				$trackingno->changeProps(array(
					'ali_order_no' => $order->ali_order_no,
					//保存order_id
					'order_id' => $order->order_id,
					'old_tracking_no' => $sub_code->sub_code
				));
				$trackingno->save();
			}
		}
		$productcode2=array();
		$productcode2=product::getprodutcode(3);//通过渠道判断
		$channel_ids=Channel::channelids(1);//渠道需要验证的ID
		
		$order->order_status='4';
		$order->is_send='0';
		$order->tracking_no=null;
		$order->channel_id=null;
		$order->account=null;
		$order->total_list_no='';
		$order->get_trace_flag='1';
		//退回已支付，默认有纸化
		$order->dhl_pdf_type='0';
		if(in_array($order->service_code,$productcode2)){
			$order->add_data_status='';
		}
		$order->save();
		$fee=Fee::meta()->destroyWhere('order_id=? and fee_type="2"',request('order_id'));
		Subcode::meta()->destroyWhere('order_id=?',request('order_id'));
		return $this->_redirectMessage('设置退回已支付状态', '成功', url('/detail',array('order_id'=>$order->order_id)),2);
	}
	
	/**
	 * 批量退回已支付
	 */
	function actionReturnPaid(){
		$order_ids = request('order_ids');
		foreach ($order_ids as $order_id){
			$order=Order::find("order_id = ?",$order_id)->getOne();
			if(!$order->isNewRecord()){
				//退回已支付之前保存阿里订单号与末端物流单号之间的关系
				$sub_codes = Subcode::find('order_id=?',$order_id)->getAll();
				if (count($sub_codes)){
					foreach ($sub_codes as $sub_code){
						$trackingno = new ReturnPaidTrackingno();
						$trackingno->changeProps(array(
							'ali_order_no' => $order->ali_order_no,
							'old_tracking_no' => $sub_code->sub_code,
							//保存order_id
							'order_id' => $order->order_id
						));
						$trackingno->save();
					}
				}
				$productcode2=array();
				$productcode2=product::getprodutcode(3);//通过渠道判断
				$order->order_status='4';
				$order->is_send='0';
				$order->tracking_no=null;
				$order->channel_id=null;
				$order->account=null;
				$order->total_list_no='';
				$order->get_trace_flag='1';
				if(in_array($order->service_code,$productcode2)){
					$order->add_data_status='';
				}
				$order->save();
				$fee=Fee::meta()->destroyWhere('order_id=? and fee_type="2"',$order_id);
				Subcode::meta()->destroyWhere('order_id=?',$order_id);
			}
		}
		return 'success';
	}
	
	/**
	 * 推送订单到快件系统中
	 */
	function actionPushorder(){
		//交货验证之后，状态是已出库，未发送
		//Feature #80340
		$channel_id=Channel::find('send_kj=1')->setColumns('channel_id')->asArray()->getAll();
		$channel_id=Helper_Array::getCols($channel_id, 'channel_id');
		if(!count($channel_id)){
			echo "无需要发送三免数据";
			exit;
		}
		$orders=Order::find("order_status in (?)  and add_data_status='1' and is_send='0' and channel_id in (?)",array('7','8'),$channel_id)->getAll();
		$i=1;
		//print_r($orders);exit;
		foreach ($orders as $order){
			if($order->channel->network_code <> 'UPS'){
				continue;
			}
			//$account=UPSAccount::find("account = ?",$order->account)->getOne();
			//查询far_package表中包裹数量
			$package_count=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
			//查询账号
			$account=UPSAccount::find('account=?',$order->account)->asArray()->getOne();
			//判断订单为高价还是低价，根据结果决定使用哪个经营单位编码
			if($order->declaration_type=='DL' || $order->total_amount > 700 || $order->weight_actual_in > 70){//高价
				$business_code=$order->business_code;
			}else{
				$business_code=$account['business_code'];
			}
			//查询子单号
			$sub_code=Helper_Array::getCols(Subcode::find('order_id=?',$order->order_id)->asArray()->getAll(), 'sub_code');
			//获取invoice信息
			$invoice=array();
			$product_count=count($order->product);
			//中文品名，默认选择第一个
			$commodity_name="";
			//查询product表中的产品总数量
			$product_sum=Orderproduct::find('order_id=?',$order->order_id)->sum('product_quantity','product_sum')->getAll();
			//使用的产品重量
			//print_r($product_sum);exit;
			$product_weight=0;
			//数量
			$quantity1=0;
			$quantity2=0;
			//只拿第一条产品
			//$order_product=Orderproduct::find('order_id=?',$order->order_id)->getOne();
			//平均重量
			$actweight=$order->weight_actual_out/$product_sum['product_sum'];
			foreach ($order->product as $order_product){
				//默认单位
				$unit1='件';
				$weight=0;
				//递减数量
				$product_count=$product_count-1;
				//获取主产品名称
				if(!$commodity_name){
					$commodity_name=$order_product->product_name;
				}
				$check_hs=Hs::find('HSCode=?',$order_product->hs_code_far)->getOne();
				$quantity2=$order_product->product_quantity;
				$quantity1=$order_product->product_quantity;
				//判断最后使用总数减去前面分配的重量，已达到总重量一致
				if($product_count==0){
					$weight=$order->weight_actual_out-$product_weight;
				}else{
					$weight=$actweight*$order_product->product_quantity;
				}		
				
				//校验第一计量单位  
				if($check_hs->unit1=='千克'){					
					$quantity1=round($weight,3);
				}else{		
					$unit1=$check_hs->unit1;
				}
				$product_weight +=$weight;
				/* if($check_hs->unit2=='千克' || ($check_hs->unit2=='' && $check_hs->unit1=='千克')){
					$quantity2=$order->weight_actual_out;
				} */
				//更换单位字段顺序   
				$invoice[]=array(
					'product_name_en_far'=>$order_product->product_name_en_far,
					'product_name_far'=>$order_product->product_name_far,
					'hs_code_far'=>$order_product->hs_code_far,
					'weight'=>round($weight,3),
					'declaration_price'=>$order_product->declaration_price,
					'currency_code'=>$order->currency_code,
					'product_quantity1_far'=>$quantity1,
					'product_unit1_far'=>$check_hs->unit1,
					'product_quantity2_far'=>$quantity2,
					'product_unit2_far'=>$unit1,
				);
				//更换单位字段顺序  
				$order_product->product_quantity2_far=$quantity2;
				$order_product->product_unit2_far=$unit1;
				$order_product->product_quantity1_far=$quantity1;
				$order_product->product_unit1_far=$check_hs->unit1;
				$order_product->save(); 
				
			}
			
			$vat='';
			if($order->tax_payer_id){
				$vat=' VAT:'.$order->tax_payer_id;
			}
			$account_sync=Accountsync::find('account=?',$order->account)->getOne();
			//组合数据splitAddress
			$consignee_street=Helper_Common::splitAddress($order->consignee_street1.' '.$order->consignee_street2.$vat);
			//组合数据splitAddress
			$sender_street=Helper_Common::splitAddress($account['address']);
			$data=array(
				'tracking_no'=>$order->tracking_no,
				'record_order_date'=>$order->record_order_date,
				'declaration_type'=>$order->declaration_type,
				'total_amount'=>$order->total_amount,
				'weight_income_in'=>$order->weight_income_in,
				'weight_actual_in'=>$order->weight_actual_in,
				'weight_cost_out'=>$order->weight_label,
				'weight_actual_out'=>$order->weight_actual_out,
				'packing_type'=>$order->packing_type,
				'item_count'=>$package_count['sum_quantity'],
				'consignee_country_code'=>$order->consignee_country_code,
				'account'=>$order->account,
				'sender_cn'=>$account['sender_cn'],
				'aname'=>$account['aname'],
				'address'=>$account['address'],
				'address_cn'=>$account['address_cn'],
				'name'=>$account['name'],
				'phone'=>$account['phone'],
				'city'=>$account['city'],
				'city_cn'=>$account['city_cn'],
				'business_code'=>$business_code,
				'consignee_cn'=>$order->consignee_cn,
				'consignee_name'=>$order->consignee_name1,
				'consignee_company'=>$order->consignee_name2?$order->consignee_name2:$order->consignee_name1,
				'consignee_address_cn'=>$order->consignee_address_cn,
				'consignee_address'=>$order->consignee_street1.' '.$order->consignee_street2.$vat,
				'consignee_mobile'=>$order->consignee_mobile,
				'consignee_city'=>$order->consignee_city,
				'consignee_postal_code'=>$order->consignee_postal_code,
				'credit_code'=>$account['credit_code'],
				'channel_id'=>$account_sync->channel_id,
				'channel_name'=>$account_sync->channel_name,
				'product_id'=>$account_sync->product_id,
				'product_name'=>$account_sync->product_name,
				'commission_code'=>$order->commission_code,
				'commodity_name'=>$commodity_name,
				'sort'=>$order->sort,
				'subcodes'=>$sub_code,
				'invoice'=>$invoice,
				'payment_mode'=>'PP',
				'customs_tax_mode'=>'0',
				'sender_postal_code'=>$order->sender_postal_code,
				'sender_address1'=>$sender_street[0],
				'sender_address2'=>@$sender_street[1],
				'sender_address3'=>@$sender_street[2],
				'consignee_address1'=>$consignee_street[0],
				'consignee_address2'=>@$consignee_street[1],
				'consignee_address3'=>@$consignee_street[2],
			);
			QLog::log($order->tracking_no.json_encode($data));
			//发送数据
			//print_r($data);exit;
			$response=Helper_Curl::post('http://kuaijian.far800.com/index.php?controller=cron&action=getwaybill', json_encode($data));
			if($response=='成功'){//推送信息成功
				$order->is_send='1';
				$order->error_message='';
				$order->save();
				echo '序号:'.$i.'  末端运单号: '.$order->tracking_no." 推送信息成功<br>";
				$i++;
				flush();
			}else{//失败
				$order->error_message=$response;
				$order->add_data_status='';
				$order->save();
			}
		}
		exit();
	}
	
	/*
	 * 入库超时订单
	 */
	function actionchaoshi(){
		$select = Order::find('order_status in ("1","14","15","16")');
		$data=time()-30*24*60*60;
		$select->where('create_time<?',$data);
		$pagination = null;
		$list=$select->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
	}
	
	/*
	 * 支付超时订单
	 */
	function actionzhifu(){
		$select = Order::find('order_status="10"');
		$data=time()-8*24*60*60;
		$select->where('warehouse_confirm_time<? and IFNULL(payment_time,"0")="0"',$data);
		$pagination = null;
		$list=$select->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
	}
	
	/*
	 * 三免数据预警
	 */
	function actionyujing(){
		$select = Order::find('order_status="8"')->getAll();
		$arr=array();
		foreach ($select as $s){
			if(!preg_match('/^[0-9a-zA-Z,\s\n\r]+$/', $s->consignee_name1)){
				$arr[]=$s->order_id;
			}elseif(!preg_match('/^[0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]+$/',$s->consignee_state_region_code.$s->consignee_city.$s->consignee_street1.$s->consignee_street2)){
				$arr[]=$s->order_id;
			}
		}
		$pagination = null;
		$list=Order::find('order_id in (?)',$arr)->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('order_id desc')->getAll();
		$this->_view['orders']=$list;
		$this->_view['pagination']=$pagination;
	}
	
	/*
	 * 导出入库信息
	 */
	function actionorderin(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		require_once INDEX_DIR.DS.'_library'.DS.'phpexcel'.DS.'PHPExcel.php';
		require_once INDEX_DIR.DS.'_library'.DS.'phpexcel'.DS.'PHPExcel'.DS.'Writer'.DS.'Excel2007.php';
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$objexcel = new PHPExcel();
		$objexcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objexcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
		$objexcel->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
		$objexcel->getActiveSheet()->getRowDimension('3')->setRowHeight(15);
		$sheet = $objexcel->getActiveSheet();
		$sheet->mergeCells('A1:H1');
		$var = $order->ali_order_no.'入库核重详情';
		$sheet->setCellValue('A1', $var);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A1')->getFont()->setName('宋体');
		$sheet->getStyle('A1')->getFont()->setSize('11');
		
		//设置单元格宽度
		$sheet->getColumnDimension('A')->setWidth(12);
		$sheet->getColumnDimension('B')->setWidth(6);
		$sheet->getColumnDimension('C')->setWidth(6);
		$sheet->getColumnDimension('D')->setWidth(9);
		$sheet->getColumnDimension('E')->setWidth(6);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(12);
		
		/*设置G金额字段格式为2位小数点*/
		$product = Product::find('product_name=?',$order->service_code)->getOne();
		if($product->type=='5'){
			$setcode = "0.000";
		}else{
			$setcode = "0.00";
		}
		
		$sheet -> getStyle('A1') -> getFont() -> setBold(true);
		// 设置单元格边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array( //设置全部边框
					'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
				),
				
			),
		);
		
		$length = sizeof($order->farpackages);
		$sheet->getStyle( 'A1:H'.($length+4))->applyFromArray($styleThinBlackBorderOutline);
		
		$sheet->setCellValue('A2','包裹袋数量');
		$sheet->setCellValue('B2',request('pak'));
		$sheet->setCellValue('C2','');
		$sheet->setCellValue('D2','纸箱数量');
		$sheet->setCellValue('E2',request('box'));
		$sheet->setCellValue('F2','');
		$sheet->setCellValue('G2','异形数量');
		$sheet->setCellValue('H2',request('special'));
		
		$sheet->setCellValue('A3','数量');
		$sheet->setCellValue('B3','长');
		$sheet->setCellValue('C3','宽');
		$sheet->setCellValue('D3','高');
		$sheet->setCellValue('E3','实重');
		$sheet->setCellValue('F3','单件体积重');
		$sheet->setCellValue('G3','单件计费重');
		$sheet->setCellValue('H3','计费重小计');
		$i=4;
		//获取入库重量通用方法
		$weight_arr = Helper_Quote::getweightarr($order, 1);
		foreach ($weight_arr['package'] as $f){
			$sheet->getStyle ('E'.$i)->getNumberFormat()->setFormatCode ($setcode);
			$sheet->getStyle ('F'.$i)->getNumberFormat()->setFormatCode ($setcode);
			$sheet->getStyle ('G'.$i)->getNumberFormat()->setFormatCode ($setcode);
			$sheet->getStyle ('H'.$i)->getNumberFormat()->setFormatCode ($setcode);
			$objexcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
			$sheet->setCellValue('A'.$i, $f['quantity']);
			$sheet->setCellValue('B'.$i, $f['length']);
			$sheet->setCellValue('C'.$i, $f['width']);
			$sheet->setCellValue('D'.$i, $f['height']);
			$sheet->setCellValue('E'.$i, $f['weight']);
			$sheet->setCellValue('F'.$i, $f['volumn_weight']);
			if ($product->type=='3'){
				$sheet->setCellValue('G'.$i, '');
				$sheet->setCellValue('H'.$i, '');
			}else{
				$sheet->setCellValue('G'.$i, $f['cost_weight']);
				$sheet->setCellValue('H'.$i, $f['total_cost_weight']);
			}
			$i++;
		}
		$objexcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$sheet->setCellValue('A'.$i,'');
		$sheet->setCellValue('B'.$i,'');
		$sheet->setCellValue('C'.$i,'');
		$sheet->setCellValue('D'.$i,'总计');
		$sheet->setCellValue('G'.$i,'');
		if ($product->type=='3'){
			$sheet->setCellValue('E'.$i,$weight_arr['total_real_weight']);
			$sheet->setCellValue('F'.$i,$weight_arr['total_volumn_weight']);
		}else{
			$sheet->setCellValue('E'.$i,'');
			$sheet->setCellValue('F'.$i,'');
		}
		$sheet->getStyle ('H'.$i)->getNumberFormat()->setFormatCode ($setcode);
		$sheet->setCellValue('H'.$i,$weight_arr['total_cost_weight']);
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		$objwriter = new PHPExcel_Writer_Excel2007($objexcel);
		header('Content-Disposition: attachment;filename="'.$order->ali_order_no.'入库核重详情.xls"');
		header('Cache-Control: max-age=0');
		try {
			$objwriter->save('php://output');
		} catch (PHPExcel_Writer_Exception $ex) {
			$dir=Q::ini('upload_tmp_dir');
			$filename = $order->ali_order_no.'入库核重详情.xlsx';
			$objwriter->save($dir.DS.$filename);
		}
		exit();
		break;
	}
	
	/*
	 * 导出渠道信息
	 */
	function actionorderqu(){
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		require_once INDEX_DIR.DS.'_library'.DS.'phpexcel'.DS.'PHPExcel.php';
		require_once INDEX_DIR.DS.'_library'.DS.'phpexcel'.DS.'PHPExcel'.DS.'Writer'.DS.'Excel2007.php';
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$objexcel = new PHPExcel();
		$objexcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objexcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
		$objexcel->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
		$objexcel->getActiveSheet()->getRowDimension('3')->setRowHeight(15);
		$sheet = $objexcel->getActiveSheet();
		$sheet->mergeCells('A1:H1');
		$var = $order->ali_order_no.'预报核重详情';
		$sheet->setCellValue('A1', $var);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A1')->getFont()->setName('宋体');
		$sheet->getStyle('A1')->getFont()->setSize('11');
		
		//设置单元格宽度
		$sheet->getColumnDimension('A')->setWidth(12);
		$sheet->getColumnDimension('B')->setWidth(6);
		$sheet->getColumnDimension('C')->setWidth(6);
		$sheet->getColumnDimension('D')->setWidth(9);
		$sheet->getColumnDimension('E')->setWidth(6);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(12);
		
		
		$sheet -> getStyle('A1') -> getFont() -> setBold(true);
		// 设置单元格边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array( //设置全部边框
					'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
				),
				
			),
		);
		
		$length = sizeof($order->faroutpackages);
		$sheet->getStyle( 'A1:H'.($length+4))->applyFromArray($styleThinBlackBorderOutline);
		
		$sheet->setCellValue('A2',$order->packing_type);
		$sheet->setCellValue('B2','');
		$sheet->setCellValue('C2','');
		$sheet->setCellValue('D2','异形');
		$sheet->setCellValue('E2',request('special'));
		$sheet->setCellValue('F2','');
		$sheet->setCellValue('G2','');
		$sheet->setCellValue('H2','');
		
		$sheet->setCellValue('A3','数量');
		$sheet->setCellValue('B3','长');
		$sheet->setCellValue('C3','宽');
		$sheet->setCellValue('D3','高');
		$sheet->setCellValue('E3','实重');
		$sheet->setCellValue('F3','单件体积重');
		$sheet->setCellValue('G3','单件计费重');
		$sheet->setCellValue('H3','计费重小计');
		$i=4;
		//获取出库重量通用方法
		$weight_arr = Helper_Quote::getweightarr($order, 3);/*设置G金额字段格式为2位小数点*/
		if($order->channel_id){
			$channel = Channel::find('channel_id=?',$order->channel_id)->getOne();
			if($channel->type=='5'){
				$setcode = "0.000";
			}else{
				$setcode = "0.00";
			}
		}
		foreach ($weight_arr['package'] as $f){
			if($order->channel_id){
				$sheet->getStyle ('E'.$i)->getNumberFormat()->setFormatCode ($setcode);
				$sheet->getStyle ('F'.$i)->getNumberFormat()->setFormatCode ($setcode);
				$sheet->getStyle ('G'.$i)->getNumberFormat()->setFormatCode ($setcode);
				$sheet->getStyle ('H'.$i)->getNumberFormat()->setFormatCode ($setcode);
			}
			
			$objexcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
			$sheet->setCellValue('A'.$i, $f['quantity_out']);
			$sheet->setCellValue('B'.$i, $f['length_out']);
			$sheet->setCellValue('C'.$i, $f['width_out']);
			$sheet->setCellValue('D'.$i, $f['height_out']);
			$sheet->setCellValue('E'.$i, $f['label_weight']);
			if ($order->channel_id){
				$channel = Channel::find('channel_id=?',$order->channel_id)->getOne();
				$sheet->setCellValue('F'.$i, $f['volumn_weight']);
				if($channel->type=='3'){
					$sheet->setCellValue('G'.$i, '');
					$sheet->setCellValue('H'.$i, '');
				}else{
					$sheet->setCellValue('G'.$i, $f['cost_weight']);
					$sheet->setCellValue('H'.$i, $f['total_cost_weight']);
				}
			}else{
				$sheet->setCellValue('F'.$i, '');
				$sheet->setCellValue('G'.$i, '');
				$sheet->setCellValue('H'.$i, '');
			}
			$i++;
		}
		$objexcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$sheet->setCellValue('A'.$i,'');
		$sheet->setCellValue('B'.$i,'');
		$sheet->setCellValue('C'.$i,'');
		$sheet->setCellValue('D'.$i,'总计');
		if ($order->channel_id && $channel->type=='3'){
			$sheet->setCellValue('E'.$i,$weight_arr['total_real_weight']);
			$sheet->setCellValue('F'.$i,$weight_arr['total_volumn_weight']);
		}else{
			$sheet->setCellValue('E'.$i,'');
			$sheet->setCellValue('F'.$i,'');
		}
		$sheet->setCellValue('G'.$i,'');
		
		if ($order->channel_id){
			$sheet->getStyle ('H'.$i)->getNumberFormat()->setFormatCode ($setcode);
			$sheet->setCellValue('H'.$i,$weight_arr['total_cost_weight']);
		}else{
			$sheet->setCellValue('H'.$i,'');
		}
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		$objwriter = new PHPExcel_Writer_Excel2007($objexcel);
		header('Content-Disposition: attachment;filename="'.$order->ali_order_no.'预报核重详情.xls"');
		header('Cache-Control: max-age=0');
		try {
			$objwriter->save('php://output');
		} catch (PHPExcel_Writer_Exception $ex) {
			$dir=Q::ini('upload_tmp_dir');
			$filename = $order->ali_order_no.'预报核重详情.xlsx';
			$objwriter->save($dir.DS.$filename);
		}
		exit();
		break;
	}
	
	/*
	 * 判断偏远
	 */
	function actioncheckpy(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$pro=Product::find('product_name=?',$order->service_code)->getOne();
		$product_p_p_r=Productppr::find('product_id=? and effective_time <=? and invalid_time>=?',$pro->product_id,time(),time())->getOne();
		$consignee_postal_code=str_replace(array(" ",'-'),'',request('consignee_postal_code'));
		$post_code=$consignee_postal_code;
		QLog::log('原邮编：'.request('consignee_postal_code').' 整理后邮编：'.$consignee_postal_code);
		//订单邮编和偏远库邮编完全一样的，则直接判定为偏远
		$postcode_remote = Remote::find('remote_manage_id = ? and country_code_two=? and ((start_postal_code<=? and end_postal_code>=?)) and ifnull(start_postal_code,"")!="" and ifnull(end_postal_code,"")!=""',$product_p_p_r->remote_manage_id,$order->consignee_country_code,request('consignee_postal_code'),request('consignee_postal_code'))->getOne();
		if(!$postcode_remote->isNewRecord()){
			QLog::log('偏远1');
			return 'success';
		}
		//订单邮编长
		if($order->consignee_country_code !='GB') {
			$remote=Remote::find('remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""',$product_p_p_r->remote_manage_id,$order->consignee_country_code)->getOne();
			if(!$remote->isNewRecord()){
				$length=strlen($remote->start_postal_code);
				if(strlen($post_code)>$length){
					$post_code=substr($post_code, 0,$length);
					QLog::log('截取后邮编：'.$post_code);
				}
			}
		}
		$remote_postcode=Remote::find('remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"")!="" and ifnull(end_postal_code,"")!=""',$product_p_p_r->remote_manage_id,$order->consignee_country_code,$post_code,$post_code)->getOne();
		if(!$remote_postcode->isNewRecord()){
			QLog::log('偏远2');
			return 'success';
		}
		//数据库邮编长
		$remote_zip1=Remote::find('remote_manage_id = ? and country_code_two=? and left(start_postal_code,'.strlen($consignee_postal_code).')<=? and left(end_postal_code,'.strlen($consignee_postal_code).')>=? and ifnull(start_postal_code,"")!="" and ifnull(end_postal_code,"")!=""',$product_p_p_r->remote_manage_id,$order->consignee_country_code,$consignee_postal_code,$consignee_postal_code)->getOne();
		if(!$remote_zip1->isNewRecord()){
			QLog::log('偏远3');
			return 'success';
		}else {
			$remote_zip2=Remote::find('remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""',$product_p_p_r->remote_manage_id,$order->consignee_country_code)->getAll();
			if(count($remote_zip2)>0){
				foreach ($remote_zip2 as $z){
					$start_postal_code=$z->start_postal_code;
					$end_postal_code=$z->end_postal_code;
					if("'".substr($consignee_postal_code, 0, strlen($start_postal_code))>="'".$start_postal_code && "'".substr($consignee_postal_code, 0, strlen($end_postal_code))<="'".$end_postal_code){
						QLog::log('偏远4');
						return 'success';
					}
				}
			}
		}
		//订单信息里城市信息和偏远库里的城市信息完全一样时
		$remote_city=Remote::find("country_code_two = ? and remote_manage_id= ? and (remote_city=? || remote_city=?) and ifnull(remote_city,'') != '' ",$order->consignee_country_code,$product_p_p_r->remote_manage_id,request('consignee_city'),strtolower(str_replace('-','',str_replace(' ','',request('consignee_city')))))->getOne();
		if(!$remote_city->isNewRecord()){
			QLog::log('偏远5');
			return 'success';
		}else{
			$state_code = str_replace(array(' ','-'),'',request('consignee_state_region_code'));
			QLog::log('原省州：'.request('consignee_state_region_code').' 整理后省州：'.$state_code);
			if(strlen(request('consignee_state_region_code'))>0 && strlen($state_code)>0){
				//当订单信息里省州信息和偏远库的城市信息完全一样
				$remote_state = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,'') != ''",$product_p_p_r->remote_manage_id,$order->consignee_country_code,strtolower(request('consignee_state_region_code')),strtolower($state_code))->getOne();
				if(!$remote_state->isNewRecord()){
					QLog::log('疑似偏远1');
					return 'yisi';
				}else{
					//当订单里的城市信息和省信息在偏远库里有出现，但又不是完全一致
					$consignee_city_like = str_replace(array(' ','-'),'',request('consignee_city'));
					QLog::log('原城市：'.request('consignee_city').' 整理后城市：'.$consignee_city_like);
					$remote_like = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city like ? or remote_city like ? or remote_city like ? or remote_city like ?) and ifnull(remote_city,'') != ''",$product_p_p_r->remote_manage_id,$order->consignee_country_code,'%'.request('consignee_city').'%','%'.strtolower($consignee_city_like).'%','%'.request('consignee_state_region_code').'%','%'.strtolower($state_code).'%')->getOne();
					if(!$remote_like->isNewRecord()){
						QLog::log('疑似偏远2');
						return 'yisi';
					}
				}
			}
			//当偏远库里的偏远城市在订单省、城市信息、以及地址里有出现，但是又不是完全一样
			$remote_city=Remote::find('country_code_two = ? and remote_manage_id= ? and ifnull(remote_city,"") != ""',$order->consignee_country_code,$product_p_p_r->remote_manage_id)->getAll();
			foreach ($remote_city as $city){
				QLog::log(strlen(strpos(request('consignee_city'),$city->remote_city)));
				QLog::log(strpos(request('consignee_city'),$city->remote_city)>-1 || strpos(request('consignee_state_region_code'),$city->remote_city)>-1 || strpos(request('consignee_street1'), $city->remote_city)>-1 || strpos(request('consignee_street2'),$city->remote_city)>-1);
				if(strpos(request('consignee_city'),$city->remote_city)>-1 || strpos(request('consignee_state_region_code'),$city->remote_city)>-1 || strpos(request('consignee_street1'), $city->remote_city)>-1 || strpos(request('consignee_street2'),$city->remote_city)>-1){
					QLog::log('疑似偏远3');
					return 'yisi';
				}
			}
		}
		return 'f';
	}
	//检查邮编城市无服务
	function actionchecknoservice(){
		$order=Order::find('order_id=?',request('order_id'))->getOne();
		$product=Product::find('product_name=?',$order->service_code)->getOne();
		$consignee_postal_code=request('consignee_postal_code');
		if($product->check_zip=='1'){
			if(strlen($consignee_postal_code)>0){
				$noservice_zip = Noserivcezipcode::find("zip_code = ? and service_code = ? and city = '' and country_code = ''",$consignee_postal_code,$order->service_code)->getOne();
				if(!$noservice_zip->isNewRecord()){
					return 'youbianwfw';
				}
				$noservice_zip = Noserivcezipcode::find("zip_code = ? and country_code = ? and service_code = ? and city = ''",$consignee_postal_code,$order->consignee_country_code,$order->service_code)->getOne();
				if(!$noservice_zip->isNewRecord()){
					return 'guojiayoubianwfw';
				}
			}
			if(strlen(request('consignee_city'))>0){
				$noservice_city = Noserivcezipcode::find("city = ? and service_code = ? and zip_code = '' and country_code = ''",request('consignee_city'),$order->service_code)->getOne();
				if(!$noservice_city->isNewRecord()){
					return 'chengshiwfw';
				}
				$noservice_city = Noserivcezipcode::find("city = ? and country_code = ? and service_code = ? and zip_code = ''",request('consignee_city'),$order->consignee_country_code,$order->service_code)->getOne();
				if(!$noservice_city->isNewRecord()){
					return 'guojiachengshiwfw';
				}
			}
			$noservice_country = Noserivcezipcode::find("country_code = ? and service_code = ? and zip_code = '' and city = ''",$order->consignee_country_code,$order->service_code)->getOne();
			if(!$noservice_country->isNewRecord()){
				return 'guojiawfw';
			}
			if(strlen($consignee_postal_code)>0 && strlen(request('consignee_city'))>0){
				$noservice_zip = Noserivcezipcode::find("zip_code = ? and city = ? and service_code = ? and country_code = ''",$consignee_postal_code,request('consignee_city'),$order->service_code)->getOne();
				if(!$noservice_zip->isNewRecord()){
					return 'chengshiyoubianwfw';
				}
				$noservice_city = Noserivcezipcode::find("zip_code = ? and city = ? and country_code = ? and service_code = ?",$consignee_postal_code,request('consignee_city'),$order->consignee_country_code,$order->service_code)->getOne();
				if(!$noservice_city->isNewRecord()){
					return 'guojiachengshiyoubianwfw';
				}
			}
		}
		return 'success';
	}
	//批量修改快递单号
	function actionImportedit(){
		set_time_limit(0);
		if(request_is_post ()){
			$errors = array ();
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('order/importedit'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('order/importedit'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = date ( 'YmdHis' ).'ExpenseImport.'.$file->extname ();
			$file_route = $des_dir.DS.$filename;
			$file->move ( $file_route );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ($file_route,true);
			$sheet =$xls->toHeaderMap ();
			//导入的表中有数据
			//必填字段
			$required_fields = array (
				'订单号','国内快递单号','原末端单号','原渠道','换单号','新渠道'
			);
			$error=array();
			if (!empty($sheet)){
				foreach ($sheet as $k=>$row){
					//判断基础信息不得为空
					foreach ($required_fields as $field ) {
						if (!isset( $row [$field] )) {
							return $this->_redirectMessage('失败','模板字段缺失，请检查', url("order/importedit"));
						}
					}
					if(!strlen($row ['订单号'])){
						$error[$k]['订单号'] = '订单号不能为空';
					}else{
						$order = Order::find('ali_order_no = ?',$row ['订单号'])->getOne();
						if($order->isNewRecord()){
							$error[$k]['订单号'] = $row ['订单号'].'订单号不存在';
						}
						if(!empty($row ['换单号'])){
							$quantity = Faroutpackage::find('order_id = ?',$order->order_id)->getSum('quantity_out');
							if($quantity>1){
								$error[$k]['订单号'] = '该订单为一票多件';
							}
							if(strlen($row ['原末端单号'])>0){
								if($order->tracking_no <> $row ['原末端单号']){
									$error[$k]['原末端单号'] = '原末端单号'.$row ['原末端单号'].'与系统保存不符';
								}
							}
						}
						if(!empty($row ['新渠道'])){
							$new_channel_id = Channel::find('channel_name = ?',$row ['新渠道'])->getOne();
							if($new_channel_id->isNewRecord()){
								$error[$k]['新渠道'] = '该渠道不存在';
							}
							if(strlen($row ['原渠道'])>0){
								$channel_id = Channel::find('channel_name = ?',$row ['原渠道'])->getOne();
								if($channel_id->isNewRecord()){
									$error[$k]['原渠道'] = '该渠道不存在';
								}else{
									if($order->channel_id <> $channel_id->channel_id){
										$error[$k]['原渠道'] = '原渠道'.$row ['原渠道'].'与系统保存不符';
									}
								}
							}
						}
					}
				}
				$this->_view['errors']=$error;
				if(empty($error)){
					foreach ($sheet as &$row){
						$newreference = Order::find('ali_order_no = ?',$row['订单号'])->getOne();
						if(!empty($row['国内快递单号'])){
							$new_reference_no = $row['国内快递单号'];
							$new_reference_no = preg_replace('/[.。，、　 ]/',',', $new_reference_no);
							Alireference::meta()->destroyWhere('order_id=?',$newreference->order_id);
							$reference_nos=explode(',', $new_reference_no);
							$reference_no = array_filter($reference_nos);
							foreach ($reference_no as $r){
								$re=new Alireference(array(
									'order_id'=>$newreference->order_id,
									'reference_no'=>$r
								));
								$re->save();
							}
							$newreference->reference_no = implode(',', $reference_no);
						}
						if(!empty($row['换单号'])){
							$newreference->tracking_no = $row['换单号'];
							if($newreference->tracking_no<>$row['换单号']){
								if( $newreference->get_trace_flag == '2' || $newreference->get_trace_flag == '3'){
									$newreference->get_trace_flag='1';
								}
							}
							Subcode::meta()->destroyWhere('order_id = ?',$order->order_id);
							//保存子单信息，用于交货核查
							$subcode=new Subcode();
							$subcode->order_id=$order->order_id;
							$subcode->sub_code=$row['换单号'];
							$subcode->save();
						}
						if(!empty($row['新渠道'])){
							$newchannel = Channel::find('channel_name = ?',$row['新渠道'])->getOne();
							$newreference->channel_id = $newchannel->channel_id;
						}
						$newreference->save();
					}
					return $this->_redirectMessage('成功','批量修改国内快递单号成功', url("order/importedit"));
				}
			}else {
				return $this->_redirectMessage('失败','请填写修改内容', url("order/importedit"));
			}
		}
	}
	function actiondloadchange(){
		return $this->_redirect(QContext::instance()->baseDir(). 'public/download/批量修改快递单号模板.xlsx');
	}
	//发件人备注项
	function actionsendcontact(){
		$contact = Contact::find()->getAll();
		$this->_view['contact']=$contact;
	}
	function actioneditsendcontact(){
		$contact = Contact::find('id = ?',request('id'))->getOne();
		if(request_is_post()){
			$contact = Contact::find('id = ?',request('id'))->getOne();
			$contact->sender_company = request('sender_company');
			$contact->comment = request('comment');
			$contact->save();
			return $this->_redirectMessage('成功', '发件人信息编辑成功', url('order/sendcontact'));
		}
		$this->_view['contact']=$contact;
	}
	function actiondelsend(){
		if (request ( "id" )) {
			$contact = Contact::find ( "id = ?", request ( "id" ) )->getOne ();
			$contact->destroy();
		}
		exit();
	}
	//复制订单
	function actionsavenewali(){
		if(request('order_id')&&request('ali_order_no')&&request('customer_id')){
			$order_id = request('order_id');
			$ali_order_no = request('ali_order_no');
			$customer_id = request('customer_id');
			$order = Order::find('order_id = ?',$order_id)->getOne();
			if($order->isNewRecord()){
				return 'noorder';
			}else{
				if(!strlen($order->payment_time)){
					return 'nopaytime';
				}
			}
			$order_check = Order::find('ali_order_no = ?',$ali_order_no)->getOne();
			if(!$order_check->isNewRecord()){
				return 'saved';
			}
			$order_json = Alijson::find('order_id = ? and api_name = "booking"',$order_id)->getOne();
			$info = json_decode($order_json->ali_json,true);
			QLog::log('原订单'.$info['bookingOrderDTO']);
			$data = json_decode($info['bookingOrderDTO'],true);
			//产生新订单
			if($data['aliOrderNo'] != $ali_order_no){
				$data['aliOrderNo'] = $ali_order_no;
				$info['bookingOrderDTO'] = json_encode($data);
				$newjson = json_encode($info);
				Qlog::log('新订单'.$newjson);
				$url='http://1688.far800.com/api/orderbooking';
				// 					          $url='http://1688test.far800.com/api/orderbooking';
				// 					          $url='localhost/AliExpress/Code/api/orderbooking';
				$response=Helper_Curl::post($url, $newjson);
				QLog::log($response);
				$response = json_decode($response,true);
				if($response['isSuccess'] == true){
					$neworder = Order::find('ali_order_no = ?',$ali_order_no)->getOne();
					$neworder->department_id = request('copy_department_id') ? request('copy_department_id') : $order->department_id;
					$neworder->far_warehouse_in_time = time();
					$neworder->far_warehouse_in_operator = MyApp::currentUser('staff_name');
					$neworder->warehouse_confirm_time = time();
					$neworder->payment_time = time();
					$neworder->order_status = '4';
					$neworder->customer_id = $customer_id;
					$neworder->add_data_status = '1';
					$neworder->warehouse_in_department_id = $order->warehouse_in_department_id;
					$neworder->packing_type = $order->packing_type;
					$neworder->volumn_chargeable = $order->volumn_chargeable;
					$neworder->weight_income_in = $order->weight_income_in;
					$neworder->weight_actual_in = $order->weight_actual_in;
					$neworder->consignee_name1 = $order->consignee_name1;
					$neworder->consignee_name2 = $order->consignee_name2;
					$neworder->consignee_mobile = $order->consignee_mobile;
					$neworder->consignee_telephone = $order->consignee_telephone;
					$neworder->tax_payer_id = $order->tax_payer_id;
					$neworder->consignee_state_region_code = $order->consignee_state_region_code;
					$neworder->consignee_city = $order->consignee_city;
					$neworder->consignee_postal_code = $order->consignee_postal_code;
					$neworder->consignee_street1 = $order->consignee_street1;
					$neworder->consignee_street2 = $order->consignee_street2;
					$neworder->package_total_in = $order->package_total_in;
					$neworder->save();
					//材质信息复制
					$orderproducts = Orderproduct::find('order_id = ?',$neworder->order_id)->getAll();
					foreach ($orderproducts as $orderproduct){
						$old_product = Orderproduct::find('product_name_en_far = ? and order_id = ?',$orderproduct->product_name_en_far,$order_id)->getOne();
						$orderproduct->material_use = $old_product->material_use;
						$orderproduct->save();
					}
					//复制原far_package信息
					$far_packages = Farpackage::find('order_id = ?',$order_id)->getAll();
					foreach ($far_packages as $far_package){
						$new_far_package = new Farpackage(array(
							'order_id'=>$neworder->order_id,
							'barcode'=>$far_package->barcode,
							'quantity'=>$far_package->quantity,
							'length'=>$far_package->length,
							'width'=>$far_package->width,
							'height'=>$far_package->height,
							'weight'=>$far_package->weight,
							'length_out'=>$far_package->length_out,
							'width_out'=>$far_package->width_out,
							'height_out'=>$far_package->height_out,
							'weight_out'=>$far_package->weight_out,
						));
						$new_far_package->save();
					}
					//复制原far_out_package信息
					$far_out_packages = Farpackage::find('order_id = ?',$neworder->order_id)->getAll();
					foreach ($far_out_packages as $far_out_package){
						$new_far_out_package = new Faroutpackage(array(
							'order_id'=>$neworder->order_id,
							'far_id'=>$far_out_package->far_package_id,
							'quantity_out'=>$far_out_package->quantity,
							'length_out'=>$far_out_package->length,
							'width_out'=>$far_out_package->width,
							'height_out'=>$far_out_package->height,
							'weight_out'=>$far_out_package->weight,
						));
						$new_far_out_package->save();
					}
					//复制原fee信息
					$fee = Fee::find('order_id = ? and fee_type = "1"',$order_id)->getAll();
					foreach ($fee as $old_fee){
						$new_fee = new Fee();
						$new_fee->order_id=$neworder->order_id;
						$new_fee->fee_type=$old_fee->fee_type;
						$new_fee->fee_item_code=$old_fee->fee_item_code;
						$new_fee->fee_item_name=$old_fee->fee_item_name;
						$new_fee->quantity=$old_fee->quantity;
						$new_fee->amount=$old_fee->amount;
						$new_fee->btype_id=$old_fee->btype_id;
						$new_fee->save();
					}
				}
				return 'success';
			}else{
				return 'samealiorderno';
			}
		}
	}
	
	function actionsaveheadline(){
		$abnormal_parcel_ids=request('abnormal_parcel_ids');
		$headtype=request('headtype');
		if($headtype[0]=='all'){
			unset($headtype[0]);
		}
		foreach ($abnormal_parcel_ids as $id){
			foreach ($headtype as $h){
				$ab=abnormalparcelheadline::find('abnormal_parcel_id =? and headline_id=?',$id,$h)->getOne();
				if($ab->isNewRecord()){
					$ab->abnormal_parcel_id=$id;
					$ab->headline_id=$h;
					$ab->save();
				}
			}
		}
		return 'success';
	}
	
	/*
	 * 渠道异常件标签列表
	 */
	function actionheadline(){
		$headline=headline::find();
		$pagination = null;
		$list=$headline->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->order('create_time desc')->getAll();
		$this->_view['list']=$list;
		$this->_view['pagination']=$pagination;
	}
	/*
	 * 渠道异常件标签编辑
	 */
	function actionheadlineedit(){
		$headline=new headline();
		if(request('headline_id')){
			$headline=headline::find('headline_id = ?',request('headline_id'))->getOne();
		}
		if(request_is_post()){
			$headline->headline=request('headline');
			$headline->save();
			return $this->_redirectMessage('保存成功', '', url('/headline'));
		}
		$this->_view['headline']=$headline;
	}
	/*
	 * 渠道异常件标签删除
	 */
	function actionhdelete(){
		$headline_id = request('headline_id');
		headline::meta()->destroyWhere('headline_id = ? ',$headline_id);
		return $this->_redirect(url('order/headline'));
	}
	
	/**
	 * 渠道异常件标签树结构
	 */
	function actionheadlinetree(){
		$headlinetypes=headline::find()->getAll();
		$arr=array();
		$checkeds = array ();
		if (request ( "checked" ) != null) {
			$checkeds = explode ( ",", request ( "checked" ) );
		}
		$array = array ();
		foreach($headlinetypes as $type){
			$array[]=array(
				"id" => $type->headline_id,
				"text" => $type->headline,
				"checked" => in_array ( $type->headline_id, $checkeds ) ? "checked" : "",
				"attributes" => "",
			);
		}
		$arr [] = array (
			"id" => "all",
			"text" => "",
			"state" => request ( "state" ) != null && request ( "state" ) ? "" : "open",
			"checked" => in_array ( "all", $checkeds ) ? "checked" : "",
			"attributes" => "",
			"children" => $array
		);
		echo json_encode($arr);
		exit();
	}
	/**
	 * @todo   订单批量导入
	 * @author 吴开龙
	 * @since  2020-9-10 9:42:09
	 * @return
	 * @link   #82497
	 */
	function actionBatchImport(){
		if(request_is_post ()){
			if(!request('customer_id')){
				return $this->_redirectMessage('请选择客户','',url('order/batchimport'));
			}
			set_time_limit(0);
			ini_set('memory_limit', '-1');//不限制内存
			$errors = array ();
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('order/batchimport'));
			}
			$file = $uploader->file ( 'file' );//获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage('文件格式不正确：xls、xlsx','',url('order/batchimport'));
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
			$filename = date ( 'YmdHis' ).'ExpenseImport.'.$file->extname ();
			$file_route = $des_dir.DS.$filename;
			$file->move ( $file_route );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ($file_route,true);
			$sheet =$xls->toHeaderMap ();
			//将两种模式数据结构统一
			if(isset($sheet[0]['中文品名1'])){
				//品名横向
				$sheet = Helper_Array::groupBy($sheet, '客户订单号');
				foreach ($sheet as $k => $s){
					if(count($s) > 1){
						$errors[$k][] = '横向模板中不能有重复的订单号';
						continue;
					}
					foreach ($s as $s1){
						$k1 = 1;
						while (1){
							//第一条数据必须添加
							if($k1 != 1){
								if(!@$s1['中文品名'.$k1] || $s1['中文品名'.$k1] == ''){
									break;
								}
							}
							$data[$k][$k1-1] = $s1;
							$data[$k][$k1-1]['中文品名'] = $s1['中文品名'.$k1];
							$data[$k][$k1-1]['英文品名'] = $s1['英文品名'.$k1];
							$data[$k][$k1-1]['海关编码'] = $s1['海关编码'.$k1];
							$data[$k][$k1-1]['申报单价'] = $s1['申报单价'.$k1];
							$data[$k][$k1-1]['申报数量'] = $s1['申报数量'.$k1];
							//$data[$k][$k1-1]['材质用途'] = $s1['材质用途'.$k1];
							$data[$k][$k1-1]['材质'] = $s1['材质'.$k1];
							$data[$k][$k1-1]['用途'] = $s1['用途'.$k1];
							$data[$k][$k1-1]['是否带电'] = $s1['是否带电'.$k1];
							$data[$k][$k1-1]['配货信息'] = $s1['配货信息'.$k1];
							$k1 ++;
						}
					}
				}
				// 				echo "<pre>";
				// 				print_r($data);
				// 				exit;
			}else{
				//品名纵向
				$data = Helper_Array::groupBy($sheet, '客户订单号');
			}
			//进行各种判断
			foreach ($data as $key => $value){
				if($key == ''){
					continue;
				}
				if(strlen($key) < 4 || strlen($key) > 50){
					$errors[$key][] = '订单号长度需在4-50之间';
					continue;
				}
				$order = Order::find('order_no=? and customer_id=?',$key,request('customer_id'))->getOne();
				if(!$order->isNewRecord()){
					$errors[$key][] = '系统中已有此单号';
					continue;
				}
				foreach ($value as $k3 => $v){
					$k4 = $k3+1;
					//必填字段验证
					$title = array(
						'客户订单号',
						'运输方式',
						'目的地',
						'收件人姓名',
						'收件人省/州',
						'收件人城市',
						'收件人地址',
						'收件人邮编',
						'包裹总件数',
						'包裹类型',
						'中文品名',
						'英文品名',
						'海关编码',
						'申报单价',
						'申报数量',
						'是否带电'
					);
					foreach ($title as $k5 => $t){
						if($v[$t] == ''){
							if($k5 < 10 && $k3 == 0){
								$errors[$key][] = '['.$t.']不能为空';
							}
							if($k5 >= 10){
								$errors[$key][] = '包裹['.$k4.']['.$t.']不能为空';
							}
						}
					}
					if(mb_strlen($v['中文品名'],'utf8') > 32){
						$errors[$key][] = '包裹['.$k4.'][中文品名]长度超出32';
					}
					if(mb_strlen($v['英文品名'],'utf8') > 32){
						$errors[$key][] = '包裹['.$k4.'][英文品名]长度超出32';
					}
					if(!is_numeric($v['申报数量']) || strpos($v['申报数量'],'.') || $v['申报数量'] < 1){
						$errors[$key][] = '包裹['.$k4.'][申报数量]必须为正整数';
					}
					if($v['是否带电'] != '是' && $v['是否带电'] != '否'){
						$errors[$key][] = '包裹['.$k4.'][是否带电]未按格式填写是/否';
					}
				}
				if($value[0]['是否报关'] == '是'){
					if(!$value[0]['委托书编号'] || !$value[0]['经营单位编码']){
						$errors[$key][] = '当为报关件时[委托书编号]和[经营单位编码]必填';
					}
				}
				if($value[0]['目的地'] == 'BR'){
					//国家为br税号必填
					if(!$value[0]['税号'] || strlen($value[0]['税号']) < 11 || strlen($value[0]['税号']) > 14){
						$errors[$key][] = '收件国家为BR税号必填且长度在11-14';
					}
				}
				if(strlen($value[0]['收件人地址']) > 120){
					$errors[$key][] = '收件人地址长度超出120字符';
				}
				if(strlen($value[0]['收件人姓名']) < 2 || strlen($value[0]['收件人姓名']) > 32){
					$errors[$key][] = '收件人姓名长度应在2-32之间';
				}
				if(strlen($value[0]['收件人省/州']) < 1 || strlen($value[0]['收件人省/州']) > 40){
					$errors[$key][] = '收件人省/州长度应在1-40之间';
				}
				if(strlen($value[0]['收件人城市']) < 1 || strlen($value[0]['收件人城市']) > 32){
					$errors[$key][] = '收件人城市长度应在1-32之间';
				}
// 				if(strlen($value[0]['收件人电话']) < 11 || strlen($value[0]['收件人电话']) > 11){
// 					$errors[$key][] = '收件人电话长度应为11位';
// 				}
				if($value[0]['收件人邮箱'] && !filter_var($value[0]['收件人邮箱'], FILTER_VALIDATE_EMAIL)){
					$errors[$key][] = '收件人邮箱为非法邮箱格式';
				}
				if(strlen($value[0]['收件人邮编']) < 1 || strlen($value[0]['收件人邮编']) > 35){
					$errors[$key][] = '收件人邮编长度应在1-35之间';
				}
				
				//判断发件人信息
				$i3 = 0;
				if($value[0]['发件人国家']){
					$i3 ++;
				}
				if($value[0]['发件人省']){
					$i3 ++;
				}
				if($value[0]['发件人城市']){
					$i3 ++;
				}
				if($value[0]['发件人邮编']){
					$i3 ++;
				}
				if($value[0]['发件人地址信息1']){
					$i3 ++;
				}
				if($value[0]['发件人地址信息2']){
					$i3 ++;
				}
				if($value[0]['发件人公司']){
					$i3 ++;
				}
				if($value[0]['发件人姓名']){
					$i3 ++;
				}
				if($value[0]['发件人联系电话']){
					$i3 ++;
				}
				if($value[0]['发件人联系邮箱']){
					$i3 ++;
				}
				if($i3 != 0 && $i3 != 10){
					$errors[$key][] = '发件人信息数据必须都填或者都为空';
				}
				//FDA申报信息测试;要么空白，要么全填
				$i4 = 0;
				if($value[0]['FDA制造商全称']){
					$i4 ++;
					//判断不可为中文
					if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $value[0]['FDA制造商全称'], $match)) {
						$errors[$key][] = 'FDA制造商全称不能包含中文';
					}
				}
				if($value[0]['FDA制造商城市']){
					$i4 ++;
					//判断不可为中文
					if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $value[0]['FDA制造商城市'], $match)) {
						$errors[$key][] = 'FDA制造商城市不能包含中文';
					}
				}
				if($value[0]['FDA制造商邮编']){
					$i4 ++;
					//判断不可为中文
					if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $value[0]['FDA制造商邮编'], $match)) {
						$errors[$key][] = 'FDA制造商邮编不能包含中文';
					}
				}
				if($value[0]['FDA制造商地址信息']){
					$i4 ++;
					//判断不可为中文
					if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $value[0]['FDA制造商地址信息'], $match)) {
						$errors[$key][] = 'FDA制造商地址信息不能包含中文';
					}
				}
				if($i4 != 0 && $i4 != 4){
					$errors[$key][] = 'FDA申报信息必须都填或者都为空';
				}
				//判断包裹重量长宽高数量
				$i = 1;
				while (1){
					if(!isset($value[0]['包裹重量'.$i])){
						break;
					}
					$i2 = 0;
					if($value[0]['包裹重量'.$i]){
						$i2 ++;
					}
					if($value[0]['包裹长度'.$i]){
						$i2 ++;
					}
					if($value[0]['包裹宽度'.$i]){
						$i2 ++;
					}
					if($value[0]['包裹高度'.$i]){
						$i2 ++;
					}
					if($value[0]['包裹数量'.$i]){
						$i2 ++;
						if(!is_numeric($value[0]['包裹数量'.$i]) || strpos($value[0]['包裹数量'.$i],'.') || $value[0]['包裹数量'.$i] < 1){
							$errors[$key][] = '[包裹数量'.$i.']必须为正整数';
						}
					}
					if($i2 != 0 && $i2 != 5){
						$errors[$key][] = '每个包裹信息数据必须都填或者都为空';
					}
					$i ++;
				}
				//根据运输方式查询产品 判断中美海派和无忧的订单导入时，一票多件报错
				$transport = CodeTransport::find('code=?',trim($value[0]['运输方式']))->getOne();
				$product = Product::find('product_id=?',$transport->product_id)->getOne();
				if($product->product_name == 'CNUS-FY' || $product->product_name == 'OCEAN-FY'){
					if($value[0]['包裹数量2']){
						$errors[$key][] = '中美海运专线和中美无忧专线仅支持一票一件';
					}
				}
				//计算明细包裹总数量和总重量
				$i = 1;
				$zongjianshu = 0;
				$zongzhongliang = 0;
				while (1){
					if(!isset($value[0]['包裹重量'.$i]) || !$value[0]['包裹重量'.$i]){
						break;
					}
					$zongjianshu += $value[0]['包裹数量'.$i];
					$zongzhongliang += $value[0]['包裹重量'.$i];
					$i ++;
				}
				if(!is_numeric($value[0]['包裹总件数']) || strpos($value[0]['包裹总件数'],'.') || $value[0]['包裹总件数'] < 1){
					$errors[$key][] = '[包裹总件数]必须为正整数';
				}
				if($value[0]['包裹总件数'] && $value[0]['包裹重量1']){
					if($value[0]['包裹总件数'] != $zongjianshu){
						$errors[$key][] = '包裹总件数和分包裹信息数量加起来的件数不一致';
					}
				}
				if($value[0]['包裹总重量'] && $value[0]['包裹重量1']){
					if($value[0]['包裹总重量'] != $zongzhongliang){
						$errors[$key][] = '包裹总重量和分包裹信息重量加起来的数据不一致';
					}
				}
				if($value[0]['包裹类型'] != 'BOX' && $value[0]['包裹类型'] != 'PAK' && $value[0]['包裹类型'] != 'DOC'){
					$errors[$key][] = '包裹类型只能填BOX/PAK/DOC';
				}
				if(!$value[0]['包裹总件数'] && !$value[0]['包裹总重量'] && !$value[0]['包裹类型']){
					if(!$value[0]['包裹重量1']){
						$errors[$key][] = '包裹总件数和总重量不填，则包裹明细必填';
					}else{
						$value[0]['包裹总件数'] = $zongjianshu;
						$value[0]['包裹总重量'] = $zongzhongliang;
					}
				}
				//如果有交货仓库则进行判断
				if($value[0]['交货仓库']){
					//查询系统是否存在
					$warehouse = CodeWarehouse::find('warehouse=?',trim($value[0]['交货仓库']))->getOne();
					//如果不存在就报错
					if($warehouse->isNewRecord()){
						//报错：交货仓库不存在
						$errors[$key][] = '交货仓库不存在';
					}
				}
			}
			$this->_view ['errors'] = $errors;
			if(empty($errors)){
				//保存数据
				foreach ($data as $v){
					//末端单号
					$tracking_no = array();
					if($v[0]['转单号'] != ''){
						$tracking_no = explode(',',$v[0]['转单号']);
					}
					
					
					//如果没有交货仓库则默认ASP_FAR_HZ线下仓
					$v[0]['交货仓库'] = $v[0]['交货仓库'] ? $v[0]['交货仓库'] : 'ASP_FAR_HZ';
					//仓库查询
					$warehouse = CodeWarehouse::find('warehouse=?',trim($v[0]['交货仓库']))->getOne();
					
					//运输方式查询
					$transport = CodeTransport::find('code=?',trim($v[0]['运输方式']))->getOne();
					$product = Product::find('product_id=?',$transport->product_id)->getOne();
					$order = new Order();
					//泛远单号
					$now='FAREX'.date('ym');
					$seq = Helper_Seq::nextVal ( $now );
					if ($seq < 1) {
						Helper_Seq::addSeq ( $now );
						$seq = 1;
					}
					$far_no=$now.sprintf("%06d",$seq).'YQ';
					$order->far_no=$far_no;
					$order->ali_order_no=$far_no;
					
					$order->customer_id = request('customer_id');
					//仓库
					$order->department_id = $warehouse->department_id;
					$order->order_no = trim($v[0]['客户订单号']);
					$order->reference_no = trim($v[0]['国内快递单号']);
					if(!empty($tracking_no)){
						$order->tracking_no = @trim($tracking_no[0]);
					}
					if(trim($v[0]['发件人国家'])){
						$order->sender_country_code = trim($v[0]['发件人国家']);
						$order->sender_state_region_code = trim($v[0]['发件人省']);
						$order->sender_city = trim($v[0]['发件人城市']);
						$order->sender_postal_code = trim($v[0]['发件人邮编']);
						$order->sender_street1 = trim($v[0]['发件人地址信息1']);
						$order->sender_street2 = trim($v[0]['发件人地址信息2']);
						$order->sender_name1 = trim($v[0]['发件人姓名']);
						$order->sender_name2 = trim($v[0]['发件人公司']);
						$order->sender_mobile = trim($v[0]['发件人联系电话']);
						$order->sender_email = trim($v[0]['发件人联系邮箱']);
					}else{
						$customer = Customer::find('customer_id=?',request('customer_id'))->getOne();
						$order->sender_country_code = $customer->sender_country_code;
						$order->sender_state_region_code = $customer->sender_state_region_code;
						$order->sender_city = $customer->sender_city;
						$order->sender_postal_code = $customer->sender_postal_code;
						$order->sender_street1 = $customer->sender_street1;
						$order->sender_street2 = $customer->sender_street2;
						$order->sender_name1 = $customer->sender_name1;
						$order->sender_name2 = $customer->sender_name2;
						$order->sender_mobile = $customer->sender_mobile;
						$order->sender_email = $customer->sender_email;
					}
					//$order->sender_postal_code = trim($v[0]['发件人邮编']);
					//上门取件和取件网点判断
					$need_pick_up = 0;
					$pick_company = '';
					if(trim($v[0]['上门取件']) == '是'){
						$zipcode = Zipcode::find()->asArray()->getAll();
						foreach ($zipcode as $code){
							if($order->sender_postal_code >= $code['zip_code_low'] && $order->sender_postal_code <= $code['zip_code_high']){
								$need_pick_up = 1;
								$pick_company = $code['pick_company'];
							}
						}
					}
// 					echo $order->sender_mobile.'<br>';
// 					echo $pick_company;
// 					exit;
					$order->need_pick_up = $need_pick_up;
					$order->pick_company = $pick_company;
					//如果默认6 仓库就为空
					$order->warehouse_code = $warehouse->warehouse;
					$order->warehouse_name = $warehouse->department_name;
					$order->transport_id = $transport->id;
					$order->service_code = $product->product_name;
					$order->channel_id = $transport->channel_id;
					$order->consignee_country_code = trim($v[0]['目的地']);
					$order->consignee_name1 = trim($v[0]['收件人姓名']);
					$order->consignee_state_region_code = trim($v[0]['收件人省/州']);
					$order->consignee_city = trim($v[0]['收件人城市']);
					$order->consignee_street1 = trim($v[0]['收件人地址']);
					$order->consignee_telephone = trim($v[0]['收件人电话']);
					$order->consignee_mobile = trim($v[0]['收件人电话']);
					$order->consignee_email = trim($v[0]['收件人邮箱']);
					$order->consignee_postal_code = trim($v[0]['收件人邮编']);
					$order->tax_payer_id = trim($v[0]['税号']);
					$order->package_total_num = trim($v[0]['包裹总件数']);
					$order->total_volumn_weight = trim($v[0]['包裹总重量']);
					$order->declaration_type = trim($v[0]['是否报关']) == '是' ? 'DL': 'QT';
					$order->commission_code = trim($v[0]['委托书编号']);
					$order->business_code = trim($v[0]['经营单位编码']);
					$order->fda_company = trim($v[0]['FDA制造商全称']);
					$order->fda_city = trim($v[0]['FDA制造商城市']);
					$order->fda_post_code = trim($v[0]['FDA制造商邮编']);
					$order->fda_address = trim($v[0]['FDA制造商地址信息']);
					if(trim($v[0]['FDA制造商全称'])){
						$order->is_pda = 1;
					}
					if(trim($v[0]['发货优先级']) == '优先'){
						$order->delivery_priority = 'TA';
					}
					$order->order_status = '1';
					$order->save();
					if(!empty($tracking_no)){
						foreach ($tracking_no as $t){
							$subcode = new Subcode();
							$subcode->order_id = $order->order_id;
							$subcode->sub_code = $t;
							$subcode->save();
						}
					}
					//保存国内快递单号
					if(trim($v[0]['国内快递单号'])){
						$reference_arr = explode(',',$v[0]['国内快递单号']);
						foreach ($reference_arr as $r){
							$reference = new Alireference();
							$reference->order_id = $order->order_id;
							$reference->reference_no = $r;
							$reference->save();
						}
					}
					//保存包裹
					$i = 1;
					while (1){
						if(!isset($v[0]['包裹重量'.$i]) || !$v[0]['包裹重量'.$i]){
							break;
						}
						$package = new Orderpackage();
						$package->order_id = $order->order_id;
						$package->package_type = trim($v[0]['包裹类型']) ?:'BOX';
						$package->quantity = trim($v[0]['包裹数量'.$i]);
						$package->unit = 'CM';
						$package->length = trim($v[0]['包裹长度'.$i]);
						$package->width = trim($v[0]['包裹宽度'.$i]);
						$package->height = trim($v[0]['包裹高度'.$i]);
						$package->weight = trim($v[0]['包裹重量'.$i]);
						$package->weight_unit = 'KG';
						$package->save();
						$i ++;
					}
					if($i == 1){
						$package = new Orderpackage();
						$package->order_id = $order->order_id;
						$package->package_type = trim($v[0]['包裹类型']) ?:'BOX';
						$package->quantity = trim($v[0]['包裹总件数']);
						$package->unit = 'CM';
						$package->length = '22';
						$package->width = '22';
						$package->height = '2.22';
						$package->weight = trim($v[0]['包裹总重量']) ?:'1';
						$package->weight_unit = 'KG';
						$package->save();
					}
					//带电产品数量
					$dian_count = 0;
					//申报总价值
					$total_amount = 0;
					//订单明细
					foreach ($v as $v2){
						$orderproduct = new Orderproduct();
						$orderproduct->order_id = $order->order_id;
						$orderproduct->product_name = $v2['中文品名'];
						//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
						$orderproduct->product_name_en = preg_replace('/[　\s]+/u',' ',$v2['英文品名']);
						$orderproduct->hs_code = $v2['海关编码'];
						$orderproduct->product_name_far = $v2['中文品名'];
						$orderproduct->product_name_en_far = preg_replace('/[　\s]+/u',' ',$v2['英文品名']);
						$orderproduct->hs_code_far = $v2['海关编码'];
						$orderproduct->declaration_price = $v2['申报单价'];
						$orderproduct->product_quantity = $v2['申报数量'];
						//$orderproduct->material_use = $v2['材质用途'];
						$orderproduct->material_use = $v2['材质'].' '.$v2['用途'];
						$orderproduct->has_battery = $v2['是否带电'] == '是' ? '1' : '0';
						$orderproduct->goods_info = $v2['配货信息'];
						$orderproduct->save();
						if($v2['是否带电'] == '是'){
							$dian_count += $v2['申报数量'];
							$order->has_battery = 1;
						}
						$total_amount += $v2['申报单价'] * $v2['申报数量'];
					}
					$order->total_amount = $total_amount;
					$order->currency_code = 'USD';
					if($dian_count > 2){
						$order->has_battery_num = 2;
					}else{
						$order->has_battery_num = 1;
					}
					$order->save();
				}
				return $this->_redirectMessage('导入成功', '成功', url('order/batchimport'));
			}
			// 			echo "<pre>";
			// 			print_r($data);
			// 			exit;
			
		}
	}
	/**
	 * @todo   获取客户单号
	 * @author stt
	 * @since  2020-11-11
	 * @return
	 * @link   #83700
	 */
	function actiongetordernos(){
		//阿里单号
		$ali_order_nos = explode(',', request('ali_order_nos'));
		//返回数据
		$data = array();
		foreach ($ali_order_nos as $ali_order_no){
			//是否已经存在
			$is_exist = false;
			$order = Order::find('ali_order_no=?',$ali_order_no)->getOne();
			if (!$order->isNewRecord()){
				//判断小标签是否存在
				$filename = $order->order_no.'_label.pdf';
				$label_pdfexist = Helper_PDF::pdfisexist($filename);
				if ($label_pdfexist['message']!='noexist'){
					//返回url
					$data[]['url'] = $label_pdfexist['url'];
					$is_exist = true;
				}
			}
			//非阿里订单可生成
			if (!$is_exist){
				//创建小标签PDF
				$time = date('YmdHis',time());
				Helper_Common::getcommonlabel($ali_order_no,$time);
				$uploadoss = new Helper_AlipicsOss();
				//上传到oss
				$dir=Q::ini('upload_tmp_dir');
				$label_data = $uploadoss->uploadAlifiles($ali_order_no.$time.'_label.pdf');
				if ($uploadoss->doesExist($ali_order_no.$time.'_label.pdf')){
					//上传成功，删除
					@unlink($dir.DS.$ali_order_no.$time.'_label.pdf');
				}
				$filename = $ali_order_no.$time.'_label.pdf';
				$newlabel_pdfexist = Helper_PDF::pdfisexist($filename);
				//返回url
				$data[]['url'] = $newlabel_pdfexist['url'];
			}
		}
		return json_encode($data);
	}
	/**
	 * @todo   批量导出末端面单功能
	 * @author stt
	 * @since  2021年1月25日10:05:07
	 * @return
	 * @link   #85487
	 */
	function actionoutputlabel(){
		ini_set('max_execution_time', '0');
		set_time_limit(0);
		$dir=Q::ini('upload_tmp_dir');
		// 最终生成的文件名（含路径）
		$filename = $dir . DS . date('YmdHis') . ".zip";
		// 生成文件
		// 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
		$zip = new ZipArchive();
		if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
			return $this->_redirectMessage('无法打开文件，或者文件创建失败', '失败', url('order/search'));
		}
		$select = Order::find();
		//多运单号搜索
		$waybill_back=array();
		if(request('waybill_codes')){
			$waybill_codes=explode(",", request('waybill_codes'));
			//去除数组中的空格
			foreach ($waybill_codes as $k=>$code){
				$waybill_codes[$k]=ltrim($code);
			}
			$waybill_codes=array_filter($waybill_codes);//去空
			$waybill_codes=array_unique($waybill_codes);//去重
			//#83709子单号批量搜索
			$suborder_ids = Helper_Array::getCols ( Subcode::find ( "sub_code in (?)", $waybill_codes )->getAll (), "order_id" );
			//根据子单号搜索到订单
			//增加国内单号搜索
			if (! empty ( $suborder_ids )) {
				$select->where('order_id in (?) or ali_order_no in (?) or far_no in (?) or tracking_no in (?) or total_list_no in (?) or order_no in (?) or reference_no in (?)',$suborder_ids,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes);
			}else{
				//原来的搜索
				$select->where('ali_order_no in (?) or far_no in (?) or tracking_no in (?) or total_list_no in (?) or order_no in (?) or reference_no in (?)',$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes,$waybill_codes);
			}
		}
		$orders = $select->getAll();
		//面单是否存在
		$flag = 0;
		if (count($orders)){
			foreach ($orders as $l) {
				if(!empty($l->tracking_no)){
					$res = $l->tracking_no.'.pdf';
					$filename1 = Helper_PDF::pdfisexist($res);
					if ($filename1['message']!='noexist') {
						//有面单存在
						$flag = 1;
						//面单临时存放位置
						$alilabel = $dir.DS.$l->tracking_no.'.pdf';
						$target = file_get_contents($filename1['url']);
						file_put_contents($alilabel,$target);
						// 第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
						$zip->addFile($alilabel, $res);
					}
				}
			}
			if ($flag==1){
				$zip->close(); // 关闭
				// 下面是输出下载;
				header("Cache-Control: max-age=0");
				header("Content-Description: File Transfer");
				// 文件名
				header('Content-disposition: attachment; filename=' . basename($filename));
				// zip格式的
				header("Content-Type: application/zip");
				// 告诉浏览器，这是二进制文件
				header("Content-Transfer-Encoding: binary");
				// 告诉浏览器，文件大小
				header('Content-Length: ' . filesize($filename));
				// 输出文件;
				@readfile($filename);
				unlink($filename);
				//删除临时存放的文件
				foreach ($orders as $l) {
					@unlink($dir.DS.$l->tracking_no.'.pdf');
				}
			}else{
				return $this->_redirectMessage('导出面单失败，面单不存在', '失败', url('order/search'));
			}
		}else{
			return $this->_redirectMessage('导出面单失败，订单不存在', '失败', url('order/search'));
		}
	}
	
}