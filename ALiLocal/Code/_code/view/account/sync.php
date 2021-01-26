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
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                      同步账号信息
		               </button>
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
				<th>产品代码</th>
				<th>产品名称</th>
				<th>渠道名称</th>
				<th>失效日期</th>
				<th>同步日期</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; if (isset($account)):?>
		<?php foreach ($account as $value):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td>
				    <a target="_blank" href="<?php echo url('/detail',array('account'=>$value->account))?>">
				    <?php echo $value->account?>
				    </a>
				</td>
				<td><?php echo $value->product_code?></td>
				<td><?php echo $value->product_name?></td>
				<td><?php echo $value->channel_name?></td>
				<td><?php echo date("Y-m-d",$value->fail_time)?></td>
				<td><?php echo date("Y-m-d",$value->create_time)?></td>
			</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</form>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

