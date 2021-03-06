<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
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
				    <th>渠道</th>
				    <td>
    				    <?php
                        echo Q::control ( 'dropdownbox', 'channel_id', array (
                            'items' => Helper_Array::toHashmap(Channel::find()->asArray()->getAll(),'channel_id','channel_name'),
                                'empty'=>true,
                                'value'=>request('channel_id'),
                        ) )?>
				    </td>
				    <th>
						日期从：
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style"=>"width:130px",
						    "required"=>'required'
						) )?>
					</td>
					<th>到</th>
					<td>
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style"=>"width:130px",
						    "required"=>'required'
						) )?>
					</td>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         查询
		               </button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if(isset($warehouse_in)):?>
	<h5>订单推送时间</h5>
	<table class="FarTable">
	   <thead>
	       <tr>
	           <th style="width:150px;"></th>
	           <th>总订单数</th>
	           <th>达标订单数</th>
	           <th>达标要求</th>
	           <th>实际达标</th>
	           <th style="width:300px;">达标情况</th>
	       </tr>
	   </thead>
	   <tbody>
	       <tr>
	           <th>入库及时率</th>
	           <td><?php echo $warehouse_in['count']?></td>
	           <td><?php echo $warehouse_in['count']-count($warehouse_in['substandard'])?></td>
	           <td>95%</td>
	           <td><?php echo sprintf('%.2f',($warehouse_in['count']-count($warehouse_in['substandard']))/$warehouse_in['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($warehouse_in['count']-count($warehouse_in['substandard']))/$warehouse_in['count']*100)/95*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $warehouse_in['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	   </tbody>
	</table>
	<h5>核查成功时间</h5>
	<table class="FarTable">
	   <thead>
	       <tr>
	           <th style="width:150px;"></th>
	           <th>总订单数</th>
	           <th>达标订单数</th>
	           <th>达标要求</th>
	           <th>实际达标</th>
	           <th style="width:300px;">达标情况</th>
	       </tr>
	   </thead>
	   <tbody>
	       <tr>
	           <th>支付及时率</th>
	           <td><?php echo $pay['count']?></td>
	           <td><?php echo $pay['count']-count($pay['substandard'])?></td>
	           <td>95%</td>
	           <td><?php echo sprintf('%.2f',($pay['count']-count($pay['substandard']))/$pay['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($pay['count']-count($pay['substandard']))/$pay['count']*100)/95*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $pay['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	   </tbody>
	</table>
	<h5>支付时间</h5>
	<table class="FarTable">
	   <thead>
	       <tr>
	           <th style="width:150px;"></th>
	           <th>总订单数</th>
	           <th>达标订单数</th>
	           <th>达标要求</th>
	           <th>实际达标</th>
	           <th style="width:300px;">达标情况</th>
	       </tr>
	   </thead>
	   <tbody>
	       <tr>
	           <th>出库及时率</th>
	           <td><?php echo $warehouse_out['count']?></td>
	           <td><?php echo $warehouse_out['count']-count($warehouse_out['substandard'])?></td>
	           <td>98%</td>
	           <td><?php echo sprintf('%.2f',($warehouse_out['count']-count($warehouse_out['substandard']))/$warehouse_out['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($warehouse_out['count']-count($warehouse_out['substandard']))/$warehouse_out['count']*100)/98*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $warehouse_out['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	   </tbody>
	</table>
	<h5>标签打印时间</h5>
	<table class="FarTable">
	   <thead>
	       <tr>
	           <th style="width:150px;"></th>
	           <th>总订单数</th>
	           <th>达标订单数</th>
	           <th>达标要求</th>
	           <th>实际达标</th>
	           <th style="width:300px;">达标情况</th>
	       </tr>
	   </thead>
	   <tbody>
	       <tr>
	           <th>派送准时率</th>
	           <td><?php echo $delivery_on_time['count']?></td>
	           <td><?php echo $delivery_on_time['count']-count($delivery_on_time['substandard'])?></td>
	           <td>95%</td>
	           <td><?php echo sprintf('%.2f',($delivery_on_time['count']-count($delivery_on_time['substandard']))/$delivery_on_time['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($delivery_on_time['count']-count($delivery_on_time['substandard']))/$delivery_on_time['count']*100)/95*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $delivery_on_time['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	       <tr>
	           <th>派送妥投率</th>
	           <td><?php echo $delivery_completed['count']?></td>
	           <td><?php echo $delivery_completed['count']-count($delivery_completed['substandard'])?></td>
	           <td>99%</td>
	           <td><?php echo sprintf('%.2f',($delivery_completed['count']-count($delivery_completed['substandard']))/$delivery_completed['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($delivery_completed['count']-count($delivery_completed['substandard']))/$delivery_completed['count']*100)/99*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $delivery_completed['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	   </tbody>
	</table>
	<h5>承运商取件时间</h5>
	<table class="FarTable">
	   <thead>
	       <tr>
	           <th style="width:150px;"></th>
	           <th>总订单数</th>
	           <th>达标订单数</th>
	           <th>达标要求</th>
	           <th>实际达标</th>
	           <th style="width:300px;">达标情况</th>
	       </tr>
	   </thead>
	   <tbody>
	       <tr>
	           <th>国际物流商时效达成率</th>
	           <td><?php echo $carriers_pick_up['count']?></td>
	           <td><?php echo $carriers_pick_up['count']-count($carriers_pick_up['substandard'])?></td>
	           <td>95%</td>
	           <td><?php echo sprintf('%.2f',($carriers_pick_up['count']-count($carriers_pick_up['substandard']))/$carriers_pick_up['count']*100).'%'?></td>
	           <td><?php echo (sprintf('%.2f',sprintf('%.2f',($carriers_pick_up['count']-count($carriers_pick_up['substandard']))/$carriers_pick_up['count']*100)/95*100)).'%'?>  <a href="<?php echo url('/export',array('order_ids'=>implode(',', $carriers_pick_up['substandard'])))?>">不达标订单明细</a></td>
	       </tr>
	   </tbody>
	</table>
	<?php endif;?>
</form>
    
<?PHP $this->_endblock();?>

