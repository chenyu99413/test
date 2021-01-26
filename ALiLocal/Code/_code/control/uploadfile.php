<?php
/**
 * 使用此控件，如果需要设置为必填项，前端需要配合Submit() 表单提交处理方法
 *
 * @author firzen
 *        
 */
class Control_UploadFile extends Control_Upload {
	/**
	 * 自定义控件-文件上传
	 */
	function render() {
		$this->_view ["id"] = $this->id ();
		$this->_view ["url"] = $this->_extract ( "url" );
		$this->_view ['class'] = $this->_extract ( 'class' );
		$this->_view ['accept'] = $this->_extract ( "accept", "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
		
		return $this->_fetchView ( dirname ( __FILE__ ) . "/uploadfile.view.php" );
	}
}