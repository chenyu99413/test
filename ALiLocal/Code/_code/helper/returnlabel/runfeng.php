<?php
class Helper_ReturnLabel_Runfeng{
	/**
	 * @todo   润峯账号渠道
	 * @author stt
	 * @since  2020-09-09
	 * @link   #82496
	 */
	static function runfeng($order,$channel_id){
		//请求方式设置
		$requestheader = array (
			'Content-Type:application/x-www-form-urlencoded'
		);
		//api 请求地址
		$api_url='http://ywys.rtb56.com/webservice/PublicService.asmx/ServiceInterfaceUTF8';
		//appToken API账号
		$apptoken = '944d4c27a2cb2dea603acd6142ebe496';
		//appKey API密码
		$appkey = 'a90a6fdd759f8ad7281cfd7ea7357a97a90a6fdd759f8ad7281cfd7ea7357a97';
		//获取总重量
		$total_weight=$order->weight_actual_out;
		//货物类型
		if($order->packing_type == 'DOC'){
			//D：文件
			$cargo_type = 'D';
		}elseif($order->packing_type == 'BOX'){
			//W：包裹
			$cargo_type = 'W';
		}elseif($order->packing_type == 'PAK'){
			//B：袋子
			$cargo_type = 'B';
		}
		foreach ($order->product as $v){
			//D：文件
			if ($cargo_type=='D'){
				$invoice[]=array(
					'invoice_enname'        =>'DOC',
					'invoice_cnname'        =>'文件',
					"invoice_quantity"      =>1,
					//单位
					'unit_code'             =>"PCE",
					"invoice_unitcharge"    =>1,
					'invoice_note'          =>'',
					'hs_code'               =>'4820300000',
				);
			}else{
				//B：袋子W：包裹
				$invoice[]=array(
					'invoice_enname'=>$v->product_name_en,
					'invoice_cnname'=>$v->product_name,
					'invoice_quantity'=>$v->product_quantity,
					'unit_code'=>'PCE',
					//单价，2位小数 (1个数量的商品价格)
					'invoice_unitcharge'=>round($v->declaration_price,2),
					'invoice_note'=>$v->goods_info,
					//海关协制编号
					'hs_code'=>$v->hs_code_far,
				);
			}
		}
		//runfeng发票数据
		$invoice_data=array('items'=>array(),'total'=>'');
		//获取总重量
		$total_weight=$order->weight_actual_out;
		$quantity=0;
		foreach ($order->product as $value){
			$quantity+=$value->product_quantity;
		}
		//发票商品信息
		foreach ($order->product as $v){
			$items[]=array(
				'Goods'=>$v->product_name_en_far,
				'GoodsCn'=>$v->product_name_far,
				'Currency'=>$order->currency_code,
				//数量
				'Count'=>$v->product_quantity,
				'UnitPrice'=>$v->declaration_price,
				'SubWeight'=>$quantity>0?(floor($total_weight/$quantity*10000))/10000*$v->product_quantity:"0.1",
				'HsCode'=>$v->hs_code_far,
				'Label'=>'',
				//sku
				'Sku'=>'',
			);
			$name = $v->product_name_en_far;
			$material = $v->material_use;
			//英文品名
			if(strpos(strtolower($v->product_name_en_far), 'mask') !== false){
				$brand = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
				$name .= ' // brand:'.$brand.' // type no:'.rand(10, 100);
				$material .= ' Civil';
			}
			//组织runfeng发票数据
			$invoice_data['items'][]=array(
				'quantity'=>$v->product_quantity,
				'unit'=>$v->product_unit,
				'name'=>$name,
				'hscode'=>$v->hs_code_far,
				//CN
				'country'=>'CN',
				'price'=>$v->declaration_price,
				'itotal'=>round($v->product_quantity*$v->declaration_price,2).' '.$order->currency_code,
				'material'=>$material,
			);
			//总申报数量
			$invoice_data['total']+=round($v->product_quantity*$v->declaration_price,2);
		}
		//组织发件人数据
		$sender_name=$order->sender_name1;
		$sender_phone=$order->sender_mobile;
		$sender_city=$order->sender_city;
		$sender_area='';
		//发件人州/省
		$sender_province = $order->sender_state_region_code;
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_country_code=$order->sender_country_code;
		//发件人公司
		$sender_company = $order->sender_name2;
		$channel=ReturnChannel::find('channel_id = ?',$channel_id)->getOne();
		if($channel->sender_id>0){
			$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
			//渠道的发件人信息
			if(!$sender->isNewRecord()){
				$sender_name=$sender->sender_name;
				$sender_phone=$sender->sender_phone;
				//发件人城市
				$sender_city=$sender->sender_city;
				$sender_address=$sender->sender_address;
				$sender_zip_code=$sender->sender_zip_code;
				//发件人国家二字代码
				$sender_country_code='CN';
				$sender_province=$sender->sender_province;
				$sender_company=$sender->sender_company;
			}
		}
		//发件人信息
		$shipper = array(
			'shipper_name' => $sender_name,
			'shipper_company'=>$sender_company,
			//发件人国家二字代码
			'shipper_countrycode' => $sender_country_code,
			'shipper_province' => $sender_province,
			'shipper_city' => $sender_city,
			'shipper_street' => $sender_address,
			//发件人邮编
			'shipper_postcode' => $sender_zip_code,
			'shipper_telephone' => $sender_phone
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
		$state='';
		//判断收件国家是否是US和CA
		if($order->consignee_country_code=='US' || $order->consignee_country_code=='CA'){
			//将收件人州转为二字码
			$states=Uscaprovince::find('province_name=? or province_code_two=?',strtolower(str_replace(' ','',$order->consignee_state_region_code)),strtoupper(str_replace(' ','',$order->consignee_state_region_code)))->getOne();
			if($states->isNewRecord()){
				$view['errormessage']=$order->consignee_country_code.'中不存在'.$order->consignee_state_region_code.'州/省';
				return $view;
			}else{
				$state=$states->province_code_two;
			}
		}else{
			$state=$order->consignee_country_code;
		}
		//判断收件人税号是否存在
		$vat='';
		if($order->tax_payer_id){
			$vat=' VAT:'.$order->tax_payer_id;
		}
		
		$consignee = array(
			'consignee_name'=>$order->consignee_name1,
			'consignee_company'=>'',
			'consignee_countrycode'=>$order->consignee_country_code,
			'consignee_province'=>$state,
			'consignee_city'=>$order->consignee_city,
			'consignee_street'=>$order->consignee_street1.' '.$order->consignee_street2,
			'consignee_postcode'=>$order->consignee_postal_code,
			'consignee_telephone'=>$phone,
			'consignee_tariff'=>$order->tax_payer_id?$order->tax_payer_id:'',
			'consignee_email'=>$order->consignee_email
		);
		$far_out_package_count = Returnoutpackage::find('return_order_id=?',$order->return_order_id)->sum('quantity','sum_quantity')->getAll();
		$paramsjson = array(
			'reference_no'			=> $order->ali_order_no,
			'shipping_method' 		=> 'PK0157',//普货全部都是红单和IP服务
			//服务商单号
			'shipping_method_no' 	=> $order->far_no,
			'order_weight' 			=> $order->weight_actual_out,
			//出库包裹数量
			'order_pieces' 			=> $far_out_package_count['sum_quantity'],
			'cargotype'    			=> $cargo_type,
			'insurance_value'       => 0,
			'mail_cargo_type'       => '4',
			'shipper' 				=> $shipper,
			'consignee' 			=> $consignee,
			'invoice' 				=> $invoice,
			'custom_hawbcode'       => "",//原单号
		);
		//创建订单并预报
		$createorder_post_body = 'appToken='.$apptoken.'&appKey='.$appkey.'&serviceMethod=createorder'.'&paramsJson='.json_encode($paramsjson);
		//记录日志
		QLog::log('runfeng订单数据：'.json_encode($paramsjson));
		//订单日志
		$log=new OrderLog(array(
			'order_id'=>$order->order_id,
			'staff_id'=>MyApp::currentUser('staff_id'),
			'staff_name'=>MyApp::currentUser('staff_name'),
			//json
			'comment'=>'runfeng订单报文：'.json_encode($paramsjson)
		));
		$log->save();
		$createorder_response=Helper_Curl::post($api_url, $createorder_post_body,$requestheader);
		$createorder_response=json_decode($createorder_response,true);
		if($createorder_response['success']){
			$tracking_no=$createorder_response['data']['channel_hawbcode'];
			//组织API打印标签数据
			$getnewlabel_paramsjson=array(
				'ConfigInfo'=>array(
					'lable_file_type'=>'2',
					'lable_paper_type'=>'',
					'lable_content_type'=>'1',
					'additional_info'=>array(
						'lable_print_invoiceinfo'=>'N',
						'lable_print_buyerid'=>'N',
						'lable_print_datetime'=>'Y',
						'customsdeclaration_print_actualweight'=>'Y'
					)
				),
				'listorder'=>array(
					array(
						'reference_no'=>$createorder_response['data']['refrence_no'],
					),
				)
			);
			//API打印标签
			$getnewlabel_post_body = 'appToken='.$apptoken.'&appKey='.$appkey.'&serviceMethod=getnewlabel&paramsJson='.json_encode($getnewlabel_paramsjson);
			$getnewlabel_response=Helper_Curl::post($api_url, $getnewlabel_post_body,$requestheader);
			//记录日志
			QLog::log('runfengAPI打印标签数据：'.json_encode($getnewlabel_paramsjson));
			//订单日志
			$log=new OrderLog(array(
				'order_id'=>$order->order_id,
				'staff_id'=>MyApp::currentUser('staff_id'),
				'staff_name'=>MyApp::currentUser('staff_name'),
				//json
				'comment'=>'runfengAPI打印标签报文：'.json_encode($getnewlabel_paramsjson)
			));
			$log->save();
			$getnewlabel_response=json_decode($getnewlabel_response,true);
			//上传面单路径
			$dir=Q::ini('upload_tmp_dir');
			@Helper_Filesys::mkdirs($dir);
			if ($getnewlabel_response['success']){
				//--
				//获取面单信息
				$source=trim(file_get_contents($getnewlabel_response['data'][0]['lable_file']));
				//面单存在且是PDF
				if (strlen($source) && substr($source, 0,4)=="%PDF")
				{
					$target=$dir.DS.$tracking_no.'.pdf';
					file_put_contents($target,$source);
					Helper_PDF::split($target,$target);//保留一半的面单
					//将pdf转为jpg格式
					exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$target} -append {$target}.jpg");
					//扫描图片中条形码获取物流单号
					$image = new ZBarCodeImage($target.'.jpg');
					$scanner = new ZBarCodeScanner();
					$barcode = $scanner->scan($image);
					//记录日志：扫描结果
					QLog::log('scan'.json_encode($barcode));
					$barcode=Helper_Array::getCols($barcode, 'data');
					$barcode=array_unique(array_reverse($barcode));
					//存入sub_code表中
					//删除原有的子单
					ReturnSubcode::find('order_id=?',$order->return_order_id)->getAll()->destroy();
					//保存新单号
					foreach ($barcode as $temp){
						if(strlen($temp)=='18'){
							$order_subcode=new ReturnSubcode();
							$order_subcode->changeProps(array(
								'order_id'=>$order->return_order_id,
								'sub_code'=>$temp
							));
							$order_subcode->save();
						}
					}
					
					//将tracking_number存入order中
					$order->new_tracking_no = $tracking_no;
					//保存
					$order->save();
				}
			}else{
				$view['errormessage']='获取面单失败';
				return $view;
			}
			//末端单号条形码
			$png_target=$dir.DS.$tracking_no.'.png';
			$png_source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$tracking_no));
			//保存末端单号条形码--
			file_put_contents($png_target,$png_source);
			$uploadoss = new Helper_AlipicsOss();
			//上传到oss
			$miandan_data = $uploadoss->uploadAlifiles($tracking_no.'.pdf');
			if ($uploadoss->doesExist($tracking_no.'.pdf')){
				//上传成功，删除
				unlink($dir.DS.$tracking_no.'.pdf');
			}
			$shipper=array(
				'account'=>'4F1R24',
				'aname'=>'RunFeng Network Technology Co.',
				//中文符号乱码
				'name'=>'SuZhou RunFeng Network Technology Co., Ltd.(YiWu branch)',
				'address'=>'No. 41 HanChun Two area BeiYuan Street Yiwu',
				'city'=>'YIWU',
				'postcode'=>'322000',
				'phone'=>'17802119771'
			);
			$chapter1='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter1.gif';
			$chapter2='http://'.$_SERVER["HTTP_HOST"].'/public/img/chapter2.gif';
			//--
			$copy_label='http://'.$_SERVER["HTTP_HOST"].'/_tmp/upload/'.$tracking_no.'.pdf.jpg';
			$view['errormessage']='';
			$view['account']='RF';
		}else{
			$view['errormessage']=$createorder_response['cnmessage'];
		}
		return $view;
	}
}