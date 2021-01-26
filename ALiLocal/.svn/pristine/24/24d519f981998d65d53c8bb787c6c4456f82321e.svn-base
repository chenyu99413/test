<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    账号同步
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<td>
					   <a class="btn btn-danger btn-small" href="<?php echo url('/upsdetail');?>">
			             <i class="icon-edit"></i>
			                     	新增信息
		               </a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>序号</th>
				<th>打单账号</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; if (isset($accounts)):?>
		<?php foreach ($accounts as $value):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td>
				    <a target="_blank" href="<?php echo url('/upsdetail',array('id'=>$value->id))?>">
				    <?php echo $value->account?>
				    </a>
				</td>
			</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

