<?php

class Helper_Number{
	/**
	 * round 效果加强
	 * @param float $num
	 * @param number $r
	 * @return string
	 */
	static function round($num,$r=2){
		return number_format($num,$r);
// 		return sprintf("%.{$r}f",$num);
	}
}
