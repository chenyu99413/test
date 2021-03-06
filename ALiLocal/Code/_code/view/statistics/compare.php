<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>对账<?php $this->_endblock(); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
if (!request('f')):?>
<div>
<form action="" method="post" enctype="multipart/form-data">
<table>
<tr>
	<td>
		<input type="file" name="file">
	</td>
	<td>
		<input type="submit" value="上传" class="btn btn-warning">
	</td>
</tr>
</table>

</form>
</div>
<?php else:?>
<div style="width:700px;">
<h4>差异列表&nbsp;<a class="btn btn-warning btn-small" href="<?php echo url('/compare',array('export'=>1,'f'=>request('f'),'currency'=>$currency))?>"><i class="icon-download"></i>导出差异列表</a></h4>
<table class="FarTable">
	<thead>
	<tr>
		<th>阿里订单号</th>
		<th>运单号</th>
		<th>预报重</th>
		<th>币种</th>
		<th>应付总金额</th>
		<th>账单重</th>
		<th>账单总金额</th>
		<th>差异</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($newData as $oRow):?>
		<tr>
			<td><?php echo $oRow['ali_order_no']?></td>
			<td><?php echo $oRow['tracking_no']?></td>
			<td><?php echo $oRow['weight_label']?></td>
			<td><?php echo $oRow['currency']?></td>
			<td><?php echo $oRow['fee_amount']?></td>
			<td><?php echo $oRow['weight_bill']?></td>
			<td><?php echo $oRow['bill_amount']?></td>
			<td><?php echo $oRow['balance']?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>
</div>
<?php endif;?>
<?PHP $this->_endblock();?>
