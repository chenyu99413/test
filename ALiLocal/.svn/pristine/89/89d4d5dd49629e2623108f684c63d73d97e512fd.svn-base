<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';

class Controller_ReturnOrder extends Controller_Abstract {
	/**
	 * @todo   退件入库总单列表
	 * @author 吴开龙
	 * @since  2020-11-13 09:18:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnTotal() {
		$total = ReturnTotal::find();
		//创建时间
		if(request('start_date')){
			$total->where('create_time >= ?',strtotime(request('start_date').'00:00:00'));
		}
		//结束时间
		if(request('end_date')){
			$total->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//总单单号
		if(request('return_total_no')){
			$total_list_no=explode("\r\n", request('return_total_no'));
			//去空
			$total_list_no=array_filter($total_list_no);
			//去重
			$total_list_no=array_unique($total_list_no);
			$total->where('return_total_no in (?)',$total_list_no);
		}
		
		//分页
		$pagination = null;
		$total=$total->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->order('return_total_id desc')->getAll();
		$this->_view['total']=$total;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * @todo   退件入库总单状态修改
	 * @author 吴开龙
	 * @since  2020-11-13 09:38:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnStatus() {
		if(request('return_total_id')){
			$no_sub_code = array();
			//根据总单号查出退货订单表所有数据
			$r_orders = ReturnOrder::find('return_total_id in (?)',request('return_total_id'))->getAll();
			//如果数据不为空就判断是否完全扫描结束
			if(count($r_orders)){
				foreach ($r_orders as $o1){
					//循环获取所有ali订单表id
					$order_id[] = $o1->order_id;
					$return_order_id[] = $o1->return_order_id;
				}
				//获取已扫描的子单号
				$package = ReturnPackage::find('return_order_id in (?)',$return_order_id)->getAll();
				foreach ($package as $p){
					if($p->sub_code){
						$true_sub_code[] = $p->sub_code;
					}
				}
				//获取未扫描的子单号
				$sub_codes = Subcode::find('order_id in (?)',$order_id)->getAll();
				foreach ($sub_codes as $s){
					//判断如果不在已扫描里面就是为扫描单号
					if(!in_array($s->sub_code,$true_sub_code)){
						$no_sub_code[] = $s->sub_code;
					}
				}
			}
			if(count($no_sub_code)){
				return $this->_redirectMessage('未完全扫描结束', '', url('/returntotal'));
			}else{
				$total = ReturnTotal::find('return_total_id=?',request('return_total_id'))->getOne();
				$total->status = 1;
				$total->save();
			}
		}
		return $this->_redirect(url('/returntotal'));
	}
	/**
	* @todo   退件入库扫描列表界面
	* @author 吴开龙
	* @since  2020-11-10 09:58:55
	* @param 
	* @return view
	* @link   #83699
	 */
	function actionReturnIn() {
		$all_sub_code = array();
		$true_sub_code = array();
		$no_sub_code = array();
		$total = ReturnTotal::find('return_total_id=?',request('return_total_id'))->getOne();
		//根据总单获取所有阿里订单的order_id
		if(request('return_total_id')){
			$order_id = array();
			$return_order_id = array();
			$r_orders = ReturnOrder::find('return_total_id = ?',request('return_total_id'))->getAll();
			if(count($r_orders)){
				foreach ($r_orders as $o1){
					//订单id数组
					$order_id[] = $o1->order_id;
					$return_order_id[] = $o1->return_order_id;
				}
				//获取已扫描的子单号
				$package = ReturnPackage::find('return_order_id in (?)',$return_order_id)->getAll();
				foreach ($package as $p){
					if($p->sub_code){
						$true_sub_code[] = $p->sub_code;
					}
				}
				//获取未扫描的子单号
				$sub_codes = Subcode::find('order_id in (?)',$order_id)->getAll();
				foreach ($sub_codes as $s){
					//获取全部单号
					$all_sub_code[] = $s->sub_code;
					//判断如果不在已扫描里面就取出放入但号不全目录里
					if(!in_array($s->sub_code,$true_sub_code)){
						$no_sub_code[] = $s->sub_code;
					}
				}
			}
		}
		$this->_view['total']=$total;
		$this->_view['all_sub_code']=$all_sub_code;
		$this->_view['true_sub_code']=$true_sub_code;
		$this->_view['no_sub_code']=$no_sub_code;
	}
	/**
	 * @todo   退件入库保存ajax返回
	 * @author 吴开龙
	 * @since  2020-11-13 17:52:55
	 * @param
	 * @return json
	 * @link   #83699
	 */
	function actionReturnInAjax(){
		if(request_is_post()){
			//返回数据初始化
			$data = array(
				'code' => 0,
				'msg' => ''
			);
			//判断总单是否存在
			if(!$_POST['return_total_id']){
				//如果不存在则新建总单表
				$total = new ReturnTotal();
				$total->return_total_no = date('YmdHis');
				$total->operate_name = MyApp::currentUser('staff_name');
				$total->operate_id = MyApp::currentUser('staff_id');
				$total->save();
				$return_total_id = $total->return_total_id;
				$return_total_no = $total->return_total_no;
			}else{
				//总单存在
				$return_total_id = $_POST['return_total_id'];
				$return_total_no = $_POST['return_total_no'];
			}
			//获取订单信息
			$order = Order::find('order_id=?',$_POST['order_id'][0])->getOne();
			//计算总件数和总重量
			//退货总件数
			$return_num = 0;
			foreach ($_POST['quantity_out'] as $quantity){
				$return_num += $quantity;
			}
			//退货总重量
			$return_weight = 0;
			foreach ($_POST['weight_out'] as $weight){
				$return_weight += $weight;
			}
			$scan_no = '';
			//判断是否已存在
			$returnorder = ReturnOrder::find('order_id=?',$order->order_id)->getOne();
			if(!$returnorder->isNewRecord()){
				//订单存在时执行
				foreach ($_POST['type'] as $k => $v){
					//循环type，如果存在则说明点击的这一列，并将它(子单号)添加到同列包裹里面
					if($v){
						//添加子单号以及修改其他可编辑包裹信息
						$returnpackage = ReturnPackage::find('return_package_id=?',$_POST['return_package_id'][$k])->getOne();
						if($returnpackage->sub_code){
							$data['msg'] = '此包裹已存在子单号';
							return json_encode($data);
						}
						$scan_no = trim($v);
						$returnpackage->sub_code = trim($v);
						$returnpackage->quantity = $_POST['quantity_out'][$k];
						$returnpackage->weight = $_POST['weight_out'][$k];
						$returnpackage->length = $_POST['length_out'][$k];
						$returnpackage->width = $_POST['width_out'][$k];
						$returnpackage->height = $_POST['height_out'][$k];
						$returnpackage->note = $_POST['note'][$k];
						$returnpackage->save();
						//重新计算总件数和总重量
						$returnorder->return_num = $return_num;
						$returnorder->return_weight = $return_weight;
						$returnorder->save();
					}
				}
			}else{
				//退货单不存在说明第一次扫描此订单的单号
				
				//计算原件数和重量
				$num = 0;
				$weight = 0;
				$out_package = Faroutpackage::find('order_id=?',$order->order_id)->getAll();
				foreach ($out_package as $op){
					$num += $op->quantity_out;
					$weight += $op->weight_out;
				}
				//保存退货订单信息
				$returnorder = new ReturnOrder();
				//复制原订单数据
				$y_order = Order::find('ali_order_no=?',$order->ali_order_no)->asArray()->getOne();
				$returnorder->changeProps($y_order);
				$returnorder->channel_id = null;
				$returnorder->department_id = MyApp::currentUser('department_id');
				$returnorder->order_id = $order->order_id;
				$returnorder->tracking_no = $order->tracking_no;
				//阿里订单号前面加R
				$returnorder->ali_order_no = 'R'.$order->ali_order_no;
				$returnorder->return_total_id = $return_total_id;
				$returnorder->order_status = '10';
				$returnorder->return_time = time();
				$returnorder->original_num = $num;
				$returnorder->original_weight = $weight;
				$returnorder->return_num = $return_num;
				$returnorder->return_weight = $return_weight;
				$returnorder->scan_id = MyApp::currentUser('staff_id');
				$returnorder->scan_name = MyApp::currentUser('staff_name');
				$returnorder->save();
				//保存订单明细信息
				$y_product = Orderproduct::find('order_id=?',$order->order_id)->asArray()->getAll();
				foreach ($y_product as $yp){
					$product = new ReturnOrderproduct();
					$product->changeProps($yp);
					$product->return_order_id = $returnorder->return_order_id;
					$product->save();
				}
				//保存包裹信息
				foreach ($_POST['type'] as $k => $v){
					$package = new ReturnPackage();
					$package->return_order_id = $returnorder->return_order_id;
					$package->quantity = $_POST['quantity_out'][$k];
					$package->weight = $_POST['weight_out'][$k];
					$package->length = $_POST['length_out'][$k];
					$package->width = $_POST['width_out'][$k];
					$package->height = $_POST['height_out'][$k];
					$package->note = $_POST['note'][$k];
					$package->sub_code = trim($v);
					if($v){
						$scan_no = trim($v);
						$package->sub_code = trim($v);
					}
					$package->save();
				}
			}
			//全部子单号封装为数组
			$sub_code = explode("\r\n",$_POST['sub_code']);
			$sub_code = array_filter($sub_code);
			//拼接上扫描的单号
			$sub_code[] = $scan_no;
			//根据总单获取所有阿里订单的order_id
			$order_id = array();
			$r_orders = ReturnOrder::find('return_total_id in (?)',$return_total_id)->getAll();
			foreach ($r_orders as $o1){
				//订单id数组
				$order_id[] = $o1->order_id;
			}
			$no_sub_code = array();
			//子单号表数据
			$sub_codes = Subcode::find('order_id in (?)',$order_id)->getAll();
			foreach ($sub_codes as $s){
				//判断如果不在已扫描里面就取出放入但号不全目录里
				if(!in_array($s->sub_code,$sub_code)){
					$no_sub_code[] = $s->sub_code;
				}
			}
			
			
			$data['return_total_id'] = $return_total_id;
			$data['scan_no'] = $scan_no;
			$data['sub_code'] = $sub_code;
			$data['no_sub_code'] = $no_sub_code;
			//成功返回
			$data['code'] = 1;
			$data['msg'] = '保存成功';
			$data['data'] = $data;
			return json_encode($data);
		}
	}
	/**
	 * @todo   退件入库扫描ajax返回页面
	 * @author 吴开龙
	 * @since  2020-11-10 17:22:55
	 * @param
	 * @return json
	 * @link   #83699
	 */
	function actionInScan() {
		//返回数据初始化
		$data = array(
			'code' => 0,
			'msg'  => ''
		);
		//验证订单正确性 查出子单信息
		$subcode = Subcode::find('sub_code=?',request('scan_no'))->getOne();
		if($subcode->isNewRecord()){
			$data['msg'] = '未找到订单';
			return json_encode($data);
		}
		//查询订单
		$order = Order::find('order_id=?',$subcode->order_id)->getOne();
		
		//验证是否已添加
		$returnpackage = ReturnPackage::find('sub_code=?',request('scan_no'))->getOne();
		if(!$returnpackage->isNewRecord()){
			$data['msg'] = '已扫描';
			return json_encode($data);
		}
		
		
		
// 		$returnin = ReturnOrder::find('tracking_no=?',$order->tracking_no)->getOne();
// 		if(!$returnin->isNewRecord()){
// 			$data['msg'] = '订单已扫描';
// 			return json_encode($data);
// 		}
		//验证是否有出库包裹
		$out_package = Faroutpackage::find('order_id=?',$order->order_id)->getAll();
		if(!count($out_package)){
			$data['msg'] = '订单无出库包裹';
			return json_encode($data);
		}
		$returnin = ReturnOrder::find('order_id=?',$order->order_id)->getOne();
		//判断是否已经在其他总单扫描过
		if((!$returnin->isNewRecord() && !request('return_total_id')) || (!$returnin->isNewRecord() && $returnin->return_total_id != request('return_total_id'))){
			$data['msg'] = '该订单已在其他总单里面扫描';
			return json_encode($data);
		}
		//判断退货包裹是否添加过
		if(!$returnin->isNewRecord()){
			$returnpackage = ReturnPackage::find('return_order_id=?',$returnin->return_order_id)->order('sub_code')->getAll();
			//如果有退货包裹，退货包裹信息数据组装进data1
			foreach ($returnpackage as $k => $package){
				$data1[$k]['order_id'] = $order->order_id;
				$data1[$k]['ali_order_no'] = $order->ali_order_no;
				//已扫描的子单号信息
				$data1[$k]['return_package_id'] = $package->return_package_id;
				$data1[$k]['tracking_no'] = $package->sub_code;
				$data1[$k]['quantity_out'] = $package->quantity;
				$data1[$k]['weight_out'] = $package->weight;
				$data1[$k]['length_out'] = $package->length;
				$data1[$k]['width_out'] = $package->width;
				$data1[$k]['height_out'] = $package->height;
			}
		}else{
			//出库包裹信息数据组装进data1
			foreach ($out_package as $k => $package){
				$data1[$k]['order_id'] = $order->order_id;
				$data1[$k]['ali_order_no'] = $order->ali_order_no;
				$data1[$k]['return_package_id'] = '';
				$data1[$k]['tracking_no'] = '';
				$data1[$k]['quantity_out'] = $package->quantity_out;
				$data1[$k]['weight_out'] = $package->weight_out;
				$data1[$k]['length_out'] = $package->length_out;
				$data1[$k]['width_out'] = $package->width_out;
				$data1[$k]['height_out'] = $package->height_out;
			}
		}
		
		
		//获取子单号
		$subcode = Subcode::find('order_id=?',$order->order_id)->getAll();
		//子单号信息组装进data2
		foreach ($subcode as $s){
			$data2[] = $s->sub_code;
		}
		//组装返回数据
		$data['code'] = 1;
		$data['outpackage'] = $data1;
		$data['subcode'] = $data2;
		return json_encode($data);
		exit;
	}
	/**
	 * @todo   退件入库订单列表界面
	 * @author 吴开龙
	 * @since  2020-11-11 16:02:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnList() {
		//列表，仓库权限
		$orders=ReturnOrder::find("department_id in (?)",RelevantDepartment::relateddepartmentids());
		//计算仓储时间
		$oo = ReturnOrder::find('order_status in (?)',array('10','20','30','40','50'))->getAll();
		foreach ($oo as $oo1){
			$oo1->storage_time = ceil((time()-$oo1->return_time)/60/60);
			$oo1->save();
		}
		
		if(request('return_total_no')){
			$total = ReturnTotal::find('return_total_no=?',request("return_total_no"))->getOne();
			$orders->where("return_total_id =?",$total->return_total_id);
		}
		//时间搜索
		if(request("start_date")){
			$orders->where("return_time >=?",strtotime(request("start_date").':00'));
		}
		if (request("end_date")){
			$orders->where("return_time <=?",strtotime(request("end_date").':59'));
		}
		//单号批量搜索
		if(request('order_no')){
			$waybill_codes=explode("\r\n", request('order_no'));
			$waybill_codes=array_filter($waybill_codes);//去空
			$waybill_codes=array_unique($waybill_codes);//去重
			$orders->where('ali_order_no in (?) or new_tracking_no in (?) or tracking_no in (?)',$waybill_codes,$waybill_codes,$waybill_codes);
		}
		
		//克隆对象
		$order_count=clone $orders;
		//根据状态分组查询
		$counts=$order_count->group('order_status')->count()->columns('order_status')->asArray()->getAll();
		//根据分组字段序列化数组
		$counts=Helper_Array::toHashmap($counts,'order_status','row_count');
		//counts[0]是【全部】状态
		$counts[0] = 0;
		foreach ($counts as $v){
			$counts[0]+=$v;
		}
		$active_id = 0;
		// 仓储中
		if (request ( "parameters" ) == "cangchuzhong") {
			$orders->where('order_status = 10');
			$active_id = 10;
		}
		// 已确认
		if (request ( "parameters" ) == "yiqueren") {
			$orders->where('order_status = 15');
			$active_id = 15;
		}
		// 待重发
		if (request ( "parameters" ) == "daichongfa") {
			$orders->where('order_status=20');
			$active_id = 20;
		}
		// 待销毁
		if (request ( "parameters" ) == "daixiaohui") {
			$orders->where('order_status=30');
			$active_id = 30;
		}
		// 待退回
		if (request ( "parameters" ) == "daituihui") {
			$orders->where('order_status=40');
			$active_id = 40;
		}
		// 已打印
		if (request ( "parameters" ) == "yidayin") {
			$orders->where('order_status=50');
			$active_id = 50;
		}
		// 已重发
		if (request ( "parameters" ) == "yichongfa") {
			$orders->where('order_status=60');
			$active_id = 60;
		}
		// 已销毁
		if (request ( "parameters" ) == "yixiaohui") {
			$orders->where('order_status=70');
			$active_id = 70;
		}
		// 已退回
		if (request ( "parameters" ) == "yituihui") {
			$orders->where('order_status=80');
			$active_id = 80;
		}
		//批量修改状态为已确认
		if(request('do') == '15'){
			$return_id = explode (',',request('return_id'));
			$list = $orders->where('order_status = ? and return_order_id in (?)','10',$return_id)->getAll();
			
			foreach ($list as $o){
				//判断扫描是否结束
				if($o->order_status == 10){
					$package = ReturnPackage::find('return_order_id=?',$o->return_order_id)->getAll();
					foreach ($package as $p){
						//判断扫描未结束跳过
						if(!$p->sub_code){
							continue 2;
						}
					}
				}
				//当批量转入已确认时复制入库包裹信息到出库包裹信息
				$rp = ReturnPackage::find('return_order_id=?',$o->return_order_id)->asArray()->getAll();
				foreach ($rp as $r){
					$rop = new ReturnOutPackage();
					$rop->changeProps($r);
					$rop->save();
				}
				//修改订单状态
				$o->order_status = request('do');
				$o->save();
			}
			return $this->_redirectMessage('成功','转入成功',url('/returnlist'));
			exit;
		}
		//批量修改状态
		if(request('do') == '20' || request('do') == '30' || request('do') == '40'){
			//只能已确认状态下使用
			if(request ( "parameters" ) != "yiqueren"){
				return $this->_redirectMessage('失败','只有“已确认”状态下的订单，才能进行批量转入已重发/已销毁/已退回',url('/returnlist'));
			}
			$return_id = explode (',',request('return_id'));
			$list = $orders->where('order_status in (?) and return_order_id in (?)',array('15','20','30','40','50'),$return_id)->getAll();
			
			foreach ($list as $o){
				//判断扫描是否结束
				if($o->order_status == 10){
					$package = ReturnPackage::find('return_order_id=?',$o->return_order_id)->getAll();
					foreach ($package as $p){
						//判断扫描未结束跳过
						if(!$p->sub_code){
							continue 2;
						}
					}
				}
				//修改订单状态
				$o->order_status = request('do');
				$o->save();
			}
			return $this->_redirectMessage('成功','转入成功',url('/returnlist'));
			exit;
		}
		//导出
		//1
		if(request('do') == '导出'){
			set_time_limit(0);
			ini_set('memory_limit', '-1');
			$order = $orders->asArray()->getAll();
			//创建一个excel空文件
			Helper_ExcelX::startWriter ( 'returnorder'.request("start_date").'-'.request("end_date")  );
			//写入表头 内容为$header,addRow为写入内容
			$header = array (
				'退货仓库','订单状态','末端单号','ALS单号','退货入库时间','入库操作人','仓储时间','重发单号','重发渠道','重发时间','重发操作人','销毁时间','销毁操作人','退回单号','退回时间','退回操作人','原件数','原重量','退货件数','退货重量','件数差异','重量差异'
			);
			Helper_ExcelX::addRow ($header);
			//循环写入数据，以每200条为节点
			$tmp_order = array ();
			foreach($order as $k => $v){
				$tmp_order[] = $v;
				if (count ( $tmp_order ) == '1000') {
					//写入数据的函数封装
					$this->receiveExportAddRow($tmp_order);
					//重置数组以便循环插入时不重复
					$tmp_order = array ();
				}
			}
			if (count ( $tmp_order )) {
				$this->receiveExportAddRow($tmp_order);
			}
			//写入结束
			Helper_ExcelX::closeWriter ();
			exit();
		}
		//查询
		$pagination = null;
		$list=$orders->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )
		->order('return_order_id desc')->getAll();
		
// 		echo "<pre>";
// 		echo (request ( "parameters" ));
// 		exit;
		$parameters=request ( "parameters" );
		$this->_view ["orders"] = $list;
		$this->_view ['pagination']=$pagination;
		$this->_view ["parameters"] = $parameters;
		$this->_view ["active_id"] = $active_id;
		$this->_view ["counts"] = $counts;
		$this->_view ["tabs"] = $this->createTabs ( $counts );
	}
	/**
	 * @todo   退件入库订单明细界面
	 * @author 吴开龙
	 * @since  2020-11-14 13:52:55
	 * @param
	 * @return view
	 * @link   #84411
	 */
	function actionReturnEdit(){
		//退件订单信息表
		$order = ReturnOrder::find('return_order_id=?',request('return_order_id'))->getOne();
		$this->_view ["order"] = $order;
		//退件入库包裹表
		$returnpackage = ReturnPackage::find('return_order_id=?',request('return_order_id'))->getAll();
		$this->_view ["returnpackage"] = $returnpackage;
		$product = ReturnOrderproduct::find('return_order_id=?',$order->return_order_id)->getAll();
		$this->_view ["product"] = $product;
		//原始出库包裹信息
		$faroutpackage = Faroutpackage::find('order_id=?',$order->order_id)->getAll();
		$this->_view ["faroutpackage"] = $faroutpackage;
		//退货出库包裹信息
		$returnoutpackage = Returnoutpackage::find('return_order_id=?',$order->return_order_id)->getAll();
		$this->_view ["returnoutpackage"] = $returnoutpackage;
		$msg = 0;
		if(request_is_post()){
			$order->changeProps($_POST);
			$order->save();
			$msg = 1;
		}
		$this->_view ["msg"] = $msg;
// 		echo "<pre>";
// 		print_r($returnpackage);
// 		exit;
	}
	/**
	 * @todo   退件入库订单明细界面保存包裹ajax
	 * @author 吴开龙
	 * @since  2020-11-14 17:22:55
	 * @param
	 * @return view
	 * @link   #84411
	 */
	function actionSavePackages(){
		if(count($_POST)){
			//循环添加
			foreach ($_POST['return_package_id'] as $k => $v){
				//判断是入库还是出库包裹
				if(request('type') == 1){
					$r_p = ReturnPackage::find('return_package_id=?',$v)->getOne();
				}else{
					$r_p = ReturnOutPackage::find('return_package_id=?',$v)->getOne();
				}
				//添加数据
				$r_p->quantity = $_POST['quantity'][$k];
				$r_p->length = $_POST['length'][$k];
				$r_p->width = $_POST['width'][$k];
				$r_p->height = $_POST['height'][$k];
				$r_p->weight = $_POST['weight'][$k];
				$r_p->save();
			}
		}
		return json_encode(array('code'=>1,'msg'=>'修改成功'));
	}
	/**
	 * @todo   保存订单明细信息
	 * @author 吴开龙
	 * @since  2020-11-14 15:52:55
	 * @param
	 * @return view
	 * @link   #84411
	 */
	function actionProductSave() {
		if (request ( "price" )) {
			$order = ReturnOrder::find ( "return_order_id = ?", request ( "return_order_id" ) )->getOne ();
			$p1 = request ( "price" );
			$price = ReturnOrderproduct::find ( "order_product_id = ?", $p1 ["order_product_id"] )->getOne ();
			$price->order_id = $order->order_id;
			$price->return_order_id = $order->return_order_id;
			$p1['product_name'] = $p1['product_name_far'];
			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
			$p1['product_name_en'] = preg_replace('/[　\s]+/u',' ',$p1['product_name_en_far']);
			$p1['hs_code'] = $p1['hs_code_far'];
			$p1['product_unit'] = 'pcs';
			$price->changeProps ( $p1 );
			$price->save ();
			//修改申报总价值
			$o_product = ReturnOrderproduct::find("return_order_id = ?", request ( "return_order_id" ))->getAll();
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
	 * @todo   删除订单明细信息
	 * @author 吴开龙
	 * @since  2020-11-14 15:32:55
	 * @param
	 * @return json
	 * @link   #84411
	 */
	function actionProductDel() {
		if (request ( "order_product_id" )) {
			$price = ReturnOrderproduct::find ( "order_product_id = ?", request ( "order_product_id" ) )->getOne ();
			$order = ReturnOrder::find ( "return_order_id = ?", $price->return_order_id )->getOne ();
			$price->destroy ();
			//修改申报总价值
			$o_product = ReturnOrderproduct::find("order_id = ?", $order->order_id)->getAll();
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
	 * @todo   导出具体写入函数
	 * @author 吴开龙
	 * @since  2020-11-30 09:57:08
	 * @return 
	 * @link   #83699
	 */
	function receiveExportAddRow($fee_select){
		foreach ($fee_select as $num => $value){
			//状态
			$status = ReturnOrder::$status[$value['order_status']];
			//重发和退回单号显示问题
			$chongfa = '';
			$tuihui = '';
			$chongfaren = '';
			$tuihuiren = '';
			$xiaohuiren = '';
			if( in_array($value['order_status'], array('20','50','60'))){
				//重发
				$chongfa = $value['new_tracking_no'];
				$chongfaren = $value['again_name'];
			}elseif ( in_array($value['order_status'], array('40','80'))){
				//退回
				$tuihui = $value['new_tracking_no'];
				$tuihuiren = $value['again_name'];
			}elseif ( in_array($value['order_status'], array('30','70'))){
				//销毁
				$xiaohuiren = $value['again_name'];
			}
			//原件数、重量
			$faroutpackage = Faroutpackage::find('order_id=?',$value['order_id'])->getAll();
			$y_num = 0;
			$y_weight = 0;
			foreach ($faroutpackage as $fop){
				$y_num += $fop->quantity_out;
				$y_weight += $fop->weight_out;
			}
			//退货件数、重量
			$package = Returnpackage::find('return_order_id=?',$value['return_order_id'])->getAll();
			$num = 0;
			$weight = 0;
			//累加
			foreach ($package as $p){
				$num += $p->quantity;
				$weight += $p->weight;
			}
			//插入数据
			$sheet =array(
				Department::find('department_id=?',$value['department_id'])->getOne()->department_name,
				$status,
				$value['tracking_no'],
				$value['ali_order_no'],
				date('Y-m-d H:i:s',$value['return_time']),
				$value['scan_name'],
				$value['storage_time'],
				$chongfa,
				ReturnChannel::find('channel_id=?',$value['channel_id'])->getOne()->channel_name,
				$value['again_time'] ? date('Y-m-d H:i:s',$value['again_time']) : '',
				$chongfaren,
				$value['destroy_time'] ? date('Y-m-d H:i:s',$value['destroy_time']) : '',
				$xiaohuiren,
				$tuihui,
				$value['send_back_time'] ? date('Y-m-d H:i:s',$value['send_back_time']) : '',
				$tuihuiren = '',
				$y_num,
				$y_weight,
				$num,
				$weight,
				$y_num - $num,
				$y_weight - $weight
			);
			//输出
			Helper_ExcelX::addRow ( $sheet );
		}
	}
	/**
	 * @todo   退件入库订单列表创建标签
	 * @author 吴开龙
	 * @since  2020-11-11 16:32:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function createTabs($counts) {
		return array (
			array (
				"id" => "0","title" => "全部","count" => val($counts,0,0),
				"href" => "javascript:TabSwitch()"
			),
			array (
				"id" => "10","title" => "仓储中","count" => val($counts,10,0),
				"href" => "javascript:TabSwitch('cangchuzhong')"
			),
			array (
				"id" => "15","title" => "已确认","count" => val($counts,15,0),
				"href" => "javascript:TabSwitch('yiqueren')"
			),
			array (
				"id" => "20","title" => "待重发","count" => val($counts,20,0),
				"href" => "javascript:TabSwitch('daichongfa')"
			),
			array (
				"id" => "30","title" => "待销毁","count" => val($counts,30,0),
				"href" => "javascript:TabSwitch('daixiaohui')"
			),
			array (
				"id" => "40","title" => "待退回","count" => val($counts,40,0),
				"href" => "javascript:TabSwitch('daituihui')"
			),
			array (
				"id" => "50","title" => "已打印","count" => val($counts,50,0),
				"href" => "javascript:TabSwitch('yidayin')"
			),
			array (
				"id" => "60","title" => "已重发","count" => val($counts,60,0),
				"href" => "javascript:TabSwitch('yichongfa')"
			),
			array (
				"id" => "70","title" => "已销毁","count" => val($counts,70,0),
				"href" => "javascript:TabSwitch('yixiaohui')"
			),
			array (
				"id" => "80","title" => "已退回","count" => val($counts,80,0),
				"href" => "javascript:TabSwitch('yituihui')"
			)
		);
	}
	/**
	 * @todo   退件渠道
	 * @author 吴开龙
	 * @since  2020-11-12 10:32:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnChannel() {
		
		$this->_view ['channels'] = ReturnChannel::find ()->getAll ();
	}
	/**
	 * @todo   退件渠道编辑页面
	 * @author 吴开龙
	 * @since  2020-11-12 11:11:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnChannelEdit() {
		$channel=ReturnChannel::find('channel_id=?',request('channel_id'))->getOne();
		if(request_is_post()){
			//判断渠道名称是否存在
			$channel_check=ReturnChannel::find('channel_name=?',request('channel_name'))->getOne();
			if(!$channel_check->isNewRecord() && ($channel_check->channel_id!=request('channel_id'))){
				return $this->_redirectMessage('退件渠道编辑失败', '退件渠道名称已存在', url('/returnchanneledit',array('channel_id'=>$channel->channel_id)),2);
			}else{
				//$sender_id = @join ( ',', request ( 'sender_id' ) );
				$channel=ReturnChannel::find('channel_id=?',request('channel_id'))->getOne();
				$channel->channel_name=request('channel_name');
				$channel->channel_group_id=request('channel_group_id');
				$channel->network_code=request('network_code');
				$channel->trace_network_code=request('trace_network_code');
				//$channel->sender_id=$sender_id;
				$channel->account=request('account');
				$channel->print_method=request('print_method');
				$channel->supplier_id=request('supplier_id');
				$channel->label_sign=request('label_sign');
				$channel->check_complete=request('check_complete');
				$channel->send_kj=request('send_kj');
				$channel->has_battery=request('has_battery');
				$channel->is_declaration=request('is_declaration');
				$channel->sort_code=request('sort_code');
				
				$channel->length=request('length');
				$channel->width=request('width');
				$channel->height=request('height');
				$channel->perimeter=request('perimeter');
				
				$channel->girth=request('girth');
				$channel->weight=request('weight');
				$channel->total_cost_weight=request('total_cost_weight');
				$channel->declare_threshold=request('declare_threshold');
				$channel->type=request('type');
				$channel->forecast_type=request('forecast_type');
				$channel->is_pda=request('is_pda');
				$channel->postcode_verify=request('postcode_verify');
				$channel->save();
				
				//保存可用部门
				ReturnChanneldepartmentavailable::find('channel_id=?',$channel->channel_id)->getAll()->destroy();
				foreach (explode(',', request('department_hidden')) as $department_id){
					if(!$department_id){
						continue;
					}
					$available= new ReturnChanneldepartmentavailable();
					$available->changeProps(array(
						'channel_id'=>$channel->channel_id,
						'department_id'=>$department_id
					));
					$available->save();
				}
				return $this->_redirectMessage('退件渠道编辑', '退件渠道编辑成功', url('/returnchanneledit',array('channel_id'=>$channel->channel_id)));
			}
		}
		$this->_view['channel']=$channel;
		//筛选可用部门
		$available_department=ReturnChanneldepartmentavailable::find('channel_id=?',$channel->channel_id)->getAll();
		$this->_view ["department"] = array (
			"state" => true,
			"checked" => implode ( ",", Helper_Array::getCols ( $available_department, "department_id" ) )
		);
	}
	/**
	 * @todo   退件渠道成本管理页面
	 * @author 吴开龙
	 * @since  2020-11-23 13:11:55
	 * @param
	 * @return view
	 * @link   #83699
	 */
	function actionReturnChannelCost(){
		//产品渠道成本
		$channel = ReturnChannel::find ()->getAll ();
		$channelcost = ReturnChannelCost::find ( "channel_id = ?", request ( "channel_id" ) )->getOne ();
		$channelcostpprs = ReturnChannelcostppr::find ( "channel_cost_id = ?", $channelcost->channel_cost_id )->getAll ();
		$channelcostforluma = ReturnChannelCostformula::find ( "channel_cost_id = ?", $channelcost->channel_cost_id )->getAll ();
		$this->_view ["channelcost"] = $channelcost;
		$this->_view ["channelcostpprs"] = $channelcostpprs;
		$this->_view ["channelcostforluma"] = $channelcostforluma;
		$this->_view ["channel"] = $channel;
		if(request_is_post()){
// 			echo "<pre>";
// 			print_r($channelcost);
// 			print_r($_POST);
// 			exit;
			//保存主表数据
			$channelcost->changeProps ( $_POST['channelcost'] );
			$channelcost->channel_id = request ( "channel_id" );
			$channelcost->save ();
			//删除对应明细数据
			$channelcostpprs->destroy();
			//保存明细表数据
			foreach ($_POST['channel_cost_p_p_r_id'] as $k => $v){
				if(!$_POST['dropdown_price_manage'][$k]){
					continue;
				}
				$ppr = new ReturnChannelcostppr();
				$ppr->channel_cost_id = $channelcost->channel_cost_id;
				$ppr->price_manage_id = $_POST['dropdown_price_manage'][$k];
				$ppr->partition_manage_id = $_POST['dropdown_partition_manage'][$k];
				$ppr->remote_manage_id = $_POST['dropdown_remote_manage'][$k];
				$ppr->single_lowest_weight = $_POST['single_lowest_weight'][$k];
				$ppr->effective_time = strtotime($_POST['datebox_effective_time'.$v]);
				$ppr->invalid_time = strtotime($_POST['datebox_invalid_time'.$v]);
				$ppr->save();
			}
			return $this->_redirectMessage ( "退件渠道成本", "保存成功", url ( "/returnchannelcost", array (
				"channel_id" => $channelcost->channel_id
			) ) );
		}
	}
	/**
	 * @todo   退件渠道费用公式保存
	 * @author 吴开龙
	 * @since  2020-11-24 10:14:07
	 * @param
	 * @return json
	 * @link   #83699
	 */
	function actionSaveOperate(){
		//保存
		if (request_is_post ()) {
			//操作费
			if (request ( "formula" ) != null || strlen ( request ( "formula" ) ) > 0) {
				$value=json_decode ( request ( "formula" ),true);
				$formula =  ReturnChannelCostformula::find("channel_cost_formula_id=?",$value[0]["channel_cost_formula_id"])->getOne();
				if (request ( "delete_flag" ) == "true") {
					if (! $formula->isNewRecord ()) {
						$formula->destroy ();
					}
				}else{
					$value[0]['effective_time']=strtotime($value[0]['effective_time']."00:00:00");
					$value[0]['fail_time']=strtotime($value[0]['fail_time']."23:59:59");
					$supplier = Supplier::find('supplier = ?',$value[0]['supplier'])->getOne();
					$formula->changeProps ( $value[0] );
					$formula->supplier_id = $supplier->supplier_id;
					$formula->save ();
				}
				echo ($formula->channel_cost_formula_id);
			}
			exit;
		}
	}
	/**
	 * @todo   退件渠道发件人 导入
	 * @author 吴开龙
	 * @since  2020-11-12 14:14:07
	 * @param
	 * @return json
	 * @link   #83699
	 */
	function actionReturnChannelImport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//上传文件开始
		$uploader = new Helper_Uploader();
		//检查指定名字的上传对象是否存在
		if (! $uploader->existsFile ( 'file' )) {
			return $this->_redirectMessage('未上传文件','',url('/returnchanneledit'), 3 );
		}
		$file = $uploader->file ( 'file' );//获得文件对象
		if (! $file->isValid ( 'xls' )) {
			return $this->_redirectMessage('文件格式不正确：xls','',url('/returnchanneledit'), 3 );
		}
		$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
		$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
		$file->move ( $filename );
		ini_set ( "memory_limit", "3072M" );
		$xls = Helper_Excel::readFile ( $filename,true);
		$sheets =$xls->toHeaderMap ();
		$error = array ();
		//必填字段
		$required_fields = array (
			'发件人代码',
			'发件人姓名',
			'发件人公司',
			'发件人国家',
			'发件人省',
			'发件人市',
			'发件人电话',
			'发件人邮编',
			'发件人地址'
		);
		foreach ( $sheets as $k => $row ) {
			//判断基础信息不得为空
			//print_r(strtotime($row['登账日期']));exit;
			foreach ( $required_fields as $field ) {
				if (empty ( $row [$field] )) {
					$error [$k] [$field] = '必填数据不可为空';
				}
			}
		}
		//错误输出
		$this->_view ['errors'] = $error;
		if (empty ( $error )) {
			$sender_id = array();
			foreach ($sheets as $sheet){
				$sender = Sender::find('sender_code=?',$sheet['发件人代码'])->getOne();
				if($sender->isNewRecord()){
					$sender->sender_code = $sheet['发件人代码'];
					$sender->sender_name = $sheet['发件人姓名'];
					$sender->sender_company = $sheet['发件人公司'];
					$sender->sender_country = $sheet['发件人国家'];
					$sender->sender_province = $sheet['发件人省'];
					$sender->sender_city = $sheet['发件人市'];
					$sender->sender_area = @$sheet['发件人区县'];
					$sender->sender_phone = $sheet['发件人电话'];
					$sender->sender_zip_code = $sheet['发件人邮编'];
					$sender->sender_address = $sheet['发件人地址'];
					$sender->sender_email = @$sheet['发件人邮箱'];
					$sender->save();
				}
				$sender_id[] = $sender->sender_id;
			}
			$sender_id_str = implode(',', $sender_id);
			$channel = ReturnChannel::find('channel_id=?',request('channel_id'))->getOne();
			$channel->sender_id = $sender_id_str;
			$channel->save();
			return $this->_redirectMessage ( '导入成功', '成功', url ( '/returnchanneledit',array('channel_id' => request('channel_id')) ), 3 );
			exit ();
		}
	}
	/**
	 * @todo   退件渠道发件人 导出
	 * @author 吴开龙
	 * @since  2020-11-12 14:32:00
	 * @param
	 * @return json
	 * @link   #83699
	 */
	function actionReturnChannelExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		$channel = ReturnChannel::find ('channel_id=?',request('channel_id'))->getOne();
		$sender_id = explode(',',$channel->sender_id);
		$sender = Sender::find('sender_id in (?)',$sender_id)->getAll();
		$header = array (
			'发件人代码',
			'发件人姓名',
			'发件人公司',
			'发件人国家',
			'发件人省',
			'发件人市',
			'发件人区县',
			'发件人电话',
			'发件人邮编',
			'发件人地址',
			'发件人邮箱'
		);
		$sheet = array (
			$header
		);
		foreach ($sender as $value){
			$row =array(
				$value->sender_code,
				$value->sender_name,
				$value->sender_company,
				$value->sender_country,
				$value->sender_province,
				$value->sender_city,
				$value->sender_area,
				$value->sender_phone,
				$value->sender_zip_code,
				$value->sender_address,
				$value->sender_email
			);
			$sheet [] = $row;
		}
		Helper_Excel::array2xls ( $sheet, '退件渠道发件人导出.xls' );
		exit ();
	}
	/**
	 * @todo   退件渠道偏派邮编 导入
	 * @author 吴开龙
	 * @since  2020-11-02 14:52:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnChannelImportCode(){
		//主表数据
		$channelcode = ReturnChannelZipCode::find('channel_id=?',request('channel_id'));
		//查询条件
		if(request('zip_code')){
			$channelcode->where('zip_code = ?',request('zip_code'));
		}
		//导入
		if(request_is_post() && request('import') == '导入'){
			//程序运行时间
			ini_set('max_execution_time', '0');
			//程序运行内存
			ini_set('memory_limit', '2G');
			set_time_limit(0);
			//上传文件开始
			$uploader = new Helper_Uploader();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage('未上传文件','',url('/returnchannelimportcode',array('channel_id'=>request('channel_id'))), 3 );
			}
			//获得文件对象
			$file = $uploader->file ( 'file' );
			if (! $file->isValid ( 'xls' )) {
				return $this->_redirectMessage('文件格式不正确：xls','',url('/returnchannelimportcode',array('channel_id'=>request('channel_id'))), 3 );
			}
			//缓存路径
			$des_dir = Q::ini ( 'upload_tmp_dir' );
			$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
			$file->move ( $filename );
			ini_set ( "memory_limit", "3072M" );
			$xls = Helper_Excel::readFile ( $filename,true);
			$sheets =$xls->toHeaderMap ();
			// 		echo "<pre>";
			// 		print_r($sheets);
			// 		exit;
			//判断字段是否正确
			if(@!$sheets[0]['邮编']){
				return $this->_redirectMessage ( '导入失败', '邮编字段不存在', url ( '/returnchannelimportcode',array('channel_id' => request('channel_id')) ), 3 );
				exit ();
			}
			//删除原有数据
			ReturnChannelZipCode::meta()->destroyWhere('channel_id=?',request('channel_id'));
			//循环添加
			foreach ($sheets as $sheet){
				//判断邮编不存在跳出
				if(!$sheet['邮编']){
					break;
				}
				//添加新数据
				$zipcode = new ReturnChannelZipCode();
				//主表id
				$zipcode->channel_id = request('channel_id');
				$zipcode->zip_code = $sheet['邮编'];
				$zipcode->save();
			}
			return $this->_redirectMessage ( '导入成功', '成功', url ( '/returnchannelimportcode',array('channel_id' => request('channel_id')) ), 3 );
			exit ();
		}
		$pagination = null;
		$channelcode=$channelcode->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->getAll();
		
		$this->_view['channelcode']=$channelcode;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * @todo   退件渠道偏派邮编添加
	 * @author 吴开龙
	 * @since  2020-11-12 15:03:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnChannelImportCodeAdd(){
		//判断
		if (request('zip_code')){
			//添加数据
			$disabled = ReturnChannelZipCode::find ( "id=?", request ( "id" ) )->getOne ();
			$disabled->channel_id = request('channel_id');
			$disabled->zip_code = request('zip_code');
			$disabled->save ();
			//返回id
			echo ($disabled->id);
		}
		exit();
	}
	/**
	 * @todo   退件渠道偏派邮编删除
	 * @author 吴开龙
	 * @since  2020-11-12 15:03:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnDeleteZip(){
		//id为真执行
		if(request ( 'id' )){
			//删除
			ReturnChannelZipCode::meta()->destroyWhere('id = ?',request ( 'id' ));
		}
		exit;
	}
	/**
	 * @todo   退件重发扫描
	 * @author 吴开龙
	 * @since  2020-11-25 16:33:07
	 * @return json
	 * @link   #83699
	 */
	function actionCheckorder(){
		$rorder = ReturnOrder::find('ali_order_no=? or tracking_no=?','R'.request('ali_order_no'),request('ali_order_no'))->getOne();
		if($rorder->isNewRecord()){
			$data['message']='notexist';
		}
		if($rorder->order_status == 50){
			$data['message']='checkout';
		}
		if($rorder->order_status == 60 || $rorder->order_status == 70 || $rorder->order_status == 80){
			$data['message']='退件订单已出库';
		}
		//阿里订单号
		$data['ali_order_no']=request('ali_order_no');
		$data['channel_id']=$rorder->channel_id;
		echo json_encode($data);
		exit();
	}
	/**
	 * @todo   退件重发打印
	 * @author 吴开龙
	 * @since  2020-11-24 14:03:07
	 * @return json
	 * @link   #83699
	 */
	function actionCheckout() {
		if(request_is_post()){
			//初始化返回参数
			$data = array(
				'code' => 0,
				'message' => ''
			);
			//获取订单信息
			//$order = Order::find('ali_order_no=? or tracking_no=?',request('ali_order_no'),request('ali_order_no'))->getOne();
			$rorder = ReturnOrder::find('ali_order_no=? or tracking_no=?','R'.request('ali_order_no'),request('ali_order_no'))->getOne();
			//判断订单状态
			if($rorder->order_status == '10'){
				$data['message']='请先转入待重发';
				return json_encode($data);
			}
			$channel_all = ReturnChanneldepartmentavailable::availablechannelids($rorder->customer_id);
			//如果渠道不存在则计算出最优渠道来
			if(!$rorder->channel_id){
				//获取渠道成本
				$channelcost=ReturnChannelCost::find()->getAll();
				//判断产品下的渠道是否有权限
				$cc_channel = 0;
				foreach ($channelcost as $cc){
					if(in_array($cc->channel_id, $channel_all)){
						$cc_channel = 1;
					}
				}
				//判断
				//1
				//1
				if(!$cc_channel){
					$data['message']='wuyouhuaqudao';
					echo json_encode($data);
					exit;
				}
				if(count($channelcost)<=0){
					$data['message']='渠道成本不存在';
					return json_encode($data);
				}
				foreach ($channelcost as $temp){
					//判断渠道权限
					if(!in_array($temp->channel_id, $channel_all)){
						continue;
					}
					//获取价格-偏派-分区表
					$channelcostppr=ReturnChannelcostppr::find('channel_cost_id=? and effective_time<=? and invalid_time>=?',$temp->channel_cost_id,time(),time())->getOne();
					
					if($channelcostppr->isNewRecord()){
						continue;
					}
					$temp_channel = ReturnChannel::find('channel_id=?',$temp->channel_id)->getOne();
					
					//判断偏远邮编
					if($temp_channel->postcode_verify == 1){
						//取出渠道偏远邮编数据
						$zipcode = ChannelZipCode::find('channel_id = ? and zip_code=?',$temp_channel->channel_id,$rorder->consignee_postal_code)->getOne();
						//如果没有数据则跳过
						if($zipcode->isNewRecord()){
							continue;
						}
					}
					//是否支持带电
					if($rorder->has_battery==1){
						if ($temp_channel->has_battery!=1){
							continue;
						}
					}
					//是否支持PDA品类
					if($rorder->is_pda==1){
						if ($temp_channel->is_pda!=1){
							continue;
						}
					}
					//是否支持申报
					if($rorder->declaration_type=='DL'){
						if ($temp_channel->is_declaration!=1){
							continue;
						}
					}
					//申报总价阈值
					foreach ($rorder->faroutpackages as $faroutpackage){
						
						$arr = array($faroutpackage->length,$faroutpackage->width,$faroutpackage->height);
						sort($arr);
						//最长边限制
						if($temp_channel->length && $arr[2]>=$temp_channel->length){
							continue 2;
						}
						//第二长边限制
						if ($temp_channel->width && $arr[1]>=$temp_channel->width){
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
						if ($temp_channel->weight && $faroutpackage->weight>=$temp_channel->weight){
							continue 2;
						}
					}
					//申报总价阈值
					if ($temp_channel->declare_threshold){
						if($rorder->total_amount>$temp_channel->declare_threshold){
							continue;
						}
					}
					//整票计费重
					if ($temp_channel->total_cost_weight){
						if($rorder->weight_cost_out>$temp_channel->total_cost_weight){
							continue;
						}
					}
					/*
					  限制额度未引入
					 */
					//获取异形包装费
					$special_fee=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$rorder->order_id)->getOne();
					if($special_fee->isNewRecord()){
						$special_count=0;
					}else{
						$special_count=$special_fee->quantity;
					}
					$network=Network::find("network_code=? ",$temp_channel->network_code)->getOne();
					$quote= new Helper_Quote();
					// 					if ($rorder->customer->customs_code=='ALCN'){
// 						$cainiaofee = new Helper_CainiaoFee();
// 						$price=$cainiaofee->payment($rorder, $channelcostppr,$network->network_id,$special_count);
// 					}else{
					$price=$quote->returnpayment($rorder, $channelcostppr,$network->network_id,$special_count);
					//}
					//存在生效费用项无法计算
					if(@$price['success']=='formulaerror'){
						$data['message']=$price['success'];
						echo json_encode($data);
						exit();
					}
// 					if (count($price)&&$price['total_single_weight']){
// 						//取系统单件计费重和单件最低计费重中二者中较大者，得到的整票货的计费重
// 						$rorder->total_single_weight = $price['total_single_weight'];
// 						$rorder->save();
// 					}
					if(count($price)<=0){
						continue;
					}
					if(!$price['public_price']){
						continue;
					}
					
// 					//如果设置阈值
// 					if ($product->threshold){
// 						//计算应收
// 						$total_receivable = Fee::find("fee_type= '1' and order_id=?",$rorder->order_id)->getAll();
// 						$public_price = 0;
// 						foreach ($total_receivable as $tot_r){
// 							if($tot_r->currency != 'CNY'){
// 								$public_price += Helper_Quote::exchangeRate($rorder->warehouse_confirm_time,$tot_r->amount, $tot_r->currency,0,'',$tot_r->rate);
// 							}else{
// 								$public_price += $tot_r->amount;
// 							}
// 						}
// 						//应收-应付
// 						$maoli = $public_price-$price['public_price'];
// 						if ($maoli<$product->threshold){
// 							$flag = 1;
// 							continue;
// 						}
// 					}
					$price_array[$channelcostppr->channel_cost_p_p_r_id]=$price['public_price'];
					$price_info_array[$channelcostppr->channel_cost_p_p_r_id]=$price['price_info'];
					
					//循环结束
				}
				//判断是否有查询失败的报价
				if(!isset($price_array) || count($price_array)==0 || max($price_array)==0){
					//无可用渠道
					$data['message']='无可用渠道';
					return json_encode($data);
				}else{
					//获取最小的价格和价格表id
					$channel_cost_p_p_r_id=array_search(min($price_array), $price_array);
					$channel_cost_p_p_r=ReturnChannelcostppr::find('channel_cost_p_p_r_id=?',$channel_cost_p_p_r_id)->getOne();
					$channel_cost=ReturnChannelCost::find('channel_cost_id=?',$channel_cost_p_p_r->channel_cost_id)->getOne();
					//实际此时是产品代码proudct_code
					$account_name=$price_info_array[$channel_cost_p_p_r_id]['account'];
					$account_sync=Accountsync::find('product_code=?',$account_name)->getOne();
// 					$channel=ReturnChannel::find('channel_id = ?',$channel_cost->channel_id)->getOne();
					if(!$account_sync->isNewRecord()){
						$account_name=$account_sync->account;
					}
					//保存出库渠道
					$rorder->channel_id=$channel_cost->channel_id;
					$rorder->save();
					//调用打单方法
					$view=Helper_Common::getReturnLabel($rorder, $account_name,$channel_cost->channel_id);
					if(!isset($view['errormessage']) || $view['errormessage']!=''){
						//渠道获取面单失败
						$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
						return json_encode($data);
					}else{//结束
						//删除原有费用
						Fee::find("order_id=? and fee_type='2' and is_return=1",$rorder->order_id)->getAll()->destroy();
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
									'order_id'=>$rorder->order_id,
									'fee_type'=>'2',
									'fee_item_code'=>$fee_sub_code->sub_code,
									'fee_item_name'=>$fee_sub_code->item_name,
									'quantity'=>$fee_item['quantity'],
									'currency'=>$currency_code,
									'rate'=>$rate,
									'amount'=>$fee_item['fee'],
									'is_return'=> 1,
									'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $channel_cost->channel->supplier_id
								));
								$fee->save();
							}
						}
						$data['account']=$view['account'];
						$data['message']='true';
						return json_encode($data);
					}
				}
				//计算渠道结束
			}else{
				//判断渠道是否有权限
				if(!in_array($rorder->channel_id, $channel_all)){
					$data['message']="channel_id_no";
					echo json_encode($data);
					exit();
				}
				//判断渠道带电与否是否正确
				$channel_c1 = Channel::find('channel_id=?',$rorder->channel_id)->getOne();
				if($rorder->has_battery==1 && $channel_c1->has_battery!=1) {
					$data['message']="has_battery";
					echo json_encode($data);
					exit();
				}
				//是否支持PDA品类
				if($rorder->is_pda==1){
					if ($channel_c1->is_pda!=1){
						$data['message']="nopda";
						echo json_encode($data);
						exit();
					}
				}
				//是否支持申报
				if($rorder->declaration_type=='DL'){
					if ($channel_c1->is_declaration!=1){
						$data['message']="nobaoguan";
						echo json_encode($data);
						exit();
					}
				}
				//如果渠道存在，直接打单
				//首先判断渠道里的网络是不是UPS
				$channel_c=ReturnChannel::find("channel_id=?",$rorder->channel_id)->getOne();
				if(!$channel_c->isNewRecord() && ($channel_c->network_code=='UPS' || $channel_c->network_code=='EMS' || $channel_c->network_code=='FEDEX' || $channel_c->network_code=='US-FY' || $channel_c->network_code=='DHL' || $channel_c->network_code=='DHLE' || $channel_c->network_code=='YWML')){
					//查找渠道对应的渠道成本
					$channelcost_c=ReturnChannelCost::find('channel_id=?',$rorder->channel_id)->getOne();
					if (!$channelcost_c->isNewRecord()){
						$channelcostppr_c=ReturnChannelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c->channel_cost_id,time(),time())->getOne();
						if(!$channelcostppr_c->isNewRecord()){
							//先查找分区
							$partition_code='';
							$partition_code2='';
							$partion_c=Partition::find("partition_manage_id=? and country_code_two=?",$channelcostppr_c->partition_manage_id,$rorder->consignee_country_code)->getAll();
							foreach ($partion_c as $p){
								if(strlen($p->postal_code)>0 && (substr($p->postal_code, 0,strlen($rorder->consignee_postal_code))==$rorder->consignee_postal_code || substr($rorder->consignee_postal_code, 0,strlen($p->postal_code))==$p->postal_code)){
									$partition_code=$p->partition_code;
								}
								if(!$p->postal_code){
									$partition_code2=$p->partition_code;
								}
							}
							if(!$partition_code){
								$partition_code=$partition_code2;
							}
							$price_c=Price::find("price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?",$channelcostppr_c->price_manage_id,$partition_code,$rorder->packing_type,$rorder->weight_cost_out,$rorder->weight_cost_out)->getOne();
							
							if(!$price_c->isNewRecord()){
								$account_sync_c=Accountsync::find("product_code=?",$price_c->account)->getOne();
								//print_r($account_sync_c);exit;
								if(!$account_sync_c->isNewRecord()){
									$account_c=$account_sync_c->account;
								}
								//获取异形包装费
								$special_fee_c=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$rorder->order_id)->getOne();
								$special_count_c=0;
								if(!$special_fee_c->isNewRecord()){
									$special_count_c=$special_fee_c->quantity;
								}
								$network_c=Network::find("network_code=? ",$channel_c->network_code)->getOne();
								$quote= new Helper_Quote();
								// 								if ($rorder->customer->customs_code=='ALCN'){
// 									$cainiaofee = new Helper_CainiaoFee();
// 									$fees_c=$cainiaofee->payment($rorder, $channelcostppr_c,$network_c->network_id,$special_count_c);
// 								}else{
								$fees_c=$quote->returnpayment($rorder, $channelcostppr_c,$network_c->network_id,$special_count_c);
// 								}
								//存在生效费用项无法计算
								if(@$fees_c['success']=='formulaerror'){
									$data['message']=$fees_c['success'];
									echo json_encode($data);
									exit();
								}
// 								echo "<pre>";
// 								print_r($account_c);
// 								print_r($rorder->channel_id);
// 								exit;
								//--
								$view=Helper_Common::getReturnLabel($rorder, @$account_c ,$rorder->channel_id);
								if(!isset($view['errormessage']) || $view['errormessage']!=''){
									//渠道获取面单失败
									$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
								}else{
									//删除原有费用
									Fee::find("order_id=? and fee_type='2' and is_return=1",$rorder->order_id)->getAll()->destroy();
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
													'order_id'=>$rorder->order_id,
													'fee_type'=>'2',
													'fee_item_code'=>$fee_sub_code->sub_code,
													'fee_item_name'=>$fee_sub_code->item_name,
													'quantity'=>$fee_item['quantity'],
													'amount'=>$fee_item['fee'],
													'currency'=>$currency_code,
													'is_return' => 1,
													'rate'=>$rate,
													'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $rorder->channel->supplier_id
												));
												$fee->save();
											}
										}
									}
// 									if(count($limit_ids)>0){
// 										$limit_ids = array_unique($limit_ids);
// 										$limit_lists = ChannelLimitationAmount::find('channel_id = ? and limitation_amount_id in (?)',$channel_c->channel_id,$limit_ids)->getAll();
// 										foreach ($limit_lists as $list){
// 											if($list->type == 0){
// 												$list->used_value = $list->used_value+1;
// 											}elseif($list->type == 1){
// 												$list->used_value = $list->used_value+$total_weight;
// 											}elseif($list->type == 2){
// 												$list->used_value = $list->used_value+$weight_cost_out;
// 											}
// 											$list->save();
// 										}
// 									}
									
									//存入打单账号
									// 									$rorder->account=$view['account_number'];
									// 									$rorder->add_data_status=1;
									// 									$rorder->save();
									//修改状态为已打印
									$rorder->order_status='50';
									$rorder->save();
									$data['account']=$view['account'];
									$data['message']='true';
								}
							}else{
								$data['message']="价格未找到";
							}
						}else{
							$data['message']="无可用渠道";
						}
					}else{
						$data['message']="无可用渠道";
					}
					//非UPS渠道,只计算费用，不生成面单
				}
			}
			$data['hasbattery']=$rorder->has_battery;
			$data['declaration_type']=$rorder->declaration_type;
			$data['channel_name']=ReturnChannel::find('channel_id=?',$rorder->channel_id)->getOne()->channel_name;
			echo json_encode($data);
			exit();
			
			
		}
	}
	/**
	 * @todo   退件重发打印，获取物流单号
	 * @author 吴开龙
	 * @since  2020-11-26 11:03:07
	 * @return json
	 * @link   #83699
	 */
	function actionGettrackingno(){
		//--
		//$order = Order::find('ali_order_no=? or tracking_no=?',request('ali_order_no'),request('ali_order_no'))->getOne();
		$rorder = ReturnOrder::find('ali_order_no=? or tracking_no=?','R'.request('ali_order_no'),request('ali_order_no'))->getOne();
		$channel=ReturnChannel::find('channel_id=?',$rorder->channel_id)->getOne();
		//获取sub_code个数
		$sub_code=ReturnSubcode::find('order_id=?',$rorder->return_order_id)->getAll();
		//是否打印fda发票
		$flag = false;
		$data=array(
			'tracking_no'=>$rorder->new_tracking_no,
			'country'=>$rorder->consignee_country_code,
			'sub_code_count'=>count($sub_code),
			'network_code'=>$channel->network_code,
			'flag'=>$flag,
			'dhl_pdf_type'=>$rorder->dhl_pdf_type,
		);
		echo json_encode($data);
		exit();
	}
	/**
	 * @todo   退件重发扫描出库总单列表
	 * @author 吴开龙
	 * @since  2020-11-26 14:33:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnOutTotal(){
		$total = ReturnOutTotal::find();
		//创建时间
		if(request('start_date')){
			$total->where('create_time >= ?',strtotime(request('start_date').'00:00:00'));
		}
		//结束时间
		if(request('end_date')){
			$total->where('create_time <= ?',strtotime(request('end_date').'23:59:59'));
		}
		//总单单号
		if(request('return_total_no')){
			$total_list_no=explode("\r\n", request('return_total_no'));
			//去空
			$total_list_no=array_filter($total_list_no);
			//去重
			$total_list_no=array_unique($total_list_no);
			$total->where('return_total_no in (?)',$total_list_no);
		}
		
		//分页
		$pagination = null;
		$total=$total->limitPage ( request ( "page", 1 ), request ( 'page_size', 30 ) )
		->fetchPagination ( $pagination )->order('return_out_total_id desc')->getAll();
		$this->_view['total']=$total;
		$this->_view['pagination']=$pagination;
	}
	/**
	 * @todo   退件重发扫描出库明细页面
	 * @author 吴开龙
	 * @since  2020-11-26 15:33:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnOut(){
		$rorder = ReturnOrder::find('return_out_total_id=?',request('return_out_total_id'))->asArray()->getAll();
		$return_order_id = Helper_Array::toHashmap ( $rorder, 'return_order_id', 'return_order_id' );
// 		echo "<pre>";
// 		print_r($return_order_id);
// 		exit;
		//返回参数
		$subcode = array();
		if(count($return_order_id)){
			//区分货物流箱
			if(request('type') == 1){
				$subcode = ReturnSubcode::find('order_id in (?)',$return_order_id)->getAll();
			}else{
				$subcode = ReturnPackage::find('return_order_id in (?)',$return_order_id)->getAll();
			}
			$this->_view['subcode']=$subcode;
		}else{
			$this->_view['subcode']=$subcode;
		}
	}
	/**
	 * @todo   退件重发扫描出库ajax返回
	 * @author 吴开龙
	 * @since  2020-11-26 16:33:07
	 * @return json
	 * @link   #83699
	 */
	function actionScanOutAjax(){
		$order_nos = explode ( "\n", request ( 'order_no' ) );
		//tracking_no4是当前扫描的单号，用于判断使用
		$data = array(
			'tracking_no1' => array(),
			'tracking_no2' => array(),
			'tracking_no3' => array(),
			'tracking_no4' => end($order_nos)
		);
		foreach ($order_nos as $s){
			//去空格 为空 跳过
			$s = trim($s);
			if(!$s){
				continue;
			}
			//重复扫描
			if(in_array($s, $data['tracking_no1'])){
				continue;
			}
			//重发，扫描新单号
			if(request('type') == '1'){
				$tb_sub_code = ReturnSubcode::find('sub_code = ?',$s)->getOne();
				//判断是否扫描的是有单无货
				if(in_array($s, $data['tracking_no3'])){
					unset($data['tracking_no3']);
					$data['tracking_no1'][] = $s;
					continue;
				}
				$order = ReturnOrder::find('return_order_id = ? and order_status="50"',$tb_sub_code->order_id)->getOne();
				if($tb_sub_code->isNewRecord() || $order->isNewRecord()){
					$data['tracking_no2'][] = $s;
					continue;
				}
				$sub_code_count = ReturnSubcode::find('order_id = ?', $tb_sub_code->order_id)->getAll();
				//成功
				if(count($sub_code_count) == 1){
					$data['tracking_no1'][] = $s;
					continue;
				}
				//判断一票多件
				if(count($sub_code_count) > 1){
					foreach ($sub_code_count as $code){
						if(in_array($code->sub_code, $order_nos)){
							$data['tracking_no1'][] = $code->sub_code;
						}else{
							$data['tracking_no3'][] = $code->sub_code;
						}
					}
				}
			}else{
				//销毁和退货，扫描对应的原单号
				$package = ReturnPackage::find('sub_code = ?',$s)->getOne();
				//判断是否扫描的是有单无货
				if(in_array($s, $data['tracking_no3'])){
					unset($data['tracking_no3']);
					$data['tracking_no1'][] = $s;
					continue;
				}
				$order = ReturnOrder::find('return_order_id = ?',$package->return_order_id);
				if (request('type') == '2'){
					//销毁
					$order->where('order_status="30"');
				}else{
					//退货
					$order->where('order_status="40"');
				}
				$order = $order->getOne();
// 				echo "$s<pre>";
// 				print_r($package);
// 				print_r($order);
//				exit;
				//判断是否存在
				if($package->isNewRecord() || $order->isNewRecord()){
					$data['tracking_no2'][] = $s;
					continue;
				}
				$package_count = ReturnPackage::find('return_order_id = ?', $package->return_order_id)->getAll();
				//成功
				if(count($package_count) == 1){
					$data['tracking_no1'][] = $s;
					continue;
				}
				//判断一票多件
				if(count($package_count) > 1){
					foreach ($package_count as $code){
						if(in_array($code->sub_code, $order_nos)){
							$data['tracking_no1'][] = $code->sub_code;
						}else{
							$data['tracking_no3'][] = $code->sub_code;
						}
					}
				}
			}
		}
		return json_encode($data);
	}
	/**
	 * @todo   退件重发扫描出库确认提交
	 * @author 吴开龙
	 * @since  2020-11-27 13:33:07
	 * @return json
	 * @link   #83699
	 */
	function actionScanOutSubmit(){
		$order_nos = explode ( "\n", request ( 'order_no' ) );
		$total = ReturnOutTotal::find('return_out_total_id=?',request('return_out_total_id'))->getOne();
		if($total->isNewRecord()){
			$total->type = request('type');
			$total->return_total_no = request('return_total_no');
			$total->operate_name = MyApp::currentUser('staff_name');
			$total->operate_id = MyApp::currentUser('staff_id');
			$total->save();
		}
		if(request('type') == 1){
			//重发
			$sub_code_count = ReturnSubcode::find('sub_code in (?)', $order_nos)->group('order_id')->getAll();
			foreach ($sub_code_count as $subcode){
				$rorder = ReturnOrder::find('return_order_id=? and order_status="50"',$subcode->order_id)->getOne();
				//订单不存在跳过
				if($rorder->isNewRecord()){
					continue;
				}
				//计算应付
				$this->returnFee($rorder);
				
				$rorder->return_out_total_id = $total->return_out_total_id;
				$rorder->again_time = time();
				$rorder->order_status = '60';
				$rorder->again_id = MyApp::currentUser('staff_id');
				$rorder->again_name = MyApp::currentUser('staff_name');
				$rorder->save();
			}
		}else{
			//销毁、退货
			$package_count = ReturnPackage::find('sub_code in (?)', $order_nos)->group('return_order_id')->getAll();
			foreach ($package_count as $subcode){
				$rorder = ReturnOrder::find('return_order_id=? and (order_status="30" or order_status="40")',$subcode->return_order_id)->getOne();
				//订单不存在跳过
				if($rorder->isNewRecord()){
					continue;
				}
				//计算应付
				$this->returnFee($rorder);
				//保存出库信息
				if(request('type') == 2){
					$rorder->return_out_total_id = $total->return_out_total_id;
					$rorder->destroy_time = time();
					$rorder->order_status = '70';
				}else{
					$rorder->return_out_total_id = $total->return_out_total_id;
					$rorder->send_back_time = time();
					$rorder->order_status = '80';
				}
				$rorder->again_id = MyApp::currentUser('staff_id');
				$rorder->again_name = MyApp::currentUser('staff_name');
				$rorder->save();
			}
		}
		echo 1;
		exit;
	}
	/**
	 * @todo   退件出库计算应付费用
	 * @author 吴开龙
	 * @since  2020-11-27 17:13:07
	 * @return json
	 * @link   #83699
	 */
	function returnFee($rorder){
		//$order = Order::find('order_id=?',$order_id)->getOne();
		$product=Product::find('product_name=?',$rorder->service_code)->getOne();
		
		//获取异形包装费
		$special_fee_c_t=Fee::find("order_id=? and fee_type='2' and fee_item_code='logisticsExpressASP_EX0034'",$rorder->order_id)->getOne();
		$special_count_c_t=0;
		if(!$special_fee_c_t->isNewRecord()){
			$special_count_c_t=$special_fee_c_t->quantity;
		}
		//查找渠道成本
		$channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$rorder->channel_id)->getOne();
		if(!$channelcost_c_t->isNewRecord()){
			$channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,$rorder->record_order_date,$rorder->record_order_date)->getOne();
			if(!$channelcostppr_c_t->isNewRecord()){
				//取出
				$network_c_t=Network::find("network_code=? ",$rorder->channel->network_code)->getOne();
				$quote= new Helper_Quote();
				//调用计算函数
				$price_c_t=$quote->returnpayment($rorder, $channelcostppr_c_t,$network_c_t->network_id,$special_count_c_t,$rorder->record_order_date);
				if(count($price_c_t) && count($price_c_t['price_info'])){
					//删除原“退件”费用 
					Fee::find('order_id=? and fee_type=2 and is_return=1',$rorder->order_id)->getAll()->destroy();
					//循环添加
					foreach ($price_c_t['price_info']['fee_item'] as $key=>$fee_item){
						if($fee_item['fee']!='0'){
							//币种 
							if(@$fee_item['currency_code']){
								$currency_code = $fee_item['currency_code'];
								$rate = $fee_item['rate'];
							}else{
								$currency_code = 'CNY';
								$rate = 1;
							}
							//获取
							$fee_sub_code=FeeItem::find('sub_code=?',$key)->getOne();
							//添加费用
							$fee= new Fee();
							$fee->changeProps(array(
								'order_id'=>$rorder->order_id,
								'fee_type'=>'2',
								'fee_item_code'=>$fee_sub_code->sub_code,
								'fee_item_name'=>$fee_sub_code->item_name,
								'quantity'=>$fee_item['quantity'],
								'amount'=>$fee_item['fee'],
								'currency'=>$currency_code,
								'rate'=>$rate,
								'is_return'=>1,
								'account_date'=>$rorder->warehouse_out_time,
								'btype_id'=>(isset($fee_item['btype_id'])  && !empty($fee_item['btype_id']))? $fee_item['btype_id'] : $rorder->channel->supplier_id
							));
							$fee->save ();
						}
					}
				}
			}
		}
	}
	/**
	 * @todo   退件出库扫描完成
	 * @author 吴开龙
	 * @since  2020-11-27 15:13:07
	 * @return json
	 * @link   #83699
	 */
	function actionReturnOutStatus(){
		$outtotal = ReturnOutTotal::find('return_out_total_id=?',request('return_out_total_id'))->getOne();
		$outtotal->status = 1;
		$outtotal->save();
		return $this->_redirect(url('/returnouttotal'));
	}
}
