<?php
/**
 * 常用函数助手
 *
 * @package helper
 */
class Helper_Token {
	/**
	 * set Token
	 */
	static function setToken() {
		@session_start();
		$_SESSION['token']=time().rand(100, 999);
		session_write_close();
		return $_SESSION['token'];
	}
	
	/**
	 * reset Token
	 */
	static function resetToken() {
		@session_start();
		$_SESSION['token']=time().rand(100, 999);
		session_write_close();
	}
}