<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <?php //主体部分 ?>
<?php if (!empty($errors)):?>
<div>
<table class="table table-bordered table-hover table-condensed">
	<caption>
		<span class="label label-warning">导入结果</span>
	</caption>
	<thead>
		<tr>
			<th>行数</th>
			<th>错误提示</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($errors as  $i => $err):?>
		<tr>
			<td><?php echo $i+2?></td>
			<td><?php print_r($err)?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
</div>
<?php endif;?>    
<?PHP $this->_endblock();?>

