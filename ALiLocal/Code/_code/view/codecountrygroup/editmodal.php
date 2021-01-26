<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('container_main');?>
<div style="width: 500px;">
	<form style="margin-bottom: 0px;" id="edit-countrygroup-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 100px;">
				<col style="width: 220px;">
			</colgroup>
			<tbody>
				<tr>
					<th>名称</th>
					<td>
						<input type="text" name="name" value="<?php echo $group->name?>" />
						<input type="hidden" name="countrygroup_id"
							value="<?php echo request('countrygroup_id')?>" />
					</td>
				</tr>
				<tr>
					<th>国家</th>
					<td>
						<?php
						echo Q::control ( 'myselect', 'countrygroup_codes', array (
							'items' => Helper_Array::toHashmap(Country::find()->asArray()->getAll(), 'code_word_two','chinese_name'),
							'selected' => $checked,
							'multiple' => 'multiple',
							'style' => 'width:135px',
						) );
						?>
					</td>
				</tr>
				<tr>
					<th>粘贴录入国家</th>
					<td><textarea style="width: 200px; height: 150px" id="countrylist" name="countrylist"
							placeholder="国家二字码/国家名称（一行一条）,如填写将覆盖上面的国家"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="save-group">
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
	$('#save-group').on('click',function(){
		var saveload = layer.load(1);
		var index = parent.layer.getFrameIndex(window.name);
		var form_data = $('#edit-countrygroup-form').serialize();
		$.ajax({
			url: '<?php echo url("codecountrygroup/EditModalSave")?>',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(data) {
			layer.close(saveload);
			parent.layer.msg(data.message);
			if (data.success) {
				parent.layer.close(index);
				parent.$('#search-countrygroup-btn').click();
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