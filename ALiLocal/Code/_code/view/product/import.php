<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    无服务邮编导入
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form style="margin: 0; padding: 0;" class="" method="post"
	action="<?php echo url('product/import') ?>" enctype="multipart/form-data">
	<input type="file" name="file" style="border: 1px solid #ccc;">
	<button type="submit" class="btn btn-small btn-primary">
		<i class="icon-cloud-upload"></i>
		上传
	</button>
	<a class="btn btn-small btn-warning" href="<?php echo url('product/download')?>">
		<i class="icon-cloud-download"></i>
		下载模板
	</a>
</form>
<br/>
<div class="span8" style="margin-left:0px;">
<table class="table table-bordered table-hover table-condensed">
	<caption>
		<span class="label label-warning">导入结果</span>
	</caption>
	<thead>
		<tr>
			<th style="width:30px;">行数</th>
			<th style="width:30px;">状态</th>
			<th style="width:100px;">信息</th>
		</tr>
	</thead>
	<tbody>
	<?php if(@$sheet):?>
		<?php $i=1?>
		<?php foreach ($sheet as $val):?>
		<tr>
			<td style="width:30px;"><?php echo ++$i?></td>
			<td><?php echo $val['状态']?></td>
			<td><?php echo $val['信息']?></td>
		</tr>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
</div>
<?PHP $this->_endblock();?>

