<?php
class Controller_Download extends Controller_Abstract {
	/**
	 */
	function actionIndex() {
		$filePath = request ( "file_path" );
		if (! file_exists ( $filePath )) {
			echo "文件找不到";
			exit ();
		} else {
			$file = fopen ( $filePath, "r" );
			$fileSize = filesize ( $filePath );
				
			// 输入文件标签
			Header ( "Content-type: application/octet-stream" );
			Header ( "Accept-Ranges: bytes" );
			Header ( "Content-length: " . $fileSize );
			Header ( "Content-Disposition: attachment; filename=\"" . request ( "file_name", time () ) . "\"" );
			// 输出文件内容
			echo fread ( $file, $fileSize );
			fclose ( $file );
			exit ();
		}
	}
	
	/**
	 * 下载客户端
	 */
	function actionCsclient() {
	    return $this->_redirect(QContext::instance()->baseDir(). 'public/download/far800国际快件.rar');
	}
	
	function actionFirefoxclient(){
	    return $this->_redirect(QContext::instance()->baseDir(). 'public/download/Firefox Setup 49.0.2.exe');
	}
	
	
	/**
	 * 创建下载
	 */
	static function createDownloadFile($name) {
		$file_name = iconv ( "utf-8", "gb2312", $name );
		$filePath = _INDEX_DIR_ . '/public/download/' . $file_name;
		$fp = fopen ( $filePath, "r" );
		$file_size = filesize ( $filePath );
		
		header ( "Content-Type: application/force-download" );
		header ( "Content-Type: application/octet-stream" );
		header ( "Content-Type: application/download" );
		header ( 'Content-Disposition:inline;filename="' . $file_name . '"' );
		header ( "Content-Transfer-Encoding: binary" );
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Pragma: no-cache" );
		
		$buffer = 1024;
		$file_count = 0;
		
		while ( ! feof ( $fp ) && $file_count < $file_size ) {
			$file_con = fread ( $fp, $buffer );
			$file_count += $buffer;
			echo $file_con;
		}
		fclose ( $fp );
		exit ();
	}
	
	/**
	 * 判断文件是否存在
	 */
	function actionCheckfile() {
		$filePath = request ( "file_path" );
		if (! file_exists ( $filePath )) {
			if (request ( "langu" )) {
				echo "File not found.";
			} else
				echo "文件找不到";
		} else {
			echo "";
		}
		exit ();
	}
}