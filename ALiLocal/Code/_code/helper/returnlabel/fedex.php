<?php
class Helper_ReturnLabel_Fedex{
	/**
	 * @todo   fedex账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function fedex($order,$channel_id){
		$outpackage=Returnoutpackage::find('return_order_id = ?',$order->return_order_id)->getAll();
		if(count($outpackage)==0){
			$view['errormessage']='无包裹，不能出库';
			return $view;
		}
		$quantity='';
		foreach ($outpackage as $o){
			$quantity += $o->quantity;
		}
		//判断地址2是否纯数字
		if(is_numeric($order->consignee_street2)){
			//纯数字就拼接在一起
			$address_str = $order->consignee_street1.$order->consignee_street2;
			//超过120个字符
			//1
			if(strlen($address_str) > 120){
				$view['errormessage']='收件人地址超长，地址的总字符不超过120';
				return $view;
			}else{
				$address[] = $address_str;
			}
		}else{
			$address=Helper_Common::splitAddressfedex($order->consignee_street1.' '.$order->consignee_street2);
			if(count($address)>3){
				$view['errormessage']='收件人地址超长，地址1+地址2的总字符不超过70';
				return $view;
			}
		}
		//做报错提示
		//收件人手机号与电话不一致
		if ($order->consignee_telephone != $order->consignee_mobile){
			$view['errormessage']='收件人手机号与电话不一致!';
			return $view;
		}
		$consignee_street[]=isset($address[0])?$address[0]:$address[1];
		if(isset($address[1])){
			$consignee_street[]=$address[1];
		}
		$product=ReturnOrderproduct::find('return_order_id = ?',$order->return_order_id)->getAll();
		$product_qua=ReturnOrderproduct::find('return_order_id = ?',$order->return_order_id)->getSum('product_quantity');
		$pro=array();
		$invoice=array('items'=>array(),'total'=>'');
		$z=1;
		$weight=Returnoutpackage::find('order_id = ?',$order->order_id)->getSum('weight_out');
		foreach ($product as $p){
			$weight_sum=$order->packing_type=='PAK'?$order->weight_label:$weight;
			$pro=array(
				'v15:NumberOfPieces'=>'1',
				'v15:Description'=>$p->product_name_en_far,
				'v15:CountryOfManufacture'=>'CN',
				'v15:Weight'=>array(
					'v15:Units'=>'KG',
					'v15:Value'=>sprintf('%.3f',$weight_sum/$product_qua)
				),
				'v15:Quantity'=>$p->product_quantity,
				'v15:QuantityUnits'=>'EA',
				'v15:UnitPrice'=>array(
					'v15:Currency'=>'USD',
					'v15:Amount'=>sprintf('%.2f',$p->declaration_price),
				)
			);
			$invoice['total']+=round($p->product_quantity*$p->declaration_price,2);
			//FAR做Invoice
			if($z<4){
				$invoice['items'][]=array(
					'description'=>$p->product_name_far.' '.$p->product_name_en_far.' '.$p->hs_code_far,
					'quantity'=>$p->product_quantity,
					'price'=>$p->declaration_price,
					'itotal'=>round($p->product_quantity*$p->declaration_price,2),
				);
			}
			$z++;
		}
		if($order->packing_type=='PAK'){
			$packing_type='FEDEX_PAK';
		}else {
			// 	        $packing_type='FEDEX_BOX';
			$packing_type='YOUR_PACKAGING';
		}
		$state=$order->consignee_city;
		//判断收件国家是否是US和CA
		if($order->consignee_country_code=='US' || $order->consignee_country_code=='CA'){
			//将收件人州转为二字码
			$states=Uscaprovince::find('province_name=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州/省';
				return $view;
			}else{
				$state=$states->province_code_two;
			}
		}
		$j=1;
		//fedex测试账号信息
		// 	    $key='vOSDgWEXV7QnRbxP';
		// 	    $password='h2XRZgneNCQALyHjMP682U5re';
		// 	    $accountnumber='510087500';
		// 	    $meternumber='100421805';
		//fedex假发生产账号信息
		// 	    $key='f5XzZpzEwekZ8TJv';
		// 	    $password='ILI1RWIQVhrjIZFBY5CqNP41i';
		// 	    $accountnumber='631526993';
		// 	    $meternumber='250139501';
		//fedex创梦谷生产账号信息
		$key='Y8DNyhcsybFkXWDy';
		$password='2CLHx5wETXxDSCe1alwpS6Jxn';
		$accountnumber='497335868';
		$meternumber='251395350';
		$sender_name=$order->sender_name1;
		$sender_company=$order->sender_name2;
		$sender_phone=$order->sender_mobile.($order->sender_telephone?'/'.$order->sender_telephone:'');
		$sender_province=$order->sender_state_region_code;
		$sender_city=$order->sender_city;
		$sender_area='';
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_email=$order->sender_email;
		$sender_country_code=$order->sender_country_code;
		$channel=ReturnChannel::find('channel_id = ?',$channel_id)->getOne();
		if($channel->sender_id){
			$sender_id = explode(',', $channel->sender_id);
			$sender_id = $sender_id[rand(0, count($sender_id)-1)];
			$sender=Sender::find('sender_id = ?',$sender_id)->getOne();
			if(!$sender->isNewRecord()){
				$sender_name=$sender->sender_name;
				$sender_company=$sender->sender_company;
				$sender_phone=$sender->sender_phone;
				$sender_province=$sender->sender_province;
				$sender_city=$sender->sender_city;
				$sender_area=$sender->sender_area;
				$sender_address=$sender->sender_address;
				$sender_zip_code=$sender->sender_zip_code;
				$sender_email=$sender->sender_email;
				$sender_country_code='CN';
			}
		}
		foreach ($outpackage as $out){
			for ($i=0;$i<$out->quantity;$i++){
				$mastertrackingid='';
				if($order->tracking_no){
					$mastertrackingid  =array(
						'v15:TrackingIdType'=>$order->fedex_tracking_id_type,
						'v15:FormId'=>$order->fedex_form_id,
						'v15:TrackingNumber'=>$order->tracking_no
					);
				}
				$res = array(
					'soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v15="http://fedex.com/ws/ship/v15"' => array(
						'soapenv:Header' =>'',
						'soapenv:Body' => array(
							'v15:ProcessShipmentRequest' => array(
								'v15:WebAuthenticationDetail' => array(
									'v15:UserCredential'=>array(
										'v15:Key'=>$key,
										'v15:Password'=>$password,
									),
								),
								'v15:ClientDetail'=>array(
									'v15:AccountNumber'=>$accountnumber,
									'v15:MeterNumber'=>$meternumber
								),
								'v15:TransactionDetail'=>array(
									'v15:CustomerTransactionId'=>'ProcessShipmentRequest_v15'
								),
								'v15:Version'=>array(
									'v15:ServiceId'=>'ship',
									'v15:Major'=>'15',
									'v15:Intermediate'=>'0',
									'v15:Minor'=>'0'
								),
								'v15:RequestedShipment'=>array(
									'v15:ShipTimestamp'=>date("Y-m-d").'T'.date("H:i:s").'+08:00',
									'v15:DropoffType'=>'REGULAR_PICKUP',
									'v15:ServiceType'=>'INTERNATIONAL_PRIORITY',
									'v15:PackagingType'=>$packing_type,
									'v15:TotalWeight'=>array(
										'v15:Units'=>'KG',
										'v15:Value'=>$order->weight_label
									),
									'v15:PreferredCurrency'=>'USD',
									'v15:Shipper'=>array(
										'v15:Contact'=>array(
											'v15:PersonName'=>$sender_name,
											'v15:CompanyName'=>$sender_company,
											'v15:PhoneNumber'=>$sender_phone,
											'v15:EMailAddress'=>$sender_email
										),
										'v15:Address'=>array(
											'v15:StreetLines'=>$sender_address,
											'v15:City'=>$sender_city,
											'v15:StateOrProvinceCode'=>$sender_province,
											'v15:PostalCode'=>$sender_zip_code,
											'v15:CountryCode'=>$sender_country_code
										),
									),
									'v15:Recipient'=>array(
										'v15:Contact'=>array(
											'v15:PersonName'=>$order->consignee_name1,
											'v15:CompanyName'=>$order->consignee_name1 != $order->consignee_name2?$order->consignee_name2:'',
											'v15:PhoneNumber'=>$order->consignee_mobile,
											'v15:EMailAddress'=>$order->consignee_email
										),
										'v15:Address'=>array(
											'v15:StreetLines'=>$consignee_street,
											'v15:City'=>$order->consignee_city,
											'v15:StateOrProvinceCode'=>$state,
											'v15:PostalCode'=>$order->consignee_postal_code,
											'v15:CountryCode'=>$order->consignee_country_code
										),
									),
									'v15:ShippingChargesPayment'=>array(
										'v15:PaymentType'=>'SENDER',
										'v15:Payor'=>array(
											'v15:ResponsibleParty'=>array(
												'v15:AccountNumber'=>$accountnumber,
											),
										)
									),
									'v15:CustomsClearanceDetail'=>array(
										'v15:DutiesPayment'=>array(
											'v15:PaymentType'=>'RECIPIENT',
											'v15:Payor'=>array(
												'v15:ResponsibleParty'=>array(
													'v15:AccountNumber'=>'',
												),
											)
										),
										'v15:CustomsValue'=>array(
											'v15:Currency'=>'USD',
											'v15:Amount'=>$order->total_amount
										),
										'v15:CommercialInvoice'=>array(
											'v15:TermsOfSale'=>'DDU',
										),
										'v15:Commodities'=>$pro,
									),
									'v15:LabelSpecification'=>array(
										'v15:LabelFormatType'=>'COMMON2D',
										'v15:ImageType'=>'PDF',
										'v15:LabelStockType'=>'STOCK_4X6',
										//                                         'v15:CustomerSpecifiedDetail'=>array(
										//                                             'v15:CustomContent'=>array(
										//                                                 'v15:BarcodeEntries'=>array(
										//                                                     'v15:Position'=>array(
										//                                                         'v15:X'=>'50',
										//                                                         'v15:Y'=>'2'
										//                                                     ),
										//                                                     'v15:BarcodeSymbology'=>'CODE128'
										//                                                 )
										//                                             )
										//                                         )
									),
									'v15:MasterTrackingId'=>$mastertrackingid,
									'v15:PackageCount'=>$quantity,
									'v15:RequestedPackageLineItems'=>array(
										'v15:SequenceNumber'=>$j,
										'v15:Weight'=>array(
											'v15:Units'=>'KG',
											'v15:Value'=>$order->packing_type=='PAK'?$order->weight_label:$out->weight
										),
										'v15:Dimensions'=>array(
											'v15:Length'=>floor($out->length),
											'v15:Width'=>floor($out->width),
											'v15:Height'=>floor($out->height),
											'v15:Units'=>'CM'
										),
									)
								),
							),
						),
					)
				);
				$res = Helper_xml::simpleArr2xml($res);
				QLog::log('fedexxml:'.$res);
				//fedex测试地址
				//         	    $url = "https://wsbeta.fedex.com:443/web-services";
				//正式地址
				$url='https://ws.fedex.com:443/web-services';
				Helper_Curl::$connecttimeout=300;
				Helper_Curl::$timeout=300;
				try {
					$return = Helper_Curl::post($url, $res);
				} catch ( Exception $e ) {
					$view['errormessage']='接口超时';
					return $view;
				}
				QLog::log('fedex:'.$return);
				$return=self::xmlToArray($return);
				if(isset($return['ProcessShipmentReply'])){
					if($return['ProcessShipmentReply']['HighestSeverity']=="SUCCESS" || $return['ProcessShipmentReply']['HighestSeverity']=="WARNING" || $return['ProcessShipmentReply']['HighestSeverity']=="NOTE"){
						$trackingno=$return['ProcessShipmentReply']['CompletedShipmentDetail']['MasterTrackingId']['TrackingNumber'];
						//非退件订单
						if(!$order->new_tracking_no){
							$order->new_tracking_no=$trackingno;
							$order->fedex_tracking_id_type=$return['ProcessShipmentReply']['CompletedShipmentDetail']['MasterTrackingId']['TrackingIdType'];
							$order->fedex_form_id=$return['ProcessShipmentReply']['CompletedShipmentDetail']['MasterTrackingId']['FormId'];
							$order->save();
						}
						
						$dir=Q::ini('upload_tmp_dir');
						@Helper_Filesys::mkdirs($dir);
						if($j>1){
							$trackingno=$return['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber'];
						}
						$target=$dir.DS.$trackingno.'.pdf';
						$source=$return['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
						file_put_contents($target,base64_decode($source));
						if($j>1){
							$filenames=array($dir.DS.$order->tracking_no.'.pdf',$target);
							@Helper_PDF::merge($filenames,$dir.DS.$order->tracking_no.'.pdf','file');
							@unlink($target);
						}
						//删除原单号
						ReturnSubcode::find('order_id=?',$order->return_order_id)->getAll()->destroy();
						//保存子单信息，用于交货核查
						$subcode=new ReturnSubcode();
						$subcode->order_id=$order->return_order_id;
						$subcode->sub_code=$trackingno;
						$subcode->save();
						
					}else {
						//保存原订单信息
						if($order->tracking_no){
							$order->tracking_no=null;
							$order->fedex_tracking_id_type='';
							$order->fedex_form_id='';
							$order->save();
							ReturnSubcode::meta()->destroyWhere('return_order_id = ?',$order->return_order_id);
						}
						
						$error='';
						if(isset($return['ProcessShipmentReply']['Notifications']['Message'])){
							$error=$return['ProcessShipmentReply']['Notifications']['Message'];
						}else {
							foreach ($return['ProcessShipmentReply']['Notifications'] as $n){
								$error .= $n['Message'];
							}
						}
						$view['errormessage']=$error;
					}
				}else {
					if($order->tracking_no){
						$order->tracking_no=null;
						$order->fedex_tracking_id_type='';
						$order->fedex_form_id='';
						$order->save();
						ReturnSubcode::meta()->destroyWhere('return_order_id = ?',$order->return_order_id);
					}
					
					$view['errormessage']=@$return['detail']['desc'];
				}
				if(isset($view['errormessage'])){
					return $view;
				}
				$j++;
			}
		}
		
		//保存发件人抬头到订单表
		$order->sender_id = @$sender->sender_id ? $sender->sender_id : '';
		$order->save();
		Helper_PDF::fedex($dir.DS.$order->tracking_no.'.pdf',$product);
		
 		//面单上传到oss
 		
 		//重发
 		$tracking_no = $order->new_tracking_no;
 		//上传
		$uploadoss = new Helper_AlipicsOss();
		$miandan_data = $uploadoss->uploadAlifiles($tracking_no.'.pdf');
		if ($uploadoss->doesExist($tracking_no.'.pdf')){
			//上传成功，删除
			unlink($dir.DS.$tracking_no.'.pdf');
		}
		$jsonfile=$dir.DS.$tracking_no.'.json';
		$shipper=array(
			'PersonName'=>$sender_name,
			'CompanyName'=>$sender_company,
			'PhoneNumber'=>$sender_phone,
			'StreetLines'=>$sender_address,
			'City'=>$sender_area.' DISTRICT '.$sender_city.' CITY '.$sender_zip_code.' '.$sender_country_code,
		);
		$view['errormessage']='';
		$view['account']='FEDEX';
		return $view;
	}
	static function xmlToArray( $xml )
	{
		$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches))
		{
			$count = count($matches[0]);
			$arr = array();
			for($i = 0; $i < $count; $i++)
			{
				$key = $matches[1][$i];
				$val = self::xmlToArray( $matches[2][$i] );  // 递归
				if(array_key_exists($key, $arr))
				{
					if(is_array($arr[$key]))
					{
						if(!array_key_exists(0,$arr[$key]))
						{
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
}