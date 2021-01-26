<?php
/**
 * 下拉框
 * 需要与 BootCSS.v2 + jquery.chosen.js 结合使用
 *
 * @author xuedong
 *        
 */
class Control_MySelect extends QUI_Control_Abstract {
	function render() {
		$id = $this->id (); //第二个参数 ： stu
		$select_name = $this->_extract ( 'select_name' ); //第三个参数中的 select_name : string (如果 name 和 id 相同，则可以不传该参数)
		$items = $this->_extract ( 'items' ); //第三个参数中的 items : array
		$selected = $this->_extract ( 'selected' ); //第三个参数中的 checked : string
		$required = $this->_extract ( 'required' ); //第三个参数中的 required : string
		$multiple = $this->_extract ( 'multiple' ); //第三个参数中的 multiple : string

		$out = '<select class="chosen-select" ';
		if (strlen ( $select_name )) {
			$out .= $this->printIdAndName ( $id, $select_name, $multiple );
		} else {
			$out .= $this->printIdAndName ( $id, $id, $multiple );
		}
		$out .= $this->_printDisabled ();
		$out .= $this->_printAttrs ();
		if ($multiple) {
			$out .= 'multiple="" ';
		}
		$out .= ">\n";
		$out .= "<option value=''></option>\n";
		foreach ( ( array ) $items as $value => $caption ) {
			if (is_array ( $caption )) {
				$out .= '<optgroup label=' . $value . '>';
				foreach ( $caption as $v => $c ) {
					$out .= '<option value="' . htmlspecialchars ( $v ) . '" ';
					if (is_array ( $selected )) {
						if (in_array ( $v, $selected )) {
							$out .= 'selected="selected" ';
						}
					} else {
						if ($v == $selected && strlen ( $v ) == strlen ( $selected )) {
							$out .= 'selected="selected" ';
						}
					}
					$out .= '>';
					$out .= htmlspecialchars ( $c );
					$out .= "</option>\n";
				}
				$out .= '</optgroup>\n';
			} else {
				$out .= '<option value="' . htmlspecialchars ( $value ) . '" ';
				if (is_array ( $selected )) {
					if (in_array ( $value, $selected )) {
						$out .= 'selected="selected" ';
					}
				} else {
					if ($value == $selected && strlen ( $value ) == strlen ( $selected )) {
						$out .= 'selected="selected" ';
					}
				}

				$out .= '>';
				$out .= htmlspecialchars ( $caption );
				$out .= "</option>\n";
			}
		}
		$out .= "</select>\n";

		$config = 'disable_search_threshold: 8';
		if (! $required) {
			//显示 x 符号，可以取消选择
			$config .= ',allow_single_deselect: true';
		}
		$out .= '<script type="text/javascript">';
		$out .= '$("#' . $id . '").chosen({' . $config . '});';
		$out .= '</script>';

		return $out;
	}
	/**
	 * 设置 id、name
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $multiple
	 * @return string
	 */
	function printIdAndName($id, $name = '', $multiple) {
		$out = 'id="' . htmlspecialchars ( $id ) . '" ';
		if (strlen ( $this->_attrs ['name'] ) > 0) {
			$fix = $multiple ? '[]' : '';
			$out .= ' name="' . htmlspecialchars ( $this->_attrs ['name'] ) . $fix . '" ';
		}
		return $out;
	}
}

