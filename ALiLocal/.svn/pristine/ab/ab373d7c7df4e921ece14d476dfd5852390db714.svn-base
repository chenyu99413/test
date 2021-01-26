<div style="width: 500px;">
	<form style="margin-bottom: 0px;" id="edit-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 200px;">
			</colgroup>
			<tbody>
				<tr>
					<td>
						<textarea name="remark" style="width:490px;" rows="1"><?php echo $totaltrack->remark?></textarea>
						<input type="hidden" name='total_list_id' value="<?php echo request('total_list_id')?>"/>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="save-remark">
				<i class="icon-save"></i>
				保存
			</button>
		</div>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$('#save-remark').on('click',function(){
		var saveload = layer.load(1);
		var formData = new FormData(document.getElementById('edit-form'));
		$.ajax({
			url: '<?php echo url("warehouse/editsave")?>',
			type: 'POST',
			cache: false,
			processData: false,
			contentType: false,
			data: formData,
			dataType: 'json',
		})
		.done(function(data) {
			layer.close(saveload);
			layer.msg(data.message);
			if (data.success) {
				window.location.reload();
			}
		})
		.fail(function(data) {
			layer.close(saveload);
			layer.alert('发生内部错误，暂时无法修改');
		});
	});
});
</script>
