<?php
/**
 * 报价助手
 *
 * @author D23
 *
 */
class Helper_Quote {
	/**
	 * 计算应收
	 *
	 * @return string
	 */
	function receivable($order, $weight, $packing_box_quantity = '', $packing_pak_quantity = '', $special_packing_quantity = '', $date = '') {
		bcscale(4);
		if ($date == '') {
			$date = time ();
		}
		//根据order获取产品
		$product = Product::find ( 'product_name=?', $order->service_code )->getOne ();
		//燃油费率
		//获取网络燃油表
		$rate = 0;
		if ($order->service_code != 'EMS-FY') {
			$productfuel = Productfuel::find ( 'product_id=? and effective_date<=? and fail_date>=?', $product->product_id, $date, $date )->getOne ();
			if (! $productfuel->isNewRecord ()) {
				$rate = $productfuel->rate;
			}
		}
		
		$package_quantity=0;
		foreach ( $order->farpackages as $temp ) {
			$package_quantity += $temp->quantity;
		}
		
		//获取产品中偏派-价格-分区
		$product_p_p_r = Productppr::find ( 'product_id=? and effective_time <=? and invalid_time>=?', $product->product_id, $date, $date )->getOne ();
		//偏远地区附加费
		//查询偏派表
		//         $postcode='';
		//         if(in_array($order->service_code, array('EMS-FY','EUUS-FY'))){
		$postcode = $order->consignee_postal_code;
		//         }
		$order->consignee_postal_code = str_replace ( array (
			" ",
			'-'
		), '', $order->consignee_postal_code );
		$post_code = $order->consignee_postal_code;
		//除了GB,其他国家都使用任意一个起始邮编的长度来截取超长的邮编
		if ($order->consignee_country_code != 'GB') {
			$remote = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $product_p_p_r->remote_manage_id, $order->consignee_country_code )->getOne ();
			if (! $remote->isNewRecord ()) {
				$length = strlen ( $remote->start_postal_code );
				if (strlen ( $post_code ) > $length) {
					$post_code = substr ( $post_code, 0, $length );
				}
			}
		}
		//$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $postcode, $postcode )->getOne ();
		//if ($remote_postcode->isNewRecord ()) {
			$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $post_code, $post_code )->getOne ();
		//}
		$remote_city = Remote::find ( 'remote_manage_id = ? and country_code_two=? and (remote_city=? || remote_city=?) and ifnull(remote_city,"") != "" ', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $order->consignee_city, strtolower ( str_replace ( '-', '', str_replace ( ' ', '', $order->consignee_city ) ) ) )->getOne ();
		
		if (! $remote_city->isNewRecord ()) { //偏派城市
			if ($remote_city->additional_weight > 0) {
				if (($weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(ceil (bcdiv( (bcsub($weight , $remote_city->first_weight)) , $remote_city->additional_weight )) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			} else {
				if (($weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(bcsub($weight , $remote_city->first_weight) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			}
			if ($remote_fee <= $remote_city->lowest_fee) {
				$remote_fee = $remote_city->lowest_fee;
			}
		} else {
			if (! $remote_postcode->isNewRecord ()) { //偏派邮编
				if ($remote_postcode->additional_weight > 0) {
					if (($weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil (bcdiv( bcsub($weight , $remote_postcode->first_weight) , $remote_postcode->additional_weight )) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				} else {
					if (($weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($weight , $remote_postcode->first_weight) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				}
				if ($remote_fee <= $remote_postcode->lowest_fee) {
					$remote_fee = $remote_postcode->lowest_fee;
				}
			} else {
				$remote_fee = 0;
			}
		}
		
		if ($remote_fee == 0) {
			$zip = $order->consignee_postal_code;
			$length_zip = strlen ( $zip );
			//             if($order->consignee_country_code!='GB') {
			$remote_zip1 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and left(start_postal_code,' . $length_zip . ')<=? and left(end_postal_code,' . $length_zip . ')>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $zip, $zip )->getOne ();
			if (! $remote_zip1->isNewRecord ()) {
				if ($remote_zip1->additional_weight > 0) {
					if (($weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil (bcdiv( bcsub($weight , $remote_zip1->first_weight) , $remote_zip1->additional_weight )) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				} else {
					if (($weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($weight , $remote_zip1->first_weight) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				}
				if ($remote_fee <= $remote_zip1->lowest_fee) {
					$remote_fee = $remote_zip1->lowest_fee;
				}
			} else {
				$remote_zip2 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $product_p_p_r->remote_manage_id, $order->consignee_country_code )->getAll ();
				if (count ( $remote_zip2 ) > 0) {
					foreach ( $remote_zip2 as $z ) {
						$start_postal_code = $z->start_postal_code;
						$end_postal_code = $z->end_postal_code;
						if ("'" . substr ( $zip, 0, strlen ( $start_postal_code ) ) >= "'" . $start_postal_code && "'" . substr ( $zip, 0, strlen ( $end_postal_code ) ) <= "'" . $end_postal_code) {
							if ($z->additional_weight > 0) {
								if (($weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(ceil (bcdiv( bcsub($weight , $z->first_weight) , $z->additional_weight )) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							} else {
								if (($weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(bcsub($weight , $z->first_weight) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							}
							if ($remote_fee <= $z->lowest_fee) {
								$remote_fee = $z->lowest_fee;
							}
							if ($remote_fee > 0) {
								break;
							}
						}
					}
				}
			}
			//             }
		}
		//原币种
		$remote_fee1 = $remote_fee;
		$remote_rate = 1;
		$currency = '';
		//如果偏派币种不是人民币，就转换一下
		if(!$remote_city->isNewRecord()){
			if($remote_city->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_city->currency_code);
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_city->currency_code,$date, '');
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_city->currency_code;
			}
		}else{
			if($remote_postcode->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_postcode->currency_code);
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_postcode->currency_code,$date, '');
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_postcode->currency_code;
			}
		}
		
		
		if ($order->service_code == 'ePacket-FY') {
			$remote_fee = 0;
		}
		if (in_array ( $order->service_code, array (
			'EMS-FY',
			'EUUS-FY'
		) ) && ! empty ( $postcode )) {
			$order->consignee_postal_code = $postcode;
		}
		//基础运费
		//获取分区
		$partition_code = '';
		$partition_code2 = '';
		//print_r($order);exit;
		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $product_p_p_r->partition_manage_id, $order->consignee_country_code )->getAll ();
		foreach ( $partition as $p ) {
			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $order->consignee_postal_code ) ) == $order->consignee_postal_code || substr ( $order->consignee_postal_code, 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
				$partition_code = $p->partition_code;
			}
			if (! $p->postal_code) {
				$partition_code2 = $p->partition_code;
			}
		}
		if (! $partition_code) {
			$partition_code = $partition_code2;
		}
		//获取价格
		//包装类型
		$packing = 'BOX';
		if ($order->packing_type == 'DOC') {
			$packing = "DOC";
		}
		$price = Price::find ( 'price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?', $product_p_p_r->price_manage_id, $partition_code, $packing, $weight, $weight )->getOne ();
		if ($price->isNewRecord ()) {
			$data = array ();
			return $data;
		}
		//计算运费
		$tracking_fee = 0;
		if (! $price->isNewRecord ()) {
			if ($price->additional_weight > 0) {
				if (($weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(ceil (bcdiv( bcsub($weight , $price->first_weight) , $price->additional_weight )) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			} else {
				if (($weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(bcsub($weight , $price->first_weight) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			}
		}
		//原币种
		$tracking_fee1 = $tracking_fee;
		$tracking_rate = 1;
		//计算费用不是人民币，就转换为人民币
		if($price->currency_code != 'CNY'){
			$tracking_fee = self::exchangeRate($date,$tracking_fee, $price->currency_code);
			$code_currency = CodeCurrencyItem::getCurrencyRate($price->currency_code,$date, '');
			//汇率
			$tracking_rate = $code_currency;
		}
		
		//燃油附加费
		//$rate_fee = ($tracking_fee + $over_size_fee + $remote_fee + $pss) * $rate;
		$fee_amount = 0; //总费用
		$data = array ();
		$volumn = 0;
		$netweight = 0;
		foreach ( $order->farpackages as $package ) {
			$volumn += (floor ( $package->length ) * floor ( $package->width ) * floor ( $package->height )) * $package->quantity;
			$netweight += $package->weight * $package->quantity;
		}
		$receivable_formula = Receivableformula::find('product_id=? and package_type=? and customs_code=?',$product->product_id,$order->packing_type,$order->customer->customs_code)->getAll();
		if (count ( $receivable_formula ) > 0) {
			foreach ( $receivable_formula as $v ) {
				if ($v->calculation_flag == '1' && $v->effective_time <= $date && $v->fail_time >= $date) {
					$v_rate = 1;
					if($price->currency_code != 'CNY'){
						$code_currency = CodeCurrencyItem::getCurrencyRate($v->currency_code,$date, '');
						//汇率
						$v_rate = $code_currency;
					}
					
					$item_code = FeeItem::find ( 'item_name=? and customs_code=?', $v->fee_name ,$order->customer->customs_code)->getOne ();
					//超尺寸/超重附加费
					$over_size_fee = 0;
					$over_size_quantity = 0;
					foreach ( $order->farpackages as $temp ) {
						if ($item_code->sub_code == 'logisticsExpressASP_EX0035' ||$item_code->sub_code == 'Overlength_Charges') {
							$array = array (
								$temp->length,
								$temp->width,
								$temp->height
							);
							
							rsort ( $array ); //逆排序
							$over_size_formula_fee = Helper_Formula::parse ( $v->formula, array (
								'net_weight' => $temp->weight,
								'girth' => $array [0]+2*($array [1]+$array [2]),
								'first_length' => $array [0],
								'second_length' => $array [1],
								'declaration_type' => $order->declaration_type,
								'weight' => '',
								'country' => '',
								'packing_box' => '',
								'packing_pak' => '',
								'icount' => '',
								'over_count' => '',
								'special_count' => '',
							) );
							//#83414
							if ($over_size_formula_fee===false){
								//存在生效费用项无法计算
								QLog::log('formulaerror:'.$v->formula);
								$data['success'] = 'formulaerror';
								return $data;
							}
							$over_size_fee += $over_size_formula_fee*$temp->quantity;
							if ($over_size_formula_fee != 0){
								$over_size_quantity += $temp->quantity;
							}
						}
					}
					if ($over_size_fee != 0) { // false 和 0 都不计算
						//超尺寸/超重附加费
						if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
							$data [$item_code->item_code] = array (
								'fee' => round($over_size_fee, 2),
								'currency_code' => $v->currency_code,
								'rate' => $v_rate,
								'quantity' => $over_size_quantity,
							);
						}
						//计算超长燃油费
						$fee_amount = bcadd($over_size_fee,$fee_amount);
						//上面已经计算过了，这里跳过
						if($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges'){
							continue;
						}
					}
					
					$item_fee = Helper_Formula::parse ( $v->formula, array (
						'net_weight' => '',
						'girth' => '',
						'first_length' => '',
						'second_length' => '',
						'declaration_type' => $order->declaration_type,
						'weight' => $weight,
						'country' => $order->consignee_country_code,
						'packing_box' => $packing_box_quantity,
						'packing_pak' => $packing_pak_quantity,
						'icount' => $package_quantity,
						'over_count' => $over_size_quantity,
						'special_count' => $special_packing_quantity,
					) );
					//#83414
					if ($item_fee===false){
						//存在生效费用项无法计算
						QLog::log('formulaerror:'.$v->formula);
						$data['success'] = 'formulaerror';
						return $data;
					}
					//echo "<pre>";print_r($item_fee.'---'.$item_code->sub_code);
					if ($item_fee != 0) { // false 和 0 都不计算
						if ($item_code->sub_code == 'logisticsExpressASP_EX0034') { //异形包装
							$data [$item_code->item_code] = array (
								'fee' => round($item_fee, 2),
								'currency_code' => $v->currency_code,
								'rate' => $v_rate,
								'quantity' => $special_packing_quantity,
							);
							// 						} elseif ($item_code->sub_code == 'logisticsExpressASP_EX0035') { //超尺寸/超重附加费
							// 							$data [$item_code->item_code] = array (
							// 								'fee' => round($item_fee, 2),
							// 								'currency_code' => $v->currency_code,
							// 								'rate' => $v_rate,
							// 								'quantity' => $over_size_quantity,
							// 							);
						} elseif ($item_code->sub_code == 'logisticsExpressASP_EX0003') { //纸箱
							$data [$item_code->item_code] = array (
								'fee' => round($item_fee, 2),
								'currency_code' => $v->currency_code,
								'rate' => $v_rate,
								'quantity' => $packing_box_quantity,
							);
						} elseif ($item_code->sub_code == 'logisticsExpressASP_EX0002') { //包装袋
							$data [$item_code->item_code] = array (
								'fee' => round($item_fee, 2),
								'currency_code' => $v->currency_code,
								'rate' => $v_rate,
								'quantity' => $packing_pak_quantity,
							);
						}else {
							$data [$item_code->item_code] = array (
								'fee' => round($item_fee, 2),
								'currency_code' => $v->currency_code,
								'rate' => $v_rate,
								'quantity' => '1',
							);
						}
						//计算费用不是人民币，就转换为人民币
						if($v->currency_code != 'CNY'){
							$item_fee = self::exchangeRate($date,$item_fee, $v->currency_code);
						}
						if(in_array($item_code->item_code, array('EX0044'))){
							$fee_amount = bcadd($item_fee,$fee_amount);
						}
						
					}
				}
			}
		}
		//燃油附加费
		$rate_fee = bcmul(bcadd(bcadd($tracking_fee , $remote_fee) , $fee_amount) , $rate);
		//echo "<pre>";print_r($data);exit;
		
		//派送费
		$distribute_fee = 0;
		if ($order->need_pick_up == '1' && strlen ( $order->sender_postal_code ) == '6') {
			$zipcode = Zipcode::find ( 'pick_company="青岛仓" and zip_code_low <= ? and zip_code_high >= ?', $order->sender_postal_code, $order->sender_postal_code )->getOne ();
			if (! $zipcode->isNewRecord ()) {
				$distribute_fee = '5';
				$data ['EX0036'] = array (//取件费
					'fee' => round($distribute_fee, 2),
					'quantity' => '1',
				);
			}
		}
		//组合返回各项费用
		$data['EX0001'] = array ('fee' => round($tracking_fee1, 2),'quantity' => '1','currency_code' => $price->currency_code,'rate' => $tracking_rate);
		// 		$data['EX0002'] = array ('fee' => round($packing_pak_fee, 2),'quantity' => $packing_pak_quantity);
		// 		$data['EX0003'] = array ('fee' => round($packing_box_fee, 2),'quantity' => $packing_box_quantity);
		// 		$data['EX0034'] = array ('fee' => $special_packing_fee,'quantity' => $special_packing_quantity);//异形包装费
		//$data['EX0012'] = array ('fee' => round($customs_fee, 2),'quantity' => 1);
		$data['EX0020'] = array ('fee' => round($remote_fee1, 2),'quantity' => '1','currency_code' => $currency,'rate' => $remote_rate);
		// 		$data['EX0035'] = array ('fee' => $over_size_fee,'quantity' => $over_size_quantity);//超尺寸/超重附加费
		$data['EX0019'] = array ('fee' => round($rate_fee, 2),'quantity' => '1');
		// 		$data['EX0036'] = array ('fee' => $distribute_fee,'quantity' => '1');//取件费
		// 		$data['EX0044'] = array ('fee' => $pss,'quantity' => '1');//旺季附加费
		return $data;
	}
	/**
	 * @todo 汇率换算 输入币种，返回换算汇率后的人民币或其他币种
	 * @author 吴开龙
	 * @since 2020.6.3
	 * @param $fee金额，$currency外币 $type 状态 0：外币换算成人民币  1：人民币换算成外币 $supplier_id供应商id；$rate汇率
	 * @return view
	 * @link #80101
	 */
	static function exchangeRate($warehouse_confirm_time,$fee, $currency, $type = 0, $supplier_id = '',$rate = ''){
		bcscale(3);
		if($rate == ''){
			//获取币种设置的数据
			//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$currency,$warehouse_confirm_time,$warehouse_confirm_time)->getOne();
			$code_currency = CodeCurrencyItem::getCurrencyRate($currency,$warehouse_confirm_time, $supplier_id);
			if(!$code_currency){
				return $fee;
			}
			if($type == 1){
				//人民币换算成外币
				$return = bcdiv($fee , $code_currency);
			}else{
				//外币换算成人民币
				$return = bcmul($code_currency , $fee);
			}
		}else{
			//传入汇率的情况下，直接计算
			if($type == 1){
				//人民币换算成外币
				$return = bcdiv($fee , $rate);
			}else{
				//外币换算成人民币
				$return = bcmul($rate , $fee);
			}
		}
		
		//四舍五入保留两位小数
		return round($return,2);
	}
	/**
	 * 根据国家、城市、邮编、包裹类型、长、宽、高、重量计算应付(渠道成本)
	 *
	 * @return string
	 */
	function calchannelpayment($caldata, $channelcostppr, $network_id, $special_packing_fee_count = '') {
		bcscale(4);
		$date = time ();
		$weight_cost_out = 0;
		$total_weight = 0;
		//标签重
		$label_weight = 0;
		$total_cost_weight = 0;
		//如果包裹类型是PAK将实重算作计费重
		//获取渠道成本中计泡系数
		$channel = ChannelCost::find ( 'channel_cost_id=?', $channelcostppr->channel_cost_id )->getOne ();
		$tb_channel = Channel::find ( 'channel_id=?', $channel->channel_id )->getOne ();
		$disabled_country = ChannelCountryDisabled::find ( 'channel_id = ? and effect_time<=? and failure_time>=?', $channel->channel_id, $date, $date )->getOne();
		$countries = explode(',', $disabled_country->country_code_two);
		if (count ( $countries ) > 0) {
			if (in_array ( $caldata['country_code'], $countries )) {
				return array ();
			}
		}
		if ($caldata['packing_type'] == 'PAK') {
			$total_weight = $caldata['weight'];
			//PAK类型的时候，计费重大于 0.5的整数倍0.1以内，自动向下取整到0.5的整数倍
			if ($total_weight > 0.5 && (floor ( $total_weight / 0.5 ) * 0.5 + 0.1) > $total_weight) {
				$total_weight = floor ( $total_weight / 0.5 ) * 0.5;
			} else {
				$total_weight = sprintf ( "%.1f", $total_weight );
			}
			if ($total_weight < 0.5) {
				$total_weight = 0.5;
			}
			$total_cost_weight = $total_weight;
			if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$total_weight)){
				$total_weight = $channelcostppr->single_lowest_weight;
			}
		} else {
			$ratio = $channel->ratio;
			
			if ($caldata['product_name'] == 'EMS-FY' && floor ( $caldata['length_out'] ) < 60 && floor ( $caldata['width_out'] ) < 60 && floor ( $caldata['height_out'] ) < 60) {
				$volumn_weight = '0';
			} else {
				if($ratio){
					$volumn_weight = bcdiv(bcmul(bcmul(floor ( $caldata['length_out'] ) , floor ( $caldata['width_out'] ) ) , floor ( $caldata['height_out'] )) , $ratio);
				}else{
					$volumn_weight = '0';
				}
			}
			if ($volumn_weight > $caldata['weight']) {
				$label_weight = $volumn_weight;
				$weight_cost_out = ceil ( $volumn_weight / 0.5 ) * 0.5;
			} else {
				$label_weight = $caldata['weight'];
				$weight_cost_out = ceil ( $caldata['weight'] / 0.5 ) * 0.5;
			}
			//大于20公斤
			if ($label_weight > 20) {
				if ((floor ( $label_weight ) + 0.1) > $label_weight) {
					$label_weight = floor ( $label_weight );
				} else {
					$label_weight = floor ( $label_weight ) + 0.5;
				}
			} else {
				if ((floor ( $label_weight / 0.5 ) * 0.5 + 0.1) > $label_weight) {
					$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
				} else {
					$label_weight = ceil ( $label_weight / 0.5 ) * 0.5;
				}
			}
			if ($label_weight < 0.5) {
				$label_weight = 0.5;
			}
			//增加一个字段，记实计费重量
			$total_cost_weight = ($weight_cost_out > 20 && $caldata['product_name'] == 'Express_Standard_Global') ? ceil ( $weight_cost_out ) : $weight_cost_out;
			$total_weight = $total_cost_weight;
			//单件最低计费重和单件计费重，谁大取谁且渠道设置了单件最低计费重
			if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$weight_cost_out)){
				$total_weight = $channelcostppr->single_lowest_weight;
			}
		}
		if ($caldata['product_name'] == 'ePacket-FY') {
			$total_cost_weight = 0;
			$total_cost_weight = $caldata['weight'];
			$total_cost_weight = sprintf ( "%.3f", $total_cost_weight );
			$total_weight = $total_cost_weight;
			$label_weight = $total_cost_weight;
			if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$total_cost_weight)){
				$total_weight = sprintf ( "%.3f", $channelcostppr->single_lowest_weight );
			}
		}
		$volumn = 0;
		$netweight = 0;
		$volumn += (floor ( $caldata['length_out'] ) * floor ( $caldata['width_out'] ) * floor ( $caldata['height_out'] ));
		$netweight += $caldata['weight'];
		//偏远地区附加费
		$remote_city = Remote::find ( 'remote_manage_id = ? and country_code_two=? and (remote_city=? || remote_city=?) and ifnull(remote_city,"") != ""', $channelcostppr->remote_manage_id, $caldata['country_code'], $caldata['city'], strtolower ( str_replace ( '-', '', str_replace ( ' ', '', $caldata['city'] ) ) ) )->getOne ();
		if (! $remote_city->isNewRecord ()) { //偏派城市
			if ($remote_city->additional_weight > 0) {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_weight )) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			} else {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			}
			if ($remote_fee <= $remote_city->lowest_fee) {
				$remote_fee = $remote_city->lowest_fee;
			}
		} else {
			$post_code = str_replace ( array (
				" ",
				'-'
			), '', $caldata['zip_code'] );
			//除了GB,其他国家都使用任意一个起始邮编的长度来截取超长的邮编
			if ($caldata['country_code'] != 'GB') {
				$remote = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $channelcostppr->remote_manage_id, $caldata['country_code'] )->getOne ();
				if (! $remote->isNewRecord ()) {
					$length = strlen ( $remote->start_postal_code );
					if (strlen ( $post_code ) > $length) {
						$post_code = substr ( $post_code, 0, $length );
					}
				}
			}
			//$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $caldata['country_code'], $caldata['zip_code'], $caldata['zip_code'] )->getOne ();
			//if ($remote_postcode->isNewRecord ()) {
				$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $caldata['country_code'], $post_code, $post_code )->getOne ();
			//}
			if (! $remote_postcode->isNewRecord ()) { //偏派邮编
				if ($remote_postcode->additional_weight > 0) {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_weight) ) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				} else {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				}
				if ($remote_fee <= $remote_postcode->lowest_fee) {
					$remote_fee = $remote_postcode->lowest_fee;
				}
			} else {
				$remote_fee = 0;
			}
		}
		
		if ($remote_fee == 0) {
			$zip = str_replace ( array (
				" ",
				'-'
			), '', $caldata['zip_code'] );
			$length_zip = strlen ( $zip );
			//             if($caldata['country_code']!='GB') {
			$remote_zip1 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and left(start_postal_code,' . $length_zip . ')<=? and left(end_postal_code,' . $length_zip . ')>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $caldata['country_code'], $zip, $zip )->getOne ();
			if (! $remote_zip1->isNewRecord ()) {
				if ($remote_zip1->additional_weight > 0) {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_weight) ) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				} else {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				}
				if ($remote_fee <= $remote_zip1->lowest_fee) {
					$remote_fee = $remote_zip1->lowest_fee;
				}
			} else {
				$remote_zip2 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $channelcostppr->remote_manage_id, $caldata['country_code'] )->getAll ();
				if (count ( $remote_zip2 ) > 0) {
					foreach ( $remote_zip2 as $z ) {
						$start_postal_code = $z->start_postal_code;
						$end_postal_code = $z->end_postal_code;
						if ("'" . substr ( $zip, 0, strlen ( $start_postal_code ) ) >= "'" . $start_postal_code && "'" . substr ( $zip, 0, strlen ( $end_postal_code ) ) <= "'" . $end_postal_code) {
							if ($z->additional_weight > 0) {
								if (($total_weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $z->first_weight) , $z->additional_weight) ) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							} else {
								if (($total_weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(bcsub($total_weight , $z->first_weight) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							}
							if ($remote_fee <= $z->lowest_fee) {
								$remote_fee = $z->lowest_fee;
							}
							if ($remote_fee > 0) {
								break;
							}
						}
					}
				}
			}
			//             }
		}
		
		//原币种
		$remote_fee1 = $remote_fee;
		$remote_rate = 1;
		$currency = '';
		//如果偏派币种不是人民币，就转换一下
		if(!$remote_city->isNewRecord()){
			if($remote_city->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_city->currency_code, 0, $tb_channel->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_city->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_city->currency_code,$date, $tb_channel->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_city->currency_code;
			}
		}else{
			if($remote_postcode->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_postcode->currency_code, 0, $tb_channel->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_postcode->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_postcode->currency_code,$date, $tb_channel->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_postcode->currency_code;
			}
			// 			echo "<pre>";
			// 			print_r($remote_postcode->currency_code);
			// 			exit;
		}
		
		
		if ($caldata['product_name'] == 'ePacket-FY') {
			$remote_fee = 0;
		}
		//超尺寸/超重附加费(已改其他方法计算)
		$over_size_quantity = '';
		//总包裹数
		$package_quantity = 1;
		
		// 	    foreach ( $order->faroutpackages as $temp ) {
		// 	        if ($caldata['product_name'] == 'Express_Standard_Global') {
		// 	            $array = array (
		// 	                $temp->length_out,
		// 	                $temp->width_out,
		// 	                $temp->height_out
		// 	            );
		
		// 	            rsort ( $array ); //逆排序
		// 	            if ($array [0] > '121') {
		// 	                //收取超尺寸费用
		// 	                $over_size_quantity += $temp->quantity_out;
		// 	            } else {
		// 	                if ($array [1] > '76') {
		// 	                    //收取超尺寸费用
		// 	                    $over_size_quantity += $temp->quantity_out;
		// 	                } else {
		// 	                    if ($temp->weight_out > 32) {
		// 	                        //收取超重费用
		// 	                        $over_size_quantity += $temp->quantity_out;
		// 	                    }
		// 	                }
		// 	            }
		// 	        }
		// 	        $package_quantity += $temp->quantity_out;
		// 	    }
		
		// 	    if ($caldata['product_name'] == 'Express_Standard_Global') {
		// 	        $array = array (
		// 	            $caldata['length_out'],
		// 	            $caldata['width_out'],
		// 	            $caldata['height_out']
		// 	        );
		
		// 	        rsort ( $array ); //逆排序
		// 	        if ($array [0] > '121') {
		// 	            //收取超尺寸费用
		// 	            $over_size_quantity = 1;
		// 	        } else {
		// 	            if ($array [1] > '76') {
		// 	                //收取超尺寸费用
		// 	                $over_size_quantity = 1;
		// 	            } else {
		// 	                if ($caldata['weight'] > 32) {
		// 	                    //收取超重费用
		// 	                    $over_size_quantity = 1;
		// 	                }
		// 	            }
		// 	        }
		// 	    }
		
		//基础运费
		$tracking_fee = 0;
		//获取分区
		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $channelcostppr->partition_manage_id, $caldata['country_code'] )->getAll ();
		if (count ( $partition ) == 0) {
			return array ();
		}
		$partition_code = '';
		$partition_code2 = '';
		foreach ( $partition as $p ) {
			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $caldata['zip_code'] ) ) == $caldata['zip_code'] || substr ( $caldata['zip_code'], 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
				$partition_code = $p->partition_code;
			}
			if (! $p->postal_code) {
				$partition_code2 = $p->partition_code;
			}
		}
		if (! $partition_code) {
			$partition_code = $partition_code2;
		}
		//包装类型
		$packing = 'BOX';
		if ($caldata['packing_type'] == 'DOC') {
			$packing = "DOC";
		}
		if ($caldata['packing_type'] == 'PAK' && ($caldata['product_name'] == 'WIG-FY' || $caldata['product_name'] == 'EUUS-FY')) {
			$packing = "PAK";
		}
		//获取价格
		$price = Price::find ( 'price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?', $channelcostppr->price_manage_id, $partition_code, $packing, $total_weight, $total_weight )->getOne ();
		//计算运费
		if (! $price->isNewRecord ()) {
			if ($price->additional_weight > 0) {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $price->first_weight) , $price->additional_weight) ) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			} else {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(bcsub($total_weight , $price->first_weight) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			}
		} else {
			return array ();
		}
		//原币种
		$tracking_fee1 = $tracking_fee;
		$tracking_rate = 1;
		//计算费用不是人民币，就转换为人民币
		if($price->currency_code != 'CNY'){
			$tracking_fee = self::exchangeRate($date,$tracking_fee, $price->currency_code, 0, $tb_channel->supplier_id);
			//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$price->currency_code,$date,$date)->getOne();
			$code_currency = CodeCurrencyItem::getCurrencyRate($price->currency_code,$date, $tb_channel->supplier_id);
			//汇率
			$tracking_rate = $code_currency;
		}
		//燃油费率
		//获取网络燃油表
		$rate = 0;
		if ($caldata['product_name'] != 'EMS-FY') {
			$network = Networkfuel::find ( 'network_id=? and effective_date<=? and fail_date>=?', $network_id, $date, $date )->getOne ();
			if (! $network->isNewRecord ()) {
				$rate = $network->rate;
			}
		}
		
		//燃油折扣
		if ($channel->fuel_surcharge_dicount > 0 && $rate > 0) {
			$rate = bcmul($rate , $channel->fuel_surcharge_dicount);
		}
		$fee_amount = 0; //总费用
		//获取渠道成本类型
		$channelcosttype = ChannelCosttype::find ( "channel_cost_id = ? and package_type = ?", $channel->channel_cost_id, $caldata['packing_type'] )->getOne ();
		$fee_item_info = array ();
		if (! $channelcosttype->isNewRecord ()) {
			if (count ( $channelcosttype->channelcostformula ) > 0) {
				foreach ( $channelcosttype->channelcostformula as $v ) {
					if ($v->calculation_flag == '1' && $v->effective_time <= $date && $v->fail_time >= $date) {
						//根据公式里的币种转换偏派和运费的币种
						$remote_fee2 = $remote_fee;
						$tracking_fee2 = $tracking_fee;
						$v_rate = 1;
						if($v->currency_code != 'CNY'){
							$tracking_fee2 = self::exchangeRate($date,$tracking_fee, $v->currency_code, 1, $tb_channel->supplier_id);
							$remote_fee2 = self::exchangeRate($date,$remote_fee, $v->currency_code, 1, $tb_channel->supplier_id);
							$code_currency = CodeCurrencyItem::getCurrencyRate($v->currency_code,$date, $tb_channel->supplier_id);
							//汇率
							$v_rate = $code_currency;
						}
						$item_code = FeeItem::find ( 'item_name=?', $v->fee_name )->getOne ();
						//超尺寸/超重附加费
						$over_size_fee = 0;
						if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
							$array = array (
								$caldata['length_out'],
								$caldata['width_out'],
								$caldata['height_out']
							);
							
							rsort ( $array ); //逆排序
							$over_size_formula_fee = Helper_Formula::parse ( $v->formula, array (
								'net_weight' => $caldata['weight'],
								'girth' => $array [0]+2*($array [1]+$array [2]),
								'first_length' => $array [0],
								'second_length' => $array [1],
								'weight' => '',
								'country' => '',
								'baf' => '',
								'zone' => '',
								'freight' => '',
								'icount' => '',
								'tax' => '',
								'over_count' => '',
								'special_count' => '',
								'remote' => '',
								'cubic' => ''
							) );
							$over_size_fee +=$over_size_formula_fee;
						}
						if ($over_size_fee != 0) { // false 和 0 都不计算
							//超尺寸/超重附加费
							if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $over_size_quantity,
									'fee' => round($over_size_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							}
							//上面已经计算过了，这里跳过
							if($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges'){
								continue;
							}
						}
						
						$item_fee = Helper_Formula::parse ( $v->formula, array (
							'girth' => '',
							'first_length' => '',
							'second_length' => '',
							'net_weight' => $netweight,
							'weight' => $total_weight,
							'country' => $caldata['country_code'],
							'baf' => $rate,
							'zone' => $partition_code,
							'freight' => $tracking_fee2,
							'icount' => $package_quantity,
							'tax' => $channel->tax,
							'over_count' => $over_size_quantity,
							'special_count' => $special_packing_fee_count,
							'remote' => $remote_fee2,
							'cubic' => $volumn
						) );
						if ($item_fee != 0) { // false 和 0 都不计算
							// 	                        if ($item_code->sub_code == 'logisticsExpressASP_EX0035') { //超尺寸
							// 	                            $fee_item_info [$item_code->sub_code] = array (
							// 	                                'quantity' => $over_size_quantity,
							// 	                                'fee' => round($item_fee,2),
							// 	                                'btype_id' => $v->supplier_id
							// 	                            );
							// 	                        } else {
							if ($item_code->sub_code == 'logisticsExpressASP_EX0034') { //异型包装
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $special_packing_fee_count,
									'fee' => round($item_fee,2),
									'btype_id' => $v->supplier_id
								);
							} else {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => '1',
									'fee' => round($item_fee,2),
									'btype_id' => $v->supplier_id
								);
							}
							// 	                        }
							//计算费用不是人民币，就转换为人民币
							if($v->currency_code != 'CNY'){
								$item_fee = self::exchangeRate($date,$item_fee, $v->currency_code, 0, $tb_channel->supplier_id);
							}
							$fee_amount = bcadd($item_fee,$fee_amount);
						}
					}
				}
			}
		}
		//判断渠道成本是否已经计算燃油
		if ($channel->fuel_surcharge_flag == '1') {
			$tracking_fee = bcmul($tracking_fee , bcadd(1 , $rate));
			$tracking_fee1 = bcmul($tracking_fee1 , bcadd(1 , $rate));
		}
		//偏远+燃油
		$remote_fee = bcmul($remote_fee , bcadd(1 , $rate));
		$remote_fee1 = bcmul($remote_fee1 , bcadd(1 , $rate));
		$tax_fee = 0;
		if (isset ( $fee_item_info ['service_fee'] )) {
			if(@$fee_item_info ['service_fee'] ['currency_code'] != 'CNY'){
				$service_fee = self::exchangeRate($date,$fee_item_info ['service_fee'] ['fee'], @$fee_item_info ['service_fee'] ['currency_code'], 0, $tb_channel->supplier_id);
			}else{
				$service_fee = $fee_item_info ['service_fee'] ['fee'];
			}
			$tax_fee = bcmul(bcadd(bcadd(bcsub($fee_amount , $service_fee) , $tracking_fee) , $remote_fee) , $channel->tax);
		} else {
			$tax_fee = bcmul(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $channel->tax);
		}
		$fee_item_info ['logisticsExpressASP_EX0001'] = array (
			'quantity' => '1',
			'fee' => round($tracking_fee1, 2),
			'rate' => $tracking_rate,
			'currency_code' => $price->currency_code
		);
		$fee_item_info ['logisticsExpressASP_EX0020'] = array (
			'quantity' => '1',
			'fee' => round($remote_fee1, 2),
			'rate' => $remote_rate,
			'currency_code' => $currency
		);
		$fee_item_info ['tax'] = array (
			'quantity' => '1',
			'fee' => round($tax_fee,2)
		);
		// 	    if ($order->need_pick_up == '1' && strlen ( $order->sender_postal_code ) == '6') {
		// 	        $zipcode = Zipcode::find ( 'pick_company="青岛仓" and zip_code_low <= ? and zip_code_high >= ?', $order->sender_postal_code, $order->sender_postal_code )->getOne ();
		// 	        if (! $zipcode->isNewRecord ()) {
		// 	            $fee_item_info ['logisticsExpressASP_EX0036'] = array (
		// 	                'quantity' => '1',
		// 	                'fee' => '5'
		// 	            );
		// 	        }
		// 	    }
		//组合返回各项费用
		$data = array ();
		$data ['public_price'] = round(bcadd(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $tax_fee),2); //总运费
		$data ['price_info'] = array (
			'fee_item' => $fee_item_info,
			'rate' => $rate, //燃油费率
			'account' => $price->account, //账号,实际是产品代码，product_code
			'total_weight' => $total_cost_weight, //计费重
			'weight_label' => $label_weight  //标签重
		);
		return $data;
	}
	/**
	 * 计算应付
	 *
	 * @return string
	 */
	function payment($order, $channelcostppr, $network_id, $special_packing_fee_count = '', $date = '') {
		bcscale(4);
		if ($date == '') {
			$date = time ();
		}
		
		$channel = ChannelCost::find ( 'channel_cost_id=?', $channelcostppr->channel_cost_id )->getOne ();
		$channel_data = Channel::find('channel_id=?',$channel->channel_id)->getOne();
		$weight_arr = self::getweightarr($order, 2 ,$channel_data);
		$volumn = 0;
		foreach ($order->faroutpackages as $farout){
			$volumn += (floor ( $farout->length_out ) * floor ( $farout->width_out ) * floor ( $farout->height_out ))* $farout->quantity_out ;
		}
		// 		$volumn = $weight_arr['total_volumn_weight'];
		//出库包裹总计费重
		$total_volumn_weight = $weight_arr['total_volumn_weight'];
		$netweight = $weight_arr['total_real_weight'];
		//标签重
		$label_weight = $weight_arr['total_label_weight'];
		
		//实计费重量保存到order表
		$total_cost_weight = $weight_arr['total_cost_weight'];
		
		//根据单件最低计费重计算出来的总计费重或根据单件最低计费重计算出来的总计费重
		$total_weight = $weight_arr['total_cost_weight'];
		
		//根据单件最低计费重计算出来的总计费重
		$total_single_weight = $weight_arr['total_cost_weight'];
		
		$disabled_country = ChannelCountryDisabled::find ( 'channel_id = ? and effect_time<=? and failure_time>=?', $channel->channel_id, $date, $date )->getOne();
		$countries = explode(',', $disabled_country->country_code_two);
		if (count ( $countries ) > 0) {
			if (in_array ( $order->consignee_country_code, $countries )) {
				return array ();
			}
		}
		//偏远地区附加费
		$remote_city = Remote::find ( 'remote_manage_id = ? and country_code_two=? and (remote_city=? || remote_city=?) and ifnull(remote_city,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $order->consignee_city, strtolower ( str_replace ( '-', '', str_replace ( ' ', '', $order->consignee_city ) ) ) )->getOne ();
		if (! $remote_city->isNewRecord ()) { //偏派城市
			if ($remote_city->additional_weight > 0) {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_weight) ) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			} else {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			}
			if ($remote_fee <= $remote_city->lowest_fee) {
				$remote_fee = $remote_city->lowest_fee;
			}
		} else {
			$post_code = str_replace ( array (
				" ",
				'-'
			), '', $order->consignee_postal_code );
			//除了GB,其他国家都使用任意一个起始邮编的长度来截取超长的邮编
			if ($order->consignee_country_code != 'GB') {
				$remote = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $channelcostppr->remote_manage_id, $order->consignee_country_code )->getOne ();
				if (! $remote->isNewRecord ()) {
					$length = strlen ( $remote->start_postal_code );
					if (strlen ( $post_code ) > $length) {
						$post_code = substr ( $post_code, 0, $length );
					}
				}
			}
			//$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $order->consignee_postal_code, $order->consignee_postal_code )->getOne ();
			//if ($remote_postcode->isNewRecord ()) {
				$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $post_code, $post_code )->getOne ();
			//}
			if (! $remote_postcode->isNewRecord ()) { //偏派邮编
				if ($remote_postcode->additional_weight > 0) {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_weight) ) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				} else {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				}
				if ($remote_fee <= $remote_postcode->lowest_fee) {
					$remote_fee = $remote_postcode->lowest_fee;
				}
			} else {
				$remote_fee = 0;
			}
		}
		
		if ($remote_fee == 0) {
			$zip = str_replace ( array (
				" ",
				'-'
			), '', $order->consignee_postal_code );
			$length_zip = strlen ( $zip );
			//             if($order->consignee_country_code!='GB') {
			$remote_zip1 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and left(start_postal_code,' . $length_zip . ')<=? and left(end_postal_code,' . $length_zip . ')>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $zip, $zip )->getOne ();
			if (! $remote_zip1->isNewRecord ()) {
				if ($remote_zip1->additional_weight > 0) {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_weight) ) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				} else {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				}
				if ($remote_fee <= $remote_zip1->lowest_fee) {
					$remote_fee = $remote_zip1->lowest_fee;
				}
			} else {
				//查询慢，调整
				$remote_zip2 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!="" and start_postal_code<=(left((?),length(start_postal_code))) and end_postal_code>=(left((?),length(end_postal_code)))', $channelcostppr->remote_manage_id, $order->consignee_country_code ,$zip,$zip)->getOne ();
				//
				if (! $remote_zip2->isNewRecord ()) {
					//
					if ($remote_zip2->additional_weight > 0) {
						if (($total_weight - $remote_zip2->first_weight) > 0) {
							$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_zip2->first_weight) , $remote_zip2->additional_weight) ) , $remote_zip2->additional_fee) , $remote_zip2->first_fee);
						} else {
							$remote_fee = $remote_zip2->first_fee;
						}
					} else {
						if (($total_weight - $remote_zip2->first_weight) > 0) {
							$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_zip2->first_weight) , $remote_zip2->additional_fee) , $remote_zip2->first_fee);
						} else {
							$remote_fee = $remote_zip2->first_fee;
						}
					}
					//
					if ($remote_fee <= $remote_zip2->lowest_fee) {
						$remote_fee = $remote_zip2->lowest_fee;
					}
				}
			}
			//             }
		}
		//原币种
		$remote_fee1 = $remote_fee;
		$remote_rate = 1;
		$currency = '';
		//如果偏派币种不是人民币，就转换一下
		if(!$remote_city->isNewRecord()){
			if($remote_city->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_city->currency_code, 0, $channel_data->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_city->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_city->currency_code,$date, $channel_data->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_city->currency_code;
			}
		}else{
			if($remote_postcode->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_postcode->currency_code, 0, $channel_data->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_postcode->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_postcode->currency_code,$date, $channel_data->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_postcode->currency_code;
			}
			// 			echo "<pre>";
			// 			print_r($remote_postcode->currency_code);
			// 			exit;
		}
		
		if ($order->service_code == 'ePacket-FY') {
			$remote_fee = 0;
		}
		//超尺寸/超重附加费(已改其他方法计算)
		// 		$over_size_quantity = '';
		//总包裹数
		$package_quantity = 0;
		
		foreach ( $order->faroutpackages as $temp ) {
			// 			if ($order->service_code == 'Express_Standard_Global') {
			// 				$array = array (
			// 					$temp->length_out,
			// 					$temp->width_out,
			// 					$temp->height_out
			// 				);
			
			// 				rsort ( $array ); //逆排序
			// 				if ($array [0] > '121') {
			// 					//收取超尺寸费用
			// 					$over_size_quantity += $temp->quantity_out;
			// 				} else {
			// 					if ($array [1] > '76') {
			// 						//收取超尺寸费用
			// 						$over_size_quantity += $temp->quantity_out;
			// 					} else {
			// 						if ($temp->weight_out > 32) {
			// 							//收取超重费用
			// 							$over_size_quantity += $temp->quantity_out;
			// 						}
			// 					}
			// 				}
			// 			}
			$package_quantity += $temp->quantity_out;
		}
		
		//基础运费
		$tracking_fee = 0;
		//获取分区
		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $channelcostppr->partition_manage_id, $order->consignee_country_code )->getAll ();
		if (count ( $partition ) == 0) {
			return array ();
		}
		$partition_code = '';
		$partition_code2 = '';
		foreach ( $partition as $p ) {
			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $order->consignee_postal_code ) ) == $order->consignee_postal_code || substr ( $order->consignee_postal_code, 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
				$partition_code = $p->partition_code;
			}
			if (! $p->postal_code) {
				$partition_code2 = $p->partition_code;
			}
		}
		if (! $partition_code) {
			$partition_code = $partition_code2;
		}
		//包装类型
		$packing = 'BOX';
		if ($order->packing_type == 'DOC') {
			$packing = "DOC";
		}
		if ($order->packing_type == 'PAK' && ($order->service_code == 'WIG-FY' || $order->service_code == 'EUUS-FY')) {
			$packing = "PAK";
		}
		//获取价格
		$price = Price::find ( 'price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?', $channelcostppr->price_manage_id, $partition_code, $packing, $total_weight, $total_weight )->getOne ();
		//计算运费
		if (! $price->isNewRecord ()) {
			if ($price->additional_weight > 0) {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $price->first_weight) , $price->additional_weight) ) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			} else {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(bcsub($total_weight , $price->first_weight) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			}
		} else {
			return array ();
		}
		//原币种
		$tracking_fee1 = $tracking_fee;
		$tracking_rate = 1;
		//计算费用不是人民币，就转换为人民币
		if($price->currency_code != 'CNY'){
			$tracking_fee = self::exchangeRate($date,$tracking_fee, $price->currency_code, 0, $channel_data->supplier_id);
			$code_currency = CodeCurrencyItem::getCurrencyRate($price->currency_code,$date, $channel_data->supplier_id);
			//汇率
			$tracking_rate = $code_currency;
		}
		//燃油费率
		//获取网络燃油表
		$rate = 0;
		if ($order->service_code != 'EMS-FY') {
			$network = Networkfuel::find ( 'network_id=? and effective_date<=? and fail_date>=?', $network_id, $date, $date )->getOne ();
			if (! $network->isNewRecord ()) {
				$rate = $network->rate;
			}
		}
		
		//燃油折扣
		if ($channel->fuel_surcharge_dicount > 0 && $rate > 0) {
			$rate = bcmul($rate , $channel->fuel_surcharge_dicount);
		}
		$fee_amount = 0; //总费用
		//获取渠道成本类型
		$channelcosttype = ChannelCosttype::find ( "channel_cost_id = ? and package_type = ? and customs_code=?", $channel->channel_cost_id, $order->packing_type, $order->customer->customs_code)->getOne ();
		$fee_item_info = array ();
		$new_over_size_fee = 0;
		if (! $channelcosttype->isNewRecord ()) {
			if (count ( $channelcosttype->channelcostformula ) > 0) {
				foreach ( $channelcosttype->channelcostformula as $v ) {
					if ($v->calculation_flag == '1' && $v->effective_time <= $date && $v->fail_time >= $date) {
						//根据公式里的币种转换偏派和运费的币种
						$remote_fee2 = $remote_fee;
						$tracking_fee2 = $tracking_fee;
						$v_rate = 1;
						if($v->currency_code != 'CNY'){
							$tracking_fee2 = self::exchangeRate($date,$tracking_fee, $v->currency_code, 1, $channel_data->supplier_id);
							$remote_fee2 = self::exchangeRate($date,$remote_fee, $v->currency_code, 1, $channel_data->supplier_id);
							$code_currency = CodeCurrencyItem::getCurrencyRate($v->currency_code,$date, $channel_data->supplier_id);
							//汇率
							$v_rate = $code_currency;
						}
						$item_code = FeeItem::find ( 'item_name=? and customs_code=?', $v->fee_name , $order->customer->customs_code)->getOne ();
						//带电的订单(公式设置了带电附加费)会计算带电附加费#80931
						if($order->has_battery!=1&&$v->fee_name=='带电附加费'){
							continue;
						}
						//超尺寸/超重附加费
						$over_size_fee = 0;
						$over_size_quantity = 0;
						foreach ( $order->faroutpackages as $temp ) {
							if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
								$array = array (
									$temp->length_out,
									$temp->width_out,
									$temp->height_out
								);
								
								rsort ( $array ); //逆排序
								$over_size_formula_fee = Helper_Formula::parse ( $v->formula, array (
									'net_weight' => $temp->weight_out,
									'girth' => $array [0]+2*($array [1]+$array [2]),
									'first_length' => $array [0],
									'second_length' => $array [1],
									'declaration_type' => $order->declaration_type,
									'weight' => '',
									'country' => '',
									'baf' => $rate,
									'zone' => '',
									'freight' => '',
									'icount' => '',
									'tax' => '',
									'over_count' => '',
									'special_count' => '',
									'remote' => '',
									'cubic' => ''
								) );
								if ($over_size_formula_fee===false){
									QLog::log('formulaerror:'.$v->formula);
									$data['success'] = 'formulaerror';
									return $data;
								}
								$over_size_fee +=$over_size_formula_fee*$temp->quantity_out;
								if ($over_size_formula_fee != 0){
									$over_size_quantity += $temp->quantity_out;
								}
							}
						}
						if ($over_size_fee != 0) { // false 和 0 都不计算
							//超尺寸/超重附加费
							if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $over_size_quantity,
									'fee' => round($over_size_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							}
							//上面已经计算过了，这里跳过
							if($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges'){
								//计算费用不是人民币，就转换为人民币
								if($v->currency_code != 'CNY'){
									$new_over_size_fee += self::exchangeRate($date,$over_size_fee, $v->currency_code, 0, $channel_data->supplier_id);
								}
								continue;
							}
						}
						
						
						$item_fee = Helper_Formula::parse ( $v->formula, array (
							'girth' => '',
							'first_length' => '',
							'second_length' => '',
							'declaration_type' => $order->declaration_type,
							'net_weight' => @$netweight,
							'weight' => $total_weight,
							'country' => $order->consignee_country_code,
							'baf' => $rate,
							'zone' => $partition_code,
							'freight' => $tracking_fee2,
							'icount' => $package_quantity,
							'tax' => $channel->tax,
							'over_count' => $over_size_quantity,
							'special_count' => $special_packing_fee_count,
							'remote' => $remote_fee2,
							'cubic' => @$volumn
						) );
						//#83414
						if ($item_fee===false){
							//存在生效费用项无法计算
							QLog::log('formulaerror:'.$v->formula);
							$data['success'] = 'formulaerror';
							return $data;
						}
						if ($item_fee != 0) { // false 和 0 都不计算
							// 							if ($item_code->sub_code == 'logisticsExpressASP_EX0035') { //超尺寸
							// 								$fee_item_info [$item_code->sub_code] = array (
							// 									'quantity' => $over_size_quantity,
							// 									'fee' => round($over_size_fee, 2),
							// 									'rate' => $v_rate,
							// 									'currency_code' => $v->currency_code,
							// 									'btype_id' => $v->supplier_id
							// 								);
							// 							} else {
							if ($item_code->sub_code == 'logisticsExpressASP_EX0034') { //异型包装
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $special_packing_fee_count,
									'fee' => round($item_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							} else {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => '1',
									'fee' => round($item_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							}
							// 							}
							
							//计算费用不是人民币，就转换为人民币
							if($v->currency_code != 'CNY'){
								$item_fee = self::exchangeRate($date,$item_fee, $v->currency_code, 0, $channel_data->supplier_id);
							}
							$fee_amount = bcadd($item_fee,$fee_amount);
						}
					}
				}
			}
		}
		//判断渠道成本是否已经计算燃油
		if ($channel->fuel_surcharge_flag == '1') {
			$tracking_fee = bcmul($tracking_fee , bcadd(1 , $rate));
			$tracking_fee1 = bcmul($tracking_fee1 , bcadd(1 , $rate));
		}
		//偏远+燃油
		$remote_fee = bcmul($remote_fee , bcadd(1 , $rate));
		$remote_fee1 = bcmul($remote_fee1 , bcadd(1 , $rate));
		$tax_fee = 0;
		if (isset ( $fee_item_info ['service_fee'] )) {
			if($fee_item_info ['service_fee'] ['currency_code'] != 'CNY'){
				$service_fee = self::exchangeRate($date,$fee_item_info ['service_fee'] ['fee'], $fee_item_info ['service_fee'] ['currency_code'], 0, $channel_data->supplier_id);
			}else{
				$service_fee = $fee_item_info ['service_fee'] ['fee'];
			}
			$tax_fee = bcmul(bcadd(bcadd(bcsub($fee_amount , $service_fee) , $tracking_fee) , $remote_fee) , $channel->tax);
		} else {
			$tax_fee = bcmul(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $channel->tax);
		}
		$fee_item_info ['logisticsExpressASP_EX0001'] = array (
			'quantity' => '1',
			'fee' => round($tracking_fee1, 2),
			'rate' => $tracking_rate,
			'currency_code' => $price->currency_code
		);
		$fee_item_info ['logisticsExpressASP_EX0020'] = array (
			'quantity' => '1',
			'fee' => round($remote_fee1, 2),
			'rate' => $remote_rate,
			'currency_code' => $currency
		);
		$fee_item_info ['tax'] = array (
			'quantity' => '1',
			'fee' => round($tax_fee, 2)
		);
		if ($order->need_pick_up == '1' && strlen ( $order->sender_postal_code ) == '6') {
			$zipcode = Zipcode::find ( 'pick_company="青岛仓" and zip_code_low <= ? and zip_code_high >= ?', $order->sender_postal_code, $order->sender_postal_code )->getOne ();
			if (! $zipcode->isNewRecord ()) {
				$fee_item_info ['logisticsExpressASP_EX0036'] = array (
					'quantity' => '1',
					'fee' => '5'
				);
			}
		}
		//组合返回各项费用
		$data = array ();
		$data ['public_price'] = round(bcadd(bcadd(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $tax_fee),$new_over_size_fee),2); //总运费
		QLog::log('public_price'.$data ['public_price']);
		$data ['price_info'] = array (
			'fee_item' => $fee_item_info,
			'rate' => $rate, //燃油费率
			'account' => $price->account, //账号,实际是产品代码，product_code
			'total_weight' => $total_cost_weight, //计费重
			'weight_label' => $label_weight  //标签重
		);
		//整票货的计费重
		$data ['total_single_weight'] = $total_weight;
		$data ['total_out_volumn_weight'] = $total_volumn_weight;
		return $data;
	}
	/**
	 * @todo   组织重量数组
	 * @author stt
	 * @since  July 31th 2020
	 */
	static function getweightarr($order,$flag,$channel=null,$packages_arr=null) {
		//总预报重(标签重)
		$total_label_weight=0;
		
		//总计费重
		$total_cost_weight=0;
		
		//订单渠道,$flag=1是应收,$flag=2是应付
		if ($flag==1){
			$channel=Product::find("product_name=?",$order->service_code)->getOne();
		}else{
			if (is_null($channel)){
				$channel=Channel::find('channel_id=?',$order->channel_id)->getOne();
			}
		}
		$weight_arr = array();
		$is_packages_arr = 0;
		if ($flag==1){
			if (count($packages_arr)>0){
				$packages = $packages_arr;
				$is_packages_arr = 1;
			}else{
				$packages = $order->farpackages;
			}
		}else{
			$packages = $order->faroutpackages;
		}
		//单件计费重
		$total_weight='';
		//总预报重(标签重)
		$total_label_weight=0;
		$volumn_weight=0;
		$channel_flag = 0;
		$quantity_flag = 0;
		$is_jipao = 0;
		$total_volumn_weight = 0;
		$total_real_weight = 0;
		$total_quantity = 0;
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$quantity = $package->quantity_out;
				$total_quantity+=$quantity;
			}
		}
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$length = $package->length_out;
				$height = $package->height_out;
				$width = $package->width_out;
				$weight = $package->weight_out;
				$quantity = $package->quantity_out;
				//入库
			}elseif($flag==1){
				//保存入库包裹信息
				if ($is_packages_arr){
					$length = $package['length'];
					$height = $package['height'];
					$width = $package['width'];
					$weight = $package['weight'];
					$quantity = $package['quantity'];
				}else{
					$length = $package->length;
					$height = $package->height;
					$width = $package->width;
					$weight = $package->weight;
					$quantity = $package->quantity;
				}
				//预报
			}else{
				$length = floor($package->length_out);
				$height = floor($package->height_out);
				$width = floor($package->width_out);
				$weight = $package->weight_out;
				$quantity = $package->quantity_out;
			}
			
			//订单的产品
			$pr=Product::find('product_name=?',$order->service_code)->getOne();
			//产品下的渠道成本
			if ($flag==1){
				$ch=ChannelCost::find('channel_id=? and product_id=?',$order->channel_id,$pr->product_id)->getOne();
			}else{
				$ch=ChannelCost::find('channel_id=? and product_id=?',$channel->channel_id,$pr->product_id)->getOne();
			}
			//发件日
			if ($order->record_order_date){
				$date = $order->record_order_date;
			}else{
				$date = time();
			}
			//基础运费价格表
			$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$ch->channel_cost_id,$date,$date)->getOne();
			
			$ratio=$flag==1?$pr->ratio:$ch->ratio;
			//定义长宽高数组
			$verify_array=array($length,$width,$height);
			rsort($verify_array);
			//计算单件体积重
			if(($channel->type==4 && ceil($verify_array[0])<=60 )||$channel->type==5 ){
				$volumn_weight="0";
			}else {
				if ($ratio){
					$volumn_weight=(ceil($length)*ceil($width)*ceil($height))/$ratio;
				}else{
					$volumn_weight="0";
				}
			}
			if($order->packing_type=='PAK' && ($flag==2||$flag==3)){
				$total_weight=$weight;
				$label_weight = $total_weight;
				//PAK加上规则2：实重减3g
				if ($channel->forecast_type==2){
					$label_weight = $label_weight-0.003;
				}
				if ($total_weight < 0.5) {
					$total_weight = 0.5;
				}
			}else{
				if ($flag==2 || $flag==3){
					$label_weight = $weight;
					if ($channel->forecast_type==1){
						if ((floor ( $label_weight / 0.5 ) * 0.5 + 0.1) >= $label_weight) {
							$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
						}
					}elseif ($channel->forecast_type==2){
						$label_weight = $label_weight-0.003;
					}else{
						$label_weight = $weight;
					}
				}
				if ($flag == 3){
					$weight = $label_weight;
				}
				if($volumn_weight>$weight){
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $volumn_weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out  = ceil ( $volumn_weight / 0.5 ) * 0.5;
					}
					$is_jipao = 1;
				}else{
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out = ceil ( $weight / 0.5 ) * 0.5;
					}
				}
				
				$total_weight=$weight_cost_out;
			}
			//小包类
			if($channel->type==5){
				$total_weight='';
				$total_weight = $weight;
				$total_weight = sprintf("%.3f",$total_weight);
			}
			//#83852 
			//单件最低计费重，仅针对一票多件的情况下生效
			if ($flag==2 && $channelcostppr && $total_quantity>1){
				if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$total_weight)){
					$total_weight = $channelcostppr->single_lowest_weight;
				}
			}
			@$total_label_weight += $label_weight*$quantity;
			@$total_real_weight += $weight*$quantity;
			@$total_volumn_weight += $volumn_weight*$quantity;
			@$total_cost_weight += $total_weight*$quantity;
			if ($flag==1){
				$arr[$key]['quantity'] = $quantity;
				$arr[$key]['length'] = $length;
				$arr[$key]['width'] = $width;
				$arr[$key]['height'] = $height;
				$arr[$key]['weight'] = $weight;
			}else{
				$arr[$key]['quantity_out'] = $quantity;
				$arr[$key]['length_out'] = $length;
				$arr[$key]['width_out'] = $width;
				$arr[$key]['height_out'] = $height;
				$arr[$key]['weight_out'] = $weight;
			}
			if ($order->channel_id || $flag==1){
				$arr[$key]['volumn_weight'] = $channel->type==5?sprintf("%.3f",$volumn_weight):sprintf("%.2f",$volumn_weight);
				$arr[$key]['label_weight'] = $channel->type==5?sprintf("%.3f",@$label_weight):sprintf("%.2f",@$label_weight);
				$arr[$key]['cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight):sprintf("%.2f",$total_weight);
				$arr[$key]['total_cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight*$quantity):sprintf("%.2f",$total_weight*$quantity);
				$arr[$key]['total_single_label_weight'] = ($channel->type==5)?sprintf("%.3f",@$label_weight*$quantity):sprintf("%.2f",@$label_weight*$quantity);
			}
			if ($quantity>1){
				$quantity_flag = 2;
			}
		}
		if ($channel_flag==2){
			$total_cost_weight = ceil ( $total_cost_weight / 0.5 ) * 0.5;
		}
		$weight_arr['package']=@$arr;
		
		$weight_arr['is_jipao']=$is_jipao;
		
		//总预报重(标签重)
		$weight_arr['total_label_weight']=($channel->type==5)?sprintf("%.3f",$total_label_weight):sprintf("%.2f",$total_label_weight);
		
		//总实重
		$weight_arr['total_real_weight']=sprintf("%.2f",@$total_real_weight);
		
		//总体积重
		$weight_arr['total_volumn_weight']=sprintf("%.2f",@$total_volumn_weight);
		
		//总计费重 在flag==3得时候是预报总计费重
		$weight_arr['total_cost_weight']=($channel->type==5)?sprintf("%.3f",$total_cost_weight):sprintf("%.2f",$total_cost_weight);
		
		if($channel->type==2 && (count($packages)>1 || $quantity_flag==2)){
			$weight_arr['total_cost_weight'] = ceil ( $weight_arr['total_cost_weight'] / 0.5 ) * 0.5;
		}elseif($channel->type==3 && (count($packages)>1 || $quantity_flag==2)){
			if ($total_real_weight>$total_volumn_weight){
				$weight_arr['total_cost_weight'] = ceil ( $total_real_weight / 0.5 ) * 0.5;
			}else{
				$weight_arr['total_cost_weight'] = ceil ( $total_volumn_weight / 0.5 ) * 0.5;
			}
		}
		return $weight_arr;
	}
	/**
	 * 获取重量数组
	 */
	static function getweightlist($order, $channelcost) {
		$channelcostppr = Channelcostppr::find ( 'channel_cost_id=? and effective_time<=? and invalid_time>=?', $channelcost->channel_cost_id, time (), time () )->getOne ();
		if ($channelcostppr->isNewRecord ()) {
			return array ();
		}
		//优化空数组
		$total_weight_array = array ();
		//标签重
		$label_weight = 0;
		//如果包裹类型是PAK将实重算作计费重
		//获取渠道成本中计泡系数
		$channel = ChannelCost::find ( 'channel_cost_id=?', $channelcostppr->channel_cost_id )->getOne ();
		if ($order->packing_type == 'PAK') {
			foreach ( $order->faroutpackages as $v ) {
				$label_weight += $v->weight_out * $v->quantity_out;
			}
			//PAK类型的时候，计费重大于 0.5的整数倍0.1以内，自动向下取整到0.5的整数倍
			if ($label_weight > 0.5 && (floor ( $label_weight / 0.5 ) * 0.5 + 0.1) > $label_weight) {
				$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
			} else {
				$label_weight = sprintf ( "%.1f", $label_weight );
			}
		} else {
			$ratio = $channel->ratio;
			foreach ( $order->faroutpackages as $faroutpackage ) {
				$volumn_weight = $faroutpackage->length_out * $faroutpackage->width_out * $faroutpackage->height_out / $ratio;
				if ($volumn_weight > $faroutpackage->weight_out) {
					$label_weight += $volumn_weight * $faroutpackage->quantity_out;
				} else {
					$label_weight += $faroutpackage->weight_out * $faroutpackage->quantity_out;
				}
			}
			//大于20公斤
			if ($label_weight > 20) {
				if ((floor ( $label_weight ) + 0.1) > $label_weight) {
					$label_weight = floor ( $label_weight );
				} else {
					$label_weight = ceil ( $label_weight );
				}
			} else {
				if ((floor ( $label_weight / 0.5 ) * 0.5 + 0.1) > $label_weight) {
					$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
				} else {
					$label_weight = ceil ( $label_weight / 0.5 ) * 0.5;
				}
			}
		}
		if ($label_weight > 20) {
			$total_weight_array = array (
				'n' => $label_weight,
				'n+1' => $label_weight + 1,
				'n+2' => $label_weight + 2
			);
		} else {
			$total_weight_array = array (
				'n' => $label_weight,
				'n+0.5' => $label_weight + 0.5,
				'n+1' => $label_weight + 1,
				'n=21' => 21
			);
		}
		return $total_weight_array;
	}
	/**
	 * @todo   退件计算应付
	 * @author 吴开龙
	 * @since  2020-11-30 14:36:04
	 * @link   #83699
	 */
	function returnpayment($order, $channelcostppr, $network_id, $special_packing_fee_count = '', $date = '') {
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
		bcscale(4);
		if ($date == '') {
			$date = time ();
		}
		
		$channel = ReturnChannelCost::find ( 'channel_cost_id=?', $channelcostppr->channel_cost_id )->getOne ();
		$channel_data = ReturnChannel::find('channel_id=?',$channel->channel_id)->getOne();
		$weight_arr = self::returnreturngetweightarr($order, 2 ,$channel_data);
		$volumn = 0;
		foreach ($order->faroutpackages as $farout){
			$volumn += (floor ( $farout->length ) * floor ( $farout->width ) * floor ( $farout->height ))* $farout->quantity;
		}
		// 		$volumn = $weight_arr['total_volumn_weight'];
		//出库包裹总计费重
		$total_volumn_weight = $weight_arr['total_volumn_weight'];
		$netweight = $weight_arr['total_real_weight'];
		//标签重
		$label_weight = $weight_arr['total_label_weight'];
		
		//实计费重量保存到order表
		$total_cost_weight = $weight_arr['total_cost_weight'];
		
		//根据单件最低计费重计算出来的总计费重或根据单件最低计费重计算出来的总计费重
		$total_weight = $weight_arr['total_cost_weight'];
		
		//根据单件最低计费重计算出来的总计费重
		$total_single_weight = $weight_arr['total_cost_weight'];
		
// 		$disabled_country = ChannelCountryDisabled::find ( 'channel_id = ? and effect_time<=? and failure_time>=?', $channel->channel_id, $date, $date )->getOne();
// 		$countries = explode(',', $disabled_country->country_code_two);
// 		if (count ( $countries ) > 0) {
// 			if (in_array ( $order->consignee_country_code, $countries )) {
// 				return array ();
// 			}
// 		}
		//偏远地区附加费
		$remote_city = Remote::find ( 'remote_manage_id = ? and country_code_two=? and (remote_city=? || remote_city=?) and ifnull(remote_city,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $order->consignee_city, strtolower ( str_replace ( '-', '', str_replace ( ' ', '', $order->consignee_city ) ) ) )->getOne ();
		if (! $remote_city->isNewRecord ()) { //偏派城市
			if ($remote_city->additional_weight > 0) {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_weight) ) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			} else {
				if (($total_weight - $remote_city->first_weight) > 0) {
					$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_city->first_weight) , $remote_city->additional_fee) , $remote_city->first_fee);
				} else {
					$remote_fee = $remote_city->first_fee;
				}
			}
			if ($remote_fee <= $remote_city->lowest_fee) {
				$remote_fee = $remote_city->lowest_fee;
			}
		} else {
			$post_code = str_replace ( array (
				" ",
				'-'
			), '', $order->consignee_postal_code );
			//除了GB,其他国家都使用任意一个起始邮编的长度来截取超长的邮编
			if ($order->consignee_country_code != 'GB') {
				$remote = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $channelcostppr->remote_manage_id, $order->consignee_country_code )->getOne ();
				if (! $remote->isNewRecord ()) {
					$length = strlen ( $remote->start_postal_code );
					if (strlen ( $post_code ) > $length) {
						$post_code = substr ( $post_code, 0, $length );
					}
				}
			}
			//$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $order->consignee_postal_code, $order->consignee_postal_code )->getOne ();
			//if ($remote_postcode->isNewRecord ()) {
				$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $post_code, $post_code )->getOne ();
			//}
			if (! $remote_postcode->isNewRecord ()) { //偏派邮编
				if ($remote_postcode->additional_weight > 0) {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_weight) ) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				} else {
					if (($total_weight - $remote_postcode->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_postcode->first_weight) , $remote_postcode->additional_fee) , $remote_postcode->first_fee);
					} else {
						$remote_fee = $remote_postcode->first_fee;
					}
				}
				if ($remote_fee <= $remote_postcode->lowest_fee) {
					$remote_fee = $remote_postcode->lowest_fee;
				}
			} else {
				$remote_fee = 0;
			}
		}
		
		if ($remote_fee == 0) {
			$zip = str_replace ( array (
				" ",
				'-'
			), '', $order->consignee_postal_code );
			$length_zip = strlen ( $zip );
			//             if($order->consignee_country_code!='GB') {
			$remote_zip1 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and left(start_postal_code,' . $length_zip . ')<=? and left(end_postal_code,' . $length_zip . ')>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $channelcostppr->remote_manage_id, $order->consignee_country_code, $zip, $zip )->getOne ();
			if (! $remote_zip1->isNewRecord ()) {
				if ($remote_zip1->additional_weight > 0) {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_weight) ) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				} else {
					if (($total_weight - $remote_zip1->first_weight) > 0) {
						$remote_fee = bcadd(bcmul(bcsub($total_weight , $remote_zip1->first_weight) , $remote_zip1->additional_fee) , $remote_zip1->first_fee);
					} else {
						$remote_fee = $remote_zip1->first_fee;
					}
				}
				if ($remote_fee <= $remote_zip1->lowest_fee) {
					$remote_fee = $remote_zip1->lowest_fee;
				}
			} else {
				$remote_zip2 = Remote::find ( 'remote_manage_id = ? and country_code_two=? and ifnull(start_postal_code,"")!=""', $channelcostppr->remote_manage_id, $order->consignee_country_code )->getAll ();
				if (count ( $remote_zip2 ) > 0) {
					foreach ( $remote_zip2 as $z ) {
						$start_postal_code = $z->start_postal_code;
						$end_postal_code = $z->end_postal_code;
						if ("'" . substr ( $zip, 0, strlen ( $start_postal_code ) ) >= "'" . $start_postal_code && "'" . substr ( $zip, 0, strlen ( $end_postal_code ) ) <= "'" . $end_postal_code) {
							if ($z->additional_weight > 0) {
								if (($total_weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $z->first_weight) , $z->additional_weight) ) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							} else {
								if (($total_weight - $z->first_weight) > 0) {
									$remote_fee = bcadd(bcmul(bcsub($total_weight , $z->first_weight) , $z->additional_fee) , $z->first_fee);
								} else {
									$remote_fee = $z->first_fee;
								}
							}
							if ($remote_fee <= $z->lowest_fee) {
								$remote_fee = $z->lowest_fee;
							}
							if ($remote_fee > 0) {
								break;
							}
						}
					}
				}
			}
			//             }
		}
		//原币种
		$remote_fee1 = $remote_fee;
		$remote_rate = 1;
		$currency = '';
		//如果偏派币种不是人民币，就转换一下
		if(!$remote_city->isNewRecord()){
			if($remote_city->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_city->currency_code, 0, $channel_data->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_city->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_city->currency_code,$date, $channel_data->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_city->currency_code;
			}
		}else{
			if($remote_postcode->currency_code != 'CNY'){
				$remote_fee = self::exchangeRate($date,$remote_fee, $remote_postcode->currency_code, 0, $channel_data->supplier_id);
				//$code_currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_postcode->currency_code,$date,$date)->getOne();
				$code_currency = CodeCurrencyItem::getCurrencyRate($remote_postcode->currency_code,$date, $channel_data->supplier_id);
				//汇率
				$remote_rate = $code_currency;
				$currency = $remote_postcode->currency_code;
			}
			// 			echo "<pre>";
			// 			print_r($remote_postcode->currency_code);
			// 			exit;
		}
		
		if ($order->service_code == 'ePacket-FY') {
			$remote_fee = 0;
		}
		//超尺寸/超重附加费(已改其他方法计算)
		// 		$over_size_quantity = '';
		//总包裹数
		$package_quantity = 0;
		
		foreach ( $order->faroutpackages as $temp ) {
			// 			if ($order->service_code == 'Express_Standard_Global') {
			// 				$array = array (
			// 					$temp->length_out,
			// 					$temp->width_out,
			// 					$temp->height_out
			// 				);
			
			// 				rsort ( $array ); //逆排序
			// 				if ($array [0] > '121') {
			// 					//收取超尺寸费用
			// 					$over_size_quantity += $temp->quantity_out;
			// 				} else {
			// 					if ($array [1] > '76') {
			// 						//收取超尺寸费用
			// 						$over_size_quantity += $temp->quantity_out;
			// 					} else {
			// 						if ($temp->weight_out > 32) {
			// 							//收取超重费用
			// 							$over_size_quantity += $temp->quantity_out;
			// 						}
			// 					}
			// 				}
			// 			}
			$package_quantity += $temp->quantity;
		}
		
		//基础运费
		$tracking_fee = 0;
		//获取分区
		$partition = Partition::find ( 'partition_manage_id=? and country_code_two=?', $channelcostppr->partition_manage_id, $order->consignee_country_code )->getAll ();
		if (count ( $partition ) == 0) {
			return array ();
		}
		$partition_code = '';
		$partition_code2 = '';
		foreach ( $partition as $p ) {
			if (trim ( $p->postal_code ) && (substr ( $p->postal_code, 0, strlen ( $order->consignee_postal_code ) ) == $order->consignee_postal_code || substr ( $order->consignee_postal_code, 0, strlen ( $p->postal_code ) ) == $p->postal_code)) {
				$partition_code = $p->partition_code;
			}
			if (! $p->postal_code) {
				$partition_code2 = $p->partition_code;
			}
		}
		if (! $partition_code) {
			$partition_code = $partition_code2;
		}
		//包装类型
		$packing = 'BOX';
		if ($order->packing_type == 'DOC') {
			$packing = "DOC";
		}
		if ($order->packing_type == 'PAK' && ($order->service_code == 'WIG-FY' || $order->service_code == 'EUUS-FY')) {
			$packing = "PAK";
		}
		//获取价格
		$price = Price::find ( 'price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?', $channelcostppr->price_manage_id, $partition_code, $packing, $total_weight, $total_weight )->getOne ();
		//计算运费
		if (! $price->isNewRecord ()) {
			if ($price->additional_weight > 0) {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(ceil ( bcdiv(bcsub($total_weight , $price->first_weight) , $price->additional_weight) ) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			} else {
				if (($total_weight - $price->first_weight) > 0) {
					$tracking_fee = bcadd(bcmul(bcsub($total_weight , $price->first_weight) , $price->additional_fee) , $price->first_fee);
				} else {
					$tracking_fee = $price->first_fee;
				}
			}
		} else {
			return array ();
		}
		//原币种
		$tracking_fee1 = $tracking_fee;
		$tracking_rate = 1;
		//计算费用不是人民币，就转换为人民币
		if($price->currency_code != 'CNY'){
			$tracking_fee = self::exchangeRate($date,$tracking_fee, $price->currency_code, 0, $channel_data->supplier_id);
			$code_currency = CodeCurrencyItem::getCurrencyRate($price->currency_code,$date, $channel_data->supplier_id);
			//汇率
			$tracking_rate = $code_currency;
		}
		//燃油费率
		//获取网络燃油表
		$rate = 0;
		if ($order->service_code != 'EMS-FY') {
			$network = Networkfuel::find ( 'network_id=? and effective_date<=? and fail_date>=?', $network_id, $date, $date )->getOne ();
			if (! $network->isNewRecord ()) {
				$rate = $network->rate;
			}
		}
		
		//燃油折扣
		if ($channel->fuel_surcharge_dicount > 0 && $rate > 0) {
			$rate = bcmul($rate , $channel->fuel_surcharge_dicount);
		}
		$fee_amount = 0; //总费用
		//获取渠道成本类型
		$channelcost = ReturnChannelCost::find ( "channel_id = ?", $channel->channel_id)->getOne ();
		$fee_item_info = array ();
		$new_over_size_fee = 0;
		if (! $channelcost->isNewRecord ()) {
			$channelcostformula = ReturnChannelCostformula::find('channel_cost_id=?',$channelcost->channel_cost_id)->getAll();
			if (count ( $channelcostformula ) > 0) {
				foreach ( $channelcostformula as $v ) {
					if ($v->calculation_flag == '1' && $v->effective_time <= $date && $v->fail_time >= $date) {
						//根据公式里的币种转换偏派和运费的币种
						$remote_fee2 = $remote_fee;
						$tracking_fee2 = $tracking_fee;
						$v_rate = 1;
						if($v->currency_code != 'CNY'){
							$tracking_fee2 = self::exchangeRate($date,$tracking_fee, $v->currency_code, 1, $channel_data->supplier_id);
							$remote_fee2 = self::exchangeRate($date,$remote_fee, $v->currency_code, 1, $channel_data->supplier_id);
							$code_currency = CodeCurrencyItem::getCurrencyRate($v->currency_code,$date, $channel_data->supplier_id);
							//汇率
							$v_rate = $code_currency;
						}
						$item_code = FeeItem::find ( 'item_name=? and customs_code=?', $v->fee_name , $order->customer->customs_code)->getOne ();
						//带电的订单(公式设置了带电附加费)会计算带电附加费#80931
						if($order->has_battery!=1&&$v->fee_name=='带电附加费'){
							continue;
						}
						//超尺寸/超重附加费
						$over_size_fee = 0;
						$over_size_quantity = 0;
						foreach ( $order->faroutpackages as $temp ) {
							if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
								$array = array (
									$temp->length,
									$temp->width,
									$temp->height
								);
								
								rsort ( $array ); //逆排序
								$over_size_formula_fee = Helper_Formula::parse ( $v->formula, array (
									'net_weight' => $temp->weight,
									'girth' => $array [0]+2*($array [1]+$array [2]),
									'first_length' => $array [0],
									'second_length' => $array [1],
									'declaration_type' => $order->declaration_type,
									'weight' => '',
									'country' => '',
									'baf' => $rate,
									'zone' => '',
									'freight' => '',
									'icount' => '',
									'tax' => '',
									'over_count' => '',
									'special_count' => '',
									'remote' => '',
									'cubic' => ''
								) );
								if ($over_size_formula_fee===false){
									QLog::log('formulaerror:'.$v->formula);
									$data['success'] = 'formulaerror';
									return $data;
								}
								$over_size_fee +=$over_size_formula_fee*$temp->quantity;
								if ($over_size_formula_fee != 0){
									$over_size_quantity += $temp->quantity;
								}
							}
						}
						if ($over_size_fee != 0) { // false 和 0 都不计算
							//超尺寸/超重附加费
							if ($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges') {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $over_size_quantity,
									'fee' => round($over_size_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							}
							//上面已经计算过了，这里跳过
							if($item_code->sub_code == 'logisticsExpressASP_EX0035'||$item_code->sub_code == 'Overlength_Charges'){
								//计算费用不是人民币，就转换为人民币
								if($v->currency_code != 'CNY'){
									$new_over_size_fee += self::exchangeRate($date,$over_size_fee, $v->currency_code, 0, $channel_data->supplier_id);
								}
								continue;
							}
						}
						
						
						$item_fee = Helper_Formula::parse ( $v->formula, array (
							'girth' => '',
							'first_length' => '',
							'second_length' => '',
							'declaration_type' => $order->declaration_type,
							'net_weight' => @$netweight,
							'weight' => $total_weight,
							'country' => $order->consignee_country_code,
							'baf' => $rate,
							'zone' => $partition_code,
							'freight' => $tracking_fee2,
							'icount' => $package_quantity,
							'tax' => $channel->tax,
							'over_count' => $over_size_quantity,
							'special_count' => $special_packing_fee_count,
							'remote' => $remote_fee2,
							'cubic' => @$volumn
						) );
						//#83414
						if ($item_fee===false){
							//存在生效费用项无法计算
							QLog::log('formulaerror:'.$v->formula);
							$data['success'] = 'formulaerror';
							return $data;
						}
						if ($item_fee != 0) { // false 和 0 都不计算
							// 							if ($item_code->sub_code == 'logisticsExpressASP_EX0035') { //超尺寸
							// 								$fee_item_info [$item_code->sub_code] = array (
							// 									'quantity' => $over_size_quantity,
							// 									'fee' => round($over_size_fee, 2),
							// 									'rate' => $v_rate,
							// 									'currency_code' => $v->currency_code,
							// 									'btype_id' => $v->supplier_id
							// 								);
							// 							} else {
							if ($item_code->sub_code == 'logisticsExpressASP_EX0034') { //异型包装
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => $special_packing_fee_count,
									'fee' => round($item_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							} else {
								$fee_item_info [$item_code->sub_code] = array (
									'quantity' => '1',
									'fee' => round($item_fee, 2),
									'rate' => $v_rate,
									'currency_code' => $v->currency_code,
									'btype_id' => $v->supplier_id
								);
							}
							// 							}
							
							//计算费用不是人民币，就转换为人民币
							if($v->currency_code != 'CNY'){
								$item_fee = self::exchangeRate($date,$item_fee, $v->currency_code, 0, $channel_data->supplier_id);
							}
							$fee_amount = bcadd($item_fee,$fee_amount);
						}
					}
				}
			}
		}
		//判断渠道成本是否已经计算燃油
		if ($channel->fuel_surcharge_flag == '1') {
			$tracking_fee = bcmul($tracking_fee , bcadd(1 , $rate));
			$tracking_fee1 = bcmul($tracking_fee1 , bcadd(1 , $rate));
		}
		//偏远+燃油
		$remote_fee = bcmul($remote_fee , bcadd(1 , $rate));
		$remote_fee1 = bcmul($remote_fee1 , bcadd(1 , $rate));
		$tax_fee = 0;
		if (isset ( $fee_item_info ['service_fee'] )) {
			if($fee_item_info ['service_fee'] ['currency_code'] != 'CNY'){
				$service_fee = self::exchangeRate($date,$fee_item_info ['service_fee'] ['fee'], $fee_item_info ['service_fee'] ['currency_code'], 0, $channel_data->supplier_id);
			}else{
				$service_fee = $fee_item_info ['service_fee'] ['fee'];
			}
			$tax_fee = bcmul(bcadd(bcadd(bcsub($fee_amount , $service_fee) , $tracking_fee) , $remote_fee) , $channel->tax);
		} else {
			$tax_fee = bcmul(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $channel->tax);
		}
		$fee_item_info ['logisticsExpressASP_EX0001'] = array (
			'quantity' => '1',
			'fee' => round($tracking_fee1, 2),
			'rate' => $tracking_rate,
			'currency_code' => $price->currency_code
		);
		$fee_item_info ['logisticsExpressASP_EX0020'] = array (
			'quantity' => '1',
			'fee' => round($remote_fee1, 2),
			'rate' => $remote_rate,
			'currency_code' => $currency
		);
		$fee_item_info ['tax'] = array (
			'quantity' => '1',
			'fee' => round($tax_fee, 2)
		);
		if ($order->need_pick_up == '1' && strlen ( $order->sender_postal_code ) == '6') {
			$zipcode = Zipcode::find ( 'pick_company="青岛仓" and zip_code_low <= ? and zip_code_high >= ?', $order->sender_postal_code, $order->sender_postal_code )->getOne ();
			if (! $zipcode->isNewRecord ()) {
				$fee_item_info ['logisticsExpressASP_EX0036'] = array (
					'quantity' => '1',
					'fee' => '5'
				);
			}
		}
		//组合返回各项费用
		$data = array ();
		$data ['public_price'] = round(bcadd(bcadd(bcadd(bcadd($fee_amount , $tracking_fee) , $remote_fee) , $tax_fee),$new_over_size_fee),2); //总运费
		QLog::log('public_price'.$data ['public_price']);
		$data ['price_info'] = array (
			'fee_item' => $fee_item_info,
			'rate' => $rate, //燃油费率
			'account' => $price->account, //账号,实际是产品代码，product_code
			'total_weight' => $total_cost_weight, //计费重
			'weight_label' => $label_weight  //标签重
		);
		//整票货的计费重
		$data ['total_single_weight'] = $total_weight;
		$data ['total_out_volumn_weight'] = $total_volumn_weight;
		return $data;
	}
	/**
	 * @todo   退件-组织重量数组
	 * @author stt
	 * @since  July 31th 2020
	 */
	static function returnreturngetweightarr($order,$flag,$channel=null,$packages_arr=null) {
		//总预报重(标签重)
		$total_label_weight=0;
		
		//总计费重
		$total_cost_weight=0;
		
		//订单渠道,$flag=1是应收,$flag=2是应付
		if ($flag==1){
			$channel=Product::find("product_name=?",$order->service_code)->getOne();
		}else{
			if (is_null($channel)){
				$channel=ReturnChannel::find('channel_id=?',$order->channel_id)->getOne();
			}
		}
		$weight_arr = array();
		$is_packages_arr = 0;
		if ($flag==1){
			if (count($packages_arr)>0){
				$packages = $packages_arr;
				$is_packages_arr = 1;
			}else{
				$packages = $order->farpackages;
			}
		}else{
			$packages = $order->faroutpackages;
		}
		//单件计费重
		$total_weight='';
		//总预报重(标签重)
		$total_label_weight=0;
		$volumn_weight=0;
		$channel_flag = 0;
		$quantity_flag = 0;
		$is_jipao = 0;
		$total_volumn_weight = 0;
		$total_real_weight = 0;
		$total_quantity = 0;
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$quantity = $package->quantity;
				$total_quantity+=$quantity;
			}
		}
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$length = $package->length;
				$height = $package->height;
				$width = $package->width;
				$weight = $package->weight;
				$quantity = $package->quantity;
				//入库
			}elseif($flag==1){
				//保存入库包裹信息
				if ($is_packages_arr){
					$length = $package['length'];
					$height = $package['height'];
					$width = $package['width'];
					$weight = $package['weight'];
					$quantity = $package['quantity'];
				}else{
					$length = $package->length;
					$height = $package->height;
					$width = $package->width;
					$weight = $package->weight;
					$quantity = $package->quantity;
				}
				//预报
			}else{
				$length = floor($package->length);
				$height = floor($package->height);
				$width = floor($package->width);
				$weight = $package->weight;
				$quantity = $package->quantity;
			}
			
			$ch=ChannelCost::find('channel_id=?',$channel->channel_id)->getOne();
			//发件日
			if ($order->record_order_date){
				$date = $order->record_order_date;
			}else{
				$date = time();
			}
			//基础运费价格表
			$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$ch->channel_cost_id,$date,$date)->getOne();
			
			$ratio=$ch->ratio;
			//定义长宽高数组
			$verify_array=array($length,$width,$height);
			rsort($verify_array);
			//计算单件体积重
			if(($channel->type==4 && ceil($verify_array[0])<=60 )||$channel->type==5 ){
				$volumn_weight="0";
			}else {
				if ($ratio){
					$volumn_weight=(ceil($length)*ceil($width)*ceil($height))/$ratio;
				}else{
					$volumn_weight="0";
				}
			}
			if($order->packing_type=='PAK' && ($flag==2||$flag==3)){
				$total_weight=$weight;
				$label_weight = $total_weight;
				//PAK加上规则2：实重减3g
				if ($channel->forecast_type==2){
					$label_weight = $label_weight-0.003;
				}
				if ($total_weight < 0.5) {
					$total_weight = 0.5;
				}
			}else{
				if ($flag==2 || $flag==3){
					$label_weight = $weight;
					if ($channel->forecast_type==1){
						if ((floor ( $label_weight / 0.5 ) * 0.5 + 0.1) >= $label_weight) {
							$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
						}
					}elseif ($channel->forecast_type==2){
						$label_weight = $label_weight-0.003;
					}else{
						$label_weight = $weight;
					}
				}
				if ($flag == 3){
					$weight = $label_weight;
				}
				if($volumn_weight>$weight){
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $volumn_weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out  = ceil ( $volumn_weight / 0.5 ) * 0.5;
					}
					$is_jipao = 1;
				}else{
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out = ceil ( $weight / 0.5 ) * 0.5;
					}
				}
				
				$total_weight=$weight_cost_out;
			}
			//小包类
			if($channel->type==5){
				$total_weight='';
				$total_weight = $weight;
				$total_weight = sprintf("%.3f",$total_weight);
			}
			//#83852
			//单件最低计费重，仅针对一票多件的情况下生效
			if ($flag==2 && $channelcostppr && $total_quantity>1){
				if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$total_weight)){
					$total_weight = $channelcostppr->single_lowest_weight;
				}
			}
			@$total_label_weight += $label_weight*$quantity;
			@$total_real_weight += $weight*$quantity;
			@$total_volumn_weight += $volumn_weight*$quantity;
			@$total_cost_weight += $total_weight*$quantity;
			if ($flag==1){
				$arr[$key]['quantity'] = $quantity;
				$arr[$key]['length'] = $length;
				$arr[$key]['width'] = $width;
				$arr[$key]['height'] = $height;
				$arr[$key]['weight'] = $weight;
			}else{
				$arr[$key]['quantity_out'] = $quantity;
				$arr[$key]['length_out'] = $length;
				$arr[$key]['width_out'] = $width;
				$arr[$key]['height_out'] = $height;
				$arr[$key]['weight_out'] = $weight;
			}
			if ($order->channel_id || $flag==1){
				$arr[$key]['volumn_weight'] = $channel->type==5?sprintf("%.3f",$volumn_weight):sprintf("%.2f",$volumn_weight);
				$arr[$key]['label_weight'] = $channel->type==5?sprintf("%.3f",@$label_weight):sprintf("%.2f",@$label_weight);
				$arr[$key]['cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight):sprintf("%.2f",$total_weight);
				$arr[$key]['total_cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight*$quantity):sprintf("%.2f",$total_weight*$quantity);
				$arr[$key]['total_single_label_weight'] = ($channel->type==5)?sprintf("%.3f",@$label_weight*$quantity):sprintf("%.2f",@$label_weight*$quantity);
			}
			if ($quantity>1){
				$quantity_flag = 2;
			}
		}
		if ($channel_flag==2){
			$total_cost_weight = ceil ( $total_cost_weight / 0.5 ) * 0.5;
		}
		$weight_arr['package']=@$arr;
		
		$weight_arr['is_jipao']=$is_jipao;
		
		//总预报重(标签重)
		$weight_arr['total_label_weight']=($channel->type==5)?sprintf("%.3f",$total_label_weight):sprintf("%.2f",$total_label_weight);
		
		//总实重
		$weight_arr['total_real_weight']=sprintf("%.2f",@$total_real_weight);
		
		//总体积重
		$weight_arr['total_volumn_weight']=sprintf("%.2f",@$total_volumn_weight);
		
		//总计费重 在flag==3得时候是预报总计费重
		$weight_arr['total_cost_weight']=($channel->type==5)?sprintf("%.3f",$total_cost_weight):sprintf("%.2f",$total_cost_weight);
		
		if($channel->type==2 && (count($packages)>1 || $quantity_flag==2)){
			$weight_arr['total_cost_weight'] = ceil ( $weight_arr['total_cost_weight'] / 0.5 ) * 0.5;
		}elseif($channel->type==3 && (count($packages)>1 || $quantity_flag==2)){
			if ($total_real_weight>$total_volumn_weight){
				$weight_arr['total_cost_weight'] = ceil ( $total_real_weight / 0.5 ) * 0.5;
			}else{
				$weight_arr['total_cost_weight'] = ceil ( $total_volumn_weight / 0.5 ) * 0.5;
			}
		}
		return $weight_arr;
	}
	/**
	 * @todo   组织重量数组
	 * @author stt
	 * @since  July 31th 2020
	 */
	static function returngetweightarr($order,$flag,$channel=null,$packages_arr=null) {
		//总预报重(标签重)
		$total_label_weight=0;
		
		//总计费重
		$total_cost_weight=0;
		
		//订单渠道,$flag=1是应收,$flag=2是应付
		if ($flag==1){
			$channel=Product::find("product_name=?",$order->service_code)->getOne();
		}else{
			if (is_null($channel)){
				$channel=ReturnChannel::find('channel_id=?',$order->channel_id)->getOne();
			}
		}
		$weight_arr = array();
		$is_packages_arr = 0;
		if ($flag==1){
			if (count($packages_arr)>0){
				$packages = $packages_arr;
				$is_packages_arr = 1;
			}else{
				$packages = $order->farpackages;
			}
		}else{
			$packages = $order->faroutpackages;
		}
		//单件计费重
		$total_weight='';
		//总预报重(标签重)
		$total_label_weight=0;
		$volumn_weight=0;
		$channel_flag = 0;
		$quantity_flag = 0;
		$is_jipao = 0;
		$total_volumn_weight = 0;
		$total_real_weight = 0;
		$total_quantity = 0;
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$quantity = $package->quantity_out;
				$total_quantity+=$quantity;
			}
		}
		foreach ($packages as $key=>$package){
			//出库
			if ($flag==2){
				$length = $package->length_out;
				$height = $package->height_out;
				$width = $package->width_out;
				$weight = $package->weight_out;
				$quantity = $package->quantity_out;
				//入库
			}elseif($flag==1){
				//保存入库包裹信息
				if ($is_packages_arr){
					$length = $package['length'];
					$height = $package['height'];
					$width = $package['width'];
					$weight = $package['weight'];
					$quantity = $package['quantity'];
				}else{
					$length = $package->length;
					$height = $package->height;
					$width = $package->width;
					$weight = $package->weight;
					$quantity = $package->quantity;
				}
				//预报
			}else{
				$length = floor($package->length_out);
				$height = floor($package->height_out);
				$width = floor($package->width_out);
				$weight = $package->weight_out;
				$quantity = $package->quantity_out;
			}
			
			$ch=ChannelCost::find('channel_id=?',$channel->channel_id)->getOne();
			//发件日
			if ($order->record_order_date){
				$date = $order->record_order_date;
			}else{
				$date = time();
			}
			//基础运费价格表
			$channelcostppr= Channelcostppr::find('channel_cost_id =? and effective_time <=? and invalid_time>?',$ch->channel_cost_id,$date,$date)->getOne();
			
			$ratio=$ch->ratio;
			//定义长宽高数组
			$verify_array=array($length,$width,$height);
			rsort($verify_array);
			//计算单件体积重
			if(($channel->type==4 && ceil($verify_array[0])<=60 )||$channel->type==5 ){
				$volumn_weight="0";
			}else {
				if ($ratio){
					$volumn_weight=(ceil($length)*ceil($width)*ceil($height))/$ratio;
				}else{
					$volumn_weight="0";
				}
			}
			if($order->packing_type=='PAK' && ($flag==2||$flag==3)){
				$total_weight=$weight;
				$label_weight = $total_weight;
				//PAK加上规则2：实重减3g
				if ($channel->forecast_type==2){
					$label_weight = $label_weight-0.003;
				}
				if ($total_weight < 0.5) {
					$total_weight = 0.5;
				}
			}else{
				if ($flag==2 || $flag==3){
					$label_weight = $weight;
					if ($channel->forecast_type==1){
						if ((floor ( $label_weight / 0.5 ) * 0.5 + 0.1) >= $label_weight) {
							$label_weight = floor ( $label_weight / 0.5 ) * 0.5;
						}
					}elseif ($channel->forecast_type==2){
						$label_weight = $label_weight-0.003;
					}else{
						$label_weight = $weight;
					}
				}
				if ($flag == 3){
					$weight = $label_weight;
				}
				if($volumn_weight>$weight){
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $volumn_weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out  = ceil ( $volumn_weight / 0.5 ) * 0.5;
					}
					$is_jipao = 1;
				}else{
					if($channel->type==2 && (count($packages)>1||$quantity>1)){
						$weight_cost_out = $weight;
						$channel_flag = 2;
					}else{
						$weight_cost_out = ceil ( $weight / 0.5 ) * 0.5;
					}
				}
				
				$total_weight=$weight_cost_out;
			}
			//小包类
			if($channel->type==5){
				$total_weight='';
				$total_weight = $weight;
				$total_weight = sprintf("%.3f",$total_weight);
			}
			//#83852
			//单件最低计费重，仅针对一票多件的情况下生效
			if ($flag==2 && $channelcostppr && $total_quantity>1){
				if ($channelcostppr->single_lowest_weight&&($channelcostppr->single_lowest_weight>$total_weight)){
					$total_weight = $channelcostppr->single_lowest_weight;
				}
			}
			@$total_label_weight += $label_weight*$quantity;
			@$total_real_weight += $weight*$quantity;
			@$total_volumn_weight += $volumn_weight*$quantity;
			@$total_cost_weight += $total_weight*$quantity;
			if ($flag==1){
				$arr[$key]['quantity'] = $quantity;
				$arr[$key]['length'] = $length;
				$arr[$key]['width'] = $width;
				$arr[$key]['height'] = $height;
				$arr[$key]['weight'] = $weight;
			}else{
				$arr[$key]['quantity_out'] = $quantity;
				$arr[$key]['length_out'] = $length;
				$arr[$key]['width_out'] = $width;
				$arr[$key]['height_out'] = $height;
				$arr[$key]['weight_out'] = $weight;
			}
			if ($order->channel_id || $flag==1){
				$arr[$key]['volumn_weight'] = $channel->type==5?sprintf("%.3f",$volumn_weight):sprintf("%.2f",$volumn_weight);
				$arr[$key]['label_weight'] = $channel->type==5?sprintf("%.3f",@$label_weight):sprintf("%.2f",@$label_weight);
				$arr[$key]['cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight):sprintf("%.2f",$total_weight);
				$arr[$key]['total_cost_weight'] = ($channel->type==5)?sprintf("%.3f",$total_weight*$quantity):sprintf("%.2f",$total_weight*$quantity);
				$arr[$key]['total_single_label_weight'] = ($channel->type==5)?sprintf("%.3f",@$label_weight*$quantity):sprintf("%.2f",@$label_weight*$quantity);
			}
			if ($quantity>1){
				$quantity_flag = 2;
			}
		}
		if ($channel_flag==2){
			$total_cost_weight = ceil ( $total_cost_weight / 0.5 ) * 0.5;
		}
		$weight_arr['package']=@$arr;
		
		$weight_arr['is_jipao']=$is_jipao;
		
		//总预报重(标签重)
		$weight_arr['total_label_weight']=($channel->type==5)?sprintf("%.3f",$total_label_weight):sprintf("%.2f",$total_label_weight);
		
		//总实重
		$weight_arr['total_real_weight']=sprintf("%.2f",@$total_real_weight);
		
		//总体积重
		$weight_arr['total_volumn_weight']=sprintf("%.2f",@$total_volumn_weight);
		
		//总计费重 在flag==3得时候是预报总计费重
		$weight_arr['total_cost_weight']=($channel->type==5)?sprintf("%.3f",$total_cost_weight):sprintf("%.2f",$total_cost_weight);
		
		if($channel->type==2 && (count($packages)>1 || $quantity_flag==2)){
			$weight_arr['total_cost_weight'] = ceil ( $weight_arr['total_cost_weight'] / 0.5 ) * 0.5;
		}elseif($channel->type==3 && (count($packages)>1 || $quantity_flag==2)){
			if ($total_real_weight>$total_volumn_weight){
				$weight_arr['total_cost_weight'] = ceil ( $total_real_weight / 0.5 ) * 0.5;
			}else{
				$weight_arr['total_cost_weight'] = ceil ( $total_volumn_weight / 0.5 ) * 0.5;
			}
		}
		return $weight_arr;
	}
}
class QuoteException extends QException {}
