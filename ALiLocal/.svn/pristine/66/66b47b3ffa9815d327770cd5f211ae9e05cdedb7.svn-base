<?php
class Helper_Label_Hualei{
	/**
	 * @todo UPS 9Y3152 账号渠道  系统打单
	 * @author 许杰晔
	 * @since 2020.6.2
	 * @param $order $account 账号信息
	 * @return array
	 * @link #80103
	 */
	static function hualei($order,$account,$channel_id){
		$url="http://116.62.140.203:8082/createOrderApi.htm";
		$invoice=array();
		$package_type=$order->packing_type;
		$package_code=($package_type=='DOC')?'04':'02';
		$far_package_count=Faroutpackage::find('order_id=?',$order->order_id)->sum('quantity_out','sum_quantity')->getAll();
		$order_product=Orderproduct::find('order_id=?',$order->order_id)->getOne();
		$desc=$order_product->product_name_en;
		$total_weight=$order->weight_actual_out;
		//判断收件人税号是否存在
		$vat='';
		if($order->tax_payer_id){
			$vat=' VAT:'.$order->tax_payer_id;
		}
		
		$state='';
		$city='';
		$address='';
		//判断收件国家是否是US和CA
		if($order->consignee_country_code=='US' || $order->consignee_country_code=='CA'){
			//将收件人州转为二字码
			$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)),strtoupper(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州';
				return $view;
			}else{
				$state=$states->province_code_two;
				$city=$order->consignee_city;
				$address=$order->consignee_street1.$order->consignee_street2;
			}
		}else{
			//如果拼接后，城市+，+省州，总字符超过30个字符时，将省州信息放在街道地址后，即：完整街道地址+英文逗号+省州信息
			if(mb_strlen($order->consignee_city.$order->consignee_state_region_code)>30){
				$address=$order->consignee_street1.$order->consignee_street2.','.$order->consignee_state_region_code;
				$city=$order->consignee_city;
			}else{
				//收件人城市字段由原来的传系统城市信息更改为在系统城市字段后加英文字符的逗号及收件人省州信息
				$city=$order->consignee_city.','.$order->consignee_state_region_code;
				$address=$order->consignee_street1.$order->consignee_street2;
			}
			$state=$order->consignee_country_code;
		}
		
		$fp_invoice=array('items'=>array(),'total'=>'');
		foreach ($order->product as $p){
			$invoice[]=array(
				"invoice_amount"=> $p->product_quantity*$p->declaration_price,
				"invoice_pcs"=> $p->product_quantity,
				"invoice_title"=> $p->product_name_en_far,
				"invoice_weight"=> "",
				"item_id"=>"",
				"item_transactionid"=> "",
				"sku"=> $p->product_name_far,
				"sku_code"=> ""
			);
			$name = $p->product_name_en_far;
			$material = $p->material_use;
			if(strpos(strtolower($p->product_name_en_far), 'mask') !== false){
				$brand = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
				$name .= ' // brand:'.$brand.' // type no:'.rand(10, 100);
				$material .= ' Civil';
			}
			$fp_invoice['items'][]=array(
				'quantity'=>$p->product_quantity,
				'unit'=>$p->product_unit,
				'name'=>$name,
				'hscode'=>$p->hs_code_far,
				'country'=>'CN',
				'price'=>$p->declaration_price,
				'itotal'=>round($p->product_quantity*$p->declaration_price,2).' '.$order->currency_code,
				'material'=>$material,
			);
			$fp_invoice['total']+=round($p->product_quantity*$p->declaration_price,2);
		}
		$account_address=Helper_Common::splitAddress($account->address);
		$arr=array(
			"buyerid"=> "",
			"consignee_address"=> $address,
			//件数，小包默认1，快递需真实填写 
			"order_piece"=>$far_package_count['sum_quantity'],
			"consignee_city"=> $city,
			"consignee_mobile"=> $order->consignee_mobile? $order->consignee_mobile : $order->consignee_telephone,
			"order_returnsign"=>"Y",
			"consignee_name"=> $order->consignee_name1,
			"trade_type"=> "ZYXT",
			"consignee_postcode"=> $order->consignee_postal_code,
			"consignee_state"=>$state,
			"consignee_telephone"=> $order->consignee_telephone ? $order->consignee_telephone : $order->consignee_mobile,
			"country"=> $order->consignee_country_code,
			"customer_id"=> "20561",
			"customer_userid"=> "17241",
			"orderInvoiceParam"=> $invoice,
			"order_customerinvoicecode"=> $order->ali_order_no,
			"product_id"=> "5461",
			"weight"=> $order->weight_label,
			"product_imagepath"=> "",
			"shipper_name"=>$account->aname,
			"shipper_companyname"=>$account->name,
			"shipper_address1"=>$account_address[0],
			"shipper_address2"=>isset($account_address[1]) ? $account_address[1] : '',
			"shipper_address3"=>isset($account_address[2]) ? $account_address[1] : '',
			"shipper_address2"=>'',
			"shipper_address3"=>'',
			"shipper_city"=>$account->city,
			"shipper_state"=>'',
			"shipper_postcode"=>$account->postcode,
			"shipper_country"=>'CN',
			"shipper_telephone"=>$account->phone
		);
		//print_r($arr);exit;
		QLog::log(json_encode($arr));
		$return=Helper_Curl::post($url, 'param='.json_encode($arr));
		//print_r($return);exit;
		QLog::log($return);
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			'comment'=>'UPSHL订单报文：'.json_encode($arr)
		));
		$log->save();
		$return=json_decode($return,true);
		if($return['ack']=='true'){
			sleep(2);
			$labelurl='http://116.62.140.203:8089/order/FastRpt/PDF_NEW.aspx?PrintType=lab10_10&order_id=';
			$dir=Q::ini('upload_tmp_dir');
			@Helper_Filesys::mkdirs($dir);
			$target=$dir.DS.$return['tracking_number'].'.pdf';
			try {
				$source=file_get_contents($labelurl.$return['order_id']);
				file_put_contents($target,$source);
				Helper_PDF::rotate($target,$target);//旋转面单 
				//保存退件信息 
				$order->tracking_no=$return['tracking_number'];
				$order->ems_order_id=$return['order_id'];
				//保存 
				$order->save();
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
				
				//保存子单信息，用于交货核查
				$barcode=array();
				if(count($return['childList'])){
					foreach ($return['childList'] as $sub){
						$barcode[] = $sub['child_number'];
					}
				}
				//链接单号
				$subcode1=array('waybillcode'=>$return['tracking_number']);
				$subcode2=array('waybillcode'=>$return['tracking_number']);
				//注释
				if(count($barcode)>11){
					for ($n=1;$n<11;$n++){
						$subcode1['info'][]=array(
							'subcode'=>($n+1).'. '.$barcode[$n]
						);
					}
					for ($m=11;$m<count($barcode);$m++){
						$subcode2['info'][]=array(
							'subcode'=>($m+1).'. '.$barcode[$m]
						);
					}
				}else{
					for ($m=1;$m<count($barcode);$m++){
						$subcode1['info'][]=array(
							'subcode'=>($m+1).'. '.$barcode[$m]
						);
					}
				}
				//保存子单号
				if($barcode>0){
					foreach ($barcode as $sun){
						$subcode=new Subcode();
						$subcode->order_id=$order->order_id;
						$subcode->sub_code=$sun;
						$subcode->save();
					}					
				}
				$jsonfile=$dir.DS.$return['tracking_number'].'.json';
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
				//判断是否是高价
				$hv='';
				if($order->declaration_type=='DL' || $order->total_amount > 700 || $order->weight_actual_in > 70){
					$hv='HV';
				}
				//判断电子章
				if(!in_array($account->account,array('73X574','5315WW','0032F1','5119E4','79YE39','794YA6','3X5001','79Y3R3','503RY1'))){
					$chapter1='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter1.gif';
					$chapter2='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter2.gif';
				}else{
					$chapter1='';
					$chapter2='';
				}
				$uploadoss = new Helper_AlipicsOss();
				//上传到oss
				$miandan_data = $uploadoss->uploadAlifiles($return['tracking_number'].'.pdf');
				if ($uploadoss->doesExist($return['tracking_number'].'.pdf')){
					//上传成功，删除
					unlink($dir.DS.$return['tracking_number'].'.pdf');
				}
				//组织发票数据
				$hualeiinvoice_arr = array(
					'invoice'=>$fp_invoice,
					'shipmentid'=>Helper_Common::creatShipid($return['tracking_number']),
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
					'city'=>$city,
					'postcode'=>$order->consignee_postal_code,
					'ref1'=>'',
					'ref2'=>'',
					'address'=>$address.$vat,//$vat收件人税号
					'freight'=>'0',
					'tks'=>$return['tracking_number'],
					'ali_order_no'=>$order->ali_order_no,
					'subcode1'=>$subcode1,
					'subcode2'=>$subcode2,
					'poc_line1'=>'[X] '.$poc_line1,
					'poc_line2'=>'[X] '.$poc_line2.'[X] '.$poc_line3,
					'poc_line3'=>'',
					'poc_line2_cn'=>'[X] '.$poc_line2_cn.'[X] '.$poc_line3_cn,
					'shipmentCharge'=>"",
				);
				//生成的条码图片
				$barcode=$dir.DS.$return['tracking_number'].'.barcode.png';
				$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$return['tracking_number']));
				file_put_contents($barcode, $source);
				//是否打印fda发票
				$flag = false;
				//收件人国家是US fda数据都存在
				if ($order->consignee_country_code=='US'&&$order->fda_company&&$order->fda_address&&$order->fda_city&&$order->fda_post_code){
					$flag = true;
				}
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
						Helper_Invoice::upsinvoice($hualeiinvoice_arr);
					}
				}
				$others_file_arr[0] = $dir.DS.$return['tracking_number'].'_invoice.pdf';
				$others_file_arr[1] = $dir.DS.$return['tracking_number'].'_invoice.pdf';
				//生成copy1
				Helper_Invoice::upscopy1($hualeiinvoice_arr);
				$others_file_arr[2] = $dir.DS.$return['tracking_number'].'_copy_1.pdf';
				$sub_code=Subcode::find('order_id=?',$order->order_id)->getAll();
				//子单数量超过11，打印copy2
				if (count($sub_code)>11){
					Helper_Invoice::upscopy2($hualeiinvoice_arr);
					$others_file_arr[3] = $dir.DS.$return['tracking_number'].'_copy_2.pdf';
				}
				//合成PDF文件
				$others_file_path = $dir.DS.$return['tracking_number'].'_others.pdf';
				Helper_PDF::merge($others_file_arr,$others_file_path,'file');
				//上传到oss
				$invoice_data = $uploadoss->uploadAlifiles($return['tracking_number'].'_others.pdf');
				if ($uploadoss->doesExist($return['tracking_number'].'_others.pdf')){
					//上传成功，删除
					unlink($dir.DS.$return['tracking_number'].'_others.pdf');
					unlink($dir.DS.$return['tracking_number'].'_invoice.pdf');
					unlink($dir.DS.$return['tracking_number'].'_copy_1.pdf');
					//子单数量超过11
					if (count($sub_code)>11){
						unlink($dir.DS.$return['tracking_number'].'_copy_2.pdf');
					}
				}
				unlink($barcode);		
				
				$view['errormessage']='';
				$view['account']='UPS';
				
			} catch (Exception $e) {
				$view['errormessage']="UPS华磊获取面单失败";
			}
		}else {
			$view['errormessage']=urldecode($return['message']);
		}
		return $view;
	}
}