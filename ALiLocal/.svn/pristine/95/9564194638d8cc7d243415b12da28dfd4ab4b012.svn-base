<?php
class Control_PartitionTabs extends QUI_Control_Abstract {
	
	/**
	 * 自定义控件-菜单
	 *
	 * @see QUI_Control_Abstract::render()
	 */
	function render() {
		$tabs = $this->tabs;
		$active_id = $this->active_id;
		
		$out = "<div class=\"tabs-header\"><div style=\"margin-left: 0px; margin-right: 0px;\"class=\"tabs-wrap\"><ul style=\"height: 26px;\" class=\"tabs\">";
		foreach ( $tabs as $tab ) {
			if ($tab ['id'] == $active_id) {
				$out .= "<li class=\"tabs-selected\" style=\"width:30px\">";
			} else {
				$out .= "<li style=\"width:20px\">";
			}
			$out .= "<a style=\"height: 25px; line-height: 25px;\" href=\"" . (empty ( $tab ['href'] ) ? "javascript:void(0)" : $tab ['href']) . "\" class=\"tabs-inner\" onclick=\"if(change_flag){return confirm('数据未保存，是否继续？继续将丢失所有工作内容。是否继续？');}\">";
			$out .= "<span class=\"tabs-title\">";
			$out .= $tab ['title'];
			if (isset ( $tab ['count'] )) {
				if (empty ( $tab ['count'] )) {
					$out .= " <span class=\"badge badge-success\">0</span>";
				} else {
					$out .= " <span class=\"badge\">" . $tab ['count'] . "</span>";
				}
				$out .= "</span>";
			}
			$out .= "</a></li>";
		}
		$out .= "</ul></div></div>";
		return $out;
	}
}