<?php
class Helper_Label_Fsp{
	/**
	 * @todo   FSP账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function fsp($order,$channel_id){
		//1
		set_time_limit(0);
		$out=Faroutpackage::find('order_id = ?',$order->order_id)->getSum('quantity_out');
		if($out > 1){
			$view['errormessage']='该产品只支持一票一件';
			return $view;
		}
		$state='';
		//判断收件国家是否是US
		if($order->consignee_country_code=='US'){
			//将收件人州转为二字码
			$states=Uscaprovince::find('province_name=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州/省';
				return $view;
			}else{
				$state=$states->province_code_two;
			}
		}else {
			$view['errormessage']='该产品只支持收件国[US]';
			return $view;
		}
		$sender_name=$order->sender_name1.($order->sender_name2?' '.$order->sender_name2:'');
		$sender_phone=$order->sender_mobile.($order->sender_telephone?'/'.$order->sender_telephone:'');
		$sender_city=$order->sender_city;
		$sender_area='';
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_country_code=$order->sender_country_code;
		$channel=Channel::find('channel_id = ?',$channel_id)->getOne();
		if($channel->sender_id>0){
			$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
			if(!$sender->isNewRecord()){
				$sender_name=$sender->sender_name.($sender->sender_company?' '.$sender->sender_company:'');
				$sender_phone=$sender->sender_phone;
				$sender_city=$sender->sender_city;
				$sender_address=$sender->sender_address;
				$sender_zip_code=$sender->sender_zip_code;
				$sender_country_code='CN';
			}
		}
		$product=Orderproduct::find('order_id = ?',$order->order_id)->getAll();
		$token="2rcBrK5k5QJFoejbaCs0";
		//$url='http://api.fullspeedparcel.com/?c=apiTms&a=createOrders';
		$url='http://api.fullspeedparcel.com/?c=apiTms&a=createOrders';
		$brr=array();
		foreach ($product as $p){
			$sku=date('Ymd').rand('100000','999999');
			$brr[]=array(
				"proName"=>$p->product_name_en_far,
				"proNum"=>$p->product_quantity,
				"proPrice"=>$p->declaration_price,
				"proCnName"=>$p->product_name_far,
				"sku"=>$sku
			);
		}
		$arr=array();
		$arr['token']=$token;
		$arr['timestamp']=time();
		$arr['shippingCode']='UPS-GROUND';
		if(in_array($order->department_id, array('7','8','23'))){
			$arr['warehouseId']='92';
		}else {
			$arr['warehouseId']='44';
		}
		$arr['needPdf']='1';
		$arr['orderNum']=$order->ali_order_no;
		$arr['weight']=$order->weight_label;
		$arr['realname']=$order->consignee_name1.($order->consignee_name2?' '.$order->consignee_name2:'');
		$arr['telephone']=$order->consignee_mobile;
		$arr['countryCode']=$order->consignee_country_code;
		$arr['province']=$state;
		$arr['city']=$order->consignee_city;
		$arr['area']='';
		if(is_numeric($order->consignee_street2)){
			$arr['address']=$order->consignee_street1.$order->consignee_street2;
		}else{
			$arr['address']=$order->consignee_street1;
		}
		$arr['name2']=$order->consignee_street2;
		$arr['postcode']=$order->consignee_postal_code;
		$arr['sendName']=$sender_name;
		$arr['sendPhone']=$sender_phone;
		$arr['senderCountryCode']=$sender_country_code;
		$arr['senderCity']=$sender_city;
		$arr['sendAddress']=$sender_address;
		$arr['sendStreetNum']='';
		$arr['sendZipcode']=$sender_zip_code;
		$arr['proDesc']=json_encode($brr);
		QLog::log('fsp订单数据：'.json_encode($arr));
		$postData=self::getPostData($arr);
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			'comment'=>'fsp订单报文：'.$postData
		));
		$log->save();
		Helper_Curl::$connecttimeout='120';
		Helper_Curl::$timeout='120';
		$return=Helper_Curl::post($url, $postData);
		QLog::log('fsp下单回执：'.$return);
		$return=json_decode($return,true);
		if($return['code']=='0' || $return['code']=='-1011'){
			$params=array(
				'token'=>$token,
				'timestamp'=>time(),
				'needPdf'=>1,
				'orderNum'=>$order->ali_order_no
			);
			$r=Helper_Curl::post('http://api.fullspeedparcel.com/?c=apiTms&a=getShippingInfo',Order::getPostData($params));
			QLog::log('fsp获取面单回执：'.$r);
			$r=json_decode($r,true);
			if($r['code']=='0'){
				//推送fsp长宽高重量
				$farout = Faroutpackage::find('order_id = ?',$order->order_id)->getOne();
				$key="ocR6fZSESVR1T29Xly6o";
				$arr_weight=array();
				$arr_weight['token']="EOmn3QvLCrq1nCcgl76J";
				$arr_weight['timestamp']=time();
				$arr_weight['shippingNum']=$r['data']['shippingNum'];
				$arr_weight['weight']=$farout->weight_out;
				$arr_weight['length']=floor($farout->length_out);
				$arr_weight['width']=floor($farout->width_out);
				$arr_weight['height']=floor($farout->height_out);
				$postData=self::getPostData($arr_weight,$key);
				$return_weight=Helper_Curl::post('http://api.fullspeedparcel.com/?c=apiTms&a=stockWeight', $postData);
				$return_weight=json_decode($return_weight,true);
				$log_weight = new OrderLog();
				$log_weight->order_id = $order->order_id;
				$log_weight->staff_id = MyApp::currentUser('staff_id');
				$log_weight->staff_name = MyApp::currentUser('staff_name');
				$log_weight->comment = '推送fsp长：'.$farout->length_out.'，宽：'.$farout->width_out.'，高：'.$farout->height_out.
				'，重量：'.$farout->weight_out.'，返回结果：'.$return_weight['message'];
				$log_weight->save();
				if($return_weight['code'] != '0'){
					$view['errormessage']='推送fsp长：'.$farout->length_out.'，宽：'.$farout->width_out.'，高：'.$farout->height_out.
					'，重量：'.$farout->weight_out.'，返回结果：'.$return_weight['message'];
					return $view;
				}
				$order->ems_order_id=$r['data']['serialNum'];
				$order->tracking_no=$r['data']['shippingNum'];
				//保存发件人抬头到订单表
				$order->sender_id = @$sender->sender_id ? $sender->sender_id : '';
				$order->save();
				//保存子单信息，用于交货核查
				$subcode=new Subcode();
				$subcode->order_id=$order->order_id;
				$subcode->sub_code=$r['data']['shippingNum'];
				$subcode->save();
				$dir=Q::ini('upload_tmp_dir');
				@Helper_Filesys::mkdirs($dir);
				$target=$dir.DS.$r['data']['shippingNum'].'.pdf';
				file_put_contents($target,base64_decode($r['data']['shippingInfo']));
				// 	            if(in_array(MyApp::currentUser('department_id'), array('7','8','23'))){
				// 	                Helper_PDF::fsp($target);
				// 	            }
				$view['errormessage']='';
				$view['account']='US-FY';
			}else {
				$view['errormessage']=$r['message'].($r['code']=='-1019'?'请稍后重试':'');
			}
		}else {
			$view['errormessage']=$return['message'];
		}
		return $view;
	}
	static function getPostData($postData,$key="AJsFkok2ty31TaXPOXTD")
	{
		ksort($postData); //除sign参数外， 数组按字母排序
		$strPara = http_build_query($postData);
		$sign = md5($strPara.$key);//签名
		$postData['sign'] = $sign;
		return http_build_query($postData);
	}
}