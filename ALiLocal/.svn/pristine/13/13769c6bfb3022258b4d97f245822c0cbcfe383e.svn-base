<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';
/**
 * @todo 时区管理
 * @author 吴开龙
 * @since 2020-6-23 09:14:07
 * @param 
 * @return json
 * @link #80454
 */
class Controller_CodeTimeZone extends Controller_Abstract {
	function actionSearch() {
		
	}
	function actionList() {
		$page = intval ( request ( 'page', 1 ) );
		$page_size = intval ( request ( 'page_size', 30 ) );
		$pagination = null;
	
		$select = CityTimezone::find ();
	
		if (request ( 'code_word_two' )) {
			$code_word_two = request ( 'code_word_two' );
			$select->where ( 'code_word_two = ?', $code_word_two );
		}
		if (request ( 'city' )) {
			$city = request ( 'city' );
			$select->where ( 'city = ?', $city );
		}
		$timezone = $select->limitPage ( $page, $page_size )
		->fetchPagination ( $pagination )
		->getAll ();
	
		$this->_view ['timezones'] = $timezone;
		$this->_view ['pagination'] = $pagination;
	}
	function actionEditModal() {
		$timezone = CityTimezone::find ( 'id = ?', request ( 'id' ) )->getOne ();
		$this->_view ['timezone'] = $timezone;
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
		$timezone = new CityTimezone();
		if (request ( 'id' )) {
			$timezone = CityTimezone::find ( 'id = ?', request ( 'id' ) )->getOne ();
			if ($timezone->isNewRecord ()) {
				return $this->_redirectAjax ( false, '数据错误' );
			}
		}
		$timezone->code_word_two = request ( 'code_word_two' );
		$timezone->city = request ( 'city' );
		$timezone->timezone = request ( 'timezone' );
		$timezone->save ();
		return $this->_redirectAjax ( true, '保存成功' );
	}
	/**
	 * @todo 时区管理 导出
	 * @author 吴开龙
	 * @since 2020-7-14 16:14:07
	 * @param
	 * @return json
	 * @link #81131
	 */
	function actionExport(){
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '2G');
		set_time_limit(0);
		$select = CityTimezone::find ()->getAll();
		$header = array (
			'国家二字码',
			'城市代码',
			'时区'
		);
		$sheet = array (
			$header
		);
		foreach ($select as $value){
			$row =array(
				$value->code_word_two,
				$value->city,
				$value->timezone
			);
			$sheet [] = $row;
		}
		Helper_Excel::array2xls ( $sheet, '城市时区管理导出.xls' );
		exit ();
	}
	/**
	 * @todo 时区管理 导入
	 * @author 吴开龙
	 * @since 2020-7-14 16:14:07
	 * @param
	 * @return json
	 * @link #81131
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
		$required_fields = array (
			'国家二字码',
			'城市代码',
			'时区'
		);
		if(!isset($sheets[0]['国家二字码']) || !isset($sheets[0]['城市代码']) || !isset($sheets[0]['时区'])){
			return $this->_redirectMessage('缺少字段:国家二字码、城市代码、时区','',url('code/set'), 3 );
		}
		$timezone = CityTimezone::find()->getAll()->destroy();
		foreach ($sheets as $sheet){
			$timezone = new CityTimezone();
			$timezone->code_word_two = $sheet['国家二字码'];
			$timezone->city = $sheet['城市代码'];
			$timezone->timezone = $sheet['时区'];
			$timezone->save();
		}
		return $this->_redirectMessage ( '导入成功', '成功', url ( 'code/set' ), 3 );
		exit ();
	}
}

?>