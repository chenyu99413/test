<div style="display: none">
	<input id="upload_file_<?php echo @$id?>" type="file" name="file" accept="<?php echo $accept ?>"
		onchange="UploadFile(this,'<?php echo @$url?>');MessagerProgress('导入');" />
</div>
<a id="<?php echo @$id?>" class="btn btn-warning <?php echo $class?>"
	href="javascript:void(0)"
	onclick="$('#upload_file_<?php echo @$id?>').trigger('click');">
	<i class="icon-upload"></i>
	上传
</a>