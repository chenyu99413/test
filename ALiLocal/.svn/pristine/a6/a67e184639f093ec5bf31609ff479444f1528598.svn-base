<?php
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\Exception\WriterException;
use Box\Spout\Reader\Internal\XLSX\Worksheet;

use Box\Spout\Writer\Style\StyleBuilder;
include_once _INDEX_DIR_.'/lib/ext/autoloader.php';
include_once _INDEX_DIR_.'/_code/helper/excelexport.php';
include_once _INDEX_DIR_.'/_code/helper/excelexport2.php';


define('XLSX_EOL', "\n");
class Helper_ExcelX {
	static function array2xlsx($arr,$filename,$widths=null){
		if (is_array($widths)){
			\Box\Spout\Writer\Internal\XLSX\Worksheet::$_WIDTHS=$widths;
		}
		//https://github.com/box/spout
		$writer=WriterFactory::create(Type::XLSX);
		$writer->setTempFolder(INDEX_DIR.DS.'_tmp');
		$writer->openToBrowser($filename.'.xlsx');
		foreach ($arr as $row){
			$writer->addRow($row);
		}
		$writer->close();
	}
	static $writer;
	static function startWriter($filename){
		$writer= new ExportDataExcel('browser',$filename.'.xlsx');
		self::$writer=$writer;
		$writer->initialize();
		return $writer;
	}
	static function addRow($row){
		return self::$writer->addRow($row);
	}
	static function closeWriter(){
		self::$writer->finalize();
	}
	//超过一百万条数据的分批导入
	static $writer2;
	//开始
	static function startWriter2($filename){
		$writer= new ExportDataExcel2('browser',$filename.'.xlsx');
		self::$writer2=$writer;
		$writer->initialize();
		return $writer;
	}
	//插入数据 $page为几，就是第几个sheet
	static function addRow2($row, $page = 1,$header = array()){
		return self::$writer2->addRow($row, $page, $header);
	}
	//结束
	static function closeWriter2(){
		self::$writer2->finalize();
	}
	/**
	 * 读取xlsx文件
	 * @param string $filename
	 * @return \Box\Spout\Reader\ReaderInterface
	 */
	static function readXlsx($filename){
		$reader = ReaderFactory::create(Type::XLSX);
		$reader->open($filename);
		/*
			foreach ($reader->getSheetIterator() as $sheet) {
			foreach ($sheet->getRowIterator() as $row) {
			// do stuff with the row
			}
			}
	
			$reader->close();
		*/
		return $reader;
	}
	/**
	 * 保存excel文件到服务器上
	 * @param unknown $arr
	 * @param unknown $filename
	 */
	static function savexlsx($arr,$filename){
		header("Content-type:text/html;charset=utf-8");
		$writer=WriterFactory::create(Type::XLSX);
		$writer->setTempFolder(INDEX_DIR.DS.'_tmp');
		$writer->openToFile(INDEX_DIR.DS.'_tmp'.DS.'upload'.DS.$filename);
		foreach ($arr as $row){
			$writer->addRow($row);
		}
		$writer->close();
	}
}
