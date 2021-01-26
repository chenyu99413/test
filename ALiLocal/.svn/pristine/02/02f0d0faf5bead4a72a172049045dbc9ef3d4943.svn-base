<?php
/**
 * 翻页控件
 *
 * @author xuedong
 *         << 1 ... 5 6 7 ... 20 >> | 61-70 of 195 | 跳至[6]页 | 每页[10]条
 *         第一页、起始部分、中间部分、结尾部分、最后一页 | 数量统计 | page_jump | page_size | display_page_size_select
 */
class Control_AjaxPagination extends QUI_Control_Abstract {
	function render() {
		$pagination = $this->pagination;
		$udi = $this->get ( 'udi', $this->_context->requestUDI () );
		$length = $this->get ( 'length', 3 ); //中间位置页码数量
		$slider = $this->get ( 'slider', 1 ); //收尾位置页码数量
		$prev_label = $this->get ( 'prev_label', '' );
		$next_label = $this->get ( 'next_label', '' );
		$url_args = $this->get ( 'url_args' );
		$attr = $this->get ( 'attr' );
		$page_size_select = $this->_extract ( 'page_size_select', array (
			30,
			50,
			100,
			200
		) );
		$display_page_size_select = $this->_extract ( 'display_page_size_select', true );
		$getList = $this->_extract ( 'getList', 'getList' );

		if (is_null ( $url_args )) {
			$url_args = array_merge ( $_GET, $_POST );
			unset ( $url_args [QContext::UDI_CONTROLLER] );
			unset ( $url_args [QContext::UDI_ACTION] );
			unset ( $url_args [QContext::UDI_MODULE] );
			unset ( $url_args ['page'] );
			foreach ( $url_args as $k => $v ) {
				if (! is_array ( $v )) {
					$url_args [$k] = $v;
				} else {
					$url_args [$k] = array_filter ( $v, 'strlen' );
				}
			}
		}
		$url_args = ( array ) $url_args;

		// 开始拼接翻页控件字符串
		$out = "<div class=\"pagination pagination-small\">\n";
		$out .= "<ul id=\"" . h ( $this->id () ) . "\">\n";

		// 第一页
		if ($pagination ['current'] == $pagination ['first']) {
			$out .= "<li class=\"disabled\"><a><i class=\"icon-double-angle-left\"></i> {$prev_label}</a></li>\n";
		} else {
			$url_args ['page'] = $pagination ['prev'];
			$url = url ( $udi, $url_args );
			$prev_page = $pagination ['current'] - 1;
			$out .= "<li><a href=\"{$url}\" {$attr} class=\"ajaxpagination\" data-page=\"{$prev_page}\"><i class=\"icon-double-angle-left\"></i> {$prev_label}</a></li>\n";
		}

		$base = $pagination ['first'];
		$current = $pagination ['current'];
		if ($current < $pagination ['first']) {
			$current = $pagination ['first'];
		}
		if ($current > $pagination ['last']) {
			$current = $pagination ['last'];
		}

		$mid = intval ( $length / 2 ); //中间部分：当前页前面的数量
		$begin = $current - $mid; //中间部分：起始页码
		if ($begin < $pagination ['first']) {
			$begin = $pagination ['first'];
		}
		$end = $begin + $length - 1; //中间部分：结尾页码
		if ($end >= $pagination ['last']) {
			$end = $pagination ['last'];
			$begin = $end - $length + 1;
			if ($begin < $pagination ['first']) {
				$begin = $pagination ['first'];
			}
		}

		// 起始部分
		if ($begin > $pagination ['first']) {
			for($i = $pagination ['first']; $i < $pagination ['first'] + $slider && $i < $begin; $i ++) {
				$url_args ['page'] = $i;
				$in = $i + 1 - $base;
				$url = url ( $udi, $url_args );
				$out .= "<li><a href=\"{$url}\" {$attr} class=\"ajaxpagination\" data-page=\"{$in}\">{$in}</a></li>\n";
			}

			if ($i < $begin) {
				$out .= "<li class=\"disabled\"><span>...</span></li>\n";
			}
		}

		// 中间部分
		for($i = $begin; $i <= $end; $i ++) {
			$url_args ['page'] = $i;
			$in = $i + 1 - $base;
			if ($i == $pagination ['current']) {
				$out .= "<li class=\"active\"><a>{$in}</a></li>\n";
			} else {
				$url = url ( $udi, $url_args );
				$out .= "<li><a href=\"{$url}\" {$attr} class=\"ajaxpagination\" data-page=\"{$in}\">{$in}</a></li>\n";
			}
		}

		// 结尾部分
		if ($pagination ['last'] - $end > $slider) {
			$out .= "<li class=\"disabled\"><span>...</span></li>\n";
			$end = $pagination ['last'] - $slider;
		}
		for($i = $end + 1; $i <= $pagination ['last']; $i ++) {
			$url_args ['page'] = $i;
			$in = $i + 1 - $base;
			$url = url ( $udi, $url_args );
			$out .= "<li><a href=\"{$url}\" {$attr} class=\"ajaxpagination\" data-page=\"{$in}\">{$in}</a></li>\n";
		}

		// 最后一页
		if ($pagination ['current'] == $pagination ['last']) {
			$out .= "<li class=\"disabled\"><a>{$next_label} <i class=\"icon-double-angle-right\"></i></a></li>\n";
		} else {
			$url_args ['page'] = $pagination ['next'];
			$url = url ( $udi, $url_args );
			$next_page = $pagination ['current'] + 1;
			$out .= "<li><a href=\"{$url}\" {$attr} class=\"ajaxpagination\" data-page=\"{$next_page}\">{$next_label} <i class=\"icon-double-angle-right\"></i></a></li>\n";
		}

		// 数量统计
		$left = ($pagination ['current'] - $pagination ['page_base']) * $pagination ['page_size'] + 1;
		$right = $left + $pagination ['page_size'] - 1;
		if ($right > $pagination ['record_count']) {
			$right = $pagination ['record_count'];
		}
		$out .= "<li class=\"disabled\"><span>{$left}-{$right} of {$pagination['record_count']}</span></li>\n";

		// page_jump
		$out .= "<li class=\"disabled\"><span>跳至</span></li><li class=\"disabled\">";
		$out .= "<input type=\"text\" name=\"page_jump\" value=\"" . $this->pagination ['current'] . "\" style=\"margin:0;padding:4px;float:left;text-align:center;width:40px;height:16px;\">";
		$out .= "</li><li class=\"disabled\"><span>页</span></li>";

		// page_size
		if ($display_page_size_select) {
			$out .= "<li class=\"disabled\"><span>每页</span></li><li class=\"disabled\">";
			$out .= "<select name=\"page_size_select\" style=\"margin:0;padding:2px;float:left;width:55px;height:26px;\">";
			foreach ( $page_size_select as $value ) {
				$out .= "<option value=\"" . $value . "\" ";
				if ($this->pagination ['page_size'] == $value) {
					$out .= "selected=\"selected\"";
				}
				$out .= ">" . $value . "</option>";
			}
			$out .= "</select></li><li class=\"disabled\"><span>条</span></li>";
		} else {
			$out .= "<select name=\"page_size_select\" style=\"display:none;\">";
			$out .= "<option value=\"" . $this->pagination ['page_size'] . "\" selected=\"selected\">" . $this->pagination ['page_size'] . "</option>";
			$out .= "</select>";
		}
		$out .= "</ul></div>\n";

		// 脚本
		$out .= '<script type="text/javascript">';
		$out .= '$("#' . h ( $this->id () ) . '").find("[name=page_jump]").on("change",function(){
			var page = $(this).val();
			var page_size = $("#' . h ( $this->id () ) . '").find("[name=page_size_select]").val();
			' . $getList . '(page,page_size);
		});';
		$out .= '$("#' . h ( $this->id () ) . '").find("[name=page_size_select]").on("change",function(){
			var page_size = $(this).val();
			' . $getList . '("1",page_size);
		});';
		$out .= '$("#' . h ( $this->id () ) . '").find(".ajaxpagination").on("click",function(e){
			e.preventDefault();
			var page = $(this).data("page");
			var page_size = $("#' . h ( $this->id () ) . '").find("[name=page_size_select]").val();
			' . $getList . '(page,page_size);
			return false;
		});';
		$out .= '</script>';

		return $out;
	}
}