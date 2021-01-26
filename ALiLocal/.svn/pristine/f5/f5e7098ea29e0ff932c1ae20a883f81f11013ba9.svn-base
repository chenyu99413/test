<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    价格列表
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
    <form method="POST">
	<div class="FarTool">
		<a class="btn btn-success btn-small" href="<?php echo url('price/edit')?>">
			<i class="icon-plus"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:60%;">
		<thead>
			<tr>
				<th>价格名称</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach(PriceManage::find()->getAll() as $value):?>
			<tr>
				<td nowrap="nowrap">
					<a
						href="<?php echo url("price/edit",array("price_manage_id"=>$value->price_manage_id))?>"><?php echo $value->price_name?></a>
				</td>
				<td nowrap="nowrap">
					<a class="btn btn-mini"
						href="<?php echo url("price/edit",array("price_manage_id"=>$value->price_manage_id))?>">
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

