<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php
echo Q::control ( 'path', '', array (
	'path' => array (
		'仓库业务' => '','总单列表' => url ( 'warehouse/totallist' ),'总单明细' => '' 
	) 
) )?>
<label>总单单号：<?php echo $total->total_list_no?></label>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>出库时间</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 123px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 123px;">
					</td>
				    <th>(阿里/末端)单号</th>
					<td>   
					   <input name="order_no" type="text" style="width: 110px"
							value="<?php echo request('order_no')?>">
                    </td>
                    <th>产品</th>
                    <td><?php
                        $service_product = Product::find()->asArray()->getAll();
                        echo Q::control ( 'dropdownbox', 'service_code', array (
                        'items'=>Helper_Array::toHashmap($service_product,'product_name','product_chinese_name'),
                        'empty'=>true,
                        'value' => request('service_code'),
                        ) )?>
                    </td>
                    <th>国家</th>
					<td>
						<input name="consignee_country_code" type="text" style="width: 30px"
							value="<?php echo request('consignee_country_code')?>">
					</td>
					<th>网络</th>
                    <td><?php
                        echo Q::control ( 'dropdownbox', 'network_code', array (
                        'items' => Helper_Array::toHashmap(Channel::find()->setColumns('network_code')->asArray()->getAll(),'network_code','network_code'),
                        'empty'=>true,
                        'value'=>request('network_code'),
                        ) )?>
                    </td>
					<th>渠道</th>
                    <td><?php
                        echo Q::control ( 'dropdownbox', 'channel_id', array (
                        'items' => Helper_Array::toHashmap(Channel::find()->asArray()->getAll(),'channel_id','channel_name'),
                        'empty'=>true,
                        'value'=>request('channel_id'),
                        ) )?>
                    </td>
				</tr>
				<tr>
					<td colspan="2">
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
	<table class="FarTable">
		<thead>
			<tr>
				<th>No</th>
				<th>总单单号</th>
				<th>阿里单号</th>
				<th>状态</th>
				<th>末端单号</th>
				<th>销售产品</th>
				<th>渠道</th>
				<th>国家</th>
				<th>网络</th>
				<th>出库日期</th>
				<th>发件日期</th>
				<th>订单轨迹</th>
			</tr>
		</thead>
		<tbody>
		<?php $status=Order::$status?>
		<?php $i=1; foreach ($orders as $order):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $order->total_list_no?></td>
				<td><a  target="_blank"
					    href="<?php echo url('order/detail', array('order_id' => $order->order_id))?>">
					    <?php echo $order->ali_order_no ?>
					</a>
				</td>
				<td><?php echo $status[$order->order_status]?></td>
				<td><?php echo $order->tracking_no?></td>
				<td><?php echo $order->service_product->product_chinese_name?></td>
				<td><?php echo $order->channel->channel_name?></td>
				<td><?php echo $order->consignee_country_code?></td>
				<td><?php echo $order->channel->network_code?></td>
				<td><?php echo date('Y-m-d',$order->warehouse_out_time)?></td>
				<td><?php echo date('Y-m-d',$order->record_order_date)?></td>
				<?php $route=Route::find('tracking_no=?',$order->tracking_no)->order('time desc')->getOne()?>
            	<td><?php echo $route->description?></td>
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
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

