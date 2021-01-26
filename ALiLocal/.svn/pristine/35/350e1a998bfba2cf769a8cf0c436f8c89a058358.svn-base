<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>黑名单编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'黑名单管理' => '',
			'黑名单查询' => url ( 'product/blacklist' ),
			'黑名单编辑' => ''
		) 
	) );
?>
<div>
<form action="" method="post" class="FarSearch">
<table>
<tr>
<th>国家二字码</th>
<td><input type="text" name="consignee_country_code" value="<?php echo $black->consignee_country_code?>">
    <input type="hidden" name="blacklist_id" value="<?php echo $black->blacklist_id?>">
</td>
<th>城市</th>
<td><input type="text" name="consignee_city" value="<?php echo $black->consignee_city?>"></td>
<th>省州</th>
<td><input type="text" name="consignee_state_region_code" value="<?php echo $black->consignee_state_region_code?>"></td>
<th>邮编</th>
<td><input type="text" name="consignee_postal_code" value="<?php echo $black->consignee_postal_code?>"></td>
<th>品名</th>
<td><input type="text" name="product_name" value="<?php echo $black->product_name?>"></td>
<th>产品</th>
<td>
	<?php
	echo Q::control ( "dropdownbox", "product_id", array (
		"items" => Helper_Array::toHashmap ( Product::find ()->getAll (), "product_id", "product_chinese_name" ),
		"value" => $black->product_id,
		"style" => "width: 120px",
	) )?>
</td>
</tr>
<tr>
<th>发件人</th>
<td><input type="text" name="sender_name1" value="<?php echo $black->sender_name1?>"></td>
<th>发件人公司</th>
<td><input type="text" name="sender_name2" value="<?php echo $black->sender_name2?>"></td>
<th>地址</th>
<td colspan="8"><input type="text" name="sender_street1" value="<?php echo $black->sender_street1?>" style="width: 550px"></td>
</tr>
</table>
<div class="row text-center">
		<a class="btn btn-inverse" href="<?php echo url('product/blacklist')?>">
			<i class="icon-reply"></i>
			返回
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="icon-save"></i>
			保存
		</button>
</div>
</form>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">

</script>
