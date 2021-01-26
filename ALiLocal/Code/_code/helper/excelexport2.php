<?php
// php-export-data by Eli Dickinson, http://github.com/elidickinson/php-export-data

/**
 * ExportData is the base class for exporters to specific file formats. See other
 * classes below.
 */
abstract class ExportData2 {
	protected $exportto; // Set in constructor to one of 'browser', 'file', 'string' 
	protected $stringdata; // stringdata so far, used if export string mode 
	protected $tempfile; // handle to temp file (for export file mode) 
	protected $tempfilename; // temp file name and path (for export file mode) 

	public $filename; // file mode: the output file name; browser mode: file name for download; string mode: not used 

	public function __construct($exportto = "browser", $filename = "exportdata") {
		if(!in_array($exportto, array('browser','file','string') )) {
			throw new Exception("$exportto is not a valid ExportData export type");
		}
		$this->exportto = $exportto;
		$this->filename = $filename;
	}

	public function initialize() {

		switch($this->exportto) {
			case 'browser':
				$this->sendHttpHeaders();
				break;
			case 'string':
				$this->stringdata = '';
				break;
			case 'file':
				$this->tempfilename = tempnam(sys_get_temp_dir(), 'exportdata');
				$this->tempfile = fopen($this->tempfilename, "w");
				break;
		}

		$this->write($this->generateHeader());
	}

	public function addRow($row, $page, $header) {
		//当page不等于1的时候开始执行判断分页
		if($page != 1){
			//获取字符串
			$among = $this->generateAmong($page);
			//输出
			$this->write($among);
			//判断如果不为空就添加头部标题
			if($among != ''){
				$this->write($this->generateRow($header));
			}
		}
		$this->write($this->generateRow($row));
	}

	public function finalize() {

		$this->write($this->generateFooter());

		switch($this->exportto) {
			case 'browser':
				flush();
				break;
			case 'string':
				// do nothing
				break;
			case 'file':
				// close temp file and move it to correct location
				fclose($this->tempfile);
				rename($this->tempfilename, $this->filename);
				break;
		}
	}

	public function getString() {
		return $this->stringdata;
	}

	abstract public function sendHttpHeaders();

	protected function write($data) {
		switch($this->exportto) {
			case 'browser':
				echo $data;
				break;
			case 'string':
				$this->stringdata .= $data;
				break;
			case 'file':
				fwrite($this->tempfile, $data);
				break;
		}
	}

	protected function generateHeader() {
		// can be overridden by subclass to return any data that goes at the top of the exported file
	}

	protected function generateFooter() {
		// can be overridden by subclass to return any data that goes at the bottom of the exported file		
	}

	// In subclasses generateRow will take $row array and return string of it formatted for export type
	abstract protected function generateRow($row);

	abstract protected function generateAmong($row);
	
}

/**
 * ExportDataTSV - Exports to TSV (tab separated value) format.
 */
class ExportDataTSV2 extends ExportData2 {
	function generateAmong($row) {
	}
	function generateRow($row) {
		foreach ($row as $key => $value) {
			// Escape inner quotes and wrap all contents in new quotes.
			// Note that we are using \" to escape double quote not ""
			$row[$key] = '"'. str_replace('"', '\"', $value) .'"';
			$string = $row[$key];
			if($string[0] === 0 || $string[0] === '0')
			{
				$row[$key] = ' '.$string;
			}
		}
		return implode("\t", $row) . "\n";
	}

	function sendHttpHeaders() {
		header("Content-type: text/tab-separated-values");
    header("Content-Disposition: attachment; filename=".basename($this->filename));
	}
}

/**
 * ExportDataCSV - Exports to CSV (comma separated value) format.
 */
class ExportDataCSV2 extends ExportData2 {
	function generateAmong($row) {
	}
	function generateRow($row) {
		foreach ($row as $key => $value) {
			// Escape inner quotes and wrap all contents in new quotes.
			// Note that we are using \" to escape double quote not ""
			$row[$key] = '"'. str_replace('"', '\"', $value) .'"';
		}
		$string = $row[$key];
		if($string === 0 || $string === '0')
		{
			$row[$key] = ' '.$string;
		}
		return implode(",", $row) . "\n";
	}

	function sendHttpHeaders() {
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".basename($this->filename));
	}
}


/**
 * ExportDataExcel2 exports data into an XML format  (spreadsheetML) that can be 
 * read by MS Excel 2003 and newer as well as OpenOffice
 * 
 * Creates a workbook with a single worksheet (title specified by
 * $title).
 * 
 * Note that using .XML is the "correct" file extension for these files, but it
 * generally isn't associated with Excel. Using .XLS is tempting, but Excel 2007 will
 * throw a scary warning that the extension doesn't match the file type.
 * 
 * Based on Excel XML code from Excel_XML (http://github.com/oliverschwarz/php-excel)
 *  by Oliver Schwarz
 */
class ExportDataExcel2 extends ExportData2 {

	const XMLHEADER = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
	const XMLFOOTER = "</Workbook>";
	static $style;

	public $encoding = 'UTF-8'; // encoding type to specify in file. 
	// Note that you're on your own for making sure your data is actually encoded to this encoding
	// title for Worksheet 
	public $title = 'Sheet';
	//sheet 计数
	public $jishu = 1; 
	//写入开头
	function generateHeader() {

		// workbook header
		$output = stripslashes(sprintf(self::XMLHEADER, $this->encoding)) . "\n";

		// Set up styles
		if (self::$style) {
			$output.=self::$style;
		}else{
			$output .= "<Styles>\n";
			$output .= "<Style ss:ID=\"sDT\"><NumberFormat ss:Format=\"Short Date\"/></Style>\n";
			$output .= "</Styles>\n";
		}

		// worksheet header
		$output .= sprintf("<Worksheet ss:Name=\"%s\">\n    <Table>\n", htmlentities($this->title.$this->jishu));

		return $output;
	}
	//分页函数
	function generateAmong($page) {
		$output = '';
		//当第一次累加page则执行分页
		if($page - $this->jishu == 1){
			//保存累加数字
			$this->jishu ++;
			//写入新sheet工作表
			$output .= "    </Table>\n</Worksheet>\n";
			// worksheet header
			$output .= sprintf("<Worksheet ss:Name=\"%s\">\n    <Table>\n", htmlentities($this->title.$this->jishu));
		}
		//返回
		return $output;
	}
	//写入结尾
	function generateFooter() {
		$output = '';

		// worksheet footer
		$output .= "    </Table>\n</Worksheet>\n";

		// workbook footer
		$output .= self::XMLFOOTER;

		return $output;
	}
	//添加数据
	function generateRow($row) {
		$output = '';
		$output .= "        <Row>\n";
		foreach ($row as $k => $v) {
			$output .= $this->generateCell($v);
		}
		$output .= "        </Row>\n";
		return $output;
	}
	//具体数据添加内容
	private function generateCell($item) {
		$output = '';
		$style = '';
		// Tell Excel to treat as a number. Note that Excel only stores roughly 15 digits, so keep 
		// as text if number is longer than that.
		if (substr($item,0,1)=='\''){
			$type='String';
			$item=substr($item,1);
		}elseif(preg_match("/^-?\d+(?:[.,]\d+)?$/",$item) && (strlen($item) < 15)) {
			$type = 'Number';
		}
		// Sniff for valid dates; should look something like 2010-07-14 or 7/14/2010 etc. Can
		// also have an optional time after the date.
		//
		// Note we want to be very strict in what we consider a date. There is the possibility
		// of really screwing up the data if we try to reformat a string that was not actually 
		// intended to represent a date.
		elseif(preg_match("/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/",$item) &&
					($timestamp = strtotime($item)) &&
					($timestamp > 0) &&
					($timestamp < strtotime('+500 years'))) {
			$type = 'DateTime';
			$item = strftime("%Y-%m-%dT%H:%M:%S",$timestamp);
			$style = 'sDT'; // defined in header; tells excel to format date for display
		}
		else {
			$type = 'String';
		}

		$item = str_replace('&#039;', '&apos;', htmlspecialchars($item, ENT_QUOTES));
		$output .= "            ";
		$output .= $style ? "<Cell ss:StyleID=\"$style\">" : "<Cell>";
		$output .= sprintf("<Data ss:Type=\"%s\">%s</Data>", $type, $item);
		$output .= "</Cell>\n";
		return $output;
	}

	function sendHttpHeaders() {
		header("Content-Type: application/vnd.ms-excel; charset=" . $this->encoding);
		header("Content-Disposition: inline; filename=\"" . basename($this->filename) . "\"");
	}

}