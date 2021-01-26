<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('container_main');?>
<div style="width: 450px;">
	<form style="margin-bottom: 0px;" id="edit-relationship-form">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 80px;">
				<col style="width: 200px;">
			</colgroup>
			<tbody>
				<tr>
					<th class="required-title">阿里产品代码</th>
					<td>
						<input required="required" type="text" name="ali_product" id="ali_product" value="<?php echo $product->ali_product?>"  />
						<input type="hidden" name="id" value="<?php echo $product->id?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">IB代码</th>
					<td>
						<input required="required" type="text" name="ib_product" id="ib_product" value="<?php echo $product->ib_product?>"  />
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
	var ali_product = $("#ali_product").val();
	var ib_product = $("#ib_product").val();
	if(ali_product == "" || ib_product == ""){
		layer.msg('数据不能为空');
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-relationship-form').serialize();
    	$.ajax({
    		url: '<?php echo url("code/productrelationshipeditsave")?>',
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