<?php
/**
 * @todo 传入订单返回币种是否存在
 * @package helper
 * @author 吴开龙
 * @since 2020-6-10 14:18:51
 * @param $id 订单id
 * @return boolean
 * @link #
 */
class Helper_Currency {
	/**
	 * @todo 传入订单返回币种是否存在
	 * @package helper
	 * @author 吴开龙
	 * @since 2020-6-10 14:18:51
	 * @param $order 订单 $data 时间
	 * @return boolean
	 * @link #
	 */
	static function isCurreny($order, $date = '') {
		if ($date == '') {
			$date = time ();
		}
		//产品表
		$product = Product::find('product_name=?',$order->service_code)->getOne();
		//应收公式表
		$channel_formula = Receivableformula::find('product_id=?',$product->product_id)->getAll();
		foreach ($channel_formula as $channel){
			$currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$channel->currency_code,$date,$date)->getOne();
			if($currency->isNewRecord()){
				return false;
			}
		}
		//渠道成本公示表
		if($order->channel_id){
			$ch_cost = ChannelCost::find('product_id=? and channel_id=?',$product->product_id,$order->channel_id)->getOne();		
			$cost_type = ChannelCosttype::find('channel_cost_id=? and package_type=?',$ch_cost->channel_cost_id,$order->packing_type)->getOne();		
			$formula = ChannelCostformula::find('type_id=?',$cost_type->type_id)->getAll();
			foreach ($formula as $f){
				$currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$f->currency_code,$date,$date)->getOne();
				if($currency->isNewRecord()){
					return false;
				}
			}
		}
		//获取产品中偏派-价格-分区
		$product_p_p_r = Productppr::find ( 'product_id=? and effective_time <=? and invalid_time>=?', $product->product_id, $date, $date )->getOne ();
		$postcode = $order->consignee_postal_code;

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
		$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $postcode, $postcode )->getOne ();
		if ($remote_postcode->isNewRecord ()) {
			$remote_postcode = Remote::find ( 'remote_manage_id = ? and country_code_two=? and start_postal_code<=? and end_postal_code>=? and ifnull(start_postal_code,"") != "" and ifnull(end_postal_code,"") != ""', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $post_code, $post_code )->getOne ();
		}
		$remote_city = Remote::find ( 'remote_manage_id = ? and country_code_two=? and (remote_city=? || remote_city=?) and ifnull(remote_city,"") != "" ', $product_p_p_r->remote_manage_id, $order->consignee_country_code, $order->consignee_city, strtolower ( str_replace ( '-', '', str_replace ( ' ', '', $order->consignee_city ) ) ) )->getOne ();
		if (! $remote_city->isNewRecord ()) {
			$currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_city->currency_code,$date,$date)->getOne();
			if($currency->isNewRecord()){
				return false;
			}
		}else{
			$currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$remote_postcode->currency_code,$date,$date)->getOne();
			if($currency->isNewRecord()){
				return false;
			}
		}
		
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
		$price = Price::find ( 'price_manage_id=? and partition_code=? and boxing_type=? and start_weight<? and end_weight>=?', $product_p_p_r->price_manage_id, $partition_code, $packing, $order->weight_income_in, $order->weight_income_in )->getOne ();
		if (!$price->isNewRecord ()) {
			$currency = CodeCurrency::find('code=? and start_date <= ? and end_date >= ?',$price->currency_code,$date,$date)->getOne();
			if($currency->isNewRecord()){
				return false;
			}
		}
		
		return true;
	}
}

