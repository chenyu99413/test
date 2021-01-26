<?php
class Helper_ReturnLabel_Aliups{
	/**
	 * @todo   UPS账号渠道  系统打单
	 * @author 许杰晔
	 * @since  2020.6.2
	 * @param  $order $account 账号信息
	 * @return array
	 * @link   #80103
	 */
	static function aliups($order,$account,$channel_id){
		// invoice
		//1
		$invoice=array('items'=>array(),'total'=>'');
		$order_product=ReturnOrderproduct::find('return_order_id=?',$order->return_order_id)->getOne();
		$desc=$order_product->product_name_en;
		foreach ($order->product as $v){
			$name = $v->product_name_en_far;
			$material = $v->material_use;
			if(strpos(strtolower($v->product_name_en_far), 'mask') !== false){
				$brand = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
				$name .= ' // brand:'.$brand.' // type no:'.rand(10, 100);
				$material .= ' Civil';
			}
			$invoice['items'][]=array(
				'quantity'=>$v->product_quantity,
				'unit'=>$v->product_unit,
				'name'=>$name,
				'hscode'=>$v->hs_code_far,
				'country'=>'CN',
				'price'=>$v->declaration_price,
				'itotal'=>round($v->product_quantity*$v->declaration_price,2).' '.$order->currency_code,
				'material'=>$material,
			);
			$invoice['total']+=round($v->product_quantity*$v->declaration_price,2);
		}
		$package=array();
		// 参考编码
		$refno=array();
		$ref1='';
		$ref2=$order->ali_order_no;
		$far_package_count=Returnoutpackage::find('return_order_id=?',$order->return_order_id)->sum('quantity','sum_quantity')->getAll();
		//获取包裹类型
		$package_type=$order->packing_type;
		$package_code=($package_type=='DOC')?'04':'02';
		//一票多件
		$total_weight=$order->weight_actual_out;
		for($i=0;$i<$far_package_count['sum_quantity'] ;$i++){
			$package[]= array(
				'Description' => trim($desc,';'),
				'PackageWeight' => array(
					'UnitOfMeasurement' => array(
						'Code' => 'KGS',
						'Description' => 'Kilograms'
					),
					'Weight' => strval(floor($total_weight/$far_package_count['sum_quantity']*10000)/10000),
				),
				'Packaging' => array(
					'Code' => $package_code,
				),
				'ReferenceNumber'=>$refno,
			);
		}
		// 付款方式
		$shipmentcharge = array();
		// 三方
		if ($account->tp_account){
			$shipmentcharge[]= array(
				'Type'=>'01',
				'BillThirdParty'=>array(
					'AccountNumber' => $account->tp_account,
					'Address'=>array(
						'PostalCode'=>$account->tp_postalcode,
						'CountryCode'=>$account->tp_countrycode
					)
				)
			);
		}else {
			// 预付
			$shipmentcharge[]=array(
				'BillShipper' => array(
					'AccountNumber' => $account->account
				),
				'Type' => '01'
			);
		}
		$state='';
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
		}
		//判断收件人税号是否存在
		$vat='';
		if($order->tax_payer_id){
			$vat=' VAT:'.$order->tax_payer_id;
		}
		//判断地址2是否纯数字
		if(is_numeric($order->consignee_street2)){
			//纯数字就拼接在一起
			$address_str = $order->consignee_street1.$order->consignee_street2.$vat;
			//超过120个字符
			if(strlen($address_str) > 120){
				$view['errormessage']='收件人地址超长，地址的总字符不超过120';
				return $view;
			}else{
				$address[] = $address_str;
			}
		}else{
			$address=Helper_Common::splitAddress($order->consignee_street1.' '.$order->consignee_street2.$vat);
			if(count($address)>3){
				$view['errormessage']='收件人地址超长，地址1+地址2的总字符不超过105';
				return $view;
			}
		}
		$data = array(
			'UPSSecurity' => array(
				'ServiceAccessToken' => array(
					'AccessLicenseNumber' => $account->license
				),
				'UsernameToken' => array(
					'Password' => $account->pwd,
					'Username' => $account->userid
				)
			),
			'ShipmentRequest' => array(
				'Request' => array(
					'RequestOption' => 'nonvalidate',
					'TransactionReference' => array(
						'CustomerContext' => 'iBayTest'
					)
				),
				'Shipment' => array(
					'Description' => trim($desc,';'),
					'Package' =>$package,
					'PaymentInformation' => array(
						//预付
						'ShipmentCharge' =>$shipmentcharge,
					),
					'Service' => array(
						'Code' => '65',
						'Description' => '2',
					),
					'Shipper' => array(
						'Address' => array(
							'AddressLine' => Helper_Common::splitAddress($account->address),
							'City' => $account->city,
							'CountryCode' => $account->countrycode,
							'PostalCode' => $account->postcode,
							'StateProvinceCode' => $account->state
						),
						'AttentionName' => $account->aname,
						'Name' => $account->aname,
						'Phone' => array(
							'Number' => $account->phone
						),
						'ShipperNumber' => $account->account
					),
					'ShipTo' => array(
						'Address' => array(
							'AddressLine' => $address,
							'City' => $order->consignee_city,
							'CountryCode' => $order->consignee_country_code,
							'PostalCode' => $order->consignee_postal_code,
							'StateProvinceCode' => $state
						),
						'AttentionName' => trim($order->consignee_name1),
						'Name' => trim($order->consignee_name1) != trim($order->consignee_name2)?trim($order->consignee_name2):'',
						'EMailAddress'=>$order->consignee_email,
						'Phone' => array(
							'Number' => $order->consignee_mobile
						)
					),
					'ShipmentRatingOptions' => array(
						'NegotiatedRatesIndicator' => '0'
					)
				),
				'LabelSpecification' => array(
					'LabelImageFormat' => array(
						'Code' => 'GIF'
					)
				)
			)
		);
		if ($state==''){
			unset($data['ShipmentRequest']['Shipment']['ShipTo']['Address']['StateProvinceCode']);
		}
		if ($package_code=='04'){
			$data['ShipmentRequest']['Shipment']['DocumentsOnlyIndicator']= (object)array();
		}
		QLog::log('upsend:'.$order->ali_order_no.json_encode($data));
		//aliups订单报文
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			//json
			'comment'=>'aliups订单报文：'.json_encode($data)
		));
		$log->save();
		// 	    $endpoint='https://wwwcie.ups.com';
		$endpoint='https://onlinetools.ups.com';
		set_time_limit(400);
		Helper_Curl::$connecttimeout=300;
		Helper_Curl::$timeout=300;
		$r=Helper_Curl::post($endpoint.'/rest/Ship', json_encode($data),array(
			'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
			'Access-Control-Allow-Methods: POST',
			'Access-Control-Allow-Origin: *',
			'Content-Type: application/json'
		));
		// 		echo $r;
		$r=json_decode($r,true);
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$tks=array();
		if (isset($r['ShipmentResponse']['Response']['ResponseStatus']['Code']) && $r['ShipmentResponse']['Response']['ResponseStatus']['Code']==1){
			$pkg=$r['ShipmentResponse']['ShipmentResults']['PackageResults'];
			if (isset($pkg['TrackingNumber'])){
				$pkg=array($pkg);
			}
			$subcode1=array('waybillcode'=>$pkg[0]['TrackingNumber']);
			$subcode2=array('waybillcode'=>$pkg[0]['TrackingNumber']);
			for ($n=1;$n<11;$n++){
				$subcode1['info'][$n]=array(
					'subcode'=>isset($pkg[$n]['TrackingNumber'])?($n+1).'. '.$pkg[$n]['TrackingNumber']:''
				);
			}
			if(count($pkg)>11){
				for ($m=11;$m<count($pkg);$m++){
					$subcode2['info'][]=array(
						'subcode'=>($m+1).'. '.$pkg[$m]['TrackingNumber']
					);
				}
			}
			//删除退件子单
			ReturnSubcode::find('order_id=?',$order->return_order_id)->getAll()->destroy();
			foreach ($pkg as $tr){
				$tks[]=$tr['TrackingNumber'];
				$target=$dir.DS.$tr['TrackingNumber'].'.pdf';
				$source=$dir.DS.$tr['TrackingNumber'].'.gif';
				file_put_contents($source, base64_decode($tr['ShippingLabel']['GraphicImage']));
				Helper_PDF::upslabel($source,$target);
				//保存到退件订单表
				$order_subcode=new ReturnSubcode();
				//保存
				$order_subcode->changeProps(array(
					'order_id'=>$order->return_order_id,
					'sub_code'=>$tr['TrackingNumber']
				));
				$order_subcode->save();
			}
			foreach ($tks as $fname){
				$filenames []=$dir.DS.$fname.'.pdf';
			}
			/* $watermarkpath=array('chapter1'=>_INDEX_DIR_.'/public/img/chapter1.gif','chapter2'=>_INDEX_DIR_.'/public/img/chapter2.gif');
			 if($account->account=='73X574' || $account->account=='5315WW' || $account->account=='0032F1' || $account->account=='79YE39' || $account->account=='5119E4' || $account->account=='3X5001' || $account->account=='794YA6' || $account->account=='79Y3R3'){
			 $watermarkpath=array();
			 }
			 @Helper_PDF::merge($filenames,$dir.DS.$tks[0].'.pdf','file',$watermarkpath); */
			$uploadoss = new Helper_AlipicsOss();
			//上传到oss
			$miandan_data = $uploadoss->uploadAlifiles($tks[0].'.pdf');
			if ($uploadoss->doesExist($tks[0].'.pdf')){
				//上传成功，删除
				unlink($dir.DS.$tks[0].'.pdf');
			}
			//上传gif
			$miandangif_data = $uploadoss->uploadAlifiles($tks[0].'.gif');
			if ($uploadoss->doesExist($tks[0].'.gif')){
				//上传成功，删除
				unlink($dir.DS.$tks[0].'.gif');
			}
			//将tracking_number存入order中
			//重发
			$order->new_tracking_no = $tks[0];
			//保存
			$order->save();
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			//11111111111111
			$view['errormessage']='';
			$view['account']='UPS';
		}else {
			$view['errormessage']=isset($r['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])?$r['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description']:$r;
		}
		return $view;
	}
}