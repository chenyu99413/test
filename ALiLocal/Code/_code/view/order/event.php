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
						$customer = Customer::find('customer_id=?',$order->customer_id)->getOne();
						if($customer->customs_code == 'ALPL'){
							echo Q::control ( "dropdownbox", "event_code", array (
								"items" => Event::$pl_event,
								"value" => request ( "event_code" ),
								"style" => "width:205px",
								"empty" =>true,
								"required"=>'required'
							) );
						}else{
							echo Q::control ( "dropdownbox", "event_code", array (
								"items" => $event_code,
								"value" => request ( "event_code" ),
								"style" => "width:205px",
								"empty" =>true,
								"required"=>'required'
							) );
						}
						?>
					</td>
					<th class="required-title">事件时间</th>
					<td>
						<input class="easyui-datetimebox"
						name="event_time"
						value="<?php echo Helper_Util::strDate ( "Y-m-d H:i:s", time() )?>"
						style="width: 150px"/>
					</td>
					<th class="required-title">事件位置</th>
					<td>
					    <?php
						echo Q::control ( "dropdownbox", "event_location", array (
							"items" => array('杭州'=>'杭州','上海'=>'上海','义乌'=>'义乌','广州'=>'广州','南京'=>'南京','US'=>'US','青岛'=>'青岛','深圳'=>'深圳','Russian'=>'Russian'),
						    "value" => '杭州',
							"style" => "width:150px",
						    "required"=>'required'
						) )?>
					</td>
					<th class="required-title">时区号</th>
					<td>
						<input  required="required" style="width: 80px;" type="text" maxlength="32" name="timezone" value="8" />
					</td>
					<th class='carrier'>失败原因</th>
				    <td class='carrier' colspan='3'><input name="carrier[reason_name]" type="text" style="width: 180px"
							value=""></td>
					<th class='required-title failed'>失败原因</th>
				    <td class='failed' colspan='3'><?php
						echo Q::control ( "dropdownbox", "failed_reason", array (
							"items" => array("承运商查验退运" =>'承运商查验退运',"海关查验退运" =>'海关查验退运',"安检退运" =>'安检退运',"其它不可抗力原因" =>'其它不可抗力原因'),
						    "value" => request('failed_reason'),
							"style" => "width:205px",
						    "empty" =>true,
						    "required"=>'required'
						) )?>
					</td>
				</tr>
				<tr class="package" >
				    <th colspan="10"><span style="float:left">阿里package信息：</span></th>
				</tr>
				<tr class="package">
				    <td colspan="8">
    				    <table class="FarTable">
    				        <thead>
    				            <tr>
        				            <th>数量</th>
        				            <th>长度</th>
        				            <th>宽度</th>
        				            <th>高度</th>
        				            <th>重量</th>
    				            </tr>
    				        </thead>
    				        <tbody>
    				            <?php foreach ($order->packages as $v):?>
    				            <tr>
    				                <td><?php echo $v->quantity?></td>
    				                <td><?php echo $v->length?></td>
    				                <td><?php echo $v->width?></td>
    				                <td><?php echo $v->height?></td>
    				                <td><?php echo $v->weight?></td>
    				            </tr>
    				            <?php endforeach;?>
    				        </tbody>
    				    </table>
				    </td>
				</tr>
				<tr class="package" >
				    <th colspan="10"><span style="float:left">添加Package信息：</span><a style="float:left" href="javascript:void(0)" onclick="addpackages()"><i class="icon-plus-sign">添加新package</i></a></th>
				</tr>
				<tr class="package">
				    <th>数量</th>
				    <td><input name="package[quantity_far][]" type="number" step="0.01" required="required" style="width: 190px"
							value=""></td>
				    <th>长度</th>
				    <td><input name="package[length_far][]" type="number" step="0.01" required="required" style="width: 140px"
							value=""></td>
				    <th>宽度</th>
				    <td><input name="package[width_far][]" type="number" step="0.01" required="required" style="width: 150px"
							value=""></td>
				    <th>高度</th>
				    <td><input name="package[height_far][]" type="number" step="0.01" required="required" style="width: 150px"
							value=""></td>
				    <th>重量</th>
				    <td><input name="package[weight_far][]" type="number" step="0.01" required="required" style="width: 100px"
							value=""></td>
				</tr>
				
				<tr class="package" >
				    <th>失败原因</th>
				    <td colspan='3'><input name="package[reason_name]" type="text" style="width: 410px"
							value=""></td>
				</tr>
				<tr class="fee">
				    <th colspan="10"><span style="float:left">添加核查信息：</span></th>
				</tr>
				<tr class="fee">
				    <th>费用名称</th>
				    <td colspan='9'>
    				    <table>
    				        <tr>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0001" checked='checked' />基础运费</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0001]" type="number" style="width:100px" value=""></td>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0035" checked='checked' />超尺寸/超重附加费</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0035]" type="number" style="width:100px" value=""></td>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0012" checked='checked' />代办服务费--一般贸易报关</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0012]" type="number" style="width:100px" value=""></td>
    				        </tr>
    				        <tr>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0019" checked='checked' />燃油附加费</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0019]" type="number" style="width:100px" value=""></td>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0020" checked='checked' />偏远地区附加费</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0020]" type="number" style="width:100px" value=""></td>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0002" />包装-包裹袋</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0002]" type="number" style="width:100px" value=""></td>
    				        </tr>
    				        <tr>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0003" />包装-纸箱</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0003]" type="number" style="width:100px" value=""></td>
    				            <td><input name="fee[fee_code][]" type="checkbox" value="EX0034" />异形包装费</td>
    				            <th>数量</th>
    				            <td><input name="fee[quantity][EX0034]" type="number" style="width:100px" value=""></td>
    				        </tr>
    				    </table>
				    </td>
				</tr>
				<tr class="fee">
				    <th>失败原因</th>
				    <td colspan='3'><input name="fee[reason_name]" type="text" style="width: 300px"
							value=""></td>
				</tr>
				<tr class="ship">
					<th>船期</th>
					<td><input class="easyui-datetimebox" name="sailling_date" type="text" style="width: 200px" value=""></td>
					<th>柜号</th>
					<td><input name="container_no" type="text" style="width: 200px" value=""></td>
					<th>提单号</th>
					<td><input name="bill_no" type="text" style="width: 200px" value=""></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if(Helper_ViewPermission::isAudit()):?>
	<div class="offset5 span2">
	   <button class="btn btn-small btn-success" id="search">
                                保存
       </button>
	</div>
	<?php endif;?>
	<h4 style="clear: both;"> 订单事件信息</h4>
	<table class="FarTable">
		<thead>
			<tr>
				<th style="width:160px;">事件时间</th>
				<th class="span2">事件代码</th>
				<th>成功时间</th>
				<th style="width:80px;">事件位置</th>
				<th style="width:80px;">时区号</th>
				<?php if($order->service_code =="OCEAN-FY"):?>
    				<th style="width:160px;">船期</th>
    				<th class="span2">柜号</th>
    				<th class="span2">提单号</th>
				<?php endif;?>
				<th>失败原因</th>
				<th style="">结果</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($list as $temp):?>
			<tr>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->event_time)?></td>
				<?php if($temp->event_code=='CHECK_WEIGHT' || $temp->event_code=='CONFIRM' || $temp->event_code=='CARRIER_PICKUP'):?>
				<td><a href="javascript:void(0)" onclick="showdetail(this)"><?php echo $temp->event_code?></a></td>
				<?php else :?>
				<td><?php echo $temp->event_code?></td>
				<?php endif;?>
				<td><?php echo Helper_Util::strDate('Y-m-d H:i:s', $temp->success_time)?></td>
				<td><?php echo $temp->event_location?></td>
				<td><?php echo $temp->timezone?></td>
				<?php if($order->service_code =="OCEAN-FY"):?>
					<td><?php echo $temp->sailling_date > 0 ? date('Y-m-d',$temp->sailling_date):'';?></td>
					<td><?php echo $temp->container_no?></td>
					<td><?php echo $temp->bill_no?></td>
				<?php endif;?>
				<td><?php echo $temp->reason?></td>
				<td>
					<?php if(Helper_ViewPermission::isAudit()):?>
					<?php if ($temp->confirm_flag =='3'):?>
						已成功推送派送失败事件，不再推送签收事件
					<?php endif;?>
					<?php if ($temp->confirm_flag !='1'):?>
					<a class="btn btn-mini btn-info" href="javascript:void(0)" onclick="eventdetail(this)" data="<?php echo $temp->event_id?>">
						<i class="icon-edit"></i>
						编辑
					</a>
					<a class="btn btn-mini btn-info" href="<?php echo url('/confirm',array('event_id'=>$temp->event_id,'code'=>'event'))?>">
						<i class="icon-ok"></i>
						确认
					</a>
					<?php endif;?>
					<?php endif;?>
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
<div id="event_detail" class="easyui-window" title="" data-options="modal:true,closed:true" style="width:1000px;height:200px;padding:10px;">
    <form method="post" action="<?php echo url('/eventdetail')?>">
        <table>
            <tr>
                <th class="required-title">事件时间</th>
    			<td>
    				<input class="easyui-datetimebox"
    				name="event_time"
    				id="event_time"
    				value=""
    				style="width: 150px"/>
    			</td>
    			<th class="required-title">事件位置</th>
    			<td>
    			    <?php
						echo Q::control ( "dropdownbox", "detail_event_location", array (
							"items" => array('杭州'=>'杭州','上海'=>'上海','义乌'=>'义乌','广州'=>'广州','南京'=>'南京','US'=>'US','青岛'=>'青岛','深圳'=>'深圳','Russian'=>'Russian'),
						    "value" => '',
							"style" => "width:150px",
						    "required"=>'required'
						) )?>
    			</td>
    			<th class="required-title">时区号</th>
    			<td>
    				<input  required="required" style="width: 80px;" type="text" maxlength="32" name="timezone" id="timezone" />
    			</td>
            </tr>
            <tr>
                <td id="event_info" colspan="6">
                </td>
            </tr>
        </table>
        <div class="FarTool text-center" style="margin-top:30px;margin-right:60px;">
    		<button class="btn btn-primary btn-small" type="submit">
    			<i class="icon-save"></i>
    			保存
    		</button>
        </div>
        <input type="hidden" name="event_id" value='' id="event_id">
    </form>
</div>
<?PHP $this->_endblock();?>
<script type="text/javascript">
	$(function(){
		var packages=$(".package");
		var fee=$(".fee");
		var carrier=$(".carrier");
		var failed=$(".failed");
		var ship = $('.ship');
		fee.detach();
		carrier.detach();
		packages.detach();
		failed.detach();
		ship.detach();
		$("#event_code").change(function(){
			if($(this).val()=='CHECK_WEIGHT'){
				$('#anchor').after(packages);
				fee.detach();
				carrier.detach();
				failed.detach();
			}else if($(this).val()=='CONFIRM'){
				$('#anchor').after(fee);
				packages.detach();
				carrier.detach();
				failed.detach();
			}else if($(this).val()=='CARRIER_PICKUP'){
				$('#anchor').append(carrier);
				packages.detach();
				fee.detach();
				failed.detach();
			}else if($(this).val()=='DELIVERY_TO_FLIGHT'){
				$('#anchor').append(failed);
				packages.detach();
				fee.detach();
			}else if($(this).val()=='WAREHOUSE_OPERATION'){
				$('#anchor').append(failed);
				packages.detach();
				fee.detach();
			}else if($(this).val()=='LOAD'){
				$('#anchor').after(ship);
				fee.detach();
				carrier.detach();
				packages.detach();
				failed.detach();
			}else if($(this).val()=='DELIVERY'){
				$('#anchor').append(carrier);
				packages.detach();
				fee.detach();
				failed.detach();
			}else{
				fee.detach();
				carrier.detach();
				packages.detach();
				failed.detach();
			}
			if($(this).val()!='LOAD'){
				ship.detach();
			}
		});
	})
	function showdetail(obj){
		var event_code=$(obj).html();
		$.ajax({
			url:'<?php echo url('order/orderinfo')?>',
			type:'POST',
			dataType:'json',
			data:{event_code:event_code,order_id:$("#order_id").val()},
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
	function addpackages(){
		$('.package').eq(-2).clone().insertAfter($('.package').eq(-2));
		if($(".package").length==6){
			$('.package').eq(-2).append(" <a class='btn btn-danger btn-mini' onclick='del(this)' href='javascript:void(0)'><i class='icon-remove'></i></a>");
		}
	}
	function editaddpackages(){
		$('.edit_packages').last().clone().insertAfter($('.edit_packages').last());
		if($(".edit_packages").length==2){
			$('.edit_packages').last().append(" <a class='btn btn-danger btn-mini' onclick='del(this)' href='javascript:void(0)'><i class='icon-remove'></i></a>");
		}
	}
	function del(obj){
		$(obj).parent().remove();	
	}
	function editpackagedel(obj){
		$(obj).parent().parent().remove();	
	}
	//编辑事件
	function eventdetail(obj){
		var event_id=$(obj).attr('data');
		$.ajax({
			url:'<?php echo url('/getevent')?>',
			data:{event_id:event_id},
			type:'post',
			dataType:'json',
			success:function(data){
				$('#event_detail').window({
				    width:'1000px',
				    height:'350px',
				});
				$('#event_detail').window('center');
				$('#event_info').children().remove();
				//写入基本事件信息
				var date=new Date(parseInt(data.event_time) * 1000);
				var year = date.getFullYear(),
				month = date.getMonth() + 1,
				day = date.getDate(),
				hour = date.getHours(),
				minute = date.getMinutes(),
				second = date.getSeconds();
				if(second<10){
					second='0'+second;
				}
				//写入窗口信息
				$('#event_time').datetimebox('setValue',year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second);//设值
				$('#detail_event_location').val(data.event_location);
				$('#timezone').val(data.timezone);
				$('#event_id').val(data.event_id);
				$('#event_detail').window({
				    title:data.event_code,
				});
				if(data.event_code=='CHECK_WEIGHT'){
					var table_str='<table><tr><th colspan="10"><span style="float:left">添加Package信息：</span><a style="float:left" href="javascript:void(0)" onclick="editaddpackages()"><i class="icon-plus-sign">添加新package</i></a></th></tr>';
					$.each(data.packages,function(key,value){
						table_str+='<tr class="edit_packages"><th>数量</th><td><input name="package[quantity_far][]" type="number" step="0.01" required="required" style="width: 150px"value="'+value.quantity+'"></td><th>长度</th><td><input name="package[length_far][]" type="number" step="0.01" required="required" style="width: 140px"value="'+value.length+'"></td>'+
						'<th>宽度</th><td><input name="package[width_far][]" type="number" step="0.01" required="required" style="width: 150px"value="'+value.width+'"></td><th>高度</th><td><input name="package[height_far][]" type="number" step="0.01" required="required" style="width: 150px"value="'+value.height+'"></td>'+
						'<th>重量</th><td><input name="package[weight_far][]" type="number" step="0.01" required="required" style="width: 130px"value="'+value.weight+'">';
						if(key>0){
							table_str+='<a class="btn btn-danger btn-mini" onclick="editpackagedel(this)" href="javascript:void(0)"><i class="icon-remove"></i></a></td></tr>'
						}else{
							table_str+='</td></tr>';
						}
					})
					table_str+='</table>';
					$("#event_info").append(table_str);
				}
				if(data.event_code=='CONFIRM'){
					var table_str='<table><tr><th colspan="10"><span style="float:left">添加核查信息：</span</th></tr>';
						table_str+='<tr><td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0001" />基础运费</td><th>数量</th><td><input name="fee[quantity][EX0001]" type="number" style="width:100px" value=""></td>'+
				            '<td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0035" />超尺寸/超重附加费</td><th>数量</th><td><input name="fee[quantity][EX0035]" type="number" style="width:100px" value=""></td>'+
				            '<td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0012" />代办服务费--一般贸易报关</td><th>数量</th><td><input name="fee[quantity][EX0012]" type="number" style="width:100px" value=""></td></tr>'+
				        	'<tr><td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0019" />燃油附加费</td><th>数量</th><td><input name="fee[quantity][EX0019]" type="number" style="width:100px" value=""></td>'+
				            '<td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0020" />偏远地区附加费</td><th>数量</th><td><input name="fee[quantity][EX0020]" type="number" style="width:100px" value=""></td>'+
				            '<td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0002" />包装-包裹袋</td><th>数量</th><td><input name="fee[quantity][EX0002]" type="number" style="width:100px" value=""></td></tr>'+
				        	'<tr><td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0003" />包装-纸箱</td><th>数量</th><td><input name="fee[quantity][EX0003]" type="number" style="width:100px" value=""></td>'+
				            '<td><input class="checkbox" name="fee[fee_code][]" type="checkbox" value="EX0034" />异形包装费</td><th>数量</th><td><input name="fee[quantity][EX0034]" type="number" style="width:100px" value=""></td></tr>';
					table_str+='</table>';
					$("#event_info").append(table_str);
					$('.checkbox').each(function(){
						var obj=$(this);
						$.each(data.fee,function(key,value){
								if(value.fee_item_code==obj.val()){
									obj.attr('checked','checked');
									obj.parent().next().next().children().val(value.quantity);
								}
						})
					});
				}
				$('#event_detail').window('open');
			}
		});
	}
</script>

