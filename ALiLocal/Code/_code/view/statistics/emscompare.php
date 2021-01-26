<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>对账<?php $this->_endblock(); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div id="dialog_save" class="easyui-dialog hide"title="确认订单"
		data-options="closed:true, modal:true"
		style="width:450px; height: 150px;">
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                    <th class="required-title">账单号</th>
                    <td><input name="hidden_currency" type="hidden" id="hidden_currency" value="<?php echo $currency?>"/></td>
					<td><input type="text" style="width: 200px" required="required" id="bill_no" name="bill_no" value="<?php request('bill_no')?>"></td>
              </tr>
        </table>
        <table>
        <tr>
		    <td>
		      <button class="btn btn-primary" type="submit" onclick="save()" style="margin-left: 150px">
					保存
				</button>
			</td>
		</tr>
		</table>		
        </div>
    </div>
<div style="width:700px;">
<input type="hidden" name="hiddenchannel_id" value="<?php echo request('hiddenchannel_id')?>">
<h4>差异列表&nbsp;<a id='confirm' class="btn btn-success" href="javascript:void(0);" onclick="confirm()">确认 </a></h4>
<table class="FarTable">
	<thead>
	<tr>
	    <th><input type="checkbox" onchange="selectall()"></th>
		<th>阿里订单号</th>
		<th>运单号</th>
		<th>计费重</th>
		<th>账单重</th>
		<th>币种</th>
		<th>应付总金额</th>
		<th>账单总金额</th>
		<th>差异</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isset($diffData)):?>
	<?php foreach ($diffData as $dRow):?>
		<tr>
		    <td><input type="checkbox" class="ids" name="ids[]" value="<?php echo $dRow['order_id']?>"></td>
			<td><?php echo $dRow['ali_order_no']?></td>
			<td><?php echo $dRow['tracking_no']?></td>
			<td><?php echo $dRow['weight_label']?></td>
			<td><?php echo $dRow['weight_bill']?></td>
			<td><?php echo $dRow['currency']?></td>
			<td><?php echo $dRow['fee_amount']?></td>
			<td><?php echo $dRow['bill_amount']?></td>
			<td><?php echo $dRow['balance']?></td>
		</tr>
	<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
</div>
<div style="width:700px;">
<h4>无数据列表&nbsp;</h4>
<table class="FarTable">
	<thead>
	<tr>
		<th>运单号</th>
		<th>账单重</th>
		<th>账单总金额</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isset($newData)):?>
	<?php foreach ($newData as $oRow):?>
		<tr>
			<td><?php echo $oRow['tracking_no']?></td>
			<td><?php echo $oRow['weight_bill']?></td>
			<td><?php echo $oRow['bill_amount']?></td>
		</tr>
	<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
</div>
<div style="width:700px;">
<h4>无账单列表&nbsp;</h4>
<table class="FarTable">
	<thead>
	<tr>
	    <th>阿里订单号</th>
		<th>运单号</th>
		<th>计费重</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isset($orderData)):?>
	<?php foreach ($orderData as $dRow): $order=Order::find('tracking_no = ?',$dRow)->getOne()?>
		<tr>
			<td><?php echo $order->ali_order_no?></td>
			<td><?php echo $order->tracking_no?></td>
			<td><?php echo $order->weight_cost_out?></td>
		</tr>
	<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
</div>
<script type="text/javascript">
function selectall(){
	$(".ids").each(function(){
		$(this).prop('checked',!$(this).prop('checked'))
	});
}
function confirm(){
	if($(".ids:checked").length>0){
		$('#dialog_save').dialog('open');
		$('.window-shadow').css('top','106px');
		$('.panel').css('top','106px');
		$('#dialog_save').removeClass('hide');
	}else{
		alert("请选择订单");
		return false;
	}
}
function save(){
	if($('#bill_no').val()==''){
	   $.messager.alert('', '账单号不能为空');
	   return false;
	}
	var dropIds = new Array();  
	$(".ids").each(function(){
		if($(this).prop('checked')){
			dropIds.push($(this).val());  
		}
	});
	$.ajax({
		url:'<?php echo url('statistics/savebill')?>',
		data:{order_ids:dropIds,bill_no:$('#bill_no').val(),currency:$('#hidden_currency').val()},
		type:'post',
		async:false,
		success:function(data){
// 			alert(data);
// 			if(data=='samealiorderno'){
// 			   $.messager.alert('', '新单号不能与原单号相同');
// 			}else if(data=='noorder'){
// 			   $.messager.alert('', '无相关信息');
// 			}else if(data=='nopaytime'){
// 			   $.messager.alert('', '无支付时间');
// 			}else if(data=='success'){
			   alert('确认成功');
			   setTimeout(function (){
				  window.location.reload();
			   },1000);
// 			}
		}
	});
	
}
</script>
<?PHP $this->_endblock();?>
