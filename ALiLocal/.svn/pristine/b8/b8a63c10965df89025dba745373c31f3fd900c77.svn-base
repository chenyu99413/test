<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>偏派列表<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('remote/edit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:60%;">
		<thead>
			<tr>
				<th>偏派名称</th>
				<th width=160>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach(RemoteManage::find()->getAll() as $value):?>
			<tr>
				<td>
					<a
						href="<?php echo url("remote/edit",array("id"=>$value->remote_manage_id))?>"><?php echo $value->remote_name?></a>
				</td>
				<td>
					<a class="btn btn-mini"
						href="<?php echo url("remote/edit",array("id"=>$value->remote_manage_id))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>
<?PHP $this->_endblock();?>

