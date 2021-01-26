<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>员工编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'发件人管理' => '',
			'发件人查询' => url ( 'staff/sender' ),
			'发件人编辑' => ''
		) 
	) );
?>
<div>
<form action="" method="post" class="FarSearch">
<table>
<colgroup>
				<col width="12%" />
				<col width="40%" />
				<col width="10%" />
				<col width="40%" />
</colgroup>
<tr>
	<th class="required-title">发件人代码</th>
	<td>
	   <input type="hidden" name="sender_id" value="<?php echo request('sender_id')?>">
	   <input type="text" name="sender[sender_code]" required="required" value="<?php echo $sender->sender_code?>">
	</td>
	<th class="required-title">发件人姓名</th>
	<td>
		<input type="text" name="sender[sender_name]" required="required" value="<?php echo $sender->sender_name?>">
	</td>
</tr>
<tr>
	<th class="required-title">发件人公司</th>
	<td colspan="3">
		<input type="text" name="sender[sender_company]" required="required" style="width:598px" value="<?php echo $sender->sender_company?>">
	</td>

</tr>
<tr>
    <th class="required-title">发件人国家</th>
	<td>
		<input type="text" name="sender[sender_country]" required="required" value="<?php echo $sender->sender_country?>">
	</td>
	<th class="required-title">发件人省</th>
	<td>
		<input type="text" name="sender[sender_province]" required="required" value="<?php echo $sender->sender_province?>">
	</td>
</tr>
<tr>
	<th class="required-title">发件人市</th>
	<td>
		<input type="text" name="sender[sender_city]" required="required" value="<?php echo $sender->sender_city?>">
	</td>
	<th >发件人区县</th>
	<td>
		<input type="text" name="sender[sender_area]"  value="<?php echo $sender->sender_area?>">
	</td>
</tr>
<tr>
	<th class="required-title">发件人电话</th>
	<td>
		<input type="text" name="sender[sender_phone]" required="required" value="<?php echo $sender->sender_phone?>">
	</td>
	<th class="required-title">发件人邮编</th>
	<td>
		<input type="text" name="sender[sender_zip_code]" required="required" value="<?php echo $sender->sender_zip_code?>">
	</td>
</tr>
<tr>
	<th class="required-title">发件人地址</th>
	<td colspan="3">
		<input type="text" name="sender[sender_address]" required="required" style="width:598px" value="<?php echo $sender->sender_address?>">
	</td>
</tr>
<tr>
	<th>发件人邮箱</th>
	<td>
		<input type="text" name="sender[sender_email]" value="<?php echo $sender->sender_email?>">
	</td>
</tr>
</table>
<div class="row text-center">
		<a class="btn btn-inverse" href="<?php echo url('staff/sender')?>">
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
