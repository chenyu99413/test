<?php
/**
 * ALI api助手
 *
 * @author D23
 *
 */
class Helper_ALI {
    /**
     * 获取签名
     * @return string
     */
    function sign($url,$data,$action){
        $secretKey='7pph77bslrI';
        $urlpath=strstr($url, 'param2/');
        $s=$urlpath.$action.'DTO'.$data;
        $signature=strtoupper(bin2hex(hash_hmac('sha1',$s,$secretKey,true)));
        return $signature;
    }
    /**
     * notifyBizEvent
     * @return string
     */
    function notifyBizEvent($event){
        //根据事件时区生成标准格式事件时间
        $str='';
        if($event->timezone>='0'){
        	if(strlen(abs($event->timezone))=='1'){
        		$str='+0'.abs($event->timezone).'00';
            }else{
            	$str='+'.abs($event->timezone).'00';
            }
        }else{
            if(strlen($event->timezone)=='2'){
                $str='-0'.abs($event->timezone).'00';
            }else{
                $str='-'.abs($event->timezone).'00';
            }
        }
        $event_time=substr(date(DATE_ISO8601,$event->event_time), 0,strlen(date(DATE_ISO8601,$event->event_time))-5).$str;
        $order=Order::find("order_id=?",$event->order_id)->getOne();
        $notifybizevent=array();
        if(!$order->isNewRecord()){
            $event_code=Eventcode::find('event_code=?',$event->event_code)->getOne();
            if(!$event_code->isNewRecord()){
            	$notifybizeventdto=array();
            	$notifybizevent=array('aliOrderNo'=>$order->ali_order_no,'eventTime'=>$event_time,'eventCode'=>$event->event_code,'status'=>'SUCCESS','location'=>$event->event_location,'reason'=>'');
                if($event->reason){
                	$notifybizevent['status']="FAILURE";
                	$notifybizevent['reason']=$event->reason;
                }
                // 称重
                if($event->event_code=='CHECK_WEIGHT'){
                    foreach ($order->farpackages as $package){
                    	$l_w_h=array(ceil($package->length),ceil($package->width),ceil($package->height));
    					rsort($l_w_h);
    					$notifybizeventdto[]=array(
                            'packageType'=>'BOX',
                            'quantity'=>$package->quantity,
                            'unit'=>'CM',
                            'length'=>$l_w_h[0],
                            'width'=>$l_w_h[1],
                            'height'=>$l_w_h[2],
                            'weight'=>$package->weight,
                            'weightUnit'=>'KG'
                        );
                    }
                    $notifybizevent['extInfo']=json_encode($notifybizeventdto);
                    // 核查
                }elseif ($event->event_code=='CONFIRM'){
                	$confirm_ext=array();
                    $confirm_ext['trackNo']=$order->far_no;
                    // 目前默认批次是1
                    $confirm_ext['batchId']='1';
                    foreach ($order->fees as $fee){
                        if($fee->fee_type=='1'){
                        	$notifybizeventdto[]=array(
                                'code'=>$fee->fee_item_code,
                                'name'=>$fee->fee_item_name,
                                'quantity'=>$fee->quantity
                            );
                        }
                    }
                    if($order->service_code=='ePacket-FY'){
                    	$notifybizeventdto[]=array(
                            'code'=>"logisticsExpressASP_EX0038",
                            'name'=>"小包操作费",
                            'quantity'=>"1"
                        );
                    }
                    $confirm_ext['chargeItemArray']=$notifybizeventdto;
                    $notifybizevent['extInfo']=json_encode($confirm_ext);
                    // 承运商取件，出库之后操作
                }elseif ($event->event_code=='CARRIER_PICKUP'){
                	$notifybizeventdto=array('carrierName'=>'FAR','location'=>$event->event_location);
                	$notifybizevent['extInfo']=json_encode($notifybizeventdto);
                }elseif ($event->event_code=='LOAD'){
                    $channel=Channel::find('channel_id=?',$order->channel_id)->getOne();
                    $network=Network::find('network_code=?',$channel->trace_network_code)->getOne();
                    $containerno=explode(',',$event->container_no);
                    $notifybizeventdto=array(
                        'saillingDate'=>$event->sailling_date,
                        'containerNo'=>$containerno,
                        'lastMileTrackNo'=>$order->tracking_no,
                        'lastMileCarrier'=>$network->network_name,
                        'billOfLadingNo'=>$event->bill_no,
                    );
                    $notifybizevent['extInfo']=json_encode($notifybizeventdto);
                }
            }
        }
        return $notifybizevent;
    }
    /**
     * notifyTrace
     * @return string
     */
    function notifyTrace($tracking){
        //根据轨迹时区生成标准格式轨迹时间
        $str='';
        $tracking->timezone=str_replace('+','',$tracking->timezone);
        if($tracking->timezone>='0'){
            if(strlen($tracking->timezone)=='1'){
                $str='+0'.$tracking->timezone.'00';
            }else{
                $str='+'.$tracking->timezone.'00';
            }
        }else{
            if(strlen($tracking->timezone)=='2'){
                $str='-0'.abs($tracking->timezone).'00';
            }else{
                $str='-'.abs($tracking->timezone).'00';
            }
        }
        $trace_time=substr(date(DATE_ISO8601,$tracking->trace_time), 0,strlen(date(DATE_ISO8601,$tracking->trace_time))-5).$str;
        $order=Order::find('order_id=?',$tracking->order_id)->getOne();
        $notifyTrace=array();
        $notifyTrace['aliOrderNo']=$order->ali_order_no;
        $tracking_code=$tracking->tracking_code;
        if($tracking_code=="F_DELIVERY_5044"){
        	$tracking_code='UNKNOWN';
        }
        $notifyTrace['traceInfos'][]=array(
            'location'=>$tracking->location,
        	'traceCode'=>$tracking_code,
        	'traceDescEn'=>(($tracking_code=='F_CARRIER_PICKUP_RT_5035' || $tracking_code=='UNKNOWN') && $tracking->trace_desc_en)?$tracking->trace_desc_en:(isset(Tracking::$trace_code_en[$tracking_code]) ?Tracking::$trace_code_en[$tracking_code] :$tracking->trace_desc_en),
        	'traceDescCn'=>(($tracking_code=='F_CARRIER_PICKUP_RT_5035' || $tracking_code=='UNKNOWN') && $tracking->trace_desc_cn)?$tracking->trace_desc_cn:(isset(Tracking::$trace_code_cn[$tracking_code]) ?Tracking::$trace_code_cn[$tracking_code] :$tracking->trace_desc_cn),
            'operatorName'=>$tracking->operator_name,
            'quantity'=>$tracking->quantity,
            'traceTime'=>$trace_time
        );
        return $notifyTrace;
    }
}
class AlLException extends QException{}
