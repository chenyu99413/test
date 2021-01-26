<?php
class Helper_Label_Abcsp{
	/**
	 * @todo   ABC-SP
	 * @author stt
	 * @since  2021年1月6日13:17:10
	 * @link   #85054
	 */
	static function abcsp($order,$channel_id){
		$channel=Channel::find('channel_id = ?',$channel_id)->getOne();
		$out=Faroutpackage::find('order_id = ?',$order->order_id)->getSum('quantity_out');
		if($out > 1){
			$view['errormessage']='只支持一票一件';
			return $view;
		}
		//url
		$url='http://exp.oms.chigoose.com';
		$method = "/login/getToken";
		//key
		$appkey = 'FAR';
		//security
		$security = 'e82c2ee7194b3cd89546549b69a42567';
		$post_body = array(
			'appKey'=>$appkey,
			'security'=>$security
		);
		//获取token,2小时有效
		$gettoken_response=Helper_Curl::post($url.$method, http_build_query($post_body));
		$gettoken_response_arr = json_decode($gettoken_response,true);
		//成功
		if($gettoken_response_arr['resultCode']=='1'){
			//组织打单数据
			$time = date('Y-m-d H:i:s',time());
			//生成32位guid
			if ($order->guid){
				$guid = $order->guid;
			}else{
				$guid = Helper_Common::createguid(false);
			}
			$orderdetaillist = array();
			//$goods = Orderproduct::find('order_id = ?',$order->order_id)->getAll();
			//商品条数
			$product_count=count($order->product);
			//产品总数量
			$product_sum=Orderproduct::find('order_id=?',$order->order_id)->sum('product_quantity','product_sum')->getAll();
			$order_package = Faroutpackage::find ( 'order_id = ?', $order->order_id )->getOne();
			//使用的产品重量
			$product_weight=0;
			//平均重量
			$actweight=$order_package->weight_out/$product_sum['product_sum'];
			foreach ($order->product as $good){
				$weight=0;
				//递减数量
				$product_count=$product_count-1;
				//判断最后使用总数减去前面分配的重量，已达到总重量一致
				if($product_count==0){
					$weight=$order_package->weight_out-$product_weight;
				}else{
					$weight=$actweight*$good->product_quantity;
				}
				//组装内容
				$product_weight +=$weight;
				$orderdetaillist[]=array(
					//参考我们原来发给IB预报一样处理方式，从产品编号库里自动提取编号匹配，不要用HS Code
					'itemCode'=>date('Ymd').rand(100000, 999999),
					//中文品名
					'itemName'=>$good->product_name_far,
					//英文品名
					'itemEnglishName'=>$good->product_name_en_far,
					//商品单价
					'unitPrice'=>$good->declaration_price,
					//商品数量
					'itemCount'=>$good->product_quantity,
					//产品净重
					'goodsNetWeight'=>sprintf('%.3f',$weight*0.95),
					//产品毛重
					'goodsRoughWeight'=>sprintf('%.3f',$weight),
					//USD ISO 数字三位 编码
					'bargainCurrency'=>'840',
					//申报数量
					'declareCount'=>$good->product_quantity,
					//申报单位
					'declareMeasureUnit'=>'件'
				);
			}
			$country = Country::find('code_word_two=?',$order->consignee_country_code)->getOne();
			$state = '';
			//判断收件国家是否是US和CA
			if($order->consignee_country_code=='US' || $order->consignee_country_code=='CA'){
				//将收件人州转为二字码
				$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)),strtoupper(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
				if($states->isNewRecord()){
					$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州';
					return $view;
				}else{
					$state=$states->province_code_two;
				}
			}else{
				$state=$order->consignee_country_code;
			}
			$message = array(
				'OMSOrder'=>array(
					'orderNo' => $order->ali_order_no,
					//测试是APIZY，正式是...FAR
					'clientCode' => 'FAR',
					//I-进口； E -出口
					'ieFlag' => 'E',
					//业务类型: 出口转运代收货款（EXTRCOD)
					'businessType' => 'EXTRCOD',
					//业务模式编码
					'businessModelCode' => $channel->account,
					//订单总金额
					'orderTotalAmount' => $order->total_amount,
					//订单商品总件数
					'orderTotalCount' => $product_sum['product_sum'],
					//订单时间
					'orderTime' => date('Y-m-d H:i:s',time()),
					//毛重
					'roughWeight' => $order_package->weight_out,
					//净重
					'netWeight' => sprintf('%.3f',$order_package->weight_out*0.95),
					//收件人
					'consignee' =>array(
						//收件人国家
						'consigneeCountry'=>$order->consignee_country_code,
						'consigneeName'=>$order->consignee_name1,
						'consigneePhone'=>$order->consignee_mobile,
						'consigneeEmail'=>'declaration@far800.com',
						'consigneePost'=>$order->consignee_postal_code,
						'consigneeAddress'=>$order->consignee_street1.' '.$order->consignee_street2,
						'province'=>$state,
						'city'=>$order->consignee_city
					),
					'destinationCountry'=>$country->chinese_name,
					'destinationCountryCode'=>$order->consignee_country_code,
					'orderDetailList'=>$orderdetaillist,
					'codCurrency'=>'840',
					'codFee'=>$order->total_amount
				),
				//是否强制刷新 1强制刷新
				'isUpdate' => 0,
			);
			
			$data = array (
				//系统分配的用户身份ID
				'appKey' => $appkey,
				//传输报文的报文数据格式
				'dataFormat' => 'json',
				'message'=>json_encode($message),
				//请求调用的指定接口的接口名称
				'method' => 'Chigoose.order.addorder',
				//32位guid
				'notifyId' => $guid,
				//调用时间的时间戳(具体到秒)
				'timestamp' => $time,
				//版本号
				'version' => '1.0'
			);
			ksort($data);
			//记录发送报文
			$log=new OrderLog(array(
				'order_id'=>$order->order_id,
				'staff_id'=>MyApp::currentUser('staff_id'),
				'staff_name'=>MyApp::currentUser('staff_name'),
				'comment'=>'abcsp订单报文：'.json_encode($data)
			));
			//保存
			$log->save();
			$sign = $security;
			foreach ($data as $key => $value){
				$sign .= $key.'='.$value.':';
			}
			$sign = substr($sign,0,strlen($sign)-1);
			$sign .= $security;
			$label_header = array (
				'token:' .$gettoken_response_arr['resultData']['token'],
				'Sign:'.strtoupper(md5($sign))
			);
			$method = "/order/addorder";
			$result=Helper_Curl::post($url.$method, http_build_query($data), $label_header);
			QLog::log('abcsp下单回执：'.$result);
			//测试返回
			$error_message = $result;
			$result = json_decode($result,true);
			
			if($result['resultCode']==1){
				$resultdata = json_decode($result['resultData'],true);
				//末端单号
				$tracking_code = $resultdata['mailNo'];
				//末端单号超出26位
				if (strlen($tracking_code)>26){
					//把前八位截掉
					//例如4209160792612927005430000027406270->92612927005430000027406270
					$tracking_code = substr($tracking_code , 8);
				}
				//原订单信息
				$order->guid = $guid;
				$order->tracking_no = $tracking_code;
				$order->save();
				//保存子单信息，用于交货核查
				$subcode=new Subcode();
				$subcode->order_id=$order->order_id;
				$subcode->sub_code=$tracking_code;
				$subcode->save();
				$dir=Q::ini('upload_tmp_dir');
				@Helper_Filesys::mkdirs($dir);
				$target=$dir.DS.$tracking_code.'.pdf';
				$count=0;
				// 
				while($count < 3){
					// 
					$source = file_get_contents($resultdata['pfd']);
					// 
					if (!$source){
						//接口生成pdf慢,增加时间 
						sleep(8);
						$count++;
						QLog::log($source);
					}else{
						break;
					}
				}
				//判断面单是否存在
				if ($source){
					file_put_contents($target,$source);
					//旋转面单 90
					Helper_PDF::rotate($target,$target);
					//旋转面单 90
					Helper_PDF::rotate($target,$target);
					//标签标识
					if($channel->label_sign){
						//jpg
						exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$target} -append {$target}.jpg");
						Helper_PDF::abcsp($target,$channel->label_sign);
						//成功删除
						@unlink($target.'.jpg');
					}
					$uploadoss = new Helper_AlipicsOss();
					//上传到oss
					$miandan_data = $uploadoss->uploadAlifiles($tracking_code.'.pdf');
					if ($uploadoss->doesExist($tracking_code.'.pdf')){
						//上传成功，删除
						unlink($dir.DS.$tracking_code.'.pdf');
					}
				}else{
					$view['errormessage'] = "获取面单失败";
					return $view;
				}
				
				$view['errormessage']='';
				$view['account']='ABC-SP';
			}else{
				$view['errormessage']=$error_message;
			}
		}else{
			//失败
			$view['errormessage']='ABC-SP token 获取失败';
		}
		return $view;
	}
}