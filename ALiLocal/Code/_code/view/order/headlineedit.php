<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>渠道异常件标签编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'渠道异常件标签管理' => '',
			'渠道异常件标签查询' => url ( 'order/headline' ),
			'渠道异常件标签编辑' => ''
		) 
	) );
?>
<div>
<form action="" method="post" class="FarSearch">
<table>
<tr>
<th>标签</th>
<td><input type="text" name="headline" style="width: 200px" value="<?php echo $headline->headline?>">
    <input type="hidden" name="headline_id" value="<?php echo $headline->headline_id?>">
</td>
</tr>
</table>
<div class="row text-center">
		<a class="btn btn-inverse" href="<?php echo url('order/headline')?>">
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
