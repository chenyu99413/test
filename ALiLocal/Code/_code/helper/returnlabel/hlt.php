<?php
class Helper_ReturnLabel_Hlt{
	/**
	 * @todo   HLT账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function hlt($order,$account,$channel_id){
		//1
		set_time_limit(0);
		$state=$order->consignee_state_region_code;
		//判断收件国家是否是US
		if($order->consignee_country_code=='US' || $order->consignee_country_code=='CA'){
			//将收件人州转为二字码
			$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)),strtoupper(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州/省';
				return $view;
			}else{
				$state=$states->province_code_two;
			}
		}
		$farout=Returnoutpackage::find('return_order_id = ?',$order->return_order_id)->getAll();
		$weight='';
		foreach ($farout as $f){
			$weight +=$f->quantity*$f->weight;
		}
		$packing_type='W';
		$product=ReturnOrderproduct::find('return_order_id = ?',$order->return_order_id)->getAll();
		$product_quantity=ReturnOrderproduct::find('return_order_id = ?',$order->return_order_id)->getSum('product_quantity');
		$arr=array();
		$invoice=array('items'=>array(),'total'=>'');
		foreach ($product as $p){
			$arr[]=array(
				'cnName'=>$p->product_name_far,
				'customsNo'=>'',
				'name'=>$p->product_name_en_far,
				'netWeight'=>sprintf('%.3f',$weight/$product_quantity*$p->product_quantity),
				'pieces'=>$p->product_quantity,
				'productMemo'=>'',
				'unitPrice'=>$p->declaration_price,
			);
			$invoice['items'][]=array(
				'name'=>$p->product_name_en_far,
				'quantity'=>$p->product_quantity,
				'hscode'=>$p->hs_code_far,
				'price'=>$p->declaration_price,
				'itotal'=>round($p->product_quantity*$p->declaration_price,2),
				'weight'=>$weight/$product_quantity*$p->product_quantity,
				'country'=>'CN'
			);
			$invoice['total']+=round($p->product_quantity*$p->declaration_price,2);
		}
		$quantity=Returnoutpackage::find('return_order_id = ?',$order->return_order_id)->getSum('quantity_out');
		$outpackage=Returnoutpackage::find('return_order_id = ?',$order->return_order_id)->getAll();
		$packages=array();
		foreach ($outpackage as $out){
			$packages[]=array(
				'length'=>1,
				'width'=>1,
				'height'=>1,
				'weight'=>1,
				'trackingNo'=>''
			);
		}
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
		if($channel->sender_id>0){
			$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
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
				$sender_country_code=$sender->sender_country;
			}
		}
		$array=array(
			'userToken'=>'a43fb2769566442291622d8c6e6d8b5c',
			'createOrderRequest'=>array(
				'cargoCode'=>$packing_type,
				'city'=>$order->consignee_city,
				'consigneeCompanyName'=>$order->consignee_name1 != $order->consignee_name2?$order->consignee_name2:'',
				'consigneeMobile'=>$order->consignee_telephone?$order->consignee_telephone:$order->consignee_mobile,
				'consigneeName'=>$order->consignee_name1,
				'consigneePostcode'=>$order->consignee_postal_code,
				'consigneeStreetNo'=>'',
				'consigneeTelephone'=>$order->consignee_mobile,
				'declareItems'=>$arr,
				'packageItems'=>$packages,
				'additionalJson'=>'',
				'destinationCountryCode'=>$order->consignee_country_code,
				'goodsCategory'=>'O',
				'goodsDescription'=>'',
				'height'=>'',
				'insured'=>'N',
				'length'=>'',
				'memo'=>'',
				'orderNo'=>$order->ali_order_no,
				'originCountryCode'=>$sender_country_code,
				'pieces'=>$quantity,
				'platformNo'=>'',
				'province'=>$state,
				'shipperAddress'=>$sender_address,
				'shipperCity'=>$sender_city,
				'shipperCompanyName'=>$sender_company,
				'shipperMobile'=>$sender_phone,
				'shipperName'=>$sender_name,
				'shipperPostcode'=>$sender_zip_code,
				'shipperProvince'=>$sender_province,
				'shipperStreet'=>'',
				'shipperStreetNo'=>'',
				'shipperTelephone'=>'',
				'street'=>$order->consignee_street1.($order->consignee_street2?' '.$order->consignee_street2:''),
				'trackingNo'=>'',
				'transportWayCode'=>$account,
				'weight'=>$weight,
				'width'=>'',
			),
		);
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			'comment'=>'麦链订单报文：'.json_encode($array)
		));
		$log->save();
		QLog::log(json_encode($array));
		$url_order="http://tms.mailiancn.com:8086/xms/services/order?wsdl";
		$client=new SoapClient($url_order, array ('encoding' => 'UTF-8' ));
		$str=$client->__soapCall('createAndAuditOrder', $array);
		$str=get_object_vars($str);
		QLog::log('hlt回执：'.json_encode($str));
		if(isset($str['error'])){
			$error=get_object_vars($str['error']);
			$view['errormessage']=$channel->channel_name.":".$error['errorInfo'];
		}else {
			$trackingno=$str['trackingNo'];
			$oid=$str['id'];
			$url_label="http://tms.mailiancn.com:8086/xms/client/order_online!print.action?userToken=a43fb2769566442291622d8c6e6d8b5c&oid=".$oid."&printSelect=3&pageSizeCode=3&showCnoBarcode=0";
			if($account=='SH-DHL-MX'){
				$r=file_get_contents($url_label);
			}else {
				$r=Helper_Curl::get($url_label);
			}
			QLog::log('hlt面单回执：'.$r);
			if($r){
				$dir=Q::ini('upload_tmp_dir');
				@Helper_Filesys::mkdirs($dir);
				$target=$dir.DS.$trackingno.'.pdf';
				file_put_contents($target,$r);
			}else {
				$view['errormessage']='获取面单失败';
				return $view;
			}
			//保存退件信息
			$order->new_tracking_no=$trackingno;
			$order->ems_order_id=$oid;
			//保存发件人抬头到订单表
			$order->sender_id = @$sender->sender_id ? $sender->sender_id : '';
			$order->save();
			
			//删除原单号
			ReturnSubcode::find('order_id=?',$order->return_order_id)->getAll()->destroy();
			//保存退件子单信息，用于交货核查
			$subcode=new ReturnSubcode();
			$subcode->order_id=$order->return_order_id;
			$subcode->sub_code=$trackingno;
			$subcode->save();
			if($account=='ML3001' || $account=='ML0715'){
				$jsonfile=$dir.DS.$trackingno.'.json';
				$shipper=array(
					'aname'=>$sender_name,
					'name'=>$sender_company,
					'address'=>$sender_address.' '.$sender_city.' '.$sender_zip_code,
					'country'=>Country::find('code_word_two=?',$sender_country_code)->getOne()->english_name,
					'phone'=>$sender_phone
				);
				file_put_contents($jsonfile, json_encode(array(
					'invoice'=>$invoice,
					'shipper'=>$shipper,
					'itemcount'=>$quantity,
					'total_weight'=>$weight,
					'aname'=>trim($order->consignee_name1),
					'name'=>trim($order->consignee_name1) != trim($order->consignee_name2)?trim($order->consignee_name2):'',
					'email'=>$order->consignee_email,
					'phone'=>$order->consignee_mobile,
					'countrycode'=>$order->consignee_country_code,
					'countryname'=>Country::find('code_word_two=?',$order->consignee_country_code)->getOne()->english_name,
					'state'=>$state,
					'city'=>$order->consignee_city,
					'postcode'=>$order->consignee_postal_code,
					'address'=>$order->consignee_street1.' '.$order->consignee_street2,
					'tks'=>$trackingno,
					'ali_order_no'=>$order->ali_order_no,
					'tax_payer_id'=>$order->tax_payer_id,
					'date'=>date("Y/m/d")
				)));
			}
			$view['errormessage']='';
			$view['account']=$account;
		}
		return $view;
	}
}