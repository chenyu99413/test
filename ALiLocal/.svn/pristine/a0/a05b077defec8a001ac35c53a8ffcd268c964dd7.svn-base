<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="post">
<div class="FarSearch" >
		<table>
			<tbody>
				
					<tr>
		                  <th>部门</th>
		                  <td><?php
		                        echo Q::control ( 'dropdownbox', 'department_id', array (
		                        'items'=>RelevantDepartment::relateddepartments(),
		                        'empty'=>true,
		                        'style'=>'width:130px',
		                        'value' => request('department_id'),
		                        ) )?>
		                  </td>
						  <td>
						   <button class="btn btn-primary btn-small" id="search">
				             <i class="icon-search"></i>
				                                         搜索
			               </button>
			               <button type="submit" name="export" class="btn btn-small btn-info" value="exportlist">
								<i class="icon-download"></i>
								导出
							</button>
						  </td>
					</tr>
			</tbody>
		</table>
	</div>
</form>
<table class="FarTable">
<thead>
<tr>
	<th>序号</th>
	<th>仓库</th>
	<th>阿里订单号</th>
	<th>订单时间 </th>
	<th>入库时间</th>
	<th>网络</th>
	<th>渠道</th>
</tr>
</thead>
<tbody>
<?php $i=1; foreach ($orders as $order):?>
<tr>
	<td><?php echo $i?></td>
	<td><?php echo $order->warehouse_name?></td>
	<td>
	   <a target="edit_<?php echo $order->order_id?>" href="<?php echo url("order/detail",array("order_id"=>$order->order_id))?>">
	       <?php echo $order->ali_order_no?>
	   </a>
	</td>
	<td><?php echo date('Y-m-d H:i:s',$order['create_time'])?></td>
	<td><?php $time = $order->far_warehouse_in_time ? $order->far_warehouse_in_time : $order->warehouse_in_time;
	            echo date('Y-m-d H:i:s',$time)?></td>
	<td><?php echo $order->channel->network_code?></td>
	<td><?php echo $order->channel->channel_name?></td>
</tr>
<?php $i++;endforeach;?>
</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
<script type="text/javascript">
function edit(id){
	
}
</script>
<?PHP $this->_endblock();?>

