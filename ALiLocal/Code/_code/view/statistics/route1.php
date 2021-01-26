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
<form method="post" >
    <div class="FarSearch">
        <table>
            <tbody>
                <tr>
                    <th>网络</th>
                    <td>
                     <?php
                        echo Q::control ( 'dropdownlist', 'network', array('items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_code'),
                        'value' => request('network', '' ),
                        'style'=>'width:120px'
                     ) )?>
                    </td>
                    <td>
                        <button class="btn btn-small btn-info" name="search" id="search">
							<i class="icon-search"></i>
							搜索
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
	<th>阿里单号</th>
	<th>DST</th>
	<th>网络</th>
	<th>运单号 &nbsp;<?php if(request('network') != '' && request('network') != 'FEDEX' && request('network') !="YWML" && request('network') !="DHLE"):?><a href="#13" onclick="trace()"><i class="icon icon-link"></i></a><?php endif;?></th>
	<th>最近轨迹时间</th>
	<th>最近轨迹地点</th>
	<th style="width:300px">最近轨迹</th>
	<th>抓取时间</th>
	<th>操作</th>
	<th>?</th>
</tr>
</thead>
<tbody>
<?php foreach ($list as $row):?>
<tr>
	<td><?php echo $row->ali_order_no?></td>
	<td><?php echo $row->consignee_country_code?></td>
	<td>
		<?php echo $row->order->channel->network_code?>
	</td>
	<td>
	<?php if($row->order->channel->network_code == 'EMS' || $row->order->channel->network_code == 'USPS'):?>
	<a target="_blank" href="https://t.17track.net/en#nums=<?php echo $row->tracking_no?>">
	<?php elseif($row->order->channel->network_code=="FEDEX" || $row->order->channel->trace_network_code=="FEDEX") :?>
	<a target="_blank" href="https://www.trackingmore.com/fedex-tracking/cn.html?number=<?php echo $row->tracking_no?>">
	<?php elseif($row->order->channel->trace_network_code=="DHL") :?>
	<a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $row->tracking_no?>&brand=DHL">
	<?php elseif($row->order->channel->trace_network_code=="DHLE") :?>
	<a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
	<?php else:?>
	<a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $row->tracking_no?>&requester=WT/trackdetails">
	<?php endif;?>
	<?php echo $row->tracking_no?>
	</a>
	</td>
	<td><?php echo date('m-d H:i',$row->time)?></td>
	<td><?php echo $row->location?></td>
	<td><?php echo $row->description?></td>
	<td><?php echo date('m-d H:i',$row->create_time)?></td>
	<td>
		<a class="btn btn-mini" target="_blank" href="<?php echo url('order/trace',array('order_id'=>$row->order_id))?>">轨迹</a>
		<a class="btn btn-mini" target="_blank" href="<?php echo url('order/newIssueParcel',array('ali_order_no'=>$row->ali_order_no))?>">问题</a>
		
		<script type="text/javascript">tns.push("<?php echo $row->tracking_no?>")</script>
	</td>
	<td>
		<a target="_blank" href="<?php echo url('order/issue',array('ali_order_no'=>$row->ali_order_no,'parcel_flag'=>1))?>">
			<?php echo Order::find('order_id =?',$row->order_id)->getOne()->getACount()?>
		</a>
	</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php echo Q::control('pagination','',array('pagination'=>$pagination))?>
<script type="text/javascript">
function trace(){
	var network_code="<?php echo request('network')?>";
	if(network_code=="EMS"){
		window.open("https://t.17track.net/en#nums="+tns.join());
	}else{
		window.open("https://www.ups.com/track?loc=en_US&tracknum="+tns.join("%250D%250A")+"&requester=WT/trackdetails");
	}
}
</script>
<?PHP $this->_endblock();?>
