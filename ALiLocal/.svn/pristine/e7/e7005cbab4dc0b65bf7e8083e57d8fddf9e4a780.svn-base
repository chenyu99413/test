<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php 
echo Q::control ( 'path', '', array (
	'path' => array (
		'取件业务' => '',
		'取件邮编' => url ( 'pickup/zipcode' ),
		'批量取件邮编导入' => url ( 'pickup/batchzipcodeimport' )
	)
) );
?>
<div style="width:100%;height:80px;line-height:80px;" >
	<form method="post" enctype="multipart/form-data">
	      <input type="file" name="file"  accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
	      <button type="submit" class='btn'                                             
	          onclick="this.disabled='disabled';MessagerProgress('<?php echo "导入中......"?>');$(form).submit()">  
	     		<span><?php echo '导入'?></span>
	      </button> 
	      <a href='<?=$_BASE_DIR?>public/download/取件邮编批量导入模板.xlsx' class="btn btn-success">
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
			<th><?php echo '导入情况' ?></th>
		</tr>
		<?php foreach ($errors as  $i => $re):?>
		<tr>
			<td><?php echo $i+2?></td>
			<td style="<?php echo $re == '成功'?'color:green;':'color:red;'?>"><?php foreach($re as $k=>$v){ echo '['.$k.']:'.$v.'; ';}?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php endif;?>
<?PHP $this->_endblock();?>

