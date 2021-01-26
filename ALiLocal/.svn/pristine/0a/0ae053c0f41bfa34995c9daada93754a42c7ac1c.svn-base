<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>分区列表<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('partition/edit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:60%;">
		<thead>
			<tr>
				<th>分区名称</th>
				<th nowrap="nowrap">分区数</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach(PartitionManage::find()->getAll() as $value):?>
			<tr>
				<td nowrap="nowrap">
					<a
						href="<?php echo url("partition/edit",array("id"=>$value->partition_manage_id))?>"><?php echo $value->partition_name?></a>
				</td>
				<td style="text-align: right"><?php echo $value->partition_count?></td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("partition/edit",array("id"=>$value->partition_manage_id))?>">
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

