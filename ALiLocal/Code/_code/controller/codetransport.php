<?php
/**
 * @todo   运输方式管理
 * @author 吴开龙
 * @since  2020-9-11 09:07:24
 * @return
 * @link   #82521
 */
class Controller_CodeTransport extends Controller_Abstract {
	/**
	 * @todo   运输方式管理主页
	 * @author 吴开龙
	 * @since  2020-9-11 09:07:24
	 * @return
	 * @link   #82521
	 */
	function actionSearch() {
	}
	/**
	 * @todo   运输方式管理列表页
	 * @author 吴开龙
	 * @since  2020-9-11 09:07:24
	 * @return
	 * @link   #82521
	 */
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
		$select = CodeTransport::find ();
		
		if (request ( 'code' )) {
			$code = request ( 'code' );
			$select->where ( 'code = ?', $code );
		}
		if (request ( 'name' )) {
			$name = request ( 'name' );
			$select->where ( 'name like ?', "%{$name}%" );
		}
		if (request ( 'product_id' )) {
			$product_id = request ( 'product_id' );
			$select->where ( 'product_id = ?', $product_id );
		}
		
		$currencys = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
		
		$this->_view ['currencys'] = $currencys;
		$this->_view ['pagination'] = $pagination;
	}
	/**
	 * @todo   运输方式管理详情修改页面
	 * @author 吴开龙
	 * @since  2020-9-11 09:07:24
	 * @return
	 * @link   #82521
	 */
	function actionEditModal() {
		$currency = CodeTransport::find ( 'id = ?', request ( 'id' ) )->getOne ();
		$this->_view ['logs'] = CodeTransportLog::find('code_id=?',request ( 'id' ))->order('create_time desc')->getAll();
		$this->_view ['currency'] = $currency;
	}
	/**
	 * @todo   运输方式管理后台修改方法
	 * @author 吴开龙
	 * @since  2020-9-11 09:07:24
	 * @return
	 * @link   #82521
	 */
	function actionEditSave() {
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		// 保存数据
		$currency = new CodeTransport();
		if (request ( 'id' )) {
			$currency = CodeTransport::find ( 'id = ?', request ( 'id' ) )->getOne ();
			if ($currency->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$currency->code = request ( 'code' );
		$currency->name = request ( 'name' );
		$currency->product_id = request ( 'product_id' );
		$currency->channel_id = request ( 'channel_id' );
		$currency->book_type = request ( 'book_type' );
		$currency->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
	/**
	 * @todo   根据产品选择渠道，ajax方法
	 * @author 吴开龙
	 * @since  2020-9-11 09:07:24
	 * @return
	 * @link   #82521
	 */
	function actionChange(){
		$cost = ChannelCost::find('product_id=?',request('product_id'))->asArray()->getAll();
		$data = array();
		foreach ($cost as $c){
			$channel = Channel::find('channel_id=?',$c['channel_id'])->getOne();
			$data[] = array(
				'channel_id' => $c['channel_id'],
				'channel_name' => $channel->channel_name
			);
		}
		return json_encode($data);
	}
	/**
	 * @todo   运输方式
	 * @author stt
	 * @since  2020-9-28
	 * @return
	 * @link   #82849
	 */
	function actiontransporttree() {
		//默认选中
		$checkeds = array ();
		if (request ( "checked" ) != null) {
			$checkeds = explode ( ",", request ( "checked" ) );
		}
		$codetransports = CodeTransport::find()->getAll();
		foreach ( $codetransports as $codetran ) {
			$array [] = array (
				"id" => $codetran->id,
				"text" => $codetran->code,
				"checked" => in_array ( $codetran->id, $checkeds ) ? "checked" : "",
				"attributes" => ""
			);
		}
		echo (json_encode ( $array ));
		exit ();
	}
	/**
	 * @todo   渠道
	 * @author 吴开龙
	 * @since  2020-12-31 13:52:15
	 * @return
	 * @link   #84543
	 */
	function actiontransporttree2() {
		//默认选中
		$checkeds = array ();
		if (request ( "checked" ) != null) {
			$checkeds = explode ( ",", request ( "checked" ) );
		}
		//取出数据
		$codetransports = Channel::find('channel_status=1')->getAll();
		//循环
		foreach ( $codetransports as $codetran ) {
			$array [] = array (
				"id" => $codetran->channel_id,
				"text" => $codetran->channel_name,
				"checked" => in_array ( $codetran->channel_id, $checkeds ) ? "checked" : "",
				"attributes" => ""
			);
		}
		//输出
		echo (json_encode ( $array ));
		exit ();
	}
}

?>