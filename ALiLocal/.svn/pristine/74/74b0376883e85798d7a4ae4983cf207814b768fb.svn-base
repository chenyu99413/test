<?php

/**
 * 定义 Control_DropdownBox 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: dropdownbox.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * Control_DropdownBox 构造一个下拉列表框
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: dropdownbox.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_DropdownBox extends QUI_Control_Abstract {
	function render() {
		$selected = $this->_extract ( 'selected' );
		$value = $this->_extract ( 'value' );
		$items = $this->_extract ( 'items' );
		$valueAsCatpion = $this->_extract ( 'valueAsCatpion' );
		
		if (is_array ( $value ) || (strlen ( $value ) && strlen ( $selected ) == 0)) {
			$selected = $value;
		}
		
		$out = '<select ';
		$out .= $this->_printIdAndName ();
		$out .= $this->_printDisabled ();
		$out .= $this->_printAttrs ();
		$out .= ">\n";
		
		if ($this->get ( 'empty' )) {
			$out .= "<option value='" . $this->get ( 'emptyValue', '' ) . "'>" . $this->get ( 'emptyTitle' ) . "</option>";
		}
		
		$flag = false;
		foreach ( ( array ) $items as $key => $caption ) {
			if (is_array ( $caption )) {
				$out .= '<optgroup label=' . $key . '>';
				foreach ( $caption as $v => $c ) {
					if ($valueAsCatpion) {
						$v = $c;
					}
					$out .= '<option value="' . htmlspecialchars ( $v ) . '" ';
					if ((is_array ( $selected ) && in_array ( $v, $selected )) || ($v == $selected && strlen ( $v ) == strlen ( $selected ))) {
						$out .= 'selected="selected" ';
						$flag = true;
					}
					$out .= '>';
					$out .= htmlspecialchars ( $c );
					$out .= "</option>\n";
				}
				$out .= '</optgroup>';
			} else {
				if ($valueAsCatpion) {
					$key = $caption;
				}
				$out .= '<option value="' . htmlspecialchars ( $key ) . '" ';
				if ((is_array ( $selected ) && in_array ( $key, $selected )) || ($key == $selected && strlen ( $key ) == strlen ( $selected ))) {
					$out .= 'selected="selected" ';
					$flag = true;
				}
				$out .= '>';
				$out .= htmlspecialchars ( $caption );
				$out .= "</option>\n";
			}
		}
		
		if ($this->get ( 'default' ) && ! $flag) {
			$out .= "<option value='" . $value . "' selected='selected'>" . $this->get ( 'default' ) . "</option>";
		}
		$out .= "</select>\n";
		
		return $out;
	}
}

