<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    问题件列表
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<style>
.font-red, .font-red a{
	color:red;
}
</style>
<div>
</div>
<form method="POST" id="issue_list">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
						创建日期从：
					</th>
					<td>
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date" ),
							"style"=>"width:80px"
						) )?>
					</td>
					<th>到</th>
					<td>
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date"),
							"style"=>"width:80px"
						) )?>
					</td>
					<th>(阿里/末端)单号</th>
					<td>
						<textarea name="ali_order_no" placeholder="每行一个单号" style="width: 110px "><?php echo request('ali_order_no')?></textarea>
					</td>
					<th>问题件状态</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'parcel_flag', array (
							'items' => array (
								'1' => '开启',
								'2' => '关闭',
							    '3' => '延置处理'
							),
							'style'=>'width:78px',
							'empty'=>true,
							'value' => request ( 'parcel_flag' ,1) 
						) )?>
					</td>
					<th>网络</th>
					<td>
					   <?php
						echo Q::control ( 'dropdownbox', 'network', array('items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_code'),
							'empty'=>true,
							'style'=>'width:70px',
							'value' => request ( 'network') 
						) )?>
					</td>
					<?php if(request('parameters')=='warehouse_issue' || request('parameters')=='own_issue' || request('parameters')=='beforearrive_issue'):?>
					 <th>仓库</th>
                     <td><?php
                            echo Q::control ( 'dropdownbox', 'department_id', array (
                            'items'=>Helper_Array::toHashmap(Department::departmentlist(),'department_id','department_name'),
                            'empty'=>true,
                            'style'=>'width:70px',
                            'value' => request('department_id'),
                            ) )?>
                      </td>
					<?php endif;?>
					<td>
					   <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <?php if(Helper_ViewPermission::isAudit()):?>
		               <a class="btn btn-success btn-small" href="<?php echo url('/newissueparcel')?>" target='_blank'>
			             <i class="icon-plus"></i>
			                                         新建
		               </a>
		               <?php endif;?>
		               <button class="btn btn-warning btn-small" name="export" value="export">
			             <i class="icon-download"></i>
			                                         导出
		               </button>
		               <?php if(Helper_ViewPermission::isAudit()):?>
		               <a class="btn btn-info btn-small"href="<?php echo url('/issuebatchclose')?>">
		               		<i class="icon-upload"></i> 批量关闭
		               </a> 
		               <?php endif;?>
		               
		               <a id='confirmgj' class="btn btn-success btn-small" href="javascript:void(0);" onclick="confirmgj()">批量跟进 </a>
					</td>
				</tr>
				<tr>
				  <th>发件人信息</th>
				  <td colspan="3">
                    <input style="width: 200px" type="text" id="sender" name="sender"  value="<?php echo  request('sender')?>">
                  </td>
                  <th>国内快递单号</th>
				  <td>
	                    <input type="text" style="width: 110px " id="reference_no" name="reference_no"  value="<?php echo  request('reference_no')?>">
	               </td>
                  <?php if(request('parameters')=='channel_issue'):?>
					<th>渠道异常</th>
					<td>
					    <input class="easyui-combotree" name="headlinetype[]" data-options="url:'<?php echo url('order/headlinetree',array('checked'=>implode(',',request('headlinetype',array()))))?>'
								, method:'get', multiple:true,width:'180px'" />
					</td>
					<?php endif;?>
					<?php if (request('parameters')=='beforearrive_issue'):?>
					<th>产品</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'service_code', array (
							'items' => Helper_Array::toHashmap(Product::find()->getAll(), 'product_name','product_chinese_name'),
							'empty'=>true,
							'style'=>'width:78px',
							'value' => request ( 'service_code'),
						) )?>
					</td>
					<th>订单状态</th>
					<td>
						<?php
						echo Q::control ( 'dropdownbox', 'order_status', array (
							'items' => Order::$status,
							'empty'=>true,
							'style'=>'width:70px',
							'value' => request ( 'order_status') 
						) )?>
					</td>
					<?php endif;?>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="dialog_save" class="easyui-dialog hide"title="关联渠道异常件"
		data-options="closed:true, modal:true"
		style="width:450px; height: 150px;">
		<div class="span4">
        <table class="FarTable">
        	  <tr>
                    <th class="required-title">渠道异常类型</th>
					<td><input class="easyui-combotree" id="headtype" name="headtype[]" data-options="url:'<?php echo url('order/headlinetree',array('checked'=>implode(',',request('headtype',array()))))?>'
								, method:'get', multiple:true,width:'250px'" /></td>
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
    
    <div id="dialog_plgj" class="easyui-dialog hide"title="批量跟进异常件"
		data-options="closed:true, modal:true"
		style="width:700px; height: 200px;">
		<div class="span7">
        <table class="FarTable">
        	  <tr>
                    <th class="required-title" width="60px">跟进内容</th>
					<td colspan="3" style="border-left: 2px #eee">
						<textarea style="width: 570px" rows="4" name="follow_up_content" id="follow_up_content"></textarea>
               		</td>
              </tr>
        </table>
        <table style="width:100%;">
        <tr>
		    <td align="center">
		      <button class="btn btn-primary" type="submit" onclick="savegj()">
					保存
				</button>
			</td>
		</tr>
		</table>		
        </div>
    </div>
    
	<div id="issue" class="tabs-container " style="min-width: 1148px;">
		<?php
		echo Q::control ( "tabs", "description", array (
			"tabs" => $tabs,"active_id" => $active_id 
		) );
		?>
		<div class="tabs-panels">
			<div class="panel-body panel-body-noheader panel-body-noborder"
				style="padding: 10px;">
            	<table class="FarTable" id="list">
            		<thead>
            			<tr>
            			    <th><input type="checkbox" onchange="selectall()"></th>
            				<th style="width:30px;">No</th>
            				<th style="width:90px">问题件编号</th>
            				<th style="width:100px;">阿里单号</th>
            				<th style="width:60px;">订单状态</th>
            				<?php if($active_id == '4'):?>
            				<th style="width:150px">国内快递单号</th>
            				<?php else:?>
            				<th style="width:150px">末端运单号 &nbsp; 
            				<a href="#13" class=" copy" data-clipboard-target="#tns"><i class="icon icon-copy"></i></a>&nbsp;
            				<?php if(request('network') && request('network') != 'FEDEX' && request('network') !="YWML" && request('network') !="DHLE"):?><a href="javascript:trace()" ><i class="icon icon-link"></i></a><?php endif;?>
            				</th>
            				<?php endif;?>
            				<th style="width:60px">问题类型</th>
            				<th >状态</th>
            				<?php if(request('parameters')=='channel_issue'):?>
            				<th style="width:130px">异常</th>
            				<?php endif;?>
            				<th style="width:130px">位置</th>
            				<th style="width:60px">发起人</th>
            				<th style="width:90px">发起时间</th>
            				<?php if(request('parameters')=='channel_issue'):?>
            				<th style="width:90px">截止时间</th>
            				<?php endif;?>
            				<th style="width:70px;">最后跟进</th>
            				<th style="">最后备注</th>
            			</tr>
            		</thead>
            		<tbody>
            		<?php $i=1;$status=array('1'=>'取件','2'=>'库内','3'=>'渠道','4'=>'无主件','5'=>'港前');$tns=array();?>
            		<?php foreach ($parcels as $parcel):$tns[]=$parcel->order->tracking_no;?>
            			<?php $history=Abnormalparcelhistory::find("abnormal_parcel_id=?",$parcel->abnormal_parcel_id)->order("create_time desc")->getOne()?>
            			<?php 
            			$now = strtotime(date('Y-m-d'));
            			$fiveday  = $now+4*24*3600;
            			?>
            			<tr id="tr_<?php echo $parcel->abnormal_parcel_id?>" <?php if ($parcel->deadline>=$now&&$parcel->deadline<=$fiveday&&request('parameters')=='channel_issue'){echo 'class="font-red"';}?>>           			 
            			    <td><input type="checkbox" class="ids" name="ids[]" value="<?php echo $parcel->abnormal_parcel_id?>"></td>
            				<td><?php echo $i++ ?></td>
            				<td><a  target="_blank"
            					    href="<?php echo url('order/issuehistory', array('abnormal_parcel_id' => $parcel->abnormal_parcel_id))?>">
            					    <?php echo $parcel->abnormal_parcel_no ?>
            					</a>
            				</td>
            				<td><a  target="_blank"
            					    href="<?php echo url('order/detail', array('order_id' => $parcel->order->order_id))?>">
            					    <?php echo $parcel->ali_order_no?$parcel->ali_order_no:'' ?>
            					</a>
            				</td>
            				<td>
            					<?php echo $parcel->order->order_status?Order::$status[$parcel->order->order_status]:''?>
            				</td>
            				<?php if ($active_id == '4'):?>
            				<td>
            				    <?php echo $parcel->reference_no?>
            				</td>
            				<?php else:?>
            				<td>
            				    <?php if($parcel->order->service_code == 'EMS-FY'):?>
            				    <a target="_blank" href="https://www.trackingmore.com/china-ems-tracking/cn.html?number=<?php echo $parcel->order->tracking_no?>">
            				    <?php elseif($parcel->order->service_code == 'Express_Standard_Global' || $parcel->order->service_code == 'US-FY'):?>
            					<a target="_blank" href="https://www.ups.com/track?loc=en_US&tracknum=<?php echo $parcel->order->tracking_no?>&requester=WT/trackdetails">
	            				<?php elseif($parcel->order->service_code == 'WIG-FY' || $parcel->order->channel->trace_network_code=="FEDEX"):?>
            					<a target="_blank" href="https://www.trackingmore.com/fedex-tracking/cn.html?number=<?php echo $parcel->order->tracking_no?>">
	            				<?php elseif($parcel->order->channel->trace_network_code=="DHL") :?>
                            	<a target="_blank" href="https://www.dhl.com/en/express/tracking.html?AWB=<?php echo $parcel->order->tracking_no?>&brand=DHL">
                            	<?php elseif($parcel->order->channel->trace_network_code=="DHLE") :?>
                            	<a target="_blank" href="https://ecommerceportal.dhl.com/track/?locale=en">
	            				<?php endif;?>
	            				<?php echo $parcel->order->tracking_no?>
	            				</a>
            				</td>
            				<?php endif;?>
            				<td ><?php echo $status[$parcel->issue_type]?></td>
            				<td style="width:30px;"><?php echo $parcel->parcel_flag=='1'?"开启":($parcel->parcel_flag=='2'?"关闭":"延置处理")?></td>
            				<?php if(request('parameters')=='channel_issue'):?>
            				<td><?php $line=''; $head=abnormalparcelheadline::find('abnormal_parcel_id =?',$parcel->abnormal_parcel_id)->getAll(); foreach ($head as $h){
            				    $ab=headline::find('headline_id =?',$h->headline_id)->getOne();
            				    if(!$ab->isNewRecord()){
            				        $line .= ','.$ab->headline;
            				    }
            				}  ?><?php echo trim($line,',')?></td>
            				<?php endif;?>
            				<td>
            				<?php if($parcel->issue_type=='1' || $parcel->issue_type=='2' || $parcel->issue_type=='5'):?>
            				<?php echo Department::find('department_id=?',$parcel->order->department_id)->getOne()->department_name?>
            				<?php elseif ($parcel->issue_type=='3'):?>
            				<?php echo $parcel->order->channel->channel_name?>
            				<?php elseif ($parcel->issue_type=='4'):?>
            				<?php echo Department::find('department_id=?',$parcel->location)->getOne()->department_name?>
            				<?php endif;?>
            				</td>
            				<td><?php echo $parcel->abnormal_parcel_operator?></td>
            				<td align="center"><?php echo Helper_Util::strDate('m-d H:i', $parcel->create_time)?></td>
            				<?php if(request('parameters')=='channel_issue'):?>
            				<td>
            				<?php echo Helper_Util::strDate('Y-m-d', $parcel->deadline)?>
            				</td>
            				<?php endif;?>
            				<td><?php echo $history->follow_up_operator?></td>
            				<?php $str = '['.date('m-d H:i',$history->create_time).'] '. $history->follow_up_content;  
            						mb_internal_encoding ( "UTF-8" );
									$str_length = mb_strwidth ( $str );?>
							<?php if($str_length > 140):?>
            				<td>
            					<?php echo mb_substr ( $str, 0, 70 ).' '?><a class="btn btn-small btn-primary" href="javascript:void(0);" id='<?php echo  $parcel->abnormal_parcel_id?>' onclick="remark(this)">...</a>
            					<input id="hidden_<?php echo $parcel->abnormal_parcel_id?>" type="hidden" value="<?php echo '['.date('m-d H:i',$history->create_time).'] '. $history->follow_up_content?>"/>
            				</td>
            				<?php else :?>
            				<td>
            					<?php echo '['.date('m-d H:i',$history->create_time).'] '. $history->follow_up_content?>
            				</td>
            				<?php endif;?>
            			</tr>
            		<?php endforeach;?>
            		</tbody>
            	</table>
            	<?php if(Helper_ViewPermission::isAudit()):?>
            	<?php if(request('parameters')=='channel_issue'):?>
            	<a id='confirm' class="btn btn-success" href="javascript:void(0);" onclick="confirm()">关联 </a>
            	<?php endif;?>
            	<?php endif;?>
            	<textarea rows="" cols="" id="tns" style="width:1px;height:1px;"><?php echo implode("\n",$tns)?></textarea>
            </div>
		</div>
	</div>
	<input id="parameters" type="hidden" name="parameters" value="<?php echo $parameters?>">
</form>
	<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>

<script type="text/javascript">
	
	function remark(obj){
		var id=$(obj).attr("id");
		var text = $("#hidden_"+id).val();
		var new_id = 'new'+id;
		var parameters = $("#parameters").val();
		if(parameters=='channel_issue'){
			var num = 15;
		}else{
			var num = 14;
		}
		if($('#'+new_id).text().length==0){
			$('#tr_'+id).after("<tr id='tr_"+new_id+"'><td id='"+new_id+"' colspan='"+num+"'>"+text+"</td></tr>");
		}else{
			$('#tr_'+new_id).remove();
		}
	}
	/**
	 *  点击tabs设置隐藏框值 
	 */	 
	function TabSwitch(code){
		$("#parameters").val(code);
		$("#issue_list").trigger("submit");
	}
	function trace(){
		var network_code="<?php echo request('network')?>";
		if(network_code=="EMS"){
			window.open("https://t.17track.net/en#nums="+$('#tns').val().replace(/\n/g,","));
		}else if(network_code=="UPS" || network_code=="US-FY"){
			window.open("https://www.ups.com/track?loc=en_US&tracknum="+$('#tns').val().replace(/\n/g,"%250D%250A")+"&requester=WT/trackdetails");
		}
	}

	function selectall(){
		$(".ids").each(function(){
			$(this).prop('checked',!$(this).prop('checked'))
		});
	}

	function confirm(){
		var height=$('#issue').height()*0.93;
// 		alert(height);
		if($(".ids:checked").length>0){
			$('#dialog_save').dialog('open');
			$('.window-shadow').css('top',height);
			$('.panel').css('top',height);
			$('#dialog_save').removeClass('hide');
		}else{
			alert("请选择订单");
			return false;
		}
	}

	function save(){
		var headtype = $('#headtype').combotree('getValues');
		if(headtype==''){
		   $.messager.alert('', '渠道异常类型不能为空');
		   return false;
		}
		var dropIds = new Array();  
		$(".ids").each(function(){
			if($(this).prop('checked')){
				dropIds.push($(this).val());  
			}
		});
		$.ajax({
			url:'<?php echo url('order/saveheadline')?>',
			data:{abnormal_parcel_ids:dropIds,headtype:headtype},
			type:'post',
			async:false,
			success:function(data){
				   alert('关联成功');
				   setTimeout(function (){
					  window.location.reload();
				   },1000);
 			}
		});
	}

	function confirmgj(){
		var height=$('#issue').height()*0.3;
// 		alert(height);
		if($(".ids:checked").length>0){
			$('#dialog_plgj').dialog('open');
			$('.window-shadow').css('top',height);
			$('.panel').css('top',height);
			$('#dialog_plgj').removeClass('hide');
		}else{
			alert("请选择订单");
			return false;
		}
	}
	
	function savegj(){
		var follow_up_content = $('#follow_up_content').val();
		if(follow_up_content==''){
		   $.messager.alert('', '批量跟进内容不能为空');
		   return false;
		}
		var dropIds = new Array();  
		$(".ids").each(function(){
			if($(this).prop('checked')){
				dropIds.push($(this).val());  
			}
		});
		$.ajax({
			url:'<?php echo url('order/savemanyhistory')?>',
			data:{abnormal_parcel_ids:dropIds,follow_up_content:follow_up_content},
			type:'post',
			async:false,
			success:function(data){
				 $.messager.alert('', '保存成功');
				 $('#dialog_plgj').panel("close");
				   setTimeout(function (){
					  window.location.reload();
				   },1000);
 			}
		});
	}
</script>
<?PHP $this->_endblock();?>

