<?php

/**
 * 末端渠道轨迹跟踪信息
 * @author firzen
 *
 */
class Route extends QDB_ActiveRecord_Abstract {
	
	/**
	 * 返回对象的定义
	 *
	 * @static
	 *
	 * @return array
	 */
	static function __define() {
		return array (
			
			// 用什么数据表保存对象
			'table_name' => 'tb_routes',
			
			// 指定数据表记录字段与对象属性之间的映射关系
			// 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
			'props' => array (
			),
			'validations' => array (),
			'create_autofill' => array (
				'create_time'=>self::AUTOFILL_TIMESTAMP
			),
			'update_autofill' => array (
			),
		);
	}
	
	/**
	 * 开启一个查询，查找符合条件的对象或对象集合
	 *
	 * @static
	 *
	 * @return QDB_Select
	 */
	static function find() {
		$args = func_get_args ();
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ )->findByArgs ( $args );
	}
	
	/**
	 * 返回当前 ActiveRecord 类的元数据对象
	 *
	 * @static
	 *
	 * @return QDB_ActiveRecord_Meta
	 */
	static function meta() {
		return QDB_ActiveRecord_Meta::instance ( __CLASS__ );
	}
	
	/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */
	/**
	 * 去重保存，主要估计时间从签收倒序传入
	 * @param array $args
	 * @return void|QDB_ActiveRecord_Abstract
	 */
	static function checkAndSave($args){
		$md5=md5($args['tracking_no'].$args['time'].$args['location'].$args['description']);
		if (self::find('md5=?',$md5)->getCount()){
			return;
		}
		$obj=new self($args);
		$obj->md5=$md5;
		return $obj->save();
		//@todo 监测签收情况，同时修改订单状态
		
	}
	/**
	 * 国家匹配
	 * @param string $countryNameOrCodeWordTwo
	 * @return boolean|string
	 */
	static function countryMatch($locaiton){
	    if(strpos($locaiton, ',Republic of')!==false){
	        $locaiton=str_ireplace(",Republic of",'',$locaiton);
	    }
	    if(strpos($locaiton, ', Republic of')!==false){
	        $locaiton=str_ireplace(", Republic of",'',$locaiton);
	    }
		$arr=explode(',',$locaiton);
		$country=trim(array_pop($arr));
		$city=trim(array_shift($arr));
		
		$country=str_ireplace("Republic of",'',$country);
		if (count($arr)){
			$arr[0]=trim($arr[0]);
		}
		if(!$country){
		    return false;
		}
		if (strtoupper($country)==strtoupper('United States')){
			$country='US';
		}elseif (!empty($arr[0]) && strtoupper($arr[0])==strtoupper('Korea')){
			$country='KR';
		}elseif (!empty($arr[0]) && strtoupper($arr[0])==strtoupper('Venezuela')){
			$country='VE';
		}elseif ($country=='HONG KONG' || $country=='HKG'){
			$country='CN';
		}elseif (strtoupper($country)==strtoupper('Viet Nam')){
			$country='VN';
		}elseif (strtoupper($country)==strtoupper('Serbia')){
			$country='RS';
		}
		if (strlen($country)==2){
			$c=Country::find('code_word_two=?',$country)->getOne();			
		} elseif (strlen($country)==3){
		    $c=Country::find('code_word_three=?',$country)->getOne();
		} else {
		    $c=Country::find('english_name = ? or english_name2 = ?',$country,$country)->getOne();
		    if($c->isNewRecord()){
			     $c=Country::find('english_name like ? or english_name2 like ?','%'.$country.'%','%'.$country.'%')->getOne();
		    	 if($c->isNewRecord()){
		    	 	$c=Country::find('chinese_name =? ',$country)->getOne();
		    	 }
		    }
		}
		if ($c->isNewRecord()){
			return false;
		}
		return $c->code_word_two;
	}
	 function guessTimeZone($location=null){
		if (is_null($location)){
			$location=$this->location;
		}
		$arr=explode(',',$location);
		$city=trim(array_shift($arr));//将数组开头的单元移出数组		
		$country=self::countryMatch($location);
		
		try {
			$r= CityTimezone::match($country,$city);
		}catch (Exception $ex){
			return $ex->getMessage();
		}
		return $r;
	}
	/**
	 * 生成阿里轨迹
	 * @param int $lastTime UTC+8
	 * @param Order $order
	 */
	function generateTrace($lastTime,$order,$prevLocation=null){
	    static $rules;
		if (is_null($rules)){
			//先是否优先匹配，然后排序数字小的优先，最后是按id增大的顺序#83180
			$rules=RouteMatchRule::find()->order('is_priority asc,sort asc, id asc')->getAll();
			$rules=Helper_Array::groupBy($rules,'network_code');
		}
		$location=strlen(trim($this->location))?trim($this->location):$prevLocation;
		//@todo 自动生成阿里轨迹记录
		if(!$this->time_zone){
    		$tz=$this->guessTimeZone($location);
    		if (!is_int($tz)){
    			$tz=-19;
    		}
		}else {
		    $tz=$this->time_zone;
		}
		
		$utc8=$this->time+(8-$tz)*3600;
		// 地址检查
		if (strlen(trim($location))==0){
			QLog::log('No country');
			return false;
		}
		$locs=explode(',',$location);
		if (empty($locs)){
			QLog::log('No location');
			// 没有地址
			return false;
		}
		$countryName=trim(array_pop($locs));//删除返回数组中的最后一个元素
		if (strtoupper($countryName)=='CHINA' || strtoupper($countryName)=='CN'){
			// 中国段不匹配
			return false;
		}
		$country=self::countryMatch($location);
		
		$qty=Orderpackage::find('order_id=?',$order->order_id)->getSum('quantity');
		
		// auto_confirm
		$autoConfirm=(Tracking::find('order_id =? and confirm_flag=0',$order->order_id)->getCount() == 0 )? 1:0;
		if ($utc8 >= $lastTime ){
		    $matched=false;
			$rules_channel=$rules['UPS'];
			if($this->network_code=='DHL'){
				$rules_channel=isset($rules['DHL'])?$rules['DHL']:array();
			}elseif ($this->network_code=='US-FY'){
			    $rules_channel=isset($rules['US-FY'])?$rules['US-FY']:array();
			}elseif ($this->network_code=='DHLE'){
			    $rules_channel=isset($rules['DHLE'])?$rules['DHLE']:array();
			}elseif ($this->network_code=='YWML'){
			    $rules_channel=isset($rules['YWML'])?$rules['YWML']:array();
			}elseif($this->network_code=='USPS'){
				$rules_channel=isset($rules['USPS'])?$rules['USPS']:array();
			}
			$i=0;
			foreach ($rules_channel as $ruleObj){
			    if($i>0){
			        break;
			    }
				$condition=$ruleObj->keyword;
				$code=$ruleObj->ali_code;
				
				$condition=str_replace('\\','\\\\',$condition);
				$condition=str_replace('/','\\/',$condition);
				
				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
				
				$condition=str_replace('*','.*?',$condition);
				$this->description=str_replace(PHP_EOL, '', $this->description);
				if (preg_match('/^'.$condition.'$/i',$this->description)){
					$matched=true;
					
					QLog::log($condition);
					
					if (!$ruleObj->auto){
						//匹配到异常
						$autoConfirm=0;
					}
					if($code=='S_DELIVERY_SIGNED' && $country<>$order->consignee_country_code){
						$autoConfirm=0;
					}
					if($tz==-19){
					    $autoConfirm=2;
					}
					$tr=new Tracking();
					$tr->changeProps(array(
						'order_id'=>$order->order_id,
						'customer_id'=>$order->customer_id,
						'far_no'=>$order->far_no,
						'tracking_code'=>$code,
						'timezone'=>$tz,
						'confirm_flag'=>$autoConfirm,
						'trace_time'=>$this->time,
						'location'=>$locs[0],
						'quantity'=>$qty,
						'trace_desc_cn'=>strlen($ruleObj->cn_desc)? $ruleObj->cn_desc:Tracking::$trace_code_cn[$code],
						'trace_desc_en'=>self::clearDescription($this->description),
						'route_id'=>$this->id,
					    'flag'=>$autoConfirm=='2'?'1':'0'
					));
					$tr->save();
					if($order->order_status=='7'){
					    $order->order_status='8';
					    $order->save();
					}
					// 如果是签收，那么还要生成签收事件
					if($order->customer->customs_code=='ALPL'){
						$event_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
					}else{
						$event_code = 'DELIVERY';
					}
					if ($code=='S_DELIVERY_SIGNED' && Event::find('order_id =? and event_code =?',$order->order_id, $event_code)->getCount() ==0){
						$evt=new Event();
						$evt->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'event_code'=>$event_code,
							'event_time'=>$this->time,
							'event_location'=>trim($locs[0]),
// 							'location'=>trim($locs[0]),
							'timezone'=>$tz,
							'confirm_flag'=>0,
						));
						$evt->save();
						//保存签收时间
						if($order->customer->customs_code!='FARA00001'){
							$order->delivery_time=$this->time;
							$order->save();
						}
					}
					// @todo 把订单改成签收状态
					$i++;
				}
			}
			if ($matched==false && ($order->service_code=='Express_Standard_Global' || $order->service_code=='US-FY' || $order->service_code=='EUUS-FY')){
				QLog::log('无匹配规则');
				
				// mismatch
				$code='F_DELIVERY_5053';
				$tr=new Tracking();
				$tr->changeProps(array(
					'order_id'=>$order->order_id,
					'customer_id'=>$order->customer_id,
					'far_no'=>$order->far_no,
					'tracking_code'=>$code,
					'timezone'=>$tz,
					'confirm_flag'=>2,
					'trace_time'=>$this->time,
					'location'=>@$locs[0],
					'quantity'=>$qty,
					'trace_desc_cn'=>'快件滞留等待中',
					'trace_desc_en'=>self::clearDescription($this->description),
					'route_id'=>$this->id,
				    'flag'=>'2'
				));
				$tr->save();
			}
		}else {
			QLog::log('Time-');
		}
	}
	/**
	 * 生成EMS阿里轨迹
	 * @param int $lastTime UTC+8
	 * @param Order $order
	 */
	function emsgenerateTrace($lastTime,$order){
		static $rules;
		if (is_null($rules)){
			//先是否优先匹配，然后排序数字小的优先，最后是按id增大的顺序#83180
			$rules=RouteMatchRule::find()->order('is_priority asc,sort asc, id asc')->getAll();
			$rules=Helper_Array::groupBy($rules,'network_code');
		}
		$location=trim($this->location);
		//@todo 自动生成阿里轨迹记录
		if(!$this->time_zone){
			$tz=$this->guessTimeZone($location);
			if (!is_int($tz)){
				$tz=8;
			}
		}else {
			$tz=$this->time_zone;
		}
		
		$utc8=$this->time+(8-$tz)*3600;
		$country=self::countryMatch($location);
		
		$qty=Orderpackage::find('order_id=?',$order->order_id)->getSum('quantity');
		// auto_confirm
		$autoConfirm=(Tracking::find('order_id =? and confirm_flag=0',$order->order_id)->getCount() == 0 )? 1:0;
		if ($utc8 >= $lastTime ){
			$matched=false;
			$rules_channel=isset($rules['EMS'])?$rules['EMS']:array();
			$i=0;
			foreach ($rules_channel as $ruleObj){
				if($i>0){
					break;
				}
				$condition=$ruleObj->keyword;
				$code=$ruleObj->ali_code;
				
				$condition=str_replace('\\','\\\\',$condition);
				$condition=str_replace('/','\\/',$condition);
				
				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
				
				$condition=str_replace('*','.*?',$condition);
				QLog::log($condition);
				$this->description=str_replace(PHP_EOL, '', $this->description);
				if (preg_match('/^'.$condition.'$/i',$this->description)){
					$matched=true;
					
					
					
					if (!$ruleObj->auto){
						//匹配到异常
						$autoConfirm=0;
					}
					/* if($code=='S_DELIVERY_SIGNED' && $country<>$order->consignee_country_code){
						$autoConfirm=0;
					} 
					if($tz==-19){
						$autoConfirm=0;
					}*/
					$tr=new Tracking();
					$tr->changeProps(array(
						'order_id'=>$order->order_id,
						'customer_id'=>$order->customer_id,
						'far_no'=>$order->far_no,
						'tracking_code'=>$code,
						'timezone'=>$tz,
						'confirm_flag'=>$autoConfirm,
						'trace_time'=>$this->time,
						'location'=>empty ( trim ( $location ) ) ? 'Other':$location,
						'quantity'=>$qty,
						'trace_desc_cn'=>strlen($ruleObj->cn_desc)? $ruleObj->cn_desc:Tracking::$trace_code_cn[$code],
						'trace_desc_en'=>self::clearDescription($this->description),
						'route_id'=>$this->id,
						'flag'=>$autoConfirm=='2'?'1':'0'
					));
					$tr->save();
					if($order->order_status=='7'){
						$order->order_status='8';
						$order->save();
					}
					if($order->customer->customs_code=='ALPL'){
						$event_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
					}else{
						$event_code = 'DELIVERY';
					}
					// 如果是签收，那么还要生成签收事件
					if ($code=='S_DELIVERY_SIGNED' && Event::find('order_id =? and event_code =?',$order->order_id, $event_code)->getCount() ==0){
						$evt=new Event();
						$evt->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'event_code'=>$event_code,
							'event_time'=>$this->time,
							'event_location'=>empty ( trim ( $location ) ) ? 'Other':$location,
							'timezone'=>$tz,
							'confirm_flag'=>0,
						));
						$evt->save();
					}
					$i++;
				}
			}
		}else {
			QLog::log('Time-');
		}
	}
	static function clearDescription($str){
		return str_ireplace(array('UPS','DHL','FEDEX','ARMEX','TNT'),'Carrier',$str);
	}
	
	/**
	 * 根据fedex轨迹生成阿里轨迹
	 */
	function generateFedexTrace($order,$city,$country){
// 		if($country =='CN'){
// 			return false;
// 		}
		static $fedexrules;
		if (is_null($fedexrules)){
			//先是否优先匹配，然后排序数字小的优先，最后是按id增大的顺序#83180
			$fedexrules=RouteMatchRule::find()->order('is_priority asc,sort asc, id asc')->getAll();
			$fedexrules=Helper_Array::groupBy($fedexrules,'network_code');
		}
		$qty=Orderpackage::find('order_id=?',$order->order_id)->getSum('quantity');
		// auto_confirm
		$autoConfirm=(Tracking::find('order_id =? and confirm_flag=0',$order->order_id)->getCount() == 0 )? 1:0;
		$tracking=Tracking::find('order_id =? and tracking_code<>"F_DELIVERY_5044" and yflag<>"1"',$order->order_id)->order('trace_time desc')->getOne();
		if ($tracking->isNewRecord() || $this->time >= $tracking->trace_time ){
			$matched=false;
			$rules_channel=isset($fedexrules['FEDEX'])?$fedexrules['FEDEX']:array();
			foreach ($rules_channel as $ruleObj){
			    if($matched==true){
			        break;
			    }
				$condition=$ruleObj->keyword;
				$code=$ruleObj->ali_code;
	
				$condition=str_replace('\\','\\\\',$condition);
				$condition=str_replace('/','\\/',$condition);
	
				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
	
				$condition=str_replace('*','.*?',$condition);
				if (preg_match('/^'.$condition.'$/i',$this->description)){
					$matched=true;
					if (!$ruleObj->auto){
						//匹配到异常
						$autoConfirm=0;
					}
					if($code=='S_DELIVERY_SIGNED' && $country<>$order->consignee_country_code){
						$autoConfirm=0;
					}
					$tr=new Tracking();
					$tr->changeProps(array(
						'order_id'=>$order->order_id,
						'customer_id'=>$order->customer_id,
						'far_no'=>$order->far_no,
						'tracking_code'=>$code,
						'timezone'=>$this->time_zone,
						'confirm_flag'=>$autoConfirm,
						'trace_time'=>$this->time,
						'location'=>$city,
						'quantity'=>$qty,
						'trace_desc_cn'=>strlen($ruleObj->cn_desc)? $ruleObj->cn_desc:Tracking::$trace_code_cn[$code],
						'trace_desc_en'=>self::clearDescription($this->description),
						'route_id'=>$this->id
					));
					$tr->save();
					if($order->order_status=='7'){
					    $order->order_status='8';
					    $order->save();
					}
					if($order->customer->customs_code=='ALPL'){
						$event_code = 'LAST_MILE_GTMS_SIGNED_CALLBACK';
					}else{
						$event_code = 'DELIVERY';
					}
					// 如果是签收，那么还要生成签收事件
					if ($code=='S_DELIVERY_SIGNED' && Event::find('order_id =? and event_code =?',$order->order_id, $event_code)->getCount() ==0){
						$evt=new Event();
						$evt->changeProps(array(
							'order_id'=>$order->order_id,
							'customer_id'=>$order->customer_id,
							'event_code'=>$event_code,
							'event_time'=>$this->time,
							'event_location'=>$city,
							'timezone'=>$this->time_zone,
							'confirm_flag'=>0,
						));
						$evt->save();
					}
				}
			}
		}else {
			QLog::log('Time-');
		}
	}
	
	/**
	 * @todo   匹配轨迹匹配规则，确定签收时间
	 * @author stt
	 * @since  2020年12月18日10:38:10
	 * @param
	 * @return string
	 * @link   #84564
	 */
	function matchingrules(){
		static $rules;
		if (is_null($rules)){
			//先是否优先匹配，然后排序数字小的优先，最后是按id增大的顺序#83180
			$rules=RouteMatchRule::find()->order('is_priority asc,sort asc, id asc')->getAll();
			$rules=Helper_Array::groupBy($rules,'network_code');
		}
		
		$matched=false;
		$rules_channel=$rules['UPS'];
		if($this->network_code=='DHL'){
			$rules_channel=isset($rules['DHL'])?$rules['DHL']:array();
		}elseif ($this->network_code=='US-FY'){
			$rules_channel=isset($rules['US-FY'])?$rules['US-FY']:array();
		}elseif ($this->network_code=='DHLE'){
			$rules_channel=isset($rules['DHLE'])?$rules['DHLE']:array();
		}elseif ($this->network_code=='YWML'){
			$rules_channel=isset($rules['YWML'])?$rules['YWML']:array();
		}elseif($this->network_code=='USPS'){
			$rules_channel=isset($rules['USPS'])?$rules['USPS']:array();
		}elseif ($this->network_code=='FEDEX'){
			$rules_channel=isset($rules['FEDEX'])?$rules['FEDEX']:array();
		}elseif ($this->network_code=='EMS'){
			$rules_channel=isset($rules['EMS'])?$rules['EMS']:array();
		}
		
		$i=0;
		foreach ($rules_channel as $ruleObj){
			if($i>0){
				break;
			}
			$condition=$ruleObj->keyword;
			$code=$ruleObj->ali_code;
			
			$condition=str_replace('\\','\\\\',$condition);
			$condition=str_replace('/','\\/',$condition);
			
			$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
			
			$condition=str_replace('*','.*?',$condition);
			$this->description=str_replace(PHP_EOL, '', $this->description);
			if (preg_match('/^'.$condition.'$/i',$this->description)){
				if ($code=='S_DELIVERY_SIGNED'){
					$this->is_delivery=1;
					$this->save();
				}
			}
			
		}
		
		
	}
	
}