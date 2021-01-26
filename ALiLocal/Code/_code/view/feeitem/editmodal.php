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
					<th class="required-title">费用名称</th>
					<td>
						<input type="text" name="item_name" id="item_name" value="<?php echo $currency->item_name?>"
							required="required" />
					</td>
				</tr>
				<tr>
					<th class="required-title">费用代码</th>
					<td>
						<input required="required" type="text" name="sub_code" id="sub_code" value="<?php echo $currency->sub_code?>"  />
						<input type="hidden" name="fee_item_id"
							value="<?php echo request('fee_item_id')?>" />
					</td>
				</tr>
				<tr>
					<th>阿里代码</th>
					<td>
						<input type="text" name="item_code" id="item_code" value="<?php echo $currency->item_code?>"  />
					</td>
				</tr>
				<tr>
					<th class="required-title">客户</th>
					<td>
					<?php 
                        echo Q::control ( 'dropdownbox', 'customs_code', array (
                        	'items'=>Helper_Array::toHashmap(Customer::find()->getAll(),'customs_code','customer'),
                        	'empty'=>true,
                        	'style'=>'width:130px',
                        	'value' => $currency->customs_code,
                        ) )?>
                        
                     </td>
				</tr>
				<tr>
					<th class="required-title">支付方</th>
					<td>
					<?php 
                        echo Q::control ( 'dropdownbox', 'payer', array (
                        	'items'=>array('BUYER'=>'买家','SUPPLIER'=>'卖家'),
                        	'empty'=>true,
                        	'style'=>'width:130px',
                        	'value' => $currency->payer,
                        ) )?>
                        
                     </td>
				</tr>
				<tr>
					<th class="required-title">费用计量单位</th>
					<td>
					<?php 
                        echo Q::control ( 'dropdownbox', 'fee_unit', array (
                        	'items'=>array('ORDER'=>'票','KG'=>'千克','STERE'=>'立方米'),
                        	'empty'=>true,
                        	'style'=>'width:130px',
                        	'value' => $currency->fee_unit,
                        ) )?>
                        
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
//检查数据
function checkdata(){
	result = true;
	var sub_code = $("#sub_code").val();
	var item_name = $("#item_name").val();
	if(sub_code == "" || item_name == ""){
	    result = false;
	}
	
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("feeitem/editsave")?>',
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