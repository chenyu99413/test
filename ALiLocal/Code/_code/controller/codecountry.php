<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';
/**
   * @todo   国家管理
   * @author 吴开龙
   * @since  2020-8-12 09:14:07
   * @param 
   * @return json
   * @link   #80454
 */
class Controller_CodeCountry extends Controller_Abstract {
	function actionSearch() {
		
	}
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = Country::find ();
	
		if (request ( 'code_word_two' )) {
			$code_word_two = request ( 'code_word_two' );
			$select->where ( 'code_word_two = ?', $code_word_two );
		}
		if (request ( 'code_word_three' )) {
			$code_word_three = request ( 'code_word_three' );
			$select->where ( 'code_word_three = ?', $code_word_three );
		}
		$country = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->order('id desc')
		->getAll ();
	
		$this->_view ['country'] = $country;
		$this->_view ['pagination'] = $pagination;
	}
	function actionEditModal() {
		$country = Country::find ( 'id = ?', request ( 'id' ) )->getOne ();
		$this->_view ['country'] = $country;
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
		$country = new Country();
		if (request ( 'id' )) {
			$country = Country::find ( 'id = ?', request ( 'id' ) )->getOne ();
			if ($country->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$country->code_word_two = request ( 'code_word_two' );
		$country->code_word_three = request ( 'code_word_three' );
		$country->english_name = request ( 'english_name' );
		$country->english_name2 = request ( 'english_name2' );
		$country->chinese_name = request ( 'chinese_name' );
		$country->customs_country_code = request ( 'customs_country_code' );
		$country->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
   /**
   * @todo   国家管理 导出
   * @author 吴开龙
   * @since  2020-8-12 12:14:07
   * @param
   * @return json
   * @link   #81131
   */
	function actionExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		$select = Country::find ()->getAll();
		$header = array (
			'国家二字码',
			'国家三字码',
			'英文名称1',
			'英文名称2',
			'中文名称',
			'国家关税代码'
		);
		$sheet = array (
			$header
		);
		foreach ($select as $value){
			$row =array(
				$value->code_word_two,
				$value->code_word_three,
				$value->english_name,
				$value->english_name2,
				$value->chinese_name,
				$value->customs_country_code
			);
			$sheet [] = $row;
		}
		Helper_Excel::array2xls ( $sheet, '国家管理导出.xls' );
		exit ();
	}
   /**
   * @todo   国家管理 导入
   * @author 吴开龙
   * @since  2020-8-12 13:14:07
   * @param
   * @return json
   * @link   #81131
   */
	function actionImport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		//上传文件开始
		$uploader = new Helper_Uploader();
		//检查指定名字的上传对象是否存在
		if (! $uploader->existsFile ( 'file' )) {
			return $this->_redirectMessage('未上传文件','',url('code/set'), 3 );
		}
		$file = $uploader->file ( 'file' );//获得文件对象
		if (! $file->isValid ( 'xls' )) {
			return $this->_redirectMessage('文件格式不正确：xls','',url('code/set'), 3 );
		}
		$des_dir = Q::ini ( 'upload_tmp_dir' );//缓存路径
		$filename = $des_dir.DS.date ( 'YmdHis' ).'feeimport.'.$file->extname ();
		$file->move ( $filename );
		ini_set ( "memory_limit", "3072M" );
		$xls = Helper_Excel::readFile ( $filename,true);
		$sheets =$xls->toHeaderMap ();
		//必填字段
		if(!isset($sheets[0]['国家二字码'])){
			return $this->_redirectMessage('缺少数据','',url('code/set'), 3 );
		}
		$timezone = Country::find()->getAll()->destroy();
		foreach ($sheets as $sheet){
			if(!$sheet['国家二字码']){
				continue;
			}
			$timezone = new Country();
			$timezone->code_word_two = $sheet['国家二字码'];
			$timezone->code_word_three = $sheet['国家三字码'];
			$timezone->english_name = $sheet['英文名称1'];
			$timezone->english_name2 = $sheet['英文名称2'];
			$timezone->chinese_name = $sheet['中文名称'];
			$timezone->customs_country_code = $sheet['国家关税代码'];
			$timezone->save();
		}
		return $this->_redirectMessage ( '导入成功', '成功', url ( 'code/set' ), 3 );
		exit ();
	}
}

?>