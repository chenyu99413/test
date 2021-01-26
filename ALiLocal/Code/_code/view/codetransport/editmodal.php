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
					<th class="required-title">运输方式编码</th>
					<td>
						<input required="required" type="text" name="code" id="code" value="<?php echo $currency->code?>"  />
						<input type="hidden" name="id"
							value="<?php echo request('id')?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">运输方式名称</th>
					<td>
						<input type="text" name="name" value="<?php echo $currency->name?>"
							required="required" />
					</td>
				</tr>
				<tr>
					<th class="required-title">关联产品</th>
					<td>
						<?php
					echo Q::control("dropdownbox", "product_id", array(
						"items" => Helper_Array::toHashmap(Product::find()->getAll(), "product_id", "product_chinese_name"),
						"value" => $currency->product_id,
						"style" => "width: 95%",
						"empty" => "true"
					))?>
					</td>
				</tr>
				<tr>
					<th class="">关联渠道</th>
					<td>
						<?php
						$channel = ChannelCost::find('product_id=?',$currency->product_id)->getAll();
						foreach ($channel as $c){
							$channel_ids[] = $c->channel_id;
						}
						
					echo Q::control("dropdownbox", "channel_id", array(
						"items" => Helper_Array::toHashmap(Channel::find('channel_id in (?)',@$channel_ids)->getAll(), "channel_id", "channel_name"),
						"value" => $currency->channel_id,
						"style" => "width: 95%",
						"empty" => "true"
					))?>
					</td>
				</tr>
				<tr>
					<th class="required-title">预报方式</th>
					<td>
					<?php 
					echo Q::control("radiogroup", "book_type", array(
						"items" => array(0=>'预报',1=>'预报打单'),
						"value" => $currency->book_type,
					))?>
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
		<?php if (count($logs)){?>
		<div>
    		<table class="table table-bordered table-condensed">
    			<caption>操作日志</caption>
        		<thead>
            		<tr>
            			<th style="width:40px">操作人</th>
            			<th style="width:80px">时间</th>
            			<th style="width:100px">日志</th>
        			</tr>
        		</thead>
        		<tbody>
        			<?php foreach ( $logs as $log):?>
            		<tr>
            			<td><?php echo $log->staff_name;?></td>
            			<td><?php echo date('Y-m-d H:i:s',$log->create_time);?></td>
            			<td><?php echo $log->comment;?></td>
        			</tr>
        			<?php endforeach;?>
        		</tbody>
    		</table>
		</div>
		<?php }?>
	</form>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
//二级联动
$('#product_id').change(function(){
	if($(this).val().length == 0){
		$('#channel_id').empty();
		return false
	}
	$.ajax({
		url: '<?php echo url("CodeTransport/change")?>',
		type: 'POST',
		dataType: 'json',
		data: {'product_id':$(this).val()},
	})
	.done(function(data) {
    		$('#channel_id').empty();
    		$('#channel_id').append('<option value=""></option>')
    		$.each(data,function(k,v){
    			$('#channel_id').append('<option value="'+v.channel_id+'">'+v.channel_name+'</option>')
        	})
    	})
})
//检查数据
function checkdata(){
	result = true;
	var code = $("#code").val();
	var name = $("#name").val();
	var product_id = $("#product_id").val();
	var channel_id = $("#channel_id").val();
	if(code == "" || product_id == "" || name == ""){
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("CodeTransport/editsave")?>',
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