<?php
require dirname(__FILE__).DS.'simple_html_dom.php';
/**
 * 中文处理助手
 * @author firzen
 *
 */
class Helper_HtmlQuery {
	/**
	 * 初始化HTML
	 * @param string $str
	 * @return simple_html_dom
	 */
	static function parse($str){
		return @str_get_html($str);
	}
}
