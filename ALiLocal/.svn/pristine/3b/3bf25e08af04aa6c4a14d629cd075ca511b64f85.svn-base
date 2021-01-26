<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript">
var tns=[];
</script>
<form method="post" action="" style="margin-bottom: 2px;" id="page-top-search"> 
	<div class="FarSearch">
		<table>
			<tbody>
				<tr>
					<th>阿里单号</th>
					<th>
						<input name="ali_order_no" type="text" style="width: 100%" value="<?php echo request('ali_order_no')?>">
                    </th>
					<td>
						<button class="btn btn-mini btn-info" name="search" id="search">
							<i class="icon-search"></i>
							搜索
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input id="parameters" type="hidden" name="parameters" value="<?php echo $parameters?>">
</form>
	<div class="tabs-container " style="min-width: 1148px;">
		<?php
		echo Q::control ( "tabs", "description", array (
			"tabs" => $tabs,"active_id" => $active_id 
		) );
		?>
		<div class="tabs-panels">
			<div class="panel-body panel-body-noheader panel-body-noborder" style="padding: 0px;">
	         </div>
		</div>
		<table class="FarTable">
		<thead>
		<tr>
			<th>阿里单号</th>
			<th>网络</th>
			<th>运单号 &nbsp;<?php if(request('network') && request('network') != 'FEDEX' && request('network') !="YWML" && request('network') !="DHLE"):?><a href="#13" onclick="trace()"><i class="icon icon-link"></i></a><?php endif;?></th>
			<th>订单原签收时间</th>
			<th>签收时间</th>
			<th>操作</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($list as $row):?>
		<tr>
			<td>
			<a target="_blank" href="<?php echo url('order/detail',array('order_id'=>$row->order_id))?>">
			<?php echo $row->ali_order_no?>
			</a>
			</td>
			<td><?php echo $row->channel->network_code?></td>
			<td>
			<?php if($row->channel->network_code == 'EMS' || $row->channel->trace_network_code=="USPS"):?>
			<a target="_blank" href="https://t.17track.net/en#nums=<?php echo $row->tracking_no?>">
			<?php elseif($row->channel->network_code=="FEDEX" || $row->channel->trace_network_code=="FEDEX") :?>
			<a target="_blank" href="https://www.trackingmore.com/fedex-tracking/cn.html?number=<?php echo $row->tracking_no?>">
			<?php elseif($row->channel->trace_network_code=="DHL") :?>
			<a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $row->tracking_no?>&brand=DHL">
			<?php elseif($row->channel->trace_network_code=="DHLE") :?>
			<a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
			<?php else:?>
			<a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $row->tracking_no?>&requester=WT/trackdetails">
			<?php endif;?>
			<?php echo $row->tracking_no?>
			</a>
			</td>
			<td><?php echo date('Y-m-d H:i',$row->delivery_time)?></td>
			<?php $route = Route::find('tracking_no=? and is_delivery=1',$row->tracking_no)->order('id desc')->getOne();?>
			<td><?php echo $route->time?date('Y-m-d H:i',$route->time):''?></td>
			
			<td>
				<a class="btn btn-mini" target="_blank" href="<?php echo url('order/trace',array('order_id'=>$row->order_id))?>">轨迹</a>
				<script type="text/javascript">tns.push("<?php echo $row->tracking_no?>")</script>
				<?php if($row->is_signunusual == 1):?>
				<a class="btn btn-mini btn-info" href="<?php echo url('signunusual/savedeliverytime',array('order_id'=>$row->order_id))?>">待确认</a>
				<?php else :?>
				<a class="btn btn-mini" disabled="true">已确认</a>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach;?>
		</tbody>
		</table>
		<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
	</div>

<script type="text/javascript">
/**
 *  点击tabs设置隐藏框值 
 */	 
function TabSwitch(code){
	$("#parameters").val(code);
	$("#page-top-search").trigger("submit");
}
</script>
<?PHP $this->_endblock();?>

