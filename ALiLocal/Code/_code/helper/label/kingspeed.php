<?php
class Helper_Label_Kingspeed{
	/**
	 * @todo   Kingspeed账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function kingspeed($order,$channel_id,$rorder = 0){
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
			$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)),strtoupper(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州/省';
				return $view;
			}else{
				$state=$states->province_code_two;
			}
		}else {
			$view['errormessage']='该运输方式只支持收件国[US]';
			return $view;
		}
		$faroutpackage = Faroutpackage::find('order_id = ?',$order->order_id)->getOne();
		$wares = Orderproduct::find('order_id = ?',$order->order_id)->getAll();
		$sender_name=$order->sender_name1;
		$sender_phone=$order->sender_mobile;
		$sender_city=$order->sender_city;
		$sender_area='';
		$sender_province = $order->sender_state_region_code;
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_country_code=$order->sender_country_code;
		$sender_company = $order->sender_name2;
		$channel=Channel::find('channel_id = ?',$channel_id)->getOne();
		if(!$channel->account){
			$view['errormessage']='渠道账号不存在，请填写渠道账号';
			return $view;
		}
		if($channel->sender_id>0){
			$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
			if(!$sender->isNewRecord()){
				$sender_name=$sender->sender_name;
				$sender_phone=$sender->sender_phone;
				$sender_city=$sender->sender_city;
				$sender_address=$sender->sender_address;
				$sender_zip_code=$sender->sender_zip_code;
				$sender_country_code='CN';
				$sender_province=$sender->sender_province;
				$sender_company=$sender->sender_company;
			}
		}
		$shipper = array(
			'name' => $sender_name,
			// 	        'companyName'=>$sender_company,
			'countryCode' => $sender_country_code,
			'stateProvince' => $sender_province,
			'city' => $sender_city,
			'address' => $sender_address,
			'postcode' => $sender_zip_code,
			'phone' => $sender_phone
		);
		$phone='';
		if($order->consignee_mobile && $order->consignee_telephone){
			if($order->consignee_mobile == $order->consignee_telephone){
				$phone=$order->consignee_mobile;
			}else {
				$phone=str_replace(' ', '', $order->consignee_mobile).' '.str_replace(' ', '', $order->consignee_telephone);
			}
		}
		if(!$phone){
			$phone=!empty($order->consignee_mobile)?$order->consignee_mobile:$order->consignee_telephone;
		}
		$consignee = array(
			'name'=>$order->consignee_name1,
			'companyName'=>$order->consignee_name1,
			'countryCode'=>$order->consignee_country_code,
			'stateProvince'=>$state,
			'city'=>$order->consignee_city,
			'address'=>$order->consignee_street1.' '.$order->consignee_street2,
			'postcode'=>$order->consignee_postal_code,
			'phone'=>$phone
		);
		$product=array();
		foreach($wares as $ware){
			$product[]=array(
				'declareNameCn'=>$ware->product_name_far,
				'declareNameEn'=>$ware->product_name_en_far,
				'declareValue'=>$ware->declaration_price,
				'quantity'=>$ware->product_quantity
			);
		}
		$boxes = array();
		$boxes[]=array(
			'code' => '1',
			'weight' => $faroutpackage->weight_out<0.454?0.454:$faroutpackage->weight_out,
			"width" => $faroutpackage->width_out,
			"length" => $faroutpackage->length_out,
			"height" => $faroutpackage->height_out,
			'declareProducts' => $product
		);
		$arr = array(
			'orderNo' => $order->ali_order_no,
			'shipper' => $shipper,
			'consignee' => $consignee,
			'boxes' => $boxes,
			'productCode' => $channel->account,
			'labelType' => 'eachPdf'
		);
		$body_json = json_encode($arr);
		// 	    dump($body_json);exit();
		$log = new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			'comment'=>'EST订单报文：'.$body_json
		));
		$log->save();
		QLog::log($order->ali_order_no.$body_json);
		$header=array(
			//     	    'Authorization:KSCESHI;5A5644D844F829CE789614A5C382CDF4',//测试
			'Authorization:KS000214;A407962C9A47F57BE734B761AD219753',//正式
			'Content-Type:application/json'
		);
		Helper_Curl::$connecttimeout=300;
		Helper_Curl::$timeout=300;
		try {
			// 	    $response=Helper_Curl::post('http://apitest.kingspeed.com/label',$body_json,$header);//测试地址
			$response=Helper_Curl::post('http://apis.kingspeed.com/label',$body_json,$header);//正式地址
			QLog::log(print_r($response, true));
			$result=json_decode($response,true);
		} catch ( Exception $e ) {
			$view['errormessage']='接口超时';
			return $view;
		}
		
		if(is_null($result)){
			$view['errormessage']='面单获取失败';
		}else{
			if($result['success']==true){
				//保存发件人抬头到订单表
				if($rorder !== 0){
					//重发
					$rorder->new_tracking_no = $result['trackingNumber'];
					//保存
					$rorder->save();
				}else{
					//原订单信息
					$order->tracking_no = $result['trackingNumber'];
					$order->sender_id = @$sender->sender_id ? $sender->sender_id : '';
					$order->save();
				}
				if($rorder === 0){
					//保存子单信息，用于交货核查
					$subcode=new Subcode();
					$subcode->order_id=$order->order_id;
					$subcode->sub_code=$result['trackingNumber'];
					$subcode->save();
				}else{
					//删除原单号
					ReturnSubcode::find('order_id=?',$rorder->return_order_id)->getAll()->destroy();
					//保存退件子单信息，用于交货核查
					$subcode=new ReturnSubcode();
					$subcode->order_id=$rorder->return_order_id;
					$subcode->sub_code=$result['trackingNumber'];
					$subcode->save();
				}
				
				$box=$result['boxes'][0];
				$box['labelFileBody'];
				$dir=Q::ini('upload_tmp_dir');
				@Helper_Filesys::mkdirs($dir);
				$target=$dir.DS.$box['trackingNumber'].'.pdf';
				file_put_contents($target,base64_decode($box['labelFileBody']));
				if($channel->label_sign){
					exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$target} -append {$target}.jpg");
					Helper_PDF::kingspeed($target,$channel->label_sign);
					@unlink($target.'.jpg');
				}
				if ($rorder === 0){
					//#84640
					if($order->customer->customs_code=='HDKJ'){
						$recordformdata = file_get_contents($target);
						//组合数据
						$data=array(
							'order_no'=>$order->far_no,
							'waybill_code'=>$order->tracking_no,
							'customer'=>$order->customer->customs_code,
							'carrier'=>'UPS',
							'label'=>base64_encode($recordformdata)
						);
						//保存日志数据
						$data_clone = $data;
						//面单数据过长
						$data_clone['label'] = md5(base64_encode(file_get_contents($target)));
						//发送数据保存
						$datalog=new OrderLog(array(
							'order_id'=>$order->order_id,
							'staff_id'=>MyApp::currentUser('staff_id'),
							'staff_name'=>MyApp::currentUser('staff_name'),
							'comment'=>'快件专线更新运单号：'.json_encode($data_clone)
						));
						$datalog->save();
						//发送数据
						$response=Helper_Curl::post('http://kuaijian.far800.com/index.php?controller=cron&action=updatewaybillcode', json_encode($data));
						//返回数据保存
						$responselog=new OrderLog(array(
							'order_id'=>$order->order_id,
							'staff_id'=>MyApp::currentUser('staff_id'),
							'staff_name'=>MyApp::currentUser('staff_name'),
							'comment'=>'快件专线更新运单号返回：'.$response
						));
						$responselog->save();
					}
					
					
				}
					
				$uploadoss = new Helper_AlipicsOss();
				//上传到oss
				$miandan_data = $uploadoss->uploadAlifiles($box['trackingNumber'].'.pdf');
				if ($uploadoss->doesExist($box['trackingNumber'].'.pdf')){
					//上传成功，删除
					unlink($dir.DS.$box['trackingNumber'].'.pdf');
				}
				$view['errormessage']='';
				$view['account']='US-FY';
			}else{
				$view['errormessage']=$result['message'];
			}
		}
		return $view;
	}
}