<?php
 class Controller_SignUnusual extends Controller_Abstract {
 	/**
 	 * @todo   签收异常预警
 	 * @author stt
 	 * @since  2020年12月18日14:11:25
 	 * @param
 	 * @link   #84564
 	 */
 	function actionlist(){
 		// 签收异常已确认和待确认
 		$select = Order::find('is_signunusual=1 or is_signunusual=2');
 		// 阿里单号搜索
 		if (request('ali_order_no')){
 			$select->where('ali_order_no=?',trim(request('ali_order_no')));
 		}
 		//克隆对象
 		$order_count=clone $select;
 		//根据状态分组查询
 		$counts=$order_count->group('is_signunusual')->count()->columns('is_signunusual')->asArray()->getAll();
 		//根据分组字段序列化数组
 		$counts=Helper_Array::toHashmap($counts,'is_signunusual','row_count');
 		//counts[0]是【全部】状态
 		$counts[0] = 0;
 		foreach ($counts as $v){
 			$counts[0]+=$v;
 		}
 		$active_id = 0;
 		// 待确认
 		if (request ( "parameters" ) == "no_confirm") {
 			$select->where('is_signunusual=1');
 			$active_id = 1;
 		}
 		// 已确认
 		if (request ( "parameters" ) == "confirm") {
 			$select->where('is_signunusual=2');
 			$active_id = 2;
 		}
 		$pagination = null;
 		$this->_view ["active_id"] = $active_id;
 		$this->_view ["tabs"] = $this->createTabs ( $counts );
 		$this->_view['list'] = $select->limitPage(request('page',1),request( 'page_size', 30 ))
 		->order('order_id')
 		->fetchPagination($pagination)
 		->getAll();
 		$this->_view ['pagination']=$pagination;
 	}
 	function createTabs($counts) {
 		$tab = array (
 			array (
 				"id" => "0","title" => "全部","count" => val ( $counts, 0, 0 ),
 				"href" => "javascript:TabSwitch()"
 			),
 			array (
 				"id" => "1","title" => "待确认","count" => val ( $counts, 1, 0 ),
 				"href" => "javascript:TabSwitch('no_confirm')"
 			),
 			array (
 				"id" => "2","title" => "已确认","count" => val ( $counts, 2, 0 ),
 				"href" => "javascript:TabSwitch('confirm')"
 			)
 		);
 		return $tab;
 	}
 	/**
 	 * @todo   保存签收时间
 	 * @author stt
 	 * @since  2020年12月18日14:35:12
 	 * @link   #84564
 	 */
 	function actionsavedeliverytime(){
 		if(request('order_id')){
 			//修改tb_order表数据
 			$order = Order::find('order_id=?',request('order_id'))->getOne();
 			//is_delivery=1代表有新的签收轨迹
 			$route = Route::find('tracking_no=? and is_delivery=1',$order->tracking_no)->order('id desc')->getOne();
 			if (!$route->isNewRecord()){
 				//有签收轨迹保存签收时间
 				if ($route->time){
	 				//签收时间
	 				$order->delivery_time = $route->time;
 				}
 			}
 			//已确认
 			$order->is_signunusual = 2;
 			$order->save();
 		}
 		return $this->_redirectMessage('成功', '修改成功', url('/list'));
 	}
 	
 	
 }