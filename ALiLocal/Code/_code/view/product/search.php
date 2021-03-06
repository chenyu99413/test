<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>产品列表<?php $this->_endblock(); ?>
<?PHP $this->_block('head');?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet"
	href="<?php echo $_BASE_DIR?>public/css/tablesorter.css">
<?PHP $this->_endblock();?>

<?PHP $this->_block("contents");?>
<div class="FarTool">
	<a class="btn btn-success" href="<?php echo url('product/edit')?>">
		<i class="icon-plus"></i>
		新建
	</a>
</div>
<table class="FarTable tablesorter"  id="myTable" style="width:60%;">
	<thead>
		<tr>	
			<th>泛远产品代码</th>
			<th>泛远产品名称</th>
			<th>客户产品代码</th>
			<th>产品名称</th>
			<th width=180>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($products as $value):?>
		<tr>
			<td>
				<a
					href="<?php echo url('product/edit',array('id'=>$value->product_id))?>"><?php echo $value->product_name_far?></a>
			</td>
			<td>
				<?php echo $value->product_chinese_name_far?>
			</td>
			<td>
				<a
					href="<?php echo url('product/edit',array('id'=>$value->product_id))?>"><?php echo $value->product_name?></a>
			</td>
			<td>
				<?php echo $value->product_chinese_name?>
			</td>
			<td>
				<a class="btn btn-mini"
					href="<?php echo url('product/edit',array('id'=>$value->product_id))?>">
					<i class="icon-edit"></i>
					编辑
				</a>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#myTable").tablesorter(); 
    } 
);
</script>
<?PHP $this->_endblock();?>

