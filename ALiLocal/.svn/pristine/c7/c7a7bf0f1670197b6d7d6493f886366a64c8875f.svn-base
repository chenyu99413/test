//页面内容修改标记
var change_flag = undefined;

if($('button[value=保存],input[value=保存]').length){
	change_flag = false;
}

/**
 * 绑定所有输入框的change事件
 */
$(":input").change(function() {
	if (change_flag != undefined)
		change_flag = true;
});
