<?php

 class Controller_Test extends Controller_Abstract {
 	/**
   * @todo   补充历史数据出库体积重
   * @author 吴开龙
   * @since  2020-9-16 16:03:28
   * @param 
   * @return string
   * @link   #82635
 	*/
 	function actionIndex() {
 		phpinfo();exit;
 		set_time_limit(0);//不限制超时时间
 		ini_set('memory_limit', '-1');//不限制内存
 		$orders = Order::find('total_volumn_weight = 0 or total_out_volumn_weight = 0')->getAll();
 		foreach ($orders as $order){
 			if($order->total_volumn_weight == 0){
 				$inweight = Helper_Quote::getweightarr($order, 1);
 				$order->total_volumn_weight = $inweight['total_volumn_weight'];
 			}
 			if($order->total_out_volumn_weight == 0){
 				$outweight = Helper_Quote::getweightarr($order, 2);
 				$order->total_out_volumn_weight = $outweight['total_volumn_weight'];
 			}
 			$order->save();
 		}
 		echo "完成";
 		exit;
 	}
 	
 	/**
 	 * @todo   补充EMS承运商取件
 	 * @author 石亭亭
 	 * @since  2020-9-27
 	 * @param
 	 * @return string
 	 * @link   #82936
 	 */
 	function actionemscarrierpickup() {
 		set_time_limit(0);//不限制超时时间
 		ini_set('memory_limit', '-1');//不限制内存
 		$routes = Route::find('network_code=? and create_time>=? and description like (?)' ,'EMS' ,'1600185600' ,'%已收寄%')->getAll();
 		foreach ($routes as $route){
 			
 			$event_time = $route->time;
 			$order = Order::find('tracking_no=?',$route->tracking_no)->getOne();
 			$quantity = Farpackage::find ( 'order_id=?', $order->order_id )->sum ( 'quantity', 'sum_quantity' )
 			->getAll ();
	 		//承运商取件事件
	 		$carrier_pickup = Event::find ( "event_code='CARRIER_PICKUP' and order_id= ?", $order->order_id )->getOne ();
	 		//转运轨迹：F_CARRIER_PICKUP_RT_5035
	 		$pickup_track = Tracking::find ( "tracking_code='F_CARRIER_PICKUP_RT_5035' and trace_desc_cn like ? and order_id= ?", '%' . $route->tracking_no . '%', $order->order_id )->getOne ();
	 		//承运商取件事件
	 		if ($carrier_pickup->isNewRecord () ){
	 			$event = new Event ();
	 			$event->changeProps ( array (
	 				'order_id' => $order->order_id,
	 				'customer_id'=>$order->customer_id,
	 				'event_code' => 'CARRIER_PICKUP',
	 				'event_time' => $event_time,
	 				'event_location' => '南京',
	 				'location' => '南京',
	 				'confirm_flag' => '1',
	 				'timezone' => '8'
	 			) );
	 			$event->save ();
	 			$order->carrier_pick_time = $event_time;
	 			$order->save ();
	 		}
	 		//转运轨迹：F_CARRIER_PICKUP_RT_5035
	 		if ($pickup_track->isNewRecord () ) {
	 			$trace = new Tracking ();
	 			$trace->changeProps ( array (
	 				'order_id' => $order->order_id,
	 				'customer_id'=>$order->customer_id,
	 				'far_no' => $order->far_no,
	 				'tracking_code' => 'F_CARRIER_PICKUP_RT_5035',
	 				'location' => '南京',
	 				'trace_desc_cn' => '包裹重新安排转运,转【' . $route->tracking_no . '】',
	 				'trace_desc_en' => 'Reschedule transshipment to EMS[' . $route->tracking_no . '].Track in:http://www.ems.com.cn/english.html or https://www.17track.net/en',
	 				'timezone' => '8',
	 				'confirm_flag' => '1',
	 				'route_id' => $route->id,
	 				'quantity' => $quantity ['sum_quantity'],
	 				'trace_time' => $event_time + rand ( 0, 10 ) * 60 + rand ( 0, 10 )
	 			) );
	 			$trace->save ();
	 		}
 		}
 		echo "完成";
 		exit;
 	}
 	/**
 	 * @todo   用于迁移阿里系统的90天前面单文件到挂载盘里面
 	 * @author stt
 	 * @since  2020-10-19
 	 * @link   #83177
 	 */
 	function actionzhuanyi(){
 		set_time_limit(0);
 		ini_set('memory_limit', '-1');
 		$dir=Q::ini('upload_tmp_dir');
 		self::readAll ($dir);
 		echo 'success';
 		exit;
 	}
 	/**
 	 * @todo   读取该目录下所有内容
 	 * @author stt
 	 * @since  2020-10-19
 	 * @link   #83177
 	 */
 	static function readAll ($dir){
 		
 		if(!is_dir($dir)) return false;
 		$handle = opendir($dir);
 		$time=time();//当前时间
 		if($handle){
 			while(($fl = readdir($handle)) !== false ){
 				$temp = $dir.DS.$fl;
 				//如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
 				if(is_dir($temp) && $fl!='.' && $fl != '..'){
 					//     				echo '目录：'.$temp.'<br>';
 					//     				self::read_all($temp);
 				}else{
 					if($fl!='.' && $fl != '..'&&$fl!='logo.png'&&$fl!='logo.jpg'&&$fl!='1Z4F1R246707811679.pdf'&&$fl!='.xlsx'){
 						$mtime = filemtime($temp);
 						if ($mtime<($time-2592000)){ 
 							//上传到oss
 							$uploadoss = new Helper_AlipicsOss();
 							$invoice_data = $uploadoss->uploadAlifiles($fl);
 							echo '文件：'.$temp.'<br>';
 							if($uploadoss->doesExist($fl)){
 								//上传成功，删除
 								echo '上传成功：'.$temp.'<br>';
 								unlink($dir.DS.$fl);
 							}
 						}
 					}
 				}
 			}
 		}
 	}
 	/**
 	 * @todo   退回已支付阿里单号与物流单号关系表用order_id关联
 	 * @author stt
 	 * @since  2020-11-02
 	 * @link   #83177
 	 */
 	function actionreturnpaidtrackingno(){
 		set_time_limit(0);
 		ini_set('memory_limit', '-1');
 		//全部没有order_id的退回已支付数据
 		$return_paids=ReturnPaidTrackingno::find('order_id is null')->getAll();
 		foreach ($return_paids as $return_paid){
 			$order=Order::find('ali_order_no=?',$return_paid->ali_order_no)->getOne();
 			//order_id
 			$return_paid->order_id = $order->order_id;
 			$return_paid->save();
 		}
 		//成功
 		echo 'success';
 		exit;
 	}
 	/**
 	 * @todo   快手入库图片保存到File表
 	 * @author stt
 	 * @since  2020-11-18
 	 * @param
 	 * @return
 	 * @link   #83883
 	 */
 	function actionaddfile(){
 		set_time_limit(0);
 		ini_set('memory_limit', '-1');
 		// file无记录的订单
 		$orders = Order::find('((far_warehouse_in_time > 0) OR (warehouse_in_time > 0))')
 		->where('is_picture = 0')
 		->setColumns ( 'order_id,order_no' )
 		->order('create_time desc')
 		->getAll();
 		echo 'begin<br>';
 		foreach ($orders as $order){
 			$uploadoss = new Helper_AlipicsOss();
 			//kuaishou文件夹是否存在图片
 			//echo $order->order_no.'<br>';
 			if ($uploadoss->doesExistkuaishou($order->order_no.'.jpg')){
 				//保存一条数据
 				$file = new File ();
 				//订单ID
 				$file->order_id = $order->order_id;
 				//文件名
 				$file->file_name = $order->order_no.'.jpg';
 				//文件路径
 				$file->file_path = 'http://ia1.oss-cn-hangzhou.aliyuncs.com/kuaishou/'.$order->order_no.'.jpg';
 				$file->operator = '系统';
 				$file->save ();
 				// 照片已上传
 				$order->is_picture = '2';
 				$order->save();
 				echo  $order->order_no.'.jpg<br>';
 			}
 		}
 		echo 'end';
 		exit;
 	}
 	function actionceshimail(){
 		//测试
 		//212 x
 		$email_response=Helper_Mailer::send('xujy@far800.com', '测试！', '阿里单号失败原因');
 		echo $email_response;exit;
 	}
 	//测试
 	function actionceshiphoto(){
 		$order = Order::find('order_id=?','10717')->getOne();
 		//发送照片
 		Helper_Common::sendcheckweightphoto($order);
 		exit;
 	}
 	/* function actiongettrack(){
 		$tracking_nos=array('9200190237757357870794','9200190237757357821628');
 		$url = 'http://production.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=';
 		$body = '<?xml version="1.0" encoding="UTF-8" ?><TrackFieldRequest USERID="524FARLO6825"><Revision>1</Revision><ClientIp>127.0.0.1</ClientIp><SourceId>USPSTOOLS</SourceId>';
 		foreach ($tracking_nos as $tracking ){
 			$body .= '<TrackID ID="' . $tracking . '"/>';
 		}
 		$body .= '</TrackFieldRequest>';
 		
 		//QLog::log ( 'USPS - getTrackingInfoMultiple - url - ' . $url . $body );
 		$usps_xml = Helper_Curl::get ( $url . urlencode ( $body ) );
 		
 		//QLog::log ( 'USPS - getTrackingInfoMultiple - response - ' . $usps_xml );
 		$usps_arr = json_decode ( json_encode ( simplexml_load_string ( $usps_xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
 		dump($usps_arr,1,100);
 		exit;
 	} */
 	
 }
 	
