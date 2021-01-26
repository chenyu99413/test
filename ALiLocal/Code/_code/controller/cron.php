<?php
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/fpdf.php';
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/chinese.php';
/**
 * 后台服务控制器
 *
 */
class Controller_Cron extends Controller_Abstract{
	/**
	 * 自动发送阿里事件
	 */
	function actionnotifyBizEvent(){
		$url_sign='https://gw.open.1688.com/openapi/param2/1/ali.intl.onetouch/logistics.order.notifyBizEvent/563333';
		$events=Event::find("send_flag='0' and send_times< 2 and confirm_flag = 1 and event_time<=? and customer_id = 1",time())->order('order_id,event_time')->getAll();
		foreach ($events as $event){
			$ali_order_no=Order::find('order_id=?',$event->order_id)->getOne();
			if(strtoupper(substr($ali_order_no->ali_order_no,0,3))!='ALS'){
				$event->send_flag=2;
				$event->return_reason='非ALS单号';
				$event->save();
				continue;
			}
			//$ali_order_no->order_status !=7
			if( (!$ali_order_no->warehouse_out_time || $ali_order_no->service_code !="OCEAN-FY") && in_array($event->event_code,array('LOAD','SET_SAIL','ARRIVAL_PORT'))){
				continue;
			}
			//已成功推送派送失败事件，不再推送签收事件
			if ($ali_order_no->is_signunusual=='3' && $event->event_code=='DELIVERY' && !$event->reason){
				//不再推送
				$event->confirm_flag='3';
				$event->save();
				continue;
			}
			$ali=new Helper_ALI();
			$event_request_data=$ali->notifyBizEvent($event);
			$sign=$ali->sign($url_sign, json_encode($event_request_data),'notifyBizEvent');
			//组合完整url
			QLog::log($url_sign.'?notifyBizEventDTO='.json_encode($event_request_data).'&_aop_signature='.$sign);
			//             $url=$url_sign.'?notifyBizEventDTO='.urlencode(json_encode($event_request_data)).'&_aop_signature='.$sign;
			//             QLog::log($url);
			//通过curl get 方式发送至阿里
			$response=Helper_Curl::post($url_sign, 'notifyBizEventDTO='.urlencode(json_encode($event_request_data)).'&_aop_signature='.$sign);
			//             $response=Helper_Curl::get1($url);
			QLog::log('event_response'.$response);
			//备注
			$response=json_decode($response,true);
			if(isset($response['success']) && $response['success']==true){
				$event->success_time = time();
				$event->send_flag='1';
				$event->save();
				if($event->event_code=='DELIVERY'){
					//签收失败不修改订单状态  
					$order=Order::find('order_id=?',$event->order_id)->getOne();
					if(!$event->reason){
						//备注
						$order->delivery_time=$event->event_time;
						$order->order_status='9';
						$order->save();
					}else{
						//已成功推送派送失败事件
						$order->is_signunusual = 3;
						$order->save();
					}
				}
			}else{
				$event->send_times=$event->send_times+1;
				$event->return_reason=json_encode($response);
				$event->save();
				$order=Order::find('order_id=?',$event->order_id)->getOne();
				//$email_response=Helper_Mailer::send('xujy@far800.com', '发送事件失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
				$email_response=Helper_Mailer::send('bbop@far800.com', '紧急！阿里事件发送失败！', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
				$email_response=Helper_Mailer::send('liujian@far800.com', '紧急！阿里事件发送失败！', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
			}
			//间隔
			sleep(1);
		}
		exit();
	}
	/**
	 * 自动发送阿里轨迹
	 */
	function actionnotifyTrace(){
		$url_sign='https://gw.open.1688.com/openapi/param2/1/ali.intl.onetouch/logistics.order.notifyTrace/563333';
		$tracking=Tracking::find("send_flag = '0' and send_times< 2  and confirm_flag = '1' and trace_time<=? and customer_id = 1",time())->getAll();
		foreach ($tracking as $temp){
			$order=Order::find('order_id=?',$temp->order_id)->getOne();
			if(strtoupper(substr($order->ali_order_no,0,3))!='ALS'){
				$temp->send_flag=2;
				$temp->save();
				continue;
			}
			//已成功推送派送失败事件
			if ($order->is_signunusual=='3'){
				// 不再推送
				$temp->confirm_flag='3';
				$temp->save();
				continue;
			}
			$ali=new Helper_ALI();
			$trace_request_data=$ali->notifyTrace($temp);
			$sign=$ali->sign($url_sign, json_encode($trace_request_data),'notifyTrace');
			//组合完整url
			$url=$url_sign.'?notifyTraceDTO='.urlencode(json_encode($trace_request_data)).'&_aop_signature='.$sign;
			//通过curl get 方式发送至阿里
			$response=Helper_Curl::get1($url);
			QLog::log('trace_request'.json_encode($trace_request_data));
			QLog::log('trace_response'.$response);
			$response=json_decode($response,true);
			if(isset($response['success']) && $response['success']==true){
				$temp->send_flag='1';
				$temp->save();
				//如果轨迹是 F_DELIVERY_5048，将订单状改为已结束
				if($temp->tracking_code=='F_DELIVERY_5048'){
					$order->order_status='13';
					$order->save();
				}
				$rule_choose = AutomaticEmailRule::find('product_id = ? and tracking_code = ?',$order->service_product->product_id,$temp->tracking_code)->getOne();
				if(!$rule_choose->isNewRecord()){
					$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
					if(!$email_template->isNewRecord()){
						$title = $email_template->template_title;
						$email_info = $email_template->template_text;
						
						$postalbook = postalbook::find('code_word_two = ? and channel_id = ?',$order->consignee_country_code,$order->channel_id)->getOne();
						$track = Controller_Product::getTracking($order);
						//标题
						$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
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
						$template_title = preg_replace('/track1/',@$track[0], $template_title);
						$template_title = preg_replace('/track2/',@$track[1], $template_title);
						$template_title = preg_replace('/track3/',@$track[2], $template_title);
						$deprtment_name = $order->department_id?$order->department->department_name:'';
						$template_title = preg_replace('/warehouse/',$deprtment_name,$template_title);
						//内容
						$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
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
						QLog::log($template_title);
						QLog::log($template_info);
						$title = nl2br($template_title);
						$msg = nl2br($template_info);
						$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
						QLog::log($email_response);
						if ($email_response == 'email_success') {
							$order_log = new OrderLog ();
							$order_log->order_id = $order->order_id;
							$order_log->staff_name = '系统';
							$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
							$order_log->save ();
						}
					}
				}
			}else{
				$temp->send_times=$temp->send_times+1;
				$temp->save();
				$email_response=Helper_Mailer::send('xujy@far800.com', '发送轨迹失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
				$email_response=Helper_Mailer::send('bbop@far800.com', '发送轨迹失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
				//$email_response=Helper_Mailer::send('bbop@far800.com', '紧急！发送轨迹失败！', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
				continue;
			}
			//自动确认签收事件
			if ($temp->tracking_code =='S_DELIVERY_SIGNED'){
				$ev=Event::find('order_id =? and confirm_flag =0 and event_code="DELIVERY"',$temp->order_id)->getOne();
				if (!$ev->isNewRecord()){
					$ev->confirm_flag=1;
					$ev->save();
				}
			}
		}
		exit();
	}
	/**
     * 自动发送4pl事件
     */
    function actionfourplnotifyBizEvent(){
       	$events=Event::find("send_flag='0' and send_times< 2  and confirm_flag = '1' and event_time<=?",time())->order('order_id,event_time')->getAll();
        foreach ($events as $event){
            $ali_order_no=Order::find('order_id=?',$event->order_id)->getOne();
            if ($event->customer->customs_code=='ALPL'){
            	$fourpl = new Helper_Notify4PL();
            	$response = $fourpl->notifyPost($event);
            	QLog::log('4PL_event_response:'.$response);
            
	            $response=json_decode($response,true);
	            if(isset($response['success']) && $response['success']==true){
	            	$event->success_time = time();
	                $event->send_flag='1';
	                $event->save();
	                if($event->event_code=='LAST_MILE_GTMS_SIGNED_CALLBACK'){
	                	//签收失败不修改订单状态
	                	if(!$event->reason){
	                		//签收失败不修改订单状态
		                	$order=Order::find('order_id=?',$event->order_id)->getOne();
		                	$order->delivery_time=$event->event_time;
		                	$order->order_status='9';
		                	$order->save();
	                	}
	                }
	            }else{
	            	$event->send_times=$event->send_times+1;
	            	$event->return_reason=json_encode($response);
	            	$event->save();
// 	            	$order=Order::find('order_id=?',$event->order_id)->getOne();
// 	                $email_response=Helper_Mailer::send('xujy@far800.com', '发送事件失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
// 	                $email_response=Helper_Mailer::send('bbop@far800.com', '紧急！阿里事件发送失败！', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
// 	                $email_response=Helper_Mailer::send('liujian@far800.com', '紧急！阿里事件发送失败！', '阿里单号 '.$order->ali_order_no.',失败原因：'.json_encode($response));
	            }
            }
        }
        exit();
    }
    /**
	 * @todo   菜鸟节点发送
	 * @author 许杰晔
	 * @since  2020-08-12
	 * @param 
	 * @return json
	 * @link   #81740
	 */
    function actionCaiNiao(){  	
    	$cainiaos=CaiNiao::find("send_flag != 1 and send_times< 2  and confirm_flag = 1 and cainiao_time<=?",time())->order('order_id,cainiao_time')->getAll();
    	foreach ($cainiaos as $cai){
    		$ali_order_no=Order::find('order_id=?',$cai->order_id)->getOne();
    		/* if(strtoupper(substr($ali_order_no->ali_order_no,0,3))!='ALS'){
    			continue;
    		} */
    		
    		$cainiao=new Helper_CaiNiao();
    		$cainiao_request_url=$cainiao->NotifyCaiNiao($cai);
    		//组合完整url
    		QLog::log('cainiaourl:'.$cainiao_request_url);
    		$response=Helper_Curl::get($cainiao_request_url);
    		//print_r($response);
    		QLog::log('cainiao_response'.$response);
    		$response = json_decode(json_encode(helper_xml::xmlparse($response)),true);
    		$head=$response['head'];
    		$data=$response['body'];
    		if(isset($data['success']) && $data['success']=='100000200'){
    			$cai->return_reason=$cainiao_request_url;
    			$cai->send_flag='1';
    			$cai->save();
    		}else{
    			$cai->send_times=$cai->send_times+1;
    			$cai->return_reason=json_encode($data);
    			$cai->save();
    			$order=Order::find('order_id=?',$cai->order_id)->getOne();
    			//$email_response=Helper_Mailer::send('xujy@far800.com', '发送菜鸟节点失败', '单号 '.$order->ali_order_no.',失败原因：'.json_encode($data));
    			///$email_response=Helper_Mailer::send('bbop@far800.com', '紧急！发送菜鸟节点失败！', '单号 '.$order->ali_order_no.',失败原因：'.json_encode($data));
    			//$email_response=Helper_Mailer::send('liujian@far800.com', '紧急！发送菜鸟节点失败！', '单号 '.$order->ali_order_no.',失败原因：'.json_encode($data));
    		}
    	}
    	exit();
    }
    /**
     * 跟踪渠道末端轨迹
     */
    function actionRoute(){
    		$args=func_get_args();
    		$sleep=Q::cache('RouteSleep',array('life_time'=>3600));
    		if ($sleep && empty($args[3])){
    			self::log('sleep');
    			exit;
    		}
    		//ups
    		self::log('begin');
    		$select=Order::find('service_code in (?) and order_status in (?)',array('Express_Standard_Global','US-FY','CNUS-FY','CNUSBJ-FY','EUUS-FY','OCEAN-FY','ProtectiveEquipment-FY'), array(Order::STATUS_OUT,Order::STATUS_EXTRACTED,Order::STATUS_LOCK));
    		if (!empty($args[3]) && $args[3]!='force'){
    			$select->where('tracking_no =?',$args[3]);
    		}
    		$select= $select->order('update_time')
    			->setColumns('order_id,tracking_no,channel_id')
    			->all()
    			->getQueryHandle();
    			while (($row=$select->fetchRow())!= false){
    			self::log($row['order_id']);
    			$order=Order::find('order_id =?',$row['order_id'])->getOne();
    			
    			if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    				self::log($row['tracking_no']);
    				$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    				$network_code=$channel->network_code;
    				$tnetwork_code=$channel->trace_network_code;
    				$trackings=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1"',$row['order_id'])->getAll();
    				// 求最晚时间并转换为utc+8
    				if($network_code=='UPS'){
        				$evt=Event::find('order_id =? ',$row['order_id'])->order('event_time desc') ->getOne();
        				$lastTime=strtotime(date("Y-m-d 2:25:00",$evt->event_time))+86400*2;	//事件最后的时间+2天
    				}else {
    				    $lastTime=0;
    				}
    				if (count($trackings)) foreach ($trackings as $tr){
    					if ($tr->timezone==-19){
    						continue;
    					}
    					if($network_code=='UPS'){
        					if ($tr->trace_time + (8-$tr->timezone )*3600 > $lastTime ){
        						$lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
        					}
    					}else {
    					    if ($tr->trace_time){
    					        $lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
    					    }
    					}
    				}
    				
    				if ($network_code =='UPS' || $tnetwork_code=='UPS'){
    					$json=Helper_Curl::get('http://m.far800.com/?action=tracking&num='.$row['tracking_no'].'&lang=en');
						$routes=json_decode($json,TRUE);
    					if (!empty($routes['data']) && count($routes['data'])){
    						// 将轨迹按照时间升序排序
    						$routes['data']=array_reverse($routes['data']);
    						$prevLocation='';
    						foreach ($routes['data'] as $d){
    						    // 保存
    						    if(!isset($d['location']) || !isset($d['time']) || !isset($d['context']) || !$d['time'] || !$d['context']){
    						        continue;
    						    }
    						    if(!empty($order->warehouse_out_time) && strtotime($d['time'])<($order->warehouse_out_time-86400)){
    						    	continue;
    						    }
    						    if($d['context']<>'Order Processed: Ready for UPS' && $d['context']<>'Order Processed: In Transit to UPS'){
    						        if($order->order_status=='7'){
    						            $order->order_status='8';
    						            $order->pick_up_time=time();
    						            $order->save();
    						            self::sendmail($order);
//     						            self::saveoutevents($order, strtotime($d['time']));
    						        }
    						    }
    						   
    							$r=Route::checkAndSave(array(
    								'network_code'=>'UPS',
    								'tracking_no'=>$row['tracking_no'],
    								'time'=>strtotime($d['time']),
    								'location'=>$d['location'],
    								'description'=>$d['context'],
    							    'code'=>$d['code']
    							));
    							//已成功推送派送失败事件
    							if ($order->is_signunusual=='3'){
    								continue;
    							}
    							//UPS记录订单交给末端扫描的第一枪时间
    							if(!$order->tracking_one_time && $d['context'] == 'Origin Scan'){
    								$order->tracking_one_time = strtotime($d['time']);
    								$order->save();
    							}
    							if (!is_null($r)){
    								self::log('save');
    								//判断是否更改地址
    								if(!$order->address_change){
    									$tracking=Tracking::find('order_id=? and tracking_code="F_DELIVERY_5043"',$order->order_id)->getOne();
    									if (!$tracking->isNewRecord()){
    										$order->address_change='1';
    										$order->address_change_info='F_DELIVERY_5043:Delivery information needed,attempting to update it';
    										$order->save();
    									}else {
    										$change_array=array('corrected the street number','corrected the apartment number','corrected the postal code','delivery change','a request to modify the delivery address','delivery address has been updated','requested an alternate delivery address','updated the delivery information','request to modify the delivery address','updated the address','change of delivery');
    										foreach ($change_array as $value){
    											if(strpos(strtolower($d['context']), $value)!==false){
    												$order->address_change='1';
    												$order->address_change_info=$d['context'];
    												$order->save();
    												break;
    											}
    										}
    									}
    								}
    								if($r->description != 'Order Processed: Ready for UPS' && $r->description != 'Order Processed: In Transit to UPS'){
    								    $r->generateTrace($lastTime,$order,$prevLocation);
    								}
    							}
    							
    							if (strlen($d['location'])){
    								$prevLocation=$d['location'];
    							}
    						}
    						if(isset($routes['SDD']) && !empty($routes['SDD'])){
    						    $track=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and trace_desc_cn like ?',$row['order_id'],'%'.date('Y-m-d',$routes['SDD']).'%')->getOne();
    						    $track2=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and yflag="1"',$row['order_id'])->getOne();
    						    $track3=Tracking::find('order_id =? and tracking_code="S_DELIVERY_SIGNED"',$row['order_id'])->getOne();
    						    $tra=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1" and confirm_flag<>"2"',$order->order_id)->order('tracking_id desc')->getOne();
    						    if($track->isNewRecord() && $track3->isNewRecord()){
    						        $order->present_time=$routes['SDD'];
    						        $order->save();
    						        if($tra->location){
        						        $track->changeProps(array(
        						            'order_id'=>$order->order_id,
        						        	'customer_id'=>$order->customer_id,
        						            'far_no'=>$order->far_no,
        						            'tracking_code'=>'F_DELIVERY_5044',
        						            'timezone'=>'8',
        						            'confirm_flag'=>'1',
        						            'trace_time'=>time(),
        						            'location'=>$tra->location,
        						            'quantity'=>$tra->quantity,
        						            'trace_desc_cn'=>'预计派送时间（当地）:'.date('Y-m-d',$routes['SDD']),
        						        	'trace_desc_en'=>'Scheduled Delivery Time (Local):'.date('Y-m-d',$routes['SDD']),
        						            'yflag'=>'1'
        						        ));
        						        $track->save();
        						        if(!$track2->isNewRecord()){
        						            $track->trace_desc_cn='预计派送时间（当地） 更新:'.date('Y-m-d',$routes['SDD']);
        						            $track->trace_desc_en='Scheduled Delivery Time Updated (Local):'.date('Y-m-d',$routes['SDD']);
        						            $track->save();
        						        }
    						        }
    						    }
    						}
    					}
    				}
    			}
    		}
    		Q::writeCache('RouteSleep',true,array('life_time'=>3600));
    		exit;
    }
     /**
	 * @todo   usps抓取轨迹
	 * @author 许杰晔
	 * @since  2020-08-12
	 * @param 
	 * @return json
	 * @link   #81740
	 */
    function actionIbRoute() {
    	
    	$args=func_get_args();
    	//运行时间 
    	$sleep=Q::cache('IBCRouteSleep',array('life_time'=>3600));
    	if ($sleep && empty($args[3])){
    		self::log('sleep');
    		exit;
    	}
    	self::log('begin');
    	$select=Order::find('account in (?) and order_status in (?)',array('IB'), array(Order::STATUS_OUT,Order::STATUS_EXTRACTED,Order::STATUS_LOCK));
    	if (!empty($args[3]) && $args[3]!='force'){
    		$select->where('tracking_no =?',$args[3]);
    	}
    	$tracking_nos= $select->order('update_time')
    	->setColumns('tracking_no')
    	->all()
    	->getQueryHandle ();
//     	dump(count($tracking_nos));die;
    	$order_obj = array ();
    	while ( ($row = $tracking_nos->fetchRow ()) != false ) {
    		$order_obj [] = $row['tracking_no'];
//     		dump($row['tracking_no']);
    		if (count ( $order_obj ) < 35) {
    			continue;
    		}
//     		dump($order_obj,1,11);
	    	//$tracking_nos=array('9200190237757357873368','9205590237757358591967');
// 	    	$tracking_nos = Helper_Array::getCols($tracking_nos, 'tracking_no');
    		self::saveuspspost($order_obj);
	    	$order_obj = array ();
    	}
    	if(count($order_obj) > 0){
    		self::saveuspspost($order_obj);
    	}
    	
    	self::log('END');
    	Q::writeCache('IBCRouteSleep',true,array('life_time'=>3600));
    	exit;
    }
    /**
     * @todo   每35执行一次
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function saveuspspost($order_obj=array()){
    	$response = array (
    		'success' => false,
    		'message' => ''
    	);
    	$url = 'http://production.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=';
    	$body = '<?xml version="1.0" encoding="UTF-8" ?><TrackFieldRequest USERID="524FARLO6825"><Revision>1</Revision><ClientIp>127.0.0.1</ClientIp><SourceId>USPSTOOLS</SourceId>';
    	foreach ($order_obj as $tracking ){
    		$body .= '<TrackID ID="' . $tracking . '"/>';
    	}
    	$body .= '</TrackFieldRequest>';
    	//QLog::log ( 'USPS - getTrackingInfoMultiple - url - ' . $url . $body );
    	$usps_xml = Helper_Curl::get ( $url . urlencode ( $body ) );
    	//QLog::log ( 'USPS - getTrackingInfoMultiple - response - ' . $usps_xml );
    	$usps_arr = json_decode ( json_encode ( simplexml_load_string ( $usps_xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
    	if (! is_array ( $usps_arr )) {
    		$response ['message'] = '获取轨迹失败';
    		return $response;
    	}
    	//回传一单轨迹
    	if (isset ( $usps_arr ['TrackInfo'] ['@attributes'] )) {
    		if (! in_array( $usps_arr ['TrackInfo'] ['@attributes'] ['ID'], $order_obj )) {
    			$response ['message'] = '失败';
    			return $response;
    		}
    		//保存
    		self::saveuspsroute ($usps_arr ['TrackInfo'] );
    	} else {
    		//回传多单轨迹
    		if (isset($usps_arr ['TrackInfo'])){
    			//多单遍历保存
    			foreach ( $usps_arr ['TrackInfo'] as $trackinfo ) {
    				if (! in_array ( @$trackinfo ['@attributes'] ['ID'], $order_obj )) {
    					continue;
    				}
    				//保存
    				self::saveuspsroute (  $trackinfo );
    			}
    		}
    	}
    }
    
    /**
     * @todo   usps保存轨迹
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function saveuspsroute($param_arr=array()){
		//测试数据
		//$param = Helper_Ceshidata::uspstrace();
		//$param_arr = json_decode($param,true);
    	//判断是否为轨迹重查重推
    	$trail_detail = TrailTotalDetail::find('tracking_no=?',$param_arr['@attributes']['ID'] )->getOne();
    	if(!$trail_detail->isNewRecord()){
    		//如果是轨迹重查重推数据，则修改状态
    		$trail_detail->status = 1;
    		$trail_detail->save();
    	}
    	$order = Order::find ( 'tracking_no = ?', $param_arr['@attributes']['ID'] )->getOne ();
    	if (isset ( $param_arr ['TrackDetail'] ) && $param_arr ['TrackDetail']) {
    		if (isset ( $param_arr ['TrackDetail'] ['EventTime'] )) {
    			//保存
    			self::saveuspstrace ( $order, $param_arr ['TrackDetail'] );
    		} else {
    			krsort ( $param_arr ['TrackDetail'] );
    			foreach ( $param_arr ['TrackDetail'] as $trace ) {
    				//保存
    				self::saveuspstrace ( $order, $trace );
    			}
    		}
    	}
    	//TrackSummary
    	if (isset ( $param_arr ['TrackSummary'] ) && $param_arr ['TrackSummary']) {
    		//保存
    		self::saveuspstrace ( $order, @$param_arr ['TrackSummary'] );
    	}
    }
    /**
     * @todo   usps保存轨迹
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function saveuspstrace($order = null, $data = array()){
    	if (! isset ( $data ['Event'] )) {
    		return;
    	}
    	if (! isset ( $data ['EventCode'] )) {
    		return;
    	}
    	//USPS
    	if ($order->channel->network_code == 'USPS') {
    		$quantity = Farpackage::find ( 'order_id=?', $order->order_id )->sum ( 'quantity', 'sum_quantity' )->getAll ();
    		$event_time = strtotime ( @$data ['EventDate'] . (!is_array(@$data ['EventTime'])?@$data ['EventTime']:''));
    		//当轨迹发生时间在订单出库时间之前时，不作匹配；
    		if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    			return;
    		}
    		$event_content = @$data ['Event'];
    		$event_location = '';
    		if ($data ['EventCity']){
    			$event_location .= $data ['EventCity'];
    			$event_location .= $data ['EventState'] ? ','.$data ['EventState'] : '';
    			$event_location .= $data ['EventZIPCode'] ? ','.$data ['EventZIPCode'] : '';
    			$event_location .= ',US';
    		}
    		//末端单号
    		$nu = $order->tracking_no;
    		//保存route
    		$route = Route::checkAndSave ( array (
    			'network_code' => 'USPS',
    			'tracking_no' => $nu,
    			'time' => $event_time,
    			'location' => $event_location,
    			'description' => $event_content,
    			'code' => @$data ['EventCode']
    		) );
    		//已成功推送派送失败事件
    		if ($order->is_signunusual=='3'){
    			return;
    		}
    		//USPS记录订单交给末端扫描的第一枪时间
    		if(!$order->tracking_one_time && $event_content == 'Arrived Shipping Partner Facility, USPS Awaiting Item'){
    			$order->tracking_one_time = $event_time;
    			$order->save();
    		}
    		if (! is_null ( $route )) {
    			if ($order->order_status == '7') {
    				$order->order_status = '8';
    				$order->pick_up_time=time();
    				$order->save ();
    			}
    			if(!$route->time_zone){
    				$tz=$route->guessTimeZone(trim($event_location));
    			}else {
    				$tz=$route->time_zone;
    			}
    			$trackings = Tracking::find ( 'order_id = ?', $order->order_id )->order('trace_time asc')->getAll ();
    			$last_time = 0;
    			if (count ( $trackings )){
    				foreach ( $trackings as $tr ) {
    					if ($tr->trace_time) {
    						$last_time = $tr->trace_time + (8 - $tr->timezone) * 3600;
    					}
    				}
    			}
    			//轨迹匹配规则
    			$route->generateTrace($last_time,$order);
    			if ((strpos ( $data['EventStatusCategory'], 'Accepted' ) !== false)){
    				$order->pick_up_time=$event_time;
    				$order->save ();
    			}
    			//当两条轨迹时间完全一样的，只推送第一次轨迹；
    			$same_time_count = Route::find('time=? and tracking_no=?',$event_time,$nu)->getCount();
    			if ($same_time_count>1){
    				return;
    			}
    			//匹配签收信息
    			if($data['EventCode']=='01' || $data['EventCode']=='DL' || $data['EventCode']=='41' || $data['EventCode']=='63'){
    				//4pl
    				if($order->customer->customs_code=='ALPL'){
    					$event_delivery_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
    				}else{
    					$event_delivery_code = 'DELIVERY';
    				}
    				//签收事件
    				$event_delivery = Event::find ( "event_code=? and order_id= ?", $event_delivery_code,$order->order_id )->getOne ();
    				//签收轨迹
    				$track_delivery = Tracking::find ( "tracking_code='S_DELIVERY_SIGNED' and order_id=?", $order->order_id )->getOne ();
    				//签收轨迹
    				if ($track_delivery->isNewRecord ()) {
    					$trace = new Tracking ();
    					$trace->changeProps ( array (
    						'order_id' => $order->order_id,
    						'customer_id'=>$order->customer_id,
    						'far_no' => $order->far_no,
    						'tracking_code' => 'S_DELIVERY_SIGNED',
    						'location' =>  empty ( trim ( $event_location ) ) ? 'Other':$event_location,
    						'trace_desc_cn' => '快件已签收',
    						'timezone' => $tz,
    						'quantity' => $quantity ['sum_quantity'],
    						'confirm_flag' => '0',
    						'route_id' => $route->id,
    						'trace_time' => $event_time
    					) );
    					$trace->save ();
    				}
    				//delivery事件
    				if ($event_delivery->isNewRecord ()) {
    					$event = new Event ();
    					$event->changeProps ( array (
    						'order_id' => $order->order_id,
    						'customer_id'=>$order->customer_id,
    						'event_code' => $event_delivery_code,
    						'event_time' => $event_time,
    						'event_location' =>  empty ( trim ( $event_location ) ) ? 'Other':$event_location,
    						'confirm_flag' => '0',
    						'timezone' => $tz
    					) );
    					$event->save ();
    				}
    			}
    		}
    	}
    }
    /**
     * 跟踪EMS渠道末端轨迹
     */
    function actionOldEmsRoute(){
    	set_time_limit(0);
    	$select=Order::find("(service_code='EMS-FY' or service_code='ePacket-FY') and order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    	while ($row=$select->fetchRow()){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$network_code=$channel->network_code;
    			if ($network_code =='EMS'){
    				$json=Helper_Curl::get1('http://211.156.193.140:8000/cotrackapi/api/track/mail/'.$row['tracking_no'],array(
    					'version:ems_track_cn_1.0','authenticate:jsmobile_c8c8jk890qws'
    				));
    				QLog::log($row['tracking_no'].'轨迹回执:'.$json);
    				$routes=json_decode($json,TRUE);
    				if (isset($routes['traces']) && count($routes['traces'])){
    					foreach ($routes['traces'] as $value){
    						//保存route
    						$route=Route::find("tracking_no=? and network_code='EMS' and description=?",$row['tracking_no'],$value['remark'])->getOne();
    						if($route->isNewRecord()){
    							$md5=md5($row['tracking_no'].$value['acceptTime'].$value['acceptAddress'].$value['remark']);
    							$route->changeProps(array(
    								'network_code'=>'EMS',
    								'md5'=>$md5,
    								'tracking_no'=>$row['tracking_no'],
    								'time'=>strtotime($value['acceptTime']),
    								'location'=>$value['acceptAddress'],
    								'description'=>$value['remark']
    							));
    							$route->save();
    							if($order->order_status=='7'){
    							    $order->order_status='8';
    							    $order->pick_up_time=time();
    							    $order->save();
    							    $this->saveoutevents($order, strtotime($value['acceptTime']));
    							}
    						}
    						if(strpos($value['remark'], '南京国际营销中心已收件')!==false){
    							//承运商取件事件
    							$carrier_pickup=Event::find("event_code='CARRIER_PICKUP' and order_id= ?",$row['order_id'])->getOne();
    							//转运轨迹：F_CARRIER_PICKUP_RT_5035
    							$pickup_track=Tracking::find("tracking_code='F_CARRIER_PICKUP_RT_5035' and trace_desc_cn like ? and order_id= ?",'%'.$row['tracking_no'].'%',$row['order_id'])->getOne();
    							//承运商取件事件
    							if ($carrier_pickup->isNewRecord()){
    								$event=new Event();
    								$event->changeProps(array(
    									'order_id'=>$order->order_id,
    									'customer_id'=>$order->customer_id,
    									'event_code'=>'CARRIER_PICKUP',
    									'event_time'=>strtotime($value['acceptTime']),
    									'event_location'=>'南京',
    									'location'=>'南京',
    									'confirm_flag'=>'1',
    									'timezone'=>'8'
    								));
    								$event->save();
    								$order->carrier_pick_time=strtotime($value['acceptTime']);
    								$order->save();
    							}
    							//转运轨迹：F_CARRIER_PICKUP_RT_5035
    							if($pickup_track->isNewRecord()){
    								$trace=new Tracking();
    								$trace->changeProps(array(
    									'order_id'=>$order->order_id,
    									'far_no'=>$order->far_no,
    									'tracking_code'=>'F_CARRIER_PICKUP_RT_5035',
    									'location'=>'南京',
    									'trace_desc_cn'=>'包裹重新安排转运,转【'.$row['tracking_no'].'】',
            							'trace_desc_en'=>'Reschedule transshipment to EMS['.$row['tracking_no'].'].Track in:http://www.ems.com.cn/english.html or https://www.17track.net/en',
    									'timezone'=>'8',
    									'confirm_flag'=>'1',
    									'route_id'=>$route->id,
    									'quantity'=>$quantity['sum_quantity'],
    									'trace_time'=>strtotime($value['acceptTime'])+rand(0, 10)*60+rand(0, 10)
    								));
    								$trace->save();
    							}
    						}
	    					if(strpos($value['remark'], '妥投')!==false && strpos($value['remark'], '未妥投')===false && strpos($value['remark'], '退回 妥投')===false){
	    						//收件城市分区
	    						$time_zone=CityTimezone::find('code_word_two=? and city=?',$order->consignee_country_code,trim($order->consignee_city))->getOne();
	    						if($time_zone->isNewRecord()){
	    							$time_zone=CityTimezone::find('code_word_two=?',$order->consignee_country_code)->order('timezone desc')->getOne();
	    						}
	    						//签收事件
	    						$event_delivery=Event::find("event_code='DELIVERY' and order_id= ?",$order->order_id)->getOne();
	    						//签收轨迹
	    						$track_delivery=Tracking::find("tracking_code='S_DELIVERY_SIGNED' and order_id=?",$order->order_id)->getOne();
    							//签收轨迹
    							if($track_delivery->isNewRecord()){
    								$trace=new Tracking();
    								$trace->changeProps(array(
    									'order_id'=>$order->order_id,
    									'far_no'=>$order->far_no,
    									'tracking_code'=>'S_DELIVERY_SIGNED',
    									'location'=>$order->consignee_city,
    									'trace_desc_cn'=>'快件已签收',
    									'timezone'=>$time_zone->timezone,
    									'quantity'=>$quantity['sum_quantity'],
    									'confirm_flag'=>'1',
    									'route_id'=>$route->id,
    									'trace_time'=>strtotime($value['acceptTime'])
    								));
    								$trace->save();
    							}
    							//delivery事件
    							if ($event_delivery->isNewRecord()){
    								$event=new Event();
    								$event->changeProps(array(
    									'order_id'=>$order->order_id,
    									'customer_id'=>$order->customer_id,
    									'event_code'=>'DELIVERY',
    									'event_time'=>strtotime($value['acceptTime']),
    									'event_location'=>$order->consignee_city,
    									'confirm_flag'=>'1',
    									'timezone'=>$time_zone->timezone
    								));
    								$event->save();
    								//$order->delivery_time=strtotime($value['acceptTime']);
    								//$order->order_status='9';
    								//$order->save();
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	exit;
    }
    
     /**
     * 跟踪EMS渠道末端轨迹
     */
    function actionEmsRoute(){
    	set_time_limit(0);
    	$select=Order::find("order_status in (?) and get_trace_flag in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED),array(1));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    	while (($row=$select->fetchRow())!= false){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$network_code=$channel->network_code;
    			$tnetwork_code=$channel->trace_network_code;
    			if ($network_code =='EMS' || $network_code =='DHL' || $tnetwork_code == 'DHL'){
    				if($network_code =='EMS'){
    					$carrier = 'china-ems';
    				}elseif ($network_code =='DHL' || $tnetwork_code == 'DHL'){
    					$carrier = 'dhl';
    				}
    				//$ft_url = 'http://check.coomao.com/index.php/default/ftracking/refresh';
     				$ft_url = 'http://check.coomao.com/index.php/default/ftracking/book';
    				
    				$query_str = array (
    					'apikey' => 'EmsRoute',
    					'carrier' => $carrier,
    					'bill_no' => $order->tracking_no,
    					'external_id' => $order->ali_order_no
    				);
    				if($network_code =='DHL'){
    					$query_str['lang'] = 'en';
    				}
    				$query_str = http_build_query ( $query_str );
    				$url = $ft_url . '?' . $query_str;
    				QLog::log ( 'FarTracking - refresh - url - ' . $url );
    				try {
    					$fartracking_res = Helper_Curl::post ( $url, '' );
    				} catch ( Exception $e ) {
    					continue;
    				}
    				QLog::log('emsroute'.$row['tracking_no'].' '.$fartracking_res);
					$fartracking_res = json_decode($fartracking_res,true);
//     				if(isset($fartracking_res['code']) && ($fartracking_res['code']=='0' || $fartracking_res['code']=='1000')){
    				if(isset($fartracking_res['code']) && $fartracking_res['code']=='0'){
	    				// 不需要再次订阅
	    				$order->get_trace_flag = 2;
	    				$order->save ();
// 	    				$fartracking_res = json_encode($fartracking_res);
// 	    				$response = self::saveemsroute($fartracking_res);
// 	    				if(!$response['success']){
// 	    					$order->get_trace_flag = 3;
// 	    					$order->save ();
// 	    				}
    				}else {
    					$message = json_decode($fartracking_res['message'],true);
    					if($message['meta']['message']=='Tracking already exists.'){
    						$order->get_trace_flag = 2;
    						$order->save ();
    					}else {
    						$order->get_trace_flag = 3;
    						$order->save ();
    						$email_response=Helper_Mailer::send('xujy@far800.com', $network_code.'轨迹订阅失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.@$message['meta']['message']);
    						continue;
    					}
    				}
    			}
    		}
    	}
    	exit;
    }
    
    /**
     * @todo   定时订阅已提取的末端轨迹
     * @author 吴开龙
     * @since  2020-10-26 11:00:54
     * @param
     * @return
     * @link   #
     */
    function actionTimingRoute(){
    	set_time_limit(0);
    	//当前日期的时间戳
    	$time = strtotime(date('Y-m-d'));
    	//取出所有DHL和EMS已出库和已提取的订单
    	$select=Order::find("order_status in (?) and account in (?) and (again_time is null or again_time <> ?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED),array('DHL','EMS'),$time)->getAll();
    	foreach ($select as $o){
    		//取出最新的一条轨迹 判断时间超过三天就执行修改
    		if($o->again_time){
    			continue;
    		}
    		//已重新订阅过就不在订阅了
    		$tracking=Tracking::find('order_id=?',$o->order_id)->order('create_time desc')->getOne();
    		//判断时间是否超过三天，超过则执行 
    		if(time() - $tracking->create_time > 432000){
    			//修改状态为4，执行强制订阅
    			$o->get_trace_flag=4;
    			//保存执行时间
    			$o->again_time = strtotime(date('Y-m-d'));
    			$o->save();
    		}
    	}
    	exit;
    }
    
    /**
     * 强制订阅末端轨迹
     */
    function actionRefRoute(){
    	set_time_limit(0);
    	$select=Order::find("order_status in (?) and get_trace_flag in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED),array(4));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    	$i=1;
    	ob_end_clean (); 
    	while (($row=$select->fetchRow())!= false){  		
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$network_code=$channel->network_code;
    			$tnetwork_code=$channel->trace_network_code;  			
    			if ($network_code =='EMS' || $network_code =='DHL' || $tnetwork_code == 'DHL'){
    				echo $order->ali_order_no;
    				if($network_code =='EMS'){
    					$carrier = 'china-ems';
    				}elseif ($network_code =='DHL' || $tnetwork_code == 'DHL'){
    					$carrier = 'dhl';
    				}else{
    					$order->get_trace_flag = 2;
    					$order->save ();
    					continue;
    				}
    				
    				$ft_url = 'http://check.coomao.com/index.php/default/ftracking/refresh';
    				
    				$query_str = array (
    					'apikey' => 'EmsRoute',
    					'carrier' => $carrier,
    					'bill_no' => $order->tracking_no,
    					'external_id' => $order->ali_order_no
    				);
    				if($network_code =='DHL'){
    					$query_str['lang'] = 'en';
    				}
    				$query_str = http_build_query ( $query_str );
    				$url = $ft_url . '?' . $query_str;
    				//QLog::log ( 'FarTracking - refresh - url - ' . $url );
    				try {
    					$fartracking_res = Helper_Curl::post ( $url, '' );
    				} catch ( Exception $e ) {
    					continue;
    				}
    				//QLog::log('emsroute'.$row['tracking_no'].' '.$fartracking_res);
    				//echo $fartracking_res;
    				$fartracking_res = json_decode($fartracking_res,true);    				
    				//     				if(isset($fartracking_res['code']) && ($fartracking_res['code']=='0' || $fartracking_res['code']=='1000')){
    				if(isset($fartracking_res['code']) && $fartracking_res['code']=='0'){
    					// 不需要再次订阅
//     					echo '成功';
    					$order->get_trace_flag = 2;
    					$order->save ();
    					// 	    				$fartracking_res = json_encode($fartracking_res);
    					// 	    				$response = self::saveemsroute($fartracking_res);
    					// 	    				if(!$response['success']){
    					// 	    					$order->get_trace_flag = 3;
    					// 	    					$order->save ();
    					// 	    				}
    				}else {
    					$message = json_decode($fartracking_res['message'],true);
    					if($message['meta']['message']=='Tracking already exists.'){
    						$order->get_trace_flag = 2;
    						$order->save ();
    					}else {
    						$order->get_trace_flag = 3;
    						$order->save ();
    						//$email_response=Helper_Mailer::send('xujy@far800.com', $network_code.'轨迹订阅失败', '阿里单号 '.$order->ali_order_no.',失败原因：'.@$message['meta']['message']);
    						//continue;
    					}
    					echo '失败';
    				}
    				$i++;
    				if (($i) % 15 == 0) {
    					sleep ( 60 );
    					flush ();
    				}
    			}
    		}
    		
    	}
    	exit;
    }
    
    function actionEmsRouteCallBack(){
    	$param = @$_POST ['param'];
    	QLog::log('emsrouteCallBack:'.' '.$param);
    	$response = self::saveemsroute($param);
    	return $response;
    	exit ();
    }
    	
    static function saveemsroute($param){
    	$response = array (
    		'success' => false,
    		'message' => ''
    	);
    	$param_arr = json_decode ( $param, true );
    	$lastResult = array ();
    	if (isset ( $param_arr ['lastResult'] )) {
    		$lastResult = $param_arr ['lastResult'];
    	} else {
    		$lastResult = @$param_arr ['data'] ['lastResult'];
    	}
    	
    	if (! $lastResult ['nu']) {
    		$response ['message'] = "沒有单号";
    		return $response;
    	}
    	
    	$nu = @$lastResult ['nu'];
    	$data = @$lastResult ['data'];
    	if (count ( $data ) <= 0) {
    		$response ['message'] = $nu . " 数据为空";
    		return $response;
    	}
    	
    	$order = Order::find ( 'tracking_no = ?', $nu )->getOne ();
    	if ($order->isNewRecord ()) {
    		$response ['message'] = $nu . " 不存在";
    		return $response;
    	}
    	//判断是否为轨迹重查重推
    	$trail_detail = TrailTotalDetail::find('tracking_no=?',$nu)->getOne();
    	if(!$trail_detail->isNewRecord()){
    		//如果是轨迹重查重推数据，则修改状态
    		$trail_detail->status = 1;
    		$trail_detail->save();
    	}
    	if ($order->channel->network_code == 'EMS') {
    		$quantity = Farpackage::find ( 'order_id=?', $order->order_id )->sum ( 'quantity', 'sum_quantity' )
    		->getAll ();
    		
    		foreach ( $data as $key=>$value ) {
    			$event_time = strtotime ( $value ['time'] );
    			//当轨迹发生时间在订单出库时间之前时，不作匹配； 
    			if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    				continue;
    			}
    			$event_content = str_replace ( 'esb location ', '', $value ['context'] );
    			$event_content = str_replace ( 'old ', '', $event_content );
    			
    			$event_location = str_replace ( 'esb location ', '', $value ['location'] );
    			$event_location = str_replace ( 'old ', '', $event_location );
    			
    			//保存route
    			$route = Route::checkAndSave ( array (
    				'network_code' => 'EMS',
    				'tracking_no' => $nu,
    				'time' => $event_time,
    				'location' => $event_location,
    				'description' => $event_content
    			) );
    			if ($order->is_signunusual=='3'){
    				continue;
    			}
    			//EMS记录订单交给末端扫描的第一枪时间
    			if(!$order->tracking_one_time && stristr($event_content,'已到达') !== false && stristr($event_content,'投递局') !== false){
    				$order->tracking_one_time = $event_time;
    				$order->save();
    			}
    			if (! is_null ( $route )) {
    				if ($order->order_status == '7') {
    					$order->order_status = '8';
    					$order->pick_up_time=time();
    					$order->save ();
    					self::sendmail ( $order );
    					// 	    				$checkout_time=$event_time;
    					// 	    				if(strpos($event_content, '南京国际营销中心已收件')!==false  || strpos($event_content, '南京国际跨境中心已收件')!==false){
    					// 	    					$Hour=date("H",$event_time);
    					// 	    					if($Hour<22){
    					// 	    						$checkout_time=$event_time-(30+rand(1, 7))*60-4*(rand(1, 7)+2);
    					// 	    					}else{
    					// 	    						$checkout_time=$event_time-24*60*60+(30+rand(1, 7))*60-4*(rand(1, 7)+2);
    					// 	    					}
    					// 	    				}
    					//     				self::saveoutevents($order, $checkout_time);
    				}
    				if(!$route->time_zone){
    					$tz=$route->guessTimeZone(trim($event_location));
    					if (!is_int($tz)){
    						$tz=8;
    					}
    				}else {
    					$tz=$route->time_zone;
    				}
    				if ((strpos ( $event_content, '已收件' ) !== false || strpos ( $event_content, '已收寄' ) !== false ) && ($key==0 || $key ==1) ){
    					if($order->customer->customs_code=='ALPL'){
    						$event_code = 'SORTING_CENTER_HO_OUT_CALLBACK';
    					}else{
    						$event_code = 'CARRIER_PICKUP';
    					}
    					//承运商取件事件
    					$carrier_pickup = Event::find ( "event_code='CARRIER_PICKUP' and order_id= ?", $order->order_id )->getOne ();
    					//转运轨迹：F_CARRIER_PICKUP_RT_5035
    					$pickup_track = Tracking::find ( "tracking_code='F_CARRIER_PICKUP_RT_5035' and trace_desc_cn like ? and order_id= ?", '%' . $nu . '%', $order->order_id )->getOne ();
    					//承运商取件事件
    					if ($carrier_pickup->isNewRecord () ){
    						$event = new Event ();
    						$event->changeProps ( array (
    							'order_id' => $order->order_id,
    							'customer_id'=>$order->customer_id,
    							'event_code' => $event_code,
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
    							'trace_desc_cn' => '包裹重新安排转运,转【' . $nu . '】',
    							'trace_desc_en' => 'Reschedule transshipment to EMS[' . $nu . '].Track in:http://www.ems.com.cn/english.html or https://www.17track.net/en',
    							'timezone' => '8',
    							'confirm_flag' => '1',
    							'route_id' => $route->id,
    							'quantity' => $quantity ['sum_quantity'],
    							'trace_time' => $event_time + rand ( 0, 10 ) * 60 + rand ( 0, 10 )
    						) );
    						$trace->save ();
    					}
    				}
    				//当两条轨迹时间完全一样的，只推送第一次轨迹；
    				$same_time_count = Route::find('time=? and tracking_no=?',$event_time,$nu)->getCount();
    				
    				if ($same_time_count>1){
    					continue;
    				}
    				
    				$trackings = Tracking::find ( 'order_id = ?', $order->order_id )->order('trace_time asc')->getAll ();
    				$last_time = 0;
    				if (count ( $trackings )){
    					foreach ( $trackings as $tr ) {
    						if ($tr->timezone == - 19) {
    							continue;
    						}
    						if ($tr->trace_time) {
    							$last_time = $tr->trace_time + (8 - $tr->timezone) * 3600;
    						}
    					}
    				}
    				//匹配自定义轨迹
    				$route->emsgenerateTrace($last_time,$order);
    				 //匹配签收信息  				
    				if(strpos($event_content, '妥投')!==false && strpos($event_content, '未妥投')===false 
    					&& strpos($event_content, '退回 妥投')===false && strpos($event_content, '退回妥投')===false){
    					//4pl
    					if($order->customer->customs_code=='ALPL'){
    						$event_delivery_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
    					}else{
    						$event_delivery_code = 'DELIVERY';
    					}
    					//签收事件
    					$event_delivery = Event::find ( "event_code=? and order_id= ?", $event_delivery_code,$order->order_id )->getOne ();
    					//签收轨迹
    					$track_delivery = Tracking::find ( "tracking_code='S_DELIVERY_SIGNED' and order_id=?", $order->order_id )->getOne ();
    					
    					//签收轨迹
    					if ($track_delivery->isNewRecord ()) {
    						$trace = new Tracking ();
    						$trace->changeProps ( array (
    							'order_id' => $order->order_id,
    							'customer_id'=>$order->customer_id,
    							'far_no' => $order->far_no,
    							'tracking_code' => 'S_DELIVERY_SIGNED',
    							'location' =>  empty ( trim ( $event_location ) ) ? 'Other':$event_location,
    							'trace_desc_cn' => '快件已签收',
    							'timezone' => $tz,
    							'quantity' => $quantity ['sum_quantity'],
    							'confirm_flag' => '0',
    							'route_id' => $route->id,
    							'trace_time' => $event_time
    						) );
    						$trace->save ();
    					}
    					//delivery事件
    					if ($event_delivery->isNewRecord ()) {
    						$event = new Event ();
    						$event->changeProps ( array (
    							'order_id' => $order->order_id,
    							'customer_id'=>$order->customer_id,
    							'event_code' => $event_delivery_code,
    							'event_time' => $event_time,
    							'event_location' =>  empty ( trim ( $event_location ) ) ? 'Other':$event_location,
    							'confirm_flag' => '0',
    							'timezone' => $tz
    						) );
    						$event->save ();
    					}
    				} else {
    					//EMS其他轨迹都记录UNknow
    					//修改时区
    					$trace = Tracking::find ( 'route_id = ?', $route->id )->getOne ();
    					if ($trace->isNewRecord () && $event_time >= $last_time) {
    						$trace->changeProps ( array (
    							'order_id' => $order->order_id,
    							'customer_id'=>$order->customer_id,
    							'far_no' => $order->far_no,
    							'tracking_code' => 'UNKNOWN',
    							'location' => empty ( trim ( $event_location ) ) ? 'Other':$event_location,
    							'trace_desc_cn' => $event_content,
    							'timezone' => $tz,
    							'quantity' => $quantity ['sum_quantity'],
    							'confirm_flag' => '1',
    							'route_id' => $route->id,
    							'trace_time' => $event_time
    						) );
    						$trace->save ();
    					}
    				}
    			}
    		}
    	}elseif ($order->channel->network_code == 'DHL' || $order->channel->trace_network_code == 'DHL'){
    		$quantity = Farpackage::find ( 'order_id=?', $order->order_id )->sum ( 'quantity', 'sum_quantity' )
    		->getAll ();
    		$trackings=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1"',$order->order_id)->getAll();
    		
    		$last_time=0;
    		if (count($trackings)) foreach ($trackings as $tr){
    			if ($tr->timezone==-19){
    				continue;
    			}
    			if ($tr->trace_time){
    				$last_time =$tr->trace_time + (8-$tr->timezone )*3600;
    			}
    		}
    		
    		foreach ( $data as $value ) {
    			$event_time = strtotime ( $value ['time'] );
    			if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    				continue;
    			}
    			
    			$location=$value ['location'];
    			$location=explode("-", $location);
    			$country=@$location[1];
    			if(strpos($country, ',')){
    				$loca=$country;
    				$loca=explode(",", $loca);
    				$country=$loca[0];
    			}
    			
    			if((strpos ( strtolower ( $value ['context'] ), 'delivered' ) !== false && strpos ( strtolower ( $value ['context'] ), 'refused delivery' ) === false ) && empty($country)){
    				$country=$order->consignee_country_code;
    			}
    			
    			$route=Route::checkAndSave(array(
    				'network_code'=>'DHL',
    				'tracking_no'=>$nu,
    				'time'=>$event_time,
    				'location'=>@$location[0].','.$country,
    				'description'=>$value ['context'],
    			));
    			//DHL记录订单交给末端扫描的第一枪时间
    			if(!$order->tracking_one_time && stristr($value ['context'],'Arrived at Delivery Facility in') !== false){
    				$order->tracking_one_time = $event_time;
    				$order->save();
    			}
    			if (!is_null($route) && (strpos($value ['context'],'Shipment information received') === false || $value ['context'] !='HONG KONG - HONG KONG  Shipment information received')){
    				$route->generateTrace($last_time,$order);
    				
    				if($order->order_status=='7' ){
    					$order->order_status='8';
    					$order->pick_up_time=time();
    					$order->save();
    					//                             	self::saveoutevents($order, strtotime($date.$hour));
    					self::sendmail($order);
    				}
    			}   			
    		}
    	}
    	$response ['success'] = true;
    	$response ['message'] = $nu . " 接受成功";
    	return $response;
    }
    
    /**
     * 跟踪FEDEX渠道末端轨迹
     * @param unknown $str
     */
    function actionFedexRoute(){
    	set_time_limit(0);
        $select=Order::find("service_code in (?) and order_status in (?)",array('Express_Standard_Global','WIG-FY','EUUS-FY','US-FY'),array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    	//fedex测试账号信息
    	//$key='vOSDgWEXV7QnRbxP';
    	//$password='h2XRZgneNCQALyHjMP682U5re';
    	//$accountNumber='510087500';
    	//$meterNumber='100421805';
    	//fedex假发生产账号信息
//     	$key='f5XzZpzEwekZ8TJv';
// 	    $password='ILI1RWIQVhrjIZFBY5CqNP41i';
// 	    $accountNumber='631526993';
// 	    $meterNumber='250139501';
//		fedex创梦谷生产账号信息
    	$key='Y8DNyhcsybFkXWDy';
    	$password='2CLHx5wETXxDSCe1alwpS6Jxn';
    	$accountNumber='497335868';
    	$meterNumber='251395350';
    	while ($row=$select->fetchRow()){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
    			$network_code=$channel->network_code;
    			$tnetwork_code=$channel->trace_network_code;
    			if ($network_code =='FEDEX' || $tnetwork_code == 'FEDEX'){
    				$province='';
    				if(in_array($order->consignee_country_code, array('US','CA'))){
    					$usca=Uscaprovince::find('province_name=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
    					if(!$usca->isNewRecord()){
    						$province=$usca->province_code_two;
    					}else{
    						$province=$order->consignee_city;
    					}
    				}
    				$res = array(
    					'soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v9="http://fedex.com/ws/track/v9"' => array(
    						'soapenv:Header' =>'',
    						'soapenv:Body' => array(
    							'v9:TrackRequest' => array(
    								'v9:WebAuthenticationDetail' => array(
    									'v9:UserCredential'=>array(
    										'v9:Key'=>$key,
    										'v9:Password'=>$password,
    									),
    								),
    								'v9:ClientDetail'=>array(
    									'v9:AccountNumber'=>$accountNumber,
    									'v9:MeterNumber'=>$meterNumber,
    									'v9:Localization'=>array(
    										'v9:LanguageCode'=>'EN',
    										'v9:LocaleCode'=>'US',
    									)
    								),
    								'v9:TransactionDetail'=>array(
    									'v9:CustomerTransactionId'=>'Track By Number_v9',
    									'v9:Localization'=>array(
    										'v9:LanguageCode'=>'EN',
    										'v9:LocaleCode'=>'US'
    									)
    								),
    								'v9:Version'=>array(
    									'v9:ServiceId'=>'trck',
    									'v9:Major'=>'9',
    									'v9:Intermediate'=>'1',
    									'v9:Minor'=>'0'
    								),
    								'v9:SelectionDetails'=>array(
    									'v9:CarrierCode'=>'FDXE',
    									'v9:PackageIdentifier'=>array(
    										'v9:Type'=>'TRACKING_NUMBER_OR_DOORTAG',
    										'v9:Value'=>$row['tracking_no']
    									),
    								),
    								'v9:ProcessingOptions'=>'INCLUDE_DETAILED_SCANS'
    							),
    						),
    					)
    				);
    				$res = Helper_xml::simpleArr2xml($res,null);
    				//fedex测试地址
    				//$url = "https://wsbeta.fedex.com:443/web-services";
    				//fedex正式地址
    				$url='https://ws.fedex.com:443/web-services';
    				try {
    					$return = Helper_Curl::post($url, $res);
    				} catch ( Exception $e ) {
    					continue;
    				}
    				$return=self::xml_to_array($return);
    				if($return['TrackReply']['CompletedTrackDetails']['TrackDetails']){
    					$return=$return['TrackReply']['CompletedTrackDetails']['TrackDetails'];
    					if(isset($return['Notification']['Severity']) && $return['Notification']['Severity'] != 'ERROR'){
    						// 保存
    						if(count($return['Events'])>0){
    							foreach (array_reverse($return['Events']) as $d){
    								//过滤掉第一条轨迹
    								if (!isset($d['Address']['CountryCode'])){
    									continue;
    								}
    								$time_zone=substr($d['Timestamp'], 19,3);
    								if($time_zone<0){
    									$time_zone='-'.abs($time_zone);
    								}else {
    									$time_zone=abs($time_zone);
    								}
    								$time = strtotime(substr($d['Timestamp'], 0,19))+(8-$time_zone)*3600;
    								if(!empty($order->warehouse_out_time) && $time<$order->warehouse_out_time){
    									continue;
    								}
    								if($d['EventDescription']<>'Shipment information sent to FedEx'){
    								    if($order->order_status=='7'){
    								        $order->order_status='8';
    								        $order->pick_up_time=time();
    								        $order->save();
    								        self::sendmail($order);
//     								        self::saveoutevents($order, strtotime(substr($d['Timestamp'], 0,19)));
    								    }
    								}
    								$r=Route::checkAndSave(array(
    									'network_code'=>'FEDEX',
    									'tracking_no'=>$row['tracking_no'],
    									'time'=>strtotime(substr($d['Timestamp'], 0,19)),
    									'location'=>@$d['Address']['City'].','.@$d['Address']['StateOrProvinceCode'].','.@$d['Address']['CountryCode'],
    									'description'=>$d['EventDescription'].(isset($d['StatusExceptionDescription'])?' '.$d['StatusExceptionDescription']:''),
    									'time_zone'=>$time_zone,
    								    'code'=>$d['EventType']
    								));
    								if ($order->is_signunusual=='3'){
    									continue;
    								}
    								//FEDEX记录订单交给末端扫描的第一枪时间
    								if(!$order->tracking_one_time && stristr($r->description,'At local FedEx facility') !== false){
    									$order->tracking_one_time = $r->time;
    									$order->save();
    								}
    								if (!is_null($r)){
    								    if (strpos(strtoupper($r->description), 'PICKED UP')!==false){
    								    	if($order->customer->customs_code=='ALPL'){
    								    		$event_code = 'SORTING_CENTER_HO_OUT_CALLBACK';
    								    	}else{
    								    		$event_code = 'CARRIER_PICKUP';
    								    	}
    								    	$r_event = Event::find('order_id = ? and event_code =?',$order->order_id ,$event_code)->getOne();
    								        if($r_event->isNewRecord()){
    								           $new_event = new Event();
    								           $new_event->changeProps(array(
                            					'order_id'=>$order->order_id,
    								           	'customer_id'=>$order->customer_id,
    								           	'event_code'=>$event_code,
                            					'event_time'=>$r->time,
//                             					'location'=>$d['Address']['City'],
                            					'event_location'=>$d['Address']['City'],
                            					'timezone'=>$time_zone,
                            					'confirm_flag'=>'1'
                            				   ));
                            				   $new_event->save();
    								        }
    								    }
    									$r->generateFedexTrace($order,$d['Address']['City'],$d['Address']['CountryCode']);
    								}
    							}
    							if(isset($return['EstimatedDeliveryTimestamp']) && !empty($return['EstimatedDeliveryTimestamp'])){
    							    $track=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and trace_desc_cn like ?',$row['order_id'],'%'.substr($return['EstimatedDeliveryTimestamp'], 0,10).'%')->getOne();
    							    $track2=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and yflag="1"',$row['order_id'])->getOne();
    							    $track3=Tracking::find('order_id =? and tracking_code="S_DELIVERY_SIGNED"',$row['order_id'])->getOne();
    							    $tr=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1" and confirm_flag<>"2"',$order->order_id)->order('tracking_id desc')->getOne();
    							    if($track->isNewRecord() && $track3->isNewRecord()){
    							        $order->present_time=strtotime(substr($return['EstimatedDeliveryTimestamp'], 0,19));
    							        $order->save();
    							        if($tr->location){
        							        $track->changeProps(array(
        							            'order_id'=>$order->order_id,
        							        	'customer_id'=>$order->customer_id,
        							            'far_no'=>$order->far_no,
        							            'tracking_code'=>'F_DELIVERY_5044',
        							            'timezone'=>'8',
        							            'confirm_flag'=>'1',
        							            'trace_time'=>time(),
        							            'location'=>$tr->location,
        							            'quantity'=>$tr->quantity,
        							            'trace_desc_cn'=>'预计派送时间（当地）:'.substr($return['EstimatedDeliveryTimestamp'], 0,10),
        							        	'trace_desc_en'=>'Scheduled Delivery Time (Local):'.substr($return['EstimatedDeliveryTimestamp'], 0,10),
        							            'yflag'=>'1'
        							        ));
        							        $track->save();
        							        if(!$track2->isNewRecord()){
        							            $track->trace_desc_cn='预计派送时间（当地） 更新:'.substr($return['EstimatedDeliveryTimestamp'], 0,10);
        							            $track->trace_desc_en='Scheduled Delivery Time Updated (Local):'.substr($return['EstimatedDeliveryTimestamp'], 0,10);
        							            $track->save();
        							        }
    							        }
    							    }
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	//sleep(900);
    	exit;
    }
    
    /**
     * 跟踪中美专线头程轨迹
     */
    function actionUsRoute(){
    	ini_set('max_execution_time', '0');
        set_time_limit(0);
        ini_set('default_socket_timeout', '20');
        $select=Order::find("service_code='US-FY' and order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
    	$select= $select->setColumns('order_id,tracking_no,channel_id,ems_order_id')->all()->getQueryHandle();
    	while ($row=$select->fetchRow()){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['channel_id']) && !empty($row['tracking_no']) && !empty($row['ems_order_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
                $rou=Route::find('tracking_no = ? and network_code = "UPS"',$row['tracking_no'])->order('id desc')->getOne();
                if(!$rou->isNewRecord()){
                    if($rou->description != 'Order Processed: Ready for UPS' && $rou->description != 'Order Processed: In Transit to UPS'){
                        continue;
                    }
                }
                $lastTime=0;
                if (count($trackings)) foreach ($trackings as $tr){
    				if ($tr->timezone==-19){
    					continue;
    				}
    				if ($tr->trace_time){
    					$lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
    				}
    			}
    			$network_code=$channel->network_code;
    			if ($network_code =='US-FY'){
    				$params=array(
    					'token'=>'2rcBrK5k5QJFoejbaCs0',
    					'timestamp'=>time(),
    					'shippingNum'=>$row['ems_order_id'],
    				);
                    $url='http://b.fullspeedparcel.com/?c=shippingTrack&a=fullTrack';
                    Helper_Curl::$connecttimeout='120';
                    Helper_Curl::$timeout='120';
                    $r=Helper_Curl::post($url,self::getPostData('AJsFkok2ty31TaXPOXTD', $params));
                    QLog::log('UsRoute'.$row['tracking_no'].':'.$r);
    				$r=json_decode($r,true);
    				if($r['code']==0){
    				    $data=$r['data'];
                        krsort($data['list']);
                        foreach ($data['list'] as $list){
                            if($list['info']=='Data Received'){
    					        continue;
    					    }
    					    if($list['info']=='Arrived into the US' || $list['status']=='Arrived into the US'){
    					        $route=Route::checkAndSave(array(
    					            'network_code'=>$network_code,
    					            'tracking_no'=>$row['tracking_no'],
    					            'time'=>strtotime($list['time']),
    					            'location'=>$list['location'] ? $list['location'] :" ",
    					            'description'=>$list['status'],
    					        ));
    					        if (!is_null($route)){
        					        if(trim($list['location'])){
        					            $utc8=strtotime($list['time'])+13*3600;
        					            if($utc8 >= $lastTime){
        					                $qty=Orderpackage::find('order_id=?',$order->order_id)->getSum('quantity');
        					                $tring=new Tracking();
        					                $tring->changeProps(array(
        					                    'order_id'=>$order->order_id,
        					                	'customer_id'=>$order->customer_id,
        					                    'far_no'=>$order->far_no,
        					                    'tracking_code'=>'S_TH_IN',
        					                    'timezone'=>'-5',
        					                    'confirm_flag'=>1,
        					                    'trace_time'=>strtotime($list['time']),
        					                    'location'=>$list['location'],
        					                    'quantity'=>$qty,
        					                    'trace_desc_cn'=>Tracking::$trace_code_cn['S_TH_IN'],
        					                    'trace_desc_en'=>'Arrived into the US',
        					                    'route_id'=>$route->id
        					                ));
        					                $tring->save();
        					            }
        					        }
    					        }
    					    }else {
    					        $info=$list['info'];
    					        if(!strpos($info, ']')){
    					            continue;
    					        }
    					        if(empty($list['location'])){
    					            continue;
    					        }
    					        if(strlen($list['status'])==2 || !$list['status']){
    					            continue;
    					        }
    					        if($order->order_status=='7'){
    					            $order->order_status='8';
    					            $order->pick_up_time=time();
    					            $order->save();
    					            self::sendmail($order);
//     					            self::saveoutevents($order, strtotime($list['time']));
    					        }
    					        $info=str_replace("[", '', $info);
    					        $info=explode(']', $info);
    					        $info=explode(',', $info[0]);
    					        $route=Route::checkAndSave(array(
    					            'network_code'=>$network_code,
    					            'tracking_no'=>$row['tracking_no'],
    					            'time'=>strtotime($list['time']),
    					            'location'=>$list['location'].',,'.@$info[0],
    					            'description'=>$list['status'],
    					        ));
    					        if (!is_null($route)){
    					            $route->generateTrace($lastTime,$order);
    					        }
    					    }
                        }
    				}
    			}
    		}
    		sleep(3);
    	}
    	exit;
    }
    
    /**
     * 跟踪中美专线俄速通头程轨迹
     */
    function actioneUsRoute(){
        ini_set('max_execution_time', '0');
        set_time_limit(0);
        ini_set('default_socket_timeout', '20');
        $select=Order::find("service_code in ('US-FY','CNUS-FY','CNUSBJ-FY') and order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
        $select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
        while ($row=$select->fetchRow()){
            $order=Order::find('order_id =?',$row['order_id'])->getOne();
            if(!empty($order->ems_order_id)){
                continue;
            }
            if (!empty($row['channel_id']) && !empty($row['tracking_no'])){
                $channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
                $trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
                $rou=Route::find('tracking_no = ? and network_code = "UPS"',$row['tracking_no'])->order('id desc')->getOne();
                if(!$rou->isNewRecord()){
                	QLog::log('description :'.$rou->description );
                    if($rou->description != 'Order Processed: Ready for UPS' && $rou->description != 'Order Processed: In Transit to UPS'){
                        continue;
                    }
                }
                $lastTime=0;
                if (count($trackings)) foreach ($trackings as $tr){
                    if ($tr->timezone==-19){
                        continue;
                    }
                    if ($tr->trace_time){
                        $lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
                    }
                }
                $network_code=$channel->network_code;
                if ($network_code =='US-FY'){
                    $url="http://us.far800.com/api/tracking?orderNumber=".$row['tracking_no'];
                    $header=array(
                        'ClientKey:IB-QSBG',
                        'ClientSecret:5c5aea092932ce8d1a6b6efedfc6a4e6'
                    );
                    $return=Helper_Curl::get1($url,$header);
                    QLog::log($row['tracking_no'].'ibtracking:'.$return);
                    $return=json_decode($return,true);
                    foreach ($return as $r){
                        $events=$r['events'];
                        $data=array();
                        foreach ($events as $e){
                            $data[]=$e['time']['local'];
                        }
                        array_multisort($data,SORT_ASC,$events);
                        foreach ($events as $ev){
                            if($ev['status']['name']=='Received data'){
                                continue;
                            }
                            if(!empty($order->warehouse_out_time) && strtotime(@$ev['time']['local'])<($order->warehouse_out_time-86400)){
                            	continue;
                            }
                            if($order->order_status=='7'){
                                $order->order_status='8';
                                $order->pick_up_time=time();
                                $order->save();
                                self::sendmail($order);
//                                 self::saveoutevents($order, strtotime(@$ev['time']['local']));
                            }
                            $route=Route::checkAndSave(array(
                                'network_code'=>$network_code,
                                'tracking_no'=>$row['tracking_no'],
                                'time'=>strtotime(@$ev['time']['local']),
                                'location'=>@$ev['location']['city'].','.@$ev['location']['state'].','.@$ev['location']['country'],
                                'description'=>@$ev['status']['name'],
                            ));
                            if(!is_null($route) && $ev['status']['name']=='Arrived into the US'){
                                $qty=Orderpackage::find('order_id=?',$order->order_id)->getSum('quantity');
                                $tring=new Tracking();
                                $tring->changeProps(array(
                                    'order_id'=>$order->order_id,
                                	'customer_id'=>$order->customer_id,
                                    'far_no'=>$order->far_no,
                                    'tracking_code'=>'S_TH_IN',
                                    'timezone'=>'-5',
                                    'confirm_flag'=>1,
                                    'trace_time'=>strtotime(@$ev['time']['local']),
                                    'location'=>'USA',
                                    'quantity'=>$qty,
                                    'trace_desc_cn'=>Tracking::$trace_code_cn['S_TH_IN'],
                                    'trace_desc_en'=>'Arrived into the US',
                                    'route_id'=>$route->id
                                ));
                                $tring->save();
                            }
                            if (!is_null($route) && $ev['status']['name']<>'Arrived into the US'){
                                $route->generateTrace($lastTime,$order);
                            }
                        }
                    }
                }
            }
            sleep(3);
        }
        exit;
    }
    /**
     * @todo   跟踪中美专线IB头程轨迹
     * @author stt
     * @since  2020-09-02
     * @link   #82148
     */
    function actionneweUsRoute(){
    	ini_set('max_execution_time', '0');
    	set_time_limit(0);
    	ini_set('default_socket_timeout', '20');
    	$select=Order::find("order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    	while ($row=$select->fetchRow()){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if(!empty($order->ems_order_id)){
    			continue;
    		}
    		if (!empty($row['channel_id']) && !empty($row['tracking_no'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
    			$rou=Route::find('tracking_no = ? and network_code = "UPS"',$row['tracking_no'])->order('id desc')->getOne();
    			if(!$rou->isNewRecord()){
    				QLog::log('description :'.$rou->description );
    				if($rou->description != 'Order Processed: Ready for UPS' && $rou->description != 'Order Processed: In Transit to UPS'){
    					continue;
    				}
    			}
    			$lasttime=0;
    			if (count($trackings)) foreach ($trackings as $tr){
    				if ($tr->timezone==-19){
    					continue;
    				}
    				if ($tr->trace_time){
    					$lasttime =$tr->trace_time + (8-$tr->timezone )*3600;
    				}
    			}
    			$network_code=$channel->network_code;
    			if ($network_code =='US-FY'){
    				$url="http://us.far800.com/api/events?orderNumber=".$row['tracking_no'];
    				$header=array(
    					'ClientKey:lu8zReRcanz8fXNCqMVC',
    					'ClientSecret:GpOgGDkaohIE3EiH6QXx'
    				);
    				$return=Helper_Curl::get1($url,$header);
    				QLog::log($row['tracking_no'].'ibtracking:'.$return);
    				
    				$return=json_decode($return,true);
    				foreach ($return as $r){
    					$events=$r['events'];
    					$data=array();
    					foreach ($events as $e){
    						$data[]=$e['event_time'];
    					}
    					array_multisort($data,SORT_ASC,$events);
    					foreach ($events as $ev){
    						if($ev['event_content']=='Received data'){
    							continue;
    						}
    						if(!empty($order->warehouse_out_time) && strtotime(@$ev['event_time'])<($order->warehouse_out_time-86400)){
    							continue;
    						}
    						if($order->order_status=='7'){
    							$order->order_status='8';
    							$order->pick_up_time=time();
    							$order->save();
    							self::sendmail($order);
    							//                                 self::saveoutevents($order, strtotime(@$ev['time']['local']));
    						}
    						$eustime_zone = Eustimezone::find('event_location=?',@$ev['event_loaction'])->getOne();
    						if ($eustime_zone->isNewRecord()){
    							$time_zone = '8';
    						}else{
    							$time_zone = $eustime_zone->time_zone;
    						}
    						$route=Route::checkAndSave(array(
    							'network_code'=>$network_code,
    							'tracking_no'=>$row['tracking_no'],
    							'time'=>strtotime(@$ev['event_time']),
    							'time_zone'=>$time_zone,
    							'location'=>@$ev['event_loaction'],
    							'description'=>@$ev['event_content'],
    						));
    						if (!is_null($route)){
    							$route->generateTrace($lasttime,$order);
    						}
    					}
    				}
    			}
    		}
    		sleep(3);
    	}
    	exit;
    }
    				
    /*
     * 麦链头程轨迹
     */
    function actionMltRoute(){
        set_time_limit(0);
        $select=Order::find("service_code='EUUS-FY' and order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
        $select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
        $gz=array('Shipment Picked up','In Transit','Export Customs Clearance Completed','Export Customs Clearance Exception: Inspection',
            'Export Customs Clearance Failure Will Return','In Transit to Airport','Scheduled to Take off','Departed Facility in','Flight Exception: Delaying',
            'Arrived at Sort Facility','Customs Status Updated','Transferred through');
        while ($row=$select->fetchRow()){
            $order=Order::find('order_id =?',$row['order_id'])->getOne();
            if (!empty($row['channel_id']) && !empty($row['tracking_no'])){
                $channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
                $trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
                $route=Route::find('tracking_no =? and network_code in (?)',$row['tracking_no'],array('DHL','DHLE','FEDEX'))->order('id desc')->getOne();;
                if(!$route->isNewRecord()){
                    if($route->description != 'Shipment information received'){
                        continue;
                    }
                }
                // 求最晚时间并转换为utc+8
                $lastTime=0;
                if (count($trackings)) foreach ($trackings as $tr){
                    if ($tr->timezone==-19){
                        continue;
                    }
                    if ($tr->trace_time){
                        $lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
                    }
                }
                $network_code=$channel->network_code;
                $tnetwork_code=$channel->trace_network_code;
                if ($network_code =='YWML' || $tnetwork_code=="YWML"){
                    $url="http://47.107.46.136:8086/xms/services/order?wsdl";
                    $array=array(
                        'userToken'=>'a43fb2769566442291622d8c6e6d8b5c',
                        'trackingNo'=>$row['tracking_no'],
//                         'orderNo'=>$order->ali_order_no
                    );
                    $client=new SoapClient($url, array ('encoding' => 'UTF-8' ));
                    $str=$client->__soapCall('getTrack', $array);
                    $str=get_object_vars($str);
                    if($str['success']==1 || $str['success']){
                        $trace=get_object_vars($str['trace']);
                        if(count($trace['sPaths'])==1){
                            $i=0;
                            $spath=get_object_vars($trace['sPaths']);
                            $location=$spath['pathAddr'];
                            if(strpos($location, ',')){
                                $location=explode(',', $location);
                                if(strlen(@$location[1])==2){
                                    foreach ($gz as $g){
                                        if(strtoupper($spath['pathInfo'])==strtoupper($g)){
                                            $i++;
                                        }
                                    }
                                    if($i == 0){
                                        continue;
                                    }
                                    $time_zone=substr($spath['pathTime'], 19,3);
                                    if($time_zone<0){
                                    	$time_zone='-'.abs($time_zone);
                                    }else {
                                    	$time_zone=abs($time_zone);
                                    }
                                	$time = strtotime(substr($spath['pathTime'], 0,19))+(8-$time_zone)*3600;
    								if(!empty($order->warehouse_out_time) && $time<$order->warehouse_out_time){
    									continue;
    								}
                                    if($order->order_status=='7'){
                                        $order->order_status='8';
                                        $order->pick_up_time=time();
                                        $order->save();
//                                         self::saveoutevents($order, strtotime(substr($spath['pathTime'], 0,19)));
                                        self::sendmail($order);
                                    }
                                    
                                    $r=Route::checkAndSave(array(
                                        'network_code'=>'YWML',
                                        'tracking_no'=>$row['tracking_no'],
                                        'time'=>strtotime(substr($spath['pathTime'], 0,19)),
                                        'location'=>@$location[0].',,'.@$location[1],
                                        'description'=>$spath['pathInfo'],
                                        'time_zone'=>$time_zone,
                                    ));
                                    if (!is_null($r)){
                                        $r->generateTrace($lastTime,$order);
                                    }
                                }else {
                                    continue;
                                }
                            }else {
                                continue;
                            }
                        }else {
                            foreach ($trace['sPaths'] as $spaths){
                                $i=0;
                                $spath=get_object_vars($spaths);
                                $location=$spath['pathAddr'];
                                if(strpos($location, ',')){
                                    $location=explode(',', $location);
                                    if(strlen(@$location[1])==2){
                                        foreach ($gz as $g){
                                            if(strtoupper($spath['pathInfo'])==strtoupper($g)){
                                                $i++;
                                            }
                                        }
                                        if($i == 0){
                                            continue;
                                        }
                                        $time_zone=substr($spath['pathTime'], 19,3);
                                        if($time_zone<0){
                                        	$time_zone='-'.abs($time_zone);
                                        }else {
                                        	$time_zone=abs($time_zone);
                                        }
                                        $time = strtotime(substr($spath['pathTime'], 0,19))+(8-$time_zone)*3600;
                                        if(!empty($order->warehouse_out_time) && $time<$order->warehouse_out_time){
                                        	continue;
                                        }
                                        if($order->order_status=='7'){
                                            $order->order_status='8';
                                            $order->pick_up_time=time();
                                            $order->save();
                                        }
                                        $r=Route::checkAndSave(array(
                                            'network_code'=>'YWML',
                                            'tracking_no'=>$row['tracking_no'],
                                            'time'=>strtotime(substr($spath['pathTime'], 0,19)),
                                            'location'=>@$location[0].',,'.@$location[1],
                                            'description'=>$spath['pathInfo'],
                                            'time_zone'=>$time_zone,
                                        ));
                                        if (!is_null($r)){
                                            $r->generateTrace($lastTime,$order);
                                        }
                                    }else {
                                        continue;
                                    }
                                }else {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }
        exit;
    }
    
    /**
     * 跟踪麦链的5个DHL末端轨迹（每天本地跑一次）
     */
    function actionMlDhlRoute(){
        set_time_limit(0);
        $select=Order::find("service_code in (?) and ali_order_no='ALS00202279523' ",array('Express_Standard_Global','EUUS-FY','US-FY','ProtectiveEquipment-FY'));
        
        $select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
        while ($row=$select->fetchRow()){
            $order=Order::find('order_id =?',$row['order_id'])->getOne();
            if (!empty($row['channel_id']) && !empty($row['tracking_no'])){
                $channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
                $trackings=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1"',$row['order_id'])->getAll();
                $lastTime=0;
                if (count($trackings)) foreach ($trackings as $tr){
                    if ($tr->timezone==-19){
                        continue;
                    }
                    if ($tr->trace_time){
                        $lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
                    }
                }
                $network_code=$channel->network_code;
                $tnetwork_code=$channel->trace_network_code;
                if ($network_code =='DHL' || $tnetwork_code == 'DHL'){
                    $url = "https://www.dhl.com/shipmentTracking?AWB=".$row['tracking_no'];
                    $return = Helper_Curl::get($url);
                    QLog::log('MlDhlRoute'.$row['tracking_no'].':'.$return);
                    print_r($return);exit;
                    $return = json_decode($return, true);
                   
                    if(isset($return['results'][0]['checkpoints'])){
                        $checkpoints=array_reverse($return['results'][0]['checkpoints']);
                        foreach ($checkpoints as $event){
                            $date=$event['date'];
                            $hour=$event['time'];
                            $location=@$event['location'];
                            $location=explode("-", $location);
                            $country=@$location[1];
                            if(strpos($country, ',')){
                                $loca=$country;
                                $loca=explode(",", $loca);
                                $country=$loca[0];
                            }
                            if(!empty($order->warehouse_out_time) && strtotime($date.$hour)<($order->warehouse_out_time-86400)){
                            	continue;
                            }
                            if($order->order_status=='7' && $event['description']<>'Shipment information received'){
                            	$order->order_status='8';
                            	$order->pick_up_time=time();
                            	$order->save();
//                             	self::saveoutevents($order, strtotime($date.$hour));
                            	self::sendmail($order);
                            }
                            if(strpos($event['description'], 'Delivered')!==false && empty($country)){
                                $country=$order->consignee_country_code;
                            }
                            $r=Route::checkAndSave(array(
                                'network_code'=>'DHL',
                                'tracking_no'=>$row['tracking_no'],
                                'time'=>strtotime($date.$hour),
                                'location'=>@$location[0].','.$country,
                                'description'=>$event['description'],
                            ));
                            if (!is_null($r) && $event['description']<>'Shipment information received'){
                                $r->generateTrace($lastTime,$order);
                            }
                        }
                        if(isset($return['results'][0]['edd']['date']) && !empty($return['results'][0]['edd']['date']) && isset($return['results'][0]['edd']['label']) && $return['results'][0]['edd']['label']=='Estimated Delivery'){
                            $time=strtotime($return['results'][0]['edd']['date']);
                            $track=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and trace_desc_cn like ?',$row['order_id'],'%'.date('Y-m-d',$time).'%')->getOne();
                            $track2=Tracking::find('order_id =? and tracking_code="F_DELIVERY_5044" and yflag="1"',$row['order_id'])->getOne();
                            $track3=Tracking::find('order_id =? and tracking_code="S_DELIVERY_SIGNED"',$row['order_id'])->getOne();
                            $tra=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1" and confirm_flag<>"2"',$order->order_id)->order('tracking_id desc')->getOne();
                            if($track->isNewRecord() && $track3->isNewRecord()){
                                $order->present_time=$time;
                                $order->save();
                                if($tra->location){
                                    $track->changeProps(array(
                                        'order_id'=>$order->order_id,
                                    	'customer_id'=>$order->customer_id,
                                        'far_no'=>$order->far_no,
                                        'tracking_code'=>'F_DELIVERY_5044',
                                        'timezone'=>'8',
                                        'confirm_flag'=>'1',
                                        'trace_time'=>time(),
                                        'location'=>$tra->location,
                                        'quantity'=>$tra->quantity,
                                        'trace_desc_cn'=>'预计派送时间（当地）:'.date('Y-m-d',$time),
                                    	'trace_desc_en'=>'Scheduled Delivery Time (Local):'.date('Y-m-d',$time),
                                        'yflag'=>'1'
                                    ));
                                    $track->save();
                                    if(!$track2->isNewRecord()){
                                        $track->trace_desc_cn='预计派送时间（当地） 更新:'.date('Y-m-d',$time);
                                        $track->trace_desc_en='Scheduled Delivery Time Updated (Local):'.date('Y-m-d',$time);
                                        $track->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        exit;
    }
    
    /**
     * 跟踪麦链SH-DHL-MX末端轨迹
     */
    function actionmxroute(){
    	set_time_limit(0);
    	$select=Order::find("service_code='EUUS-FY' and order_status in (?)",array(Order::STATUS_OUT,Order::STATUS_EXTRACTED));
    	$select= $select->setColumns('order_id,tracking_no,channel_id')->all()->getQueryHandle();
    
    	while ($row=$select->fetchRow()){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			$trackings=Tracking::find('order_id =?',$row['order_id'])->getAll();
    
    			// 求最晚时间并转换为utc+8
//     			$evt=Event::find('order_id =? ',$row['order_id'])->order('event_time desc') ->getOne();
//     			$lastTime=strtotime(date("Y-m-d 2:25:00",$evt->event_time))+86400*2;	//事件最后的时间+2天
    			$lastTime=0;
    			if (count($trackings)) foreach ($trackings as $tr){
    				if ($tr->timezone==-19){
    					continue;
    				}
    				if ($tr->trace_time){
    					$lastTime =$tr->trace_time + (8-$tr->timezone )*3600;
    				}
    			}
    			$network_code=$channel->network_code;
    			$tnetwork_code=$channel->trace_network_code;
    			if ($network_code =='DHLE' || $tnetwork_code == 'DHLE'){
    				$reslut=Helper_Curl::get("https://api.dhlecommerce.dhl.com/rest/v1/OAuth/AccessToken?clientId=LTIxNDEyOTEwNzM=&password=MjAzMDI5MTU");
    				$reslut=json_decode($reslut,true);
    				if(isset($reslut['accessTokenResponse']['token'])){
    					$arr=array(
    						'trackItemRequest'=>array(
    							'trackingReferenceNumber'=>array($row['tracking_no']),
    							'messageLanguage'=>'zh_EN',
    							'messageVersion'=>'1.1',
    							'token'=>$reslut['accessTokenResponse']['token']
    						),
    					);
    					$header=array(
    						'Content-Type:application/json'
    					);
    					$url='https://api.dhlecommerce.dhl.com/rest/v2/Tracking';
    					$return=Helper_Curl::post($url, json_encode($arr),$header);
    					QLog::log('mxroute'.$row['tracking_no'].':'.$return);
    					$return=json_decode($return,true);
    					if(isset($return['trackItemResponse']['items'])){
    						$items=$return['trackItemResponse']['items'];
    						foreach ($items as $item){
    							$events=$item['events'];
    							krsort($events);
    							foreach ($events as $e){
    								if(!empty($order->warehouse_out_time) && strtotime($e['timestamp'])<($order->warehouse_out_time-86400)){
    									continue;
    								}
    							    if($e['description']<>'Shipment data received by 3rd party'){
    							        if($order->order_status=='7'){
    							            $order->order_status='8';
    							            $order->pick_up_time=time();
    							            $order->save();
//     							            self::saveoutevents($order, strtotime($e['timestamp']));
											self::sendmail($order);
    							        }
    							    }
    								$r=Route::checkAndSave(array(
    									'network_code'=>'DHLE',
    									'tracking_no'=>$row['tracking_no'],
    									'time'=>strtotime($e['timestamp']),
    									'location'=>@$e['address']['city'].','.@$e['address']['state'].','.@$e['address']['countryCode'],
    									'description'=>$e['description'],
    								    'code'=>$e['status']
    								));
    								if (!is_null($r)){
    									$r->generateTrace($lastTime,$order);
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	exit;
    }
    
    
    function actionexport(){
    	set_time_limit(0);
    	ini_set('memory_limit', '-1');
    	//创建一个excel空文件，文件名 应付统计
    	Helper_ExcelX::startWriter ( 'cope_with'  );
    	$header = array (
    		'部门	 ',
    		'发件日	 ',
    		'委托人	 ',
    		'客户	 ',
    		'运单号	 ',
    		'原单号	 ',
    		'收付类型',
    		'金额	 ',
    		'发票号	 ',
    		'账单抬头',
    		'付款方式',
    		'网络	 ',
    		'服务类别',
    		'客重	 ',
    		'客户公开价',
    		'结算重	 ',
    		'产品公开价',
    		'产品	 ',
    		'原始包装',
    		'类别	 ',
    		'包装	 ',
    		'拒付状态',
    		'合同天数',
    		'凭证号	 ',
    		'总部凭证号',
    		'销账日期',
    		'登帐日	 ',
    		'签收日	 ',
    		'目的地	 ',
    		'分区	 ',
    		'取件员	 ',
    		'销售员	 ',
    		'BAF	 ',
    		'来源	 ',
    		'开票日期',
    		'创建日期',
    		'创建人	 ',
    		'账期	 ',
    		'收件人	 ',
    		'公司	 ',
    		'寄件人	 ',
    	);
    	//写入表头 内容为$header,addRow为写入内容
    	Helper_ExcelX::addRow ($header);
    	//循环写入数据，以每200条为节点
    	$tmp_order = array ();
    	$fee_select = Fee::find('fee_type=1')->setColumns('fee_id')->asArray()->getAll();
    	//echo count($fee_select);exit;
    	//foreach($fee_select as $k => $v){
    		//$tmp_order[] = $v;
    		//写入数据的函数封装
    	$this->paymentExportAddRow($fee_select);
    		//重置数组以便循环插入时不重复
    		//$tmp_order = array ();
    	//}
    	//写入结束
    	Helper_ExcelX::closeWriter ();
    	exit ();
    }
    
    function actionPayexport(){
    	set_time_limit(0);
    	ini_set('memory_limit', '-1');
    	//创建一个excel空文件，文件名 应付统计
    	Helper_ExcelX::startWriter ( 'cope_with'  );
    	$header = array (
    		'部门	 ',
    		'发件日	 ',
    		'委托人	 ',
    		'客户	 ',
    		'运单号	 ',
    		'原单号	 ',
    		'收付类型',
    		'金额	 ',
    		'发票号	 ',
    		'账单抬头',
    		'付款方式',
    		'网络	 ',
    		'服务类别',
    		'客重	 ',
    		'客户公开价',
    		'结算重	 ',
    		'产品公开价',
    		'产品	 ',
    		'原始包装',
    		'类别	 ',
    		'包装	 ',
    		'拒付状态',
    		'合同天数',
    		'凭证号	 ',
    		'总部凭证号',
    		'销账日期',
    		'登帐日	 ',
    		'签收日	 ',
    		'目的地	 ',
    		'分区	 ',
    		'取件员	 ',
    		'销售员	 ',
    		'BAF	 ',
    		'来源	 ',
    		'开票日期',
    		'创建日期',
    		'创建人	 ',
    		'账期	 ',
    		'收件人	 ',
    		'公司	 ',
    		'寄件人	 ',
    	);
    	//写入表头 内容为$header,addRow为写入内容
    	Helper_ExcelX::addRow ($header);
    	//循环写入数据，以每200条为节点
    	$tmp_order = array ();
    	// 		$order_ids = Helper_Array::getCols(Order::find ('order_status=9 and order_id>=1 and order_id<10000 and ali_testing_order!=1')->setColumns('order_id')->asArray()->order ( 'create_time desc' )->getAll (),'order_id');
    	// 		$fee_select = Fee::find('order_id in(?) and fee_type=1',$order_ids)->getAll();
    	$fee_select = Fee::find('fee_type=2')->setColumns('fee_id')->asArray()->getAll();
    	//echo count($fee_select);exit;
    	//$fee_select = Fee::find('order_id in(?) and fee_type=2',$order_ids)->setColumns('fee_id')->asArray()->getAll();
    	//echo count($fee_select);exit;
    	//foreach($fee_select as $k => $v){
    	//$tmp_order[] = $v;
    	//写入数据的函数封装
    	$this->paymentAddRow2($fee_select);
    	//重置数组以便循环插入时不重复
    	//$tmp_order = array ();
    	//}
    	//写入结束
    	Helper_ExcelX::closeWriter ();
    	exit ();
    }
    
    function paymentAddRow2($fee_select){
    	foreach ($fee_select as $fee){
    		$value = Fee::find('fee_id=?',$fee['fee_id'])->getOne();
    		
    		if($value->remark=='no'){
    			continue;
    		}
    		$order = Order::find ('order_id=?',$value->order_id)->getOne ();
    		$product=Product::find('product_name=?',$order->service_code)->getOne();
    		$channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
    		$channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,$order->record_order_date,$order->record_order_date)->getOne();
    		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $channelcostppr_c_t->partition_manage_id, $order->consignee_country_code )->getAll ();
    		if (count ( $partition ) == 0) {
    			$partition_code = '';
    		}
    		$partition_code = '';
    		$partition_code2 = '';
    		foreach ( $partition as $p ) {
    			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $order->consignee_postal_code ) ) == $order->consignee_postal_code || substr ( $order->consignee_postal_code, 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
    				$partition_code = $p->partition_code;
    			}
    			if (! $p->postal_code) {
    				$partition_code2 = $p->partition_code;
    			}
    		}
    		if (! $partition_code) {
    			$partition_code = $partition_code2;
    		}
    		$pickup = PickUpMember::find('wechat_id=?',$order->wechat_id)->getOne();
    		$network_c_t=Network::find("network_code=? ",$order->channel->network_code)->getOne();
    		$rate = 0;
    		if ($order->service_code != 'EMS-FY') {
    			$network = Networkfuel::find ( 'network_id=? and effective_date<=? and fail_date>=?', $network_c_t->network_id, $order->record_order_date, $order->record_order_date )->getOne ();
    			if (! $network->isNewRecord ()) {
    				$rate = $network->rate;
    			}
    		}
    		
    		//燃油折扣
    		if ($channelcost_c_t->fuel_surcharge_dicount > 0 && $rate > 0) {
    			$rate = bcmul($rate , $channelcost_c_t->fuel_surcharge_dicount,2);
    		}
    		$sheet =array(
    			$order->department->department_name,//部门
    			$order->record_order_date?date('Y-m-d',$order->record_order_date):'',//发件日
    			'阿里国际站',//委托人
    			'阿里国际站',//客户
    			$order->ali_order_no,//运单号
    			'',//原单号
    			'成本',//收付类型
    			$value->amount.$value->currency,//金额
    			$value->invoice_no,//发票号
    			$value->waybill_title,//账单抬头
    			'',//付款方式
    			$order->channel->network_code,//网络
    			'',//服务类别
    			$order->weight_label,//客重
    			'',//客户公开价
    			$order->weight_cost_out,//结算重
    			'',//产品公开价
    			$order->service_product->product_chinese_name,//产品
    			'BOX',//原始包装
    			'',//类别
    			'BOX',//包装
    			'',//拒付状态
    			'',//合同天数
    			$value->voucher_no,//凭证号
    			'',//总部凭证号
    			$value->voucher_time?date('Y-m-d',$value->voucher_time):'',//销账日期
    			$value->account_date?date('Y-m-d',$value->account_date):'',//登帐日
    			$order->delivery_time?date('Y-m-d',$order->delivery_time):'',//签收日
    			$order->consignee_country_code,//目的地
    			$partition_code,//分区
    			$pickup->name,//取件员
    			'',//销售员
    			$rate,//BAF
    			'',//来源
    			$value->invoice_time?date('Y-m-d',$value->invoice_time):'',//开票日期
    			$value->create_time?date('Y-m-d',$value->create_time):'',//创建日期
    			'阿里国际站',//创建人
    			'',//账期
    			$order->consignee_name1,//收件人
    			$order->consignee_name2,//公司
    			$order->sender_name1,//寄件人
    		);
    		Helper_ExcelX::addRow ( $sheet );
    	}
    }
    
    
    /**
     * @todo 应付统计内容具体写入函数
     * @author 吴开龙
     * @since May 10th 2020
     * @return array
     * @link #79779
     * @ps 代码是系统原来的代码，所以代码内部没有注释，整体功能是为excel写入数据
     */
    function paymentExportAddRow($fee_select){
    	
    	foreach ($fee_select as $fee){
    		//print_r($fee);exit;
    		$value = Fee::find('fee_id=?',$fee['fee_id'])->getOne();
    		$order = Order::find ('order_id =?',$value->order_id)->order ( 'create_time desc' )->getOne ();
    		if($order->ali_order_no){
	    		$product=Product::find('product_name=?',$order->service_code)->getOne();
	    		$channelcost_c_t=ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();
	    		$channelcostppr_c_t=Channelcostppr::find("channel_cost_id=? and effective_time<=? and invalid_time>=?",$channelcost_c_t->channel_cost_id,$order->warehouse_confirm_time,$order->warehouse_confirm_time)->getOne();
	    		$partition_code = '';
	    		$partition_code2 = '';
	    		//获取产品中偏派-价格-分区
	    		$product_p_p_r = Productppr::find ( 'product_id=? and effective_time <=? and invalid_time>=?', $product->product_id, $order->warehouse_confirm_time, $order->warehouse_confirm_time )->getOne ();
	    		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $product_p_p_r->partition_manage_id, $order->consignee_country_code )->getAll ();
	    		foreach ( $partition as $p ) {
	    			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $order->consignee_postal_code ) ) == $order->consignee_postal_code || substr ( $order->consignee_postal_code, 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
	    				$partition_code = $p->partition_code;
	    			}
	    			if (! $p->postal_code) {
	    				$partition_code2 = $p->partition_code;
	    			}
	    		}
	    		if (! $partition_code) {
	    			$partition_code = $partition_code2;
	    		}
	    		$pickup = PickUpMember::find('wechat_id=?',$order->wechat_id)->getOne();
	    		$network_c_t=Network::find("network_code=? ",$order->channel->network_code)->getOne();
	    		$rate = 0;
	    		if ($order->service_code != 'EMS-FY') {
	    			$productfuel = Productfuel::find ( 'product_id=? and effective_date<=? and fail_date>=?', $product->product_id, $order->warehouse_confirm_time, $order->warehouse_confirm_time )->getOne ();
	    			if (! $productfuel->isNewRecord ()) {
	    				$rate = $productfuel->rate;
	    			}
	    		}
	    		$sheet =array(
	    			$order->department->department_name,//部门
	    			$order->record_order_date?date('Y-m-d',$order->record_order_date):'',//发件日
	    			'阿里国际站',//委托人
	    			'阿里国际站',//客户
	    			$order->ali_order_no,//运单号
	    			'',//原单号
	    			'收入',//收付类型
	    			$value->amount.$value->currency,//金额
	    			$value->invoice_no,//发票号
	    			$value->waybill_title,//账单抬头
	    			'',//付款方式
	    			$order->channel->network_code,//网络
	    			'',//服务类别
	    			$order->weight_label,//客重
	    			'',//客户公开价
	    			$order->weight_cost_out,//结算重
	    			'',//产品公开价
	    			$order->service_product->product_chinese_name,//产品
	    			'BOX',//原始包装
	    			'',//类别
	    			'BOX',//包装
	    			'',//拒付状态
	    			'',//合同天数
	    			$value->voucher_no,//凭证号
	    			'',//总部凭证号
	    			$value->voucher_time?date('Y-m-d',$value->voucher_time):'',//销账日期
	    			$value->account_date?date('Y-m-d',$value->account_date):'',//登帐日
	    			$order->delivery_time?date('Y-m-d',$order->delivery_time):'',//签收日
	    			$order->consignee_country_code,//目的地
	    			$partition_code,//分区
	    			$pickup->name,//取件员
	    			'',//销售员
	    			$rate,//BAF
	    			'',//来源
	    			$value->invoice_time?date('Y-m-d',$value->invoice_time):'',//开票日期
	    			$value->create_time?date('Y-m-d',$value->create_time):'',//创建日期
	    			'阿里国际站',//创建人
	    			'',//账期
	    			$order->consignee_name1,//收件人
	    			$order->consignee_name2,//公司
	    			$order->sender_name1,//寄件人
	    		);
	    		Helper_ExcelX::addRow ( $sheet );
    		}
    	}
    }
    
   	static function saveoutevents($order,$checkout_time){
    	//判断三个出库事件
    	$events_out=Event::find('order_id=? and event_code in ("WAREHOUSE_OUTBOUND","CARRIER_PICKUP")',$order->order_id)->getAll();
    	if(!count($events_out)){
    		//存入3个事件
    		$department=Department::find('department_id=?',$order->department_id)->getOne();
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
    		if($order->service_code == 'Express_Standard_Global'){
    			$location='深圳';
    		}
    		//出库事件
    		$outbound_event= new Event();
    		$outbound_event->changeProps(array(
    			'order_id'=>$order->order_id,
    			'customer_id'=>$order->customer_id,
    			'event_code'=>'WAREHOUSE_OUTBOUND',
    			'event_time'=>$checkout_time,
    			'event_location'=>$location,
    			'timezone'=>'8',
    			'confirm_flag'=>'1',
    			'operator'=>'系统'
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
    		if($order->service_code!='EMS-FY' && $order->service_code!='WIG-FY' && $order->service_code!='ePacket-FY'){
    			$pickup_event= new Event();
    			$pickup_event->changeProps(array(
    				'order_id'=>$order->order_id,
    				'customer_id'=>$order->customer_id,
    				'event_code'=>'CARRIER_PICKUP',
    				'event_time'=>$carrier_time,
    				'location'=>$location,
    				'event_location'=>$location,
    				'timezone'=>'8',
    				'confirm_flag'=>'1'
    			));
    			$pickup_event->save();
    			//承运商取件时间
    			$order->carrier_pick_time=$carrier_time;
    		}elseif ($order->service_code=='EMS-FY' || $order->service_code=='ePacket-FY'){
    			$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
    			$trace=new Tracking();
    			$trace->changeProps(array(
    				'order_id'=>$order->order_id,
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
    		//出库时间
    		$order->warehouse_out_time=$checkout_time;
    		$feeall=Fee::find('order_id = ?',$order->order_id)->getAll();
    		foreach ($feeall as $fee){
    			$fee->account_date=$checkout_time;
    			$fee->save();
    		}
    		$order->save();
    	}
    }
    /**
     * @todo   支付超时预警邮件 自动发送，定时任务
     * @author 吴开龙
     * @since  2020-06-02
     * @return Boolean
     * @link   #80102
     */
    function actionAutoSendMail(){
    	//取出支付时间为空，核查时间不为空的订单
    	$order = Order::find('payment_time is null and warehouse_confirm_time is not null and order_status="10" and auto_send_mail_stu != 2')->getAll();
    	foreach ($order as $o){
    		//不是阿里订单直接跳过
    		$str = substr($o->ali_order_no , 0 , 2);
    		if($str != 'AL'){
    			continue;
    		}
    		//发送失败直接跳过
    		$event = Event::find('order_id=?',$o->order_id)->getOne();
    		if($event->send_flag != 1){
    			continue;
    		}
    		//匹配邮件规则，未设置规则的直接跳过
    		$rule_choose = AutomaticEmailRule::find('product_id = ? and (tracking_code = ? || tracking_code = ?)',$o->service_product->product_id,'支付超时预警48H','支付超时预警24H')->getOne();
    		if($rule_choose->isNewRecord()){
    			continue;
    		}
    		//支付超时预警48H（支付超时定义：当前时间-订单核查时间的结果刚刚等于120小时）
    		if($o->auto_send_mail_stu == 0 && $rule_choose->tracking_code == '支付超时预警48H'){
    			if(time() - $o->warehouse_confirm_time >= 432000){
    				$return = self::autoSendMailOut($o,$rule_choose);
    				if($return){
    					$o->auto_send_mail_stu = 1;
    					$o->save();
    				}
    			}
    		}
    		//支付超时预警24H（支付超时定义：当前时间-订单核查时间的结果刚刚等于144小时）
    		if($o->auto_send_mail_stu == 1 && $rule_choose->tracking_code == '支付超时预警24H'){
	    		if(time() - $o->warehouse_confirm_time >= 518400){
	    			$return = self::autoSendMailOut($o,$rule_choose);
	    			if($return){
	    				$o->auto_send_mail_stu = 2;
	    				$o->save();
	    			}
	    		}
    		}
    	}
    }
    /**
     * @todo   支付超时预警邮件 发送邮件
     * @author 吴开龙
     * @since  2020-06-02
     * @return Boolean
     * @link   #80102
     */
    static function autoSendMailOut($order,$rule_choose){
    	$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
    	if(!$email_template->isNewRecord()){
    		$title = $email_template->template_title;
    		$email_info = $email_template->template_text;
    		
    		$postalbook = postalbook::find('code_word_two = ? and channel_id = ?',$order->consignee_country_code,$order->channel_id)->getOne();
    		$track = Controller_Product::getTracking($order);
    		//标题
    		$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
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
    		$template_title = preg_replace('/track1/',@$track[0], $template_title);
    		$template_title = preg_replace('/track2/',@$track[1], $template_title);
    		$template_title = preg_replace('/track3/',@$track[2], $template_title);
    		$deprtment_name = $order->department_id?$order->department->department_name:'';
    		$template_title = preg_replace('/warehouse/',$deprtment_name,$template_title);
    		//内容
    		$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
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
    		QLog::log($template_title);
    		QLog::log($template_info);
    		$title = nl2br($template_title);
    		$msg = nl2br($template_info);
    		$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
    		QLog::log($email_response);
    		if ($email_response == 'email_success') {
    			$order_log = new OrderLog ();
    			$order_log->order_id = $order->order_id;
    			$order_log->staff_name = '系统';
    			$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
    			$order_log->save ();
    			return true;
    		}
    	}
    	return false;
    }
    //订单状态由已出库变成已提取自动向客户发送邮件
    static function sendmail($order){
    	$rule_choose = AutomaticEmailRule::find('product_id = ? and tracking_code = ?',$order->service_product->product_id,'订单已提取')->getOne();
    	if(!$rule_choose->isNewRecord()){
    		$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
    		if(!$email_template->isNewRecord()){
    			$title = $email_template->template_title;
    			$email_info = $email_template->template_text;
    
    			$postalbook = postalbook::find('code_word_two = ? and channel_id = ?',$order->consignee_country_code,$order->channel_id)->getOne();
    			$track = Controller_Product::getTracking($order);
    			//标题
    			$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
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
    			$template_title = preg_replace('/track1/',@$track[0], $template_title);
    			$template_title = preg_replace('/track2/',@$track[1], $template_title);
    			$template_title = preg_replace('/track3/',@$track[2], $template_title);
    			$deprtment_name = $order->department_id?$order->department->department_name:'';
    			$template_title = preg_replace('/warehouse/',$deprtment_name,$template_title);
    			//内容
    			$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
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
    			QLog::log($template_title);
    			QLog::log($template_info);
    			$title = nl2br($template_title);
    			$msg = nl2br($template_info);
    			$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
    			QLog::log($email_response);
    			if ($email_response == 'email_success') {
    				$order_log = new OrderLog ();
    				$order_log->order_id = $order->order_id;
    				$order_log->staff_name = '系统';
    				$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
    				$order_log->save ();
    			}
    		}
    	}
    }
    
    static function getPostData($key,$postData)
    {
    	ksort($postData); //除sign参数外， 数组按字母排序
    	$strPara = http_build_query($postData);
    	$sign = md5($strPara.$key);//签名
    	$postData['sign'] = $sign;
    	return http_build_query($postData);
    }
    
    
    
    /*
     * xml转array
     */
    static function xml_to_array( $xml ){
    	$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    	if(preg_match_all($reg, $xml, $matches)){
    		$count = count($matches[0]);
    		$arr = array();
    		for($i = 0; $i < $count; $i++){
	    		$key = $matches[1][$i];
	    		$val = self::xml_to_array( $matches[2][$i] );  // 递归
	    		if(array_key_exists($key, $arr)){
		    		if(is_array($arr[$key])){
		    			if(!array_key_exists(0,$arr[$key])){
			    			$arr[$key] = array($arr[$key]);
			    		}
		    		}else{
		    			$arr[$key] = array($arr[$key]);
		    		}
	    			$arr[$key][] = $val;
	    		}else{
	    			$arr[$key] = $val;
	    		}
    		}
    		return $arr;
    	}else{
    		return $xml;
    	}
    }
    
    static function log($str){
    		QLog::log($str);
    		echo date('Ymd H:i').']'.$str."\n";
    }
    
    /**
     * 推送数据到新的出库表格里
     */
    function actionsyncpackage(){
    	set_time_limit(0);
    	$farpackages=Farpackage::find()->getAll();
    	foreach ($farpackages as $f){
    		$order=Order::find('order_id=? and order_status=5',$f->order_id)->getOne();
    		if(!$order->isNewRecord()){
    			continue;
    		}
    		$faroutpackge=new Faroutpackage();
    		$faroutpackge->order_id=$f->order_id;
    		$faroutpackge->quantity_out=$f->quantity;
    		if($f->weight_out>0){
    			$faroutpackge->length_out=$f->length_out;
    			$faroutpackge->width_out=$f->width_out;
    			$faroutpackge->height_out=$f->height_out;
    			$faroutpackge->weight_out=$f->weight_out;
    		}else{
    			$faroutpackge->length_out=$f->length;
    			$faroutpackge->width_out=$f->width;
    			$faroutpackge->height_out=$f->height;
    			$faroutpackge->weight_out=$f->weight;
    		}
    		$faroutpackge->save();
    	}
    	exit;
    }
    
    /**
     * 测试拆分阿里推送过来的数据
     */
    function actiontestapi(){
    	$url='localhost/AliExpress/Code/api/orderbooking';
    	$requestBody='{"bookingOrderDTO":"{\"aliOrderNo\":\"ALS0020190824\",\"consignee\":{\"city\":\"North hudson\",\"countryCode\":\"US\",\"mobile\":\"7153772133\",\"name1\":\"Ceme-Tube LLC\",\"name2\":\"Ceme-Tube LLC\",\"postalCode\":\"54016\",\"stateRegionCode\":\"Wisconsin\",\"street1\":\"579 Schommer Dr\"},\"consignor\":{\"city\":\"苏州市\",\"countryCode\":\"CN\",\"email\":\"eric.hwang@wonsten.com\",\"mobile\":\"18112752189\",\"name1\":\"王欣跃\",\"postalCode\":\"215600\",\"stateRegionCode\":\"江苏省\",\"street1\":\"乐余镇兆丰开发区双丰路5号\"},\"customsDeclaration\":{\"currencyCode\":\"USD\",\"declarationType\":\"QT\",\"totalAmount\":1000},\"needInsurance\":false,\"needPickUp\":false,\"packages\":[{\"height\":40,\"length\":67,\"packageType\":\"BOX\",\"quantity\":1,\"unit\":\"CM\",\"weight\":40,\"weightUnit\":\"KG\",\"width\":40}],\"products\":[{\"declarationPrice\":1000,\"hasBattery\":false,\"hsCode\":\"9617001100\",\"productName\":\"撕碎机筛网\",\"productNameEn\":\"Screen for shredder \",\"productQuantity\":1,\"productUnit\":\"pcs\"}],\"referenceNo\":\"803678522198,803678522199\",\"serviceCode\":\"Express_Standard_Global\",\"warehouse\":{\"code\":\"ASP_FAR_SH_PD\",\"name\":\"泛远上海浦东仓\"}}","sign":"302c02147ab07e89a99b19c4fe7f79a1ec9d918a2989f3d4021439822da781fe778c67b5b07caffb00b15b8a7e73"}';
    	$r=Helper_Curl::post($url, $requestBody);
    	dump($r);
    	exit;
    }
    
    /**
     * 将阿里老订单里的运单号拆分到新表中
     */
    function actionsplitreference(){
    	set_time_limit(0);
    	$orders=Order::find('order_id <1452')->getAll();
    	foreach ($orders as $order){
    	  	if(strlen($order->reference_no)){
	        	$references=explode(",", $order->reference_no);
	        	foreach ($references as $r){
	        		$alireference=new Alireference();
	        		$alireference->order_id=$order->order_id;
	        		$alireference->reference_no=$r;
	        		$alireference->save();
	        	}
        	}
    	}
    	exit;
    }
    
    /**
     * 处理老数据的重量问题
     */
    function actioncalcweight(){
    	set_time_limit(0);
    	$orders=Order::find('ali_testing_order !="1" and order_status not in ("1","2","3","11") and order_id <1000')->getAll();
    	foreach ($orders as $order){
    		$far_in_packages=Farpackage::find('order_id=?',$order->order_id)->getAll();
    		//应收实重
    		if(!$order->weight_actual_in){
    			if(count($far_in_packages)){
    				$weight_actual_in=0;
    				foreach ($far_in_packages as $far_in_actual){
    					$weight_actual_in+=$far_in_actual->weight*$far_in_actual->quantity;
    				}
    				if($weight_actual_in>0){
    					$order->weight_actual_in=$weight_actual_in;
    					$order->save();
    				}
    			} 
    		}
    		//应收计费重
    		if(!$order->weight_income_in){
    			if(count($far_in_packages)){
    				 $weight_income_in=0;
    				 foreach ($far_in_packages as $far_in_income){
    				 	$volumn_weight=$far_in_income->length*$far_in_income->width*$far_in_income->height/5000;
    				 	$volumn=$volumn_weight>$far_in_income->weight;
    				 	if($volumn){
    				 		$weight_income_in+=$volumn_weight>20?ceil($volumn_weight)*$far_in_income->quantity:ceil($volumn_weight/0.5)*0.5*$far_in_income->quantity;
    				 	}else{
    				 		$weight_income_in+=$far_in_income->weight>20?ceil($far_in_income->weight)*$far_in_income->quantity:ceil($far_in_income->weight/0.5)*0.5*$far_in_income->quantity;
    				 	}
    				 	
    				 }
    				 if($weight_income_in>0){
    				 	$order->weight_income_in=$weight_income_in>20?ceil($weight_income_in):$weight_income_in;
    				 	$order->save();
    				 }
    			} 
    		}
    	}
    	exit;
    }
    
    /**
     * 自动计算老的应收费用
     */
    function actioncalcfee(){
    	set_time_limit(0);
    	$fees=Fee::find('fee_type="1" and amount is null and fee_item_name="基础运费"')->getAll();
    	foreach ($fees as $fee){
    		$order=Order::find("order_id=?",$fee->order_id)->getOne();
    		if($order->isNewRecord() || $order->order_status =='2' ||  $order->order_status =='3'){
    			continue;
    		}
    		//基础运费
    		$tracking_fee=0;
    		$partition_code='';
    		$partition_code2='';
    		$partition=Partition::find('partition_manage_id=1 and country_code_two=?',$order->consignee_country_code)->getAll();
    		foreach ($partition as $p){
    		    if(trim($p->postal_code) && (substr($p->postal_code, 0,strlen($order->consignee_postal_code))==$order->consignee_postal_code || substr($order->consignee_postal_code, 0,strlen($p->postal_code))==$p->postal_code)){
    		        $partition_code=$p->partition_code;
    		    }
    		    if(!$p->postal_code){
    		        $partition_code2=$p->partition_code;
    		    }
    		}
    		if(!$partition_code){
    		    $partition_code=$partition_code2;
    		}
    		//获取价格
    		$price=Price::find('price_manage_id=3 and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?',$partition_code,"BOX",$order->weight_income_in,$order->weight_income_in)->getOne();
    		if($price->isNewRecord()){
    			continue;
    		}
    		if($price->additional_weight>0){
    			if(($order->weight_income_in-$price->first_weight)>0){
    				$tracking_fee=(ceil(($order->weight_income_in-$price->first_weight)/$price->additional_weight)*$price->additional_fee)+$price->first_fee;
    			}else{
    				$tracking_fee=$price->first_fee;
    			}
    		}else{
    			if(($order->weight_income_in-$price->first_weight)>0){
    				$tracking_fee=(($order->weight_income_in-$price->first_weight)*$price->additional_fee)+$price->first_fee;
    			}else{
    				$tracking_fee=$price->first_fee;
    			}
    		}
    		if($tracking_fee<=0){
    			continue;
    		}
    		$fee->amount=$tracking_fee;
    		$fee->save();
    		
    		//燃油附加费
    		$fuel_amount=0;
    		$fuel_fee=Fee::find('fee_type="1" and amount is null and fee_item_name="燃油附加费" and order_id=?',$order->order_id)->getOne();
    		if($fuel_fee->isNewRecord()){
    			continue;
    		}
    		$fuel_amount=Fee::find('fee_type="1" and fee_item_name in ("基础运费","超尺寸/超重附加费","偏远地区附加费") and order_id=?',$order->order_id)->getSum("amount");
    		if($fuel_amount<=0){
    			continue;
    		}
    		$fuel_fee->amount=sprintf("%.2f",$fuel_amount*0.15);
    		$fuel_fee->save();
    	}
    	exit;
    }
    
    /**
     * 处理老数据的毛利
     */
    function actioncalcprofit(){
    	set_time_limit(0);
    	$orders=Order::find('order_id <5119 and ifnull(profit,"")="" ')->getAll();
    	foreach ($orders as $order){
    		$shou=Fee::find("order_id=? and fee_type='1'",$order->order_id)->getSum(amount);
    		$fu=Fee::find("order_id=? and fee_type='2'",$order->order_id)->getSum(amount);
    		$amout=$shou-$fu;
    		if($amout){
    			$order->profit=$amout;
    			$order->save();
    		}
    	}
    	exit;
    }
    
    /**
     * 检索老数据中更改地址的订单
     */
    function actionchangeaddress(){
    	set_time_limit(0);
    	$orders=Order::find()->getAll();
    	foreach ($orders as $order){
    		$tracking=Tracking::find('order_id=? and tracking_code="F_DELIVERY_5043"',$order->order_id)->getOne();
    		if (!$tracking->isNewRecord()){
    			$order->address_change='1';
    			$order->address_change_info='F_DELIVERY_5043:Delivery information needed,attempting to update it';
    			$order->save();
    		}else {
    			$change_array=array('corrected the street number','corrected the apartment number','corrected the postal code','delivery change','a request to modify the delivery address','delivery address has been updated','requested an alternate delivery address','updated the delivery information','request to modify the delivery address','updated the address','change of delivery');
    			$routes=Route::find('tracking_no=?',$order->tracking_no)->getAll();
    			foreach ($routes as $route){
    				foreach ($change_array as $value){
    					if(strpos(strtolower($route->description), $value)!==false){
    						$order->address_change='1';
    						$order->address_change_info=$route->description;
    						$order->save();
    						//跳出两层循环
    						 break 2;
    					}
    				}
    			}
    		}
    	}
    	exit;
    }
    
    /**
     * 发送面单给阿里（已打印之后才发送）
     */
    function actionuploadRecordForm(){
    	set_time_limit(0);
    	$url_sign='https://gw.open.1688.com/openapi/param2/1/ali.intl.onetouch/logistics.order.uploadRecordForm/563333';
    	$select = Order::find("order_status = '6' and send_ali_form !='1' and ali_testing_order !='1' and send_times< 2 and create_time > 1594656000 ")->setColumns('order_id')->all()->getQueryHandle();
    	while (($row = $select->fetchRow()) != false) {
    		$order = Order::find('order_id=?', $row['order_id'])->getOne();
    		if($order->isNewRecord()){
    			continue;
    		}
    		if(strtoupper(substr($order->ali_order_no, 0,3))!='ALS'){
    			continue;
    		}
    		//FAR面单
    		$filename = $order->order_id.'.pdf';
    		//新命名FAR面单
    		$filename_no = $order->order_no.'.pdf';
    		//FAR面单是否存在
    		$pdfisexist = Helper_PDF::pdfisexist($filename);
    		//新命名FAR面单是否存在
    		$pdfisexist_no = Helper_PDF::pdfisexist($filename_no);
    		// 二者都不存在
    		if ($pdfisexist['message']=='noexist' && $pdfisexist_no['message']=='noexist'){
    			// 生成新命名FAR面单
    			Helper_Common::getfarlabeltoali($order);
    		}
    		// FAR面单存在
    		if ($pdfisexist['message']!='noexist'){
    			$oss_alilabel = $pdfisexist['url'];
    		}else{
    			// 其他情况都用新命名FAR面单路径
    			$oss_alilabel = $pdfisexist_no['url'];
    		}
    		$dir=Q::ini('upload_tmp_dir');
    		$alilabel = $dir.DS.$order->order_no.'.pdf';
    		$target = file_get_contents($oss_alilabel);
    		file_put_contents($alilabel,$target);
    		//QLog::log('uploadRecordForm'.$alilabel);
    		
    		exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$alilabel} -append {$alilabel}.jpg");
    		$recordformdata=file_get_contents($alilabel.'.jpg');
    		if($order->customer->customs_code=='ALPL'){
    			$fourpl = new Helper_Notify4PL();
    			$data=array(
    				'recordFormType'=>'express_label',
    				'recordFormData'=>base64_encode($recordformdata)
    			);
    			$response = $fourpl->notifyPostUploadRecordForm($data,$order);
    			QLog::log('4PL_form_response:'.$response);
    		}else{
    			//uploadRecordFormDTO数据
    			$recordform_request_data=array(
    				'aliOrderNo'=>$order->ali_order_no,
    				'recordFormType'=>'farLabel',
    				'recordFormData'=>base64_encode($recordformdata)
    			);
    			//QLog::log(json_encode($recordform_request_data));
    			$ali=new Helper_ALI();
    			$sign=$ali->sign($url_sign, json_encode($recordform_request_data),'uploadRecordForm');
    			$post_data='uploadRecordFormDTO='.urlencode(json_encode($recordform_request_data)).'&_aop_signature='.$sign;
    			//QLog::log($post_data);
    			//通过curl post 方式发送至阿里x-www-form-urlencoded 方式发送
    			$response=Helper_Curl::post($url_sign, $post_data,array(
    				'Content-Type:application/x-www-form-urlencoded'
    			));
    			QLog::log('ali_form_response:'.$response);
    		}
    		$response=json_decode($response,true);
    		if(isset($response['success']) && $response['success']==true){
    			$order->send_ali_form='1';
    			$order->save();
    			$uploadoss = new Helper_AlipicsOss();
    			if ($uploadoss->doesExist($order->order_no.'.pdf')){
    				// 上传成功，删除
    				unlink($dir.DS.$order->order_no.'.pdf');
    				//上传成功，删除.pdf.jpg文件
    				unlink($dir.DS.$order->order_no.'.pdf.jpg');
    			}
    		}else{
    			$order->send_ali_form_error=$response['message'];
    			$order->send_times+=1;
    			$order->save();
    			//$email_response=Helper_Mailer::send('xujy@far800.com', '发送面单失败', '阿里单号 '.$order->ali_order_no);
    		}
    	}
    	exit();
    }
    /**
     * 发送面单给阿里（已打印之后才发送）
     */
    function actioncainiaoceshiuploadRecordForm(){
    	set_time_limit(0);
    	$url_sign='https://gw.open.1688.com/openapi/param2/1/ali.intl.onetouch/logistics.order.uploadRecordForm/563333';
    	$select = Order::find("order_status = '6' and order_id=? and customer_id = '11' and send_ali_form !='1' and ali_testing_order !='1' and send_times< 2 and create_time > 1594656000 ",request('order_id'))->setColumns('order_id')->all()->getQueryHandle();
    	while (($row = $select->fetchRow()) != false) {
    		$order = Order::find('order_id=?', $row['order_id'])->getOne();
    		if($order->isNewRecord()){
    			continue;
    		}
    		if(strtoupper(substr($order->ali_order_no, 0,3))!='ALS'){
    			continue;
    		}
    		$i=1;
    		$total_value='';
    		$invoice = array();
    		foreach ($order->product as $v){
    			if($i>=4){
    				break;
    			}
    			$invoice['items'][]=array(
    				'prdoduct_name_hs'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
    				'quantity'=>$v->product_quantity,
    				'price'=>$v->declaration_price,
    				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
    			);
    			$total_value += round($v->product_quantity*$v->declaration_price,2);
    			$i++;
    		}
    		$toal_package=Farpackage::find('order_id=?',$order->order_id)->getSum('quantity');
    		$total_weight=sprintf("%.2f",$order->weight_income_in);
    		
    		//仓库名称
    		$consignor='From Consignor : ';
    		//发件人相关信息：
    		$shipper="Shipper: ";
    		if($order->department_id==6){
    			$consignor.="Far's warehouse in Hangzhou";
    			$shipper.='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022 ';
    			$shipper.='Miss.Zhang ';
    			$shipper.='0571-87834076';
    			//上海仓
    		}elseif ($order->department_id==7){
    			$consignor.="Far's warehouse in Shanghai";
    			$shipper.='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323 ';
    			$shipper.='Mr.Gu ';
    			$shipper.='021-58590952';
    			//义乌仓
    		}elseif ($order->department_id==8){
    			$consignor.="Far's warehouse in Yiwu";
    			$shipper.='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000 ';
    			$shipper.='Mr.Yang ';
    			$shipper.='0579-85119351';
    			//广州仓
    		}elseif ($order->department_id==22){
    			$consignor.="Far's warehouse in Guangzhou";
    			$shipper.='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080 ';
    			$shipper.='Miss.Li ';
    			$shipper.='020-36301839';
    			//青岛仓
    		}elseif ($order->department_id==23){
    			$consignor.="Far's warehouse in Qingdao";
    			$shipper.='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108 ';
    			$shipper.='Mr Wang ';
    			$shipper.='18661786160';
    			//深圳仓
    		}elseif ($order->department_id==24){
    			$consignor.="FAR's warehouse in Shenzhen Longhua";
    			$shipper.='Unit 5,No.8 Non-bonded Warehouse, South China International Logistics Center,No.1 Mingkang Road,Minzhi Street,Longhua New District,Shenzhen City,China 518000 ';
    			$shipper.='Mr Wang ';
    			$shipper.='4000857988';
    		}
    		
    		$dir=Q::ini('upload_tmp_dir');
    		@Helper_Filesys::mkdirs($dir);
    		$barcode=$dir.DS.$order->order_id.'.barcode.png';
    		$logo=$dir.DS.'logo.png';
    		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$order->far_no));
    		file_put_contents($barcode, $source);
    		$pdf=new PDF_Chinese('L','mm','far');
    		$pdf->AddGBFont('simhei', '黑体');
    		$pdf->AddPage();
    		$pdf->SetFont('Arial','B',12);
    		//阿里单号
    		$pdf->Cell(84,6,'Order No. :'.$order->ali_order_no,'1');
    		$pdf->Cell(66,6,'','TR');
    		$pdf->Ln();
    		$pdf->Cell(84,20,'','1','','C');
    		//泛远单号条形码
    		$pdf->Image($barcode,'12','19','80','14');
    		//泛远logo
    		$pdf->Image($logo,'95','15','63','20');
    		$pdf->Cell(66,20,'','R');
    		$pdf->Ln();
    		//泛远单号
    		$pdf->Cell(84,6,$order->far_no,'1','','C');
    		$pdf->SetFont('Arial','B',10);
    		//产品
    		$pdf->Cell(66,6,$order->service_code,'RB','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		//仓库名称
    		$pdf->Cell(150,8,$consignor,'1');
    		$pdf->Ln();
    		//发件人相关信息
    		$pdf->MultiCell(150,8,$shipper,'1');
    		//收件人姓名
    		$pdf->MultiCell(150,6,'To Consignee: '.$order->consignee_name1.' '.$order->consignee_name2,'1');
    		$pdf->SetFont('Arial','B',12);
    		//收件人地址1
    		$pdf->MultiCell(150,8,'Street Address1: '.$order->consignee_street1,'1');
    		//收件人地址2
    		$pdf->MultiCell(150,8,'Street Address2: '.$order->consignee_street2,'1');
    		//收件人城市和邮编
    		$pdf->Cell(99,8,'City&PostCode: '.$order->consignee_city.' '.$order->consignee_postal_code,'1');
    		$pdf->SetFont('Arial','B',10);
    		//收件人行政区/州
    		$pdf->Cell(51,8,'State: '.$order->consignee_state_region_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		//收件人电话
    		$pdf->Cell(99,8,'Contact Phone: '.$order->consignee_mobile,'1');
    		//收件人国家
    		$pdf->Cell(51,8,'Country: '.$order->consignee_country_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		$pdf->Cell(150,8,'SHIPMENT INFORMATION:','1','','C');
    		$pdf->Ln();
    		$pdf->Cell(99,7,'Main Products List','1','','C');
    		$pdf->SetFont('Arial','B',10);
    		$pdf->Cell(14,7,'Amount','1','','C');
    		$pdf->Cell(20,7,'Price/Unit','1','','C');
    		$pdf->Cell(17,7,'Subtotal','1','','C');
    		foreach ($invoice['items'] as $in){
    			$pdf->Ln();
    			$pdf->SetFont('simhei','',9);
    			//FAR中文品名FAR英文品名FARHS编码
    			$pdf->Cell(99,6,iconv("utf-8","gbk",$in['prdoduct_name_hs']),'1','','L');
    			$pdf->SetFont('Arial','B',11);
    			//产品数量
    			$pdf->Cell(14,6,$in['quantity'],'1','','C');
    			//单价
    			$pdf->Cell(20,6,$in['price'],'1','','C');
    			//总价
    			$pdf->Cell(17,6,$in['itotal'],'1','','C');
    		}
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		$pdf->Cell(99,6,'','1');
    		$pdf->Cell(34,6,'Total Value(USD):','1','','C');
    		//产品总价
    		$pdf->Cell(17,6,$total_value,'1','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',14);
    		$pdf->Cell(99,8,'Total packages','1','','C');
    		$pdf->Cell(51,8,'Total Weight(KG):','1','','C');
    		$pdf->Ln();
    		//包裹件数
    		$pdf->Cell(99,8,$toal_package,'1','','C');
    		//包裹重
    		$pdf->Cell(51,8,$total_weight,'1','','C');
    		$pdf->Ln();
    		$pdf->Cell(150,8,'Shipping Charges Payment Term: Prepaid&DDU','1');
    		$pdf->Ln();
    		$pdf->MultiCell(150,7,'Declaration Statement:I hereby certify that the information of this invoice is truce and correct and the contents and value of this shipment is as stated above.','1','L');
    		$alilabel=$dir.DS.$order->order_id.'.pdf';
    		$pdf->Output($alilabel,'F');
    		$pdf->Close();
    		exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$alilabel} -append {$alilabel}.jpg");
    		$recordformdata=file_get_contents($alilabel.'.jpg');
    		if($order->customer->customs_code=='ALPL'){
    			$fourpl = new Helper_Notify4PL();
    			$data=array(
    				'recordFormType'=>'express_label',
    				'recordFormData'=>base64_encode($recordformdata)
    			);
    			$response = $fourpl->notifyPostUploadRecordForm($data,$order);
    			QLog::log('4PL_form_response:'.$response);
	    		
	    		$response=json_decode($response,true);
	    		if(isset($response['success']) && $response['success']==true){
	    			$order->send_ali_form='1';
	    			$order->save();
	    		}else{
	    			$order->send_ali_form_error=$response['message'];
	    			$order->send_times+=1;
	    			$order->save();
// 	    			$email_response=Helper_Mailer::send('xujy@far800.com', '发送面单失败', '阿里单号 '.$order->ali_order_no);
	    		}
    		}
    	}
    	exit();
    }
    
    /**
     * 测试发送中性面单给阿里
     */
    function actionTestUploadone(){
    	ini_set('max_execution_time', '0');
    	set_time_limit(0);
    	//ALS00200398537
    	$order = Order::find("ali_order_no='ALS00000580001'")->getOne();
    	if (!$order->isNewRecord()) {
    		$i=1;
    		$total_value='';
    		foreach ($order->product as $v){
    			if($i>=4){
    				break;
    			}
    			$invoice['items'][]=array(
    				'prdoduct_name_hs'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
    				'quantity'=>$v->product_quantity,
    				'price'=>$v->declaration_price,
    				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
    			);
    			$total_value += round($v->product_quantity*$v->declaration_price,2);
    			$i++;
    		}
    		$toal_package=Farpackage::find('order_id=?',$order->order_id)->getCount();
    		$total_weight=$order->weight_income_in;
    
    		//仓库名称
    		$consignor='From Consignor : ';
    		//发件人相关信息：
    		$shipper="Shipper: ";
    		if($order->department_id==6){
    			$consignor.="Far's warehouse in Hangzhou";
    			$shipper.='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022 ';
    			$shipper.='Miss.Zhang ';
    			$shipper.='0571-87834076';
    			//上海仓
    		}elseif ($order->department_id==7){
    			$consignor.="Far's warehouse in Shanghai";
    			$shipper.='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323 ';
    			$shipper.='Mr.Gu ';
    			$shipper.='021-58590952';
    			//义乌仓
    		}elseif ($order->department_id==8){
    			$consignor.="Far's warehouse in Yiwu";
    			$shipper.='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000 ';
    			$shipper.='Mr.Yang ';
    			$shipper.='0579-85119351';
    			//广州仓
    		}elseif ($order->department_id==22){
            	$consignor.="Far's warehouse in Guangzhou";
            	$shipper.='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080 ';
            	$shipper.='Miss.Li ';
            	$shipper.='020-36301839';
            	//青岛仓
            }elseif ($order->department_id==23){
            	$consignor.="Far's warehouse in Qingdao";
            	$shipper.='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108 ';
            	$shipper.='Mr Wang ';
            	$shipper.='18661786160';
            	//深圳仓
            }elseif ($order->department_id==24){
            	$consignor.="FAR's warehouse in Shenzhen Longhua";
            	$shipper.='Unit 5,No.8 Non-bonded Warehouse, South China International Logistics Center,No.1 Mingkang Road,Minzhi Street,Longhua New District,Shenzhen City,China 518000 ';
            	$shipper.='Mr Wang ';
            	$shipper.='4000857988';
            }
    
    		$dir=Q::ini('upload_tmp_dir');
    		@Helper_Filesys::mkdirs($dir);
    		$barcode=$dir.DS.$order->order_id.'.barcode.png';
    		$logo=$dir.DS.'logo.png';
    		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$order->far_no));
    		file_put_contents($barcode, $source);
    		$pdf=new PDF_Chinese('L','mm','far');
    		$pdf->AddGBFont('simhei', '黑体');
    		$pdf->AddPage();
    		$pdf->SetFont('Arial','B',12);
    		//阿里单号
    		$pdf->Cell(84,6,'Order No. :'.$order->ali_order_no,'1');
    		$pdf->Cell(66,6,'','TR');
    		$pdf->Ln();
    		$pdf->Cell(84,20,'','1','','C');
    		//泛远单号条形码
    		$pdf->Image($barcode,'12','19','80','14');
    		//泛远logo
    		$pdf->Image($logo,'95','15','63','20');
    		$pdf->Cell(66,20,'','R');
    		$pdf->Ln();
    		//泛远单号
    		$pdf->Cell(84,6,$order->far_no,'1','','C');
    		$pdf->SetFont('Arial','B',10);
    		//产品
    		$pdf->Cell(66,6,$order->service_code,'RB','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		//仓库名称
    		$pdf->Cell(150,8,$consignor,'1');
    		$pdf->Ln();
    		//发件人相关信息
    		$pdf->MultiCell(150,8,$shipper,'1');
    		//收件人姓名
    		$pdf->MultiCell(150,6,'To Consignee: '.$order->consignee_name1.' '.$order->consignee_name2,'1');
    		$pdf->SetFont('Arial','B',12);
    		//收件人地址1
    		$pdf->MultiCell(150,8,'Street Address1: '.$order->consignee_street1,'1');
    		//收件人地址2
    		$pdf->MultiCell(150,8,'Street Address2: '.$order->consignee_street2,'1');
    		//收件人城市和邮编
    		$pdf->Cell(99,8,'City&PostCode: '.$order->consignee_city.' '.$order->consignee_postal_code,'1');
    		$pdf->SetFont('Arial','B',10);
    		//收件人行政区/州
    		$pdf->Cell(51,8,'State: '.$order->consignee_state_region_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		//收件人电话
    		$pdf->Cell(99,8,'Contact Phone: '.$order->consignee_mobile,'1');
    		//收件人国家
    		$pdf->Cell(51,8,'Country: '.$order->consignee_country_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		$pdf->Cell(150,8,'SHIPMENT INFORMATION:','1','','C');
    		$pdf->Ln();
    		$pdf->Cell(99,7,'Main Products List','1','','C');
    		$pdf->SetFont('Arial','B',10);
    		$pdf->Cell(14,7,'Amount','1','','C');
    		$pdf->Cell(20,7,'Price/Unit','1','','C');
    		$pdf->Cell(17,7,'Subtotal','1','','C');
    		foreach ($invoice['items'] as $in){
    			$pdf->Ln();
    			$pdf->SetFont('simhei','',9);
    			//FAR中文品名FAR英文品名FAR HS编码
    			$pdf->Cell(99,6,iconv("utf-8","gbk",$in['prdoduct_name_hs']),'1','','L');
    			$pdf->SetFont('Arial','B',11);
    			//产品数量
    			$pdf->Cell(14,6,$in['quantity'],'1','','C');
    			//单价
    			$pdf->Cell(20,6,$in['price'],'1','','C');
    			//总价
    			$pdf->Cell(17,6,$in['itotal'],'1','','C');
    		}
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		$pdf->Cell(99,6,'','1');
    		$pdf->Cell(34,6,'Total Value(USD):','1','','C');
    		//产品总价
    		$pdf->Cell(17,6,$total_value,'1','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',14);
    		$pdf->Cell(99,8,'Total packages','1','','C');
    		$pdf->Cell(51,8,'Total Weight(KG):','1','','C');
    		$pdf->Ln();
    		//包裹件数
    		$pdf->Cell(99,8,$toal_package,'1','','C');
    		//包裹重
    		$pdf->Cell(51,8,$total_weight,'1','','C');
    		$pdf->Ln();
    		$pdf->Cell(150,8,'Shipping Charges Payment Term: Prepaid&DDU','1');
    		$pdf->Ln();
    		$pdf->MultiCell(150,7,'Declaration Statement:I hereby certify that the information of this invoice is truce and correct and the contents and value of this shipment is as stated above.','1','L');
    		$alilabel=$dir.DS.$order->order_id.'.pdf';
    		$pdf->Output($alilabel,'F');
    		$pdf->Close();
    		exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$alilabel} -append {$alilabel}.jpg");
    		$recordformdata=file_get_contents($alilabel.'.jpg');
    		//uploadRecordFormDTO数据
    		$array=array(
    			'aliOrderNo'=>$order->ali_order_no,
    			'recordFormType'=>'farLabel',
    			'recordFormData'=>base64_encode($recordformdata)
    		);
    		$url_sign='http://112.124.134.6:80/openapi/param2/1/ali.intl.onetouch/logistics.order.uploadRecordForm/831170';
    		$ali=new Helper_ALI();
    		$secretKey='fq6nVNy02u';
    		$urlpath=strstr($url_sign, 'param2/');
    		$s=$urlpath.'uploadRecordForm'.'DTO'.json_encode($array);
    		$sign=strtoupper(bin2hex(hash_hmac('sha1',$s,$secretKey,true)));
    		$post_data='uploadRecordFormDTO='.urlencode(json_encode($array)).'&_aop_signature='.$sign;
    		QLog::log($post_data);
    		//通过curl post 方式发送至阿里x-www-form-urlencoded 方式发送
    		$response=Helper_Curl::post($url_sign, $post_data,array(
	        	'Content-Type:application/x-www-form-urlencoded'
	   		 ));
    		QLog::log('ali_form_response:'.$response);
    		$response=json_decode($response,true);
    		if(isset($response['success']) && $response['success']==true){
    			$order->send_ali_form='1';
    			$order->save();
    		}else{
    			$order->send_ali_form_error=$response['message'];
    			$order->save();
    		}
    	}
    	exit();
    }
    
    /**
     * 测试接收阿里的单证错误信息
     */
    function actionTestaliformexception(){
    	$url='http://1688.far800.com/api/notifyformexception';
    	$exceptionDTO=array('aliOrderNo'=>'ALS00000576004','recordFormType'=>'farLabel','exceptionMessage'=>'商品申报数量不一致');
    	$exception=array('sign'=>'ABCD134567890','notifyFormExceptionDTO'=>json_encode($exceptionDTO));
    	QLog::log(json_encode($exception));
    	$response=Helper_Curl::post($url, json_encode($exception));
    }
    
    /**
     * 测试发送面单给阿里（出库之后才发送）
     */
    function actiontestuploadRecordForm(){
    	set_time_limit(0);
    	$select = Order::find("send_ali_form='1' and order_id >7933")->setColumns('order_id')->all()->getQueryHandle();
    	while (($row = $select->fetchRow()) != false) {
    		$order = Order::find('order_id=?', $row['order_id'])->getOne();
    		if($order->isNewRecord()){
    			continue;
    		}
    		$i=1;
    		$total_value='';
    		foreach ($order->product as $v){
    			if($i>=4){
    				break;
    			}
    			$invoice['items'][]=array(
    				'prdoduct_name_hs'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
    				'quantity'=>$v->product_quantity,
    				'price'=>$v->declaration_price,
    				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
    			);
    			$total_value += round($v->product_quantity*$v->declaration_price,2);
    			$i++;
    		}
    		$toal_package=Farpackage::find('order_id=?',$order->order_id)->getCount();
    		$total_weight=$order->weight_income_in;
    
    		//仓库名称
    		$consignor='From Consignor : ';
    		//发件人相关信息：
    		$shipper="Shipper: ";
    		if($order->department_id==6){
    			$consignor.="Far's warehouse in Hangzhou";
    			$shipper.='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022 ';
    			$shipper.='Miss.Zhang ';
    			$shipper.='0571-87834076';
    			//上海仓
    		}elseif ($order->department_id==7){
    			$consignor.="Far's warehouse in Shanghai";
    			$shipper.='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323 ';
    			$shipper.='Mr.Gu ';
    			$shipper.='021-58590952';
    			//义乌仓
    		}elseif ($order->department_id==8){
    			$consignor.="Far's warehouse in Yiwu";
    			$shipper.='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000 ';
    			$shipper.='Mr.Yang ';
    			$shipper.='0579-85119351';
    			//广州仓
    		}elseif ($order->department_id==22){
            	$consignor.="Far's warehouse in Guangzhou";
            	$shipper.='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080 ';
            	$shipper.='Miss.Li ';
            	$shipper.='020-36301839';
            	//青岛仓
            }elseif ($order->department_id==23){
            	$consignor.="Far's warehouse in Qingdao";
            	$shipper.='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108 ';
            	$shipper.='Mr Wang ';
            	$shipper.='18661786160';
            	//深圳仓
            }elseif ($order->department_id==24){
            	$consignor.="FAR's warehouse in Shenzhen Longhua";
            	$shipper.='Unit 5,No.8 Non-bonded Warehouse, South China International Logistics Center,No.1 Mingkang Road,Minzhi Street,Longhua New District,Shenzhen City,China 518000 ';
            	$shipper.='Mr Wang ';
            	$shipper.='4000857988';
            }
    
    		$dir=Q::ini('upload_tmp_dir');
    		@Helper_Filesys::mkdirs($dir);
    		$barcode=$dir.DS.$order->order_id.'.barcode.png';
    		$logo=$dir.DS.'logo.png';
    		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$order->far_no));
    		file_put_contents($barcode, $source);
    		$pdf=new PDF_Chinese('L','mm','far');
    		$pdf->AddGBFont('simhei', '黑体');
    		$pdf->AddPage();
    		$pdf->SetFont('Arial','B',12);
    		//阿里单号
    		$pdf->Cell(84,6,'Order No. :'.$order->ali_order_no,'1');
    		$pdf->Cell(66,6,'','TR');
    		$pdf->Ln();
    		$pdf->Cell(84,20,'','1','','C');
    		//泛远单号条形码
    		$pdf->Image($barcode,'12','19','80','14');
    		//泛远logo
    		$pdf->Image($logo,'95','15','63','20');
    		$pdf->Cell(66,20,'','R');
    		$pdf->Ln();
    		//泛远单号
    		$pdf->Cell(84,6,$order->far_no,'1','','C');
    		$pdf->SetFont('Arial','B',10);
    		//产品
    		$pdf->Cell(66,6,$order->service_code,'RB','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		//仓库名称
    		$pdf->Cell(150,8,$consignor,'1');
    		$pdf->Ln();
    		//发件人相关信息
    		$pdf->MultiCell(150,8,$shipper,'1');
    		//收件人姓名
    		$pdf->MultiCell(150,6,'To Consignee: '.$order->consignee_name1.' '.$order->consignee_name2,'1');
    		$pdf->SetFont('Arial','B',12);
    		//收件人地址1
    		$pdf->MultiCell(150,8,'Street Address1: '.$order->consignee_street1,'1');
    		//收件人地址2
    		$pdf->MultiCell(150,8,'Street Address2: '.$order->consignee_street2,'1');
    		//收件人城市和邮编
    		$pdf->Cell(99,8,'City&PostCode: '.$order->consignee_city.' '.$order->consignee_postal_code,'1');
    		$pdf->SetFont('Arial','B',10);
    		//收件人行政区/州
    		$pdf->Cell(51,8,'State: '.$order->consignee_state_region_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		//收件人电话
    		$pdf->Cell(99,8,'Contact Phone: '.$order->consignee_mobile,'1');
    		//收件人国家
    		$pdf->Cell(51,8,'Country: '.$order->consignee_country_code,'1');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',12);
    		$pdf->Cell(150,8,'SHIPMENT INFORMATION:','1','','C');
    		$pdf->Ln();
    		$pdf->Cell(99,7,'Main Products List','1','','C');
    		$pdf->SetFont('Arial','B',10);
    		$pdf->Cell(14,7,'Amount','1','','C');
    		$pdf->Cell(20,7,'Price/Unit','1','','C');
    		$pdf->Cell(17,7,'Subtotal','1','','C');
    		foreach ($invoice['items'] as $in){
    			$pdf->Ln();
    			$pdf->SetFont('simhei','',9);
    			//FAR中文品名FAR英文品名FAR HS编码
    			$pdf->Cell(99,6,iconv("utf-8","gbk",$in['prdoduct_name_hs']),'1','','L');
    			$pdf->SetFont('Arial','B',11);
    			//产品数量
    			$pdf->Cell(14,6,$in['quantity'],'1','','C');
    			//单价
    			$pdf->Cell(20,6,$in['price'],'1','','C');
    			//总价
    			$pdf->Cell(17,6,$in['itotal'],'1','','C');
    		}
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',11);
    		$pdf->Cell(99,6,'','1');
    		$pdf->Cell(34,6,'Total Value(USD):','1','','C');
    		//产品总价
    		$pdf->Cell(17,6,$total_value,'1','','C');
    		$pdf->Ln();
    		$pdf->SetFont('Arial','B',14);
    		$pdf->Cell(99,8,'Total packages','1','','C');
    		$pdf->Cell(51,8,'Total Weight(KG):','1','','C');
    		$pdf->Ln();
    		//包裹件数
    		$pdf->Cell(99,8,$toal_package,'1','','C');
    		//包裹重
    		$pdf->Cell(51,8,$total_weight,'1','','C');
    		$pdf->Ln();
    		$pdf->Cell(150,8,'Shipping Charges Payment Term: Prepaid&DDU','1');
    		$pdf->Ln();
    		$pdf->MultiCell(150,7,'Declaration Statement:I hereby certify that the information of this invoice is truce and correct and the contents and value of this shipment is as stated above.','1','L');
    		$alilabel=$dir.DS.$order->order_id.'.pdf';
    		$pdf->Output($alilabel,'F');
    		$pdf->Close();
    		exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$alilabel} -append {$alilabel}.jpg");
    		$recordformdata=file_get_contents($alilabel.'.jpg');
    		//uploadRecordFormDTO数据
    		$array=array(
    			'aliOrderNo'=>$order->ali_order_no,
    			'recordFormType'=>'farLabel',
    			'recordFormData'=>base64_encode($recordformdata)
    		);
    		$url_sign='http://112.124.134.6:80/openapi/param2/1/ali.intl.onetouch/logistics.order.uploadRecordForm/831170';
    		$ali=new Helper_ALI();
    		$secretKey='fq6nVNy02u';
    		$urlpath=strstr($url_sign, 'param2/');
    		$s=$urlpath.'uploadRecordForm'.'DTO'.json_encode($array);
    		$sign=strtoupper(bin2hex(hash_hmac('sha1',$s,$secretKey,true)));
    		$post_data='uploadRecordFormDTO='.urlencode(json_encode($array)).'&_aop_signature='.$sign;
    		//通过curl post 方式发送至阿里x-www-form-urlencoded 方式发送
    		$response=Helper_Curl::post($url_sign, $post_data,array(
    			'Content-Type:application/x-www-form-urlencoded'
    		));
    		QLog::log('ali_form_response:'.$response);
    		$response=json_decode($response,true);
    		if(isset($response['success']) && $response['success']==true){
    			
    		}else{
    			$order->send_ali_form_error=$response['message'];
    			$order->save();
    		}
    	}
    	exit();
    }
    /**
     * @todo   测试
     * @author 许杰晔
     * @since  2020-8-17 10:32:54
     * @param  object $order
     * @return string|boolean
     * @link   #81740
     */
    function actionCaiNiaoTest(){
    	set_time_limit(0);
    	ini_set('memory_limit', '-1');
    	
    	Helper_ExcelX::startWriter ( 'order_tracking'  );
    	$header = array (
    		'阿里订单号','跟踪单号','节点','时间','地址','轨迹'
    	);
    	Helper_ExcelX::addRow ($header);
		foreach ($tracking_no as $s){
			$order=Tracking::find('tb_tracking.order_id =? and tb_tracking.route_id >0 ',$s);
			$order->joinLeft('tb_routes', 'tb_routes.tracking_no,tb_routes.description,tb_routes.time,tb_routes.location','tb_tracking.route_id = tb_routes.id' );
			$order->joinLeft('tb_order', 'tb_order.ali_order_no','tb_order.order_id = tb_tracking.order_id' );
			$order=$order->order ( 'tb_routes.tracking_no,tb_routes.time' )
			->asarray()->get(5);
			foreach ($order as $value){
				$sheet =array(				
					$value['ali_order_no'],
					" ".$value['tracking_no'],
					$value['tracking_code'],
					date('Y-m-d H:i:s',$value['time']),
					$value['location'],
					$value['description']
				);
				//print_r($sheet);exit;
				Helper_ExcelX::addRow ( $sheet );
			}
		}
		Helper_ExcelX::closeWriter ();
    	exit;
    }
    
    /**
     * @todo   线下订单状态变成已签收
     * @author stt
     * @since  2020-10-23
     * @param  
     * @return 
     * @link   #83287
     */
    function actiontransfersign(){
    	$customers = Customer::find('customer_type=2')->asArray()->getAll();
    	//不需要发送轨迹事件的客户
    	$customer_ids = Helper_Array::getCols($customers, 'customer_id');
    	//未签收的订单有签收轨迹
    	$tracking=Tracking::find("tb_tracking.tracking_code = 'S_DELIVERY_SIGNED' and tb_order.order_status='8' and tb_tracking.customer_id in (?)",$customer_ids)
    	->joinLeft('tb_order', 'tb_order.order_status','tb_order.order_id = tb_tracking.order_id' )
    	->getAll();
	    foreach ($tracking as $temp){
	    	$order = Order::find('order_id=?',$temp->order_id)->getOne();
	    	$order->delivery_time=$temp->trace_time;
    		$order->order_status='9';
    		$order->save();
	    }
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
    	$orders = Order::find('is_picture=1')
    	->setColumns ( 'order_id,order_no' )
    	->order('create_time desc')
    	->getAll(); 	
    	self::log('begin');
    	foreach ($orders as $order){
    		$uploadoss = new Helper_AlipicsOss();
    		//kuaishou文件夹是否存在图片
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
    			self::log($order->order_no.'.jpg');			
    		}
    	}
    	self::log('end');
    	exit;
    }
    
    /**
     * @todo   30天前的那一天已签收轨迹
     * @author stt
     * @since  2020年12月18日09:20:55
     * @param
     * @return string
     * @link   #84564
     */
    function actiondelivery() {
    	//每周日早上7点执行一次
    	$istrue = false;
    	$weekday =date('w',time());
    	$hours =date('h',time());
    	if($weekday==7 && $hours==7){
    		$istrue=true;
    	}
    	if (!$istrue)
    	{
    		exit;
    	}
    	//sleep 2h
    	$sleep=Q::cache('deliverySleep',array('life_time'=>7200));
    	if ($sleep){
    		self::log('sleep');
    		exit;
    	}
    	//不限制超时时间
    	set_time_limit(0);
    	//不限制内存
    	ini_set('memory_limit', '-1');
    	//30天前的时间
    	$delivery_time = date('Y-m-d',strtotime("-30 day"));
    	$delivery_week_time = date('Y-m-d',strtotime("-36 day"));
    	//30天前的一周开始时间
    	$delivery_starttime = strtotime($delivery_week_time.' 00:00:00');
    	//30天前的一周结束时间
    	$delivery_endtime = strtotime($delivery_time.' 23:59:59');
    	//30天前的一天已签收的订单
    	$select = Order::find('delivery_time>? and delivery_time<? and order_status=9',$delivery_starttime,$delivery_endtime);
    	$select= $select->setColumns('order_id,ali_order_no,tracking_no,channel_id')->all()->getQueryHandle();
    	//USPS用到
    	$order_obj = array ();
    	while (($row=$select->fetchRow())!= false){
    		$order=Order::find('order_id =?',$row['order_id'])->getOne();
    		if (!empty($row['tracking_no']) && !empty($row['channel_id'])){
    			$channel =Channel::find('channel_id =?',$row['channel_id'])->getOne();
    			//网络
    			$network_code=$channel->network_code;
    			//末端网络
    			$tnetwork_code=$channel->trace_network_code;
    			//UPS
    			if ($network_code =='UPS' || $tnetwork_code == 'UPS'){
    				$json=Helper_Curl::get('http://m.far800.com/?action=tracking&num='.$row['tracking_no'].'&lang=en');
    				$routes=json_decode($json,true);
    				//轨迹有返回,记录日志
    				$deliverylog = new DeliveryLog();
    				$deliverylog->order_id = $order->order_id;
    				$deliverylog->staff_name = '系统';
    				$deliverylog->tracking_no = $order->tracking_no;
    				$deliverylog->comment = $json;
    				$deliverylog->save();
    				if (!empty($routes['data']) && count($routes['data'])){
    					// 将轨迹按照时间升序排序
    					$routes['data']=array_reverse($routes['data']);
    					foreach ($routes['data'] as $d){
    						// 保存
    						if(!isset($d['location']) || !isset($d['time']) || !isset($d['context']) || !$d['time'] || !$d['context']){
    							continue;
    						}
    						// 保存
    						if(!empty($order->warehouse_out_time) && strtotime($d['time'])<($order->warehouse_out_time-86400)){
    							continue;
    						}
    						//保存route
    						$r=Route::checkAndSave(array(
    							'network_code'=>'UPS',
    							'tracking_no'=>$row['tracking_no'],
    							'time'=>strtotime($d['time']),
    							'location'=>$d['location'],
    							'description'=>$d['context'],
    							'code'=>$d['code']
    						));
    						if (!is_null($r)){
    							//签收异常
    							$order->is_signunusual=1;
    							$order->save();
    							//有签收轨迹，标注一下is_delivery=1为签收轨迹
    							$r->matchingrules($order);
    						}
    					}
    				}
    			//FEDEX
    			}elseif ($network_code =='FEDEX' || $tnetwork_code == 'FEDEX'){
    				$key='Y8DNyhcsybFkXWDy';
    				$password='2CLHx5wETXxDSCe1alwpS6Jxn';
    				$accountnumber='497335868';
    				$meternumber='251395350';
    				$res = array(
    					'soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v9="http://fedex.com/ws/track/v9"' => array(
    						'soapenv:Header' =>'',
    						'soapenv:Body' => array(
    							'v9:TrackRequest' => array(
    								'v9:WebAuthenticationDetail' => array(
    									'v9:UserCredential'=>array(
    										'v9:Key'=>$key,
    										'v9:Password'=>$password,
    									),
    								),
    								'v9:ClientDetail'=>array(
    									'v9:AccountNumber'=>$accountnumber,
    									'v9:MeterNumber'=>$meternumber,
    									'v9:Localization'=>array(
    										'v9:LanguageCode'=>'EN',
    										'v9:LocaleCode'=>'US',
    									)
    								),
    								'v9:TransactionDetail'=>array(
    									'v9:CustomerTransactionId'=>'Track By Number_v9',
    									'v9:Localization'=>array(
    										'v9:LanguageCode'=>'EN',
    										'v9:LocaleCode'=>'US'
    									)
    								),
    								'v9:Version'=>array(
    									'v9:ServiceId'=>'trck',
    									'v9:Major'=>'9',
    									'v9:Intermediate'=>'1',
    									'v9:Minor'=>'0'
    								),
    								'v9:SelectionDetails'=>array(
    									'v9:CarrierCode'=>'FDXE',
    									'v9:PackageIdentifier'=>array(
    										'v9:Type'=>'TRACKING_NUMBER_OR_DOORTAG',
    										'v9:Value'=>$row['tracking_no']
    									),
    								),
    								'v9:ProcessingOptions'=>'INCLUDE_DETAILED_SCANS'
    							),
    						),
    					)
    				);
    				$res = Helper_xml::simpleArr2xml($res,null);
    				//fedex正式地址
    				$url='https://ws.fedex.com:443/web-services';
    				try {
    					//请求
    					$return = Helper_Curl::post($url, $res);
    				} catch ( Exception $e ) {
    					continue;
    				}
    				//xml转array
    				$return=self::xml_to_array($return);
    				//轨迹有返回,记录日志
    				$deliverylog = new DeliveryLog();
    				$deliverylog->order_id = $order->order_id;
    				$deliverylog->staff_name = '系统';
    				$deliverylog->tracking_no = $order->tracking_no;
    				$deliverylog->comment = json_encode($return);
    				$deliverylog->save();
    				if($return['TrackReply']['CompletedTrackDetails']['TrackDetails']){
    					$return=$return['TrackReply']['CompletedTrackDetails']['TrackDetails'];
    					if(isset($return['Notification']['Severity']) && $return['Notification']['Severity'] != 'ERROR'){
    						// 保存
    						if(count($return['Events'])>0){
    							foreach (array_reverse($return['Events']) as $d){
    								//过滤掉第一条轨迹
    								if (!isset($d['Address']['CountryCode'])){
    									continue;
    								}
    								//时区
    								$time_zone=substr($d['Timestamp'], 19,3);
    								if($time_zone<0){
    									$time_zone='-'.abs($time_zone);
    								}else {
    									$time_zone=abs($time_zone);
    								}
    								$time = strtotime(substr($d['Timestamp'], 0,19))+(8-$time_zone)*3600;
    								//出库时间之前的轨迹不同步进来
    								if(!empty($order->warehouse_out_time) && $time<$order->warehouse_out_time){
    									continue;
    								}
    								//保存route
    								$r=Route::checkAndSave(array(
    									'network_code'=>'FEDEX',
    									'tracking_no'=>$row['tracking_no'],
    									'time'=>strtotime(substr($d['Timestamp'], 0,19)),
    									'location'=>@$d['Address']['City'].','.@$d['Address']['StateOrProvinceCode'].','.@$d['Address']['CountryCode'],
    									'description'=>$d['EventDescription'].(isset($d['StatusExceptionDescription'])?' '.$d['StatusExceptionDescription']:''),
    									'time_zone'=>$time_zone,
    									'code'=>$d['EventType']
    								));
    								//有新的轨迹产生
    								if (!is_null($r)){
    									//签收异常
    									$order->is_signunusual=1;
    									$order->save();
    									//有签收轨迹，标注一下is_delivery=1为签收轨迹
    									$r->matchingrules($order);
    								}
    							}
    						}
    					}
    				}
    			//EMS和DHL
    			}elseif ($network_code =='EMS' || $network_code =='DHL' || $tnetwork_code == 'DHL'){
    				if($network_code =='EMS'){
    					//EMS
    					$carrier = 'china-ems';
    				}elseif ($network_code =='DHL' || $tnetwork_code == 'DHL'){
    					//DHL
    					$carrier = 'dhl';
    				}
    				$ft_url = 'http://check.coomao.com/index.php/default/ftracking/refresh';
    				$query_str = array (
    					'apikey' => 'EmsRoute',
    					'carrier' => $carrier,
    					'bill_no' => $order->tracking_no,
    					'external_id' => $order->ali_order_no,
    					'level' =>1,
    					//英文轨迹
    					'lang' => 'en'
    				);
    				$query_str = http_build_query ( $query_str );
    				$url = $ft_url . '?' . $query_str;
    				QLog::log ( 'FarTracking - refresh - url - ' . $url );
    				try {
    					$fartracking_res = Helper_Curl::post ( $url, '' );
    				} catch ( Exception $e ) {
    					continue;
    				}
    				//轨迹有返回,记录日志
    				$deliverylog = new DeliveryLog();
    				$deliverylog->order_id = $order->order_id;
    				$deliverylog->staff_name = '系统';
    				$deliverylog->tracking_no = $order->tracking_no;
    				$deliverylog->comment = $fartracking_res;
    				$deliverylog->save();
    				$fartracking_res = json_decode($fartracking_res,true);
    				if(isset($fartracking_res['code']) && $fartracking_res['code']=='0'){
    					$lastresult = array ();
    					if (isset ( $fartracking_res ['lastResult'] )) {
    						$lastresult = $fartracking_res ['lastResult'];
    					} else {
    						$lastresult = @$fartracking_res ['data'] ['lastResult'];
    					}
    					if (! $lastresult ['nu']) {
    						continue;
    					}
    					$nu = @$lastresult ['nu'];
    					$data = @$lastresult ['data'];
    					if (count ( $data ) <= 0) {
    						continue;
    					}
    					$order = Order::find ( 'tracking_no = ?', $nu )->getOne ();
    					if ($order->isNewRecord ()) {
    						continue;
    					}
    					//EMS
    					if ($order->channel->network_code == 'EMS') {
    						foreach ( $data as $key=>$value ) {
    							$event_time = strtotime ( $value ['time'] );
    							//当轨迹发生时间在订单出库时间之前时，不作匹配；
    							if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    								continue;
    							}
    							//轨迹内容
    							$event_content = str_replace ( 'esb location ', '', $value ['context'] );
    							$event_content = str_replace ( 'old ', '', $event_content );
    							//轨迹地点
    							$event_location = str_replace ( 'esb location ', '', $value ['location'] );
    							$event_location = str_replace ( 'old ', '', $event_location );
    							//保存route
    							$route = Route::checkAndSave ( array (
    								'network_code' => 'EMS',
    								'tracking_no' => $nu,
    								'time' => $event_time,
    								'location' => $event_location,
    								'description' => $event_content
    							) );
    							if (! is_null ( $route )) {
    								//签收轨迹
    								if(strpos($event_content, '妥投')!==false && strpos($event_content, '未妥投')===false
    									&& strpos($event_content, '退回 妥投')===false && strpos($event_content, '退回妥投')===false){
    										$route->is_delivery = 1;
    										$route->save();
    								}
    								//签收异常
    								$order->is_signunusual=1;
    								$order->save();
    								//有签收轨迹，标注一下is_delivery=1为签收轨迹
    								$route->matchingrules($order);
    							}
    						}
    					//DHL
    					}elseif ($order->channel->network_code == 'DHL' || $order->channel->trace_network_code == 'DHL'){
    						foreach ( $data as $value ) {
    							$event_time = strtotime ( $value ['time'] );
    							if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    								continue;
    							}
    							//轨迹时间
    							$event_time = strtotime ( $value ['time'] );
    							//轨迹地点
    							$location=$value ['location'];
    							$location=explode("-", $location);
    							$country=@$location[1];
    							if(strpos($country, ',')){
    								$loca=$country;
    								$loca=explode(",", $loca);
    								$country=$loca[0];
    							}
    							if((strpos ( strtolower ( $value ['context'] ), 'delivered' ) !== false && strpos ( strtolower ( $value ['context'] ), 'refused delivery' ) === false ) && empty($country)){
    								$country=$order->consignee_country_code;
    							}
    							$route=Route::checkAndSave(array(
    								'network_code'=>'DHL',
    								'tracking_no'=>$nu,
    								'time'=>$event_time,
    								'location'=>@$location[0].','.$country,
    								'description'=>$value ['context'],
    							));
    							if (!is_null($route) ){
    								//签收异常
    								$order->is_signunusual=1;
    								$order->save();
    								//有签收轨迹，标注一下is_delivery=1为签收轨迹
    								$route->matchingrules($order);
    							}
    						}
    					}
    				}
    			//USPS
    			}elseif ( $network_code =='USPS' || $tnetwork_code == 'USPS'){
    				$url = 'http://production.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=';
    				$body = '<?xml version="1.0" encoding="UTF-8" ?><TrackFieldRequest USERID="524FARLO6825"><Revision>1</Revision><ClientIp>127.0.0.1</ClientIp><SourceId>USPSTOOLS</SourceId>';
    				//末端单号
    				$order_obj [] = $row['tracking_no'];
    				self::savedeliveryuspspost($order_obj);
    				$order_obj = array();
    			}
    		}
    		//每个间隔1秒
    		sleep(1);
    	}
    	Q::writeCache('deliverySleep',true,array('life_time'=>7200));
    	//结束，输出success
    	echo 'success';
    	exit;
    }
    /**
     * @todo   每一个执行一次
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function savedeliveryuspspost($order_obj=array()){
    	$url = 'http://production.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=';
    	$body = '<?xml version="1.0" encoding="UTF-8" ?><TrackFieldRequest USERID="524FARLO6825"><Revision>1</Revision><ClientIp>127.0.0.1</ClientIp><SourceId>USPSTOOLS</SourceId>';
    	foreach ($order_obj as $tracking ){
    		$body .= '<TrackID ID="' . $tracking . '"/>';
    	}
    	$body .= '</TrackFieldRequest>';
    	$usps_xml = Helper_Curl::get ( $url . urlencode ( $body ) );
    	$usps_arr = json_decode ( json_encode ( simplexml_load_string ( $usps_xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
    	if (! is_array ( $usps_arr )) {
    		//记录失败
    		QLog::log ('获取轨迹失败');
    		return;
    	}
    	//回传一单轨迹
    	if (isset ( $usps_arr ['TrackInfo'] ['@attributes'] )) {
    		$order = Order::find ( 'tracking_no = ?', $usps_arr ['TrackInfo'] ['@attributes']['ID'] )->getOne ();
    		//轨迹有返回,记录日志
    		$deliverylog = new DeliveryLog();
    		$deliverylog->order_id = $order->order_id;
    		$deliverylog->staff_name = '系统';
    		$deliverylog->tracking_no = $order->tracking_no;
    		$deliverylog->comment = json_encode($usps_arr);
    		$deliverylog->save();
    		if (! in_array( $usps_arr ['TrackInfo'] ['@attributes'] ['ID'], $order_obj )) {
    			//记录失败
    			QLog::log ('失败');
    			return;
    		}
    		//保存
    		self::savedeliveryuspsroute ($usps_arr ['TrackInfo'] );
    	}
    }
    /**
     * @todo   usps保存轨迹
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function savedeliveryuspsroute($param_arr=array()){
    	$order = Order::find ( 'tracking_no = ?', $param_arr['@attributes']['ID'] )->getOne ();
    	if (isset ( $param_arr ['TrackDetail'] ) && $param_arr ['TrackDetail']) {
    		if (isset ( $param_arr ['TrackDetail'] ['EventTime'] )) {
    			//保存
    			self::savedeliveryuspstrace ( $order, $param_arr ['TrackDetail'] );
    		} else {
    			krsort ( $param_arr ['TrackDetail'] );
    			foreach ( $param_arr ['TrackDetail'] as $trace ) {
    				//保存
    				self::savedeliveryuspstrace ( $order, $trace );
    			}
    		}
    	}
    	//TrackSummary
    	if (isset ( $param_arr ['TrackSummary'] ) && $param_arr ['TrackSummary']) {
    		//保存
    		self::savedeliveryuspstrace ( $order, @$param_arr ['TrackSummary'] );
    	}
    }
    /**
     * @todo   usps保存轨迹
     * @author stt
     * @since  2020-11-19
     * @param
     * @return json
     * @link   #83890
     */
    static function savedeliveryuspstrace($order = null, $data = array()){
    	if (! isset ( $data ['Event'] )) {
    		return;
    	}
    	if (! isset ( $data ['EventCode'] )) {
    		return;
    	}
    	//USPS
    	if ($order->channel->network_code == 'USPS') {
    		$quantity = Farpackage::find ( 'order_id=?', $order->order_id )->sum ( 'quantity', 'sum_quantity' )->getAll ();
    		$event_time = strtotime ( @$data ['EventDate'] . (!is_array(@$data ['EventTime'])?@$data ['EventTime']:''));
    		//当轨迹发生时间在订单出库时间之前时，不作匹配；
    		if (! empty ( $order->warehouse_out_time ) && $event_time < $order->warehouse_out_time) {
    			return;
    		}
    		$event_content = @$data ['Event'];
    		$event_location = '';
    		if ($data ['EventCity']){
    			$event_location .= $data ['EventCity'];
    			$event_location .= $data ['EventState'] ? ','.$data ['EventState'] : '';
    			$event_location .= $data ['EventZIPCode'] ? ','.$data ['EventZIPCode'] : '';
    			$event_location .= ',US';
    		}
    		//末端单号
    		$nu = $order->tracking_no;
    		//保存route
    		$route = Route::checkAndSave ( array (
    			'network_code' => 'USPS',
    			'tracking_no' => $nu,
    			'time' => $event_time,
    			'location' => $event_location,
    			'description' => $event_content,
    			'code' => @$data ['EventCode']
    		) );
    		if (! is_null ( $route )) {
    			//签收异常
    			$order->is_signunusual=1;
    			$order->save();
    			//有签收轨迹，标注一下is_delivery=1为签收轨迹
    			$route->matchingrules($order);
    		}
    	}
    }
    
}