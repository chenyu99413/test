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
					<th class="required-title">仓库</th>
					<td>
						<?php 
                        echo Q::control ( 'dropdownbox', 'department_id', array (
                        	'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                        	'empty'=>true,
                        	'style'=>'width:130px',
                        	'value' => $codewarehouse->department_id,
                        ) )?>
						<input type="hidden" name="id"
							value="<?php echo request('id')?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">仓库代码</th>
					<td>
						<input type="text" name="warehouse" id="warehouse" value="<?php echo $codewarehouse->warehouse?>"
							required="required" />
					</td>
				</tr>
				<tr>
					<th>仓库英文名称</th>
					<td>
						<input type="text" name="warehouse_enname" id="warehouse_enname" value="<?php echo $codewarehouse->warehouse_enname?>"
							 />
					</td>
				</tr>
				<tr>
					<th>仓库联系人</th>
					<td>
						<input type="text" name="warehouse_contact" id="warehouse_contact" value="<?php echo $codewarehouse->warehouse_contact?>"
							/>
					</td>
				</tr>
				<tr>
					<th>仓库联系电话</th>
					<td>
						<input type="text" name="warehouse_mobile" id="warehouse_mobile" value="<?php echo $codewarehouse->warehouse_mobile?>"
							/>
					</td>
				</tr>
				<tr>
					<th>仓库地址</th>
					<td>
						<input type="text" name="warehouse_address" id="warehouse_address" value="<?php echo $codewarehouse->warehouse_address?>"
							/>
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
	var department_id = $("#department_id").val();
	if(department_id == "" ){
		layer.msg('仓库不能为空');
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("/editsave")?>',
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