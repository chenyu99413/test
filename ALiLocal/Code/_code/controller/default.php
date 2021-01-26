<?php
class Controller_Default extends Controller_Abstract {

	/**
	 * 初始化
	 *
	 * @return QView_Redirect
	 */
	function actionIndex() {
		if ($this->_login_user ["staff_id"] != null) {
			return $this->_redirect ( url ( 'staff/index' ) );
		}
		return $this->_redirect ( url ( 'staff/login' ) );
	}
	function actionAbout() {
	}
	function actionNews() {
	}
	function actionCommunity() {
	}
	function actionDocs() {
	}
	function actionDownload() {
	}
	function actionRedirectMessage() {
		return $this->_redirectMessage ( 'Caption', 'Message', '#' );
	}
}
