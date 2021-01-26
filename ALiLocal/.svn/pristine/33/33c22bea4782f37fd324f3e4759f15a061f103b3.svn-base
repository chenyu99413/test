<?php
class Helper_ReturnLabel_Hualei{
	/**
	 * @todo   UPS 9Y3152 账号渠道  系统打单
	 * @author 许杰晔
	 * @since  2020.6.2
	 * @param  $order $account 账号信息
	 * @return array
	 * @link   #80103
	 */
	static function hualei($order,$account,$channel_id){
		$url="http://116.62.140.203:8082/createOrderApi.htm";
		$invoice=array();
		$package_type=$order->packing_type;
		$package_code=($package_type=='DOC')?'04':'02';
		$far_package_count=Returnoutpackage::find('return_order_id=?',$order->return_order_id)->sum('quantity','sum_quantity')->getAll();
		$order_product=ReturnOrderproduct::find('return_order_id=?',$order->return_order_id)->getOne();
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
				//重发
				$order->new_tracking_no = $return['tracking_number'];
				//保存
				$order->ems_order_id=$return['order_id'];
				//保存
				$order->save();
				//保存子单信息，用于交货核查
				$barcode=array();
				if(count($return['childList'])){
					foreach ($return['childList'] as $sub){
						$barcode[] = $sub['child_number'];
					}
				}
				$subcode1=array('waybillcode'=>$return['tracking_number']);
				$subcode2=array('waybillcode'=>$return['tracking_number']);
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
				if($barcode>0){
					//删除原单号
					ReturnSubcode::find('order_id=?',$order->return_order_id)->getAll()->destroy();
					//保存退件子单信息
					foreach ($barcode as $sun){
						$subcode=new ReturnSubcode();
						$subcode->order_id=$order->return_order_id;
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