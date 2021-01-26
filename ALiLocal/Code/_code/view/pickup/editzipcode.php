<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
if (request('zip_code_id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'取件业务' => '',
			'取件邮编' => url ( 'pickup/zipcode' ),
			'取件邮编编辑' => url ( 'supplier/edit', array (
				'zip_code_id' => $zipcode->zip_code_id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'取件业务' => '',
			'取件邮编' => url ( 'pickup/zipcode' ),
			'新建取件邮编' => url ( 'pickup/editzipcode' ) 
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch span10" style="padding:5px;">
	<div class="span5">
		<table>
			<tbody>
				<tr>
					<th class="required-title">起始邮编</th>
    				<td>
    					<input required="required" name="zip_code_low" id="zip_code_low" type="text" style="width: 150px" value="<?php echo $zipcode->zip_code_low?>">
    				</td>
				</tr>
				<tr>
					<th class="required-title">截止邮编</th>
    				<td>
    					<input required="required" name="zip_code_high" id="zip_code_high" type="text" style="width: 150px" value="<?php echo $zipcode->zip_code_high?>">
    				</td>
				</tr>
				<tr>
					<th>省份</th>
    				<td>
    					<input name="province" id="province" type="text" style="width: 150px" value="<?php echo $zipcode->province?>">
    				</td>
				</tr>
				<tr>
					<th class="required-title">城市</th>
    				<td>
    					<input required="required" name="area" id="area" type="text" style="width: 150px" value="<?php echo $zipcode->area?>">
    				</td>
				</tr>
				<tr>
					<th class="required-title">取件网点</th>
    				<td>
    					<?php
    					echo Q::control("dropdownbox", "pick_company", array(
    						"items" => $relevant_department_names,
    						"value" => $zipcode->pick_company,
    						"empty" => "true",
    						"required" => "required"
    					))?>
    				</td>
				</tr>
				<tr>
					<?php 
					$warehouse_code=Helper_Array::toHashmap(CodeWarehouse::find()->getAll(), 'warehouse', 'warehouse');
					?>
					<th class="required-title">仓库代码</th>
    				<td>
    					<?php
    					echo Q::control("dropdownbox", "warehouse_code", array(
    						"items" => $warehouse_code,
    						"value" => $zipcode->warehouse,
    						"empty" => "true",
    						"required" => "required"
    					))?>
    				</td>
				</tr>
				<tr>
					<th class="required-title">产品代码</th>
    				<td>
    					<?php
    					echo Q::control("dropdownbox", "product", array(
    						"items" => $product,
    						"value" => $zipcode->service_code,
    						"empty" => "true",
    						"required" => "required"					
    					))?>
    				</td>
				</tr>
			</tbody>
		</table>
		</div>
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('pickup/zipcode')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
</form>
<?PHP $this->_endblock();?>

