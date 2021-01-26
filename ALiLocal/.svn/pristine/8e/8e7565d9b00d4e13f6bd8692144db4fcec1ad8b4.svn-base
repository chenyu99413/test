<?php
/**
 * @todo   事件处理
 * @author stt
 * @since  August 21th 2020
 * @link   #81992
 */
class Helper_Event{
	/**
	 * @todo   保存事件
	 * @author stt
	 * @since  August 21th 2020
	 * @link   #81992
	 */
	function saveEvent( $customer_id, $order_id, $event_code, $event_time, $event_location,$reason=NULL,$flag=0) {
		$current_event=Event::find('order_id= ? and event_code=?',$order_id,$event_code)->getOne();
		
		if($current_event->isNewRecord()){
			if (in_array($event_code, array('WAREHOUSE_INBOUND','CHECK_WEIGHT','CONFIRM'))){
				$event = Event::find ( 'order_id=? and event_code="CONFIRM"', $order_id )->getOne ();
			}elseif (in_array($event_code, array('SORTING_CENTER_INBOUND_CALLBACK','CHECK_WEIGHT_CALLBACK','CONFIRM_CALLBACK'))){
				$event = Event::find ( 'order_id=? and event_code="CONFIRM_CALLBACK"', $order_id )->getOne ();
			}else{
				$event = new Event();
			}
			$event->order_id = $order_id;
			$event->customer_id = $customer_id;
			$event->event_code = $event_code;
			$event->event_time = $event_time;
			if ($flag==1){
				$event->location = $event_location;
			}
			$event->event_location = $event_location;
			$event->reason = $reason;
			$event->timezone = '8';
			$event->confirm_flag = '1';
			$event->operator =MyApp::currentUser('staff_name');
			$event->save ();
		}
	}
}