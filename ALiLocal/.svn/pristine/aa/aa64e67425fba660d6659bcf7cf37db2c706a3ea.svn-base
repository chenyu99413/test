<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
自动发送规则编辑
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
if (request('id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'自动发送规则管理' => '',
			'自动发送规则列表' => url ( 'product/automaticemailrule' ),
			'自动发送规则编辑' => url ( 'product/ruleedit', array (
				'id' => $rule->id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'自动发送规则管理' => '',
			'自动发送规则列表' => url ( 'product/automaticemailrule' ),
			'新建自动发送规则' => url ( 'product/ruleedit' ) 
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch span10" style="padding:5px;">
	<div>
		<table>
			<tbody>
				<tr>
					<th class="required-title">规则名称</th>
    				<td>
    					<input name="automatic_email_rule" id="automatic_email_rule" type="text" style="width: 150px" required="required" value="<?php echo $rule->automatic_email_rule?>">
    				</td>
    				<th class="required-title">规则产品</th>
    				<td>
    					<?php
						echo Q::control("dropdownbox", "product_id", array(
							"items" => Helper_Array::toHashmap(Product::find()->getAll(), "product_id", "product_chinese_name"),
							"value" => $rule->product_id,
							"style" => "width: 150px",
							"empty" => "true",
							"required"=>'required'
						))?>
    				</td>
    				<th class="required-title">触发节点</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "tracking_code", array (
							"items" => $trace_code,
						    "value" => $rule->tracking_code,
							"style" => "width:200px" ,
						    "empty"=>true,
						    "required"=>'required'
						) )?>
					</td>
					<th class="required-title">邮件模板</th>
				    <td>
    				    <?php
						echo Q::control("dropdownbox", "email_id", array(
							"items" => '',
							"value" => $rule->email_id,
							"style" => "width: 150px",
							"empty" => "true",
							"required"=>'required'
						))?>
						<input type="hidden" id="hidden_email" value="<?php echo $rule->email_id?>">
				    </td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('product/automaticemailrule')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<script type="text/javascript">
$(function(){
	var product_id = $("#product_id").val();
	var email_id = $("#hidden_email").val();
	$("#email_id").empty().trigger('chosen:updated');
	$.get('<?php echo url("product/getemailtemplate")?>', {
		product_id: product_id
	}, function (data) {
		$("#email_id").append('<option value=""></option>');
		$.each(data, function (k, v) {
			if(v.id == email_id){
				$("#email_id").append('<option selected="selected" value="' + v.id + '">' + v.template_name + '</option>');
			}else{
				$("#email_id").append('<option value="' + v.id + '">' + v.template_name + '</option>');
			}
			
		});
		$("#email_id").trigger('chosen:updated');
	}, 'json');
});
$("#product_id").change(function(){
	var product_id = $("#product_id").val();
	$("#email_id").empty().trigger('chosen:updated');
	$.get('<?php echo url("product/getemailtemplate")?>', {
		product_id: product_id
	}, function (data) {
		$("#email_id").append('<option value=""></option>');
		$.each(data, function (k, v) {
			$("#email_id").append('<option value="' + v.id + '">' + v.template_name + '</option>');
		});
		$("#email_id").trigger('chosen:updated');
	}, 'json');
});
</script>
<?PHP $this->_endblock();?>

