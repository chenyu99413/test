<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div style="width:100%;height:80px;line-height:80px;" >
	<form method="post" enctype="multipart/form-data">
	      <input type="file" name="file"  accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
	      <button type="submit" class='btn'                                             
	          onclick="this.disabled='disabled';MessagerProgress('<?php echo "订单导入中......"?>');$(form).submit()">  
	     		<span><?php echo '导入'?></span>
	      </button> 
	      <a href='<?php echo url('order/dloadchange')?>' class="btn btn-success">
	       <i class="icon-cloud-download"></i>
	                       下载模板
	      </a>
	</form>
</div>
<?php if (!empty($errors)):?>
		<table class="table-bordered table">
			<tbody>
				<tr>
					<th><?php echo '行数' ?></th>
					<th><?php echo '错误提示' ?></th>
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
<?PHP $this->_endblock();?>

