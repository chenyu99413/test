<?php

/**
 * 微信助手
 * @author firzen
 *
 */
class Helper_WX{
	const APPID='wxc93221ac4f50006b';
	const SECRET='daeaeed9caf07a6a4eddb067c610e69b';
	const DEBUG=false;
	static $access_token;
	static $ticket;
	static function getAccessToken(){
		$cacheId='wxAT';
		self::$access_token=Q::cache($cacheId,array('life_time'=>3600));
		if (!(self::$access_token)){
			//https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx576b6a5034613477&secret=05df36cfdde0e2f1eaddf9ab43858284
			//http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=h-L2fsr4P4N1ZgxSSLGQqrqeJPM1alnOtkXXs1Y6weqMOU3L9w2KMK8fTKhWKnCwkAM7FNOym9IOJvkq1ol9c4BgUBL7qxxmMGIrXsW4Z6M&media_id=3Kp5uIOysITDBMqjg-JVa8_y0cUpfoFZZmGKjoFTi20l8cGBbFvn0vpUPb7cO0k1
			
			$ret=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::APPID.'&secret='.self::SECRET);
			QLog::log($ret);
			$ret=json_decode($ret,true);
			if ($ret['access_token']){
				self::$access_token=$ret['access_token'];
				Q::writeCache($cacheId, self::$access_token,array('life_time'=>3600));
			}else {
				return false;
			}
		}
		return self::$access_token;
	}
	static function getTicket(){
		$cacheId='wxTK';
		self::$ticket=Q::cache($cacheId,array('life_time'=>3600));
		if (!(self::$ticket)){
			$token=self::getAccessToken();
			$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$token.'&type=jsapi';
			$ret=file_get_contents($url);
			QLog::log($ret);
			$ret=json_decode($ret,true);
			if ($ret['ticket']){
				self::$ticket=$ret['ticket'];
				Q::writeCache($cacheId, self::$ticket,array('life_time'=>3600));
			}else {
				return false;
			}
		}
		return self::$ticket;
	}
	static function download($serverId,$filepath){
		$url=self::mediaURL($serverId);
		return file_put_contents($filepath,file_get_contents($url));
	}
	static function jsConfig($apiList){
		$ts=time();
		$nonceStr='13tim'.$ts;
		$config=array(
			'debug'=>self::DEBUG,// 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			'appId'=>self::APPID,
			'timestamp'=>$ts,
			'nonceStr'=>$nonceStr,
			'signature'=>sha1('jsapi_ticket='.self::getTicket().'&noncestr='.$nonceStr.'&timestamp='.$ts.'&url=http://www.far800.com'.$_SERVER['REQUEST_URI']),
			'jsApiList'=>$apiList,
		);
		return json_encode($config);
	}
	static function mediaURL($serverId){
		return $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='. self::getAccessToken().'&media_id='.$serverId;;
	}
	static function loginStep1($url) {
		return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPID.'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
	}
	static function loginStep2($code) {
		$r=Helper_Curl::get('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.self::APPID.'&secret='.self::SECRET.'&code='.$code.'&grant_type=authorization_code');
		$r=json_decode($r,true);
		$r2=Helper_Curl::get('https://api.weixin.qq.com/sns/userinfo?access_token='.$r['access_token'].'&openid='.$r['openid'].'&lang=zh_CN');
		return json_decode($r2,true);
	}
}