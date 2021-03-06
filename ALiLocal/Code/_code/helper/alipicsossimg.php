<?php
use OSS\OssClient;
use OSS\Core\OssException;


class Helper_AlipicsOssImg {
	/**
	 * @todo   阿里oss上传图片(本地图片上传)
	 * @author 吴开龙
	 * @since  2020-8-28 11:24:05
	 * @param  $track_no：文件名
	 * @return json
	 * @link   #81902
	 */
	static function uploadAlifiles($track_no){
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php';
		}
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php';
		}
		
		// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
		$accesskeyid = "LTAIoc2nJkBFzud9";
		$accesskeysecret = "fBTeUJdNXY02f7IDTdP7xxkMJpw72i";
		// Endpoint以杭州为例，其它Region请按实际情况填写。
		$endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
		// 设置存储空间名称。
		$bucket= "ia1";
		// 设置文件名称。
		$object = "alifiles/ali/".$track_no;
		// <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
		$filepath = _INDEX_DIR_ . "/_tmp/".$track_no;
		
		try{
			$ossclient = new OssClient($accesskeyid, $accesskeysecret, $endpoint);
			
			$ossclient->uploadFile($bucket, $object, $filepath);
			
		} catch(OssException $e) {
			$msg['message']='FAILED';
			$msg['reason']=$e->getMessage();
			QLog::log('FAILED' );
			QLog::log($e->getMessage() );
			return $msg;
		}
		$msg['message']='OK';
		QLog::log('OK' );
		return $msg;
	}
	/**
	 * @todo   阿里oss上传图片(文件流上传)
	 * @author 吴开龙
	 * @since  2020-8-28 14:24:05
	 * @param  $track_no：文件名  $is_file：文件流
	 * @return json
	 * @link   #81902
	 */
	static function uploadAlistrings($track_no,$is_file){
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php';
		}
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php';
		}
		// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
		$accesskeyid = "LTAIoc2nJkBFzud9";
		$accesskeysecret = "fBTeUJdNXY02f7IDTdP7xxkMJpw72i";
		// Endpoint以杭州为例，其它Region请按实际情况填写。
		$endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
		// 设置存储空间名称。
		$bucket= "ia1";
		// 设置文件名称。
		$object = 'alifiles/'.date('Ymd').'/'.$track_no;
		
		QLog::log('wkl:'.$object );
		try{
			$ossclient = new OssClient($accesskeyid, $accesskeysecret, $endpoint);
			$ossclient->putObject($bucket, $object, $is_file);
		} catch(OssException $e) {
			QLog::log('FAILED' );
			QLog::log($e->getMessage() );
			return false;
		}
		QLog::log('OK' );
		$endpoint2 = "http://ia1.oss-cn-hangzhou.aliyuncs.com";
		return $endpoint2.'/'.$object;
	}
	/**
	 * @todo   阿里oss上传图片(文件流上传)httx
	 * @author 吴开龙
	 * @since  2021-1-5 11:04:13
	 * @param  $track_no：文件名  $is_file：文件流
	 * @return json
	 * @link   #84966
	 */
	static function uploadAlistringsHttx($track_no,$is_file){
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/autoload.php';
		}
		if (is_file(_INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php')) {
			require_once _INDEX_DIR_ . '/_code/helper/aliyunoss/vendor/autoload.php';
		}
		// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
		$accesskeyid = "LTAIoc2nJkBFzud9";
		$accesskeysecret = "fBTeUJdNXY02f7IDTdP7xxkMJpw72i";
		// Endpoint以杭州为例，其它Region请按实际情况填写。
		$endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
		// 设置存储空间名称。
		$bucket= "ia1";
		// 设置文件名称。
		$object = 'httx/'.date('Ymd').'/'.$track_no;
		
		QLog::log('wkl:'.$object );
		try{
			//上传
			$ossclient = new OssClient($accesskeyid, $accesskeysecret, $endpoint);
			$ossclient->putObject($bucket, $object, $is_file);
		} catch(OssException $e) {
			QLog::log('FAILED' );
			QLog::log($e->getMessage() );
			return false;
		}
		QLog::log('OK' );
		$endpoint2 = "http://ia1.oss-cn-hangzhou.aliyuncs.com";
		return $endpoint2.'/'.$object;
	}
}