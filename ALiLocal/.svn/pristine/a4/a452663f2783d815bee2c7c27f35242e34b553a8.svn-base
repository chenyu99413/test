<?php
/**
 * @todo   菜鸟物流助手
 * @author 许杰晔
 * @since  2020-8-17 10:32:54
 * @param
 * @return string
 * @link   #81740       
 */
class Helper_CaiNiao {
	//TEST
	CONST URL = 'http://gw.api.alibaba.com/openapi/';
	CONST APIINFO = 'param2/20/ali.intl.shipping/data.server/';
	CONST APISECRET = 'DCfW3LzL4Bl';
	CONST APIKEY = '644351';
	
	/**
	 * @todo   菜鸟发送状态
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return string
	 * @link   #81740
	 */
	function notifyCaiNiao($cainiao){
		
		switch ($cainiao->cainiao_code){
			case 'ARRIVE':
				$url=self::sendArrival($cainiao);				
				break;
			case 'WAREHOUSE_INBOUND':	
				$url=self::sendin($cainiao);
				break;
			case 'FEE':
				$url=self::sendfee($cainiao);
				break;
			case 'WAREHOUSE_OUTBOUND':
				$url=self::sendOut($cainiao);
				break;
		}
		return $url;
	}
	/**
	 * @todo   推送抵达信息
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param  object $order  
	 * @return string|boolean
	 * @link   #81740
	 */
	static function sendArrival($cainiao) {
		$order=$cainiao->order;
		$arr=array(
			'esc'=>array(
				'head'=>array(
					'messageId'=>time(),
					'messageTime'=>date('Y-m-d H:i:s'),
					'messageWay'=>'request',
					'sender'=>self::APIKEY,
					'version'=>'1.0',
					'correlationId'=>'',
					'serviceName'=>'arriveWarehouse',
				),
				'body'=>array(
					'orderId'=>$order->ali_order_no,
					'arriveInfoList'=>array(
						'arriveInfo'=>array(
							'logisticsNo'=>$order->reference_no,
							'status'=>'RECEIPT',
							'arriveTime'=>date('Y-m-d H:i:s',$cainiao->cainiao_time),
							'note'=>'备注',
						),
					),
				)
			),
		);
		
		$xml = Helper_xml::simpleArr2xml ($arr,1,false);
		//echo $xml;exit;
		$url=self::sign($xml,'arriveWarehouse');
		return $url;
/* 		$res=Helper_Curl::get($url);
		echo $res;exit; */
	}
	
	/**
	 * @todo   推送入库信息
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param  object $order
	 * @return string|boolean
	 * @link   #81740
	 */
	static function sendin($cainiao) {
		$order=$cainiao->order;
		$product=Product::find('product_name=?',$order->service_code)->getOne();
		$goods=array();
		$goodspackage=array();
		$totalquantity=0;
		$volume=0;
		$ratio=$product->ratio>0?$product->ratio:5000;

		foreach ($order->product as $g){
			$goods[]=array(
				'nameChinese'=>$g->product_name,
				'nameEnglish'=>$g->product_name_en,
				'hscode'=>$g->hs_code,
				'description'=>$g->material_use,
				'productQuantity'=>$g->product_quantity,
			);
		}
		foreach ($order->farpackages as $f){
			$goodspackage[]=array(
				'length'=>$f->length,
				'width'=>$f->width,
				'height'=>$f->height,
				'grossWeight'=>$f->weight,
				'packageType'=>$order->packing_type,
				'quantity'=>$f->quantity,
				'hasBattery'=>$order->has_battery==1?'Y':'N',
				'opCode'=>1
			);
			$totalquantity +=$f->quantity;
			$volume +=($f->length*$f->width*$f->height)/$ratio;
		}
		
		$arr=array(
			'esc'=>array(
				'head'=>array(
					'messageId'=>time(),
					'messageTime'=>date('Y-m-d H:i:s'),
					'messageWay'=>'request',
					'sender'=>self::APIKEY,
					'version'=>'1.0',
					'serviceName'=>'enterWarehouse',
				),
				'body'=>array(
					'orderId'=>$order->ali_order_no,
					'receiptStatus'=>'RECEIPT',
					'time'=>date('Y-m-d H:i:s',$cainiao->cainiao_time),
					'note'=>'',
					'goodsSummary'=>array(
						'totalPackageQuantity'=>$totalquantity,
						'packageType'=>$order->packing_type,
						'totalVolume'=>$volume,
						'totalGrossWeight'=>$order->weight_income_in,
						'totalDeclaredValue'=>$order->total_amount,
						'totalDeclaredCurrency'=>$order->currency_code,
					),
					'goodsList'=>array(
						'goodsDeclaration'=>$goods,
					),
					'packageList'=>array(
						'goodsPackage'=>$goodspackage
					)
				)
			),
		);
		
		
		
		$xml = Helper_xml::simpleArr2xml ($arr,1,false);
		//echo $xml;exit;
		$url=self::sign($xml,'enterWarehouse');
		return $url;
/* 		$res=Helper_Curl::get($url);
		echo $res;exit; */
	}
	/**
	 * @todo   推送出库信息
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param  object $order
	 * @return string|boolean
	 * @link   #81740
	 */
	static function sendOut($cainiao) {
		$order=$cainiao->order;
		$product=Product::find('product_name=?',$order->service_code)->getOne();
		$goodspackage=array();
		$totalquantity=0;
		$volume=0;
		$ratio=$product->ratio>0?$product->ratio:5000;
		
		foreach ($order->farpackages as $f){
			$goodspackage[]=array(
				'length'=>$f->length,
				'width'=>$f->width,
				'height'=>$f->height,
				'grossWeight'=>$f->weight,
				'packageType'=>$order->packing_type,
				'quantity'=>$f->quantity,
			);
			$totalquantity +=$f->quantity;
			$volume +=($f->length*$f->width*$f->height)/$ratio;
		}
		
		$arr=array(
			'esc'=>array(
				'head'=>array(
					'messageId'=>time(),
					'messageTime'=>date('Y-m-d H:i:s'),
					'messageWay'=>'request',
					'sender'=>self::APIKEY,
					'version'=>'1.0',
					'serviceName'=>'outOfWarehouse',
				),
				'body'=>array(
					'orderId'=>$order->ali_order_no,
					'type'=>'NORMAL',
					'logisticsNo'=>$order->tracking_no,
					'logisticsCompanyService'=>$order->service_code,
					'time'=>date('Y-m-d H:i:s',$cainiao->cainiao_time),
					'note'=>'',
					'goodsSummary'=>array(
						'totalPackageQuantity'=>$totalquantity,
						'packageType'=>$order->packing_type,
						'totalVolume'=>$volume,
						'totalGrossWeight'=>$order->weight_income_in,
						'totalDeclaredValue'=>$order->total_amount,
						'totalDeclaredCurrency'=>$order->currency_code,
					),
					'packageList'=>array(
						'goodsPackage'=>$goodspackage
					)
				)
			),
		);
		
		
		
		$xml = Helper_xml::simpleArr2xml ($arr,1,false);
		//echo $xml;exit;
		$url=self::sign($xml,'outOfWarehouse');
		return $url;
/* 		$res=Helper_Curl::get($url);
		echo $res;exit; */
	}
	
	/**
	 * @todo   推送费用信息
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param  object $order
	 * @return string|boolean
	 * @link   #81740
	 */
	static function sendfee($cainiao) {
		$order=$cainiao->order;
		$chargedetail=array();
		$fees=Fee::find('order_id=? and fee_type=1',$order->order_id)->getAll();
		foreach ($fees as $fee){
			$fee_code=FeeItem::find('sub_code=?',$fee->fee_item_code)->getOne();
			$chargedetail[]=array(
				'chargeItem'=>$fee->fee_item_code,
				'unitPrice'=>$fee->amount,
				'unit'=>$fee_code->fee_unit,
				'quantity'=>$fee->quantity,
				'amount'=>round($fee->amount*$fee->quantity,2),
				'currency'=>$fee->currency,
				'payer'=>'SUPPLIER',
				'note'=>''
			);
		}
		$arr=array(
			'esc'=>array(
				'head'=>array(
					'messageId'=>time(),
					'messageTime'=>date('Y-m-d H:i:s'),
					'messageWay'=>'request',
					'sender'=>self::APIKEY,
					'version'=>'1.0',
					'serviceName'=>'serviceCharge',
				),
				'body'=>array(
					'orderId'=>$order->ali_order_no,
					'chargeDetailList'=>array(
						'chargeDetail'=>$chargedetail
					)
				)
			),
		);
		
		
		
		$xml = Helper_xml::simpleArr2xml ($arr,1,false);
		//echo $xml;exit;
		$url=self::sign($xml,'serviceCharge');
		//echo $url;exit;
		return $url;
/* 		$res=Helper_Curl::get($url);
		echo $res;exit; */
	}
	/**
	 * @todo   签名
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param  string xml
	 * @return string|boolean
	 * @link   #81740
	 */
	static function sign($xml,$service){
		//$appKey='644351';
		//$url = 'https://gw.api.alibaba.com/openapi/';//1688开放平台使用gw.open.1688.com域名

		$apiinfo = self::APIINFO. self::APIKEY;//此处请用具体api进行替换
		
		
		//配置参数，请用apiInfo对应的api参数进行替换
		$code_arr = array(
			'service' => $service,
			'data_body' => $xml
		);
		$aliparams = array();
		foreach ($code_arr as $key => $val) {
			$aliparams[] = $key . $val;
		}
		sort($aliparams);
		$sign_str = join('', $aliparams);
		$sign_str = $apiinfo . $sign_str;
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, self::APISECRET, true)));
		$code_arr['_aop_signature']=$code_sign;
		$str=http_build_query($code_arr);
		$url=self::URL.$apiinfo.'?'.$str;
		return $url;
	}
}