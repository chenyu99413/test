<?php 
class Control_Datebox extends QUI_Control_Abstract
{
	static $init=null;
	function render()
	{
		$base_dir=QContext::instance()->baseDir();
		$out='';
		if (is_null(self::$init)){
			self::$init=true;
			$out.="<script src='{$base_dir}public/js/calendar1.js'></script>";
		}
        $value    = $this->_extract('value');
        if ($value){
        	$value=date('Y-m-d',strtotime($value));
        }
        if ($value =='1970-01-01'){
        	$value='';
        }
		$out .= "<span class='control_datebox_container'><input onfocus='this.oldValue=this.value;' onchange='control_datebox_change(event,this)' onkeydown='control_datebox_kd(event,this)' onkeyup='control_datebox_ku(event,this)' value='{$value}' type=text";
		$out .= $this->_printIdAndName();
		$out .= $this->_printDisabled();
		$out .= $this->_printAttrs();
		$out .= ">";
		$out .= "<img src='{$base_dir}public/css/images/datebox_arrow.png' class='control_datebox_img' onclick='calendar.setHook(document.getElementById(\"{$this->id()}\"))'>";
		$out .= "</span>";

        return $out;
	}
}
?>