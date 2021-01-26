<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript">
</script>
<form method="post">
<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
					<th>轨迹时间</th>
					<td colspan="3">
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style" => "width: 90px"
						) )?>
						-
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style" => "width: 90px"
						) )?>
					</td>
					
					<th>运单号</th>
					<td>
						<input name="tracking_no" type="text" style="width: 150px"
							value="<?php echo request('tracking_no')?>">
					</td>
					<th>网络</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownlist', 'network_code', array('items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_code'),
                        'value' => request('network_code', '' ),
                        'style'=>'width:120px',
                     ) )?>
					</td>
					<th>渠道</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'channel_id', array('items'=>Helper_Array::toHashmap(Channel::find()->getAll(),'channel_id','channel_name'),
                        'value' => request('channel_id', '' ),
                        'style'=>'width:120px',
                        'empty'=>'true'
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
	<th>网络</th>
	<th>渠道</th>
	<th>运单号</th>
	<th>轨迹地点</th>
	<th>轨迹内容</th>
	<th style="width: 90px">轨迹时间</th>
	<th style="width: 60px">类型</th>
</tr>
</thead>
<tbody>
<?php foreach ($list as $row): $order=Order::find('order_id = ?',$row->order_id)->getOne(); $route=Route::find('id = ?',$row->route_id)->getOne();?>
<tr>
	<td><?php echo $order->channel->network_code?></td>
	<td>
		<?php echo $order->channel->channel_name?>
	</td>
	<td>
	<?php echo $route->tracking_no?>
	</td>
	<td><?php echo $route->location?></td>
	<td><?php echo $route->description?></td>
	<td><?php echo date('m-d H:i',$route->time)?></td>
	<td><?php if($row->flag=='2'):?><?php echo '无匹配'?><?php elseif($row->flag=='1') :?><?php echo '无时区'?><?php else :?><?php echo '人工'?> <?php endif;?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
</div>
<script type="text/javascript">
</script>
<?PHP $this->_endblock();?>

