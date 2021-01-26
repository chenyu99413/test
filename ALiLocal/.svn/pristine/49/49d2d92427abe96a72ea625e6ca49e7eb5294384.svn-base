<?php
/**
 * 使用此控件，如果需要设置为必填项，前端需要配合Submit() 表单提交处理方法
 * 
 * @author firzen
 *        
 */
class Control_Check extends QUI_Control_Abstract {
	/**
	 *
	 * @see QUI_Control_Abstract::render() need far.js
	 */
	function render() {
		$this->_view ['id'] = $this->id ();
		$this->_view ['name'] = $this->_extract ( 'name' );
		$this->_view ['value'] = $this->_extract ( 'value' );
		$this->_view ['text'] = $this->_extract ( 'text' );
		
		return $this->_fetchView ( dirname ( __FILE__ ) . '/checkbox.view.php' );
	}
}