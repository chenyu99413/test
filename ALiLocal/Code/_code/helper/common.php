<?php
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/fpdf.php';
require_once _INDEX_DIR_ . '/_code/helper/PDFMerger/fpdf/chinese.php';
/**
 * @todo   通用方法
 * @author stt
 * @since  2020-09-10
 * @link   #82496
 */
class Helper_Common{
	/**
	 * @todo   获取面单
	 * @author stt
	 * @since  2020-09-10
	 * @link   #82496
	 */
	static function oldGetlabel($order,$account_name,$channel_id){
		if(empty($account_name)){
			$data['errormessage']='账号不能为空';
		}else {
			if($account_name=='EMS'){
				$data=Helper_Label_Ems::ems($order);
			}elseif ($account_name=='EUB'){
				$data=Helper_Label_Eub::eub($order);
			}elseif ($account_name=='FEDEX'){
				$data=Helper_Label_Fedex::fedex($order,$channel_id);
			}elseif ($account_name=='US-FY'){
				if($channel_id=='27'){
					$data=Helper_Label_Fsp::fsp($order,$channel_id);
				}else{
					$data=Helper_Label_Kingspeed::kingspeed($order, $channel_id);
				}
			}elseif (in_array($account_name, array('ML3002','ML3001','ML0715','ML0716','ML051501','SH-DHL-MX'))){
				$data=Helper_Label_Hlt::hlt($order, $account_name, $channel_id);
			}elseif ($account_name=='DHL'){
				$data=Helper_Label_Dhl::dhl($order, $channel_id);
			}else {
				if($account_name=='4F1R24'){
					$data=Helper_Label_Runfeng::runfeng($order);
				}if($account_name=='9Y3152'){
					$account=UPSAccount::find("account = ?",$account_name)->getOne();
					//print_r($account);exit;
					$data=Helper_Label_Hualei::hualei($order,$account);
				}else{
					$account=UPSAccount::find("account = ?",$account_name)->getOne();
					if(!$account->isNewRecord()){
						if($account->account_level==2){
							$data=Helper_Label_Aliups::aliups($order,$account);
						}else{
							$data=Helper_Label_Ups::ups($order,$account);
						}
					}else{
						$data['errormessage']=$account_name.'账号不存在';
					}
				}
			}
		}
		
		return $data;
	}
	/**
	 * @todo   获取面单
	 * @author stt
	 * @since  2020-09-22
	 * @link   #82791
	 */
	static function getLabel($order,$account_name,$channel_id,$rorder = 0){
		//区分渠道和退件渠道
		if($rorder === 0){
			//渠道
			$channel = Channel::find('channel_id=?',$channel_id)->getOne();
		}else{
			//退件渠道
			$channel = ReturnChannel::find('channel_id=?',$channel_id)->getOne();
		}
		//打单方式“快件UPS”
		if ($channel->print_method=='ups'){
			if ($account_name){
				$account=UPSAccount::find("account = ?",$account_name)->getOne();
				//账号不能为空
				if($account->isNewRecord()){
					$data['errormessage']='账号不存在';
					return $data;
				}
			}else{
				//账号不能为空
				$data['errormessage']='账号不能为空';
				return $data;
			}
			
			
			//校验打单账号与渠道管理里的打单账号的一致性
			if ($account_name != $channel->account){
				$data['errormessage']='UPS指定账号异常';
				return $data;
			}
			//ups
			$data=Helper_Label_Ups::ups($order,$account,$channel_id,$rorder);
			$data['account_number'] = $account->account;
		//打单方式“上海UPS”
		}else{
			if ($channel->print_method=='aliups'){
				//渠道里面设置的账号
				$account=UPSAccount::find("account = ? and account_level=2",$channel->account)->getOne();
				if ($account_name){
					$account=UPSAccount::find("account = ? and account_level=2",$account_name)->getOne();
				}
				//账号不能为空 
				if ($account->isNewRecord()){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				//aliups
				//所有调用打单方法增加参数$rorder
				$data=Helper_Label_Aliups::aliups($order,$account,$channel_id,$rorder);
				$data['account_number'] = $account->account;
			}elseif ($channel->print_method=='dhl'){
				$data=Helper_Label_Dhl::dhl($order, $channel_id,$rorder);
				$data['account_number'] = 'DHL';
			}elseif ($channel->print_method=='ems'){
				$data=Helper_Label_Ems::ems($order,$rorder);
				$data['account_number'] = 'EMS';
			}elseif ($channel->print_method=='eub'){
				$data=Helper_Label_Eub::eub($order,$rorder);
				$data['account_number'] = 'EUB';
			}elseif ($channel->print_method=='fedex'){
				$data=Helper_Label_Fedex::fedex($order,$channel_id,$rorder);
				$data['account_number'] = 'FEDEX';
			}elseif ($channel->print_method=='ib'){
				//泛远中美专线IB
				$data=Helper_Label_IB::ib($order,$channel_id,$rorder);
				$data['account_number'] = 'IB';
			}elseif ($channel->print_method=='hlt'){
				if(empty($channel->account)){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				$data=Helper_Label_Hlt::hlt($order, $channel->account, $channel_id,$rorder);
				$data['account_number'] = $channel->account;
			}elseif ($channel->print_method=='hualei'){
				//华磊用渠道里面设置的账号
				$account=UPSAccount::find("account = ?",$channel->account)->getOne();
				if ($account->isNewRecord()){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				//hualei
				$data=Helper_Label_Hualei::hualei($order,$account,$channel_id,$rorder);
				$data['account_number'] = $account->account;
			}elseif ($channel->print_method=='kingspeed'){
				$data=Helper_Label_Kingspeed::kingspeed($order, $channel_id,$rorder);
				$data['account_number'] = 'US-FY';
			}elseif ($channel->print_method=='runfeng'){
				//runfeng
				$data=Helper_Label_Runfeng::runfeng($order,$channel_id,$rorder);
				$data['account_number'] = 'RF';
			}elseif ($channel->print_method=='abcsp'){
				//abcsp
				$data=Helper_Label_Abcsp::abcsp($order,$channel_id);
				$data['account_number'] = 'ABC-SP';
			}elseif ($channel->print_method=='shuncheng'){
				//shuncheng
				$data=Helper_Label_ShunCheng::shuncheng($order,$channel_id);
				$data['account_number'] = 'shuncheng';
			}else{
				$data['errormessage']='打单方式不存在';
				return $data;
			}
		}
		return $data;
	}
	/**
	 * @todo   退件获取面单
	 * @author wukl
	 * @since  2020-12-15 15:40:49
	 * @link   #84502
	 */
	static function getReturnLabel($order,$account_name,$channel_id){
		//退件渠道
		$channel = ReturnChannel::find('channel_id=?',$channel_id)->getOne();
		//打单方式“快件UPS”
		if ($channel->print_method=='ups'){
			if ($account_name){
				$account=UPSAccount::find("account = ?",$account_name)->getOne();
				//账号不能为空
				if($account->isNewRecord()){
					$data['errormessage']='账号不存在';
					return $data;
				}
			}else{
				//账号不能为空
				$data['errormessage']='账号不能为空';
				return $data;
			}
			
			
			//校验打单账号与渠道管理里的打单账号的一致性
			if ($account_name != $channel->account){
				$data['errormessage']='UPS指定账号异常';
				return $data;
			}
			//ups
			$data=Helper_ReturnLabel_Ups::ups($order,$account,$channel_id);
			$data['account_number'] = $account->account;
			//打单方式“上海UPS”
		}else{
			if ($channel->print_method=='aliups'){
				//渠道里面设置的账号
				$account=UPSAccount::find("account = ? and account_level=2",$channel->account)->getOne();
				if ($account_name){
					$account=UPSAccount::find("account = ? and account_level=2",$account_name)->getOne();
				}
				//账号不能为空
				if ($account->isNewRecord()){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				//aliups
				//所有调用打单方法增加参数$rorder
				$data=Helper_ReturnLabel_Aliups::aliups($order,$account,$channel_id);
				$data['account_number'] = $account->account;
			}elseif ($channel->print_method=='dhl'){
				$data=Helper_ReturnLabel_Dhl::dhl($order, $channel_id);
				$data['account_number'] = 'DHL';
			}elseif ($channel->print_method=='ems'){
				$data=Helper_ReturnLabel_Ems::ems($order);
				$data['account_number'] = 'EMS';
			}elseif ($channel->print_method=='eub'){
				$data=Helper_ReturnLabel_Eub::eub($order);
				$data['account_number'] = 'EUB';
			}elseif ($channel->print_method=='fedex'){
				$data=Helper_ReturnLabel_Fedex::fedex($order,$channel_id);
				$data['account_number'] = 'FEDEX';
			}elseif ($channel->print_method=='ib'){
				//泛远中美专线IB
				$data=Helper_ReturnLabel_IB::ib($order,$channel_id);
				$data['account_number'] = 'IB';
			}elseif ($channel->print_method=='hlt'){
				if(empty($channel->account)){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				$data=Helper_ReturnLabel_Hlt::hlt($order, $channel->account, $channel_id);
				$data['account_number'] = $channel->account;
			}elseif ($channel->print_method=='hualei'){
				//华磊用渠道里面设置的账号
				$account=UPSAccount::find("account = ?",$channel->account)->getOne();
				if ($account->isNewRecord()){
					$data['errormessage']='账号不能为空';
					return $data;
				}
				//hualei
				$data=Helper_ReturnLabel_Hualei::hualei($order,$account,$channel_id);
				$data['account_number'] = $account->account;
			}elseif ($channel->print_method=='kingspeed'){
				$data=Helper_ReturnLabel_Kingspeed::kingspeed($order, $channel_id);
				$data['account_number'] = 'US-FY';
			}elseif ($channel->print_method=='runfeng'){
				//runfeng
				$data=Helper_ReturnLabel_Runfeng::runfeng($order,$channel_id);
				$data['account_number'] = 'RF';
			}else{
				$data['errormessage']='打单方式不存在';
				return $data;
			}
		}
		return $data;
	}
	static function Checklabel($order) {
		$data=array();
		//获取订单总重
		$total_weight='';
		$package_sum=Faroutpackage::find('order_id=?',$order->order_id)->getAll();
		foreach ($package_sum as $v){
			$total_weight+=$v->weight_out*$v->quantity_out;
		}
		//将重量存入order中
		$order->weight_actual_out=($order->service_code == 'ePacket-FY')?sprintf('%.3f',$total_weight):sprintf('%.2f',$total_weight);
		$order->Save();
		//获取产品
		if($order->channel_id >0){
			//调用打单方法
			$account_name = '';
			$view=Helper_Common::getLabel($order ,$account_name,$order->channel_id);
			if(!isset($view['errormessage']) || $view['errormessage']!=''){
				//渠道获取面单失败
				$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
				
				$remark = $data['message'];
				$order->remark = $remark;
				$order->save();
				$data['code']='4040';
				if ($view['errormessage']=='账号不能为空'){
					$data['message']='系统异常，请联系客服';
					$data['code']='9999';
				}
				return $data;
			}else{
				//存入打单账号
				$order->account=$view['account_number'];
				$order->save();
				$data['account']=$view['account'];
				$data['message']='success';
			}
		}else{
			$product=Product::find('product_name=?',$order->service_code)->getOne();
			//获取渠道成本
			$channelcost=ChannelCost::find('product_id=?',$product->product_id)->getAll();
			if(count($channelcost)<=0){
				$remark = '该产品下无渠道';
				$order->remark = $remark;
				$order->save();
				//渠道需优化
				$data['message']='系统异常，请联系客服';
				$data['code']='9999';
				return $data;
			}else{
				//计算成本价格
				$price_array=array();
				$price_info_array=array();
				$limit_ids = array();
				$flag = 0;
				foreach ($channelcost as $temp){
					//判断渠道可用部门和禁用部门
					$available_department_ids=Helper_Array::getCols(Channeldepartmentavailable::find('channel_id=?',$temp->channel_id)->getAll(), 'department_id');
					$disabled_department=Channeldepartmentdisable::find('channel_id=? and department_id=? and effect_time <= ? and failure_time >= ?',$temp->channel_id,$order->department_id,time(),time())->getOne();
					if((count($available_department_ids)>0 && !in_array($order->department_id, $available_department_ids)) || !$disabled_department->isNewRecord()){
						continue;
					}
					//获取价格-偏派-分区表
					$channelcostppr=Channelcostppr::find('channel_cost_id=? and effective_time<=? and invalid_time>=?',$temp->channel_cost_id,time(),time())->getOne();
					if($channelcostppr->isNewRecord()){
						continue;
					}
					$temp_channel = Channel::find('channel_id=?',$temp->channel_id)->getOne();
					//是否支持带电
					if($order->has_battery==1){
						if ($temp_channel->has_battery!=1){
							continue;
						}
					}
					//是否支持申报
					if($order->declaration_type=='DL'){
						if ($temp_channel->is_declaration!=1){
							continue;
						}
					}
					//申报总价阈值
					foreach ($order->faroutpackages as $faroutpackage){
						
						$arr = array($faroutpackage->length_out,$faroutpackage->width_out,$faroutpackage->height_out);
						sort($arr);
						//最长边限制
						if($temp_channel->length && $arr[2]>=$temp_channel->length){
							continue;
						}
						//第二长边限制
						if ($temp_channel->width && $arr[1]>=$temp_channel->width){
							continue;
						}
						//高限制
						if ($temp_channel->height && $arr[0]>=$temp_channel->height){
							continue;
						}
						//周长限制
						if ($temp_channel->perimeter && 4*($arr[2]+$arr[1]+$arr[0])>=$temp_channel->perimeter){
							continue;
						}
						//围长限制
						if ($temp_channel->girth && $arr[2]+2*($arr[1]+$arr[0])>=$temp_channel->girth){
							continue;
						}
						//单个包裹实重限制
						if ($temp_channel->weight && $faroutpackage->weight_out>=$temp_channel->weight){
							continue;
						}
					}
					//申报总价阈值
// 					if ($temp_channel->declare_threshold){
// 						if($order->total_amount>$temp_channel->declare_threshold){
// 							continue;
// 						}
// 					}
					//新·申报总价限制
					$declare_th = Channeldeclarethreshold::find('channel_id=?',$temp_channel->channel_id)->getAll();
					foreach ($declare_th as $dt){
						//如果在国家组
						$country_group = CodeCountryGroup::find ('id=?',$dt->country_group_id)->getOne ();
						$country = explode(',', $country_group->country_codes);
						if(in_array($order->consignee_country_code, $country)){
							//判断是否在区间内
							if($order->total_amount >= $dt->front && $order->total_amount <= $dt->after){
								continue 2;
							}
						}
					}
					
					//整票计费重
					if ($temp_channel->total_cost_weight){
						if($order->weight_cost_out>$temp_channel->total_cost_weight){
							continue;
						}
					}
					$department_id = $order->department_id;
					if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $order->department_id=='23' && MyApp::currentUser('department_id') <> '23'){
						$department_id=MyApp::currentUser('department_id');
					}
					
					
					$network=Network::find("network_code=? ",$temp->channel->network_code)->getOne();
					$quote= new Helper_Quote();
					if ($order->customer->customs_code=='ALCN'){
						$cainiaofee = new Helper_CainiaoFee();
						$price=$cainiaofee->payment($order, $channelcostppr,$network->network_id);
					}else{
						$price=$quote->payment($order, $channelcostppr,$network->network_id);
					}
					if(count($price)<=0){
						continue;
					}
					if(!$price['public_price']){
						continue;
					}
					
					//如果设置阈值
					if ($product->threshold){
						//计算应收
						$total_receivable = Fee::find("fee_type= '1' and order_id=?",$order->order_id)->getAll();
						$public_price = 0;
						foreach ($total_receivable as $tot_r){
							if($tot_r->currency != 'CNY'){
								$public_price += Helper_Quote::exchangeRate($order->warehouse_confirm_time,$tot_r->amount, $tot_r->currency,0,'',$tot_r->rate);
							}else{
								$public_price += $tot_r->amount;
							}
						}
						//应收-应付
						$maoli = $public_price-$price['public_price'];
						if ($maoli<$product->threshold){
							$flag = 1;
							continue;
						}
					}
					$price_array[$channelcostppr->channel_cost_p_p_r_id]=$price['public_price'];
					$price_info_array[$channelcostppr->channel_cost_p_p_r_id]=$price['price_info'];
				}
				//判断是否有查询失败的报价
				if(count($price_array)==0 || max($price_array)==0){
					if ($flag==1){
						$remark = '渠道需优化';
						$order->remark = $remark;
						$order->save();
						//渠道需优化
						$data['message']='系统异常，请联系客服';
						$data['code']='9999';
						return $data;
						
					}else{
						$remark = '无可用渠道';
						$order->remark = $remark;
						$order->save();
						//无可用渠道
						$data['message']='系统异常，请联系客服';
						$data['code']='9999';
						return $data;
					}
				}else{
					//获取最小的价格和价格表id
					$channel_cost_p_p_r_id=array_search(min($price_array), $price_array);
					$channel_cost_p_p_r=Channelcostppr::find('channel_cost_p_p_r_id=?',$channel_cost_p_p_r_id)->getOne();
					$channel_cost=ChannelCost::find('channel_cost_id=?',$channel_cost_p_p_r->channel_cost_id)->getOne();
					//实际此时是产品代码proudct_code
					$account_name=$price_info_array[$channel_cost_p_p_r_id]['account'];
					$account_sync=Accountsync::find('product_code=?',$account_name)->getOne();
					$channel=Channel::find('channel_id = ?',$channel_cost->channel_id)->getOne();
					if(!$account_sync->isNewRecord()){
						$account_name=$account_sync->account;
					}
					//调用打单方法
					$view=Helper_Common::getLabel($order,$account_name,$channel_cost->channel_id);
					if(!isset($view['errormessage']) || $view['errormessage']!=''){
						//渠道获取面单失败
						$data['message']=isset($view['errormessage']) ? $view['errormessage'] : '打单失败！';
						$remark = $data['message'];
						$order->remark = $remark;
						$order->save();
						$data['code']='4040';
						return $data;
					}else{//结束
						//保存出库渠道
						$order->channel_id=$channel_cost->channel_id;
						//存入打单账号
						$order->account=$view['account_number'];
						$order->save();
						$data['account']=$view['account'];
						$data['message']='success';
					}
				}
			}
			
		}
		return $data;
		exit();
	}
	/**
	 * @todo   获取泛远面单
	 * @author stt
	 * @since  2020-09-16
	 * @link   #82496
	 */
	static function getfarlabel($order){
			$i=1;
			$total_value='';
			$invoice = array();
			$goods_info = '';
			foreach ($order->product as $v){
				if($i>=4){
					break;
				}
				$invoice['items'][]=array(
					'prdoduct_name_hs'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
					'quantity'=>$v->product_quantity,
					'price'=>$v->declaration_price,
					'itotal'=>round($v->product_quantity*$v->declaration_price,2),
				);
				$goods_info .= $v->goods_info.'*'.$v->product_quantity.',';
				$total_value += round($v->product_quantity*$v->declaration_price,2);
				$i++;
			}
			$toal_package=Farpackage::find('order_id=?',$order->order_id)->getSum('quantity');
			$total_weight=sprintf("%.2f",$order->weight_income_in);
			
			//仓库名称
			$consignor='From Consignor : ';
			//发件人相关信息：
			$shipper="Shipper: ";
			$warehouse_data = CodeWarehouse::find('department_id=? and warehouse=?',$order->department_id,$order->warehouse_code)->getOne();
			if (!$warehouse_data->isNewRecord()){
				$consignor.= $warehouse_data->warehouse_enname;
				$shipper.= $warehouse_data->warehouse_address;
				$shipper.= $warehouse_data->warehouse_contact;
				$shipper.= $warehouse_data->warehouse_mobile;
			}
// 			if($order->department_id==6){
// 				$consignor.="Far's warehouse in Hangzhou";
// 				$shipper.='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022 ';
// 				$shipper.='Miss.Zhang ';
// 				$shipper.='0571-87834076';
// 				//上海仓
// 			}elseif ($order->department_id==7){
// 				$consignor.="Far's warehouse in Shanghai";
// 				$shipper.='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323 ';
// 				$shipper.='Mr.Gu ';
// 				$shipper.='021-58590952';
// 				//义乌仓
// 			}elseif ($order->department_id==8){
// 				$consignor.="Far's warehouse in Yiwu";
// 				$shipper.='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000 ';
// 				$shipper.='Mr.Yang ';
// 				$shipper.='0579-85119351';
// 				//广州仓
// 			}elseif ($order->department_id==22){
// 				$consignor.="Far's warehouse in Guangzhou";
// 				$shipper.='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080 ';
// 				$shipper.='Miss.Li ';
// 				$shipper.='020-36301839';
// 				//青岛仓
// 			}elseif ($order->department_id==23){
// 				$consignor.="Far's warehouse in Qingdao";
// 				$shipper.='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108 ';
// 				$shipper.='Mr Wang ';
// 				$shipper.='18661786160';
// 				//深圳仓
// 			}elseif ($order->department_id==24){
// 				$consignor.="FAR's warehouse in Shenzhen Longhua";
// 				$shipper.='Unit 5,No.8 Non-bonded Warehouse, South China International Logistics Center,No.1 Mingkang Road,Minzhi Street,Longhua New District,Shenzhen City,China 518000 ';
// 				$shipper.='Mr Wang ';
// 				$shipper.='4000857988';
// 			}
			
			$dir=Q::ini('upload_tmp_dir');
			@Helper_Filesys::mkdirs($dir);
			$barcode=$dir.DS.$order->ali_order_no.'.barcode.png';
			$logo=$dir.DS.'logo.jpg';
			$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$order->ali_order_no));
			file_put_contents($barcode, $source);
			$pdf=new PDF_Chinese('L','mm','far');
			$pdf->AddGBFont('simhei', '黑体');
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',12);
			//客户单号
			$pdf->Cell(84,6,'Order No. :'.$order->order_no,'1');
			$pdf->Cell(66,6,'','TR');
			$pdf->Ln();
			$pdf->Cell(84,20,'','1','','C');
			//泛远单号条形码
			$pdf->Image($barcode,'12','19','80','14');
			//泛远logo
			$pdf->Image($logo,'95','15','63','20');
			$pdf->Cell(66,20,'','R');
			$pdf->Ln();
			//泛远单号
			$pdf->Cell(84,6,$order->ali_order_no,'1','','C');
			$pdf->SetFont('Arial','B',12);
			//产品
			$pdf->Cell(56,6,$order->service_code,'B','','C');
			//产品
			$pdf->Cell(10,6,$order->delivery_priority,'RB','','C');
			$pdf->Ln();
			$pdf->SetFont('Arial','B',12);
			//仓库名称
			$pdf->Cell(150,8,$consignor,'1');
			$pdf->Ln();
			//发件人相关信息
			$pdf->MultiCell(150,8,$shipper,'1');
			//收件人姓名
			$pdf->MultiCell(150,6,'To Consignee: '.$order->consignee_name1.' '.$order->consignee_name2,'1');
			$pdf->SetFont('Arial','B',12);
			//收件人地址1
			$pdf->MultiCell(150,8,'Street Address1: '.$order->consignee_street1,'1');
			//收件人地址2
			$pdf->MultiCell(150,8,'Street Address2: '.$order->consignee_street2,'1');
			//收件人城市和邮编
			$pdf->Cell(99,8,'City&PostCode: '.$order->consignee_city.' '.$order->consignee_postal_code,'1');
			$pdf->SetFont('Arial','B',10);
			//收件人行政区/州
			$pdf->Cell(51,8,'State: '.$order->consignee_state_region_code,'1');
			$pdf->Ln();
			$pdf->SetFont('Arial','B',11);
			//收件人电话
			$pdf->Cell(99,8,'Contact Phone: '.$order->consignee_mobile,'1');
			//收件人国家
			$pdf->Cell(51,8,'Country: '.$order->consignee_country_code,'1');
			$pdf->Ln();
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(150,8,'SHIPMENT INFORMATION:','1','','C');
			$pdf->Ln();
			$pdf->Cell(99,7,'Main Products List','1','','C');
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(14,7,'Amount','1','','C');
			$pdf->Cell(20,7,'Price/Unit','1','','C');
			$pdf->Cell(17,7,'Subtotal','1','','C');
			foreach ($invoice['items'] as $in){
				$pdf->Ln();
				//不加粗
				$pdf->SetFont('simhei','',10);
				//FAR中文品名FAR英文品名FARHS编码
				$pdf->Cell(99,6,iconv("utf-8","gbk",$in['prdoduct_name_hs']),'1','','L');
				$pdf->SetFont('Arial','B',11);
				//产品数量
				$pdf->Cell(14,6,$in['quantity'],'1','','C');
				//单价
				$pdf->Cell(20,6,$in['price'],'1','','C');
				//总价
				$pdf->Cell(17,6,$in['itotal'],'1','','C');
			}
			$pdf->Ln();
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(99,6,'','1');
			$pdf->Cell(34,6,'Total Value(USD):','1','','C');
			//产品总价
			$pdf->Cell(17,6,$total_value,'1','','C');
			$pdf->Ln();
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(99,8,'Total packages','1','','C');
			$pdf->Cell(51,8,'Total Weight(KG):','1','','C');
			$pdf->Ln();
			//包裹件数
			$pdf->Cell(99,8,$toal_package,'1','','C');
			//包裹重
			$pdf->Cell(51,8,$total_weight,'1','','C');
			$pdf->Ln();
			$pdf->Cell(150,8,'Shipping Charges Payment Term: Prepaid&DDU','1');
			//配货信息
			$pdf->Ln();
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(150,9,'Packing List : ','1');
// 			$pdf->SetFont('Arial','B',11);
			$pdf->Ln();
			//不加粗
			$pdf->SetFont('simhei','',10);
			$pdf->Cell(150,10,iconv("utf-8","gbk",$goods_info),'1','L');
// 			$pdf->SetFont('Arial','B',11);
			$alilabel=$dir.DS.$order->order_no.'.pdf';
			$pdf->Output($alilabel,'F');
			$pdf->Close();
			unlink($barcode);
// 			exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$alilabel} -append {$alilabel}.jpg");
// 			$recordformdata=file_get_contents($alilabel.'.jpg');
			$recordformdata = file_get_contents($alilabel);
			$farlabel = base64_encode($recordformdata);
			$uploadoss = new Helper_AlipicsOss();
// 			// 上传到oss
			$invoice_data = $uploadoss->uploadAlifiles($order->order_no.'.pdf');
			if ($uploadoss->doesExist($order->order_no.'.pdf')){
// 				// 上传成功，删除
// 				unlink($dir.DS.$order->order_no.'.pdf');
			}
			return $farlabel;
	}
	
	/**
	 * @todo   发给阿里的泛远面单
	 * @author stt
	 * @since  2020-10-26
	 * @link   #82496
	 */
	static function getfarlabeltoali($order){
		$i=1;
		$total_value='';
		$invoice = array();
		foreach ($order->product as $v){
			if($i>=4){
				break;
			}
			$invoice['items'][]=array(
				'prdoduct_name_hs'=>$v->product_name_far.' '.$v->product_name_en_far.' '.$v->hs_code_far,
				'quantity'=>$v->product_quantity,
				'price'=>$v->declaration_price,
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$total_value += round($v->product_quantity*$v->declaration_price,2);
			$i++;
		}
		$toal_package=Farpackage::find('order_id=?',$order->order_id)->getSum('quantity');
		$total_weight=sprintf("%.2f",$order->weight_income_in);
		
		//仓库名称
		$consignor='From Consignor : ';
		//发件人相关信息：
		$shipper="Shipper: ";
		if($order->department_id==6){
			$consignor.="Far's warehouse in Hangzhou";
			$shipper.='1st Floor, No.43 Ganchang Road, Xiacheng District, Hangzhou, Zhejiang, China  310022 ';
			$shipper.='Miss.Zhang ';
			$shipper.='0571-87834076';
			//上海仓
		}elseif ($order->department_id==7){
			$consignor.="Far's warehouse in Shanghai";
			$shipper.='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area, Shanghai,China 201323 ';
			$shipper.='Mr.Gu ';
			$shipper.='021-58590952';
			//义乌仓
		}elseif ($order->department_id==8){
			$consignor.="Far's warehouse in Yiwu";
			$shipper.='No.675-2 Airport Road, Yiwu City, Zhejiang Province,China 322000 ';
			$shipper.='Mr.Yang ';
			$shipper.='0579-85119351';
			//广州仓
		}elseif ($order->department_id==22){
			$consignor.="Far's warehouse in Guangzhou";
			$shipper.='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District, Guangzhou City, Guangdong Province,China 510080 ';
			$shipper.='Miss.Li ';
			$shipper.='020-36301839';
			//青岛仓
		}elseif ($order->department_id==23){
			$consignor.="Far's warehouse in Qingdao";
			$shipper.='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District, Qingdao City, Shandong Province,China 266108 ';
			$shipper.='Mr Wang ';
			$shipper.='18661786160';
			//深圳仓
		}elseif ($order->department_id==24){
			$consignor.="FAR's warehouse in Shenzhen Longhua";
			$shipper.='Unit 5,No.8 Non-bonded Warehouse, South China International Logistics Center,No.1 Mingkang Road,Minzhi Street,Longhua New District,Shenzhen City,China 518000 ';
			$shipper.='Mr Wang ';
			$shipper.='4000857988';
		}
		
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		$barcode=$dir.DS.$order->order_id.'.barcode.png';
		//--
		$logo=$dir.DS.'logo.jpg';
		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=20&rotation=0&font_family=0&font_size=0&thickness=70&start=C&code=BCGcode128&text='.$order->far_no));
		file_put_contents($barcode, $source);
		$pdf=new PDF_Chinese('L','mm','far');
		$pdf->AddGBFont('simhei', '黑体');
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		//阿里单号
		$pdf->Cell(84,6,'Order No. :'.$order->ali_order_no,'1');
		$pdf->Cell(66,6,'','TR');
		$pdf->Ln();
		$pdf->Cell(84,20,'','1','','C');
		//泛远单号条形码
		$pdf->Image($barcode,'12','19','80','14');
		//泛远logo
		$pdf->Image($logo,'95','15','63','20');
		$pdf->Cell(66,20,'','R');
		$pdf->Ln();
		//泛远单号
		$pdf->Cell(84,6,$order->far_no,'1','','C');
		$pdf->SetFont('Arial','B',10);
		//产品
		$pdf->Cell(66,6,$order->service_code,'RB','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		//仓库名称
		$pdf->Cell(150,8,$consignor,'1');
		$pdf->Ln();
		//发件人相关信息
		$pdf->MultiCell(150,8,$shipper,'1');
		//收件人姓名
		$pdf->MultiCell(150,6,'To Consignee: '.$order->consignee_name1.' '.$order->consignee_name2,'1');
		$pdf->SetFont('Arial','B',12);
		//收件人地址1
		$pdf->MultiCell(150,8,'Street Address1: '.$order->consignee_street1,'1');
		//收件人地址2
		$pdf->MultiCell(150,8,'Street Address2: '.$order->consignee_street2,'1');
		//收件人城市和邮编
		$pdf->Cell(99,8,'City&PostCode: '.$order->consignee_city.' '.$order->consignee_postal_code,'1');
		$pdf->SetFont('Arial','B',10);
		//收件人行政区/州
		$pdf->Cell(51,8,'State: '.$order->consignee_state_region_code,'1');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',11);
		//收件人电话
		$pdf->Cell(99,8,'Contact Phone: '.$order->consignee_mobile,'1');
		//收件人国家
		$pdf->Cell(51,8,'Country: '.$order->consignee_country_code,'1');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(150,8,'SHIPMENT INFORMATION:','1','','C');
		$pdf->Ln();
		$pdf->Cell(99,7,'Main Products List','1','','C');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(14,7,'Amount','1','','C');
		$pdf->Cell(20,7,'Price/Unit','1','','C');
		$pdf->Cell(17,7,'Subtotal','1','','C');
		foreach ($invoice['items'] as $in){
			$pdf->Ln();
			$pdf->SetFont('simhei','',9);
			//FAR中文品名FAR英文品名FARHS编码
			$pdf->Cell(99,6,iconv("utf-8","gbk",$in['prdoduct_name_hs']),'1','','L');
			$pdf->SetFont('Arial','B',11);
			//产品数量
			$pdf->Cell(14,6,$in['quantity'],'1','','C');
			//单价
			$pdf->Cell(20,6,$in['price'],'1','','C');
			//总价
			$pdf->Cell(17,6,$in['itotal'],'1','','C');
		}
		$pdf->Ln();
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(99,6,'','1');
		$pdf->Cell(34,6,'Total Value(USD):','1','','C');
		//产品总价
		$pdf->Cell(17,6,$total_value,'1','','C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(99,8,'Total packages','1','','C');
		$pdf->Cell(51,8,'Total Weight(KG):','1','','C');
		$pdf->Ln();
		//包裹件数
		$pdf->Cell(99,8,$toal_package,'1','','C');
		//包裹重
		$pdf->Cell(51,8,$total_weight,'1','','C');
		$pdf->Ln();
		$pdf->Cell(150,8,'Shipping Charges Payment Term: Prepaid&DDU','1');
		$pdf->Ln();
		$pdf->MultiCell(150,7,'Declaration Statement:I hereby certify that the information of this invoice is truce and correct and the contents and value of this shipment is as stated above.','1','L');
		$alilabel=$dir.DS.$order->order_no.'.pdf';
		$pdf->Output($alilabel,'F');
		$pdf->Close();
		unlink($barcode);
		$uploadoss = new Helper_AlipicsOss();
		// 上传到oss
		$invoice_data = $uploadoss->uploadAlifiles($order->order_no.'.pdf');
		if ($uploadoss->doesExist($order->order_no.'.pdf')){
			// 上传成功，删除
// 			unlink($dir.DS.$order->order_no.'.pdf');
		}
	}
	/**
	 * 将一个字符串中的全角字符转换为半角,返回转换后的字符串
	 *
	 * @param string $str 待转换字串
	 * @return string $str 处理后字串
	 */
	static function convertStrType($str)
	{
		$dictionary= array(
			'０'=>'0', '１'=>'1', '２'=>'2', '３'=>'3', '４'=>'4','５'=>'5', '６'=>'6', '７'=>'7', '８'=>'8', '９'=>'9',
			'Ａ'=>'A', 'Ｂ'=>'B', 'Ｃ'=>'C', 'Ｄ'=>'D', 'Ｅ'=>'E','Ｆ'=>'F', 'Ｇ'=>'G', 'Ｈ'=>'H', 'Ｉ'=>'I', 'Ｊ'=>'J',
			'Ｋ'=>'K', 'Ｌ'=>'L', 'Ｍ'=>'M', 'Ｎ'=>'N', 'Ｏ'=>'O','Ｐ'=>'P', 'Ｑ'=>'Q', 'Ｒ'=>'R', 'Ｓ'=>'S', 'Ｔ'=>'T',
			'Ｕ'=>'U', 'Ｖ'=>'V', 'Ｗ'=>'W', 'Ｘ'=>'X', 'Ｙ'=>'Y','Ｚ'=>'Z', 'ａ'=>'a', 'ｂ'=>'b', 'ｃ'=>'c', 'ｄ'=>'d',
			'ｅ'=>'e', 'ｆ'=>'f', 'ｇ'=>'g', 'ｈ'=>'h', 'ｉ'=>'i','ｊ'=>'j', 'ｋ'=>'k', 'ｌ'=>'l', 'ｍ'=>'m', 'ｎ'=>'n',
			'ｏ'=>'o', 'ｐ'=>'p', 'ｑ'=>'q', 'ｒ'=>'r', 'ｓ'=>'s', 'ｔ'=>'t', 'ｕ'=>'u', 'ｖ'=>'v', 'ｗ'=>'w', 'ｘ'=>'x',
			'ｙ'=>'y', 'ｚ'=>'z',
			'（'=>'(', '）'=>')', '〔'=>'(', '〕'=>')', '【'=>'[','】'=>']', '〖'=>'[', '〗'=>']', '“'=>'"', '”'=>'"',
			'‘'=>'\'', '\''=>'\'', '｛'=>'{', '｝'=>'}', '《'=>'<','》'=>'>','％'=>'%', '＋'=>'+', '—'=>'-', '－'=>'-',
			'～'=>'~','：'=>':', '。'=>'.', '、'=>',', '，'=>',', '、'=>',', '；'=>';', '？'=>'?', '！'=>'!', '…'=>'-',
			'‖'=>'|', '”'=>'"', '\''=>'`', '‘'=>'`', '｜'=>'|', '〃'=>'"','　'=>' ', '×'=>'*', '￣'=>'~', '．'=>'.', '＊'=>'*',
			'＆'=>'&','＜'=>'<', '＞'=>'>', '＄'=>'$', '＠'=>'@', '＾'=>'^', '＿'=>'_', '＂'=>'"', '￥'=>'$', '＝'=>'=',
			'＼'=>'\\', '／'=>'/' ,'＃'=>'#','！'=>'!'
		);
		return strtr($str, $dictionary);
	}
	/**
	 * 导出高价数据时生成shipid(UPS)
	 */
	static function creatShipid($waybillcode){
		$waybillcode=trim($waybillcode);
		if(strlen($waybillcode)<17){
			return $waybillcode;
		}
		$acc=substr($waybillcode,2,6);
		$num=substr($waybillcode,10,7);
		$num26=strtoupper(base_convert((int)$num, 10, 26));
		$dict1=array(
			"0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","A"=>"10","B"=>"11","C"=>"12","D"=>"13","E"=>"14","F"=>"15","G"=>"16","H"=>"17","I"=>"18","J"=>"19","K"=>"20","L"=>"21","M"=>"22","N"=>"23","O"=>"24","P"=>"25"
		);
		$dict2=array("3","4","7","8","9","B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","W","X","Y","Z");
		$r='';
		if(strlen($num26)<5){
			for($k=0;$k<5-strlen($num26);$k++){
				$r.='3';
			}
		}
		$re=null;
		for ($i=0;$i<strlen($num26);$i++){
			$re[]=substr($num26,$i,1);
			
		}
		for ($j=0;$j<count($re);$j++){
			if(isset($num26[$j])){
				$r.=$dict2[$dict1[$num26[$j]]];
			}
		}
		return $acc.$r;
	}
	/*
	 *  @todo 分拆地址1、2、3，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddress($addr){
		$addr=str_replace(" ",' ',$addr);
		$arr=explode(" ",$addr);
		$ret=array();
		$line='';
		foreach ($arr as $word){
			if (strlen($line.' '.$word)< 34){
				$line.=' '.$word;
			}else {
				//为空跳过
				if(trim($line)){
					$ret[]=trim($line);
				}
				$line=$word;
			}
		}
		if ($line){
			$ret[]=$line;
		}
		return $ret;
	}
	/*
	 *  @todo 分拆地址1、2，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddressfedex($addr){
		$addr=str_replace(" ",' ',$addr);
		$arr=explode(" ",$addr);
		$ret=array();
		$line='';
		foreach ($arr as $word){
			if (strlen($line.' '.$word)< 34){
				$line.=' '.$word;
			}else {
				//为空跳过
				if(trim($line)){
					$ret[]=trim($line);
				}
				$line=$word;
			}
		}
		if ($line){
			$ret[]=$line;
		}
		return $ret;
	}
	
	/*
	 *  @todo 分拆地址1、2，现在只有分拆1、2
	 *  增加电子邮件
	 */
	
	static function splitAddressdhl($addr){
		$addr=str_replace(" ",' ',$addr);
		$arr=explode(" ",$addr);
		$ret=array();
		$line='';
		foreach ($arr as $word){
			if (strlen($line.' '.$word)< 39){
				$line.=' '.$word;
			}else {
				//为空跳过
				if(trim($line)){
					$ret[]=trim($line);
				}
				$line=$word;
			}
		}
		if ($line){
			$ret[]=$line;
		}
		return $ret;
	}
	
	/**
	 * @todo   获取PE和VG国家发票数据
	 * @author stt
	 * @since  2020-10-10
	 * @link   #81897
	 */
	static function peinvoicedata($order,$channel_id,$account_name=''){
		$channel=Channel::find('channel_id=?',$channel_id)->getOne();
		$warhouse_info=array();
		if($channel->network_code !='UPS' || !$account_name ){
			//杭州仓
			if($order->department_id==6){
				$warhouse_info['name']="Far's warehouse in Hangzhou";
				$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District';
				//城市
				$warhouse_info['city']='Hangzhou';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='310022';
				$warhouse_info['phone']='0571-87834076';
				//上海仓
			}elseif ($order->department_id==7){
				$warhouse_info['name']="Far's warehouse in Shanghai";
				$warhouse_info['address']='No. 12, Jinshun Road, Zhuqiao Town, Pudong New Area';
				//城市
				$warhouse_info['city']='Shanghai';
				$warhouse_info['state']='Shanghai';
				$warhouse_info['postcode']='201323';
				$warhouse_info['phone']='021-58590952';
				//义乌仓
			}elseif ($order->department_id==8){
				$warhouse_info['name']="Far's warehouse in Yiwu";
				$warhouse_info['address']='No.675-2 Airport Road';
				//城市
				$warhouse_info['city']='Yiwu';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='322000';
				$warhouse_info['phone']='0579-85119351';
				//广州仓
			}elseif ($order->department_id==22){
				$warhouse_info['name']="Far's warehouse in Guangzhou";
				$warhouse_info['address']='No.7-10, Heming Business Building, Baiyun Third Line, Helong Street, Baiyun District';
				//城市
				$warhouse_info['city']='Guangzhou';
				$warhouse_info['state']='Guangdong';
				$warhouse_info['postcode']='510080';
				$warhouse_info['phone']='020-36301839';
				//青岛仓
			}elseif ($order->department_id==23){
				$warhouse_info['name']="Far's warehouse in Qingdao";
				$warhouse_info['address']='No.4 Reservoir Area, Shanhang Logistics Park, No.31 Liangjiang Road, Chengyang District';
				//城市
				$warhouse_info['city']='Qingdao';
				$warhouse_info['state']='Shandong';
				$warhouse_info['postcode']='266108';
				$warhouse_info['phone']='18661786160';
				//深圳仓
			}elseif ($order->department_id==24){
				$warhouse_info['name']="FAR's warehouse in Shenzhen Longhua";
				$warhouse_info['address']='Unit 5, No.8 Non-bonded Warehouse, South China International Logistics Center, No.1 Mingkang Road, Minzhi Street, Longhua New District';
				//城市
				$warhouse_info['city']='shenzhen';
				$warhouse_info['state']='guangdong';
				$warhouse_info['postcode']='518000';
				$warhouse_info['phone']='4000857988';
				//南京仓
			}elseif ($order->department_id==25){
				$warhouse_info['name']="Far's warehouse in Hangzhou";
				$warhouse_info['address']='1st Floor, No.43 Ganchang Road, Xiacheng District';
				//城市
				$warhouse_info['city']='Hangzhou';
				$warhouse_info['state']='Zhejiang';
				$warhouse_info['postcode']='310022';
				$warhouse_info['phone']='0571-87834076';
			}else{
				//其他情况为空
				$warhouse_info['name']="";
				$warhouse_info['address']='';
				//城市
				$warhouse_info['city']='';
				$warhouse_info['state']='';
				//邮编
				$warhouse_info['postcode']='';
				$warhouse_info['phone']='';
			}
		}
		//渠道网络是DHL 订单产品是'Express_Standard_Global','US-FY'
		if ($channel->network_code == 'DHL' && in_array($order->service_code, array('Express_Standard_Global','US-FY'))){
			$warhouse_info ['name'] = "MR LI HUI";
			$warhouse_info ['company'] = "FAR";
			$warhouse_info ['address'] = 'GUANG SHENG XIANG XIA SHI WE IF RTO,PLS RTN TO HKG FOR SHPR INST';
			//城市
			$warhouse_info ['city'] = 'TSING YI';
			$warhouse_info ['state'] = 'HONG KONG';
			$warhouse_info ['postcode'] = '';
			$warhouse_info ['phone'] = '31746198';
			//渠道设置了发件人
			if ($channel->sender_id > 0) {
				$sender = Sender::find ( 'sender_id = ?', $channel->sender_id )->getOne ();
				if(!$sender-> isNewRecord()){
					//使用渠道设置的发件人
					$warhouse_info ['name'] = $sender->sender_name;
					$warhouse_info ['company'] = $sender->sender_company;
					$warhouse_info ['address'] = $sender->sender_address;
					//城市
					$warhouse_info ['city'] = $sender->sender_city;
					$warhouse_info ['state'] = $sender->sender_province;
					$warhouse_info ['postcode'] = $sender->sender_zip_code;
					//电话
					$warhouse_info ['phone'] = $sender->sender_phone;
				}
			}
		}
		//ups账号
		$account=UPSAccount::find('account=?',$account_name)->getOne();
		$invoice=array();
		$total_value=0;
		$order_product=Orderproduct::find('order_id=?',$order->order_id)->getOne();
		//循环产品
		foreach ($order->product as $v){
			$material_use = $v->material_use;
			if($channel->network_code =='UPS' && strpos(strtolower($v->product_name_en_far), 'mask') !== false){
				//材质用途
				if(!$material_use){
					$material_use = 'Dust protection Civil non-woven';
				}
				$brand = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
				//材质用途
				$material_use .= ' // brand:'.$brand.' // type no:'.rand(10, 100);
			}
			$invoice['items'][]=array(
				//描述
				'desc'=>$v->product_name_en_far.' HS Code:'.$v->hs_code_far.' '.$material_use,
				//数量
				'quantity'=>$v->product_quantity,
				//申报单价
				'price'=>$v->declaration_price,
				//总申报价值
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$total_value+=round($v->product_quantity*$v->declaration_price,2);
		}
		//PE和VG返回发票数据
		$data=array(
			'sender'=>$channel->network_code !='UPS'?$warhouse_info:$account->toArray(),
			'consignee_name'=>$order->consignee_name1,
			'consignee_company'=>$order->consignee_name2?$order->consignee_name2:$order->consignee_name1,
			//收件人电话
			'consignee_phone'=>$order->consignee_mobile,
			'consignee_city'=>$order->consignee_city,
			'consignee_country_code'=>$order->consignee_country_code,
			//收件人国家
			'consignee_country_name'=>Country::find('code_word_two=?',$order->consignee_country_code)->getOne()->english_name,
			'consignee_state'=>$order->consignee_state_region_code,
			'consignee_postal_code'=>$order->consignee_postal_code,
			'consignee_address'=>$order->consignee_street1.' '.$order->consignee_street2,
			//税号
			'tax'=>$order->tax_payer_id,
			'invoice'=>$invoice,
			'total_value'=>$total_value,
			//末端单号
			'tracking_no'=>$order->tracking_no
		);
		return $data;
	}
	/**
	 * @todo   fda发票json数据
	 * @author stt
	 * @since  2020-10-10
	 * @link   #81897
	 */
	static function fdainvoicedata($order,$channel_id,$account=''){
		$channel=Channel::find('channel_id=?',$channel_id)->getOne();
		$invoice=array('items'=>array(),'total'=>'');
		//循环申报产品
		foreach ($order->product as $v){
			$invoice['items'][]=array(
				//英文品名
				'description'=>$v->product_name_en_far?$v->product_name_en_far:$v->product_name_en,
				//产品数量
				'quantity'=>$v->product_quantity,
				//材质用途信息
				'material'=>$v->material_use,
				//HS 编码
				'hscode'=>$v->hs_code_far,
				//Origin固定
				'origin'=>'China',
				//申报单价
				'price'=>$v->declaration_price,
				//申报总价
				'itotal'=>round($v->product_quantity*$v->declaration_price,2),
			);
			$invoice['total']+=round($v->product_quantity*$v->declaration_price,2);
		}
		//组织收件人数据
		$sender_name=$order->sender_name2;
		$sender_address=$order->sender_street1.($order->sender_street2?' '.$order->sender_street2:'');
		$sender_zip_code=$order->sender_postal_code;
		$sender_city=$order->sender_city;
		//收件人国家二字码
		$sender_country_code=$order->sender_country_code;
		if($channel->network_code=="UPS"){
			//UPS账号
			$account=UPSAccount::find("account = ?",$account)->getOne();
			if(!$account->isNewRecord()){
				$sender_name=$account->name;
				$sender_city=$account->city;
				//地址
				$sender_address=$account->address;
				$sender_zip_code=$account->postcode;
			}
		}else{
			//渠道设置了发件人
			if($channel->sender_id>0){
				$sender=Sender::find('sender_id = ?',$channel->sender_id)->getOne();
				//发件人数据存在
				if(!$sender->isNewRecord()){
					$sender_name=$sender->sender_company;
					$sender_city=$sender->sender_city;
					$sender_address=$sender->sender_address;
					//发件人邮编
					$sender_zip_code=$sender->sender_zip_code;
				}
			}
		}
		//发件人
		$shipper = array(
			'name' => $sender_name,
			'city' => $sender_city,
			'address' => $sender_address,
			//发件人邮编
			'postcode' => $sender_zip_code,
		);
		$data=array(
			//收货人公司
			'consignee_company'=>$order->consignee_name2?$order->consignee_name2:$order->consignee_name1,
			//收货人地址
			'consignee_address'=>$order->consignee_street1.' '.$order->consignee_street2,
			'consignee_city'=>$order->consignee_city,
			'consignee_postal_code'=>$order->consignee_postal_code,
			//收货人国家二字码
			'consignee_country_code'=>$order->consignee_country_code,
			'consignee_state'=>$order->consignee_state_region_code,
			'consignee_name'=>$order->consignee_name1,
			'consignee_phone'=>$order->consignee_mobile,
			//fda数据
			'fda_company'=>$order->fda_company,
			'fda_address'=>$order->fda_address,
			'fda_city'=>$order->fda_city,
			'fda_post_code'=>$order->fda_post_code,
			//包含产品数据
			'invoice'=>$invoice,
			//发货人
			'shipper'=>$shipper,
			//末端单号
			'tracking_no'=>$order->tracking_no
		);
		return $data;
	}
	/**
	 * @todo   数据分割
	 * @author stt
	 * @since  2020-10-29
	 * @link   #82496
	 */
	static function getdatasplit($data,$length){
		//地址等字符串过长时 分为多行
		$addressarr = array();
		//字符串超出长度进行处理
		if(strlen($data) > $length){
			//空格分割成数组
			$address = explode(' ', $data);
			$addressline = '';
			//循环数组
			foreach ($address as $key=>$word){
				if(strlen(($addressline.' '.$word)) > $length){
					$addressarr[] = $addressline;
					$addressline = $word;
				}else{
					//空格拼接
					$addressline = strlen($addressline)==0 ? $word : $addressline.' '.$word;
				}
				//==
				if($key == count($address)-1){
					$addressarr[] = $addressline;
				}
			}
		}else{
			//--
			$addressarr[] = $data;
		}
		return $addressarr;
	}
	/**
	 * @todo   发票类型
	 * @author stt
	 * @since  2020-11-05
	 * @link   #83311
	 */
	static function getinvoicetype($channel_id,$consignee_country_code){
		//渠道
		$channel = Channel::find('channel_id = ?',$channel_id)->getOne();
		//网络
		$network = Network::find('network_code=?',$channel->network_code)->getOne();
		//发票类型
		$invoice_type=0;
		foreach($network->countryinvoice as $ci){
			//逗号,隔开的国家二字码
			$country = explode(',',$ci->country_codes);
			if(in_array($consignee_country_code,$country)){
				$invoice_type=$ci->invoice_type;
			}
		}
		return $invoice_type;
	}
	/**
	 * @todo   创建小标签PDF，1-4个包裹生成1-4个标签，5个及5个以上生成一个标签
	 * @author stt
	 * @since  2020-10-27
	 * @link   #82496
	 */
	static function createfarlittlelabel($order){
		//小标签
		$quantity=Farpackage::find('order_id=?',$order->order_id)->sum('quantity','sum_quantity')->getAll();
		$dir=Q::ini('upload_tmp_dir');
		$label_file_path = $dir.DS.$order->order_no.'_label.pdf';
		//包裹数量大于等于5 打印1张
		if ($quantity['sum_quantity']<5 && $quantity['sum_quantity']>1){
			//包裹数量$quantity['sum_quantity']
			for ($i= 0;$i < $quantity['sum_quantity'];$i++){
				$farlittlelabelpath = $dir.DS.$order->order_no.'_label'.($i+1).'.pdf';
				Helper_Common::getfarlittlelabel($order,$i+1,$quantity['sum_quantity'],$farlittlelabelpath);
				$farlittlelabels[] = $farlittlelabelpath;
			}
			//合成一张PDF
			Helper_PDF::merge($farlittlelabels,$label_file_path,'file');
			for ($i= 0;$i < $quantity['sum_quantity'];$i++){
				//--
				@unlink($dir.DS.$order->order_no.'_label'.($i+1).'.pdf');
			}
		}else{
			Helper_Common::getfarlittlelabel($order,$quantity['sum_quantity'],$quantity['sum_quantity'],$label_file_path);
		}
		$uploadoss = new Helper_AlipicsOss();
		//上传到oss
		$label_data = $uploadoss->uploadAlifiles($order->order_no.'_label.pdf');
		if ($uploadoss->doesExist($order->order_no.'_label.pdf')){
			//上传成功，删除
			@unlink($dir.DS.$order->order_no.'_label.pdf');
		}
	}
	/**
	 * @todo   获取小标签PDF
	 * @author stt
	 * @since  2020-10-27
	 * @link   #82496
	 */
	static function getfarlittlelabel($order,$num=1,$sum_quantity,$path){
		$country = Country::find('code_word_two = ?',$order->consignee_country_code)->getOne();
		$fofn='';
		if ($order->declaration_type == 'DL') {
			$fofn = '/FO';
		} elseif ($order->total_amount > 700 || $order->weight_actual_in > 70) {
			$fofn = '/FN';
		}
		$sp = $order->volumn_chargeable ? 'P'.$fofn : 'S'.$fofn;
		$product='';
		//产品 
		switch ($order->service_code){
			case 'Express_Standard_Global':
				$product='普货';
				break;
			case 'EMS-FY':
				$product='EMS';
				break;
			case 'WIG-FY':
				$product='假发';
				break;
			case 'US-FY':
				$product='美空';
				break;
			case 'OCEAN-FY':
				$product='美海';
				break;
			case 'CNUS-FY':
				$product='无忧';
				break;
			case 'EUUS-FY':
				$product='欧美';
				break;
			case 'ePacket-FY':
				$product='EUB';
				break;
			case 'CNUSBJ-FY':
				$product='包机';
				break;
		}
		//上传路径
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		//生成的条码图片
		$barcode=$dir.DS.$order->ali_order_no.'.barcode.png';
		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$order->ali_order_no));
		file_put_contents($barcode, $source);
		$filepath = $dir.DS.$order->order_no.'_label.pdf';
		$filepath = $path;
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','label');
		//中文
		$pdf -> AddGBFont ('simhei', '黑体'); 
		//左
		$pdf->SetMargins(3,0);
		//不自动分页
		$pdf->SetAutoPageBreak(false);
		$pdf->AddPage();
		//字体Arial大小10
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('60','9','','0');
		//插入条形码
		$pdf->Image($barcode,'10','1','45','8');
		$pdf->Ln();
		//--
		$pdf->Cell('8','4',$sp,'0');
		//--
		$pdf->SetFont('simhei','',8);
		//中文
		$pdf->Cell('8','4',iconv("utf-8","gbk",$product),'0','','C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('38','4',$order->ali_order_no,'0','');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('6','4',$num.'/'.$sum_quantity,'0','');
		$pdf->Ln();
		$pdf->SetFont('simhei','',8);
		//中文
		$pdf->Cell('60','4',iconv("utf-8","gbk",$country->chinese_name),'0','','C');
		//生成pdf文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
		unlink($barcode);
	}
	/**
	 * @todo   获取普通小标签PDF
	 * @author stt
	 * @since  2020年12月4日17:16:56
	 * @link   #84246
	 */
	static function getcommonlabel($ali_order_no,$time){
		//上传路径
		$dir=Q::ini('upload_tmp_dir');
		@Helper_Filesys::mkdirs($dir);
		//生成的条码图片
		$barcode=$dir.DS.$ali_order_no.'.barcode.png';
		$source=trim(file_get_contents('http://kuaijian.far800.com/public/barcode/html/image.php?filetype=PNG&dpi=90&scale=1&rotation=0&font_family=0&font_size=8&thickness=55&start=A&code=BCGcode128&text='.$ali_order_no));
		file_put_contents($barcode, $source);
		$filepath = $dir.DS.$ali_order_no.$time.'_label.pdf';
		//创建pdf文件
		$pdf=new PDF_Chinese('P','mm','label');
		//中文
		$pdf -> AddGBFont ('simhei', '黑体');
		//左
		$pdf->SetMargins(3,0);
		//不自动分页
		$pdf->SetAutoPageBreak(false);
		$pdf->AddPage();
		//字体Arial大小10
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('60','9','','0');
		//插入条形码
		$pdf->Image($barcode,'10','1','45','8');
		$pdf->Ln();
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('60','4',$ali_order_no,'0','','C');
		$pdf->Ln();
		//生成pdf文件
		$pdf->Output($filepath,'F');
		$pdf->Close();
		//去掉条形码文件
		unlink($barcode);
	}
	/**
	 * @todo   创建32位guid
	 * @author stt
	 * @since  2021年1月7日12:56:15
	 * @link   #85054
	 */
	static function createguid($level=true){
		if($level){
			$charid = strtolower(md5(uniqid(mt_rand(), true)));
		}else{
			$charid = strtoupper(md5(uniqid(mt_rand(), true)));
		}
		$hyphen = chr(45);// "-"
		//chr(123)// "{"
		$uuid =substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12);
		//.chr(125);// "}"
		return $uuid;
	}
	/**
	 * @todo   发送照片
	 * @author stt
	 * @since  2021年1月22日10:15:47
	 * @link   #85390
	 */
	static function sendcheckweightphoto($order){
		set_time_limit(0);
		$images = File::find('order_id=?',$order->order_id)->getAll();
		foreach ($images as $image){
			$recordformdata=file_get_contents($image->file_path);
			$url_sign='https://pre-gw.api.1688.com';
			$recordform_request_data=array(
				'aliOrderNo'=>$order->ali_order_no,
				//重量复核 照⽚
				'recordFormType'=>'re_check_weight_photo',
				//Base64字符串
				'recordFormData'=>base64_encode($recordformdata)
			);
			$ali=new Helper_ALI();
			$sign=$ali->sign($url_sign, json_encode($recordform_request_data),'uploadRecordForm');
			$post_data='uploadRecordFormDTO='.urlencode(json_encode($recordform_request_data)).'&_aop_signature='.$sign;
			//通过curl post 方式发送至阿里x-www-form-urlencoded 方式发送
			$response=Helper_Curl::post($url_sign, $post_data,array(
				'Content-Type:application/x-www-form-urlencoded'
			));
			QLog::log('re_check_weight_photo:'.$response);
			$response=json_decode($response,true);
			if(isset($response['success']) && $response['success']==true){
				//发送成功
				$image->is_send=1;
				$image->save();
			}else{
				//失败发送
				$image->is_send=2;
				//失败原因
				$image->send_error_reason=$response['message'];
				$image->save();
			}
		}
	}
	
	
	
	
	
}