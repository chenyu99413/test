<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div style="width: 500px;">
	<form style="margin-bottom: 0px;" id="edit-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 100px;">
				<col style="width: 230px;">
			</colgroup>
			<tbody>
				<tr>
					<th>生效时间</th>
					<td>
						<?php
						echo Q::control ( "datebox", "effect_time", array (
							"value" => Helper_Util::strDate('Y-m-d', $disable_country->effect_time),
							"style"=>"width:125px",
						    "required"=>"required"
						) )?>
						<input type="hidden" name="disabled_country_id"
							value="<?php echo request('disabled_country_id')?>" />
						<input type="hidden" name="channel_id" 
							value="<?php echo request('channel_id')?>" />
					</td>
				</tr>
				<tr>
					<th>失效时间</th>
					<td>
						<?php
						echo Q::control ( "datebox", "failure_time", array (
							"value" => Helper_Util::strDate('Y-m-d', $disable_country->failure_time),
							"style"=>"width:125px",
						    "required"=>"required"
						) )?>
					</td>
				</tr>
				<tr>
					<th>国家</th>
					<td>
                        <?php
						echo Q::control ( 'myselect', 'country_code', array (
							'items' => Helper_Array::toHashmap(Country::find()->asArray()->getAll(), 'code_word_two','chinese_name'),
							'selected' => $checked,
							'style' => 'width:135px',
							'multiple' => 'multiple'
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
			<button type="button" class="btn btn-primary" id="save">
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
	// 保存
	$('#save').on('click',function(){
		var saveload = layer.load(1);
		var index = parent.layer.getFrameIndex(window.name);
		var form_data = $('#edit-form').serialize();
		$.ajax({
			url: '<?php echo url("/EditSave")?>',
			type: 'POST',
			dataType: 'json',
			data: form_data,
		})
		.done(function(data) {
			layer.close(saveload);
			parent.layer.msg(data.message);
			if (data.success) {
				parent.layer.close(index);
				parent.location.reload();
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

