<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>网络列表<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<form method="POST">
	<div class="FarTool">
		<a class="btn btn-success" href="<?php echo url('network/edit');?>">
			<i class="icon-add"></i>
			新建
		</a>
	</div>
	<table class="FarTable" style="width:70%;">
		<thead>
			<tr>
				<th width=150>网络代码</th>
				<th width=200>网络名称</th>
				<th width=160>操作</th>
			</tr>
		</thead>
		<tbody>
		    <?php foreach($items as $item):?>
			<tr>
				<td><?php echo $item["code"]?></td>
				<td>
					<a href="<?php echo url('network/edit',array('id'=>$item['id']))?>"><?php echo $item["name"];?></a>
				</td>
				<td>
					<a class="btn btn-mini"
						href="<?php echo url("network/edit",array("id"=>$item["id"]))?>">
						<i class="icon-edit"></i>
						编辑
					</a>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</form>

<script type="text/javascript">
	
</script>

<?PHP $this->_endblock();?>