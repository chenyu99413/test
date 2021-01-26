<?php
/**
 * curl请求助手
 * 
 * @package helper
 */
class Helper_Curl {
	static $connecttimeout = 10;
	static $timeout = 30;
	static $last_post_info = null;
	static $last_error = null;
	/**
	 * 发起请求
	 *
	 * @param string $url        	
	 * @param string $requestBody        	
	 * @param string $requestHeader        	
	 * @param bool $justInit        	
	 * @param string $responseSaveToFileName        	
	 * @return bool|string
	 */
	static function post($url, $requestBody, $requestHeader = null, $justInit = false, $responseSaveToFileName = null,$method=null) {
		$connection = curl_init ();
		
		curl_setopt ( $connection, CURLOPT_URL, $url );
		curl_setopt ( $connection, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $connection, CURLOPT_SSL_VERIFYHOST, 0 );
		if (! is_null ( $requestHeader )) {
			curl_setopt ( $connection, CURLOPT_HTTPHEADER, $requestHeader );
		}
		if (is_null($method)){
			curl_setopt ( $connection, CURLOPT_POST, 1 );
		}else {
			curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);
		}
		
		curl_setopt ( $connection, CURLOPT_POSTFIELDS, $requestBody );
		if (! is_null ( $responseSaveToFileName )) {
			$fp = fopen ( $responseSaveToFileName, 'w' );
			curl_setopt ( $connection, CURLOPT_FILE, $fp );
		} else {
			curl_setopt ( $connection, CURLOPT_RETURNTRANSFER, 1 );
		}
		curl_setopt ( $connection, CURLOPT_CONNECTTIMEOUT, self::$connecttimeout );
		curl_setopt ( $connection, CURLOPT_TIMEOUT, self::$timeout );
		if ($justInit) {
			return $connection;
		}
		$response = curl_exec ( $connection );
		self::$last_post_info = curl_getinfo ( $connection );
		$error = curl_error ( $connection );
		curl_close ( $connection );
		if (! is_null ( $responseSaveToFileName )) {
			fclose ( $fp );
		}
		if ($error) {
			throw new CurlExcpetion_Connection_Timeout ( $url );
		}
		return $response;
	}
	static function multiPost($curlHandles) {
		self::$last_error = array ();
		self::$last_post_info = array ();
		$mh = curl_multi_init ();
		foreach ( $curlHandles as $ch ) {
			curl_multi_add_handle ( $mh, $ch );
		}
		$still_running = 1;
		do {
			usleep ( 500 );
			curl_multi_exec ( $mh, $still_running );
		} while ( $still_running > 0 );
		$results = array ();
		foreach ( $curlHandles as $id => $ch ) {
			$results [$id] = curl_multi_getcontent ( $ch );
			self::$last_post_info [$id] = curl_getinfo ( $ch );
			self::$last_error [$id] = curl_error ( $ch );
			curl_multi_remove_handle ( $mh, $ch );
		}
		curl_multi_close ( $mh );
		return $results;
	}
	static function downloadFile($remote, $local, $timeout = 10) {
		$cp = curl_init ( $remote );
		$fp = fopen ( $local, "w" );
		
		curl_setopt ( $cp, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt ( $cp, CURLOPT_TIMEOUT, 3600 );
		curl_setopt ( $cp, CURLOPT_FILE, $fp );
		curl_setopt ( $cp, CURLOPT_HEADER, 0 );
		
		$r = curl_exec ( $cp );
		curl_close ( $cp );
		fclose ( $fp );
		return $r;
	}
	static function get($url) {
		$ch = curl_init ();
		//设置选项，包括URL
		curl_setopt ( $ch, CURLOPT_URL,$url);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch,CURLOPT_ENCODING , "");

		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, self::$connecttimeout );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, self::$timeout );

		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		//执行并获取HTML文档内容
		$output = curl_exec ( $ch );
		//释放curl句柄
		curl_close ( $ch );
		return $output;
	}
	static function get1($url,$requestHeader=null,$justInit=false,$responseSaveToFileName=null){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);  //设置访问的url地址
	    if (!is_null($requestHeader)){
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
	    }
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
	    $result =  curl_exec($ch);
	    curl_close ($ch);
	    return $result;
	}
}
class CurlExcpetion_Connection_Timeout extends QException {}
