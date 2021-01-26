<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    新建问题件
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'订单管理' => '','问题件列表' => url ( 'order/issue' ),'批量关闭' => '' 
	) 
) )?>
<form action="<?php echo url('order/issuebatchclose')?>" method="post" enctype="multipart/form-data">
    <div class="FarSearch">
		<table style="width:100%">
			<tbody>
				<tr>
					<td>
						<input type="file" name="file">
						<input type="submit" value="上传" class="btn btn-primary">
					</td>
					<td style="text-align: right">
						<a class=""
							href="<?php echo $_BASE_DIR?>public/download/批量关闭.xls">
							下载模板
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
	<?php if (!empty($error)):?>
		<table class="table-bordered table">
			<tr>
				<th>行数</th>
				<th>错误</th>
			</tr>
			<?php foreach ($error as $i => $err):?>
			<tr>
				<td><?php echo $i?></td>
				<td><?php print_r($err)?></td>
			</tr>
			<?php endforeach;?>
		</table>
		<?php endif;?>
<?PHP $this->_endblock();?>

