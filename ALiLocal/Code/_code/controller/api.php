<?php
class Controller_Api extends Controller_Abstract{
	static $FARCODES=array(
		//'UNKNOWN'=>'万能自定义',
		'WAREHOUSE_INBOUND'=>'仓库收到包裹',
		'CHECK_WEIGHT'=>'仓内货物查验',
		'CONFIRM'=>'待发件方确认',
		'PALLETIZE'=>'开始准备发货',
		'WAREHOUSE_OUTBOUND'=>'已出库待提取',
		'CARRIER_PICKUP'=>'承运商已取件',
		'DELIVERY'=>'快件已签收',
		'CARRIER_INTRANSMIT'=>'货物中转交航',
		'LOAD'=>'货物已装柜，待开船',
		'SET_SAIL'=>'货物已开船，待到港',
		'ARRIVAL_PORT'=>'货物已到港，待清关',
		'E_CLEARANCE_FLIGHT'=>'出口清关交航中',
		'S_CLEARANCE_START'=>'目的地清关开始',
		'S_CLEARANCE_COMPLETE'=>'目的地清关完成',
		'S_TH_IN'=>'到达转运中心',
		'S_TH_ARRANGE'=>'安排下一站转运',
		'S_TH_OUT'=>'离开转运中心',
		'S_TH_IN_LAST'=>'到达最后投递站',
	    'S_DELIVERY_SCHEDULED' => '安排投递',
	    'S_DELIVERY_SIGNED' => '快件已签收',
	    "F_CARRIER_PICKUP_5033"=>"交货失败，包裹退回到物流商",
		'F_CARRIER_PICKUP_RT_5035' => '派送延迟:已更新派送信息和计划，将重派',
		'F_CARRIER_PICKUP_RT_5034' => '承运商收件失败，包裹退回到物流商',
		'F_CLEARANCE_5037' => '进口清关延迟:查验,海关要求提供更多的清关资料（如税号、发票、许可证、货物详情、授权委托书等单据)',
		'F_CLEARANCE_5038' => '海关没收或销毁，请联络承运商',
		'F_DELIVERY_5043' => '需要进⼀步确认收件⼈信息',
		'F_DELIVERY_5044' => '预约派送',
		'F_DELIVERY_5045' => '收件人联系不上',
		'F_DELIVERY_5046' => '收件人拒收，在联系收/发件方处理中',
		'F_DELIVERY_5047' => '派送异常:退运给发件方',
		'F_DELIVERY_5048' => '派送异常:收发件方弃件、销毁',
		'F_DELIVERY_5049' => '派送异常:收件地址不在服务范围之内',
		'F_DELIVERY_5050' => '等待收件方支付税费',
		'F_DELIVERY_5051' => '收件方要求暂扣、延迟派送或自提',
		'F_DELIVERY_5052' => '部分签收',
		'F_DELIVERY_5053' => '包裹滞留中，即将派送',
	    "F_CLEARANCE_5054"=>"出口清关延迟:等待放行",
	    "F_CLEARANCE_5055"=>"出口清关延迟:安检、技术或系统等不可抗力因素",
	    "F_CLEARANCE_5056"=>"出口清关延迟:申报信息（如HS，价格等）有问题",
	    "F_CLEARANCE_5057"=>"出口清关延迟:查验",
	    "F_CLEARANCE_5058"=>"出口清关异常:包裹即将退运",
	    "F_CLEARANCE_5059"=>"进口清关延迟:查验",
	    "F_CLEARANCE_5060"=>"进口清关异常:包裹含限制产品",
	    "F_DELIVERY_5061"=>"派送延迟:偏远地址，将延迟派送",
	    "F_DELIVERY_5062"=>"派送异常:包裹放置在失物招领处，将退运",
	    "F_DELIVERY_5063"=>"派送异常:收件人未按约定自提，将退运",
	    "F_DELIVERY_5064"=>"派送异常:货物破损或丢失，在联系发件人",
	    "F_DELIVERY_5065"=>"派送异常:等待发件人退运指令",
	    "F_DELIVERY_5066"=>"派送异常:其它不可预见原因",
	    "F_CHECK_5067"=>"核查异常",
	    "F_PICKUP_5068"=> "提取异常",
	    "F_TH_5069"=>"转运异常,正在解决中"
	);
	static $FARCODES_EN= array(
		//'UNKNOWN'=>'Unknown',
		'LOAD'=>'Loaded into containers & Awaiting sailing',
		'SET_SAIL'=>'Departure for destination',
		'ARRIVAL_PORT'=>'Arrived at the port of destination, await for customs clearance',
		'WAREHOUSE_INBOUND' => 'Arrived at Warehouse',
		'CHECK_WEIGHT' => 'Inspection by Warehouse',
		'CONFIRM' => 'Await Release from Sender',
		'PALLETIZE' => 'Ready for Carrier',
		'WAREHOUSE_OUTBOUND' => 'Departed Warehouse',
		'CARRIER_PICKUP' => 'Pickup Scan',
		'DELIVERY' => 'DELIVERED',
		'CARRIER_INTRANSMIT' => 'In Transit to Export Port',
		'E_CLEARANCE_FLIGHT' => 'Export Custom Clearance Processing and Flight Booking',
		'S_CLEARANCE_START' => 'Import Custom Clearance Processing',
		'S_CLEARANCE_COMPLETE' => 'Released by The Clearing Agency',
		'S_TH_IN' => 'Arrival Scan',
		'S_TH_ARRANGE' => '',
		'S_TH_OUT' => 'Departure Scan',
		'S_TH_IN_LAST' => 'Destination Scan',
		'S_DELIVERY_SCHEDULED' => 'Out For Delivery',
		'S_DELIVERY_SIGNED' => 'DELIVERED',
	    "F_CARRIER_PICKUP_5033"=>"Carrier failed to pickup the package",
		'F_CARRIER_PICKUP_RT_5035' => 'Delivery delay: A delivery change for this package is in progress, will make reattempt.',
		'F_CARRIER_PICKUP_RT_5034' => 'Returned to logistics company.',
		'F_CLEARANCE_5037' => 'Import clearance delay: Inspection, the customs require to provide more clearance documents (tax number, invoice, license, merchandise description, power of attorney , etc).',
		'F_CLEARANCE_5038' => 'Please contact Carrier.',
		'F_DELIVERY_5043' => 'Delivery information needed,attempting to update it',
		'F_DELIVERY_5044' => 'Scheduled for delivery as agreed',
		'F_DELIVERY_5045' => 'Delivery attempted,recipient not home',
		'F_DELIVERY_5046' => 'Recipient refused delivery,contact the shipper',
		'F_DELIVERY_5047' => 'Delivery failure: Returning to the shipper.',
		'F_DELIVERY_5048' => 'Delivery failure: The package was abandoned by both the sender and receiver.',
		'F_DELIVERY_5049' => 'Delivery failure: We do not currently serve this special destination address, will transferred to a local agent for delivery.',
		'F_DELIVERY_5050' => 'Delivery delay: Related fees cannot be collected, will make reattempt.',
		'F_DELIVERY_5051' => 'Delivery delay: Delivered to Carrier Access Point.',
		'F_DELIVERY_5052' => 'Partial delivery.',
		'F_DELIVERY_5053' => 'Shipment on hold,scheduled for delivery',
	    "F_CLEARANCE_5054"=>"Export clearance delay: Awaiting release from customs",
	    "F_CLEARANCE_5055"=>"Export clearance delay: Aviation security/technical/system etc.force majeure issue",
	    "F_CLEARANCE_5056"=>"Export clearance delay: Declaration information issue (HS code, price, etc.)",
	    "F_CLEARANCE_5057"=>"Emport clearance delay: Inspection",
	    "F_CLEARANCE_5058"=>"Emport clearance failure: Package will return to shipper",
	    "F_CLEARANCE_5059"=>"Import clearance delay: Inspection",
	    "F_CLEARANCE_5060"=>"Import clearance failure: The package contains a restricted/prohibits commodity",
	    "F_DELIVERY_5061"=>"Delivery delay: Remote address, delivery will be delayed",
	    "F_DELIVERY_5062"=>"Delivery failure: The package is placed in the Lost and Found, will return",
	    "F_DELIVERY_5063"=>"Delivery failure: The receiver fails to pick up the package as agreed, will return ",
	    "F_DELIVERY_5064"=>"Delivery failure: Package may be damaged or lost,contact the shipper",
	    "F_DELIVERY_5065"=>"Delivery failure: Awaiting return to sender authorization",
	    "F_DELIVERY_5066"=>"Delivery failure: Other unexpected reason",
	    "F_CHECK_5067"=>"Check failure",
	    "F_PICKUP_5068"=>"Pick up failure",
	    "F_TH_5069"=>"ransfer failure"
	);
	
	function actionTestImg(){
		$dir=realpath(INDEX_DIR);
		exec("/usr/bin/convert -density 300 -depth 8 -quality 85 {$dir}/1Z4F1R246707811679.pdf -append {$dir}/1Z4F1R246707811679.pdf.jpg");
		$filename=$dir.DS.'1Z4F1R246707811679.pdf.jpg';
		dump($filename);
		$image = new ZBarCodeImage($filename);
		dump($image);
		$scanner = new ZBarCodeScanner();
		$barcode = $scanner->scan($image);
		dump($barcode);
		exit;
	}
	function actionTestDSA(){
		/*
		$hex=file_get_contents(INDEX_DIR.'/publickey.dsa.hex.txt');
		file_put_contents(INDEX_DIR.'/publickey.dsa.blob.txt',hex2bin($hex));
		$pem=base64_encode(hex2bin($hex));
		$pem=chunk_split($pem, 64, "\n");
		file_put_contents(INDEX_DIR.'/publickey.dsa.blob.pem',"-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----");
		echo $hex;
		*/
		$pkeyid=openssl_get_publickey(file_get_contents(INDEX_DIR.'/publickey.dsa.blob.pem'));
		var_dump($pkeyid);
		$r=openssl_verify("{\"aliOrderNo\":\"ALS00000390010\"}",'302d021500955dd38a484dea766092dcb63420e537d307e8570214145450fd8f25bed23b90d10236f24331751cd4b1',$pkeyid,OPENSSL_ALGO_SHA1);
		echo openssl_error_string();
		var_dump($r);
		exit;
	}
	static function toPinyin($str){
		$py=Helper_Chinese::toPinYin($str);
		if ($py){
			return ucfirst($py);
		}
		return $str;
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
	 * 根据原子生成固定随机数
	 * @example
	 * 	staticRand(9,15,30) =44
	 * @param int $atom
	 * @param int $range
	 * @param int $base
	 */
	static function staticRand($atom,$start,$end){
		$range=$end-$start;
		$base=$start;
		$atom=substr($atom,-1,1);
		return $base+ceil($atom/10*$range);
	}
	/**
	 * far800 查询接口
	 */
	function actionFarTrack(){
		$o=Order::find('far_no=?',request('num'))->order("order_id desc")->getOne();
		if ($o->isNewRecord()){
			echo json_encode(array('message'=>'单号不正确'));
			exit;
		}
		$ets=Event::find('order_id =? and event_time<? and confirm_flag="1" and event_code !="DELIVERY"',$o->order_id,time())->order('event_time')->getAll();
		$trs=Tracking::find('order_id =? and trace_time<? and confirm_flag="1"',$o->order_id,time())->order('trace_time')->getAll();
		$ret=array('message'=>'OK','data'=>array(),'num'=>$o->far_no,'dest'=>$o->consignee_country_code);
		// 时间修正
		$oldStyle=false;

		$etsHash=Helper_Array::toHashmap($ets,'event_code','event_time');
		$etsHashware = isset($etsHash['WAREHOUSE_OUTBOUND']) ? $etsHash['WAREHOUSE_OUTBOUND'] : 0;
		$etsHashpall = isset($etsHash['PALLETIZE']) ? $etsHash['PALLETIZE'] : 0;

		if ($etsHashware-$etsHashpall <600){
			$oldStyle=true;
		}
		
		foreach ($ets as $e){
			
			//海运取货时间未到不展示
			if($e->event_code=='CARRIER_PICKUP' && $o->service_code =='OCEAN-FY'){
				$etsHashpick = isset($etsHash['CARRIER_PICKUP']) ? $etsHash['CARRIER_PICKUP'] : 0;
				if($etsHashpick < time()){
					$ret['data'][]=array(
						'location'=>self::toPinyin($e->event_location),
						'context'=>self::$FARCODES[$e->event_code]. ' '.self::$FARCODES_EN[$e->event_code],
						'time'=>$e->event_time,
						'timeFormat'=>date('Y-m-d H:i:s',$e->event_time),
						'timezone'=>$e->timezone
					);
				}else{
					continue;
				}
			}
				
			
			$ret['data'][]=array(
				'location'=>self::toPinyin($e->event_location),
				'context'=>self::$FARCODES[$e->event_code]. ' '.self::$FARCODES_EN[$e->event_code],
				'time'=>$e->event_time,
				'timeFormat'=>date('Y-m-d H:i:s',$e->event_time),
				'timezone'=>$e->timezone
			);
			
			if($e->event_code=="LOAD" && $o->service_code =='OCEAN-FY' ){
				$channel=Channel::find('channel_id=?',$o->channel_id)->getOne();
				$network=Network::find('network_code=?',$channel->trace_network_code)->getOne();
				 $ret['data'][]=array(
					'location'=>self::toPinyin($e->event_location),
					'context'=>'目的地物流商：'.$network->network_name.' 目的地物流单号：'.$o->tracking_no.'（货件到港提取之后方可查询） Last Mile Carrier:'.$network->network_name.' Tracking No.:'.$o->tracking_no,
					'time'=>$e->event_time,
					'timeFormat'=>date('Y-m-d H:i:s',$e->event_time),
					'timezone'=>$e->timezone
				); 
			}
			
			if (!$oldStyle && $o->service_code !='EMS-FY' && $o->service_code !='OCEAN-FY' && $o->service_code !='WIG-FY' && $o->service_code !='EUUS-FY' && $o->service_code !='US-FY' && $o->channel_id !='37'){
				if($e->event_code=='CARRIER_PICKUP'){
					//如果承运商已取件，义乌仓出库的订单，同时满足A1,A2,A3三个条件时：A1当订单目的地为：US/CA/MX/PR，A2且实际出库时间在当天9:00-12：00之间,A3且属于义乌OGP的订单时,那么增加CARRIER_INTRANSMIT事件，当天 13:A分(B+2)秒 （30≤A≤45）
					if($o->department_id=='8' && in_array($o->consignee_country_code, array('US','CA','MX','PR')) && '09:00:00'<=date("H:i:s",$o->warehouse_out_time) && date("H:i:s",$o->warehouse_out_time)<='12:00:00' && $o->channel->channelgroup->channel_group_name=='义乌OGP' && (strtotime(date("Y-m-d 13:46:00",$e->event_time)))<time()){
						$transmit_time=strtotime(date("Y-m-d",$e->event_time))+13*60*60+ self::staticRand($e->order_id,30,45) *60+self::staticRand($e->order_id,45,55)+2;
						 $ret['data'][]=array(
							'location'=>self::toPinyin($e->event_location),
							'context'=>'货物中转交航 In Transit to Export Port',
							'time'=>$transmit_time,
							'timeFormat'=>date('Y-m-d H:i:s',$transmit_time),
							'timezone'=>$e->timezone
						); 
						//如果承运商已取件，那么增加CARRIER_INTRANSMIT事件，第二天 13:A分(B+2)秒 （30≤A≤45）
					}elseif ((strtotime(date("Y-m-d 13:46:00",$e->event_time))+86400)<time()){
						$transmit_time=strtotime(date("Y-m-d",$e->event_time))+86400+13*60*60+ self::staticRand($e->order_id,30,45) *60+self::staticRand($e->order_id,45,55)+2;
						 $ret['data'][]=array(
							'location'=>self::toPinyin($e->event_location),
							'context'=>'货物中转交航 In Transit to Export Port',
							'time'=>$transmit_time,
							'timeFormat'=>date('Y-m-d H:i:s',$transmit_time),
							'timezone'=>$e->timezone
						); 
					}
				}
				
				if($e->event_code=='CARRIER_PICKUP'){
					//如果承运商已取件，义乌仓出库的订单，同时满足A1,A2,A3三个条件时：A1当订单目的地为：US/CA/MX/PR，A2且实际出库时间在当天9:00-12：00之间,A3且属于义乌OGP的订单时,那么增加CARRIER_INTRANSMIT事件，18:B分(A+2)秒 （45≤B≤55）
					if($o->department_id=='8' && in_array($o->consignee_country_code, array('US','CA','MX','PR')) && '09:00:00'<=date("H:i:s",$o->warehouse_out_time) && date("H:i:s",$o->warehouse_out_time)<='12:00:00' && $o->channel->channelgroup->channel_group_name=='义乌OGP' && (strtotime(date("Y-m-d 18:56:00",$e->event_time)))<time()){
						$flight_time=strtotime(date("Y-m-d",$e->event_time))+18*60*60+self::staticRand($e->order_id,45,55)*60+self::staticRand($e->order_id,30,45)+2;
						 $ret['data'][]=array(
							'location'=>self::toPinyin($e->event_location),
							'context'=>'出口清关交航中 Export Custom Clearance Processing and Flight Booking',
							'time'=>$flight_time,
							'timeFormat'=>date('Y-m-d H:i:s',$flight_time),
							'timezone'=>$e->timezone
						); 
					}elseif ((strtotime(date("Y-m-d 18:56:00",$e->event_time))+86400)<time()){
						//如果承运商已取件，那么增加E_CLEARANCE_FLIGHT事件，第二天 18:B分(A+2)秒 （45≤B≤55）
						$flight_time=strtotime(date("Y-m-d",$e->event_time))+86400+18*60*60+self::staticRand($e->order_id,45,55)*60+self::staticRand($e->order_id,30,45)+2;
						 $ret['data'][]=array(
							'location'=>self::toPinyin($e->event_location),
							'context'=>'出口清关交航中 Export Custom Clearance Processing and Flight Booking',
							'time'=>$flight_time,
							'timeFormat'=>date('Y-m-d H:i:s',$flight_time),
							'timezone'=>$e->timezone
						); 
					}
				}
				
				if($e->event_code=='CARRIER_PICKUP'){
					//如果承运商已取件，义乌仓出库的订单，同时满足A1,A2,A3三个条件时：A1当订单目的地为：US/CA/MX/PR，A2且实际出库时间在当天9:00-12：00之间,A3且属于义乌OGP的订单时,那么增加CARRIER_INTRANSMIT事件，18:B分(A+2)秒 （45≤B≤55）
					if($o->department_id=='8' && in_array($o->consignee_country_code, array('US','CA','MX','PR')) && '09:00:00'<=date("H:i:s",$o->warehouse_out_time) && date("H:i:s",$o->warehouse_out_time)<='12:00:00' && $o->channel->channelgroup->channel_group_name=='义乌OGP' && (strtotime(date("Y-m-d 18:56:00",$e->event_time)))<time()){
						$willfly_time=strtotime(date("Y-m-d 23:25:00",$e->event_time));
						 $ret['data'][]=array(
							'location'=>'Shanghai',
							'context'=>'等待航班离境 Scheduled to Take off',
							'time'=>$willfly_time,
							'timeFormat'=>date('Y-m-d H:i:s',$willfly_time),
							'timezone'=>$e->timezone
						); 
					}elseif ((strtotime(date("Y-m-d 23:25:00",$e->event_time))+86400)<time()){
						//如果承运商已取件，那么增加E_CLEARANCE_WILLFLY事件，第二天 23:25:00
						$willfly_time=strtotime(date("Y-m-d 23:25:00",$e->event_time))+86400;
						 $ret['data'][]=array(
							'location'=>'Shanghai',
							'context'=>'等待航班离境 Scheduled to Take off',
							'time'=>$willfly_time,
							'timeFormat'=>date('Y-m-d H:i:s',$willfly_time),
							'timezone'=>$e->timezone
						); 
					}
				}
			}
		}
		foreach ($trs as $e){
			$cn_desc=$e->trace_desc_cn?$e->trace_desc_cn:self::$FARCODES[$e->tracking_code];
			$en_desc=$e->trace_desc_en?$e->trace_desc_en:self::$FARCODES_EN[$e->tracking_code];
			
			$ret['data'][]=array(
				'location'=>$o->service_code=='EMS-FY'?self::toPinyin($e->location):$e->location,
				'context'=>$cn_desc. ' '.$en_desc,
				'time'=>$e->trace_time,
				'timeFormat'=>date('Y-m-d H:i:s',$e->trace_time),
				'timezone'=>$e->timezone
			);
			if ($e->tracking_code =='S_DELIVERY_SIGNED'){
				$ret['state']=3;
			}
		}
// 		$ret['data']=Helper_Array::sortByCol($ret['data'], 'time',SORT_ASC);
		echo json_encode($ret);
		exit;
	}
    /**
     * Order Booking
     */
    function actionOrderbooking(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$res=file_get_contents ( "php://input" );
    	$ali_json->changeProps(array(
    		'api_name'=>'booking',
    		'ali_json'=>$res
    	));
    	$ali_json->save();
    	QLog::log('API1'.$res);
    	$info=json_decode($res,true);
        //QLog::log('API1.1'.$info['bookingOrderDTO']);
        $data=json_decode($info['bookingOrderDTO'],true);
        //判断数据是否存在
        if(!is_array($data) || count($data)<=0){
            return json_encode(array (
                'isSuccess' => false,'message' => '订单数据为空或格式不正确'
            ));
        }
        if(!isset($info['sign']) || $info['sign']==''){
            return json_encode(array (
                'isSuccess' => false,'message' => '签名校验失败'
            ));
        }
        if(!isset($data['aliOrderNo'])){
            return json_encode(array (
                'isSuccess' => false,'message' => '阿里订单号不存在'
            ));
        }
        //判断ali订单号是否已存在
        $order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
        if(!$order->isNewRecord()){
            return json_encode(array (
                'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$order->far_no)
            ));
        }
        $conn = QDB::getConn ();
        $conn->startTrans ();
        $now='FAREX'.date('ym');
        $seq = Helper_Seq::nextVal ( $now );
        if ($seq < 1) {
            Helper_Seq::addSeq ( $now );
            $seq = 1;
        }
        $far_no=$now.sprintf("%06d",$seq).'YQ';
        
//         if($data['consignee']['name1']==$data['consignee']['name2']){
//             $data['consignee']['name2']='';
//         }
        //判断取件网点
        $pick_company='';
        if(isset($data['needPickUp']) && $data['needPickUp']){
            $package_pre_weight = 0;
            $package_act_weight = 0;
            foreach ($data['packages'] as $packageinfo){
                $package_pre_weight += $packageinfo['quantity']*$packageinfo['length']*$packageinfo['width']*$packageinfo['height']/5000;
                $package_act_weight += $packageinfo['quantity']*$packageinfo['weight'];
            }
            $tmp_weight = $package_pre_weight>$package_act_weight?$package_pre_weight:$package_act_weight;
            $zip=Zipcode::find('zip_code_low<=? and zip_code_high>=? and service_code =?',$data['consignor']['postalCode'],$data['consignor']['postalCode'],$data['serviceCode'])->getOne();
            if(!$zip->isNewRecord()){
                //义乌和杭州 先根据取件邮编匹配之后再进行不超过3KG的订单分配到平台的判断，其余包裹，按邮编进行分配到各网点。
                $pick_company_tmp=$zip->pick_company;//临时存储取件地区
                $pick_company_array=array('杭分','义乌分','青岛仓');
                if(in_array($pick_company_tmp,$pick_company_array)){
                    $pick_company=$zip->pick_company;
                }else {
                    if($tmp_weight >= 3){
                        $pick_company=$zip->pick_company;
                    }else{
                        $pick_company="平台";
                    }
                }
                if($data['serviceCode']=='EUUS-FY'){
                	$pick_company="平台";
                }
                if($data['serviceCode']=='US-FY' && $zip->pick_company<>'青岛仓'){
                    $pick_company="平台";
                }
            }
        }
        //数据过滤
        //地址1、地址2、城市、省州、邮编 这四个信息里：如果非英文符号，自动转换成相应的英文符号。中文全角“。”更新成"." ; “，”更新成","
        $data['consignee']['mobile'] = self::convertStrType($data['consignee']['mobile']);
        $data['consignee']['street1'] = self::convertStrType($data['consignee']['street1']);
        $data['consignee']['street2'] = self::convertStrType(@$data['consignee']['street2']);
        $data['consignee']['city'] = self::convertStrType($data['consignee']['city']);
        $data['consignee']['stateRegionCode'] = self::convertStrType($data['consignee']['stateRegionCode']);
        $data['consignee']['postalCode'] = self::convertStrType($data['consignee']['postalCode']);
        //收件人电话只保留纯数字：电话里有中英文中杠“—”“-”或者下划线时，或者空格时，自动去掉
        $data['consignee']['mobile'] = preg_replace('/[^\d]/','',$data['consignee']['mobile']);
        //收件人、收件人公司 这两个信息里，不能有英文空格之外的标点符号，有直接用空格替代
        $data['consignee']['name1'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['consignee']['name1']);
        $data['consignee']['name2'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['consignee']['name2']);
        //收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里：如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
        $data['consignee']['name1'] = preg_replace('/[　\s]+/u',' ',$data['consignee']['name1']);
        $data['consignee']['name2'] = preg_replace('/[　\s]+/u',' ',$data['consignee']['name2']);
        $data['consignee']['street1'] = preg_replace('/[　\s]+/',' ',$data['consignee']['street1']);
        $data['consignee']['street2'] = preg_replace('/[　\s]+/',' ',$data['consignee']['street2']);
        $data['consignee']['city'] = preg_replace('/[　\s]+/',' ',$data['consignee']['city']);
        $data['consignee']['stateRegionCode'] = preg_replace('/[　\s]+/',' ',$data['consignee']['stateRegionCode']);
        $data['consignee']['postalCode'] = preg_replace('/[　\s]+/',' ',$data['consignee']['postalCode']);
        $data['consignor']['name1'] = preg_replace('/[　\s]+/u',' ',$data['consignor']['name1']);
        $data['consignor']['name2'] = preg_replace('/[　\s]+/u',' ',$data['consignor']['name2']);
        //将数据存入数据库
//         $comment = '';
//         $sender_company = Contact::find('sender_company like ?','%'.$data['consignor']['name1'].'%')->getOne();
//         if($sender_company->isNewRecord()){
//            if(isset($data['consignor']['name2'])&&!empty($data['consignor']['name2'])){
//               $sendercompany = Contact::find('sender_company like ?','%'.$data['consignor']['name2'].'%')->getOne();
//               if(!$sendercompany->isNewRecord()){
//                  $comment = $sendercompany->comment;
//               }
//            }
//         }else{
//            $comment = $sender_company->comment;
//         }
        $order->ali_order_no=$data['aliOrderNo'];
        $order->order_no=$data['aliOrderNo'];
        $order->reference_no= preg_replace('/\s+/', '', @$data['referenceNo']);
        $order->far_no=$far_no;
        $order->service_code=$data['serviceCode'];
        $order->sender_mobile=$data['consignor']['mobile'];
        $order->sender_telephone=@$data['consignor']['telephone'];
        $order->sender_email=@$data['consignor']['email'];
        $order->sender_name1=$data['consignor']['name1'];
        $order->sender_name2=@$data['consignor']['name2'];
        $order->sender_street1=$data['consignor']['street1'];
        $order->sender_street2=@$data['consignor']['street2'];
        $order->sender_country_code=$data['consignor']['countryCode'];
        $order->sender_city=$data['consignor']['city'];
        $order->sender_postal_code=$data['consignor']['postalCode'];
        $order->sender_state_region_code=$data['consignor']['stateRegionCode'];
//         $order->sender_comment=$comment;
//增加手机和电话号码的互补功能
        $order->consignee_mobile=$data['consignee']['mobile']?$data['consignee']['mobile']:@$data['consignee']['telephone'];
        $order->consignee_telephone=@$data['consignee']['telephone']?@$data['consignee']['telephone']:$data['consignee']['mobile'];
        $order->consignee_email=@$data['consignee']['email'];
        $order->consignee_name1=$data['consignee']['name1'];
        $order->consignee_name2=@$data['consignee']['name2'];
        $order->consignee_street1=$data['consignee']['street1'];
        $order->consignee_street2=@$data['consignee']['street2'];
        $order->consignee_country_code=$data['consignee']['countryCode'];
        $order->consignee_city=$data['consignee']['city'];
        $order->consignee_postal_code=$data['consignee']['postalCode'];
        $order->consignee_state_region_code=$data['consignee']['stateRegionCode'];
        $order->declaration_type=$data['customsDeclaration']['declarationType'];
        $order->total_amount=$data['customsDeclaration']['totalAmount'];
        $order->currency_code=$data['customsDeclaration']['currencyCode'];
        $order->need_insurance=$data['needInsurance'];
        $order->tax_payer_id=@$data['taxpayerId'];
        $order->remarks=@$data['remarks'];
        $order->delivery_priority=@$data['deliveryPriority'];
        $order->order_status='1';
        $order->need_pick_up=$data['needPickUp'];
        $order->warehouse_code=$data['warehouse']['code'];
        $order->warehouse_name=$data['warehouse']['name'];
        $order->pick_company=$pick_company;
        $order->customer_id=1;
        $order->save();
        $ali_json->ali_sign=$info['sign'];
        $ali_json->order_id=$order->order_id;
        $ali_json->ali_order_no=$order->ali_order_no;
        $ali_json->save();
        //港前问题件判断
        $detail=array(
        	'reason'=>array()
        );
        //EMS专线,当申报总价高于400.00USD时，预警：EMS超400USD
        if($order->service_code == 'EMS-FY'){
        	if($order->total_amount>400){
        		$detail['reason'][]='EMS超400USD';
        	}
        }
        //中美专线，订单总价超800USD时，进行预警：超800美金
        if($order->service_code == 'US-FY'){
        	if($order->total_amount>800){
        		$detail['reason'][]='超800美金';
        	}
        }
        //欧美专线，目的地为GB的订单，进行预警：GB订单，需要提供交易凭证
        if($order->service_code == 'EUUS-FY'){
        	if($order->consignee_country_code=='GB'){
        		$detail['reason'][]='GB订单，需要提供交易凭证';
        	}
        }
        //US 检查电话号码是否不足10位
        if($order->consignee_country_code=='US'){
        	if(strlen($order->consignee_mobile)<10){
        		$detail['reason'][]='US 检查电话号码是否不足10位';
        	}
        }
        //BR 检查订单详情里没有提供税号信息
        if($order->consignee_country_code=='BR'){
        	if($order->tax_payer_id==''){
        		$detail['reason'][]='BR 检查订单详情里没有提供税号信息';
        	}
        }
        //订单申报总金额超过700.00USD,且报关方式为：QT的订单。
        if($order->total_amount>700 && $order->declaration_type=='QT'){
        	$detail['reason'][]='订单申报总金额超过700.00USD,且报关方式为：QT的订单';
        }
        //订单申报总金额低于1.00USD的订单
        if($order->total_amount<1){
        	$detail['reason'][]='订单申报总金额低于1.00USD的订单';
        }
        //订单申报方式为：DL
        if($order->declaration_type=='DL'){
        	if($order->service_code=='EMS-FY'){
        		$detail['reason'][]='EMS不提供报关服务';
        	}else if($order->service_code == 'US-FY' || $order->service_code == 'EUUS-FY'){
        		$detail['reason'][]='无报关服务';
        	}else{
        		$detail['reason'][]='订单申报方式为：DL';
        	}
        }
        //地址1+地址2字符总数超过105的订单
        //普货专线和中美专线，地址总字符超过105时，进行预警，EMS不作限制
        if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'US-FY'){
        	if(strlen($order->consignee_street1.' '.$order->consignee_street2)>105){
        		$detail['reason'][]='地址1加地址2字符总数超过105的订单';
        	}
        }
        //假发专线地址1+地址2字符总数超过70的订单
        if($order->service_code=='WIG-FY'){
        	if(strlen($order->consignee_street1.' '.$order->consignee_street2)>70){
        		$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
        	}else {
        		$address=Order::splitAddressfedex($order->consignee_street1.' '.$order->consignee_street2);
        		if(count($address)>2){
        			$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
        		}
        	}
        }
        //收件人公司里，如果有数字进行预警，预警原因：收件公司信息含有数字
        if($order->consignee_name1){
        	if(preg_match('/\d/',$order->consignee_name1)){
        		$detail['reason'][]='收件公司/客户名称1含有数字';
        	}
        }
        if($order->consignee_name2){
        	if(preg_match('/\d/',$order->consignee_name2)){
        		$detail['reason'][]='收件公司/客户名称2含有数字';
        	}
        }
        //收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里如有非英文符，进行预警，预警原因：某某字段有非英文字符。
        //地址1、地址2、城市、省州、邮编这五个信息里：英文状态下以下符号不作预警："#" "/" "" "-" "." "," "&" "*" ";" ":" "?" ";" ""
        if($order->consignee_name1){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$order->consignee_name1)){
        		$detail['reason'][]='收件公司/客户名称1有非英文字符';
        	}
        }
        if($order->consignee_name2){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$order->consignee_name2)){
        		$detail['reason'][]='收件公司/客户名称2有非英文字符';
        	}
        }
        if($order->consignee_street1){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_street1)){
        		$detail['reason'][]='收件人地址1有非英文字符';
        	}
        }
        if($order->consignee_street2){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_street2)){
        		$detail['reason'][]='收件人地址2有非英文字符';
        	}
        }
        if($order->consignee_city){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_city)){
        		$detail['reason'][]='收件人城市有非英文字符';
        	}
        }
        if($order->consignee_state_region_code){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_state_region_code)){
        		$detail['reason'][]='收件人省/州有非英文字符';
        	}
        }
        if($order->consignee_postal_code){
        	if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_postal_code)){
        		$detail['reason'][]='收件人邮编有非英文字符';
        	}
        }
        // EMS订单邮编校验
        if($order->service_code == 'EMS-FY' || $order->service_code == 'ePacket-FY'){
        	$zipFormat = Zipformat::find('country_code_two = ?',$order->consignee_country_code)->getOne();
        	if(!$zipFormat->isNewRecord()){
        		if(!preg_match($zipFormat->zip_format_preg_match, trim($order->consignee_postal_code))){
        			$detail['reason'][]='收件人邮编格式不正确,'.$order->consignee_country_code.'的邮编格式为：'.$zipFormat->zip_format;
        		}
        	}
        }
        //查询判断是否疑似偏远
        $product1 = $product=Product::find("product_name=?",$order->service_code)->getOne();
        $productppr=Productppr::find('product_id=? and effective_time <= ? and invalid_time >= ?',$product->product_id,time(),time())
        ->getOne();
        $trim = array(' ','-');
        if(!$productppr->isNewRecord()){
            $remote_city=Remote::find('remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,"") != ""',$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_city,strtolower(str_replace($trim,'',$order->consignee_city)))
            ->getOne();
            if($remote_city->isNewRecord()){//城市不同，省州完全相同
                $remote_state = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,'') != '' ",$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_state_region_code,strtolower(str_replace($trim,'',$order->consignee_state_region_code)))->getOne();
                if(!$remote_state->isNewRecord()){
                   $order->suspected_remote='1';
                   $order->save();
                }else{
                    $remote_like = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city like ? or remote_city like ? or remote_city like ? or remote_city like ?) and ifnull(remote_city,'') != ''",$productppr->remote_manage_id,$order->consignee_country_code,'%'.$order->consignee_city.'%','%'.strtolower(str_replace($trim,'',$order->consignee_city)).'%','%'.$order->consignee_state_region_code.'%','%'.strtolower(str_replace($trim,'',$order->consignee_state_region_code)).'%')->getOne();
                    if(!$remote_like->isNewRecord()){//订单中的城市、省州在偏远城市信息中出现
                       $order->suspected_remote='1';
                       $order->save();
                    } 
                    $remote=Remote::find("country_code_two = ? and remote_manage_id= ? and ifnull(remote_city,'') != ''",$order->consignee_country_code,$productppr->remote_manage_id)->getAll();
                    foreach ($remote as $v){//偏远城市信息在订单中的城市、省州、地址中出现
                        $is_far=Order::find("consignee_state_region_code like ? or consignee_city like ? or consignee_street1 like ? or consignee_street2 like ? ",'%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%')
                        ->where('order_id= ? ',$order->order_id)->getOne();
                        if(!$is_far->isNewRecord()){
                            $order->suspected_remote='1';
                            $order->save();
                            break;
                        }
                    }
                }
            }
        }
        if($order->suspected_remote=='1'){
        	$detail['reason'][]='城市疑似偏远，需人工介入';
        }
        $black=blacklist::find('product_id=?',$product->product_id)->asArray()->getAll();
        if(count($black)>0){
            foreach ($black as $b){
                $reason='';
                $i=0;
                $bb=array_filter($b);
                $co=count($bb)-4;
                if($b['consignee_country_code'] && strtoupper(trim($order->consignee_country_code)) == strtoupper(trim($b['consignee_country_code']))){
                    $reason.='国家:'.$b['consignee_country_code'].',';
                    $i++;
                }
                if($b['consignee_postal_code'] && strtoupper(trim($order->consignee_postal_code)) ==  strtoupper(trim($b['consignee_postal_code']))){
                    $reason.='邮编:'.$b['consignee_postal_code'].',';
                    $i++;
                }
                if($b['consignee_city'] && strtoupper(trim($order->consignee_city))==strtoupper(trim($b['consignee_city']))){
                    $reason.='城市:'.$b['consignee_city'].',';
                    $i++;
                }
                if($b['consignee_state_region_code'] && strtoupper(trim($order->consignee_state_region_code)) == strtoupper(trim($b['consignee_state_region_code']))){
                    $reason.='州:'.$b['consignee_state_region_code'].',';
                    $i++;
                }
                
                if(($b['sender_name1'] && (strtoupper(trim($order->sender_name1)) == strtoupper(trim($b['sender_name1'])) ||  strtoupper(trim($order->sender_name2)) == strtoupper(trim($b['sender_name1']))))){
                    $reason.='发件人:'.$b['sender_name1'].',';
                    $i++;
                }
                if(($b['sender_name2'] && (strtoupper(trim($order->sender_name2)) == strtoupper(trim($b['sender_name2'])) || strtoupper(trim($order->sender_name1)) == strtoupper(trim($b['sender_name2']))))){
                    $reason.='发件公司:'.$b['sender_name2'].',';
                    $i++;
                }
                
                if($b['sender_street1']){
	                $condition=str_replace('\\','\\\\',$b['sender_street1']);
	                $condition=str_replace('/','\\/',$condition);
	                
	                $condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
	                
	                $condition=str_replace('*','.*?',$condition);
	                $order->consignee_street1=str_replace(PHP_EOL, '', $order->consignee_street1);
	                $order->consignee_street2=str_replace(PHP_EOL, '', $order->consignee_street2);
	                $order->sender_street1=str_replace(PHP_EOL, '', $order->sender_street1);
	                $order->sender_street2=str_replace(PHP_EOL, '', $order->sender_street2);
	                if (preg_match('/^'.$condition.'$/i',$order->consignee_street1) || preg_match('/^'.$condition.'$/i',$order->consignee_street2) || preg_match('/^'.$condition.'$/i',$order->sender_street1) || preg_match('/^'.$condition.'$/i',$order->sender_street2)){
	                	$reason.='地址:'.$b['sender_street1'].',';
	                	$i++;
	                }
                }
                if($b['product_name']){
                	$j=0;
                	$condition=str_replace('\\','\\\\',$b['product_name']);
                	$condition=str_replace('/','\\/',$condition);
                	 
                	$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
                	 
                	$condition=str_replace('*','.*?',$condition);
                	foreach ($data['products'] as $op){
                		if (preg_match('/^'.$condition.'$/i',$op['productName']) || preg_match('/^'.$condition.'$/i',$op['productNameEn'])){
                			$reason.='品名:'.$b['product_name'].',';$j++;
                		}
                	}
                	if($j>0){
                		$i++;
                	}
                }
                if($i > 0 && $i == $co){
                    $reason=trim($reason,',');
                    $order->black_flag='1';
                    $order->black_reason=$reason;
                    $order->save();
                    break;
                }
            }
        }
        if($order->black_flag=='1'){
        	$detail['reason'][]='线路:'.$order->service_product->product_chinese_name.','.$order->black_reason.'黑名单';
        }
        $consignee_postal_code=trim($order->consignee_postal_code);
        if($consignee_postal_code && in_array($order->service_code, array('EUUS-FY','Express_Standard_Global','US-FY','WIG-FY'))){
            $zipcode=array('123','1234','12345','123456','1234567','12345678','123456789');
            $count=substr_count($consignee_postal_code,substr($consignee_postal_code, 0,1));
            if(strlen($consignee_postal_code)=='1' || in_array($consignee_postal_code, $zipcode) || $count==strlen($consignee_postal_code)){
                $order->zip_flag='1';
                $order->save();
            }
        }
        if($order->zip_flag=='1'){
        	$detail['reason'][]='邮编异常';
        }
        //拆分阿里单号
        if(strlen($order->reference_no)){
        	$references=explode(",", $order->reference_no);
        	foreach ($references as $r){
        		$alireference=new Alireference();
        		$alireference->order_id=$order->order_id;
        		$alireference->reference_no=$r;
        		$alireference->save();
        	}
        }
        //存入product信息
        $flag=false;//判断阿里推送订单是否为测试订单
        $glasses_flag=false;
        $battery_flag=false;
        $limit_flag=false;
        $fda_flag=false;
        $all_flag=false;
        $mask_flag=false;
        foreach ($data['products'] as $order_product){
            $product=new Orderproduct();
            $product->changeProps(array(
                'order_id'=>$order->id(),
                'product_name'=>$order_product['productName'],
            	//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
            	'product_name_en'=>preg_replace('/[　\s]+/u',' ',$order_product['productNameEn']),
                'product_quantity'=>$order_product['productQuantity'],
                'product_unit'=>$order_product['productUnit'],
                'hs_code'=>$order_product['hsCode'],
                'declaration_price'=>$order_product['declarationPrice'],
                'has_battery'=>$order_product['hasBattery'],
            	'product_name_far'=>$order_product['productName'],
            	'product_name_en_far'=>preg_replace('/[　\s]+/u',' ',$order_product['productNameEn']),
            	'hs_code_far'=>$order_product['hsCode'],
            	'material_use'=>$order_product['material'].' '.$order_product['purpose']
            ));
            if($order_product['hasBattery']==1){
            	$order->has_battery=1;
            }else{
            	if(count($order_product['productType'])>0){
            		foreach ($order_product['productType'] as $prod){
            			if($prod['code']=='battery'){
            				$order->has_battery=1;
            			}
            			
            		}
            	}
            }
          
            
            $product->save();
            if($order_product['productName']=='系统测试订单'){
                $flag=true;
            }
            //根据配置判断是否检测pda品类
            if($product1->is_pda == 1){
            	if(strstr($product->product_name_far, '镜') || strstr($product->product_name_far, '假发') || strstr($product->product_name_far, '睫毛') || strstr($product->product_name_far, '牙刷') || strstr($product->product_name_en_far, 'glasses') || strstr($product->product_name_en_far, 'glass') || strstr($product->product_name_en_far, 'toothbrush') || strstr($product->product_name_en_far, 'wig') || strstr($product->product_name_en_far, 'eyelash')){
            		$order->is_pda=1;
            		$order->save();
            	}
            }
            if($order->consignee_country_code=='US'){
            	if(strstr($product->product_name_far, '眼镜') || strstr($product->product_name_far, '太阳镜') || strstr($product->product_name, '眼镜') || strstr($product->product_name, '太阳镜')){
            		$glasses_flag=true;
            	}
            	if(strstr($product->product_name_far, '睫毛') || strstr($product->product_name_far, '假睫毛') || strstr($product->product_name, '睫毛') || strstr($product->product_name, '假睫毛')){
            		$fda_flag=true;
            	}
            }
            if($product->has_battery){
            	$battery_flag=true;
            }
            if($order->service_code == 'US-FY'){
            	if(strstr($product->product_name_far, '车灯') || strstr($product->product_name_far, '大灯') || strstr($product->product_name_far, '头盔') || strstr($product->product_name_far, '刀') || strstr($product->product_name_far, '激光') || strstr($product->product_name, '车灯') || strstr($product->product_name, '大灯') || strstr($product->product_name, '头盔') || strstr($product->product_name, '刀') || strstr($product->product_name, '激光')){
            		$limit_flag=true;
            	}
            }
            if($order->service_code != 'CNUS-FY'){
	            if(strstr($product->product_name_far, '电') || strstr($product->product_name_far, '灯') || strstr($product->product_name_far, '器') || strstr($product->product_name_far, '磁') || strstr($product->product_name, '电') || strstr($product->product_name, '灯') || strstr($product->product_name, '器') || strstr($product->product_name, '磁')){
	            	$all_flag=true;
	            }
            }
            // US 产品品名里含：“眼镜”，“太阳镜”
            if($glasses_flag){
            	$detail['reason'][]='US 产品品名里含：“眼镜”，“太阳镜”';
            }
            //检查订单详情里，有产品“带电”
            if($battery_flag){
            	$detail['reason'][]='检查订单详情里，有产品“带电”';
            }
            //判断品名含mask的商品sku是否等于6307900010
            if(strpos(strtolower($order_product['productNameEn']), 'mask') !== false && $order_product['hsCode']<>'6307900010'){
            	$mask_flag = true;
            }
        }
        //判断品名含mask的商品sku是否等于6307900010
        if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $mask_flag){
        	$detail['reason'][]='hscode不等于6307900010的mask订单';
        }
        //中美专线，如果品名里含有：“车灯”，“大灯”“头盔”“刀”“激光”
        if($limit_flag){
        	$detail['reason'][]='疑似限运品';
        }
        //普货专线、欧美专线、假发专线，目的地为美国的订单，如果品名里含有：“睫毛”，“假睫毛”
        if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'EUUS-FY' || $order->service_code == 'WIG-FY'){
        	if($fda_flag){
        		$detail['reason'][]='美国需FDA';
        	}
        }
        //目前所有线路：当品名里含有“电”“灯”“器”“磁”，预警：品名含有#
        if($all_flag){
        	$detail['reason'][]='品名含有#';
        }
        if($flag){//是测试订单
            $order->ali_testing_order='1';
            $order->save();
        }
        $ratio=5000;
        $total_weight=0;
        $weight_income_ali=0;
        $total_quantity=0;
        //查询product获取计泡系数
        $product=Product::find('product_name=?',$data['serviceCode'])->getOne();
        if(!$product->isNewRecord()){
            $ratio=$product->ratio;
        }
        //存入package信息
        foreach ($data['packages'] as $order_package){
            $package=new Orderpackage();
            $package->changeProps(array(
                'order_id'=>$order->id(),
                'package_type'=>$order_package['packageType'],
                'quantity'=>$order_package['quantity'],
                'unit'=>$order_package['unit'],
                'length'=>$order_package['length'],
                'width'=>$order_package['width'],
                'height'=>$order_package['height'],
                'weight'=>$order_package['weight'],
                'weight_unit'=>$order_package['weightUnit'],
            ));
            $package->save();
            $total_weight+=$order_package['weight']*$order_package['quantity'];
            $weight_income_ali+=(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']>$order_package['weight']*$order_package['quantity']?
            (($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']:$order_package['weight']*$order_package['quantity'];
            $total_quantity +=$order_package['quantity'];
        }
        //存入阿里计费重量和阿里实重
        $order->weight_actual_ali=$total_weight;
        $order->weight_income_ali=$weight_income_ali;
        $order->package_total_num=$total_quantity;
        //#80732仓库代码
        $codewarehouses = CodeWarehouse::find()->getAll();
        foreach ($codewarehouses as $cw){
        	if ($data['warehouse']['code']==$cw->warehouse){
        		$order->department_id = $cw->department_id;
        	}
        }
        $order->save();
        if(count($detail['reason'])>0){
        	//新建问题件
        	$now = 'ISSUE' . date ( 'Ym' );
        	$seq = Helper_Seq::nextVal ( $now );
        	if ($seq < 1) {
        		Helper_Seq::addSeq ( $now );
        		$seq = 1;
        	}
        	$seq = str_pad ( $seq, 4, "0", STR_PAD_LEFT );
        	$abnormal_parcel_no = date ( 'Ym' ) . $seq;
        	$abnormal_parcel = new Abnormalparcel ( array (
        		'ali_order_no' => $order->ali_order_no,
        		'abnormal_parcel_no' => $abnormal_parcel_no,
        		'abnormal_parcel_operator' => '系统',
        		'issue_type' => '5',
        		'issue_content' => @implode ( ',', $detail ['reason'] )
        	) );
        	$abnormal_parcel->save ();
        	$history = new Abnormalparcelhistory ();
        	$history->abnormal_parcel_id = $abnormal_parcel->abnormal_parcel_id;
        	$history->follow_up_content = @implode ( ',', $detail ['reason'] );
        	$history->follow_up_operator = '系统';
        	$history->save ();
        }
        $conn->completeTrans ();
        if(substr($order->ali_order_no, 0, 3) == 'ALS'){
	        $rule_choose = AutomaticEmailRule::find('product_id = ? and tracking_code = ?',$order->service_product->product_id,'订单创建')->getOne();
	        if(!$rule_choose->isNewRecord()){
	        	$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
	        	if(!$email_template->isNewRecord()){
	        		$title = $email_template->template_title;
	        		$email_info = $email_template->template_text;
	        		 
	        		//标题
	        		$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
	        		$template_title = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_title);
	        		$template_title = preg_replace('/reference_no/',$order->reference_no, $template_title);
	        		$template_title = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_title);
	        		//内容
	        		$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
	        		$template_info = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_info);
	        		$template_info = preg_replace('/reference_no/',$order->reference_no, $template_info);
	        		$template_info = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_info);
	        		QLog::log($template_title);
	        		QLog::log($template_info);
	        		$title = nl2br($template_title);
	        		$msg = nl2br($template_info);
	        		try {
	        			$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
	        			QLog::log($email_response);
	        			if ($email_response == 'email_success') {
	        				$order_log = new OrderLog ();
	        				$order_log->order_id = $order->order_id;
	        				$order_log->staff_name = '系统';
	        				$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
	        				$order_log->save ();
	        			}else {
	        				return json_encode(array (
	        					'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
	        				));
	        			}
	        		} catch ( Exception $e ) {
	        			return json_encode(array (
	        				'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
	        			));
				    }
	        	}
	        }
        }
        //返回泛远单号
        return json_encode(array (
            'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
        ));
    }
    /**
     * Order Cancel
     */
    function actionOrdercancel(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'cancel',
    		'ali_json'=>file_get_contents ( "php://input" )
    	));
    	$ali_json->save();
        QLog::log('API2'.file_get_contents ( "php://input" ));
        $info=json_decode(file_get_contents ( "php://input" ),true);
        QLog::log('API2.1'.$info['cancelOrderDTO']);
        $data=json_decode($info['cancelOrderDTO'],true);
        //判断数据是否存在
        if(!is_array($data) || count($data)<=0){
            return json_encode(array (
                'isSuccess' => false,'message' => '数据为空或格式不正确'
            ));
        }
        if(!isset($info['sign']) || $info['sign']==''){
            return json_encode(array (
                'isSuccess' => false,'message' => '签名校验失败'
            ));
        }
        $order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
        if($order->isNewRecord()){
            return json_encode(array (
                'isSuccess' => false,'message' => '订单不存在'
            ));
        }
        $ali_json->ali_sign=$info['sign'];
        $ali_json->order_id=$order->order_id;
        $ali_json->ali_order_no=$order->ali_order_no;
        $ali_json->save();
        $order->reason_code=$data['reasonCode'];
        $order->reason_name=$data['reasonName'];
        $order->reason_remark=@$data['remark'];
        $order->order_status="2";
        $order->save();
        //删除ali_reference表里的快递号信息
        Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
        //删除所有相关费用
        Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
        //返回结果
        return json_encode(array (
            'isSuccess' => true,'message' => ''
        ));
    }
    /**
     * Order Return
     */
    function actionOrderreturn(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'return',
    		'ali_json'=>file_get_contents ( "php://input" )
    	));
    	$ali_json->save();
        QLog::log('API3'.file_get_contents ( "php://input" ));
        $info=json_decode(file_get_contents ( "php://input" ),true);
        QLog::log('API3.1'.$info['returnDTO']);
        $data=json_decode($info['returnDTO'],true);
        //判断数据是否存在
        if(!is_array($data) || count($data)<=0){
            return json_encode(array (
                'isSuccess' => false,'message' => '数据为空或格式不正确'
            ));
        }
        if(!isset($info['sign']) || $info['sign']==''){
            return json_encode(array (
                'isSuccess' => false,'message' => '签名校验失败'
            ));
        }
        $order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
        if($order->isNewRecord()){
            return json_encode(array (
                'isSuccess' => false,'message' => '订单不存在'
            ));
        }
        $ali_json->ali_sign=$info['sign'];
        $ali_json->order_id=$order->order_id;
        $ali_json->ali_order_no=$order->ali_order_no;
        $ali_json->save();
        $order->reason_code=$data['reasonCode'];
        $order->reason_name=$data['reasonName'];
        $order->reason_remark=@$data['remark'];
        if($order->order_status != '3'){
        	$order->return_type=$data['returnType'];
        	$order->order_status='11';
        }
        //判断returnType
        if($data['returnType']=='WAREHOUSE_RETURN'){
            $order->return_mobile=$data['contact']['mobile'];
            $order->return_telephone=$data['contact']['telephone'];
            $order->return_email=@$data['contact']['email'];
            $order->return_name1=$data['contact']['name1'];
            $order->return_name2=@$data['contact']['name2'];
            $order->return_street1=$data['contact']['street1'];
            $order->return_street2=@$data['contact']['street2'];
            $order->return_country_code=$data['contact']['countryCode'];
            $order->return_city=$data['contact']['city'];
            $order->return_postal_code=$data['contact']['postalCode'];
            $order->return_state_region_code=$data['contact']['stateRegionCode'];
    
        }
        $order->save();
        //收入
        $shou='删除收入';
        //成本
        $fu='成本';
        foreach ($order->fees as $fee){
        	if($fee->fee_type=='1'){
        		$shou.=$fee->fee_item_code.'*'.$fee->quantity.';';
        	}else{
        		$fu.=$fee->fee_item_code.'*'.$fee->quantity.';';
        	}
        }
        //没有核查的费用都删除
        if(!$order->warehouse_confirm_time){
	        if(strlen($shou)>8){
	        	$log=new OrderLog();
	        	$log->order_id=$order->order_id;
	        	$log->comment=$fu=='成本'?$shou:$shou.$fu;
	        	$log->save();
	        }
	        //删除所有相关费用
	        Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
        }
        //写入一条退件记录
//         $now='RETURN'.date('Ym');
//         $seq = Helper_Seq::nextVal ( $now );
//         if ($seq < 1) {
//             Helper_Seq::addSeq ( $now );
//             $seq = 1;
//         }
//         $seq=str_pad($seq,4,"0",STR_PAD_LEFT);
//         $return_parcel_no='R'.date('Ym').$seq;
//         $order_return= new Orderreturn();
//         $order_return->changeProps(array(
//             'ali_order_no'=>$order->ali_order_no,
//             'return_no'=>$return_parcel_no,
//             'return_status'=>'1',
//             'return_operator'=>'阿里',
//             'consignee_name'=>$data['returnType']=='WAREHOUSE_RETURN'?$data['contact']['name1']:'',
//             'consignee_phone'=>$data['returnType']=='WAREHOUSE_RETURN'?$data['contact']['mobile']:'',
//             'consignee_address'=>$data['returnType']=='WAREHOUSE_RETURN'?$data['contact']['street1']:'',
//             'express_no'=>'',
//             'express_company'=>'',
//         ));
//         $order_return->save();
        //返回结果
        return json_encode(array (
            'isSuccess' => true,'message' => ''
        ));
    }
    /**
     * Order notifyPaid
     */
    function actionOrdernotifyPaid(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'notifyPaid',
    		'ali_json'=>file_get_contents ( "php://input" )
    	));
    	$ali_json->save();
        QLog::log('API4'.file_get_contents ( "php://input" ));
        $info=json_decode(file_get_contents ( "php://input" ),true);
        QLog::log('API4.1'.$info['notifyPaidDTO']);
        $data=json_decode($info['notifyPaidDTO'],true);
        //判断数据是否存在
        if(!is_array($data) || count($data)<=0){
            return json_encode(array (
                'isSuccess' => false,'message' => '数据为空或格式不正确'
            ));
        }
        if(!isset($info['sign']) || $info['sign']==''){
            return json_encode(array (
                'isSuccess' => false,'message' => '签名校验失败'
            ));
        }
        $order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
        if($order->isNewRecord()){
            return json_encode(array (
                'isSuccess' => false,'message' => '订单不存在'
            ));
        }
        $ali_json->ali_sign=$info['sign'];
        $ali_json->order_id=$order->order_id;
        $ali_json->ali_order_no=$order->ali_order_no;
        $ali_json->save();
        if(!$order->payment_time){
            if($order->order_status=='12'){//如果是已扣件状态，将status_copy状态更改为4，status状态不变
                $order->order_status_copy='4';
                $order->payment_time=time();
                $order->save();
            }else{
                $order->order_status='4';
                $order->payment_time=time();
                $order->save();
            }
            //将入库包裹信息添加到出库包裹表中
            $farout=Faroutpackage::find('order_id=?',$order->order_id)->getOne();
            if($farout->isNewRecord()){
                $farpackages=Farpackage::find('order_id=?',$order->order_id)->getAll();
                if (count($farpackages)==1){
                	$farpackage=Farpackage::find('order_id=?',$order->order_id)->getOne();
                	$faroutpackage=new Faroutpackage(array(
                		'order_id'=>$order->order_id,
                		'far_id'=>$farpackage->far_package_id,
                		'quantity_out'=>$farpackage->quantity,
                		'length_out'=>$farpackage->length,
                		'width_out'=>$farpackage->width,
                		'height_out'=>$farpackage->height,
                		'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
                	));
                	$faroutpackage->save();
                	//定义长宽高数组
                	$verify_array=array($farpackage->length,$farpackage->width,$farpackage->height);
                	rsort($verify_array);
                	if ($verify_array[0]==22&&$verify_array[1]==22&&$verify_array[2]==2.2&&$farpackage->quantity==1){
                		$order->packing_type='PAK';
                		$order->save();
                	}
                }else{
                	foreach ($farpackages as $farpackage){
                		$faroutpackage=new Faroutpackage(array(
                			'order_id'=>$order->order_id,
                			'far_id'=>$farpackage->far_package_id,
                			'quantity_out'=>$farpackage->quantity,
                			'length_out'=>$farpackage->length,
                			'width_out'=>$farpackage->width,
                			'height_out'=>$farpackage->height,
                			'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
                		));
                		$faroutpackage->save();
                	}
                }
            }
        }
        //返回结果
        return json_encode(array (
            'isSuccess' => true,'message' => ''
        ));
    }
    /**
     * Order verifyPickupAddress
     */
    function actionOrderverifyPickupAddress(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'verifyPickupAddress',
    		'ali_json'=>file_get_contents ( "php://input" )
    	));
    	$ali_json->save();
        QLog::log('API5'.file_get_contents ( "php://input" ));
        $info=json_decode(file_get_contents ( "php://input" ),true);
        QLog::log('API5.1'.$info['pickupAddressDTO']);
        $data=json_decode($info['pickupAddressDTO'],true);
        //判断数据是否存在
        if(!is_array($data) || count($data)<=0){
            return json_encode(array (
                'isSuccess' => false,'message' => '数据为空或格式不正确'
            ));
        }
        if(!isset($info['sign']) || $info['sign']==''){
            return json_encode(array (
                'isSuccess' => false,'message' => '签名校验失败'
            ));
        }
        $ali_json->ali_sign=$info['sign'];
        $ali_json->save();
        //判断zip_code位数是否为6位
        if(strlen($data['zip'])!='6'){
            return json_encode(array (
                'isSuccess' => true,'message' => '邮编不在取件范围内','result'=>array('canPickUp'=>false)
            ));
        }
        //查询数据中邮编是否在数据库区间
        if(isset($data['warehouseCode']) && isset($data['serviceCode'])){
        	$zip_code=Zipcode::find('zip_code_low<=? and zip_code_high>=? and warehouse=? and service_code=?',$data['zip'],$data['zip'],$data['warehouseCode'],$data['serviceCode'])->getOne();
        }else{
        	$zip_code=Zipcode::find('zip_code_low<=? and zip_code_high>=?',$data['zip'],$data['zip'])->getOne();
        }
        
        if($zip_code->isNewRecord()){//不可揽收
            return json_encode(array (
                'isSuccess' => true,'message' => '邮编不在取件范围内','result'=>array('canPickUp'=>false)
            ));
        }else{//可揽收
            //返回结果
            return json_encode(array (
                'isSuccess' => true,'message' => '','result'=>array('canPickUp'=>true)
            ));
        }
    }
    
    /**
     * notifyFormException：备案单证异常通知
     */
    function actionNotifyFormException(){
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'notifyFormException',
    		'ali_json'=>file_get_contents ( "php://input" )
    	));
    	$ali_json->save();
    	QLog::log('API6'.file_get_contents ( "php://input" ));
    	$info=json_decode(file_get_contents ( "php://input" ),true);
    	QLog::log('API6.1'.$info['notifyFormExceptionDTO']);
    	$data=json_decode($info['notifyFormExceptionDTO'],true);
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '数据为空或格式不正确'
    		));
    	}
    	if(!isset($info['sign']) || $info['sign']==''){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '签名校验失败'
    		));
    	}
    	$order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
    	if($order->isNewRecord()){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '订单不存在'
    		));
    	}
    	$ali_json->ali_sign=$info['sign'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	//保存备案单证异常通知信息
    	$order->ali_form_exception_info=$data['exceptionMessage'];
    	$order->save();
    	//返回结果
    	return json_encode(array (
    		'isSuccess' => true,'message' => ''
    	));
    }
    /**
	 * @todo   notifyInfo 通⽤信息下发服务商接⼝
	 * @author stt
	 * @since  2021年1月19日11:12:13
	 * @link   #82496
	 */
    function actionnotifyInfo(){
    	$notifyinfodata = file_get_contents ( "php://input" );
    	//$notifyinfodata = Helper_Ceshidata::notifyInfodata();
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'notifyInfo',
    		'ali_json'=>$notifyinfodata
    	));
    	$ali_json->save();
    	//QLog::log('API 4.7'.$notifyinfodata);
    	$info=json_decode($notifyinfodata,true);
    	//QLog::log('API 4.7'.$info['notifyInfoDTO']);
    	$data=json_decode($info['notifyInfoDTO'],true);
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '数据为空或格式不正确'
    		));
    	}
    	//判断签名
    	if(!isset($info['sign']) || $info['sign']==''){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '签名校验失败'
    		));
    	}
    	//判断订单是否存在
    	$order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
    	if($order->isNewRecord()){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '订单不存在'
    		));
    	}
    	$ali_json->ali_sign=$info['sign'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	//请求重量 复核
    	if ($data['notifyInfoCode'] == 'RE_CHECK_WEIGHT'){
    		//发送照片
    		Helper_Common::sendcheckweightphoto($order);
    	}
    	//返回结果
    	return json_encode(array (
    		'isSuccess' => true,'message' => ''
    	));
    }
    /**
     * 用于添加测试订单
     */
    function actionAddOrder(){
    	$data=json_decode(file_get_contents ( "php://input" ),true);
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '订单数据为空或格式不正确'
    		));
    	}
    	if(!isset($data['aliOrderNo'])){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '阿里订单号不存在'
    		));
    	}
    	//判断ali订单号是否已存在
    	$order=Order::find('ali_order_no=?',$data['aliOrderNo'])->getOne();
    	if(!$order->isNewRecord()){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '订单已存在'
    		));
    	}
    	$conn = QDB::getConn ();
    	$conn->startTrans ();
    	$now='FAREX'.date('ym');
    	$seq = Helper_Seq::nextVal ( $now );
    	if ($seq < 1) {
    		Helper_Seq::addSeq ( $now );
    		$seq = 1;
    	}
    	$seq=str_pad($seq,6,"0",STR_PAD_LEFT);
    	$far_no=$now.$seq.'YQ';
    	if($data['consignee']['name1']==$data['consignee']['name2']){
    		$data['consignee']['name2']='';
    	}
    	//将数据存入数据库
    	$order->ali_order_no=$data['aliOrderNo'];
    	$order->reference_no=$data['referenceNo'];
    	$order->far_no=$far_no;
    	$order->service_code=$data['serviceCode'];
    	$order->sender_mobile=$data['consignor']['mobile'];
    	$order->sender_telephone=$data['consignor']['telephone'];
    	$order->sender_email=$data['consignor']['email'];
    	$order->sender_name1=$data['consignor']['name1'];
    	$order->sender_name2=$data['consignor']['name2'];
    	$order->sender_street1=$data['consignor']['street1'];
    	$order->sender_street2=$data['consignor']['street2'];
    	$order->sender_country_code=$data['consignor']['countryCode'];
    	$order->sender_city=$data['consignor']['city'];
    	$order->sender_postal_code=$data['consignor']['postalCode'];
    	$order->sender_state_region_code=$data['consignor']['stateRegionCode'];
    	$order->consignee_mobile=$data['consignee']['mobile'];
    	$order->consignee_telephone=$data['consignee']['telephone'];
    	$order->consignee_email=$data['consignee']['email'];
    	$order->consignee_name1=$data['consignee']['name1'];
    	$order->consignee_name2=$data['consignee']['name2'];
    	$order->consignee_street1=$data['consignee']['street1'];
    	$order->consignee_street2=$data['consignee']['street2'];
    	$order->consignee_country_code=$data['consignee']['countryCode'];
    	$order->consignee_city=$data['consignee']['city'];
    	$order->consignee_postal_code=$data['consignee']['postalCode'];
    	$order->consignee_state_region_code=$data['consignee']['stateRegionCode'];
    	$order->declaration_type=$data['customsDeclaration']['declarationType'];
    	$order->total_amount=$data['customsDeclaration']['totalAmount'];
    	$order->currency_code=$data['customsDeclaration']['currencyCode'];
    	$order->need_insurance=$data['needInsurance'];
    	$order->tax_payer_id=$data['taxpayerId'];
    	$order->remarks=$data['remarks'];
    	$order->order_status='1';
    	$order->need_pick_up=$data['needPickUp'];
    	$order->warehouse_code=$data['warehouse']['code'];
    	$order->warehouse_name=$data['warehouse']['name'];
    	$order->save();
    	//存入product信息
    	$flag=false;//判断阿里推送订单是否为测试订单
    	foreach ($data['products'] as $order_product){
    		$product=new Orderproduct();
    		$product->changeProps(array(
    			'order_id'=>$order->id(),
    			'product_name'=>$order_product['productName'],
    			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
    			'product_name_en'=>preg_replace('/[　\s]+/u',' ',$order_product['productNameEn']),
    			'product_quantity'=>$order_product['productQuantity'],
    			'product_unit'=>$order_product['productUnit'],
    			'hs_code'=>$order_product['hsCode'],
    			'declaration_price'=>$order_product['declarationPrice'],
    			'has_battery'=>$order_product['hasBattery'],
    		));
    		$product->save();
    		if($order_product['productName']=='系统测试订单'){
    			$flag=true;
    		}
    	}
    	if($flag){//是测试订单
    		$order->ali_testing_order='1';
    		$order->save();
    	}
    	$ratio=5000;
    	$total_weight=0;
    	$weight_income_ali=0;
    	//查询product获取计泡系数
    	$product=Product::find('product_name=?',$data['serviceCode'])->getOne();
    	if(!$product->isNewRecord()){
    		$ratio=$product->ratio;
    	}
    	//存入package信息
    	foreach ($data['packages'] as $order_package){
    		$package=new Orderpackage();
    		$package->changeProps(array(
    			'order_id'=>$order->id(),
    			'package_type'=>$order_package['packageType'],
    			'quantity'=>$order_package['quantity'],
    			'unit'=>$order_package['unit'],
    			'length'=>$order_package['length'],
    			'width'=>$order_package['width'],
    			'height'=>$order_package['height'],
    			'weight'=>$order_package['weight'],
    			'weight_unit'=>$order_package['weightUnit'],
    		));
    		$package->save();
    		$total_weight+=$order_package['weight']*$order_package['quantity'];
    		$weight_income_ali+=(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']>$order_package['weight']*$order_package['quantity']?
    		(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']:$order_package['weight']*$order_package['quantity'];
    	}
    	//存入阿里计费重量和阿里实重
    	$order->weight_actual_ali=$total_weight;
    	$order->weight_income_ali=$weight_income_ali;
    	$order->save();
    	$conn->completeTrans ();
    	//返回泛远单号
    	return json_encode(array (
    		'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
    	));
    }
    
    /*
     * 接受DWS数据
     */
    function actiondwsdata(){
        $data=json_decode(file_get_contents ( "php://input" ),true);
        QLog::log('dws数据:'.json_encode($data));
        //判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '订单数据为空或格式不正确'
    		));
    	}
    	if(!isset($data['barcode']) || empty($data['barcode'])){
    		return json_encode(array (
    			'isSuccess' => false,'message' => '单号不存在'
    		));
    	}
    	if(strpos($data['barcode'],'-')){
    	    $barcode=explode('-', $data['barcode']);
    	    $barcode=$barcode[0];
    	}else {
    	    $barcode=$data['barcode'];
    	}
        $order=Order::find();
    	$count1 = Order::find ( 'ali_order_no = ?', $barcode )->getCount ();
    	if($count1==1){
    	    $order=$order->where('ali_order_no = ?', $barcode)->getOne();
    	}else{
    	    $alireference=Alireference::find('reference_no=?', $barcode)->getOne();
    	    if($alireference->isNewRecord()){
    	        $order=$order->where( '1!=1' )->getOne();
    	    }else{
    	        $count2=Alireference::find('reference_no=?', $barcode)->getCount ();
    	        if($count2 > 1){
    	            return json_encode(array (
    	                'isSuccess' => false,'message' => '请拆包'
    	            ));
    	        }elseif ($count2==1){
    	            $count3=Alireference::find('order_id=?', $alireference->order_id)->getCount ();
    	            if($count3 > 1){
    	                return json_encode(array (
    	                    'isSuccess' => false,'message' => '请合并包裹'
    	                ));
    	            }
    	            $order=$order->where( 'reference_no = ? and order_status !="2"', $barcode )->getOne();
    	        }
    	    }
    	}
    	if ($order->isNewRecord ()) {
    	    return json_encode(array (
    	        'isSuccess' => false,'message' => '单号错误，或包裹数据不存在'
    	    ));
    	}
    	$err_status = array (
    	    2 => '已取消',
    	    3 => '已退货',
    	    4 => '已付款',
    	    5 => '已入库',
    	    6 => '已打印',
    	    7 => '已出库',
    	    8 => '已提取',
    	    9 => '已签收',
    	    10 => '已查验',
    	    11 => '待退货',
    	    12 => '扣件',
    	    13 => '已结束'
    	);
    	if (array_key_exists ( $order->order_status, $err_status )) {
    	    $order->dwsremarks='订单状态为【' . $err_status [$order->order_status] . '】';
    	    $order->save();
    	    return json_encode(array (
    	        'isSuccess' => false,'message' => '订单状态为【' . $err_status [$order->order_status] . '】'
    	    ));
    	}
    	$product = Product::find('product_name=?',$order->service_code)->getOne();
    	//检查应收燃油
    	if($product->check_fuel=='1'){
    	    $productfuel = Productfuel::find ( "product_id = ?", $product->product_id )
    	    ->where("effective_date <= ? and fail_date >= ?",time(),time())->getOne ();
    	    if($productfuel->isNewRecord()){
    	        $order->dwsremarks='没有设置燃油';
    	        $order->save();
    	        return json_encode(array (
    	            'isSuccess' => false,'message' => '没有设置燃油'
    	        ));
    	    }
    	}
    	//检查无服务邮编
    	if($product->check_zip=='1'){
    	    $noservice_zip = Noserivcezipcode::find("zip_code = ? and service_code = ? and city = '' and country_code = ''",$order->consignee_postal_code,$order->service_code)->getOne();
    	    if(!$noservice_zip->isNewRecord()){
    	        $order->dwsremarks='邮编无服务';
    	        $order->save();
    	        return json_encode(array (
    	            'isSuccess' => false,'message' => '邮编无服务'
    	        ));
    	    }
    	    $noservice_city = Noserivcezipcode::find("city = ? and service_code = ? and zip_code = '' and country_code = ''",$order->consignee_city,$order->service_code)->getOne();
    	    if(!$noservice_city->isNewRecord()){
    	        $order->dwsremarks='城市无服务';
    	        $order->save();
    	        return json_encode(array (
    	            'isSuccess' => false,'message' => '城市无服务'
    	        ));
    	    }
    	    $noservice_city = Noserivcezipcode::find("country_code = ? and service_code = ? and zip_code = '' and city = ''",$order->consignee_country_code,$order->service_code)->getOne();
    	    if(!$noservice_city->isNewRecord()){
    	        $order->dwsremarks='国家无服务';
    	        $order->save();
    	        return json_encode(array (
    	            'isSuccess' => false,'message' => '国家无服务'
    	        ));
    	    }
    	    $noservice_zip = Noserivcezipcode::find("zip_code = ? and city = ? and service_code = ? and country_code = ''",$order->consignee_postal_code,$order->consignee_city,$order->service_code)->getOne();
    	    if(!$noservice_zip->isNewRecord()){
    	    	$order->dwsremarks='城市邮编无服务';
    	    	$order->save();
    	    	return json_encode(array (
    	    		'isSuccess' => false,'message' => '城市邮编无服务'
    	    	));
    	    }
    	    $noservice_zip = Noserivcezipcode::find("zip_code = ? and country_code = ? and service_code = ? and city = ''",$order->consignee_postal_code,$order->consignee_country_code,$order->service_code)->getOne();
    	    if(!$noservice_zip->isNewRecord()){
    	    	$order->dwsremarks='国家邮编无服务';
    	    	$order->save();
    	    	return json_encode(array (
    	    		'isSuccess' => false,'message' => '国家邮编无服务'
    	    	));
    	    }
    	    $noservice_zip = Noserivcezipcode::find("city = ? and country_code = ? and service_code = ? and zip_code = ''",$order->consignee_city,$order->consignee_country_code,$order->service_code)->getOne();
    	    if(!$noservice_zip->isNewRecord()){
    	    	$order->dwsremarks='国家城市无服务';
    	    	$order->save();
    	    	return json_encode(array (
    	    		'isSuccess' => false,'message' => '国家城市无服务'
    	    	));
    	    }
    	    $noservice_zip = Noserivcezipcode::find("zip_code = ? and city = ? and country_code = ? and service_code = ?",$order->consignee_postal_code,$order->consignee_city,$order->consignee_country_code,$order->service_code)->getOne();
    	    if(!$noservice_zip->isNewRecord()){
    	    	$order->dwsremarks='国家城市邮编无服务';
    	    	$order->save();
    	    	return json_encode(array (
    	    		'isSuccess' => false,'message' => '国家城市邮编无服务'
    	    	));
    	    }
    	}
    	$verify_weight=$data['weight'];
        $verify_length=$data['length'];
        $verify_width=$data['width'];
        $verify_height=$data['height'];
        	//定义长宽高数组
        $verify_array=array($verify_length,$verify_width,$verify_height);
        sort($verify_array);
        //获取最大边长度
        $verify_max=$verify_array[2];
        if($order->service_code=='EMS-FY'){
            //A1.最长边≤1.05米，A2.最长边+2*（宽+高）≤2.5米，A3.除AU之外的其它国家，包裹计费重量不超过30KG  A4.AU不超过20KG
    	    if($verify_max > 105){
    	        $order->dwsremarks='最长边超过105厘米';
    	        $order->save();
    	        return json_encode(array (
    	            'isSuccess' => false,'message' => '最长边超过105厘米'
    	        ));
    	    }else{
    	        if(($verify_array[0]+$verify_array[1])*2+$verify_array[2]> 250){
    	            $order->dwsremarks='最长边+2*（宽+高）超过250厘米';
    	            $order->save();
    	            return json_encode(array (
    	                'isSuccess' => false,'message' => '最长边+2*（宽+高）超过250厘米'
    	            ));
    	        }else{
    	            if($order->consignee_country_code=="AU"){
    	                if($verify_weight > 20){
    	                    $order->dwsremarks='单件实际重量超过20kg';
    	                    $order->save();
    	                    return json_encode(array (
    	                        'isSuccess' => false,'message' => '单件实际重量超过20kg'
    	                    ));
    	                }
    	            }else{
    	                if($verify_weight > 30){
    	                    $order->dwsremarks='单件实际重量超过30kg';
    	                    $order->save();
    	                    return json_encode(array (
    	                        'isSuccess' => false,'message' => '单件实际重量超过30kg'
    	                    ));
    	                }
    	            }
    	        }
    	    }
    	}else {
            //当单件实际重量超过68kg,或者最长边超过243厘米，或者最长边+2*（宽+高）超过298厘米时，预报播报：不提供服务。
    	    //判断单件实际重量是否超过68KG
    	    if($verify_weight > 68){
    	        $order->dwsremarks='单件实际重量超过68kg';
    	        $order->save();
    	        return json_encode(array (
                    'isSuccess' => false,'message' => '单件实际重量超过68kg'
                ));
    	    }else{
    	        if($verify_max > 243){
    	            $order->dwsremarks='最长边超过243厘米';
    	            $order->save();
    	            return json_encode(array (
                        'isSuccess' => false,'message' => '最长边超过243厘米'
                    ));
    	        }else{
    	            if(($verify_array[0]+$verify_array[1])*2+$verify_array[2]> 298){
    	                $order->dwsremarks='最长边+2*（宽+高）超过298厘米';
    	                $order->save();
    	                return json_encode(array (
    	                    'isSuccess' => false,'message' => '最长边+2*（宽+高）超过298厘米'
    	                ));
    	            }
    	        }
    	    }
    	}
    	Farpackage::meta()->destroyWhere('barcode = ?',$data['barcode']);
    	$quantity_far=Farpackage::find('order_id = ?',$order->order_id)->getSum('quantity');
    	$quantity_order=Orderpackage::find('order_id = ?',$order->order_id)->getSum('quantity');
    	//保存dws数据
    	if($quantity_far < $quantity_order){
    	    $package=new Farpackage();
    		$package->changeProps(array(
    			'order_id'=>$order->order_id,
    			'quantity'=>'1',
    		    'barcode'=>$data['barcode'],
    			'length'=>$data['length'],
    			'width'=>$data['width'],
    			'height'=>$data['height'],
    			'weight'=>$data['weight']
    		));
    		$package->save();
    	}else {
    	    $order->dwsremarks='包裹总数超出预报包裹总数';
    	    $order->save();
    	    return json_encode(array (
    	        'isSuccess' => false,'message' => '包裹总数超出预报包裹总数'
    	    ));
    	}
    	//计算重量、费用
    	if($quantity_far+1 == $quantity_order){
    	    $is_jipao = 0;
			$total_weight_income_in=0;//计费重
			$total_weight_actual_in = 0; // 实重总重
			$farpackage=Farpackage::find('order_id = ?',$order->order_id)->asArray()->getAll();
			foreach ($farpackage as $f){
			    $jipao=0;
			    $length=$f['length'];
			    $width=$f['width'];
			    $height=$f['height'];
			    $weight=$f['weight'];
			    $quantity=$f['quantity'];
			    $max=max($length,$width,$height);
			    if($max<60 && $order->service_code=="EMS-FY"){
			        $jipao=0;
			    }else{
			        // 计泡 : 长 x 宽 x 高 / 计泡系数
			       $jipao =round(($length* $width* $height) / $order->service_product->ratio*100)/100;
			    }
			    if ($jipao > $weight) {
			        $is_jipao = 1;
			        $total_weight_income_in +=($jipao >20 && $order->service_code=='Express_Standard_Global') ? ceil($jipao)* $quantity: ceil($jipao/0.5)*0.5* $quantity;
			    }else {
			        $total_weight_income_in +=($weight>20 && $order->service_code=='Express_Standard_Global') ? ceil($weight) * $quantity:ceil($weight/0.5)*0.5*$quantity;
			    }
			    $total_weight_actual_in += $weight * $quantity;
			}
			$order->volumn_chargeable = $is_jipao;
			$order->weight_income_in = ($total_weight_income_in>20 && $order->service_code=='Express_Standard_Global') ? ceil($total_weight_income_in):$total_weight_income_in;
			$order->weight_actual_in = $total_weight_actual_in;
			$order->save ();
			$fee_item_code = Helper_Array::toHashmap ( FeeItem::find ()->setColumns ( 'item_code,sub_code,item_name' )
			    ->asArray ()
			    ->getAll (), 'item_code' );
			$quote = new Helper_Quote ();
			$receivable = $quote->receivable ( $order, $order->weight_income_in);
			if(!count($receivable)){
			    $order->dwsremarks='无法计算价格';
			    $order->save();
			    return json_encode(array (
			        'isSuccess' => false,'message' => '无法计算价格'
			    ));
			}
			Fee::meta ()->deleteWhere ( 'fee_type=1 and order_id=?', $order->order_id );
			QLog::log ( print_r ( $receivable, true ) );
			foreach ( $receivable as $key => $value ) {
			    if ($value ['fee']) {
			    	//币种
			    	$currency_code = 'CNY';
			    	$rate = 1;
			    	if(@$value['currency_code']){
			    		$currency_code = $value['currency_code'];
			    		$rate = $value['rate'];
			    	}
			        $fee = new Fee ( array (
			            'order_id' => $order->order_id,
			            'fee_type' => 1,
			            'fee_item_code' => $fee_item_code [$key] ['sub_code'],
			            'fee_item_name' => $fee_item_code [$key] ['item_name'],
			            'quantity' => $value ['quantity'],
			        	'amount' => $value ['fee'],
			        	'currency'=>$currency_code,
			        	'rate'=>$rate,
			        	'btype_id' => $order->customer_id
			        ) );
			        $fee->save ();
			    }
			}
			$order->order_status = '5'; // 5 入库
			if(in_array($order->service_code,array("EMS-FY","WIG-FY","EUUS-FY","US-FY","OCEAN-FY"))){
			    $order->add_data_status='1';
			}
			$order->far_warehouse_in_time=time();
			$order->save ();
    	}
    	return json_encode(array (
    		'isSuccess' => true,'message' => '' ,'result'=>array('barcode'=>$data['barcode'])
    	));
    }

    /**
     * @todo   接受菜鸟订单
     * @author 许杰晔
     * @since  2020-8-17 10:32:54
     * @param
     * @return
     * @link   #81740
     */
    function actionCaiNiaoBook(){
    	
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$res=file_get_contents ( "php://input" );
    	//$res="data_body=%3C%3Fxml+version%3D%221.0%22+encoding%3D%22utf-8%22%3F%3E%0A%3Cesc%3E%0A++%3Chead%3E%0A++++%3CmessageId%3E19055131416%3C%2FmessageId%3E%0A++++%3CmessageTime%3E2020-08-18+17%3A44%3A31+GMT%2B08%3A00%3C%2FmessageTime%3E%0A++++%3CmessageWay%3Erequest%3C%2FmessageWay%3E%0A++++%3Csender%3EALIBABA%3C%2Fsender%3E%0A++++%3Cversion%3E1.0%3C%2Fversion%3E%0A++++%3CserviceName%3EorderPayment%3C%2FserviceName%3E%0A++%3C%2Fhead%3E%0A++%3Cbody%3E%0A++++%3CorderId%3E20013118010%3C%2ForderId%3E%0A++++%3CserviceType%3EEXPRESS%3C%2FserviceType%3E%0A++++%3CpaySummary%3E%0A++++++%3Camount%3E681.130000%3C%2Famount%3E%0A++++++%3Ccurrency%3ECNY%3C%2Fcurrency%3E%0A++++++%3CpaymentMethod%3EALIPAY_ONLINE%3C%2FpaymentMethod%3E%0A++++++%3Cpayer%3ESUPPLIER%3C%2Fpayer%3E%0A++++++%3CpayTime%3E2020-08-18+17%3A44%3A31+GMT%2B08%3A00%3C%2FpayTime%3E%0A++++++%3CpayeeAccount%3Ewlsellers%40alibaba-inc.com%3C%2FpayeeAccount%3E%0A++++%3C%2FpaySummary%3E%0A++%3C%2Fbody%3E%0A%3C%2Fesc%3E&service=orderPayment&_aop_signature=DDE2A52FF07CB17D3CF1DED0B0EAA8BCB4CF6C6E";
    	//$header=getallheaders();
    	//QLog::log('header:'.$header['_aop_signature']);
    	//return 'ok';
    	 $ali_json->changeProps(array(
    		//'api_name'=>'booking',
    		'ali_json'=>$res
    	));
    	$ali_json->save(); 
    	QLog::log('CAINIAO1'.$res);
    	$res = explode('&', $res);
    	$res = substr($res[0], 10);
    	$res = urldecode($res);
    	$str = json_decode(json_encode(helper_xml::xmlparse($res)),true);
    	//QLog::log('API1.1'.$info['bookingOrderDTO']);
    	$head=$str['head'];
    	$data=$str['body'];
    	//print_r($head);exit;
    	if($head['serviceName']=='orderPayment'){
    		$ali_json->api_name='notifyPaid';
    		$ali_json->save(); 
    		$result=self::caiNiaoNotifyPaid($str,$ali_json);
    		return $result;
    	}elseif($head['serviceName']=='cancelOrder'){
    		$ali_json->api_name='cancel';
    		$ali_json->save(); 
    		$result=self::caiNiaoCancel($str,$ali_json);
    		return $result;
    	}elseif($head['serviceName']=='orderForecast'){   
    		$ali_json->api_name='booking';
    		$ali_json->save(); 
	    	if(!is_array($data) || count($data)<=0){   		
	    		return self::failure('orderForecast','100100201','订单数据为空或格式不正确');
	    	}
	    	/* if(!isset($info['sign']) || $info['sign']==''){
	    		return json_encode(array (
	    			'isSuccess' => false,'message' => '签名校验失败'
	    		));
	    	} */
	    	if(!isset($data['orderId'])){
	    		return self::failure('orderForecast','100100202','阿里订单号不存在');
	    	}
	    	//判断ali订单号是否已存在
	    	$order=Order::find('ali_order_no=?',$data['orderId'])->getOne();
	    	if(!$order->isNewRecord()){
	    		return self::failure('orderForecast','200000101','阿里订单号已存在');
	    	}
	    	$conn = QDB::getConn ();
	    	$conn->startTrans ();
	    	$now='FAREX'.date('ym');
	    	$seq = Helper_Seq::nextVal ( $now );
	    	if ($seq < 1) {
	    		Helper_Seq::addSeq ( $now );
	    		$seq = 1;
	    	}
	    	$far_no=$now.sprintf("%06d",$seq).'YQ';
	    	
	    	//         if($data['consignee']['name1']==$data['consignee']['name2']){
	    	//             $data['consignee']['name2']='';
	    	//         }
	    	//判断取件网点
	    	/* $pick_company='';
	    	if(isset($data['needPickUp']) && $data['needPickUp']){
	    		$package_pre_weight = 0;
	    		$package_act_weight = 0;
	    		foreach ($data['packages'] as $packageinfo){
	    			$package_pre_weight += $packageinfo['quantity']*$packageinfo['length']*$packageinfo['width']*$packageinfo['height']/5000;
	    			$package_act_weight += $packageinfo['quantity']*$packageinfo['weight'];
	    		}
	    		$tmp_weight = $package_pre_weight>$package_act_weight?$package_pre_weight:$package_act_weight;
	    		$zip=Zipcode::find('zip_code_low<=? and zip_code_high>=? and service_code =?',$data['consignor']['postalCode'],$data['consignor']['postalCode'],$data['serviceCode'])->getOne();
	    		if(!$zip->isNewRecord()){
	    			//义乌和杭州 先根据取件邮编匹配之后再进行不超过3KG的订单分配到平台的判断，其余包裹，按邮编进行分配到各网点。
	    			$pick_company_tmp=$zip->pick_company;//临时存储取件地区
	    			$pick_company_array=array('杭分','义乌分','青岛仓');
	    			if(in_array($pick_company_tmp,$pick_company_array)){
	    				$pick_company=$zip->pick_company;
	    			}else {
	    				if($tmp_weight >= 3){
	    					$pick_company=$zip->pick_company;
	    				}else{
	    					$pick_company="平台";
	    				}
	    			}
	    			if($data['serviceCode']=='EUUS-FY'){
	    				$pick_company="平台";
	    			}
	    			if($data['serviceCode']=='US-FY' && $zip->pick_company<>'青岛仓'){
	    				$pick_company="平台";
	    			}
	    		}
	    	} */
	    	//数据过滤
	    	//地址1、地址2、城市、省州、邮编 这四个信息里：如果非英文符号，自动转换成相应的英文符号。中文全角“。”更新成"." ; “，”更新成","
	    	$data['contactInfo']['consignee']['phoneNumber'] = self::convertStrType($data['contactInfo']['consignee']['phoneNumber']);
	    	$data['contactInfo']['consignee']['address'] = self::convertStrType($data['contactInfo']['consignee']['address']);
	    	$data['contactInfo']['consignee']['city'] = self::convertStrType($data['contactInfo']['consignee']['city']);
	    	$data['contactInfo']['consignee']['province'] = self::convertStrType($data['contactInfo']['consignee']['province']);
	    	$data['contactInfo']['consignee']['zip'] = self::convertStrType($data['contactInfo']['consignee']['zip']);
	    	//收件人电话只保留纯数字：电话里有中英文中杠“—”“-”或者下划线时，或者空格时，自动去掉
	    	$data['contactInfo']['consignee']['phoneNumber'] = preg_replace('/[^\d]/','',$data['contactInfo']['consignee']['phoneNumber']);
	    	//收件人、收件人公司 这两个信息里，不能有英文空格之外的标点符号，有直接用空格替代
	    	$data['contactInfo']['consignee']['name'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['contactInfo']['consignee']['name']);
	
	    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里：如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
	    	$data['contactInfo']['consignee']['name'] = preg_replace('/[　\s]+/u',' ',$data['contactInfo']['consignee']['name']);
	    	$data['contactInfo']['consignee']['address'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['consignee']['address']);
	    	$data['contactInfo']['consignee']['city'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['consignee']['city']);
	    	$data['contactInfo']['consignee']['province'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['consignee']['province']);
	    	$data['contactInfo']['consignee']['zip'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['consignee']['zip']);
	    	$data['contactInfo']['shipper']['name'] = preg_replace('/[　\s]+/u',' ',$data['contactInfo']['shipper']['name']);
	    	$data['contactInfo']['shipper']['address'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['shipper']['address']);
	    	$data['contactInfo']['shipper']['city'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['shipper']['city']);
	    	$data['contactInfo']['shipper']['province'] = preg_replace('/[　\s]+/',' ',$data['contactInfo']['shipper']['province']);
	    	//将数据存入数据库
	    	//         $comment = '';
	    	//         $sender_company = Contact::find('sender_company like ?','%'.$data['consignor']['name1'].'%')->getOne();
	    	//         if($sender_company->isNewRecord()){
	    	//            if(isset($data['consignor']['name2'])&&!empty($data['consignor']['name2'])){
	    	//               $sendercompany = Contact::find('sender_company like ?','%'.$data['consignor']['name2'].'%')->getOne();
	    	//               if(!$sendercompany->isNewRecord()){
	    	//                  $comment = $sendercompany->comment;
	    	//               }
	    	//            }
	    	//         }else{
	    	//            $comment = $sender_company->comment;
	    	//         }
	    	$order->ali_order_no=$data['orderId'];
	    	$order->reference_no= $data['serviceList']['domesticTransportation']['logisticsInfoList']['logisticsInfo']['logisticsNo'];
	    	$order->far_no=$far_no;
	    	$order->service_code=$data['serviceList']['internationalExpress']['logisticsCompanyService'];
	    	$order->sender_mobile=$data['contactInfo']['shipper']['mobile'];
	    	$order->sender_telephone=@$data['contactInfo']['shipper']['phoneNumber'];
	    	$order->sender_name1=$data['contactInfo']['shipper']['name'];  
	    	$order->sender_email=$data['email'];
	    	$order->sender_city=$data['contactInfo']['shipper']['city'];  
	    	$order->sender_state_region_code=$data['contactInfo']['shipper']['province'];  
	    	$order->sender_mobile=$data['contactInfo']['shipper']['mobile'];
	    	$order->sender_street1=$data['contactInfo']['shipper']['district'].$data['contactInfo']['shipper']['address'];
	    	$order->sender_country_code='CN';
	
	    	$order->consignee_mobile=@$data['contactInfo']['consignee']['mobile'];
	    	$order->consignee_telephone=@$data['contactInfo']['consignee']['phoneNumber'];
	    	$order->consignee_name1=$data['contactInfo']['consignee']['name'];
	    	$order->consignee_street1=$data['contactInfo']['consignee']['address'];
	    	$order->consignee_country_code=$data['contactInfo']['consignee']['country'];
	    	$order->consignee_city=$data['contactInfo']['consignee']['city'];
	    	$order->consignee_postal_code=$data['contactInfo']['consignee']['zip'];
	    	$order->consignee_state_region_code=$data['contactInfo']['consignee']['province'];	    	
	    	$order->declaration_type=$data['isCustomsClearance']=='Y'?'DL':'QT';	    		    	
	    	$order->total_amount=$data['consignmentInfo']['goodsSummary']['totalDeclaredValue'];
	    	$order->currency_code=$data['consignmentInfo']['goodsSummary']['totalDeclaredCurrency'];
	    	//$order->need_insurance=$data['needInsurance'];
	    	//$order->tax_payer_id=@$data['taxpayerId'];
	    	$order->remarks=@$data['sellerNote'];
	    	//$order->delivery_priority=@$data['deliveryPriority'];
	    	$order->order_status='1';
	    	$order->weight_actual_ali=$data['consignmentInfo']['goodsSummary']['billWeight'];
	    	$order->warehouse_code=$data['serviceList']['warehouseService']['warehouse'];
	    	$order->trade_no=@$data['tradeNo'];
	    	$order->iscustomsclearance=$data['isCustomsClearance'];
	    	$order->customer_id=10;
	    	$order->save();
	    	 //$ali_json->ali_sign=$info['sign'];
	    	$ali_json->order_id=$order->order_id;
	    	$ali_json->ali_order_no=$order->ali_order_no;
	    	$ali_json->save(); 
	    	//港前问题件判断
	    	$detail=array(
	    		'reason'=>array()
	    	);
	    	//EMS专线,当申报总价高于400.00USD时，预警：EMS超400USD
	    	/* if($order->service_code == 'EMS-FY'){
	    		if($order->total_amount>400){
	    			$detail['reason'][]='EMS超400USD';
	    		}
	    	}
	    	//中美专线，订单总价超800USD时，进行预警：超800美金
	    	if($order->service_code == 'US-FY'){
	    		if($order->total_amount>800){
	    			$detail['reason'][]='超800美金';
	    		}
	    	}
	    	//欧美专线，目的地为GB的订单，进行预警：GB订单，需要提供交易凭证
	    	if($order->service_code == 'EUUS-FY'){
	    		if($order->consignee_country_code=='GB'){
	    			$detail['reason'][]='GB订单，需要提供交易凭证';
	    		}
	    	}
	    	//US 检查电话号码是否不足10位
	    	if($order->consignee_country_code=='US'){
	    		if(strlen($order->consignee_mobile)<10){
	    			$detail['reason'][]='US 检查电话号码是否不足10位';
	    		}
	    	}
	    	//BR 检查订单详情里没有提供税号信息
	    	if($order->consignee_country_code=='BR'){
	    		if($order->tax_payer_id==''){
	    			$detail['reason'][]='BR 检查订单详情里没有提供税号信息';
	    		}
	    	}
	    	//订单申报总金额超过700.00USD,且报关方式为：QT的订单。
	    	if($order->total_amount>700 && $order->declaration_type=='QT'){
	    		$detail['reason'][]='订单申报总金额超过700.00USD,且报关方式为：QT的订单';
	    	}
	    	//订单申报总金额低于1.00USD的订单
	    	if($order->total_amount<1){
	    		$detail['reason'][]='订单申报总金额低于1.00USD的订单';
	    	}
	    	//订单申报方式为：DL
	    	if($order->declaration_type=='DL'){
	    		if($order->service_code=='EMS-FY'){
	    			$detail['reason'][]='EMS不提供报关服务';
	    		}else if($order->service_code == 'US-FY' || $order->service_code == 'EUUS-FY'){
	    			$detail['reason'][]='无报关服务';
	    		}else{
	    			$detail['reason'][]='订单申报方式为：DL';
	    		}
	    	}
	    	//地址1+地址2字符总数超过105的订单
	    	//普货专线和中美专线，地址总字符超过105时，进行预警，EMS不作限制
	    	if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'US-FY'){
	    		if(strlen($order->consignee_street1.' '.$order->consignee_street2)>105){
	    			$detail['reason'][]='地址1加地址2字符总数超过105的订单';
	    		}
	    	}
	    	//假发专线地址1+地址2字符总数超过70的订单
	    	if($order->service_code=='WIG-FY'){
	    		if(strlen($order->consignee_street1.' '.$order->consignee_street2)>70){
	    			$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
	    		}else {
	    			$address=Order::splitAddressfedex($order->consignee_street1.' '.$order->consignee_street2);
	    			if(count($address)>2){
	    				$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
	    			}
	    		}
	    	} */
	    	//收件人公司里，如果有数字进行预警，预警原因：收件公司信息含有数字
	    	/* if($order->consignee_name1){
	    		if(preg_match('/\d/',$order->consignee_name1)){
	    			$detail['reason'][]='收件公司/客户名称1含有数字';
	    		}
	    	}
	    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里如有非英文符，进行预警，预警原因：某某字段有非英文字符。
	    	//地址1、地址2、城市、省州、邮编这五个信息里：英文状态下以下符号不作预警："#" "/" "" "-" "." "," "&" "*" ";" ":" "?" ";" ""
	    	if($order->consignee_name1){
	    		if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$order->consignee_name1)){
	    			$detail['reason'][]='收件公司/客户名称1有非英文字符';
	    		}
	    	}
	
	    	if($order->consignee_street1){
	    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_street1)){
	    			$detail['reason'][]='收件人地址1有非英文字符';
	    		}
	    	}
	
	    	if($order->consignee_city){
	    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_city)){
	    			$detail['reason'][]='收件人城市有非英文字符';
	    		}
	    	}
	    	if($order->consignee_state_region_code){
	    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_state_region_code)){
	    			$detail['reason'][]='收件人省/州有非英文字符';
	    		}
	    	}
	    	if($order->consignee_postal_code){
	    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_postal_code)){
	    			$detail['reason'][]='收件人邮编有非英文字符';
	    		}
	    	} */
	    	// EMS订单邮编校验
	    	/* if($order->service_code == 'EMS-FY' || $order->service_code == 'ePacket-FY'){
	    		$zipFormat = Zipformat::find('country_code_two = ?',$order->consignee_country_code)->getOne();
	    		if(!$zipFormat->isNewRecord()){
	    			if(!preg_match($zipFormat->zip_format_preg_match, trim($order->consignee_postal_code))){
	    				$detail['reason'][]='收件人邮编格式不正确,'.$order->consignee_country_code.'的邮编格式为：'.$zipFormat->zip_format;
	    			}
	    		}
	    	} */
	    	//查询判断是否疑似偏远
	    	$product=Product::find("product_name=?",$order->service_code)->getOne();
	    	$productppr=Productppr::find('product_id=? and effective_time <= ? and invalid_time >= ?',$product->product_id,time(),time())
	    	->getOne();
	    	$trim = array(' ','-');
	    	if(!$productppr->isNewRecord()){
	    		$remote_city=Remote::find('remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,"") != ""',$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_city,strtolower(str_replace($trim,'',$order->consignee_city)))
	    		->getOne();
	    		if($remote_city->isNewRecord()){//城市不同，省州完全相同
	    			$remote_state = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,'') != '' ",$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_state_region_code,strtolower(str_replace($trim,'',$order->consignee_state_region_code)))->getOne();
	    			if(!$remote_state->isNewRecord()){
	    				$order->suspected_remote='1';
	    				$order->save();
	    			}else{
	    				$remote_like = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city like ? or remote_city like ? or remote_city like ? or remote_city like ?) and ifnull(remote_city,'') != ''",$productppr->remote_manage_id,$order->consignee_country_code,'%'.$order->consignee_city.'%','%'.strtolower(str_replace($trim,'',$order->consignee_city)).'%','%'.$order->consignee_state_region_code.'%','%'.strtolower(str_replace($trim,'',$order->consignee_state_region_code)).'%')->getOne();
	    				if(!$remote_like->isNewRecord()){//订单中的城市、省州在偏远城市信息中出现
	    					$order->suspected_remote='1';
	    					$order->save();
	    				}
	    				$remote=Remote::find("country_code_two = ? and remote_manage_id= ? and ifnull(remote_city,'') != ''",$order->consignee_country_code,$productppr->remote_manage_id)->getAll();
	    				foreach ($remote as $v){//偏远城市信息在订单中的城市、省州、地址中出现
	    					$is_far=Order::find("consignee_state_region_code like ? or consignee_city like ? or consignee_street1 like ? or consignee_street2 like ? ",'%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%')
	    					->where('order_id= ? ',$order->order_id)->getOne();
	    					if(!$is_far->isNewRecord()){
	    						$order->suspected_remote='1';
	    						$order->save();
	    						break;
	    					}
	    				}
	    			}
	    		}
	    	}
	    	if($order->suspected_remote=='1'){
	    		$detail['reason'][]='城市疑似偏远，需人工介入';
	    	}
	    	if (! @$data['consignmentInfo']['goodsDeclarationList']['goodsDeclaration']['0']) {
	    		$line = $data['consignmentInfo']['goodsDeclarationList']['goodsDeclaration'];
	    		$orderline = array(
	    			$line
	    		);
	    	}else{
	    		$orderline=$data['consignmentInfo']['goodsDeclarationList']['goodsDeclaration'];
	    	}
	
	    	$black=blacklist::find('product_id=?',$product->product_id)->asArray()->getAll();
	    	if(count($black)>0){
	    		foreach ($black as $b){
	    			$reason='';
	    			$i=0;
	    			$bb=array_filter($b);
	    			$co=count($bb)-4;
	    			if($b['consignee_country_code'] && strtoupper(trim($order->consignee_country_code)) == strtoupper(trim($b['consignee_country_code']))){
	    				$reason.='国家:'.$b['consignee_country_code'].',';
	    				$i++;
	    			}
	    			if($b['consignee_postal_code'] && strtoupper(trim($order->consignee_postal_code)) ==  strtoupper(trim($b['consignee_postal_code']))){
	    				$reason.='邮编:'.$b['consignee_postal_code'].',';
	    				$i++;
	    			}
	    			if($b['consignee_city'] && strtoupper(trim($order->consignee_city))==strtoupper(trim($b['consignee_city']))){
	    				$reason.='城市:'.$b['consignee_city'].',';
	    				$i++;
	    			}
	    			if($b['consignee_state_region_code'] && strtoupper(trim($order->consignee_state_region_code)) == strtoupper(trim($b['consignee_state_region_code']))){
	    				$reason.='州:'.$b['consignee_state_region_code'].',';
	    				$i++;
	    			}
	    			
	    			if(($b['sender_name1'] && (strtoupper(trim($order->sender_name1)) == strtoupper(trim($b['sender_name1'])) ||  strtoupper(trim($order->sender_name2)) == strtoupper(trim($b['sender_name1']))))){
	    				$reason.='发件人:'.$b['sender_name1'].',';
	    				$i++;
	    			}
	    			
	    			if($b['product_name']){
	    				$j=0;
	    				$condition=str_replace('\\','\\\\',$b['product_name']);
	    				$condition=str_replace('/','\\/',$condition);
	    				
	    				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
	    				
	    				$condition=str_replace('*','.*?',$condition);
	    				foreach ($orderline as $op){
	    					if (preg_match('/^'.$condition.'$/i',$op['nameChinese']) || preg_match('/^'.$condition.'$/i',$op['nameEnglish'])){
	    						$reason.='品名:'.$b['product_name'].',';$j++;
	    					}
	    				}
	    				if($j>0){
	    					$i++;
	    				}
	    			}
	    			if($i > 0 && $i == $co){
	    				$reason=trim($reason,',');
	    				$order->black_flag='1';
	    				$order->black_reason=$reason;
	    				$order->save();
	    				break;
	    			}
	    		}
	    	}
	    	if($order->black_flag=='1'){
	    		$detail['reason'][]='线路:'.$order->service_product->product_chinese_name.','.$order->black_reason.'黑名单';
	    	}
	    	
	    	//拆分阿里单号
	    	if(strlen($order->reference_no)){
	    		$references=explode(",", $order->reference_no);
	    		foreach ($references as $r){
	    			$alireference=new Alireference();
	    			$alireference->order_id=$order->order_id;
	    			$alireference->reference_no=$r;
	    			$alireference->save();
	    		}
	    	}
	    	//存入product信息
	    	$flag=false;//判断阿里推送订单是否为测试订单
	    	$glasses_flag=false;
	    	$battery_flag=false;
	    	$limit_flag=false;
	    	$fda_flag=false;
	    	$all_flag=false;
	    	$mask_flag=false;
	    	foreach ($orderline as $order_product){
	    		$product=new Orderproduct();
	    		$product->changeProps(array(
	    			'order_id'=>$order->id(),
	    			'product_name'=>$order_product['nameChinese'],
	    			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
	    			'product_name_en'=>preg_replace('/[　\s]+/u',' ',$order_product['nameEnglish']),
	    			'product_quantity'=>$order_product['statutoryQuantity'],
	    			//'product_unit'=>$order_product['productUnit'],
	    			'hs_code'=>$order_product['hscode'],
	    			'declaration_price'=>$order_product['unitPrice'],
	    			//'has_battery'=>$order_product['hasBattery'],
	    			'product_name_far'=>$order_product['nameChinese'],
	    			'product_name_en_far'=>preg_replace('/[　\s]+/u',' ',$order_product['nameEnglish']),
	    			'hs_code_far'=>$order_product['hscode'],
	    			'material_use'=>$order_product['material'].' '.$order_product['purpose']
	    		));    		
	    		$product->save();
	    		
	    		/* if($order->consignee_country_code=='US'){
	    			if(strstr($product->product_name_far, '眼镜') || strstr($product->product_name_far, '太阳镜') || strstr($product->product_name, '眼镜') || strstr($product->product_name, '太阳镜')){
	    				$glasses_flag=true;
	    			}
	    			if(strstr($product->product_name_far, '睫毛') || strstr($product->product_name_far, '假睫毛') || strstr($product->product_name, '睫毛') || strstr($product->product_name, '假睫毛')){
	    				$FDA_flag=true;
	    			}
	    		} */
	    	/* 	if($product->has_battery){
	    			$battery_flag=true;
	    		} */
	    		/* if($order->service_code == 'US-FY'){
	    			if(strstr($product->product_name_far, '车灯') || strstr($product->product_name_far, '大灯') || strstr($product->product_name_far, '头盔') || strstr($product->product_name_far, '刀') || strstr($product->product_name_far, '激光') || strstr($product->product_name, '车灯') || strstr($product->product_name, '大灯') || strstr($product->product_name, '头盔') || strstr($product->product_name, '刀') || strstr($product->product_name, '激光')){
	    				$limit_flag=true;
	    			}
	    		}
	    		if($order->service_code != 'CNUS-FY'){
	    			if(strstr($product->product_name_far, '电') || strstr($product->product_name_far, '灯') || strstr($product->product_name_far, '器') || strstr($product->product_name_far, '磁') || strstr($product->product_name, '电') || strstr($product->product_name, '灯') || strstr($product->product_name, '器') || strstr($product->product_name, '磁')){
	    				$all_flag=true;
	    			}
	    		} */
	    		// US 产品品名里含：“眼镜”，“太阳镜”
	    		/* if($glasses_flag){
	    			$detail['reason'][]='US 产品品名里含：“眼镜”，“太阳镜”';
	    		}
	    		//检查订单详情里，有产品“带电”
	    		if($battery_flag){
	    			$detail['reason'][]='检查订单详情里，有产品“带电”';
	    		}
	    		//判断品名含mask的商品sku是否等于6307900010
	    		if(strpos(strtolower($order_product['productNameEn']), 'mask') !== false && $order_product['hsCode']<>'6307900010'){
	    			$mask_flag = true;
	    		} */
	    	}
	    	//判断品名含mask的商品sku是否等于6307900010
	    	/* if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $mask_flag){
	    		$detail['reason'][]='hscode不等于6307900010的mask订单';
	    	} */
	    	//中美专线，如果品名里含有：“车灯”，“大灯”“头盔”“刀”“激光”
	    	/* if($limit_flag){
	    		$detail['reason'][]='疑似限运品';
	    	} */
	    	//普货专线、欧美专线、假发专线，目的地为美国的订单，如果品名里含有：“睫毛”，“假睫毛”
	    	/* if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'EUUS-FY' || $order->service_code == 'WIG-FY'){
	    		if($FDA_flag){
	    			$detail['reason'][]='美国需FDA';
	    		}
	    	} */
	    	//目前所有线路：当品名里含有“电”“灯”“器”“磁”，预警：品名含有#
	    	/* if($all_flag){
	    		$detail['reason'][]='品名含有#';
	    	}
	    	if($flag){//是测试订单
	    		$order->ali_testing_order='1';
	    		$order->save();
	    	} */
	    	$ratio=5000;
	    	$total_weight=0;
	    	$weight_income_ali=0;
	    	//查询product获取计泡系数
	    	$product=Product::find('product_name=?',$order->service_code)->getOne();
	    	if(!$product->isNewRecord()){
	    		$ratio=$product->ratio;
	    	}
	    	//存入package信息
	
	    	$package=new Orderpackage();
	    	$package->changeProps(array(
	    		'order_id'=>$order->id(),
	    		'package_type'=>'BOX',
	    		'quantity'=>$data['consignmentInfo']['goodsSummary']['totalPackageQuantity'],
	    		'unit'=>'CM',
	    		'length'=>'22',
	    		'width'=>'22',
	    		'height'=>'2.2',
	    		'weight'=>'0',
	    		'weight_unit'=>'kg'
	    	));
	    	$package->save();
	    		
	    	$order->package_total_num=$data['consignmentInfo']['goodsSummary']['totalPackageQuantity'];
	    	//#80732仓库代码
	    	$codewarehouses = CodeWarehouse::find()->getAll();
	    	foreach ($codewarehouses as $cw){
	    		if ($data['serviceList']['warehouseService']['warehouse']==$cw->warehouse){
	    			$order->department_id = $cw->department_id;
	    		}
	    	}
	    	$order->save();
	    	if(count($detail['reason'])>0){
	    		//新建问题件
	    		$now = 'ISSUE' . date ( 'Ym' );
	    		$seq = Helper_Seq::nextVal ( $now );
	    		if ($seq < 1) {
	    			Helper_Seq::addSeq ( $now );
	    			$seq = 1;
	    		}
	    		$seq = str_pad ( $seq, 4, "0", STR_PAD_LEFT );
	    		$abnormal_parcel_no = date ( 'Ym' ) . $seq;
	    		$abnormal_parcel = new Abnormalparcel ( array (
	    			'ali_order_no' => $order->ali_order_no,
	    			'abnormal_parcel_no' => $abnormal_parcel_no,
	    			'abnormal_parcel_operator' => '系统',
	    			'issue_type' => '5',
	    			'issue_content' => @implode ( ',', $detail ['reason'] )
	    		) );
	    		$abnormal_parcel->save ();
	    		$history = new Abnormalparcelhistory ();
	    		$history->abnormal_parcel_id = $abnormal_parcel->abnormal_parcel_id;
	    		$history->follow_up_content = @implode ( ',', $detail ['reason'] );
	    		$history->follow_up_operator = '系统';
	    		$history->save ();
	    	}
	    	$conn->completeTrans ();
	    	if(substr($order->ali_order_no, 0, 3) == 'ALS'){/* 
	    		$rule_choose = AutomaticEmailRule::find('product_id = ? and tracking_code = ?',$order->service_product->product_id,'订单创建')->getOne();
	    		if(!$rule_choose->isNewRecord()){
	    			$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
	    			if(!$email_template->isNewRecord()){
	    				$title = $email_template->template_title;
	    				$email_info = $email_template->template_text;
	    				
	    				//标题
	    				$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
	    				$template_title = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_title);
	    				$template_title = preg_replace('/reference_no/',$order->reference_no, $template_title);
	    				$template_title = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_title);
	    				//内容
	    				$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
	    				$template_info = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_info);
	    				$template_info = preg_replace('/reference_no/',$order->reference_no, $template_info);
	    				$template_info = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_info);
	    				QLog::log($template_title);
	    				QLog::log($template_info);
	    				$title = nl2br($template_title);
	    				$msg = nl2br($template_info);
	    				try {
	    					$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
	    					QLog::log($email_response);
	    					if ($email_response == 'email_success') {
	    						$order_log = new OrderLog ();
	    						$order_log->order_id = $order->order_id;
	    						$order_log->staff_name = '系统';
	    						$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
	    						$order_log->save ();
	    					}else {
	    						return json_encode(array (
	    							'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
	    						));
	    					}
	    				} catch ( Exception $e ) {
	    					return json_encode(array (
	    						'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
	    					));
	    				}
	    			}
	    		}
	    	 */}
	    	//返回泛远单号
	    	return self::success('orderForecast');
	    }
    }
    /**
     * @todo   取消菜鸟订单
     * @author 许杰晔
     * @since  2020-8-17 10:32:54
     * @param
     * @return string
     * @link   #81740
     */
    static function caiNiaoCancel($str,$ali_json){
    	//阿里原始信息存入ali_json表中
    	
    	$head=$str['head'];
    	$data=$str['body'];
    	
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return self::failure('cancelOrder','100100201','订单数据为空或格式不正确');
    	}
    	
    	$order=Order::find('ali_order_no=?',$data['orderId'])->getOne();
    	if($order->isNewRecord()){
    		return self::failure('cancelOrder','100100202','订单不存在');
    	}
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	$order->reason_code=$data['reason']['codeList']['code'];
    	//$order->reason_name=$data['reasonName'];
    	$order->reason_remark=$data['reason']['otherText'];
    	$order->order_status="2";
    	$order->save();
    	//删除ali_reference表里的快递号信息
    	Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
    	//删除所有相关费用
    	Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
    	//返回结果
    	return self::success('cancelOrder');
    }
    
    /**
     * @todo   支付通知
     * @author 许杰晔
     * @since  2020-8-17 10:32:54
     * @param
     * @return string
     * @link   #81740
     */
    static function caiNiaoNotifyPaid($str,$ali_json){
    	
    	$head=$str['head'];
    	$data=$str['body'];
  	
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		return self::failure('orderPayment','100100201','订单数据为空或格式不正确');
    	}
    	
    	$order=Order::find('ali_order_no=?',$data['orderId'])->getOne();
    	if($order->isNewRecord()){
    		return self::failure('orderPayment','200000101','订单不存在');
    	}
    	//echo 1111;exit;
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	if(!$order->payment_time){
    		if($order->order_status=='12'){//如果是已扣件状态，将status_copy状态更改为4，status状态不变
    			$order->order_status_copy='4';
    			$order->payment_time=time();
    			$order->save();
    		}else{
    			$order->order_status='4';
    			$order->payment_time=time();
    			$order->save();
    		}
    		//将入库包裹信息添加到出库包裹表中
    		$farout=Faroutpackage::find('order_id=?',$order->order_id)->getOne();
    		if($farout->isNewRecord()){
    			$farpackages=Farpackage::find('order_id=?',$order->order_id)->getAll();
    			if (count($farpackages)==1){
    				$farpackage=Farpackage::find('order_id=?',$order->order_id)->getOne();
    				$faroutpackage=new Faroutpackage(array(
    					'order_id'=>$order->order_id,
    					'far_id'=>$farpackage->far_package_id,
    					'quantity_out'=>$farpackage->quantity,
    					'length_out'=>$farpackage->length,
    					'width_out'=>$farpackage->width,
    					'height_out'=>$farpackage->height,
    					'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
    				));
    				$faroutpackage->save();
    				//定义长宽高数组
    				$verify_array=array($farpackage->length,$farpackage->width,$farpackage->height);
    				sort($verify_array);
    				if ($verify_array[0]==22&&$verify_array[1]==22&&$verify_array[2]==2.2&&$farpackage->quantity==1){
    					$order->packing_type='PAK';
    					$order->save();
    				}
    			}else{
    				foreach ($farpackages as $farpackage){
    					$faroutpackage=new Faroutpackage(array(
    						'order_id'=>$order->order_id,
    						'far_id'=>$farpackage->far_package_id,
    						'quantity_out'=>$farpackage->quantity,
    						'length_out'=>$farpackage->length,
    						'width_out'=>$farpackage->width,
    						'height_out'=>$farpackage->height,
    						'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
    					));
    					$faroutpackage->save();
    				}
    			}
    		}
    	}
    	//返回结果
    	return self::success('orderPayment');
    }
    /**
     * @todo   返回错误信息
     * @author 许杰晔 
     * @since  2020-8-17 10:32:54
     * @param
     * @return string
     * @link   #81740
     */
    static function failure($servicename, $code = '1000', $data = '')
    {	
    	
    	$arr=array(
    		'esc'=>array(
    			'head'=>array(
    				'messageId'=>time(),
    				'messageTime'=>date('Y-m-d H:i:s'),
    				'messageWay'=>'response',
    				'sender'=>644351,
    				'version'=>'1.0',
    				'serviceName'=>$servicename,
    			),
    			'body'=>array(
    				'fault'=>array(
    					'faultCode'=>$code,
    					'faultMessage'=>$data
    				),
    			)
    		),
    	);
    	$xml = Helper_xml::simpleArr2xml ($arr,1,false);
    	return $xml;
    }
    /**
     * @todo   返回成功信息
     * @author 许杰晔
     * @since  2020-8-17 10:32:54
     * @param
     * @return string
     * @link   #81740
     */
    static function success($servicename, $message = '')
    {
    	
    	$arr=array(
    		'esc'=>array(
    			'head'=>array(
    				'messageId'=>time(),
    				'messageTime'=>date('Y-m-d H:i:s'),
    				'messageWay'=>'response',
    				'sender'=>644351,
    				'version'=>'1.0',
    				'serviceName'=>$servicename,
    			),
    			'body'=>array(
    				'success'=>'100000200',
    			)
    		),
    	);
    	
    	$xml = Helper_xml::simpleArr2xml ($arr,1,false);
    	
    	return $xml;
    }
    /**
     * @todo   4pl接受订单
     * @author stt
     * @since  August 19th 2020
     */
    function actionFourplOrderBooking(){
    	$res=file_get_contents ( "php://input" );
    	
    	//自制订单预报测试数据
//     	$flag = request('flag');
//     	$vars = Helper_Ceshidata::zizhidata($flag);
//     	$vars = urldecode($vars);
//     	if ($flag==1){
//     		$msg_type = 'CAINIAO_GLOBAL_SORTINGCENTER_ORDERINFO_NOTIFY';
//     	}elseif ($flag==2){
//     		$msg_type = 'COMMON_ORDER_CANCEL_NOTIFY';
//     	}elseif ($flag==3){
//     		$msg_type = 'COMMON_UNREACHABLE_RETURN_ORDER_INFO_NOTIFY';
//     	}elseif ($flag==4){
//     		$msg_type = 'SORTING_CENTER_OUTBOUND_NOTIFY';
//     	}elseif ($flag==5){
//     		$msg_type = 'notifyFormException';
//     	}
    	
//     	$res = 'logistics_interface=%7B%22parcel%22%3A%7B%22length%22%3A%2211%22%2C%22width%22%3A%222%22%2C%22dimensionUnit%22%3A%22CM%22%2C%22goodsList%22%3A%5B%7B%22priceCurrency%22%3A%22USD%22%2C%22quantity%22%3A%224%22%2C%22cnName%22%3A%22LED%E5%85%89%E6%9D%A1%22%2C%22material%22%3A%22%E6%B2%89%E9%A6%99%E6%9C%A8%22%2C%22purpose%22%3A%22%E8%A3%85%E4%BF%AE%E6%9D%90%E6%96%99%22%2C%22name%22%3A%22test+custom+2%22%2C%22declarePrice%22%3A%2214000000%22%2C%22producproductUnittUnit%22%3A%22%E5%9D%97%22%2C%22categoryFeature%22%3A%22%5Bcf_icbu_normal%5D%22%7D%5D%2C%22weight%22%3A%22100%22%2C%22weightUnit%22%3A%22G%22%2C%22height%22%3A%223%22%7D%2C%22bizType%22%3A%22FEDEX_IP_W2D%22%2C%22receiver%22%3A%7B%22zipCode%22%3A%2212345%22%2C%22address%22%3A%7B%22country%22%3A%22US%22%2C%22province%22%3A%22Berkshire%22%2C%22city%22%3A%22Slough%22%2C%22detailAddress%22%3A%22Hellmann+house%2Ccolnbrrokby+pass%2C+Slough%2C+SL3+0EL.%22%7D%2C%22phone%22%3A%2201784810802%22%2C%22name%22%3A%22Terry+Ingram%22%7D%2C%22currentCPResCode%22%3A%22TRAN_STORE_30324206%22%2C%22logisticsOrderCode%22%3A%22LP00409250274979%22%2C%22routingTrial%22%3A%221%22%2C%22logisticsOrderCreateTime%22%3A%222020-09-24+10%3A44%3A02%22%2C%22outOrderNo%22%3A%22ALS2727111900669%22%2C%22deliverType%22%3A%221%22%2C%22orderTags%22%3A%22OPGSP%22%2C%22sender%22%3A%7B%22zipCode%22%3A%22311200%22%2C%22address%22%3A%7B%22country%22%3A%22CN%22%2C%22province%22%3A%22%E6%B5%99%E6%B1%9F%E7%9C%81%22%2C%22city%22%3A%22%E6%9D%AD%E5%B7%9E%E5%B8%82%22%2C%22district%22%3A%22%E8%90%A7%E5%B1%B1%E5%8C%BA%22%2C%22detailAddress%22%3A%22%E6%B5%99%E6%B1%9F%E7%9C%81%E6%9D%AD%E5%B7%9E%E5%B8%82%E8%90%A7%E5%B1%B1%E5%8C%BA%E4%BF%9D%E7%A8%8E%E7%89%A9%E6%B5%81%E5%9B%AD8%E5%8F%B7%E4%BB%93%22%7D%2C%22phone%22%3A%220571-57183571-8031%22%2C%22companyName%22%3A%22%E8%8F%9C%E9%B8%9F%E7%BD%91%E7%BB%9C%E7%A7%91%E6%8A%80%E6%9C%89%E9%99%90%E5%85%AC%E5%8F%B8%22%2C%22name%22%3A%22%E6%B5%8B%E8%AF%95%22%2C%22mobile%22%3A%2215978765433%22%2C%22email%22%3A%22tet%40123.com%22%7D%2C%22clearanceMode%22%3A%220110%22%2C%22domesticLogistics%22%3A%7B%22expressCompanyCode%22%3A%22EMS%22%2C%22trackingNumber%22%3A%22796536100000%22%7D%2C%22customs%22%3A%7B%22declarePriceTotal%22%3A%22312000000%22%2C%22taxNumber%22%3A%22123%22%7D%2C%22tradeList%22%3A%5B%5D%2C%22needInsurance%22%3A%22true%22%2C%22deliveryPriority%22%3A%221%22%7D&data_digest=dLSTy4tI%2Fo308CQGhlYqug%3D%3D&partner_code=TRAN_STORE_30324206&from_code=CNGFC&msg_type=CAINIAO_GLOBAL_SORTINGCENTER_ORDERINFO_NOTIFY&msg_id=LP00409250274979';
    	
		//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'ali_json'=>$res
    	));
    	$ali_json->save();
    	
    	QLog::log('4PL'.$res);
    	$res = explode('&', $res);
    	$res_arr = array();
    	
    	//获取接口名称msg_type、请求签名data_digest
    	if (count($res)>0){
    		foreach ($res as $r){
    			 @$res_data = explode('=', $r);
    			 $res_arr[@$res_data[0]] = @$res_data[1];
    			 if (@$res_data[0]=='data_digest'){
    			 	$res_arr[@$res_data[0]] = urldecode(@$res_data[1]);
    			 }
    		}
    	}
    	
    	$res = substr($res[0], 20);
    	$res = urldecode($res);
    	$str = json_decode($res,true);
    	//判断数据是否存在
    	if(!is_array($str) || count($str)<=0){
    		return self::fourplfailure('S01','订单数据为空或格式不正确');
    	}
    	//outOrderNo阿里订单号
    	if(!isset($str['logisticsOrderCode'])){
    		return self::fourplfailure('S02','订单号不存在');
    	}
    	
    	if(!is_array($res_arr) || !isset($res_arr['data_digest']) || $res_arr['data_digest']==''){
    		return self::fourplfailure('S04','签名校验失败');
    	}
    	//订单预报
    	if($res_arr['msg_type']=='CAINIAO_GLOBAL_SORTINGCENTER_ORDERINFO_NOTIFY'){
    		//判断ali订单号是否已存在
    		$order=Order::find('order_no=?',$str['logisticsOrderCode'])->getOne();
    		if(!$order->isNewRecord()){
    			return self::fourplfailure('S03','订单号已存在');
    		}
    		$ali_json->api_name='booking';
    		$ali_json->save();
    		$result=self::fourplBook($str,$ali_json,$res_arr);
    		return $result;
    		//取消订单
    	}elseif($res_arr['msg_type']=='CAINIAO_GLOBAL_PARCEL_INFORMATION_NOTIFY' && $str['eventCode']=='ORDER_CANCEL_NOTIFY'){
    		$ali_json->api_name='cancel';
    		$ali_json->save();
    		$result=self::fourplCancel($str,$ali_json,$res_arr);
    		return $result;
    	//退货
    	}elseif ($res_arr['msg_type']=='CAINIAO_GLOBAL_PARCEL_INFORMATION_NOTIFY' && $str['eventCode']=='REFUND_NOTIFY'){
    		$ali_json->api_name='return';
    		$ali_json->save();
    		$result=self::fourplReturn($str,$ali_json,$res_arr);
    		return $result;
    	//已⽀付通知
    	}elseif ($res_arr['msg_type']=='CAINIAO_GLOBAL_PARCEL_INFORMATION_NOTIFY' && $str['eventCode']=='OUTBOUND_NOTIFY'){
    		$ali_json->api_name='notifyPaid';
    		$ali_json->save();
    		$result=self::fourplNotifyPaid($str,$ali_json,$res_arr);
    		return $result;
    	//备案单证异常通知
    	}elseif ($res_arr['msg_type']=='notifyFormException' || $str['eventCode']=='notifyFormException'){
    		$ali_json->api_name='notifyFormException';
    		$ali_json->save();
    		$result=self::fourplNotifyFormException($str,$ali_json,$res_arr);
    		return $result;
    	}
    	
    }
    /**
     * @todo   4pl接收订单
     * @author stt
     * @since  August 19th 2020
     */
    static function fourplBook($data,$ali_json,$res_arr=NULL){
    	$order=Order::find('ali_order_no=?',$data['outOrderNo'])->getOne();
    	$conn = QDB::getConn ();
    	$conn->startTrans ();
    	
    	//泛远单号
    	$now='FAREX'.date('ym');
    	$seq = Helper_Seq::nextVal ( $now );
    	if ($seq < 1) {
    		Helper_Seq::addSeq ( $now );
    		$seq = 1;
    	}
    	$far_no=$now.sprintf("%06d",$seq).'YQ';
    	
    	//判断取件网点
    	$pick_company='';
    	$data['needPickUp']='';
    	//needPickUp是否上门揽件
    	if(isset($data['needPickUp']) && $data['needPickUp']){
    		$package_pre_weight = 0;
    		$package_act_weight = 0;
    		foreach ($data['parcel'] as $packageinfo){
    			$package_pre_weight += $packageinfo['quantity']*$packageinfo['length']*$packageinfo['width']*$packageinfo['height']/5000;
    			$package_act_weight += $packageinfo['quantity']*$packageinfo['weight'];
    		}
    		$tmp_weight = $package_pre_weight>$package_act_weight?$package_pre_weight:$package_act_weight;
    		$zip=Zipcode::find('zip_code_low<=? and zip_code_high>=? and service_code =?',$data['sender']['zipCode'],$data['sender']['zipCode'],$data['bizType'])->getOne();
    		//义乌和杭州 先根据取件邮编匹配之后再进行不超过3KG的订单分配到平台的判断，其余包裹，按邮编进行分配到各网点。
    		if(!$zip->isNewRecord()){
    			//临时存储取件地区
    			$pick_company_tmp=$zip->pick_company;
    			$pick_company_array=array('杭分','义乌分','青岛仓');
    			if(in_array($pick_company_tmp,$pick_company_array)){
    				$pick_company=$zip->pick_company;
    			}else {
    				if($tmp_weight >= 3){
    					$pick_company=$zip->pick_company;
    				}else{
    					$pick_company="平台";
    				}
    			}
    			if($data['serviceCode']=='EUUS-FY'){
    				$pick_company="平台";
    			}
    			if($data['serviceCode']=='US-FY' && $zip->pick_company<>'青岛仓'){
    				$pick_company="平台";
    			}
    		}
    	}
    	//数据过滤
    	//收件人地址1、地址2、城市、省州、邮编 这四个信息里：如果非英文符号，自动转换成相应的英文符号。中文全角“。”更新成"." ; “，”更新成","
    	$data['receiver']['mobile'] 				    = self::convertStrType(@$data['receiver']['mobile']);
    	$data['receiver']['address']['detailAddress']   = self::convertStrType($data['receiver']['address']['detailAddress']);
    	$data['receiver']['address']['street'] 			= self::convertStrType(@$data['receiver']['address']['street']);
    	$data['receiver']['address']['city']            = self::convertStrType($data['receiver']['address']['city']);
    	$data['receiver']['address']['province'] 		= self::convertStrType($data['receiver']['address']['province']);
    	$data['receiver']['zipCode'] 					= self::convertStrType($data['receiver']['zipCode']);
    	//收件人电话只保留纯数字：电话里有中英文中杠“—”“-”或者下划线时，或者空格时，自动去掉
    	$data['receiver']['mobile'] 					= preg_replace('/[^\d]/','',$data['receiver']['mobile']);
    	//收件人、收件人公司 这两个信息里，不能有英文空格之外的标点符号，有直接用空格替代
    	$data['receiver']['name'] 						= preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['receiver']['name']);
//     	$data['receiver']['name2'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['receiver']['name2']);
    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里：如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
    	$data['receiver']['name'] 						= preg_replace('/[　\s]+/u',' ',$data['receiver']['name']);
//     	$data['receiver']['name2'] = preg_replace('/[　\s]+/u',' ',$data['receiver']['name2']);
    	$data['receiver']['address']['detailAddress']  	= preg_replace('/[　\s]+/',' ',$data['receiver']['address']['detailAddress'] );
    	$data['receiver']['address']['street']  		= preg_replace('/[　\s]+/',' ',$data['receiver']['address']['street'] );
    	$data['receiver']['address']['city'] 			= preg_replace('/[　\s]+/',' ',$data['receiver']['address']['city']);
    	$data['receiver']['address']['province'] 		= preg_replace('/[　\s]+/',' ',$data['receiver']['address']['province']);
    	$data['receiver']['zipCode'] 					= preg_replace('/[　\s]+/',' ',$data['receiver']['zipCode']);
    	//发件人
    	$data['sender']['name'] 						= preg_replace('/[　\s]+/u',' ',$data['sender']['name']);
    	//发件人公司名
    	$data['sender']['companyName'] 					= preg_replace('/[　\s]+/u',' ',$data['sender']['companyName']);
    	//将数据存入数据库
    	$order->ali_order_no				=$data['outOrderNo'];
    	$order->order_no					=$data['logisticsOrderCode'];
    	$order->reference_no				= preg_replace('/\s+/', '', @$data['domesticLogistics']['trackingNumber']);
    	$order->far_no						=$far_no;
    	$order->service_code				=$data['bizType'];
    	$order->sender_mobile				=$data['sender']['mobile'];
    	$order->sender_telephone			=@$data['sender']['phone'];
    	//发件人手机号为空mobile，保存电话phone
    	if (isset($data['sender']['mobile']) && $data['sender']['mobile']==''){
    		$order->sender_mobile			=$data['sender']['phone'];
    	}
    	if (isset($data['sender']['phone']) && $data['sender']['phone']==''){
    		$order->sender_telephone		=$data['sender']['mobile'];
    	}
    	$order->sender_email				=@$data['sender']['email'];
    	$order->sender_name1				=$data['sender']['name'];
    	$order->sender_name2				=@$data['sender']['companyName'];
    	//地址1，必填
    	$order->sender_street1				=$data['sender']['address']['detailAddress'];
    	$order->sender_street2				=@$data['sender']['address']['district'];
    	$order->sender_country_code			='CN';
    	$order->sender_city					=$data['sender']['address']['city'];
    	$order->sender_postal_code			=$data['sender']['zipCode'];
    	$order->sender_state_region_code	=$data['sender']['address']['province'];
    	
    	$order->consignee_mobile			=$data['receiver']['mobile'];
    	$order->consignee_telephone			=@$data['receiver']['phone'];
    	//收件人手机号为空mobile，保存电话phone
    	if (isset($data['receiver']['mobile']) && $data['receiver']['mobile']==''){
    		$order->consignee_mobile			=$data['receiver']['phone'];
    	}
    	if (isset($data['receiver']['phone']) && $data['receiver']['phone']==''){
    		$order->consignee_telephone			=$data['receiver']['mobile'];
    	}
    	$order->consignee_email				=@$data['receiver']['email'];
    	$order->consignee_name1				=$data['receiver']['name'];
    	//收件人公司名
    	$order->consignee_name2				=@$data['receiver']['companyName'];
    	$order->consignee_street1			=$data['receiver']['address']['detailAddress'];
    	$order->consignee_street2			=@$data['receiver']['address']['district'];
    	//收件人国家二字码
    	$order->consignee_country_code		=$data['receiver']['address']['country'];
    	$order->consignee_city				=$data['receiver']['address']['city'];
    	$order->consignee_postal_code		=$data['receiver']['zipCode'];
    	$order->consignee_state_region_code	=$data['receiver']['address']['province'];
    	
    	//报关类型 QT(普通不报关 ,默认)/DL(贸 易报关)
    	$order->declaration_type			=@$data['clearanceMode']=='0110'?'DL':'QT';
    	//总申报价值
    	$order->total_amount				=$data['customs']['declarePriceTotal']/1000000;
    	//申报币种，必填
    	$order->currency_code				='USD';
    	//是否使用保险，默认false,必填
    	$order->need_insurance				=$data['needInsurance']=='true'?1:0;
    	//收件⼈税号
    	$order->tax_payer_id				=@$data['taxpayerId'];
    	//订单描述
    	$order->remarks						=@$data['remark'];
    	//发货优先级
//     	$order->delivery_priority			=@$data['deliveryPriority'];
    	//1未入库
    	$order->order_status				='1';
    	//1代表需要上门揽收
    	$order->need_pick_up=$data['needPickUp'];
    	$order->warehouse_code=$data['currentCPResCode'];
//     	$order->warehouse_name=$data['warehouse']['name'];
    	$order->pick_company=$pick_company;
    	//客户ID ALPL 4PL
    	$order->customer_id=11;
    	$order->save();
    	$ali_json->ali_sign=$res_arr['data_digest'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	
    	//菜鸟子单号
    	SubParcel::find('order_id=?',$order->order_id)->getAll()->destroy();
    	if(isset($data['subParcelList']) && is_array($data['subParcelList']) && is_array(@$data['subParcelList']['subParcel']) && count(@$data['subParcelList']['subParcel'])>0){
    		foreach ( $data['subParcelList']['subParcel'] as $sp){
	    		$sub_parcel = new SubParcel();
	    		$sub_parcel->order_id = $order->order_id;
	    		$sub_parcel->sub_parcel_code = $sp['logisticsOrderCode'];
	    		$sub_parcel->save();
    		}
    	}
    	//港前问题件判断
    	$detail=array(
    		'reason'=>array()
    	);
    	//EMS专线,当申报总价高于400.00USD时，预警：EMS超400USD
    	if($order->service_code == 'EMS-FY'){
    		if($order->total_amount>400){
    			$detail['reason'][]='EMS超400USD';
    		}
    	}
    	//中美专线，订单总价超800USD时，进行预警：超800美金
    	if($order->service_code == 'US-FY'){
    		if($order->total_amount>800){
    			$detail['reason'][]='超800美金';
    		}
    	}
    	//欧美专线，目的地为GB的订单，进行预警：GB订单，需要提供交易凭证
    	if($order->service_code == 'EUUS-FY'){
    		if($order->consignee_country_code=='GB'){
    			$detail['reason'][]='GB订单，需要提供交易凭证';
    		}
    	}
    	//US 检查电话号码是否不足10位
    	if($order->consignee_country_code=='US'){
    		if(strlen($order->consignee_mobile)<10){
    			$detail['reason'][]='US 检查电话号码是否不足10位';
    		}
    	}
    	//BR 检查订单详情里没有提供税号信息
    	if($order->consignee_country_code=='BR'){
    		if($order->tax_payer_id==''){
    			$detail['reason'][]='BR 检查订单详情里没有提供税号信息';
    		}
    	}
    	//订单申报总金额超过700.00USD,且报关方式为：QT的订单。
    	if($order->total_amount>700 && $order->declaration_type=='QT'){
    		$detail['reason'][]='订单申报总金额超过700.00USD,且报关方式为：QT的订单';
    	}
    	//订单申报总金额低于1.00USD的订单
    	if($order->total_amount<1){
    		$detail['reason'][]='订单申报总金额低于1.00USD的订单';
    	}
    	//订单申报方式为：DL
    	if($order->declaration_type=='DL'){
    		if($order->service_code=='EMS-FY'){
    			$detail['reason'][]='EMS不提供报关服务';
    		}else if($order->service_code == 'US-FY' || $order->service_code == 'EUUS-FY'){
    			$detail['reason'][]='无报关服务';
    		}else{
    			$detail['reason'][]='订单申报方式为：DL';
    		}
    	}
    	//地址1+地址2字符总数超过105的订单
    	//普货专线和中美专线，地址总字符超过105时，进行预警，EMS不作限制
    	if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'US-FY'){
    		if(strlen($order->consignee_street1.' '.$order->consignee_street2)>105){
    			$detail['reason'][]='地址1加地址2字符总数超过105的订单';
    		}
    	}
    	//假发专线地址1+地址2字符总数超过70的订单
    	if($order->service_code=='WIG-FY'){
    		if(strlen($order->consignee_street1.' '.$order->consignee_street2)>70){
    			$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
    		}else {
    			$address=Order::splitAddressfedex($order->consignee_street1.' '.$order->consignee_street2);
    			if(count($address)>2){
    				$detail['reason'][]='全球假发专线地址字符总数超过70的订单';
    			}
    		}
    	}
    	//收件人公司里，如果有数字进行预警，预警原因：收件公司信息含有数字
    	if($order->consignee_name1){
    		if(preg_match('/\d/',$order->consignee_name1)){
    			$detail['reason'][]='收件公司/客户名称1含有数字';
    		}
    	}
    	if($order->consignee_name2){
    		if(preg_match('/\d/',$order->consignee_name2)){
    			$detail['reason'][]='收件公司/客户名称2含有数字';
    		}
    	}
    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里如有非英文符，进行预警，预警原因：某某字段有非英文字符。
    	//地址1、地址2、城市、省州、邮编这五个信息里：英文状态下以下符号不作预警："#" "/" "" "-" "." "," "&" "*" ";" ":" "?" ";" ""
    	if($order->consignee_name1){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$order->consignee_name1)){
    			$detail['reason'][]='收件公司/客户名称1有非英文字符';
    		}
    	}
    	if($order->consignee_name2){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$order->consignee_name2)){
    			$detail['reason'][]='收件公司/客户名称2有非英文字符';
    		}
    	}
    	if($order->consignee_street1){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_street1)){
    			$detail['reason'][]='收件人地址1有非英文字符';
    		}
    	}
    	if($order->consignee_street2){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_street2)){
    			$detail['reason'][]='收件人地址2有非英文字符';
    		}
    	}
    	if($order->consignee_city){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_city)){
    			$detail['reason'][]='收件人城市有非英文字符';
    		}
    	}
    	if($order->consignee_state_region_code){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_state_region_code)){
    			$detail['reason'][]='收件人省/州有非英文字符';
    		}
    	}
    	if($order->consignee_postal_code){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$order->consignee_postal_code)){
    			$detail['reason'][]='收件人邮编有非英文字符';
    		}
    	}
    	// EMS订单邮编校验
    	if($order->service_code == 'EMS-FY' || $order->service_code == 'ePacket-FY'){
    		$zipformat = Zipformat::find('country_code_two = ?',$order->consignee_country_code)->getOne();
    		if(!$zipformat->isNewRecord()){
    			if(!preg_match($zipformat->zip_format_preg_match, trim($order->consignee_postal_code))){
    				$detail['reason'][]='收件人邮编格式不正确,'.$order->consignee_country_code.'的邮编格式为：'.$zipformat->zip_format;
    			}
    		}
    	}
    	//查询判断是否疑似偏远
    	$product=Product::find("product_name=?",$order->service_code)->getOne();
    	$productppr=Productppr::find('product_id=? and effective_time <= ? and invalid_time >= ?',$product->product_id,time(),time())->getOne();
    	$trim = array(' ','-');
    	if(!$productppr->isNewRecord()){
    		$remote_city=Remote::find('remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,"") != ""',$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_city,strtolower(str_replace($trim,'',$order->consignee_city)))->getOne();
    		if($remote_city->isNewRecord()){//城市不同，省州完全相同
    			$remote_state = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city=? or remote_city=?) and ifnull(remote_city,'') != '' ",$productppr->remote_manage_id,$order->consignee_country_code,$order->consignee_state_region_code,strtolower(str_replace($trim,'',$order->consignee_state_region_code)))->getOne();
    			if(!$remote_state->isNewRecord()){
    				$order->suspected_remote='1';
    				$order->save();
    			}else{
    				$remote_like = Remote::find("remote_manage_id = ? and country_code_two=? and (remote_city like ? or remote_city like ? or remote_city like ? or remote_city like ?) and ifnull(remote_city,'') != ''",$productppr->remote_manage_id,$order->consignee_country_code,'%'.$order->consignee_city.'%','%'.strtolower(str_replace($trim,'',$order->consignee_city)).'%','%'.$order->consignee_state_region_code.'%','%'.strtolower(str_replace($trim,'',$order->consignee_state_region_code)).'%')->getOne();
    				if(!$remote_like->isNewRecord()){//订单中的城市、省州在偏远城市信息中出现
    					$order->suspected_remote='1';
    					$order->save();
    				}
    				$remote=Remote::find("country_code_two = ? and remote_manage_id= ? and ifnull(remote_city,'') != ''",$order->consignee_country_code,$productppr->remote_manage_id)->getAll();
    				foreach ($remote as $v){//偏远城市信息在订单中的城市、省州、地址中出现
    					$is_far=Order::find("consignee_state_region_code like ? or consignee_city like ? or consignee_street1 like ? or consignee_street2 like ? ",'%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%','%'.$v->remote_city.'%')
    					->where('order_id= ? ',$order->order_id)->getOne();
    					if(!$is_far->isNewRecord()){
    						$order->suspected_remote='1';
    						$order->save();
    						break;
    					}
    				}
    			}
    		}
    	}
    	if($order->suspected_remote=='1'){
    		$detail['reason'][]='城市疑似偏远，需人工介入';
    	}
    	$black=blacklist::find('product_id=?',$product->product_id)->asArray()->getAll();
    	if(count($black)>0){
    		foreach ($black as $b){
    			$reason='';
    			$i=0;
    			$bb=array_filter($b);
    			$co=count($bb)-4;
    			if($b['consignee_country_code'] && strtoupper(trim($order->consignee_country_code)) == strtoupper(trim($b['consignee_country_code']))){
    				$reason.='国家:'.$b['consignee_country_code'].',';
    				$i++;
    			}
    			if($b['consignee_postal_code'] && strtoupper(trim($order->consignee_postal_code)) ==  strtoupper(trim($b['consignee_postal_code']))){
    				$reason.='邮编:'.$b['consignee_postal_code'].',';
    				$i++;
    			}
    			if($b['consignee_city'] && strtoupper(trim($order->consignee_city))==strtoupper(trim($b['consignee_city']))){
    				$reason.='城市:'.$b['consignee_city'].',';
    				$i++;
    			}
    			if($b['consignee_state_region_code'] && strtoupper(trim($order->consignee_state_region_code)) == strtoupper(trim($b['consignee_state_region_code']))){
    				$reason.='州:'.$b['consignee_state_region_code'].',';
    				$i++;
    			}
    			
    			if(($b['sender_name1'] && (strtoupper(trim($order->sender_name1)) == strtoupper(trim($b['sender_name1'])) ||  strtoupper(trim($order->sender_name2)) == strtoupper(trim($b['sender_name1']))))){
    				$reason.='发件人:'.$b['sender_name1'].',';
    				$i++;
    			}
    			if(($b['sender_name2'] && (strtoupper(trim($order->sender_name2)) == strtoupper(trim($b['sender_name2'])) || strtoupper(trim($order->sender_name1)) == strtoupper(trim($b['sender_name2']))))){
    				$reason.='发件公司:'.$b['sender_name2'].',';
    				$i++;
    			}
    			
    			if($b['sender_street1']){
    				$condition=str_replace('\\','\\\\',$b['sender_street1']);
    				$condition=str_replace('/','\\/',$condition);
    				
    				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
    				
    				$condition=str_replace('*','.*?',$condition);
    				$order->consignee_street1=str_replace(PHP_EOL, '', $order->consignee_street1);
    				$order->consignee_street2=str_replace(PHP_EOL, '', $order->consignee_street2);
    				$order->sender_street1=str_replace(PHP_EOL, '', $order->sender_street1);
    				$order->sender_street2=str_replace(PHP_EOL, '', $order->sender_street2);
    				if (preg_match('/^'.$condition.'$/i',$order->consignee_street1) || preg_match('/^'.$condition.'$/i',$order->consignee_street2) || preg_match('/^'.$condition.'$/i',$order->sender_street1) || preg_match('/^'.$condition.'$/i',$order->sender_street2)){
    					$reason.='地址:'.$b['sender_street1'].',';
    					$i++;
    				}
    			}
    			if($b['product_name']){
    				$j=0;
    				$condition=str_replace('\\','\\\\',$b['product_name']);
    				$condition=str_replace('/','\\/',$condition);
    				
    				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
    				
    				$condition=str_replace('*','.*?',$condition);
    				foreach ($data['parcel'] as $parcel){
    					foreach ($parcel['goodsList'] as $op){
	    					if (preg_match('/^'.$condition.'$/i',$op['cnName']) || preg_match('/^'.$condition.'$/i',$op['name'])){
	    						$reason.='品名:'.$b['product_name'].',';$j++;
	    					}
    					}
    				}
    				if($j>0){
    					$i++;
    				}
    			}
    			if($i > 0 && $i == $co){
    				$reason=trim($reason,',');
    				$order->black_flag='1';
    				$order->black_reason=$reason;
    				$order->save();
    				break;
    			}
    		}
    	}
    	if($order->black_flag=='1'){
    		$detail['reason'][]='线路:'.$order->service_product->product_chinese_name.','.$order->black_reason.'黑名单';
    	}
    	$consignee_postal_code=trim($order->consignee_postal_code);
    	if($consignee_postal_code && in_array($order->service_code, array('EUUS-FY','Express_Standard_Global','US-FY','WIG-FY'))){
    		$zipcode=array('123','1234','12345','123456','1234567','12345678','123456789');
    		$count=substr_count($consignee_postal_code,substr($consignee_postal_code, 0,1));
    		if(strlen($consignee_postal_code)=='1' || in_array($consignee_postal_code, $zipcode) || $count==strlen($consignee_postal_code)){
    			$order->zip_flag='1';
    			$order->save();
    		}
    	}
    	if($order->zip_flag=='1'){
    		$detail['reason'][]='邮编异常';
    	}
    	//拆分阿里单号
    	if(strlen($order->reference_no)){
    		$references=explode(",", $order->reference_no);
    		foreach ($references as $r){
    			$alireference=new Alireference();
    			$alireference->order_id=$order->order_id;
    			$alireference->reference_no=$r;
    			$alireference->save();
    		}
    	}
    	//存入product信息
    	$flag=false;//判断阿里推送订单是否为测试订单
    	$glasses_flag=false;
    	$battery_flag=false;
    	$limit_flag=false;
    	$fda_flag=false;
    	$all_flag=false;
    	$mask_flag=false;
    	
    	$ratio=5000;
    	$total_weight=0;
    	$weight_income_ali=0;
    	//查询product获取计泡系数
    	$product=Product::find('product_name=?',$data['bizType'])->getOne();
    	if(!$product->isNewRecord()){
    		$ratio=$product->ratio;
    	}
    	$quantity = 1;
    	if(@$data['subParcelQuantity']){
    		$quantity = $data['subParcelQuantity'];
    	}
//     	foreach ($data['parcel'] as $order_package){
    		//存入package信息
    		$order_package = $data['parcel'];
    		$package=new Orderpackage();
    		$package->changeProps(array(
    			'order_id'=>$order->id(),
    			'package_type'=>'BOX',
    			'quantity'=>$quantity,
    			'unit'=>'CM',
    			'length'=>$order_package['length'],
    			'width'=>$order_package['width'],
    			'height'=>$order_package['height'],
    			'weight'=>$order_package['weight']/1000,
    			'weight_unit'=>'KG',
    		));
    		$package->save();
    		$total_weight+=$order_package['weight'];
    		$weight_income_ali+=(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)>$order_package['weight']?
    		(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio):$order_package['weight'];
    		
    		foreach ($data['parcel']['goodsList'] as $order_product){
//     			$order_product = $data['parcel']['goodsList']['goods'];
	    		$product=new Orderproduct();
	    		$product->changeProps(array(
	    			'order_id'=>$order->id(),
	    			'product_name'=>$order_product['cnName'],
	    			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
	    			'product_name_en'=>preg_replace('/[　\s]+/u',' ',$order_product['name']),
	    			'product_quantity'=>$order_product['quantity'],
	    			'product_unit'=>@$order_product['productUnit'],
	    			'hs_code'=>@$order_product['hsCode'],
	    			'declaration_price'=>$order_product['declarePrice']/1000000,
	    			'currency_code'=>$order_product['priceCurrency'],
	    			'has_battery'=>@$order_product['hasBattery'],
	    			'product_name_far'=>$order_product['cnName'],
	    			'product_name_en_far'=>preg_replace('/[　\s]+/u',' ',$order_product['name']),
	    			'hs_code_far'=>@$order_product['hsCode'],
	    			'material_use'=>$order_product['material'].' '.$order_product['purpose']
	    		));
	    		if(@$order_product['hasBattery']==1){
	    			$order->has_battery=1;
	    		}else{
	    			if(count(@$order_product['productType'])>0){
	    				foreach (@$order_product['productType'] as $prod){
	    					if($prod['code']=='battery'){
	    						$order->has_battery=1;
	    					}
	    					
	    				}
	    			}
	    		}
	    		
	    		
	    		$product->save();
	    		if($order_product['cnName']=='系统测试订单'){
	    			$flag=true;
	    		}
	    		if($order->consignee_country_code=='US'){
	    			if(strstr($product->product_name_far, '眼镜') || strstr($product->product_name_far, '太阳镜') || strstr($product->product_name, '眼镜') || strstr($product->product_name, '太阳镜')){
	    				$glasses_flag=true;
	    			}
	    			if(strstr($product->product_name_far, '睫毛') || strstr($product->product_name_far, '假睫毛') || strstr($product->product_name, '睫毛') || strstr($product->product_name, '假睫毛')){
	    				$fda_flag=true;
	    			}
	    		}
	    		if($product->has_battery){
	    			$battery_flag=true;
	    		}
	    		if($order->service_code == 'US-FY'){
	    			if(strstr($product->product_name_far, '车灯') || strstr($product->product_name_far, '大灯') || strstr($product->product_name_far, '头盔') || strstr($product->product_name_far, '刀') || strstr($product->product_name_far, '激光') || strstr($product->product_name, '车灯') || strstr($product->product_name, '大灯') || strstr($product->product_name, '头盔') || strstr($product->product_name, '刀') || strstr($product->product_name, '激光')){
	    				$limit_flag=true;
	    			}
	    		}
	    		if($order->service_code != 'CNUS-FY'){
	    			if(strstr($product->product_name_far, '电') || strstr($product->product_name_far, '灯') || strstr($product->product_name_far, '器') || strstr($product->product_name_far, '磁') || strstr($product->product_name, '电') || strstr($product->product_name, '灯') || strstr($product->product_name, '器') || strstr($product->product_name, '磁')){
	    				$all_flag=true;
	    			}
	    		}
	    		// US 产品品名里含：“眼镜”，“太阳镜”
	    		if($glasses_flag){
	    			$detail['reason'][]='US 产品品名里含：“眼镜”，“太阳镜”';
	    		}
	    		//检查订单详情里，有产品“带电”
	    		if($battery_flag){
	    			$detail['reason'][]='检查订单详情里，有产品“带电”';
	    		}
	    		//判断品名含mask的商品sku是否等于6307900010
	    		if(strpos(strtolower($order_product['name']), 'mask') !== false && $order_product['hsCode']<>'6307900010'){
	    			$mask_flag = true;
	    		}
	    	}
//     	}
    	//存入阿里计费重量和阿里实重
    	$order->weight_actual_ali=$total_weight;
    	$order->weight_income_ali=$weight_income_ali;
    	//判断品名含mask的商品sku是否等于6307900010
    	if(in_array($order->service_code, array('Express_Standard_Global','US-FY')) && $mask_flag){
    		$detail['reason'][]='hscode不等于6307900010的mask订单';
    	}
    	//中美专线，如果品名里含有：“车灯”，“大灯”“头盔”“刀”“激光”
    	if($limit_flag){
    		$detail['reason'][]='疑似限运品';
    	}
    	//普货专线、欧美专线、假发专线，目的地为美国的订单，如果品名里含有：“睫毛”，“假睫毛”
    	if($order->service_code == 'Express_Standard_Global' || $order->service_code == 'EUUS-FY' || $order->service_code == 'WIG-FY'){
    		if($fda_flag){
    			$detail['reason'][]='美国需FDA';
    		}
    	}
    	//目前所有线路：当品名里含有“电”“灯”“器”“磁”，预警：品名含有#
    	if($all_flag){
    		$detail['reason'][]='品名含有#';
    	}
    	if($flag){//是测试订单
    		$order->ali_testing_order='1';
    		$order->save();
    	}
    	
    	
    	//#80732仓库代码
    	$codewarehouses = CodeWarehouse::find()->getAll();
    	foreach ($codewarehouses as $cw){
    		if ($data['currentCPResCode']==$cw->warehouse){
    			$order->department_id = $cw->department_id;
    		}
    	}
    	$order->save();
    	if(count($detail['reason'])>0){
    		//新建问题件
    		$now = 'ISSUE' . date ( 'Ym' );
    		$seq = Helper_Seq::nextVal ( $now );
    		if ($seq < 1) {
    			Helper_Seq::addSeq ( $now );
    			$seq = 1;
    		}
    		$seq = str_pad ( $seq, 4, "0", STR_PAD_LEFT );
    		$abnormal_parcel_no = date ( 'Ym' ) . $seq;
    		$abnormal_parcel = new Abnormalparcel ( array (
    			'ali_order_no' => $order->ali_order_no,
    			'abnormal_parcel_no' => $abnormal_parcel_no,
    			'abnormal_parcel_operator' => '系统',
    			'issue_type' => '5',
    			'issue_content' => @implode ( ',', $detail ['reason'] )
    		) );
    		$abnormal_parcel->save ();
    		$history = new Abnormalparcelhistory ();
    		$history->abnormal_parcel_id = $abnormal_parcel->abnormal_parcel_id;
    		$history->follow_up_content = @implode ( ',', $detail ['reason'] );
    		$history->follow_up_operator = '系统';
    		$history->save ();
    	}
    	$conn->completeTrans ();
//     	if(substr($order->ali_order_no, 0, 3) == 'ALS'){
//     		$rule_choose = AutomaticEmailRule::find('product_id = ? and tracking_code = ?',$order->service_product->product_id,'订单创建')->getOne();
//     		if(!$rule_choose->isNewRecord()){
//     			$email_template = EmailTemplate::find('id = ?',$rule_choose->email_id)->getOne();
//     			if(!$email_template->isNewRecord()){
//     				$title = $email_template->template_title;
//     				$email_info = $email_template->template_text;
    				
//     				//标题
//     				$template_title = preg_replace('/ali_order_no/',$order->ali_order_no, $title);
//     				$template_title = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_title);
//     				$template_title = preg_replace('/reference_no/',$order->reference_no, $template_title);
//     				$template_title = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_title);
//     				//内容
//     				$template_info = preg_replace('/ali_order_no/',$order->ali_order_no, $email_info);
//     				$template_info = preg_replace('/service_name/',$order->service_product->product_chinese_name, $template_info);
//     				$template_info = preg_replace('/reference_no/',$order->reference_no, $template_info);
//     				$template_info = preg_replace('/consignee_country_code/',$order->consignee_country_code, $template_info);
//     				QLog::log($template_title);
//     				QLog::log($template_info);
//     				$title = nl2br($template_title);
//     				$msg = nl2br($template_info);
//     				try {
//     					$email_response=Helper_Mailer::sendtemplate($order->sender_email,$title,$msg);
//     					QLog::log($email_response);
//     					if ($email_response == 'email_success') {
//     						$order_log = new OrderLog ();
//     						$order_log->order_id = $order->order_id;
//     						$order_log->staff_name = '系统';
//     						$order_log->comment = '已发送邮件，标题：'.$template_title.'  内容：' . $template_info;
//     						$order_log->save ();
//     					}else {
//     						return json_encode(array (
//     							'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
//     						));
//     					}
//     				} catch ( Exception $e ) {
//     					return json_encode(array (
//     						'isSuccess' => true,'message' => '' ,'result'=>array('aspOrderNo'=>$far_no)
//     					));
//     				}
//     			}
//     		}
//     	}
    	//返回结果
    	return self::fourplSuccess();
    }
    static function fourplCancel($data,$ali_json,$res_arr=NULL){
    	$order=Order::find('order_no=?',$data['logisticsOrderCode'])->getOne();
    	$ali_json->ali_sign=$res_arr['data_digest'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	$extinfo = json_decode($data['extInfo'],true);
    	$order->reason_code=$extinfo['reasonCode'];
    	$order->reason_name=$extinfo['reasonName'];
    	$order->reason_remark=$extinfo['remark'];
    	$order->order_status="2";
    	$order->save();
    	//删除ali_reference表里的快递号信息
    	Alireference::find('order_id=?',$order->order_id)->getAll()->destroy();
    	//删除所有相关费用
    	Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
    	//返回结果
    	return self::fourplSuccess();
    }
    static function fourplReturn($data,$ali_json,$res_arr=NULL){
    	$order=Order::find('order_no=?',$data['logisticsOrderCode'])->getOne();
    	$ali_json->ali_sign=$res_arr['data_digest'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	
    	$extinfo = json_decode($data['extInfo'],true);
    	$order->reason_code=$extinfo['reasonCode'];
    	$order->reason_name=$extinfo['reasonName'];
    	$order->reason_remark=@$extinfo['remark'];
    	if($order->order_status != '3'){
    		$order->return_type=$extinfo['returnType'];
    		$order->order_status='11';
    	}
    	//判断returnType
    	$contact = $extinfo['contact'];
    	if($extinfo['returnType']=='WAREHOUSE_RETURN'){
    		$address = $contact['address'];
    		$order->return_mobile=$contact['mobile'];
    		$order->return_telephone=$contact['phone'];
//     		$order->return_email=$contact['email'];
    		$order->return_name1=$contact['name'];
//     		$order->return_name2=$contact['name'];
    		$order->return_street1=$address['detailAddress'];
//     		$order->return_street2=$contact['detailAddress'];
//     		$order->return_country_code=$contact['countryCode'];
    		$order->return_city=$address['city'];
    		$order->return_postal_code=$contact['zipCode'];
    		$order->return_state_region_code=$address['province'];
    		
    	}
    	$order->save();
    	//收入
    	$shou='删除收入';
    	//成本
    	$fu='成本';
    	foreach ($order->fees as $fee){
    		if($fee->fee_type=='1'){
    			$shou.=$fee->fee_item_code.'*'.$fee->quantity.';';
    		}else{
    			$fu.=$fee->fee_item_code.'*'.$fee->quantity.';';
    		}
    	}
    	//没有核查的费用都删除
    	if(!$order->warehouse_confirm_time){
    		if(strlen($shou)>8){
    			$log=new OrderLog();
    			$log->order_id=$order->order_id;
    			$log->comment=$fu=='成本'?$shou:$shou.$fu;
    			$log->save();
    		}
    		//删除所有相关费用
    		Fee::find('order_id=?',$order->order_id)->getAll()->destroy();
    	}
    	//返回结果
    	return self::fourplSuccess();
    }
    
    static function fourplNotifyPaid($data,$ali_json,$res_arr=NULL){
    	$order=Order::find('order_no=?',$data['logisticsOrderCode'])->getOne();
    	$ali_json->ali_sign=$res_arr['data_digest'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	if(!$order->payment_time){
    		if($order->order_status=='12'){//如果是已扣件状态，将status_copy状态更改为4，status状态不变
    			$order->order_status_copy='4';
    			$order->payment_time=time();
    			$order->save();
    		}else{
    			$order->order_status='4';
    			$order->payment_time=time();
    			$order->save();
    		}
    		//将入库包裹信息添加到出库包裹表中
    		$farout=Faroutpackage::find('order_id=?',$order->order_id)->getOne();
    		if($farout->isNewRecord()){
    			$farpackages=Farpackage::find('order_id=?',$order->order_id)->getAll();
    			if (count($farpackages)==1){
    				$farpackage=Farpackage::find('order_id=?',$order->order_id)->getOne();
    				$faroutpackage=new Faroutpackage(array(
    					'order_id'=>$order->order_id,
    					'far_id'=>$farpackage->far_package_id,
    					'quantity_out'=>$farpackage->quantity,
    					'length_out'=>$farpackage->length,
    					'width_out'=>$farpackage->width,
    					'height_out'=>$farpackage->height,
    					'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
    				));
    				$faroutpackage->save();
    				//定义长宽高数组
    				$verify_array=array($farpackage->length,$farpackage->width,$farpackage->height);
    				rsort($verify_array);
    				if ($verify_array[0]==22&&$verify_array[1]==22&&$verify_array[2]==2.2&&$farpackage->quantity==1){
    					$order->packing_type='PAK';
    					$order->save();
    				}
    			}else{
    				foreach ($farpackages as $farpackage){
    					$faroutpackage=new Faroutpackage(array(
    						'order_id'=>$order->order_id,
    						'far_id'=>$farpackage->far_package_id,
    						'quantity_out'=>$farpackage->quantity,
    						'length_out'=>$farpackage->length,
    						'width_out'=>$farpackage->width,
    						'height_out'=>$farpackage->height,
    						'weight_out'=>$order->service_code=='ePacket-FY'?$farpackage->weight-0.003:$farpackage->weight,
    					));
    					$faroutpackage->save();
    				}
    			}
    		}
    	}
    	//返回结果
    	return self::fourplSuccess();
    }
    
    static function fourplNotifyFormException($data,$ali_json,$res_arr=NULL){
    	//判断ali订单号是否已存在
    	$order=Order::find('order_no=?',$data['logisticsOrderCode'])->getOne();
    	
    	$ali_json->ali_sign=$res_arr['data_digest'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	//保存备案单证异常通知信息
    	$order->ali_form_exception_info=$data['extInfo']['exceptionMessage'];
    	$order->save();
    	//返回结果
    	return self::fourplSuccess();
    }
    /**
     * @todo   返回错误信息
     * @author stt
     * @since  August 19th 2020
     */
    static function fourplFailure($code = 'S01', $data = '')
    {
    	
    	$arr=array(
    		'response'=>array(
    			'success'=>false,
    			'errorCode'=>$code,
    			'errorMsg'=>$data,
    		)
    	);
    	return json_encode($arr);
    }
    /**
     * @todo   返回成功信息
     * @author stt
     * @since  August 19th 2020
     */
    static function fourplSuccess()
    {
    	$arr=array(
    		'response'=>array(
    			'success'=>true,
    			'errorCode'=>'',
    			'errorMsg'=>'',
    		)
    	);
    	return json_encode($arr);
    }
    
    
    /**
     * @todo   订单预报
     * @author stt
     * @since  2020-09-10
     */
    function actionNewOrderBooking(){
    	
    	$res=file_get_contents ( "php://input" );
    	
    	//自制订单预报测试数据 正式需注释
//     	$res = Helper_Ceshidata::newzizhidata();
    	
    	//阿里原始信息存入ali_json表中
    	$ali_json= new Alijson();
    	$ali_json->changeProps(array(
    		'api_name'=>'booking',
    		'ali_json'=>$res
    	));
    	$ali_json->save();
    	
    	$newdata=json_decode($res,true);
    	$header = $newdata['header'];
    	$data = $newdata['body'];
    	//全部报错信息
    	$error_reason = array();
    	if(!isset($header['customerCode']) || $header['customerCode']==''){
    		$error_reason[] = '客户代码不能为空';
    	}
    	$customer = Customer::find('customs_code=?',$header['customerCode'])->getOne();
    	if($customer->isNewRecord()){
    		$error_reason[] = '客户不存在';
    	}
    	$customer_sign = Customer::find('customs_code=? and customer_sign=?',$header['customerCode'],$header['customerSign'])->getOne();
    	if(!isset($header['customerSign']) || $header['customerSign']=='' || $customer_sign->isNewRecord()){
    		$error_reason[] = '签名校验失败';
    	}
    	//判断数据是否存在
    	if(!is_array($data) || count($data)<=0){
    		$error_reason[] = '订单数据为空或格式不正确';
    		$error_rusult = self::allErrordata($error_reason);
    		if ($error_rusult != 'noerror'){
    			return self::newFailure('4000',$error_rusult);
    		}
    	}
    	
    	if(!isset($data['orderNo']) || $data['orderNo']==''){
    		$error_reason[] = '客户订单号不存在';
    	}
    	//判断ali订单号是否已存在
    	$order=Order::find('order_no=? and customer_id=?',$data['orderNo'],$customer_sign->customer_id)->getOne();
    	if(!$order->isNewRecord() && ($order->order_status!=1 || $order->tracking_no)){
    		$error_reason[] = $data['orderNo'].'已预报，数据无法修改，建议更新订单号重新预报';
    	}
    	$conn = QDB::getConn ();
    	$conn->startTrans ();
    	if ($order->isNewRecord()){
    		//泛远单号
    		$now='FAREX'.date('ym');
    		$seq = Helper_Seq::nextVal ( $now );
    		if ($seq < 1) {
    			Helper_Seq::addSeq ( $now );
    			$seq = 1;
    		}
    		$far_no=$now.sprintf("%06d",$seq).'YQ';
    		$order->far_no=$far_no;
    	
    		$order->ali_order_no=$far_no;
    	}
    	//客户订单号
    	$order->order_no=$data['orderNo'];
    	//国内快递单号
    	$order->reference_no= preg_replace('/\s+/', '', @$data['referenceNo']);
    	//运输方式
    	//按照新建的运输方式表来保存产品和渠道
    	$codetransport = CodeTransport::find('code=?',$data['transportCode'])->getOne();
    	if($codetransport->isNewRecord()){
    		$error_reason[] = '运输方式不存在';
    	}
    	if (!$customer_sign->transports || ($customer_sign->transports && !in_array($codetransport->id, explode(',', $customer_sign->transports)))){
    		$error_reason[] = '您尚未开通该运输方式服务，请核对运输方式代码。如有问题请联系我们。';
    	}
    	$product = Product::find('product_id=?',$codetransport->product_id)->getOne();
    	//运输方式id
    	$order->transport_id=$codetransport->id;
    	//产品代码
    	$order->service_code=$product->product_name;
    	//渠道id
    	$order->channel_id=$codetransport->channel_id;
    	if(!isset($data['totalQuantity']) || $data['totalQuantity']=='' ||$data['totalQuantity']<=0){
    		$error_reason[] = '包裹总数量必填，且大于0';
    	}
    	$package_arr = array();
//     	if(!is_array($data['packages']) || count($data['packages'])<=0){
//     		$error_reason[] = '包裹信息不存在';
//     	}
    	$total_package_quantity = 0;
    	foreach ($data['packages'] as $order_package){
    		$total_package_quantity +=  $order_package['quantity'];
    	}
    	//#83075 对比总包裹数和分包裹总数，如果对等就使用分包裹数据保存 如果不对等使用总包裹数保存一条总包裹数据
    	if ($total_package_quantity==$data['totalQuantity']){
    		$package_arr = $data['packages'];
    	}else{
    		$package_arr[0] = @$data['packages'][0];
    		$package_arr[0]['quantity'] = $data['totalQuantity'];
    		if(!isset($data['packages'][0]['length']) || $data['packages'][0]['length']=='' ||$data['packages'][0]['length']<=0){
    			$package_arr[0]['length'] = 22;
    		}
    		if(!isset($data['packages'][0]['width']) || $data['packages'][0]['width']=='' ||$data['packages'][0]['width']<=0){
    			$package_arr[0]['width'] = 22;
    		}
    		if(!isset($data['packages'][0]['height']) || $data['packages'][0]['height']=='' ||$data['packages'][0]['height']<=0){
    			$package_arr[0]['height'] = 2.22;
    		}
    		if(!isset($data['packages'][0]['weight']) || $data['packages'][0]['weight']=='' ||$data['packages'][0]['weight']<=0){
    			$package_arr[0]['weight'] = 1;
    		}
    	}
    	//是否使用上门揽收服务，默认否
    	$order->need_pick_up=0;
    	//判断取件网点
    	$pick_company='';
    	if($data['needPickUp']=='Y'){
    		$package_pre_weight = 0;
    		$package_act_weight = 0;
    		foreach ($package_arr as $packageinfo){
    			$package_pre_weight += $packageinfo['quantity']*$packageinfo['length']*$packageinfo['width']*$packageinfo['height']/5000;
    			$package_act_weight += $packageinfo['quantity']*$packageinfo['weight'];
    		}
    		$tmp_weight = $package_pre_weight>$package_act_weight?$package_pre_weight:$package_act_weight;
    		$zip=Zipcode::find('zip_code_low<=? and zip_code_high>=? and service_code =?',$data['sender']['postalCode'],$data['sender']['postalCode'],$product->product_name)->getOne();
    		if(!$zip->isNewRecord()){
    			//义乌和杭州 先根据取件邮编匹配之后再进行不超过3KG的订单分配到平台的判断，其余包裹，按邮编进行分配到各网点。
    			$pick_company_tmp=$zip->pick_company;//临时存储取件地区
    			$pick_company_array=array('杭分','义乌分','青岛仓');
    			if(in_array($pick_company_tmp,$pick_company_array)){
    				$pick_company=$zip->pick_company;
    			}else {
    				if($tmp_weight >= 3){
    					$pick_company=$zip->pick_company;
    				}else{
    					$pick_company="平台";
    				}
    			}
    			if($product->product_name=='EUUS-FY'){
    				$pick_company="平台";
    			}
    			if($product->product_name=='US-FY' && $zip->pick_company<>'青岛仓'){
    				$pick_company="平台";
    			}
    			$order->need_pick_up =1;
    		}
    	}
    	if(!is_array($data['customs']) || count($data['customs'])<=0){
    		$error_reason[] = '报关信息不存在';
    	}else{
    		if(!isset($data['customs']['declarationType']) || $data['customs']['declarationType']==''){
    			$error_reason[] = '报关方式不能为空';
    		}
    		if ($data['customs']['declarationType']=='DL'){
    			if ($product->is_declaration==2){
    				$error_reason[] = '运输方式不支持报关';
    			}
    			if(!isset($data['customs']['commissionCode']) || $data['customs']['commissionCode']==''||!isset($data['customs']['businessCode']) || $data['customs']['businessCode']==''){
    				$error_reason[] = '当选择正式报关时，委托书编号和经营单位编码均为必填项';
    			}
    		}
    	}
    	$order->declaration_type=@$data['customs']['declarationType'];
    	$order->commission_code=@$data['customs']['commissionCode'];
    	$order->business_code=@$data['customs']['businessCode'];
    	if(!is_array($data['products']) || count($data['products'])<=0){
    		$error_reason[] = '产品信息不存在';
    	}
    	//运输方式申报总价限制
    	$total_amount = 0;
    	foreach ($data['products'] as $op){
    		if(!isset($op['productName']) || $op['productName']==''){
    			$error_reason[] = '产品名称（中文）不能为空';
    		}
    		if(!isset($op['productNameEn']) || $op['productNameEn']==''){
    			$error_reason[] = '产品名称（英文）不能为空';
    		}
    		if(!isset($op['productQuantity']) || $op['productQuantity']==''){
    			$error_reason[] = '产品数量不能为空';
    		}
    		if(!isset($op['hsCode']) || $op['hsCode']==''){
    			$error_reason[] = '海关统一编码不能为空';
    		}
    		if(!isset($op['declarationPrice']) || $op['declarationPrice']==''){
    			$error_reason[] = '申报单价不能为空';
    		}
//     		if(!isset($op['material']) || $op['material']==''){
//     			$error_reason[] = '材质不能为空';
//     		}
//     		if(!isset($op['purpose']) || $op['purpose']==''){
//     			$error_reason[] = '用途不能为空';
//     		}
    		$total_amount+=$op['productQuantity'] * $op['declarationPrice'];
    	}
    	if($product->declare_threshold && $total_amount>$product->declare_threshold){
    		$error_reason[] = '申报总价高于运输方式申报总价限制';
    	}
    	
    	$hold_flag = '';
    	$i=1;
    	$total_num=0;
    	foreach ($package_arr as $farpackage){
    		$arr = array($farpackage['length'],$farpackage['width'],$farpackage['height']);
    		sort($arr);
    		//最长边限制
    		if($product->length && $arr[2]>=$product->length){
    			$hold_flag .= '最长边应小于'.$product->length.',';
    		}
    		//第二长边限制
    		if ($product->width && $arr[1]>=$product->width){
    			$hold_flag .= '第二长边应小于'.$product->width.',';
    		}
    		//高限制
    		if ($product->height && $arr[0]>=$product->height){
    			$hold_flag .= '高应小于'.$product->height.',';
    		}
    		//周长限制
    		if ($product->perimeter && 4*($arr[2]+$arr[1]+$arr[0])>=$product->perimeter){
    			$hold_flag .= '周长应小于'.$product->perimeter.',';
    		}
    		//围长限制
    		if ($product->girth && $arr[2]+2*($arr[1]+$arr[0])>=$product->girth){
    			$hold_flag .= '围长应小于'.$product->girth.',';
    		}
    		//单个包裹实重限制
    		if ($product->weight && $farpackage['weight']>=$product->weight){
    			$hold_flag .= '单个包裹实重应小于'.$product->weight.',';
    		}
    		$common_flag = '第'.$i.'个包裹：';
    		if(strlen($hold_flag)>0){
    			$hold_flag = substr($hold_flag,0,strlen($hold_flag)-1);
    			$hold_flag = $common_flag.$hold_flag.';';
    			break;
    		}
    		$i++;
    		$total_num+=$farpackage['quantity'];
    	}
    	if($total_num>1){
    		if ($product->support_one==2){
    			$error_reason[] = '此运输方式不支持一票多件';
    		}
    	}
    	if(strlen($hold_flag)>0){
    		$error_reason[] = '该运输方式不支持，'.$hold_flag;
    	}
    	//交货仓库
    	$order->warehouse_code=@$data['warehouse']['code'];
    	$order->warehouse_name=@$data['warehouse']['name'];
    	
    	$order->pick_company=$pick_company;
    	if(!is_array($data['receiver']) || count($data['receiver'])<=0){
    		$error_reason[] = '收件人信息不存在';
    	}
    	if(!isset($data['receiver']['phone']) || $data['receiver']['phone']==''){
    		$error_reason[] = '收件人手机号不能为空';
    	}
    	if(!isset($data['receiver']['receiverName1']) || $data['receiver']['receiverName1']==''){
    		$error_reason[] = '收件人姓名不能为空';
    	}
    	if(!isset($data['receiver']['addressLine1']) || $data['receiver']['addressLine1']==''){
    		$error_reason[] = '收件人地址1不能为空';
    	}
    	if(!isset($data['receiver']['countryCode']) || $data['receiver']['countryCode']==''){
    		$error_reason[] = '收件人国家二字码不能为空';
    	}
    	if(!isset($data['receiver']['city']) || $data['receiver']['city']==''){
    		$error_reason[] = '收件人城市不能为空';
    	}
    	if(!isset($data['receiver']['postalCode']) || $data['receiver']['postalCode']==''){
    		$error_reason[] = '收件人邮编不能为空';
    	}
    	if(!isset($data['receiver']['stateRegionCode']) || $data['receiver']['stateRegionCode']==''){
    		$error_reason[] = '收件人省/州不能为空';
    	}
    	//数据过滤
    	//地址1、地址2、城市、省州、邮编 这四个信息里：如果非英文符号，自动转换成相应的英文符号。中文全角“。”更新成"." ; “，”更新成","
    	$data['receiver']['phone'] = Helper_Common::convertStrType($data['receiver']['phone']);
    	$data['receiver']['addressLine1'] = Helper_Common::convertStrType($data['receiver']['addressLine1']);
    	$data['receiver']['addressLine2'] = Helper_Common::convertStrType(@$data['receiver']['addressLine2']);
    	$data['receiver']['city'] = Helper_Common::convertStrType($data['receiver']['city']);
    	$data['receiver']['stateRegionCode'] = Helper_Common::convertStrType($data['receiver']['stateRegionCode']);
    	$data['receiver']['postalCode'] = Helper_Common::convertStrType($data['receiver']['postalCode']);
    	//收件人电话只保留纯数字：电话里有中英文中杠“—”“-”或者下划线时，或者空格时，自动去掉
    	$data['receiver']['phone'] = preg_replace('/[^\d]/','',$data['receiver']['phone']);
    	//收件人、收件人公司 这两个信息里，不能有英文空格之外的标点符号，有直接用空格替代
    	$data['receiver']['receiverName1'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', $data['receiver']['receiverName1']);
    	$data['receiver']['companyName2'] = preg_replace('/([\x21-\x2f\x3a-\x40\x5b-\x60\x7B-\x7F])/',' ', @$data['receiver']['companyName2']);
    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里：如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
    	$data['receiver']['receiverName1'] = preg_replace('/[　\s]+/u',' ',$data['receiver']['receiverName1']);
    	$data['receiver']['companyName2'] = preg_replace('/[　\s]+/u',' ',$data['receiver']['companyName2']);
    	$data['receiver']['addressLine1'] = preg_replace('/[　\s]+/',' ',$data['receiver']['addressLine1']);
    	$data['receiver']['addressLine2'] = preg_replace('/[　\s]+/',' ',$data['receiver']['addressLine2']);
    	$data['receiver']['city'] = preg_replace('/[　\s]+/',' ',$data['receiver']['city']);
    	$data['receiver']['stateRegionCode'] = preg_replace('/[　\s]+/',' ',$data['receiver']['stateRegionCode']);
    	$data['receiver']['postalCode'] = preg_replace('/[　\s]+/',' ',$data['receiver']['postalCode']);
    	
    	$data['sender']['senderName1'] = preg_replace('/[　\s]+/u',' ',@$data['sender']['senderName1']);
    	$data['sender']['companyName2'] = preg_replace('/[　\s]+/u',' ',@$data['sender']['companyName2']);
    	
    	
    	if (@$data['sender']['phone']&&@$data['sender']['countryCode']&&@$data['sender']['stateRegionCode']&&@$data['sender']['city']&&@$data['sender']['postalCode']&&@$data['sender']['addressLine1']&&@$data['sender']['senderName1']){
    		//发件人信息
    		$sender_country_code=$data['sender']['countryCode'];
    		$sender_state_region_code=$data['sender']['stateRegionCode'];
    		$sender_city=$data['sender']['city'];
    		$sender_postal_code=$data['sender']['postalCode'];
    		$sender_street1=$data['sender']['addressLine1'];
    		$sender_street2=@$data['sender']['addressLine2'];
    		$sender_name2=@$data['sender']['companyName2'];
    		$sender_name1=$data['sender']['senderName1'];
    		$sender_mobile=$data['sender']['phone'];
    		$sender_email=@$data['sender']['email'];
    	}elseif($customer_sign->sender_country_code && $customer_sign->sender_state_region_code && $customer_sign->sender_city && $customer_sign->sender_postal_code && $customer_sign->sender_street1 && $customer_sign->sender_name1 && $customer_sign->sender_mobile){
    		//发件人信息
    		$sender_country_code=$customer_sign->sender_country_code;
    		$sender_state_region_code=$customer_sign->sender_state_region_code;
    		$sender_city=$customer_sign->sender_city;
    		$sender_postal_code=$customer_sign->sender_postal_code;
    		$sender_street1=$customer_sign->sender_street1;
    		$sender_street2=@$customer_sign->sender_street2;
    		$sender_name2=@$customer_sign->sender_name2;
    		$sender_name1=$customer_sign->sender_name1;
    		$sender_mobile=$customer_sign->sender_mobile;
    		$sender_email=$customer_sign->sender_email;
    	}else{
    		$error_reason[] = '发件人信息须全部为空或全部提供且完整';   		
    	}
    	//发件人信息
    	$order->sender_country_code=$sender_country_code;
    	$order->sender_state_region_code=$sender_state_region_code;
    	$order->sender_city=$sender_city;
    	$order->sender_postal_code=$sender_postal_code;
    	$order->sender_street1=$sender_street1;
    	$order->sender_street2=@$sender_street2;
    	$order->sender_name2=@$sender_name2;
    	$order->sender_name1=$sender_name1;
    	$order->sender_mobile=$sender_mobile;
    	$order->sender_email=$sender_email;
    	//US 检查电话号码是否不足10位
    	if($data['receiver']['countryCode']=='US'){
    		if(strlen($data['receiver']['phone'])<10){
    			$error_reason[] = '目的地为US的订单收件人电话字符须不少于10位';
    		}
    	}
    	//BR 检查订单详情里没有提供税号信息
    	if($data['receiver']['countryCode']=='BR'){
    		$tax_payer_id = preg_replace( '/[^0-9]/', '', @$data['taxpayerId']);
    		if(strlen($tax_payer_id) <> 11 && strlen($tax_payer_id) <> 14){
    			$error_reason[] = '目的地为BR的订单收件税号信息必填，且为11或14位';
    		}
    	}
    	//收件人公司里，如果有数字进行预警，预警原因：收件公司信息含有数字
    	if($data['receiver']['receiverName1']){
    		if(preg_match('/\d/',$data['receiver']['receiverName1'])){
    			$error_reason[] = '收件公司或客户名称1含有数字';
    		}
    	}
    	if(@$data['receiver']['companyName2']){
    		if(preg_match('/\d/',@$data['receiver']['companyName2'])){
    			$error_reason[] = '收件公司或客户名称2含有数字';
    		}
    	}
    	if(@$data['receiver']['companyName2']){
    		if(strlen($data['receiver']['companyName2'])<3){
    			$error_reason[] = '收件公司或客户名称2长度须大于2位';
    		}
    	}
    	//收件人、收件人公司、地址1、地址2、城市、省州、邮编 这六个信息里如有非英文符，进行预警，预警原因：某某字段有非英文字符。
    	//地址1、地址2、城市、省州、邮编这五个信息里：英文状态下以下符号不作预警："#" "/" "" "-" "." "," "&" "*" ";" ":" "?" ";" ""
    	if($data['receiver']['receiverName1']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r]/',$data['receiver']['receiverName1'])){
    			$error_reason[] = '收件公司或客户名称1有非英文字符';
    		}
    	}
    	if(@$data['receiver']['companyName2']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r]/',@$data['receiver']['companyName2'])){
    			$error_reason[] = '收件公司或客户名称2有非英文字符';
    		}
    	}
    	if($data['receiver']['addressLine1']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$data['receiver']['addressLine1'])){
    			$error_reason[] = '收件人地址1有非英文字符';
    		}
    	}
    	if(@$data['receiver']['addressLine2']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',@$data['receiver']['addressLine2'])){
    			$error_reason[] = '收件人地址2有非英文字符';
    		}
    	}
    	if($data['receiver']['city']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$data['receiver']['city'])){
    			$error_reason[] = '收件人城市有非英文字符';
    		}
    	}
    	if($data['receiver']['stateRegionCode']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$data['receiver']['stateRegionCode'])){
    			$error_reason[] = '收件人省或州有非英文字符';
    		}
    	}
    	if($data['receiver']['postalCode']){
    		if(preg_match('/[^0-9a-zA-Z\s\n\r\/\\#\-\.,*;:?\'\"]/',$data['receiver']['postalCode'])){
    			$error_reason[] = '收件人邮编有非英文字符';
    		}
    	}
    	$consignee_postal_code=trim($data['receiver']['postalCode']);
    	if(strlen($consignee_postal_code)=='1'){
    		$error_reason[] = '收件人邮编异常';
    	}
    	// EMS订单邮编校验
    	if($product->product_name == 'EMS-FY' || $product->product_name == 'ePacket-FY'){
    		$zipFormat = Zipformat::find('country_code_two = ?',$data['receiver']['countryCode'])->getOne();
    		if(!$zipFormat->isNewRecord()){
    			if(!preg_match($zipFormat->zip_format_preg_match, trim($data['receiver']['postalCode']))){
    				$error_reason[] = '收件人邮编格式不正确,'.$data['receiver']['countryCode'].'的邮编格式为：'.$zipFormat->zip_format;
    			}
    		}
    	}
    	$black=blacklist::find('product_id=?',$product->product_id)->asArray()->getAll();
    	$reason='';
    	//黑名单原因
    	$black_reason = '';
    	if(count($black)>0){
    		foreach ($black as $b){
    			$reason='';
    			$i=0;
    			$bb=array_filter($b);
    			$co=count($bb)-4;
    			if($b['consignee_country_code'] && strtoupper(trim($data['receiver']['countryCode'])) == strtoupper(trim($b['consignee_country_code']))){
    				$reason.='国家:'.$b['consignee_country_code'].',';
    				$i++;
    			}
    			if($b['consignee_postal_code'] && strtoupper(trim($data['receiver']['postalCode'])) ==  strtoupper(trim($b['consignee_postal_code']))){
    				$reason.='邮编:'.$b['consignee_postal_code'].',';
    				$i++;
    			}
    			if($b['consignee_city'] && strtoupper(trim($data['receiver']['city']))==strtoupper(trim($b['consignee_city']))){
    				$reason.='城市:'.$b['consignee_city'].',';
    				$i++;
    			}
    			if($b['consignee_state_region_code'] && strtoupper(trim($data['receiver']['stateRegionCode'])) == strtoupper(trim($b['consignee_state_region_code']))){
    				$reason.='州:'.$b['consignee_state_region_code'].',';
    				$i++;
    			}
    			
    			if(($b['sender_name1'] && (strtoupper(trim($sender_name1)) == strtoupper(trim($b['sender_name1'])) ||  strtoupper(trim($sender_name2)) == strtoupper(trim($b['sender_name1']))))){
    				$reason.='发件人:'.$b['sender_name1'].',';
    				$i++;
    			}
    			if(($b['sender_name2'] && (strtoupper(trim($sender_name2)) == strtoupper(trim($b['sender_name2'])) || strtoupper(trim($sender_name1)) == strtoupper(trim($b['sender_name2']))))){
    				$reason.='发件公司:'.$b['sender_name2'].',';
    				$i++;
    			}
    			
    			if($b['sender_street1']){
    				$condition=str_replace('\\','\\\\',$b['sender_street1']);
    				$condition=str_replace('/','\\/',$condition);
    				
    				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
    				
    				$condition=str_replace('*','.*?',$condition);
    				$data['receiver']['addressLine1']=str_replace(PHP_EOL, '', $data['receiver']['addressLine1']);
    				@$data['receiver']['addressLine2']=str_replace(PHP_EOL, '', @$data['receiver']['addressLine2']);
    				$sender_street1=str_replace(PHP_EOL, '', $sender_street1);
    				$sender_street2=str_replace(PHP_EOL, '', $sender_street2);
    				if (preg_match('/^'.$condition.'$/i', $data['receiver']['addressLine1']) || preg_match('/^'.$condition.'$/i',@$data['receiver']['addressLine2']) || preg_match('/^'.$condition.'$/i',$data['receiver']['addressLine1']) || preg_match('/^'.$condition.'$/i',@$data['receiver']['addressLine2'])){
    					$reason.='地址:'.$b['sender_street1'].',';
    					$i++;
    				}
    			}
    			if($b['product_name']){
    				$j=0;
    				$condition=str_replace('\\','\\\\',$b['product_name']);
    				$condition=str_replace('/','\\/',$condition);
    				
    				$condition=str_replace(array('(',')','.','[',']'),array('\\(','\\)','\\.','\\[','\\]'),$condition);
    				
    				$condition=str_replace('*','.*?',$condition);
    				foreach ($data['products'] as $op){
    					if (preg_match('/^'.$condition.'$/i',$op['productName']) || preg_match('/^'.$condition.'$/i',$op['productNameEn'])){
    						$reason.='品名:'.$b['product_name'].',';$j++;
    					}
    				}
    				if($j>0){
    					$i++;
    				}
    			}
    			if($i > 0 && $i == $co){
    				//黑名单原因
    				$black_reason=trim($reason,',');
    				break;
    			}
    		}
    	}
    	//存在黑名单
    	if($black_reason){
    		$error_reason[] = '此运输方式,'.$black_reason.'黑名单';
    	}
    	$zcode_reason='';
    	if($product->check_zip=='1'){
    		$noservice_zip = Noserivcezipcode::find("zip_code = ? and service_code = ? and city = '' and country_code = ''",$data['receiver']['postalCode'],$product->product_name)->getOne();
    		if(!$noservice_zip->isNewRecord()){
    			$zcode_reason= '邮编或城市或国家无服务';
    		}else{
    			$noservice_city = Noserivcezipcode::find("city = ? and service_code = ? and zip_code = '' and country_code = ''",$data['receiver']['city'],$product->product_name)->getOne();
    			if(!$noservice_city->isNewRecord()){
    				$zcode_reason= '邮编或城市或国家无服务';
    			}else{
    				$noservice_country = Noserivcezipcode::find("country_code = ? and service_code = ? and zip_code = '' and city = ''",$data['receiver']['countryCode'],$product->product_name)->getOne();
    				if(!$noservice_country->isNewRecord()){
    					$zcode_reason= '邮编或城市或国家无服务';
    				}else{
    					$noservice_country = Noserivcezipcode::find("zip_code = ? and country_code = ? and service_code = ? and city = ''",$data['receiver']['postalCode'],$data['receiver']['countryCode'],$product->product_name)->getOne();
    					if(!$noservice_country->isNewRecord()){
    						$zcode_reason= '邮编或城市或国家无服务';
    					}else{
    						$noservice_country = Noserivcezipcode::find("zip_code = ? and city = ? and service_code = ? and country_code = ''",$data['receiver']['postalCode'],$data['receiver']['city'],$product->product_name)->getOne();
    						if(!$noservice_country->isNewRecord()){
    							$zcode_reason= '邮编或城市或国家无服务';
    						}else{
    							$noservice_country = Noserivcezipcode::find("country_code = ? and city = ? and service_code = ? and zip_code = ''",$data['receiver']['countryCode'],$data['receiver']['city'],$product->product_name)->getOne();
    							if(!$noservice_country->isNewRecord()){
    								$zcode_reason= '邮编或城市或国家无服务';
    							}else{
    								$noservice_country = Noserivcezipcode::find("zip_code = ? and country_code = ? and city = ? and service_code = ?",$data['receiver']['postalCode'],$data['receiver']['countryCode'],$data['receiver']['city'],$product->product_name)->getOne();
    								if(!$noservice_country->isNewRecord()){
    									$zcode_reason= '邮编或城市或国家无服务';
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	if ($zcode_reason){
    		$error_reason[] = $zcode_reason;
    	}
    	//收件人信息
    	$order->consignee_mobile=$data['receiver']['phone'];
    	$order->consignee_telephone=$data['receiver']['phone'];
    	$order->consignee_email=@$data['receiver']['email'];
    	$order->consignee_name1=$data['receiver']['receiverName1'];
    	$order->consignee_name2=@$data['receiver']['companyName2'];
    	$order->consignee_street1=$data['receiver']['addressLine1'];
    	$order->consignee_street2=@$data['receiver']['addressLine2'];
    	$order->consignee_country_code=$data['receiver']['countryCode'];
    	$order->consignee_city=$data['receiver']['city'];
    	$order->consignee_postal_code=$data['receiver']['postalCode'];
    	$order->consignee_state_region_code=$data['receiver']['stateRegionCode'];
    	
    	
    	$order->tax_payer_id=@$data['taxpayerId'];
    	$order->delivery_priority=@$data['shippingPriority']=='P'?'P':'G';
    	$order->order_status='1';
    	
    	
    	$order->total_amount=$total_amount;
    	$order->currency_code='USD';
    	$order->customer_id=$customer_sign->customer_id;
    	
    	//fda
    	if (@$data['fdaDeclare']['fdaCompany']&&@$data['fdaDeclare']['fdaAddress']&&@$data['fdaDeclare']['fdaCity']&&@$data['fdaDeclare']['fdaPostCode']){
    		$order->fda_company=@$data['fdaDeclare']['fdaCompany'];
    		$order->fda_address=@$data['fdaDeclare']['fdaAddress'];
    		$order->fda_city=@$data['fdaDeclare']['fdaCity'];
    		$order->fda_post_code=@$data['fdaDeclare']['fdaPostCode'];
    	}elseif (@$data['fdaDeclare']['fdaCompany']||@$data['fdaDeclare']['fdaAddress']||@$data['fdaDeclare']['fdaCity']||@$data['fdaDeclare']['fdaPostCode']){
    		$error_reason[] = 'FDA申报信息（FDA制造商全称，FDA制造商城市，FDA制造商邮编，FDA制造商地址信息）须全部为空或全部提供且完整';
    	}
    	$error_rusult = self::allErrordata($error_reason);
    	if ($error_rusult != 'noerror'){
    		return self::newFailure('4000',$error_rusult);
    	}
    	$order->save();
    	$ali_json->ali_sign=$header['customerSign'];
    	$ali_json->order_id=$order->order_id;
    	$ali_json->ali_order_no=$order->ali_order_no;
    	$ali_json->save();
    	
    	
    	//拆分阿里单号
    	if(strlen($order->reference_no)){
    		$references=explode(",", $order->reference_no);
    		foreach ($references as $r){
    			$alireference=new Alireference();
    			$alireference->order_id=$order->order_id;
    			$alireference->reference_no=$r;
    			$alireference->save();
    		}
    	}
    	//存入product信息
    	Orderproduct::meta()->destroyWhere('order_id=?',$order->order_id);
    	$battery_num = 0;
    	foreach ($data['products'] as $order_product){
    		$oproduct=new Orderproduct();
    		$oproduct->changeProps(array(
    			'order_id'=>$order->id(),
    			'product_name'=>$order_product['productName'],
    			//英文品名如果有连续两个空格时，只保留一个空格；如果是中文空格时，自动转换成英文空格
    			'product_name_en'=>preg_replace('/[　\s]+/u',' ',$order_product['productNameEn']),
    			'product_quantity'=>$order_product['productQuantity'],
    			'product_unit'=>'pcs',
    			'hs_code'=>$order_product['hsCode'],
    			'declaration_price'=>$order_product['declarationPrice'],
    			'has_battery'=>$order_product['hasBattery']=='Y'?1:0,
    			'product_name_far'=>$order_product['productName'],
    			'product_name_en_far'=>preg_replace('/[　\s]+/u',' ',$order_product['productNameEn']),
    			'hs_code_far'=>$order_product['hsCode'],
    			'material_use'=>@$order_product['material'].' '.@$order_product['purpose'],
    			//配货信息
    			'goods_info'=>@$order_product['packinglistInfo']
    		));
    		if($order_product['hasBattery']=='Y'){
    			$battery_num+=$order_product['productQuantity'];
    			$order->has_battery=1;
    			
    		}
    		$oproduct->save();
    		
    	}
    	$order->has_battery_num=$battery_num>2?2:1;
    	// 		$flag=false;//判断阿里推送订单是否为测试订单
    	// 		if($data['transportCode']=='1001'){
    	// 			$flag=true;
    	// 		}
    	// 		if($flag){//是测试订单
    	// 			$order->ali_testing_order='1';
    	// 			$order->save();
    	// 		}
    	$ratio=5000;
    	$total_weight=0;
    	$weight_income_ali=0;
    	//查询product获取计泡系数
    	if(!$product->isNewRecord()){
    		$ratio=$product->ratio;
    	}
    	//存入package信息
    	Orderpackage::meta()->destroyWhere('order_id=?',$order->order_id);
    	Farpackage::meta()->destroyWhere('order_id=?',$order->order_id);
    	Faroutpackage::meta()->destroyWhere('order_id=?',$order->order_id);
    	foreach ($package_arr as $order_package){
	    	$package=new Orderpackage();
	    	$package->changeProps(array(
	    			'order_id'=>$order->id(),
	    			'package_type'=>@$data['packageType']?@$data['packageType']:'BOX',
	    			'quantity'=>$order_package['quantity'],
	    			'unit'=>'CM',
	    			'weight_unit'=>'KG',
	    			'length'=>$order_package['length'],
	    			'width'=>$order_package['width'],
	    			'height'=>$order_package['height'],
	    			'weight'=>$order_package['weight'],
	    	));
	    	$package->save();
	    		
	    	$farpackage=new Farpackage();
	    	$farpackage->changeProps(array(
	    			'order_id'=>$order->order_id,
	    			'quantity'=>$order_package['quantity'],
	    			'length'=>$order_package['length'],
	    			'width'=>$order_package['width'],
	    			'height'=>$order_package['height'],
	    			'weight'=>$order_package['weight']
	    	));
	    	$farpackage->save();
	    		
	    	$faroutpackage=new Faroutpackage(array(
	    			'order_id'=>$order->order_id,
	    			'far_id'=>$farpackage->far_package_id,
	    			'quantity_out'=>$farpackage->quantity,
	    			'length_out'=>$farpackage->length,
	    			'width_out'=>$farpackage->width,
	    			'height_out'=>$farpackage->height,
	    			'weight_out'=>$farpackage->weight,
	    	));
	    	$faroutpackage->save();
	    	$total_weight+=$order_package['weight']*$order_package['quantity'];
	    	$weight_income_ali+=(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']>$order_package['weight']*$order_package['quantity']?
	    		(($order_package['length']*$order_package['width']*$order_package['height'])/$ratio)*$order_package['quantity']:$order_package['weight']*$order_package['quantity'];
    	}
    	
    	//包裹总件数
    	$order->package_total_num=$total_num;
    	//存入阿里计费重量和阿里实重
    	//包裹总重量
    	$order->weight_actual_ali=$total_weight;
    	$order->weight_income_ali=$weight_income_ali;
    	$order->packing_type=@$data['packageType']?@$data['packageType']:'BOX';
    	$productcodes=Product::getprodutcode(2);
    	if(in_array($order->service_code,$productcodes)){
    		$order->add_data_status='1';
    	}
    	//#80732仓库代码
    	$codewarehouses = CodeWarehouse::find()->getAll();
    	foreach ($codewarehouses as $cw){
    		if (@$data['warehouse']['code']==$cw->warehouse){
    			$order->department_id = $cw->department_id;
    		}
    	}
    	$order->save();
    	if (!isset($data['warehouse']) || $data['warehouse']=='' || !@$data['warehouse']['code']){
    		//默认放到线下仓
    		$codedata = CodeWarehouse::find('warehouse=?','ASP_FAR_HZ')->getOne();
    		$order->department_id = $codedata->department_id;
    		$order->warehouse_code='ASP_FAR_HZ';
    		$order->warehouse_name=$codedata->department_name;
    	}
    	$order->save();
    	$conn->completeTrans ();
    	
    	if ($codetransport->book_type==1){
    		$checklabel = Helper_Common::Checklabel($order);
    		if ($checklabel['message']!='success'){
    			return self::newFailure($checklabel['code'],$checklabel['message']);
    		}
    		
    		$dir=Q::ini('upload_tmp_dir');
    		$label_data=base64_encode(file_get_contents($dir.DS.$order->tracking_no.'.pdf'));
    		$result['orderNo'] =  $order->order_no;
    		$result['label']=$label_data;
    		$result['trackingNo'] =  $order->far_no;
    		$result['carrierCode'] =  'FAR';
    		$result['traceUrl'] =  'http://www.far800.com/track?waybillNo='.$order->far_no;
    		//主单号
    		$result['LastMiletrackingNo']=$order->tracking_no;
    		$result['LastMilecarrierCode'] =  $order->channel->trace_network_code;
    		$trace_url = Network::find('network_code=?',$order->channel->trace_network_code)->getOne();
    		$result['LastMiletrackingUrl'] =  $trace_url->trace_url;
    	}else{
    		$farlabel = Helper_Common::getfarlabel($order);
    		$result['orderNo'] =  $order->order_no;
    		$result['label'] =  $farlabel;
    		$result['trackingNo'] =  $order->far_no;
    		$result['carrierCode'] =  'FAR';
    		$result['traceUrl'] = 'http://www.far800.com/track?waybillNo='.$order->far_no;
    	}
    	return self::newSuccess($result);
    	
    }
    /**
     * @todo   组织全部返回错误信息
     * @author stt
     * @since  2020-09-17
     */
    static function allErrordata($error_reason=array())
    {
    	if(count($error_reason)>0){
    		$rea = array_unique($error_reason);
    		$rea = array_filter($rea);
    		$rea = implode('|', $rea);
    		$result = $rea;
    	}else{
    		$result = 'noerror';
    	}
    	return $result;
    }
    /**
     * @todo   返回错误信息
     * @author stt
     * @since  2020-09-10
     */
    static function newFailure($code = '', $data = '')
    {
    	
    	$arr=array(
    		'response'=>array(
    			'success'=>false,
    			'errorCode'=>$code,
    			'errorMsg'=>$data
    		)
    	);
    	QLog::log('newFailure'.json_encode($arr));
    	return json_encode($arr);
    }
    /**
     * @todo   返回成功信息
     * @author stt
     * @since  2020-09-10
     */
    static function newSuccess($result='')
    {
    	$arr=array(
    		'response'=>array(
    			'success'=>true,
    			'result'=>$result
    		)
    	);
    	QLog::log('newSuccess'.json_encode($arr));
    	return json_encode($arr);
    }
   
    
    
}