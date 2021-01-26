<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
绑定订单
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
    <span id="service_product">总单号：<?php echo request('total_list_no')?></span>
</div>
<div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>总单号</th>
				<th>绑定时间</th>
				<th>阿里订单号</th>
				<th>末端运单号</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1;foreach ($order as $temp):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $temp->total_no?></td>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->create_time)?></td>
				<td><?php echo $temp->ali_order_no?></td>
				<td><?php echo $temp->tracking_no?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
</div>
<form  method="POST">
<div class="FarSearch" >
	<table>
		<tr>
		  <th>阿里订单号</th>
		  <td colspan="2"><textarea rows="1" name="ali_order_no" placeholder="每行一个单号"></textarea>
		  </td>
		  <th>末端单号</th>
		  <td><textarea rows="1" name="tracking_no" placeholder="每行一个单号"></textarea>
		  </td>
		  <th>交货核查总单号</th>
		  <td><input type="text" name="comparison_total_no" value="">
		  </td>
		  <th>启程扫描总单号</th>
		  <td><input type="text" name="out_total_no" value="">
		  </td>
		</tr>
		<tr>
		  <th>抵达扫描总单号</th>
		  <td><input type="text" name="in_total_no" value="">
		  </td>
		  <td>
		      <button class="btn btn-mini btn-info" onclick="window.location.reload()">绑定</button>
		      <a class="btn btn-mini btn-inverse" href="<?php echo url('/totaltrack')?>">返回</a>
		  </td>
		 </tr>
	</table>
</div>
</form>
<?PHP $this->_endblock();?>
