<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('container_main');?>
<div style="width: 450px;">
	<form style="margin-bottom: 0px;" id="edit-currency-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 80px;">
				<col style="width: 200px;">
			</colgroup>
			<tbody>
				<tr>
					<th class="required-title">国家二字码</th>
					<td>
						<input required="required" type="text" name="code_word_two" id="code_word_two" value="<?php echo $timezone->code_word_two?>"  />
						<input type="hidden" name="id"
							value="<?php echo request('id')?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">城市代码</th>
					<td>
						<input type="text" name="city" id="city" value="<?php echo $timezone->city?>"
							required="required" />
					</td>
				</tr>
				<tr>
					<th class="required-title">时区</th>
					<td>
						<input required="required" type="text" name="timezone" id="timezone" value="<?php echo $timezone->timezone?>"  />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" onclick="checkdata()">
				<i class="icon-save"></i>
				保存
			</button>
		</div>
	</form>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
//检查数据
function checkdata(){
	result = true;
	var code_word_two = $("#code_word_two").val();
	var city = $("#city").val();
	var timezone = $("#timezone").val();
	if(code_word_two == "" || city == "" || timezone == ""){
		layer.msg('数据不能为空');
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("codetimezone/editsave")?>',
    		type: 'POST',
    		dataType: 'json',
    		data: form_data,
    	})
    	.done(function(data) {
    		layer.close(saveload);
    		parent.layer.msg(data.message);
    		if (data.success) {
    			parent.layer.close(index);
    			parent.$('#search-currency-btn').click();
    		}
    	})
    	.fail(function(data) {
    		layer.close(saveload);
    		parent.layer.alert('发生内部错误，暂时无法修改');
    	});
	}
	return result;
}
$(function(){
	var html = '<td style="width:33%"><a class="current-month">当月</a></td>';
	$('.combo-arrow').on('click',function(){
		if($('.datebox-button table tbody tr:eq(0)').find('td').size()<3){
    		$('.datebox-button').find('table tbody tr td').css('width','33%');
    		$('.datebox-button').find('table tbody tr').append(html);
		}
	})
	// 给按钮添加事件
    $(".datebox-button").on("click",".current-month",function(){
    	$('#s_modifyDateStart').datebox('setValue',time(1));
		$('#s_modifyDateEnd').datebox('setValue',time(0));
		$(this).closest("div.combo-panel").panel("close");
    });
});
//设月底跟月头时间
function time(number){
	var date=new Date();
	var strDate=date.getFullYear()+"-";
	if(number==0){ //0 为月底
    	strDate+=date.getMonth()+2+"-";
    	strDate+=number;
	}else{
    	strDate+=date.getMonth()+1+"-";
    	strDate+=number;
	}
	return strDate;
}
</script>
<?PHP $this->_endblock();?>