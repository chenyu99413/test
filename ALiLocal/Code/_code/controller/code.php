<?php
/**
 *
 * @author xuedong
 *
 */
class Controller_Code extends Controller_Abstract {
	function actionSet() {
	}
	/**
	 * @todo   阿里对应IB对应关系
	 * @author 许杰晔
	 * @since  2020-12-14
	 * @return 
	 * @link   #84434
	 */
	function actionProductRelationship(){
		
	}
	/**
	 * @todo   阿里对应IB对应关系列表
	 * @author 许杰晔
	 * @since  2020-12-14
	 * @return
	 * @link   #84434
	 */
	function actionProductRelationshiplist(){
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
		//获取对应关系数据
		$select = CodeProductRelationShip::find ();
		
		//阿里产品代码
		if (request ( 'ali_product' )) {
			$select->where ( 'ali_product = ?', request ( 'ali_product' ) );
		}
		//产品结果
		$products = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->order('id desc')
		->getAll ();
		
		$this->_view ['products'] = $products;
		$this->_view ['pagination'] = $pagination;
	}
	/**
	 * @todo   阿里对应IB对应关系详细
	 * @author 许杰晔
	 * @since  2020-12-14
	 * @return
	 * @link   #84434
	 */
	function actionProductRelationshipEditModal(){
		//产品对应信息
		$product = CodeProductRelationShip::find ( 'id = ?', request ( 'id' ) )->getOne ();
		$this->_view ['product'] = $product;
	}
	/**
	 * @todo   保存阿里对应IB对应关系
	 * @author 许杰晔
	 * @since  2020-12-14
	 * @return
	 * @link   #84434
	 */
	function actionProductRelationshipEditSave(){
		//获取提交轨迹
		if (! request_is_ajax ()) {
			return $this->_redirectAjax ( false );
		}
		//保存产品信息
		$product = new CodeProductRelationShip();
		if (request ( 'id' )) {
			$product = CodeProductRelationShip::find ( 'id = ?', request ( 'id' ) )->getOne ();
			if ($product->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$product->ali_product = request ( 'ali_product' );
		$product->ib_product = request ( 'ib_product' );
		$product->operator=MyApp::currentUser('staff_name');
		$product->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
}