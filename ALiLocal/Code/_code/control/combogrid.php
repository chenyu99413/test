<?php
/**
 * 使用此控件，如果需要设置为必填项，前端需要配合Submit() 表单提交处理方法
 * @author firzen
 *
 */
class Control_ComboGrid extends QUI_Control_Abstract{
	/**
	 * 
	 * @see QUI_Control_Abstract::render()
	 * need far.js
	 */
	function render(){
		$this->_view['value']=$this->_extract('value');
		$this->_view['idField']=$this->_extract('idField');
		$this->_view['textField']=$this->_extract('textField');
		$this->_view['data']=$this->_extract('data');
		$this->_view['url']=$this->_extract('url');
		$this->_view['columns']=$this->_extract('columns');
		$this->_view['id']=$this->id();
		$this->_view['mode']=$this->_extract('mode');
		$this->_view['onChange']=$this->_extract('onChange');
		$this->_view['onSelect']=$this->_extract('onSelect');
		$this->_view['onLoadSuccess']=$this->_extract('onLoadSuccess');
		$this->_view['onHidePanel']=$this->_extract('onHidePanel');
		
		
		$this->_view['multiple']=$this->_extract('multiple');
		$this->_view['validType']=$this->_extract('validType');
		
		$this->_view['required']=$this->_extract('required');
		$attr='';
		$attr .= $this->_printIdAndName();
		$attr .= $this->_printDisabled();
		$attr .= $this->_printAttrs();
		$this->_view['attr']=$attr;
		$this->_view['addon_text']=$this->_extract('addon_text');
		
		return $this->_fetchView(dirname(__FILE__).'/combogrid.view.php');
	}
}