<?php
/**
* @todo 4PL api助手
* @author 吴开龙
* @since 2020-8-19 14:04:07
* @param 
* @return json
* @link #81937
 */
class Helper_Notify4PL {
	//测试地址
		const url = 'https://prelink.cainiao.com/gateway/link.do';
	// 	//正式地址
// 		const url = 'http://link.cainiao.com/gateway/link.do';
		
	//来源CP编号(即控制台上APPKEY绑定的资源编码)
// 	const logistic_provider_id = '1';
	//目的方编码（可选，如不填使用该msg_type默认目的方）
// 	const to_code = '1';
	//签名key
	const key = '772267';
	//编码方式
// 	const charset = 'utf-8';
	//当前cp资源code  不确定与logistic_provider_id是否一样？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？
// 	const currentCPResCode = '1';
	//菜⻦LP单号，下发给CP时使⽤
// 	const logisticsOrderCode = '1';
	
	/**
	 * @todo 签名
	 * @author 吴开龙
	 * @since 2020-8-19 14:04:07
	 * @param $content 报文内容
	 * @return string
	 * @link #81937
	 */
	function sign($content){
		return base64_encode(md5($content.self::key));
	}
	
	/**
	 * @todo 回传接口
	 * @author 吴开龙
	 * @since 2020-8-19 14:40:07
	 * @param $event事件
	 * @return string
	 * @link #81937
	 */
	static function notifyPost($event){
		$order=Order::find("order_id=?",$event->order_id)->getOne();
		if($order->isNewRecord()){
			return false;
		}
		$event_time=date('Y-m-d H:i:s',$event->event_time);
		$content = array(
			'logisticsOrderCode' => $order->order_no,
			'currentCPResCode' => $order->warehouse_code,
			'eventTime' => $event_time,
			'operator' => 'test',
			'eventCode' => $event->event_code,
			'statusCode' => '0',
			'reason' => '',
			'location' => $event->event_location,
// 			'timeZone' =>$event->timezone
		);
		if($event->reason){
			$content['statusCode']="1";
			$content['reason']=$event->reason;
		}
		if($event->event_code == 'SORTING_CENTER_INBOUND_CALLBACK'){
			//入库回传
		}elseif ($event->event_code == 'CHECK_WEIGHT_CALLBACK'){
			//称重
			foreach ($order->farpackages as $package){
				$l_w_h=array(ceil($package->length),ceil($package->width),ceil($package->height));
				rsort($l_w_h);
				$notifyBizEventDTO['parcelList'][]=array(
					'dimensionUnit'=>'cm',
					'length'=>$l_w_h[0],
					'width'=>$l_w_h[1],
					'height'=>$l_w_h[2],
					'weight'=>$package->weight*1000,
					'weightUnit'=>'g'
				);
			}
			$content['extInfo']=json_encode($notifyBizEventDTO);
		}elseif ($event->event_code == 'CONFIRM_CALLBACK'){
			//核查
			$confirm_ext=array();
			$confirm_ext['confirmEvent']['trackNo']=$order->far_no;
			//目前默认批次是1
			$confirm_ext['confirmEvent']['batchId']='1';
			foreach ($order->fees as $fee){
				if($fee->fee_type=='1'){
					$notifyBizEventDTO[]=array(
						'code'=>$fee->fee_item_code,
						'name'=>$fee->fee_item_name,
						'quantity'=>$fee->quantity
					);
				}
			}
			if($order->service_code=='ePacket-FY'){
				$notifyBizEventDTO[]=array(
					'code'=>"logisticsExpressASP_EX0038",
					'name'=>"小包操作费",
					'quantity'=>"1"
				);
			}
			$confirm_ext['confirmEvent']['chargeItemArray']=$notifyBizEventDTO;
			
			$content['extInfo']=json_encode($confirm_ext);
		}elseif ($event->event_code == 'SORTING_CENTER_OUTBOUND_CALLBACK'){
			//出库
		}elseif ($event->event_code == 'SORTING_CENTER_HO_OUT_CALLBACK'){
			//承运商取件
			$notifyBizEventDTO=array('carrierName'=>'FAR','location'=>$event->location);
			$content['extInfo']=json_encode($notifyBizEventDTO);
		}elseif ($event->event_code == 'LINEHAUL_HO_AIRLINE_CALLBACK'){
			//交航失败
		}elseif ($event->event_code == 'LAST_MILE_GTMS_SIGNED_CALLBACK'){
			//派送
		}
		$content = json_encode($content);
		$sign = base64_encode(md5($content.'sbuSo0o1apQT86E3R6360yuP0DFSZbRd',true));
		//公共参数
		$request_data = array(
			'logistics_interface' => $content,
			'logistic_provider_id' => $order->warehouse_code,
			'data_digest' => $sign,
		);
		//print_r($request_data);exit;
		//请求方式设置
		$requestHeader = array (
			'Content-Type:application/x-www-form-urlencoded'
		);
		try {
			//请求接口，返回数据为json格式
			$r = Helper_Curl::post ( self::url, 'msg_type=CAINIAO_GLOBAL_PARCEL_EVENT_CALLBACK&'.http_build_query($request_data), $requestHeader );
// 			print_r ($r);
// 			exit;
		} catch (Exception $e) {
			return array (
				'success' => false,
				'msg' => '接口超时'
			);
		}
		return $r;
		exit;
	}
	static function notifyPostUploadRecordForm($data,$order){
		$content = array(
			'logisticsOrderCode' => $order->order_no,
			'currentCPResCode' => $order->warehouse_code,
			'eventTime' => date('Y-m-d H:i:s',time()),
			'operator' => 'test',
			'eventCode' => 'UPLOAD_RECORDFORM_CALLBACK',
			'statusCode' => 0,
			'reason' => '',
			'location' => ''
		);
		//上传备案证件
		$confirm_ext['recordForm'] = array(
			'recordFormType' => $data['recordFormType'],
			'recordFormData' => $data['recordFormData']
		);
		$content['extInfo']=json_encode($confirm_ext);
		$content = json_encode($content);
		$sign = base64_encode(md5($content.'sbuSo0o1apQT86E3R6360yuP0DFSZbRd',true));
		//公共参数
		$request_data = array(
			'logistics_interface' => $content,
			'logistic_provider_id' => $order->warehouse_code,
			'data_digest' => $sign,
		);
		
		//请求方式设置
		$requestHeader = array (
			'Content-Type:application/x-www-form-urlencoded'
		);
		try {
			//请求接口，返回数据为json格式
			$r = Helper_Curl::post ( self::url, 'msg_type=CAINIAO_GLOBAL_PARCEL_EVENT_CALLBACK&'.http_build_query($request_data), $requestHeader );
// 			print_r ($r);
// 			exit;
		} catch (Exception $e) {
			return array (
				'success' => false,
				'msg' => '接口超时'
			);
		}
		return $r;
		exit;
	}
}
