<?php

class Controller_CodeCurrency extends Controller_Abstract {
	function actionSearch() {
	}
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = CodeCurrency::find ();
	
		if (request ( 'code' )) {
			$code = request ( 'code' );
			$select->where ( 'code = ?', $code );
		}
		if (request ( 'name' )) {
			$name = request ( 'name' );
			$select->where ( 'name like ?', "%{$name}%" );
		}
	
		$currencys = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
	
		$this->_view ['currencys'] = $currencys;
		$this->_view ['pagination'] = $pagination;
	}
	function actionEditModal() {
		$currency = CodeCurrency::find ( 'id = ?', request ( 'currency_id' ) )->getOne ();
		$this->_view ['logs'] = CodeCurrencyLog::find('code_id=?',request ( 'currency_id' ))->order('create_time desc')->getAll();
		$this->_view ['currency'] = $currency;
		$item = CodeCurrencyItem::find('oid=?',$currency->id)->getAll();
		$this->_view ['item'] = $item;
		$supplier = Supplier::find()->getAll();
		$this->_view ['supplier'] = $supplier;
	}
	function actionEditSave() {
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		// 数据重复检查
// 		foreach ( array (
// 			'code' => '代码',
// 			'name' => '名称',
// 		    'start_date' => '汇率设置时间'
// 		) as $key => $value ) {
// 			if (request ( $key )) {
// 				$select = CodeCurrency::find ( $key . ' = ?', request ( $key ) );
// 				if (request ( 'currency_id' )) {
// 					$select->where ( 'id <> ?', request ( 'currency_id' ) );
// 				}
// 				if (! $select->getOne ()
// 					->isNewRecord ()) {
// 						return $this->_redirectAjax ( false, $value . '已存在' );
// 					}
// 			}
// 		}
		// 保存数据
		$currency = new CodeCurrency();
		if (request ( 'currency_id' )) {
			$currency = CodeCurrency::find ( 'id = ?', request ( 'currency_id' ) )->getOne ();
			if ($currency->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}else{
			//判断时间不能有交集
			$currency2 = CodeCurrency::find ( 'code = ?', request ( 'code' ) )->order('id desc')->getOne ();
			if(strtotime(request('start_date').' 00:00:00') <= $currency2->end_date){
				return $this->_redirectAjax ( false, '新时间和旧时间不能有交集' );
			}
		}
		//先保存一下，后面可以产生一条日志
		$currency->save();
		$currency->code = request ( 'code' );
		$currency->name = request ( 'name' );
		$currency->rate = request ( 'rate' );
		$currency->start_date = strtotime(request('start_date').' 00:00:00');
		$currency->end_date = strtotime(request('end_date').' 23:59:59');
		$currency->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
	/**
	 * @todo   币种明细修改添加回调函数
	 * @author 吴开龙
	 * @since  2020-9-14 14:29:31
	 * @return 
	 * @link   #82553
	 */
	function actionCurrencyItem(){
		$currency = CodeCurrency::find ( 'id = ?', request ( 'currency_id' ) )->getOne ();
		if (request ( "item" )) {
			$p = request ( "item" );
			$item = CodeCurrencyItem::find ( "item_id = ?", $p ["item_id"] )->getOne ();
			if(!$item->isNewRecord()){
				//如果存在，先保存一下原数据，日志使用
				$supplier_id = $item->supplier_id;
				$rate = $item->rate;
				$start_date1 = $item->start_date;
				$end_date1 = $item->end_date;
			}
			$start_date = strtotime($p['start_date']);
			$end_date = strtotime($p['end_date']);
			$supplier = explode('-',$p['supplier_id']);
			$item->oid = $currency->id;
			$item->rate = $p['rate'];
			$item->start_date = $start_date;
			$item->end_date = $end_date;
			$item->supplier_id = $supplier[0];
			//unset($p['item_id']);
			//$item->changeProps ( $p );
			$item->save ();
			//添加日志
			$feeitemlog = new CodeCurrencyLog();
			$feeitemlog->code_id = $currency->id;
			$feeitemlog->staff_id = MyApp::currentUser('staff_id');
			$feeitemlog->staff_name = MyApp::currentUser('staff_name');
			
			$supp = Supplier::find('supplier_id=?',$supplier_id)->getOne();
			//日志内容
			$comment = '明细：';
			if ($p ["item_id"]) {
				if($supplier[0] != $supplier_id){
					$comment .= '修改供应商['.$supplier_id.'-'.$supp->supplier.']为['.$p['supplier_id'].'];';
				}
				if($p['rate'] != $rate){
					$comment .= '修改汇率['.$rate.']为['.$p['rate'].'];';
				}
				if($start_date != $start_date1){
					$comment .= '修改开始时间['.date('Y-m-d',$start_date1).']为['.date('Y-m-d',$start_date).'];';
				}
				if($end_date != $end_date1){
					$comment .= '修改结束时间['.date('Y-m-d',$end_date1).']为['.date('Y-m-d',$end_date).'];';
				}
			}else{
				if($supplier[0]){
					$comment .= '新增供应商['.$p['supplier_id'].'];';
				}
				if($p['rate']){
					$comment .= '新增汇率['.$p['rate'].'];';
				}
				if($start_date){
					$comment .= '新增开始时间['.date('Y-m-d',$start_date).'];';
				}
				if($end_date){
					$comment .= '新增结束时间['.date('Y-m-d',$end_date).'];';
				}
			}
			if($comment){
				$feeitemlog->comment = $comment;
				$feeitemlog->save();
			}
			
			echo ($item->item_id);
		}
		exit ();
	}
	/**
	 * @todo   币种明细回调函数删除
	 * @author 吴开龙
	 * @since  2020-9-14 13:29:31
	 * @return
	 * @link   #82553
	 */
	function actionCurrencyItemDel() {
		if (request ( "item_id" )) {
			$price = CodeCurrencyItem::find ( "item_id = ?", request ( "item_id" ) )->getOne ();
			//添加日志
			$feeitemlog = new CodeCurrencyLog();
			$feeitemlog->code_id = $price->oid;
			$feeitemlog->staff_id = MyApp::currentUser('staff_id');
			$feeitemlog->staff_name = MyApp::currentUser('staff_name');
			$supp = Supplier::find('supplier_id=?',$price->supplier_id)->getOne();
			$feeitemlog->comment = '明细：删除供应商['.$price->supplier_id.'-'.$supp->supplier.']汇率['.$price->rate.']开始时间['.date('Y-m-d',$price->start_date).']结束时间['.date('Y-m-d',$price->end_date).']的数据';
			$feeitemlog->save();
			$price->destroy ();
		}
		exit ();
	}
}

?>