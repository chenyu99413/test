<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';

class Controller_Tracking extends Controller_Abstract {
	/**
	 * @todo   轨迹重查列表
	 * @author 吴开龙
	 * @since  2020-10-15 15:00:54
	 * @param
	 * @return
	 * @link   #83125
	 */
	function actionList(){
		$pos = TrailTotal::find();
		if(request('total_order')){
			$pos->where('total_order=?',request('total_order'));
		}
		$page = request_is_post () ? 1 : request ( 'page' );
		$pos=$pos->limitPage ( $page, 30 )
		->fetchPagination ( $this->_view ['pagination'] )
		->order('id desc')->getAll();
		$this->_view['pos']=$pos;
	}
	/**
	 * @todo   轨迹重查添加页面
	 * @author 吴开龙
	 * @since  2020-10-15 15:40:54
	 * @param
	 * @return
	 * @link   #83125
	 */
	function actionAdd(){
		if(request_is_post ()){
			$trail1 = TrailTotal::find('total_order=?',request('total_order'))->getOne();
			if(!$trail1->isNewRecord()){
				return $this->_redirectMessage ( '失败', '总单号重复', url ( "/add" ) );
			}
			$tracking_nos = explode("\r\n", request('tracking_no'));
			$tracking_nos = array_filter($tracking_nos);
			if(!empty($tracking_nos)){
				$trail = new TrailTotal();
				$trail->total_order = request('total_order');
				$trail->save();
				foreach ($tracking_nos as $tracking_no){
					if(!$tracking_no){
						continue;
					}
					$order = Order::find ( 'tracking_no = ?', $tracking_no )->getOne ();
					if ($order->isNewRecord ()) {
						echo $tracking_no . '订单不存在<br>';
						continue;
					}
					if($order->order_status==9){
						echo $tracking_no . '已签收<br>';
						continue;
					}
					$tracko=Tracking::find('order_id=? and send_flag=1',$order->order_id)->order('tracking_id desc')->getOne();
					if($tracko->isNewRecord()){
						echo $tracking_no . '无轨迹数据<br>';
						continue;
					}
					//echo $order->order_id;
					if($tracko->route_id==0){
						echo $tracking_no . '异常<br>';
						continue;
					}
					$route_id=$tracko->route_id;
					$tracks=Tracking::find('tracking_id > ? and order_id = ?',$tracko->tracking_id,$order->order_id)->group('trace_time')->order('tracking_id')->getAll();
					if(count($tracks)){
						$tracks->destroy();
					}
					
					$route=Route::find('id >? and tracking_no=?',$route_id,$order->tracking_no)->getAll();
					if(count($route)){
						$route->destroy();
					}
					$order->get_trace_flag=4;
					$order->save();
					$trail_detail = new TrailTotalDetail();
					$trail_detail->total_id = $trail->id;
					$trail_detail->ali_order_no = $order->ali_order_no;
					$trail_detail->tracking_no = $tracking_no;
					$trail_detail->save();
					echo $tracking_no . '成功<br>';
				}
			}
			
			echo "<br>结束";
			echo '<p><a href="'.url('/add').'">返回</a></p>';
			exit;
		}
	}
	/**
	 * @todo   轨迹重查明细页面
	 * @author 吴开龙
	 * @since  2020-10-16 15:40:54
	 * @param
	 * @return
	 * @link   #83125
	 */
	function actionModify(){
		$trail_detail = TrailTotalDetail::find('total_id=?',request('total_id'));
		if(request('ali_order_no')){
			$trail_detail->where('ali_order_no=?',request('ali_order_no'));
		}
		if(request('tracking_no')){
			$trail_detail->where('tracking_no=?',request('tracking_no'));
		}
		$page = request_is_post () ? 1 : request ( 'page' );
		$trail_detail=$trail_detail->limitPage ( $page, 30 )
		->fetchPagination ( $this->_view ['pagination'] )
		->order('detail_id desc')->getAll();
		$this->_view['trail_detail']=$trail_detail;
	}
	/**
	 * @todo   轨迹重查
	 * @author 许杰晔
	 * @since  2020-9-29 15:32:54
	 * @param
	 * @return
	 * @link
	 */
	function actionTrackRecheck() {
		set_time_limit ( 0 );
		ini_set ( "memory_limit", "3072M" );
		if (request_is_post ()) {
			$errors = array ();
			$uploader = new Helper_Uploader ();
			//检查指定名字的上传对象是否存在
			if (! $uploader->existsFile ( 'file' )) {
				return $this->_redirectMessage ( '未上传文件', '', url ( 'tracking/trackrecheck' ) );
			}
			$file = $uploader->file ( 'file' ); //获得文件对象
			if (! $file->isValid ( 'xls,xlsx' )) {
				return $this->_redirectMessage ( '文件格式不正确：xls、xlsx', '', url ( 'tracking/trackrecheck' ) );
			}
			$des_dir = Q::ini ( 'upload_tmp_dir' ); //缓存路径
			$filename = date ( 'YmdHis' ) . 'trackrecheck.' . $file->extname ();
			$file_route = $des_dir . DS . $filename;
			$file->move ( $file_route );
			
			$xls = Helper_Excel::readFile ( $file_route, true );
			$sheet = $xls->toHeaderMap ();
			//导入的表中有数据
			//必填字段
			$required_fields = array (
				'末端物流单号'
			);
			$result = array();
			if (! empty ( $sheet )) {
				foreach ( $sheet as $k => $row ) {
					
					//判断基础信息不得为空
					foreach ( $required_fields as $field ) {
						if (! isset ( $row [$field] )) {
							return $this->_redirectMessage ( '失败', '模板字段缺失，请检查', url ( "tracking/trackrecheck" ) );
						}
						if (! strlen ( $row [$field] )) {
							$result[] = '【' . $field . '】不能为空';
							continue;
						}
					}
					
					$order = Order::find ( 'tracking_no = ?', $row ['末端物流单号'] )->getOne ();
					if ($order->isNewRecord ()) {
						$result[] = $row ['末端物流单号'] . '订单不存在';
						continue;
					}
					if($order->order_status==9){
						$result[] = $row ['末端物流单号'] . '已签收';
						continue;
					}
					$tracko=Tracking::find('order_id=? and send_flag=1',$order->order_id)->order('tracking_id desc')->getOne();
					if(!$tracko->isNewRecord()){
						//echo $order->order_id;
						if($tracko->route_id==0){
							$result[] = $row ['末端物流单号'] . '异常';
							continue;
						}
						$route_id=$tracko->route_id;
						$tracks=Tracking::find('tracking_id > ? and order_id = ?',$tracko->tracking_id,$order->order_id)->group('trace_time')->order('tracking_id')->getAll();
						if(count($tracks)){
							$tracks->destroy();
						}
						
						$route=Route::find('id >? and tracking_no=?',$route_id,$order->tracking_no)->getAll();
						if(count($route)){
							$route->destroy();
						}
						$order->get_trace_flag=4;
						$order->save();						
					}else{
						$result[] = $row ['末端物流单号'] . '无轨迹数据';
						continue;
					}
					$result[] = '成功';
				}
				$this->_view ['result'] = $result;
			} else {
				return $this->_redirectMessage ( '失败', '请修改表格填写内容', url ( "tracking/trackrecheck" ) );
			}
		}
	}	

	
	/**
	 * @todo   轨迹重查文档下载
	 * @author 许杰晔
	 * @since  2020-8-17 10:32:54
	 * @param
	 * @return
	 * @link
	 */
	function actionDownloadbatchtraceTemp(){
		return $this->_redirect ( QContext::instance ()->baseDir () . 'public/download/轨迹重查重推.xlsx' );
	}
}
