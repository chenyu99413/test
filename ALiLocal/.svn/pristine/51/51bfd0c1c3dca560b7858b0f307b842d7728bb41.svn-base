<?php
/**
 * 对QDB 返回的结果集用表格显示
 *
 * @author firzen
 *        
 */
class Control_Crud extends QUI_Control_Abstract {
	/**
	 *
	 * @see QUI_Control_Abstract::render()
	 */
	function render() {
		$rows=$this->_extract('rows');
		$this->_view['rows']=$rows;
		return $this->_fetchView ( dirname ( __FILE__ ) . '/crud.view.php' );
	}
}