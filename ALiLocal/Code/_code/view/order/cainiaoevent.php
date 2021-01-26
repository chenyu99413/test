<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    订单事件
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <style type="text/css">
        .table>tbody>tr>td{
            border:0px;
        }
    </style>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<?php if (!request_is_ajax()):?>
<?php
	echo Q::control ( 'path', '', array (
		'path' => array (
			'业务管理' => '',
			'订单查询' => url ( 'order/search' ),
			'订单事件' => ''
		) 
	) );
?>
<p style="clear: both;color:red">阿里单号：<?php echo $order->ali_order_no?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;泛远单号：<?php echo $order->far_no?></p><input type='hidden' id="service_code" value="<?php echo $order->service_code?>">
<?php endif;?>
<form method="POST" onsubmit=" return checktime();">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr id="anchor">
				    <th class="required-title">事件代码</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "cainiao_code", array (
							"items" => $cainiao_code,
						    "value" => request ( "cainiao_code" ),
							"style" => "width:205px",
						    "empty" =>true,
						    "required"=>'required'
						) )?>
					</td>
					<th class="required-title">事件时间</th>
					<td>
						<input class="easyui-datetimebox"
						name="cainiao_time"
						value="<?php echo Helper_Util::strDate ( "Y-m-d H:i:s", time() )?>"
						style="width: 150px"/>
					</td>				
				</tr>
			</tbody>
		</table>
	</div>
	<div class="offset5 span2">
	   <button class="btn btn-small btn-success" id="search">
                                保存
       </button>
	</div>
	<h4 style="clear: both;"> 订单事件信息</h4>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:160px;">事件时间</th>
				<th class="span2">事件代码</th>
				<th style="">结果</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($list as $temp):?>
			<tr>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->cainiao_time)?></td>
				<?php if($temp->cainiao_code=='FEE'):?>
				<td><a href="javascript:void(0)" onclick="showdetail(this)"><?php echo $temp->cainiao_code?></a></td>
				<?php else :?>
				<td><?php echo $temp->cainiao_code?></td>
				<?php endif;?>
				<td>
					<?php if ($temp->send_flag =='1'):?>
					   成功
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<input type="hidden" id="order_id"name="order_id" value="<?php echo $order->order_id?>">
</form>
<div id="window" class="easyui-window" title="" data-options="modal:true,closed:true" style="width:600px;height:300px;padding:10px;">
		
</div>

<?PHP $this->_endblock();?>
<script type="text/javascript">
	function showdetail(obj){
		var event_code=$(obj).html();
		$.ajax({
			url:'<?php echo url('order/orderinfo')?>',
			type:'POST',
			dataType:'json',
			data:{event_code:event_code,order_id:$("#order_id").val()},
			success:function(data){
				if(event_code=='FEE'){
					$('#window').children().remove();
					$('#window').window({
					    title:'FEE',
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
			}
		});
	}
	//检查时间是否填写
	function checktime(){
		var time =$('.textbox-value').val();
		if(time.length<1){
			$.messager.alert('Error','请填写事件时间');
			return false;
		}else{
			return true;
		}
	}
</script>

