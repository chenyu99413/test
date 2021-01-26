<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>黑名单编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'渠道通讯录管理' => '',
			'渠道通讯录查询' => url ( 'product/book' ),
			'渠道通讯录编辑' => ''
		) 
	) );
?>
<div>
<form action="" method="post" class="FarSearch">
<table>
<tr>
<th>渠道</th>
<td>
	<?php
	echo Q::control("dropdownbox", "channel_id", array(
		"items" => Helper_Array::toHashmap(Channel::find()->getAll(), "channel_id", "channel_name"),
		"value" => $postalbook->channel_id,
		"style" => "width: 150px",
	))?>
</td>
</tr>
<tr>
<th>国家二字码</th>
<td><input type="text" name="code_word_two" value="<?php echo $postalbook->code_word_two?>">
    <input type="hidden" name="book_id" value="<?php echo $postalbook->book_id?>">
</td>
</tr>
<tr>
<th>目的地联系电话</th>
<td colspan="3" style="border-left: 2px #eee">
	<textarea style="width: 600px" rows="4" name="servicetel" id="servicetel"><?php echo $postalbook->servicetel?></textarea>
</td>
</tr>
<tr>
<th>目的地作息时间</th>
<td colspan="3" style="border-left: 2px #eee">
<textarea style="width: 600px" rows="4" name="servicesch" id="servicesch"><?php echo $postalbook->servicesch?></textarea>
</td>
</tr>
<tr>
<th>目的地其它联系信息</th>
<td colspan="3" style="border-left: 2px #eee">
<textarea style="width: 600px" rows="4" name="customtel" id="customtel"><?php echo $postalbook->customtel?></textarea>
</td>
</tr>
</table>
<div class="row text-center">
		<a class="btn btn-inverse" href="<?php echo url('product/book')?>">
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
