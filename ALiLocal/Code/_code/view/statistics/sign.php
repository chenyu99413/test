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
<div class="tabs-header">
<div class="tabs-wrap">
<ul class="tabs" style="height: 27px;">
 <li class="tabs-selected"><a class="tabs-inner" style="height: 27px; line-height: 26.5333px;"><span class="tabs-title">有关联</span><span class="tabs-icon"></span></a></li>
 <li><a href="<?php echo url('/sign2')?>" class="tabs-inner" style="height: 27px; line-height: 26.5333px;"><span class="tabs-title">无关联</span><span class="tabs-icon"></span></a></li>
 </ul>
</div>
</div>
<form method="post">
	<div>
		<button class="btn btn-info btn-small" id="export" name="export" value="export">
	         <i class="icon-download"></i>导出
		</button>
	</div>
<table class="FarTable">
<thead>
<tr>
	<th>阿里单号</th>
	<th>DST</th>
	<th>网络</th>
	<th>运单号</th>
	<th>最近轨迹时间</th>
	<th>最近轨迹地点</th>
	<th style="width:300px">最近轨迹</th>
	<th>抓取时间</th>
	<th>操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($list as $row):?>
<tr>
	<td><?php echo $row['ali_order_no']?></td>
	<td><?php echo $row['consignee_country_code']?></td>
	<td>
		<?php $channel = Channel::find('channel_id=?',$row['channel_id'])->getOne();?>
		<?php echo $channel->network_code?>
	</td>
	<td>
	<?php if($channel->network_code == 'EMS' || $channel->network_code == 'USPS'):?>
	<a target="_blank" href="https://t.17track.net/en#nums=<?php echo $row['tracking_no']?>">
	<?php elseif ($channel->network_code == 'FEDEX' || $channel->trace_network_code=="FEDEX"):?>
	<a target="_blank" href="https://www.trackingmore.com/fedex-tracking/cn.html?number=<?php echo $row['tracking_no']?>">
	<?php elseif($channel->trace_network_code=="DHL") :?>
	<a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $row['tracking_no']?>&brand=DHL">
	<?php elseif($channel->trace_network_code=="DHLE") :?>
	<a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
	<?php else:?>
	<a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $row['tracking_no']?>&requester=WT/trackdetails">
	<?php endif;?>
	<?php echo $row['tracking_no']?>
	</a>
	</td>
	<td><?php echo date('m-d H:i',$row['time'])?></td>
	<td><?php echo $row['location']?></td>
	<td><?php echo $row['description']?></td>
	<td><?php echo date('m-d H:i',$row['create_time'])?></td>
	<td>
		<a class="btn btn-mini" target="_blank" href="<?php echo url('order/trace',array('order_id'=>$row['order_id']))?>">轨迹</a>
		<a class="btn btn-mini" target="_blank" href="<?php echo url('order/newIssueParcel',array('ali_order_no'=>$row['ali_order_no']))?>">问题</a>
		<a class="btn btn-mini btn-info" href="<?php echo url('statistics/signtype',array('type'=>'2','id'=>$row['id']))?>">移除</a>
		<?php if($row['rouets_type'] == 0):?>
		<a class="btn btn-mini btn-info" href="<?php echo url('statistics/signtype',array('type'=>'1','id'=>$row['id']))?>">待确认</a>
		<?php else :?>
		<a class="btn btn-mini" disabled="true">已确认</a>
		<?php endif;?>
		<script type="text/javascript">tns.push("<?php echo $row['tracking_no']?>")</script>
	</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</form>
<?php echo Q::control('pagination','',array('pagination'=>@$pagination))?>

<script type="text/javascript">
// function trace(){
// 	window.open("https://www.ups.com/track?loc=en_US&tracknum="+tns.join("%250D%250A")+"&requester=WT/trackdetails");
// }
</script>
<?PHP $this->_endblock();?>

