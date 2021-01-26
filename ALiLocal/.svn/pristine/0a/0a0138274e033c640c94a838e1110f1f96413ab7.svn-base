<?php

class Controller_CodeLogistics extends Controller_Abstract{

	function actionSearch() {
	}
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = CodeLogistics::find ();
	
		if (request ( 'code' )) {
			$code = request ( 'code' );
			$select->where ( 'code = ?', $code );
		}
		if (request ( 'name' )) {
			$name = request ( 'name' );
			$select->where ( 'name like ?', "%{$name}%" );
		}
	
		$logistics = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
	
		$this->_view ['logistics'] = $logistics;
		$this->_view ['pagination'] = $pagination;
	}
	function actionEditModal() {
		$Logistic = CodeLogistics::find ( 'id = ?', request ( 'logistic_id' ) )->getOne ();
		$this->_view ['Logistic'] = $Logistic;
	}
	function actionEditSave() {
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		// 数据重复检查
		foreach ( array (
			'code' => '代码',
			'name' => '名称'
		) as $key => $value ) {
			if (request ( $key )) {
				$select = CodeLogistics::find ( $key . ' = ?', request ( $key ) );
				if (request ( 'logistic_id' )) {
					$select->where ( 'id <> ?', request ( 'logistic_id' ) );
				}
				if (! $select->getOne ()
					->isNewRecord ()) {
						return $this->_redirectAjax ( false, $value . '已存在' );
				}
			}
		}
	
		// 保存数据
		$logistics = new CodeLogistics();
		if (request ( 'logistic_id' )) {
			$logistics = CodeLogistics::find ( 'id = ?', request ( 'logistic_id' ) )->getOne ();
			if ($logistics->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$logistics->code = request ( 'code' );
		$logistics->name = request ( 'name' );
		$logistics->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
}

?>