<?php
class Helper_Label_Ups{
	/**
	 * @todo   UPS账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function ups($order,$account,$channel_id,$rorder = 0){
		// invoice
		//1
		$invoice=array('items'=>array(),'total'=>'');
		$order_product=Orderproduct::find('order_id=?',$order->order_id)->getOne();
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
				'currency'=>$order->currency_code,
			);
			$invoice['total']+=round($v->product_quantity*$v->declaration_price,2);
		}
		$package=array();
		// 参考编码
		$refno=array();
		$ref1='';
		$ref2=$order->ali_order_no;
		//台州富轩账号要加一个订单号放到ref1上
		if($account->account=='79YE39'){
			$ref1=Helper_Curl::get1('http://kuaijian.far800.com/index.php?controller=cron&action=gettzrefno');			
		}else if($account->account=='503RY1'){
			//嘉兴富轩账号要加一个订单号放到ref1上
			$ref1=Helper_Curl::get1('http://kuaijian.far800.com/index.php?controller=cron&action=getjxrefno');
		}else if($account->account=='4W13V4'){
			$ref1='V928X8';
			$ref2='YY1';
		}else if($account->account=='4FV328'){
			//富轩账号要加一个订单号
			$ref1=$order->ali_order_no;
			$ref2=Helper_Curl::get1('http://kuaijian.far800.com/index.php?controller=cron&action=getrefno');			
		}else if($account->account=='92X1W0'){
			$ref1='B';		
		}elseif($account->account=='7660AY'){
			$ref1 = 'A';
		}
		if($ref2){
			$refno[]=array(
				'Code'=>'PO',
				'Value'=>$ref2
			);
		}
		if($ref1){
			$refno[]=array(
				'Code'=>'IK',
				'Value'=>$ref1
			);
		}
		$far_package_count=Faroutpackage::find('order_id=?',$order->order_id)->sum('quantity_out','sum_quantity')->getAll();
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
		$shipmentCharge = array();
		// 三方
		if ($account->tp_account){
			$shipmentCharge[]= array(
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
			$shipmentCharge[]=array(
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
						'ShipmentCharge' =>$shipmentCharge,
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
						'Name' => $account->name,
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
						'Name' => trim($order->consignee_name2)?trim($order->consignee_name2):trim($order->consignee_name1),
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
		QLog::log($order->ali_order_no.json_encode($data));
		//UPS订单报文
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			//json
			'comment'=>'UPS订单报文：'.json_encode($data)
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
			if($rorder === 0){
				//删除原有的子单
				Subcode::find('order_id=?',$order->order_id)->getAll()->destroy();
			}else{
				//删除退件子单号
				ReturnSubcode::find('order_id=?',$rorder->return_order_id)->getAll()->destroy();
			}
			foreach ($pkg as $tr){
				$tks[]=$tr['TrackingNumber'];
				$target=$dir.DS.$tr['TrackingNumber'].'.pdf';
				$source=$dir.DS.$tr['TrackingNumber'].'.gif';
				file_put_contents($source, base64_decode($tr['ShippingLabel']['GraphicImage']));
				Helper_PDF::upslabel($source,$target);
				if($rorder === 0){
					//存入sub_code表中
					$order_subcode=new Subcode();
					//保存
					$order_subcode->changeProps(array(
						'order_id'=>$order->order_id,
						'sub_code'=>$tr['TrackingNumber']
					));
					$order_subcode->save();
				}else{
					//存入退件子单表
					$order_subcode=new ReturnSubcode();
					//保存
					$order_subcode->changeProps(array(
						'order_id'=>$rorder->return_order_id,
						'sub_code'=>$tr['TrackingNumber']
					));
					$order_subcode->save();
				}
			}
			foreach ($tks as $fname){
				$filenames []=$dir.DS.$fname.'.pdf';
			}
			$watermarkpath=array('chapter1'=>_INDEX_DIR_.'/public/img/chapter1.gif','chapter2'=>_INDEX_DIR_.'/public/img/chapter2.gif');
			if($account->account=='73X574' || $account->account=='5315WW' || $account->account=='0032F1' || $account->account=='79YE39' || $account->account=='5119E4' || $account->account=='3X5001' || $account->account=='794YA6' || $account->account=='79Y3R3' || $account->account=='503RY1'){
				$watermarkpath=array();
			}
			@Helper_PDF::merge($filenames,$dir.DS.$tks[0].'.pdf','file',$watermarkpath);
			
			$upstarget = $dir.DS.$tks[0].'.pdf';
			//将tracking_number存入order中
			if($rorder !== 0){
				//重发
				$rorder->new_tracking_no = $tks[0];
				//保存
				$rorder->save();
			}else{
				//原单号
				$order->tracking_no=$tks[0];
				//保存
				$order->save();
				//#84640
				if($order->customer->customs_code=='HDKJ'){
					$recordformdata = file_get_contents($upstarget);
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
					$data_clone['label'] = md5(base64_encode(file_get_contents($upstarget)));
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
			$miandan_data = $uploadoss->uploadAlifiles($tks[0].'.pdf');
			if ($uploadoss->doesExist($tks[0].'.pdf')){
				//上传成功，删除
				unlink($dir.DS.$tks[0].'.pdf');
			}
			
			$jsonFile=$dir.DS.$tks[0].'.json';
			// ups copy
			$poc_line1='PRE';//到付
			//$poc_line1='SDT(F/D)';//预付
			if ($account->tp_account){
				$tp_country=($account->tp_countrycode=='KR')?'KOREA,SOUTH':$account->tp_countrycode;
				$poc_line2="Bill Transportation to Third Party\r\nBill Transportation Charges To:{$account->tp_account}\r\nCompany Name:{$account->tp_cname}\r\nCountry/Territory:{$tp_country}\r\n";
				$poc_line2_cn="第三方支付运输费用\r\n运输费用付款人：{$account->tp_account}\r\n公司名称：{$account->tp_cname}\r\n国家/地区：{$tp_country}\r\n";
			}else {
				$poc_line2='Bill Transportation to Shipper '.$account->account."\r\n";
				$poc_line2_cn="发件人支付运输费用".$account->account."\r\n";
			}
			$poc_line3='Bill Duty and Tax to Receiver';
			$poc_line3_cn="收件人支付关税和税款";
			if($account->account=='4FV328'){
				$specialinstruction='';
				$weight_table='';
			}else{
				if($order->packing_type=='BOX'){
					$specialinstruction='';
					//判断包裹数量
					if($far_package_count['sum_quantity']<=5){
						$weight_table='';
						$far_packages=Faroutpackage::find('order_id=?',$order->order_id)->getAll();
						foreach ($far_packages as $value){
							$weight_table.=floor($value->length_out).'*'.floor($value->width_out).'*'.floor($value->height_out).'*'.$value->quantity_out."\r\n";
						}
					}else{
						$weight_table='详见重量对比表';
					}
				}else{
					$specialinstruction='PAK';
					$weight_table='';
				}
			}
			//判断是否是高价
			$hv='';
			if($order->declaration_type=='DL' || $order->total_amount > 700 || $order->weight_actual_in > 70){
				$hv='HV';
			}
			//判断电子章
			if($account->account!='73X574' && $account->account!='5315WW' && $account->account!='0032F1' && $account->account!='5119E4' && $account->account!='79YE39' && $account->account!='3X5001' && $account->account!='794YA6' && $account->account!='79Y3R3'&& $account->account!='503RY1'){
				$chapter1='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter1.gif';
				$chapter2='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter2.gif';
			}else{
				$chapter1='';
				$chapter2='';
			}
			//生成的条码图片
			$barcode=$dir.DS.$tks[0].'.barcode.png';
			//--
			$logo=$dir.DS.'logo.jpg';
			$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$tks[0]));
			file_put_contents($barcode, $source);
			if($rorder === 0){
				//是否打印fda发票
				$flag = false;
				if ($order->consignee_country_code=='US'&&$order->fda_company&&$order->fda_address&&$order->fda_city&&$order->fda_post_code){
					$flag = true;
				}
				//打印发票的数据
				$upsinvoice_arr = array(
					'invoice'=>$invoice,
					'shipmentid'=>Helper_Common::creatShipid($tks[0]),
					'shipper'=>$account->toArray(),
					'servicecode'=>'65',
					'service'=>'1P',
					'service_name'=>'EXPRESS SAVER',
					'documentOnly'=>$package_code=='04' ?'[X] DOCUMENTS ONLY':'',
					'specialInstruction'=>$specialinstruction,
					'weight_table'=>$weight_table,
					'hv'=>$hv,
					'chapter1'=>$chapter1,
					'chapter2'=>$chapter2,
					'taxddp'=>'1',
					'itemcount'=>$far_package_count['sum_quantity'],
					'dfu'=>'',
					'weight'=>$total_weight,
					'total_weight'=>$total_weight,
					'description'=>trim($desc,';'),
					'aname'=>trim($order->consignee_name1),
					'name'=>trim($order->consignee_name2)?trim($order->consignee_name2):trim($order->consignee_name1),
					'email'=>$order->consignee_email,
					'phone'=>$order->consignee_mobile,
					'countrycode'=>$order->consignee_country_code,
					'countryname'=>Country::find('code_word_two=?',$order->consignee_country_code)->getOne()->english_name,
					'state'=>$state,
					'city'=>$order->consignee_city,
					'postcode'=>$order->consignee_postal_code,
					'ref1'=>$ref1,
					'ref2'=>$ref2,
					'address'=>$order->consignee_street1.' '.$order->consignee_street2.$vat,
					'freight'=>'0',
					'tks'=>$tks[0],
					'ali_order_no'=>$order->ali_order_no,
					'subcode1'=>$subcode1,
					'subcode2'=>$subcode2,
					'poc_line1'=>'[X] '.$poc_line1,
					'poc_line2'=>'[X] '.$poc_line2.'[X] '.$poc_line3,
					'poc_line3'=>'',
					'poc_line2_cn'=>'[X] '.$poc_line2_cn.'[X] '.$poc_line3_cn,
					'shipmentCharge'=>$r['ShipmentResponse']['ShipmentResults']['ShipmentCharges'],
				);
				if ($flag){
					//fda发票PDF
					$invoice_arr = Helper_Common::fdainvoicedata($order,$channel_id,$account->account);
					Helper_Invoice::invoicefda($invoice_arr);
				}else{
					$invoice_type = Helper_Common::getinvoicetype($channel_id, $order->consignee_country_code);
					if ($invoice_type==1){
						//PE、VG发票PDF
						$invoice_arr = Helper_Common::peinvoicedata($order,$channel_id,$account->account);
						Helper_Invoice::upsinvoicepe($invoice_arr);
					}else{
						//正常发票PDF
						Helper_Invoice::upsinvoice($upsinvoice_arr);
					}
				}
				//两张发票
				$others_file_arr[0] = $dir.DS.$tks[0].'_invoice.pdf';
				$others_file_arr[1] = $dir.DS.$tks[0].'_invoice.pdf';
				Helper_Invoice::upscopy1($upsinvoice_arr);
				$others_file_arr[2] = $dir.DS.$tks[0].'_copy_1.pdf';
				$sub_code=Subcode::find('order_id=?',$order->order_id)->getAll();
				//子单数量超过11有copy2
				if (count($sub_code)>11){
					Helper_Invoice::upscopy2($upsinvoice_arr);
					$others_file_arr[3] = $dir.DS.$tks[0].'_copy_2.pdf';
				}
				$others_file_path = $dir.DS.$tks[0].'_others.pdf';
				//合成一个PDF文件
				Helper_PDF::merge($others_file_arr,$others_file_path,'file');
				//上传到oss
				$invoice_data = $uploadoss->uploadAlifiles($tks[0].'_others.pdf');
				if ($uploadoss->doesExist($tks[0].'_others.pdf')){
					//上传成功，删除
					unlink($dir.DS.$tks[0].'_others.pdf');
					unlink($dir.DS.$tks[0].'_invoice.pdf');
					unlink($dir.DS.$tks[0].'_copy_1.pdf');
					//子单数量超过11有copy2
					if (count($sub_code)>11){
						unlink($dir.DS.$tks[0].'_copy_2.pdf');
					}
				}
				unlink($barcode);
			}
			
			$view['errormessage']='';
			$view['account']='UPS';
		}else {
			$view['errormessage']=isset($r['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])?$r['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description']:$r;
		}
		return $view;
	}
}