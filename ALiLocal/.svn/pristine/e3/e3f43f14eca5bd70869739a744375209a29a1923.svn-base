<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('container_main');?>
<div style="width: 300px;">
	<form style="margin-bottom: 0px;" id="edit-logistics-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 80px;">
				<col style="width: 200px;">
			</colgroup>
			<tbody>
				<tr>
					<th>代码</th>
					<td>
						<input type="text" name="code" value="<?php echo $Logistic->code?>" />
						<input type="hidden" name="logistic_id"
							value="<?php echo request('logistic_id')?>" />
					</td>
				</tr>
				<tr>
					<th>名称</th>
					<td>
						<input type="text" name="name" value="<?php echo $Logistic->name?>"
							required="required" />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="save-logistics">
				<i class="icon-save"></i>
				保存
			</button>
		</div>
	</form>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
$(function(){
	$('#save-logistics').on('click',function(){
		var saveload = layer.load(1);
		var index = parent.layer.getFrameIndex(window.name);
		var form_data = $('#edit-logistics-form').serialize();
		$.ajax({
			url: '<?php echo url("codelogistics/editsave")?>',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(data) {
			layer.close(saveload);
			parent.layer.msg(data.message);
			if (data.success) {
				parent.layer.close(index);
				parent.$('#search-logistics-btn').click();
			}
		})
		.fail(function(data) {
			layer.close(saveload);
			parent.layer.alert('发生内部错误，暂时无法修改');
		});
	});
});
</script>
<?PHP $this->_endblock();?>