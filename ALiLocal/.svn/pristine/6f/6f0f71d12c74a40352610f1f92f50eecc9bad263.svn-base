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
					<th>阿里/末端单号</th>
					<td colspan="2">
						<textarea placeholder="每行一个单号" name="ali_order_no"  rows="2" style="width:130px;"><?php echo request('ali_order_no')?></textarea>
					</td>
					<th>事件代码</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "event_code", array (
							"items" => Helper_Array::toHashmap(Eventcode::find()->getAll(), 'event_code','event_code'),
						    "value" => request ( "event_code" ),
							"style" => "width:205px",
						    "empty" =>true,
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
	<th style="width:20px;"><input type="checkbox" onchange="selectall(this)"></th>
	<th style="width:130px;">订单号</th>
	<th style="width:160px;">事件时间</th>
	<th style="width:160px;">重发时间</th>
	<th style="width:160px;">成功时间</th>
	<th style="width:100px;">事件代码</th>
	<th style="width:100px;">事件位置</th>
	<th style="width:60px;">时区号</th>
	<th>失败原因</th>
	<th style="width:70px;">操作</th>
</tr>
</thead>
<tbody>
<?php if(isset($list)):?>
<?php foreach ($list as $temp):?>
<tr>
	<td><input type="checkbox" class="ids" name="ids[]" value="<?php echo $temp->event_id?>"></td>
	<td><a target="blank" href="<?php echo url('order/event',array('order_id' => $temp->order->order_id))?>"><?php echo $temp->order->ali_order_no?></a></td>
	<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->event_time)?></td>
	<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->update_time)?></td>
	<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->success_time)?></td>
	<?php if($temp->event_code=='CHECK_WEIGHT' || $temp->event_code=='CONFIRM' || $temp->event_code=='CARRIER_PICKUP'):?>
	<td><a href="javascript:void(0)" order_id="<?php echo $temp->order->order_id?>" onclick="showdetail(this)"><?php echo $temp->event_code?></a></td>
	<?php else :?>
	<td><?php echo $temp->event_code?></td>
	<?php endif;?>
	<td><?php echo $temp->event_location?></td>
	<td><?php echo $temp->timezone?></td>
	<td><?php echo Event::unicode2Chinese($temp->return_reason)?></td>
	<td>
		<a class="btn btn-mini btn-info" href="<?php echo url('/resend',array('event_id'=>$temp->event_id))?>">
			重发
		</a>
		<a class="btn btn-mini btn-info" href="<?php echo url('/resenddel',array('event_id'=>$temp->event_id))?>">
			移除
		</a>
		<?php if($temp->notice_type == 0):?>
		<a class="btn btn-mini btn-info" href="<?php echo url('/notice',array('event_id'=>$temp->event_id))?>">
			已通知
		</a>
		<?php else:?>
		<a class="btn btn-mini" disabled="true">
			已通知
		</a>
		<?php endif;?>
	</td>
</tr>
<?php endforeach;?>
<?php endif;?>
</tbody>
</table>
<a style="margin-bottom: 10px" id='allresend' class="btn btn-success btn-mini" href="javascript:void(0);" onclick="allresend(1)">一键重发 </a>
<a style="margin-bottom: 10px" id='' class="btn btn-success btn-mini" href="javascript:void(0);" onclick="allresend(2)">一键移除 </a>
<a style="margin-bottom: 10px" id='' class="btn btn-success btn-mini" href="javascript:void(0);" onclick="allresend(3)">一键已通知 </a>
<div id="window" class="easyui-window" title="" data-options="modal:true,closed:true" style="width:600px;height:300px;padding:10px;">
</div>
<?php echo Q::control('pagination','',array('pagination'=>@$pagination))?>
<script type="text/javascript">
function selectall(obj){
	$(".ids").each(function(){
		$(this).prop('checked',$(obj).prop('checked'))
	});
}

function allresend(type){
	if($(".ids:checked").length>0){
		var dropIds = new Array();  
		$(".ids").each(function(){
			if($(this).prop('checked')){
				dropIds.push($(this).val());  
			}
		});
		$.ajax({
			url:'<?php echo url('statistics/allresend')?>',
			data:{event_ids:dropIds,type:type},
			type:'post',
			async:false,
			success:function(data){
				   //alert('处理中');
				   //setTimeout(function (){
					  window.location.reload();
				   //},1000);
 			}
		});
	}else{
		alert("请选择订单");
		return false;
	}
}

function showdetail(obj){
	var event_code=$(obj).html();
	$.ajax({
		url:'<?php echo url('order/orderinfo')?>',
		type:'POST',
		dataType:'json',
		data:{event_code:event_code,order_id:$(obj).attr('order_id')},
		success:function(data){
			if(event_code=='CHECK_WEIGHT'){
				$('#window').children().remove();
				$('#window').window({
				    title:'CHECK_WEIGHT',
				    width:'600px',
				    height:'300px'
				});
				$('#window').window('open');
				var table_str='<table class="table"><thead><tr><th>数量</th><th>长度</th><th>宽度</th><th>高度</th><th>重量</th></tr></thead><tbody>';
				$.each(data,function(key,value){
					table_str+='<tr><td>'+value.quantity+'</td><td>'+value.length+'</td><td>'+value.width+'</td><td>'+value.height+'</td><td>'+value.weight+'</td></tr>';
				})
				table_str+='</tbody></table>';
				$("#window").append(table_str);
			}
			if(event_code=='CONFIRM'){
				$('#window').children().remove();
				$('#window').window({
				    title:'CONFIRM',
				    width:'400px',
				    height:'300px'
				});
				$('#window').window('open');
				var table_str='<table class="table"><thead><tr><th>费用名称</th><th>数量</th></tr></thead><tbody>';
				$.each(data,function(key,value){
					table_str+='<tr><td>'+value.fee_item_name+'</td><td>'+value.quantity+'</td></tr>';
				})
				table_str+='</tbody></table>';
				$("#window").append(table_str);
			}
			if(event_code=='CARRIER_PICKUP'){
				$('#window').children().remove();
				$('#window').window({
				    title:'CARRIER_PICKUP',
				    width:'400px',
				    height:'250px'
				});
				var carrier="FAR";
				if($("#service_code").val()=='EMS-FY'){
					carrier="EMS";
				}
				$('#window').window('open');
				var table_str='<table class="table"><thead><tr><th>承运商名称</th><th>承运商仓库地址</th></tr></thead><tbody><tr><td>'+carrier+'</td><td>'+data.location+'</td></tr></tbody></table>';
				$("#window").append(table_str);
			}
		}
	});
}
</script>
<?PHP $this->_endblock();?>

