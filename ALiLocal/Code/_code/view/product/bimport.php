<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    黑名单导入
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form style="margin: 0; padding: 0;" class="" method="post"
	action="<?php echo url('product/bimport') ?>" enctype="multipart/form-data">
	<input type="file" name="file" style="border: 1px solid #ccc;">
	<button type="submit" class="btn btn-small btn-primary">
		<i class="icon-cloud-upload"></i> 上传
	</button>
	<a class="btn btn-small btn-warning"
		href="<?php echo url('product/downloadblack')?>"> <i
		class="icon-cloud-download"></i> 下载模板
	</a>
</form>
<br />
<div class="span8" style="margin-left: 0px;">
<?php if (!empty($errors)):?>
		<table class="table-bordered table">
		<tbody>
			<tr>
				<th>行数</th>
				<th>错误提示</th>
			</tr>
				<?php foreach ($errors as  $i => $err):?>
				<tr>
				<td><?php echo $i+2?></td>
				<td><?php print_r($err)?></td>
			</tr>
				<?php endforeach;?>
			</tbody>
	</table>
		<?php endif;?>
</div>
<?PHP $this->_endblock();?>

