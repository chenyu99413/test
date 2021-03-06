<?php
class Controller_Common extends Controller_Abstract {
	/**
	 * 初始化
	 */
	function actionIndex() {
	}
	
	/**
	 * 检查国家二字码是否存在
	 *
	 * @return boolean
	 */
	function actionCheckcountryexist() {
		$countrys = explode ( ",", request ( "code" ) );
		foreach ( $countrys as $value ) {
			$country = Country::find ( "code_word_two = ?", $value )->getOne ();
			if ($country->isNewRecord ()) {
				echo "false";
				exit ();
			}
		}
		echo "true";
		exit ();
	}	
	
	/**
	 * 导出
	 */
	function actionExport() {
		$data = request ( "json" );
		$file_name = (request ( "fileName" ) == null ? time () : request ( "fileName" )) . ".xls";
		Helper_Excel::array2xls ( json_decode ( $data ), $file_name );
		exit ();
	}
	
	/**
	 * SHIPID
	 */
	function actionShipid() {
		echo (Helper_Util::upsSHID ( request ( "id" ) ));
		exit ();
	}
	
	/**
	 * 读取文件
	 *
	 * @param Helper_Uploader $uploader        	
	 */
	static function readFile($uploader) {
		$file = $uploader->file ( 'file' );
		$filename = INDEX_DIR . '/_tmp/upload/' . md5 ( $file->filepath () . time () ) . '.xls';
		$file->move ( $filename );
		return $filename;
	}
	
	/**
	 * 读取文件类型
	 *
	 * @param Helper_Uploader $uploader        	
	 */
	static function getFileExtName($uploader) {
		return $uploader->file ( 'file' )->extname ();
	}
	
	/**
	 * 检查渠道
	 */
	function actionCheckchannel() {
		$channel = Channel::find ( "channel_name != ? and channel_name = ?", request ( "old" ), request ( "value" ) )->getOne ();
		if ($channel->isNewRecord ()) {
			echo "true";
		} else {
			echo "false";
		}
		exit ();
	}
	
	/**
	 * 检查网络
	 */
	function actionChecknetwork() {
		$network = Network::find ( "network_code != ? and network_code = ?", request ( "old" ), request ( "value" ) )->getOne ();
		if ($network->isNewRecord ()) {
			echo "true";
		} else {
			echo "false";
		}
		exit ();
	}
	
	/**
	 * 检查产品
	 */
	function actionCheckproduct() {
		$product = Product::find ( "product_name != ? and product_name = ?", request ( "old_name" ), request ( "value_name" ) )->getOne ();
		if (! $product->isNewRecord ()) {
			echo "产品代码已存在,无法保存";
			exit ();
		}
		
		echo "true";
		exit ();
	}
}