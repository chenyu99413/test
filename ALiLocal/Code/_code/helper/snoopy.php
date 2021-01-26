<?php
require INDEX_DIR.'/_library/Snoopy.class.php';
/**
 * 信息采集助手
 * @author firzen
 *
 */
class Helper_Snoopy {
	/**
	 * 模拟提交表单
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	static function submit($url,$params){
		$snoopy=new Snoopy();
		$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
		$snoopy->referer = "http://www.4wei.cn";
		$snoopy->rawheaders["Pragma"] = "no-cache";
		$snoopy->maxredirs = 2;
		$snoopy->offsiteok = false;
		$snoopy->expandlinks = false;
		
		$r=$snoopy->submit($url,$params);
		return $r->results;
	}
}
