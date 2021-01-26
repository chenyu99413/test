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
						<input required="required" <?php if(request('type') == 'save'):?>readonly<?php endif;?> type="text" name="code_word_two" id="code_word_two" value="<?php echo $country->code_word_two?>"  />
						<input type="hidden" name="id" value="<?php echo $country->id?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">国家三字码</th>
					<td>
						<input required="required" type="text" name="code_word_three" id="code_word_three" value="<?php echo $country->code_word_three?>"  />
					</td>
				</tr>
				<tr>
					<th class="required-title">英文名称1</th>
					<td>
						<input required="required" type="text" name="english_name" id="english_name" value="<?php echo $country->english_name?>"  />
					</td>
				</tr>
				<tr>
					<th class="">英文名称2</th>
					<td>
						<input required="required" type="text" name="english_name2" id="english_name2" value="<?php echo $country->english_name2?>"  />
					</td>
				</tr>
				<tr>
					<th class="">中文名称</th>
					<td>
						<input required="required" type="text" name="chinese_name" id="chinese_name" value="<?php echo $country->chinese_name?>"  />
					</td>
				</tr>
				<tr>
					<th class="">国家关税代码</th>
					<td>
						<input required="required" type="text" name="customs_country_code" id="customs_country_code" value="<?php echo $country->customs_country_code?>"  />
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
	var code_word_three = $("#code_word_three").val();
	var english_name = $("#english_name").val();
	if(code_word_two == "" || code_word_three == "" || english_name == ""){
		layer.msg('数据不能为空');
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("codecountry/editsave")?>',
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
</script>
<?PHP $this->_endblock();?>