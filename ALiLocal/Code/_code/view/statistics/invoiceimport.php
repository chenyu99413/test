<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>

<?PHP $this->_block('contents');?>
<div class="">
	<ul class="breadcrumb">
		<li>财务管理<span class="divider">/</span>
		</li>		
		<li class="active">
			<a href="<?php echo url('/invoiceimport')?>">导入发票信息</a>
		</li>		
		<li class="pull-right">
			<h4 style="margin-top: -1px; margin-right: 50px; color: red"></h4>
		</li>
	</ul>
</div>
<div style="width:100%;height:80px;line-height:80px;" >
	<form method="post" enctype="multipart/form-data">
	      <input type="file" name="file"  accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
	      <button type="submit" class='btn'                                             
	          onclick="this.disabled='disabled';MessagerProgress('<?php echo "导入中......"?>');$(form).submit()">  
	     		<span><?php echo '导入'?></span>
	      </button> 
	      <a href='<?php echo url('/downloadtemp')?>' class="btn btn-success">
	       <i class="icon-cloud-download"></i>
	                       下载模板
	      </a>
	</form>
</div>
<?php if (!empty($result)):?>
<table class="table-bordered table">
	<tbody>
		<tr>
			<th><?php echo '行数' ?></th>
			<th><?php echo '导入情况' ?></th>
		</tr>
		<?php foreach ($result as  $i => $re):?>
		<tr>
			<td><?php echo $i+2?></td>
			<td style="<?php echo $re == '成功'?'color:green;':'color:red;'?>"><?php print_r($re)?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php endif;?>
<?PHP $this->_endblock();?>

