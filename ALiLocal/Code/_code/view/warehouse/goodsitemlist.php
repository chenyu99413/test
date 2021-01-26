<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
包裹抵达扫描总单
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'仓库业务' => '','随货单证核查' => url ( 'warehouse/goodscheck' ),'随货单明细' => '' 
	) 
) )?>
<label>随货单号：<?php echo request('goods_check_no')?></label>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<th>主单号</th>
					<td><textarea rows="1" name="tracking_no" placeholder="每行一个单号"><?php echo request('tracking_no')?></textarea></td>
					<th>阿里单号</th>
					<td><textarea rows="1" name="ali_order_no" placeholder="每行一个单号"><?php echo request('ali_order_no')?></textarea></td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>主单号</th>
				<th>阿里单号</th>
				<th>状态</th>
				<th>渠道</th>
				<th>国家</th>
				<th>网络</th>
			</tr>
		</thead>
		<tbody>
		<?php $status=Order::$status?>
		<?php $i=1; foreach ($orders as $order):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $order->tracking_no?></td>
				<td><a  target="_blank"
					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
					    <?php echo $order->ali_order_no ?>
					</a>
				</td>
				<td><?php echo $status[$order->order_status]?></td>
				<td><?php echo $order->channel->channel_name?></td>
				<td><?php echo $order->consignee_country_code?></td>
				<td><?php echo $order->channel->network_code?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</form>
<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>

<?PHP $this->_endblock();?>

