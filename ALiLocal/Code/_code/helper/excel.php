<?php
Q::import ( _INDEX_DIR_ . '/_library/phpexcel/PHPEXCEL' );
require_once _INDEX_DIR_ . '/_library/phpexcel/PHPExcel.php';

class Helper_Excel {
	/**
	 * 读取Excel 文件并处理
	 * 
	 * @param string $filename 文件路径
	 * @param string $ReadDataOnly 是否仅读取数据，不读取格式信息
	 *
	 * @example $rows=Helper_Excel::readFile('abc.xls')->getSheetData();
	 *         
	 * @return Helper_Excel_File
	 */
	static function readFile($filename, $ReadDataOnly = FALSE) {
		return new Helper_Excel_File ( $filename, $ReadDataOnly );
	}
	
	/**
	 * 从数组生成Xls文件
	 *
	 * @param array $array        	
	 * @param string $saveFileName        	
	 * @param string $sheetname
	 *        	标题
	 * @example $arr=array(
	 *          array('Name','Sex'),
	 *          array('小黄', '男'),
	 *          array('小花','女')
	 *          );
	 *          Helper_Excel::array2xls($arr,'2014-13.xls');
	 */
	static function array2xls($array, $saveFileName = null, $sheetname = null, $sheet2Array = null, $sheet2name = null) {
		$objExcel = new PHPExcel ();
		$sheet = $objExcel->getActiveSheet ();
		if (! is_null ( $sheetname )) {
			$sheet->setTitle ( $sheetname );
		}
		//    	$objExcel->getDefaultStyle()->getFont()->setName('宋体');
		//    	$objExcel->getDefaultStyle()->getFont()->setSize(12);
		set_time_limit ( 300 );
		@$sheet->fromArray ( $array, null, 'A1', true );
		if (! is_null ( $sheet2Array )) {
			//$objExcel->setActiveSheetIndex(1);
			$sheet2 = $objExcel->addSheet ( new PHPExcel_Worksheet () );
			if (! is_null ( $sheet2name )) {
				$sheet2->setTitle ( $sheet2name );
			}
			@$sheet2->fromArray ( $sheet2Array, null, 'A1', true );
		}
		$objExcel->setActiveSheetIndex ( 0 );
		//2007
		//    	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//2003
		if (! is_null ( $saveFileName )) {
			if (substr ( $saveFileName, - 4, 4 ) == 'xlsx') {
				header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel2007' );
			} else {
				header ( 'Content-Type: application/vnd.ms-excel' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel5' );
			}
			header ( 'Content-Disposition: attachment;filename="' . $saveFileName . '"' );
			header ( 'Cache-Control: max-age=0' );
			try {
				@$objWriter->save ( 'php://output' );
			} catch ( PHPExcel_Writer_Exception $ex ) {
				$tmpF = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time () . '.xlsx';
				@$objWriter->save ( $tmpF );
				readfile ( $tmpF );
				unlink ( $tmpF );
			}
		} else {
			return $objExcel;
		}
	}
	/**
	 * 保存excel文件到服务器上
	 * @param unknown $arr
	 * @param unknown $filename
	 */
	static function savexlsx($array, $saveFileName = null, $sheetname = null, $sheet2Array = null, $sheet2name = null) {
		$objExcel = new PHPExcel ();
		$sheet = $objExcel->getActiveSheet ();
		if (! is_null ( $sheetname )) {
			$sheet->setTitle ( $sheetname );
		}
		set_time_limit ( 300 );
		$head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','W','Z',
			'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AW','AZ');
		foreach ($array as $k=>$v){
			$meger='';
			if(strstr($v[0], '部门')){
				$meger.='A'.($k+1).':'.$head[count($v)-1].($k+1);
				$sheet->mergeCells( $meger); 
				$sheet->getStyle( 'A'.($k+1))->getFont()->setSize(20);
				$sheet->getStyle( 'A'.($k+1))->getFont()->setBold(true);
				$sheet->getRowDimension($k+1)->setRowHeight(50);
			}
		}
		
		
		@$sheet->fromArray ( $array, null, 'A1', true );
		if (! is_null ( $sheet2Array )) {
			//$objExcel->setActiveSheetIndex(1);
			$sheet2 = $objExcel->addSheet ( new PHPExcel_Worksheet () );
			if (! is_null ( $sheet2name )) {
				$sheet2->setTitle ( $sheet2name );
			}
			@$sheet2->fromArray ( $sheet2Array, null, 'A1', true );
		}
		$objExcel->setActiveSheetIndex ( 0 );
		if (! is_null ( $saveFileName )) {
			if (substr ( $saveFileName, - 4, 4 ) == 'xlsx') {
				//header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel2007' );
			} else {
				header ( 'Content-Type: application/vnd.ms-excel' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel5' );
			}
// 			header ( 'Content-Disposition: attachment;filename="' . $saveFileName . '"' );
// 			header ( 'Cache-Control: max-age=0' );
			try {
				@$objWriter->save ( INDEX_DIR.DS.'_tmp'.DS.'upload'.DS.$saveFileName );
			} catch ( PHPExcel_Writer_Exception $ex ) {
				$tmpF = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time () . '.xlsx';
				@$objWriter->save ( $tmpF );
				readfile ( $tmpF );
				unlink ( $tmpF );
			}
		} else {
			return $objExcel;
		}
	}
	
	/**
	 * 从数组生成Xls文件
	 *
	 * @param array $array
	 * @param string $saveFileName
	 * @param string $sheetname
	 *        	标题
	 * @example $arr=array(
	 *          array('Name','Sex'),
	 *          array('小黄', '男'),
	 *          array('小花','女')
	 *          );
	 *          Helper_Excel::array2xls($arr,'2014-13.xls');
	 */
	static function array2xls_sheet($array, $saveFileName = null, $sheetname = null, $sheet2Array = null, $sheet2name = null,$sheet3Array = null, $sheet3name = null) {
		$objExcel = new PHPExcel ();
		$sheet = $objExcel->getActiveSheet ();
		if (! is_null ( $sheetname )) {
			$sheet->setTitle ( $sheetname );
		}
		//    	$objExcel->getDefaultStyle()->getFont()->setName('宋体');
		//    	$objExcel->getDefaultStyle()->getFont()->setSize(12);
		set_time_limit ( 300 );
		@$sheet->fromArray ( $array, null, 'A1', true );
		if (! is_null ( $sheet2Array )) {
			//$objExcel->setActiveSheetIndex(1);
			$sheet2 = $objExcel->addSheet ( new PHPExcel_Worksheet () );
			if (! is_null ( $sheet2name )) {
				$sheet2->setTitle ( $sheet2name );
			}
			@$sheet2->fromArray ( $sheet2Array, null, 'A1', true );
		}
		if (! is_null ( $sheet3Array )) {
			//$objExcel->setActiveSheetIndex(1);
			$sheet3 = $objExcel->addSheet ( new PHPExcel_Worksheet () );
			if (! is_null ( $sheet3name )) {
				$sheet3->setTitle ( $sheet3name );
			}
			@$sheet3->fromArray ( $sheet3Array, null, 'A1', true );
		}
		$objExcel->setActiveSheetIndex ( 0 );
		//2007
		//    	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//2003
		if (! is_null ( $saveFileName )) {
			if (substr ( $saveFileName, - 4, 4 ) == 'xlsx') {
				header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel2007' );
			} else {
				header ( 'Content-Type: application/vnd.ms-excel' );
				@$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel5' );
			}
			header ( 'Content-Disposition: attachment;filename="' . $saveFileName . '"' );
			header ( 'Cache-Control: max-age=0' );
			try {
				@$objWriter->save ( 'php://output' );
			} catch ( PHPExcel_Writer_Exception $ex ) {
				$tmpF = INDEX_DIR . DS . '_tmp' . DS . 'upload' . DS . 'tmp' . time () . '.xlsx';
				@$objWriter->save ( $tmpF );
				readfile ( $tmpF );
				unlink ( $tmpF );
			}
		} else {
			return $objExcel;
		}
	}
	
	/**
	 * 大数据excel导出
	 * 
	 * @param string $filename        	
	 * @param string $type        	
	 * @return ExportDataExcel
	 * @example $helper=Helper_Excel::writeInit('abc.xls');
	 *          $helper->initialize();
	 *          $helper->addRow(array(a,b,c));
	 *          $helper->finalize();
	 */
	static function writeInit($filename, $type = 'browser') {
		require_once INDEX_DIR . DS . '_library' . DS . 'excelexport.php';
		return new ExportDataExcel ( $type, " ".$filename );
	}
}
/**
 * 读取Excel文件
 *
 * @author firzen
 *        
 */
class Helper_Excel_File {
	static $_reader;
	/**
	 *
	 * @var PHPExcel
	 */
	static $_excel;
	function __construct($filename, $ReadDataOnly = FALSE) {
		if (is_null ( self::$_reader )) {
			$fileinfo = pathinfo ( $filename );
			if (strtolower ( $fileinfo ['extension'] ) == 'xlsx') {
				self::$_reader = new PHPExcel_Reader_Excel2007 ();
			} else {
				self::$_reader = new PHPExcel_Reader_Excel5 ();
			}
		}
		if ($ReadDataOnly) {
			self::$_reader->setReadDataOnly ( true );
		}
		/* @var $excel PHPExcel */
		self::$_excel = self::$_reader->load ( $filename );
		
		return $this;
	}
	/**
	 * 获得默认工作表的数据
	 */
	function getSheetData($sheetName=null) {
		/* @var $sheet 	PHPExcel_Worksheet */
		if (is_null($sheetName)){
			$sheet = self::$_excel->getActiveSheet ();
		}else {
			$sheet =self::$_excel->getSheetByName('pp');
		}
		if ($sheet instanceof PHPExcel_Worksheet){
			return $sheet->toArray ();
		}
		return false;
	}
	/**
	 * 获得以ActiveSheet第一行为key的数组集合
	 *
	 * @return array
	 */
	function toHeaderMap($sheetName=null, $trim = true) {
		if (is_null($sheetName)){
			$sheet = self::$_excel->getActiveSheet ();
		}else {
			$sheet = self::$_excel->getSheetByName($sheetName);
		}
		if ($sheet ==false ){
			throw new QException('Excel 文件无法读取');
		}
		$data_array = $sheet->toArray ();
		$new_data_array = array ();
		if (count ( $data_array ) > 1) {
			foreach ( range ( 1, count ( $data_array ) - 1 ) as $i ) {
				if (count ( $data_array [0] ) != count ( $data_array [$i] )) {
					continue;
				}
				if ($trim) {
					foreach ( $data_array [$i] as &$v ) {
						$v = trim ( $v );
					}
				}
				$new_data_array [] = array_combine ( $data_array [0], $data_array [$i] );
			}
		}
		return $new_data_array;
	}
}