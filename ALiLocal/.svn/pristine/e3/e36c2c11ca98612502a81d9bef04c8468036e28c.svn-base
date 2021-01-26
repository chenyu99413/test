<?php
/**
 * 使用此控件，如果需要设置为必填项，前端需要配合Submit() 表单提交处理方法
 *
 * @author firzen
 *        
 */
class Control_Path extends QUI_Control_Abstract {
	/**
	 *
	 * @see QUI_Control_Abstract::render()
	 */
	function render() {
		$this->_view ['path'] = $this->_extract ( 'path' );
		$this->_view ['waybill_code'] = $this->_extract ( 'waybill_code' );
		return $this->_fetchView ( dirname ( __FILE__ ) . '/path.view.php' );
	}
}