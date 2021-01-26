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
		'仓库业务' => '','包裹抵达扫描列表' => url ( 'warehouse/totalinlist' ),'总单明细' => '' 
	) 
) )?>
<label>总单单号：<?php echo $total_no?></label>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>(阿里/末端)单号</th>
					<td>   
					   <input name="order_no" type="text" style="width: 110px"
							value="<?php echo request('order_no')?>">
                    </td>
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
				<th>扫描时间</th>
				<th>阿里单号</th>
				<th>末端单号</th>
				<th>产品</th>
				<th>件数</th>
				<th>实重</th>
				<th>目的国家</th>
			</tr>
		</thead>
		<tbody>
		<?php $i=1; foreach ($lists as $list):?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $list->total_no?></td>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s',$list->create_time)?></td>
				<td>
    				
                    	<?php echo $list->ali_order_no ?>

                </td>
				<td>
    				<?php if($list->order->channel->network_code=="EMS" || $list->order->channel->network_code=="USPS"):?>
                    <a target="_blank" href="https://t.17track.net/en#nums=<?php echo $list->tracking_no?>">
                    <?php elseif($list->order->channel->network_code=="FEDEX" || $list->order->channel->trace_network_code=="FEDEX") :?>
                    <a target="_blank" href="https://www.trackingmore.com/fedex-tracking/cn.html?number=<?php echo $list->tracking_no?>">
                    <?php elseif($list->order->channel->trace_network_code=="DHL") :?>
                    <a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $list->tracking_no?>&brand=DHL">
                    <?php elseif($list->order->channel->trace_network_code=="DHLE") :?>
                    <a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
                    <?php else :?>
                    <a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $list->tracking_no?>&requester=WT/trackdetails">
                    <?php endif;?>
                    <?php echo $list->tracking_no?>
                    </a>
				</td>
				<td><?php echo $list->order->service_code ?></td>
				<?php $weight = 0; $sum = 0; if($list->order->order_id){
			        $quantity = Faroutpackage::find('order_id = ?',$list->order->order_id)->asArray()->getAll();
			        if(count($quantity) > 0){
			           $weight = $list->order->weight_actual_out;
			           $sum = Faroutpackage::find('order_id = ?',$list->order->order_id)->getSum('quantity_out');
			        }else{
			           $weight = $list->order->weight_actual_in;
			           $sum = Farpackage::find('order_id = ?',$list->order->order_id)->getSum('quantity');
			        }
			    }?>
				<td style="text-align:right;"><?php echo $sum?></td>
				<td style="text-align:right;"><?php echo sprintf('%.2f',$weight)?></td>
				<td><?php echo $list->order->consignee_country_code?></td>
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

