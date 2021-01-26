<?php
require dirname ( __FILE__ ) . DS . 'ChinesePinyin.class.php';
/**
 * 中文处理助手
 *
 * @author firzen
 *        
 */
class Helper_Chinese {
	/**
	 * 唯一实例
	 *
	 * @return ChinesePinyin
	 */
	static function instance() {
		return Q::singleton ( 'ChinesePinyin' );
	}
	/**
	 * 中文转拼音
	 *
	 * @param  string $chinesestring        	
	 * @return string
	 */
	static function toPinYin($chinesestring, $default = false) {
		$result = "";
		if ($default) {
			preg_match ( '/^[\w\d]+/', $chinesestring, $return );
			if (! empty ( $return )) {
				$result = $return [0];
			}
		}
		return $result . self::instance ()->TransformWithoutTone ( $chinesestring );
	}
	/**
	 * @todo   中文转拼音(首字母大写)
	 * @author stt
	 * @since  2020-11-05
	 * @link   #83311
	 */
	static function toPinYinucfirst($chinesestring, $default = false) {
		$result = "";
		//中文转拼音(首字母大写)
		if ($default) {
			//中文转拼音(首字母大写)
			preg_match ( '/^[\w\d]+/', $chinesestring, $return );
			if (! empty ( $return )) {
				$result = $return [0];
			}
		}
		//中文转拼音(首字母大写)
		return $result . self::instance ()->transformWithoutToneucfirst ( $chinesestring,'',false );
	}
}
