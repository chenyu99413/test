<?php
class Helper_Label_ShunCheng{
	/**
	 * @todo   ShunCheng账号渠道
	 * @author stt
	 * @since  2021年1月15日13:29:56
	 * @link   #82496
	 */
	static function shuncheng($order,$channel_id){
		set_time_limit(0);
		$channel=Channel::find('channel_id = ?',$channel_id)->getOne();
		$out=Faroutpackage::find('order_id = ?',$order->order_id)->getSum('quantity_out');
		if($out > 1){
			$view['errormessage']='该产品只支持一票一件';
			return $view;
		}
		//url
		$url = 'http://47.114.12.249/api/LvsParcels';
		$token = 'U0hGWTpTSEZZMjM1Njg5';
		$header=array(
			//正式
			'Authorization:basic U0hGWTpTSEZZMjM1Njg5',
			'Content-Type:text/json; charset=utf-8'
		);
		//产品
		//$products = Orderproduct::find('order_id = ?',$order->order_id)->getAll();
		//产品总数量 
		$product_sum=Orderproduct::find('order_id=?',$order->order_id)->sum('product_quantity','product_sum')->getAll();
		//出库包裹
		$faroutpackage = Faroutpackage::find('order_id = ?',$order->order_id)->getOne();
		//货品信息
		$customs=array();
		//使用的产品重量
		$product_weight=0;
		//商品条数
		$product_count=count($order->product);
		//平均重量
		$actweight=$faroutpackage->weight_out/$product_sum['product_sum'];
		foreach($order->product as $product){
			$weight=0;
			//递减数量
			$product_count=$product_count-1;
			//判断最后使用总数减去前面分配的重量，已达到总重量一致
			if($product_count==0){
				$weight=$faroutpackage->weight_out-$product_weight;
			}else{
				$weight=$actweight*$product->product_quantity;
			}
			//组装内容
			$customs[]=array(
				//sku
				'Sku' => date('Ymd').rand(100000, 999999),
				//物品的中文描述----使用中文品名
				'ChineseContentDescription' => $product->product_name_far,
				//物品描述----------使用英文品名
				'ItemContent' => $product->product_name_en,
				//包裹里不同物品的总数
				'ItemCount' => $product->product_quantity,
				//总价值
				'Value'=>$product->declaration_price,
				//币种
				'Currency' => 'USD',
				//净重
				'Weight' => round($weight*0.95,3)*1000,
				'weightKG' => round($weight*0.95,3),
				//箱号
				'trackNum' => $order->ali_order_no,
			);
			$product_weight +=$weight;
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
		}
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
		$arr = array(
			'ContractId' => 1,
			'FormatType' => $channel->account,
			'TaxID' => $order->tax_payer_id,
			"CustomerToken" => $token,
			'OrderNumber' => $order->ali_order_no,
			'RecipientName' => $order->consignee_name1,
			//housenumber地址2
			'RecipientHouseNumber' => $order->consignee_street2,
			//street是地址1 RecipientStreet必填
			'RecipientStreet' => $order->consignee_street1,
			'RecipientZipCode' => $order->consignee_postal_code,
			'RecipientCity' => $order->consignee_city,
			'RecipientState' => $state,
			'RecipientCountry' => $order->consignee_country_code,
			'PhoneNumber' => $order->consignee_mobile,
			'Email' => $order->consignee_email,
			'Customs' => $customs,
			'Weight' => $faroutpackage->weight_out,
			"Width" => $faroutpackage->width_out,
			"Length" => $faroutpackage->length_out,
			"Height" => $faroutpackage->height_out,
		);
		//正式系统 YQ无忧普货 ORD 77 发发件人信息
		if ($channel->channel_id==77){
			if($channel->sender_id){
				$sender_id = explode(',', $channel->sender_id);
				$sender_id = $sender_id[rand(0, count($sender_id)-1)];
				$sender=Sender::find('sender_id = ?',$sender_id)->getOne();
				if(!$sender->isNewRecord()){
					//寄件人姓名
					$arr['SenderName']=$sender->sender_name;
					//寄件人地址
					$arr['SenderAddress']=$sender->sender_address;
					//备用
					$arr['SenderSequence']=1;
					//邮编
					$arr['SenderZipCode']=$sender->sender_zip_code;
					//省
					$arr['SenderState']=$sender->sender_province;
					//城市
					$arr['SenderCity']=$sender->sender_city;
					//国家
					$arr['SenderCountry']=$sender->sender_country;
					//电话
					$arr['SenderPhone']=$sender->sender_phone;
					
				}
			}
		}
		$body_json = json_encode($arr);
		$log = new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			'comment'=>'shuncheng订单报文：'.$body_json
		));
		$log->save();
		QLog::log($order->ali_order_no.$body_json);
		
		$response=Helper_Curl::post($url,$body_json,$header);//正式地址
		QLog::log(print_r($response, true));
		$result=json_decode($response,true);
		
		if(is_null($result)){
			$view['errormessage']='面单获取失败';
		}else{
			if(@$result['Customs'][0]['label']){
				$view = self::saveshuncheng($order, $result, $channel);
			}else{
				$view['errormessage']=json_encode($result);
				//查询接口 
				$getorderinfo_url = 'http://47.114.12.249/api/GetOrderInfo';
				Qlog::log('GetOrderInfo'.$getorderinfo_url.'?formatType='.$channel->account.'&ordernumber='.$order->ali_order_no);
				$getorderinfo_result=Helper_Curl::get1($getorderinfo_url.'?formatType='.$channel->account.'&ordernumber='.$order->ali_order_no, $header);
				Qlog::log('GetOrderInforesult'.$getorderinfo_result);
				$getorderinfo_arr = json_decode($getorderinfo_result,true);
				if(@$getorderinfo_arr['Customs'][0]['label']){
					$view = self::saveshuncheng($order, $getorderinfo_arr, $channel);
				} 
			}
		}
		return $view;
	}
	static function saveshuncheng($order,$result,$channel){
		//保存发件人抬头到订单表
		//原订单信息
		$order->tracking_no = $result['Customs'][0]['label'];
		$order->save();
		//保存子单信息，用于交货核查
		$subcode=new Subcode();
		$subcode->order_id=$order->order_id;
		$subcode->sub_code=$result['Customs'][0]['label'];
		$subcode->save();
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$target=$dir.DS.$result['Customs'][0]['label'].'.pdf';
		$count=0;
		//
		while($count < 3){
			//
			$source = file_get_contents($result['Customs'][0]['pdfurl']);
			//
			if (!$source){
				sleep(3);
				$count++;
				QLog::log($source);
			}else{
				break;
			}
		}
		//判断面单是否存在
		if ($source){
			file_put_contents($target,$source);
			Helper_PDF::rotate($target,$target);//旋转面单
			//标签标识
			if($channel->label_sign){
				//jpg
				exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$target} -append {$target}.jpg");
				Helper_PDF::shuncheng($target,$channel->label_sign);
				@unlink($target.'.jpg');
			}
			$uploadoss = new Helper_AlipicsOss();
			//上传到oss
			$miandan_data = $uploadoss->uploadAlifiles($result['Customs'][0]['label'].'.pdf');
			if ($uploadoss->doesExist($result['Customs'][0]['label'].'.pdf')){
				//上传成功，删除
				unlink($dir.DS.$result['Customs'][0]['label'].'.pdf');
			}
		}
		$view['errormessage']='';
		$view['account']='shuncheng';
		return $view;
	}
}