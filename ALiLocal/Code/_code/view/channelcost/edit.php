<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>渠道成本编辑<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php

echo Q::control ( "path", "", array (
	"path" => array (
		"产品管理" => "",
		"产品列表" => url ( "product/search" ),
		$product->product_name => "",
		"渠道成本" => url ( "channelcost/search", array (
			"id" => $product->product_id 
		) ),
		"渠道成本信息" => "" 
	) 
) )?>
<div class="container" style="width:100%">
	<form method="post">
		<div class="tabs-container">
			<?php echo Q::control("tabs", "tabs_product", array ("tabs" => $tabs,"active_id" => "4"))?>
			<div class="tabs-panels">
				<div class="panel-body panel-body-noheader panel-body-noborder"
					style="padding: 10px;">
					<div class="FarSearch">
						<table>
							<tbody>
								<tr>
									<th class="required-title">渠道</th>
									<td>
										<?php
										echo Q::control ( "dropdownbox", "dropdown_channel", array (
											"name" => "channelcost[channel_id]",
											"items" => Helper_Array::toHashmap ( $channel, "channel_id", "channel_name" ),
											"value" => $channelcost->channel_id,
											"style" => "width: 150px",
										    'required'=>'required'
										) )?>
									</td>
									<th class="required-title">计泡系数（渠道）</th>
									<td>
										<input type="number" name="channelcost[ratio]" value="<?php echo $channelcost->ratio?>" required='required'/>
									</td>
									<th>杂费率</th>
									<td>
										<input type="number" style="width:100px;"  max="1" step="0.01" name="channelcost[tax]" value="<?php echo $channelcost->tax?$channelcost->tax:'0'?>"/>
									</td>
									<th>燃油附加费折扣</th>
									<td>
										<input type="text" style="width:100px;"  max="1" step="0.01" name="channelcost[fuel_surcharge_dicount]" value="<?php echo $channelcost->fuel_surcharge_dicount?>"/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a class="btn btn-samll btn-success" onclick="addtr();"
											href="javascript:void(0);">
											<i class="icon-plus"></i>
											新增渠道成本-价格-分区-偏派体系
										</a>
									</td>
								</tr>
							</tbody>
						</table>
						
						<table class="FarTable" id="ChannelcostpprmyTable" style="margin-left:0px;">
							<thead>
								<tr>
									<th width="180px;">基础运费价格表 
									<?php
										echo Q::control ( "check", "check_fuel_surcharge_flag", array (
											"name" => "channelcost[fuel_surcharge_flag]",
											"value" => $channelcost->fuel_surcharge_flag,
											"text" => "需计算燃油附加费" 
										) )?>
									</th>
									<th width="180px;">分区表</th>
									<th width="180px;">偏派表</th>
									<th width="180px;">单件最低计费重（单位为KG）</th>
									<th width="120px;">生效日期&nbsp;&gt;=</th>
									<th width="120px;">失效日期&nbsp;&lt;=</th>
								</tr>
							</thead>
							<tbody>
								<?php $Channelcostpprs= Channelcostppr::find("channel_cost_id=?",$channelcost->channel_cost_id)->getAll()?>
								<?php if(count($Channelcostpprs)):?>
								<?php foreach ($Channelcostpprs as $Channelcostppr):?>
								<tr class="Channelcostppr">
									<td>
										<?php
										echo Q::control ( "dropdownbox", "dropdown_price_manage1", array (
											"items" => Helper_Array::toHashmap ( PriceManage::find ()->getAll (), "price_manage_id", "price_name" ),
											"value" => $Channelcostppr->price_manage_id,
											"style" => "width: 260px",
											"empty" => "true",
										    "required"=>'required'
										) )?>
									</td>
									<td>
										<?php
										echo Q::control ( "dropdownbox", "dropdown_partition_manage1", array (
											"items" => Helper_Array::toHashmap ( PartitionManage::find ()->getAll (), "partition_manage_id", "partition_name" ),
											"value" => $Channelcostppr->partition_manage_id,
											"style" => "width: 200px",
											"empty" => "true",
										    "required"=>'required'
										) )?>
									</td>
									<td>
										<?php
										echo Q::control ( "dropdownbox", "dropdown_remote_manage1", array (
											"items" => Helper_Array::toHashmap ( RemoteManage::find ()->getAll (), "remote_manage_id", "remote_name" ),
											"value" => $Channelcostppr->remote_manage_id,
											"style" => "width: 180px",
											"empty" => "true",
										    "required"=>'required'
										) )?>
									</td>
									<td>
										<input type="text" style="width:100px;"  max="1" step="0.01" name="single_lowest_weight1" value="<?php echo $Channelcostppr->single_lowest_weight?$Channelcostppr->single_lowest_weight:''?>"/>
									</td>
									<td>
										<?php
										echo Q::control ( "datebox", "datebox_effective_time1_".$Channelcostppr->channel_cost_p_p_r_id, array (
											"value" => Helper_Util::strDate ( "Y-m-d", $Channelcostppr->effective_time ),
											"style" => "width: 120px",
										    "required"=>'required'
										) )?>
									</td>
									<td>
										<?php
										echo Q::control ( "datebox", "datebox_invalid_time1_".$Channelcostppr->channel_cost_p_p_r_id, array (
											"value" => Helper_Util::strDate ( "Y-m-d", $Channelcostppr->invalid_time ),
										    "style"=>($Channelcostppr->invalid_time < Helper_Util::strDate("Y-m-d",time()))?"color:red;width: 120px":"width: 120px",
										    "required"=>'required'
										) )?>
									</td>
								</tr>
								<?php endforeach;?>
								<?php endif;?>
								<tr class="Channelcostppr_hidden" style="display:none">
										<td><?php
											echo Q::control ( "dropdownbox", "dropdown_price_manage1_0", array (
												"items" => Helper_Array::toHashmap ( PriceManage::find ()->getAll (), "price_manage_id", "price_name" ),
												"value" => '',
												"style" => "width: 260px",
												"empty" => "true",
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_partition_manage1_0", array (
												"items" => Helper_Array::toHashmap ( PartitionManage::find ()->getAll (), "partition_manage_id", "partition_name" ),
												"value" => '',
												"style" => "width: 200px",
												"empty" => "true",
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_remote_manage1_0", array (
												"items" => Helper_Array::toHashmap ( RemoteManage::find ()->getAll (), "remote_manage_id", "remote_name" ),
												"value" => '',
												"style" => "width: 180px",
												"empty" => "true",
											) )?>
										</td>
										<td>
											<input type="text" style="width:100px;"  max="1" step="0.01" name="single_lowest_weight1_0" value=""/>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_effective_time1_0", array (
												"value" => '',
												"style" => "width: 120px",
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_invalid_time1_0", array (
												"value" => '',
												"style" => "width: 120px",
											) )?>
										</td>
									</tr>
							</tbody>
						</table>
					</div>
					<div class="FarTool text-center">
						<a class="btn btn-inverse"
							href="<?php echo url('channelcost/search',array("id"=>$product->product_id))?>">
							<i class="icon-reply"></i>
							返回
						</a>
						<button type="submit" class="btn btn-primary" onclick=" return Save();">
							<i class="icon-save"></i>
							保存
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="tabs-container">
			<div class="FarTool">
				<a type="button" id="daochu" class="btn btn-small btn-primary" ><i class="icon-download"></i> 导出</a>
				<a class="btn btn-info btn-small" href="javascript:void(0)" onclick="fileimport();"><i class="icon-upload"></i> 导入 </a>
				<a type="button" href="<?php echo $_BASE_DIR?>public/download/公式操作费批量导入模板.xlsx" class="btn btn-small btn-primary" ><i class="icon-download"></i> 下载模板</a>
			</div>
			<?php echo Q::control("tabs", "customs_code", array ("tabs" => $tabs_customer,"active_id" => request("customs_code") == null ? "FARA00001" : request("customs_code")))?>
			<?php echo Q::control("tabs", "channelcost_type", array ("tabs" => $tabs_type,"active_id" => request("type") == null ? "BOX" : request("type")))?>
			
			<div class="tabs-panels">
				<div class="panel-body panel-body-noheader panel-body-noborder"
					style="padding: 10px;">
					<table id="table_formula" class="FarTable">
						<caption style="text-align:left;">
							<strong style="margin-left:10px;margin-right:25px;">公式操作费</strong>
							<strong>weight:计费重，freight:基础运费，icount:总件数，baf:燃油费率，tax:税率，country:目的国二字码，zone:分区号，over_count:超尺寸/超重件数，special_count:异形件数，remote:偏远附加费，cubic:总体积（E长宽高），net_weight:实重，girth:围长，first_length:最长边，second_length:第二长边，declaration_type:报关类型</strong>
						</caption>
						<thead>
							<tr>
								<th width=140>费用名称</th>
								<th>公式</th>
								<th>备注</th>
								<th width=30>自动</th>
								<th width=100>生效日期&nbsp;&gt;=</th>
								<th width=100>失效日期&nbsp;&lt;=</th>
								<th width=100>币种</th>
								<th width=100>供应商</th>
								<th width=100>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php if($channelCostType->type_id):?>
						    <?php foreach ($channelCostType->channelcostformula as $temp):?>
							<tr id="<?php echo $temp->channel_cost_formula_id?>">
								<td><?php echo $temp->fee_name?>
								<input type="hidden" value="<?php echo $temp->fee_name?>" />
								</td>
								<td style="width:300px;word-break:break-all"><?php echo $temp->formula?></td>
								<td><?php echo $temp->remark?></td>
								<td><?php echo $temp->calculation_flag=="1"?"<i class='icon-ok'></i>":""?></td>
								<td style="text-align: center"><?php echo Helper_Util::strDate("Y-m-d", $temp->effective_time)?></td>
								<td style="text-align: center;<?php if ($temp->fail_time< time()):?> color:red;<?php endif;?>"><?php echo Helper_Util::strDate("Y-m-d", $temp->fail_time)?></td>
								<td><?php echo $temp->currency_code?></td>
								<td><?php echo Supplier::find('supplier_id = ?',$temp->supplier_id)->getOne()->supplier?></td>
								<td nowrap="nowrap">
									<a class="btn btn-mini" href="javascript:void(0);"
										onclick="EditRow([{'type':'select','option':<?php echo str_replace("\"","'",json_encode(ChannelCost::getFeename($channelCostType->type_id,$temp->channel_cost_formula_id,request('customs_code','FARA00001'))));?>,'required':'true'},{'type':'text','required':'true'},{'type':'text','required':'true'},{'type':'checkbox'},{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(CodeCurrency::getCurrencyList()));?>},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Fee::getSupplierList()));?>}],this);">
										<i class="icon-pencil"></i>
										编辑
									</a>
									<a class="btn btn-mini btn-danger"
										href="javascript:void(0);" onclick="DeleteRow(this);">
										<i class="icon-trash"></i>
										删除
									</a>
								</td>
							</tr>
							<?php endforeach;?>
							<?php endif;?>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>
									<a class="btn btn-mini btn-success"
										href="javascript:void(0);"
										onclick="NewRow([{'type':'select','option':<?php echo str_replace("\"","'",json_encode(ChannelCost::getFeename($channelCostType->type_id,NULL,request('customs_code','FARA00001'))));?>,'required':'true'},{'type':'text','required':'true'},{'type':'text','required':'true'},{'type':'checkbox'},{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(CodeCurrency::getCurrencyList()));?>},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Fee::getSupplierList()));?>}],this);">
										<i class="icon-plus"></i>
										新建
									</a>
								</td>
							</tr>
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
		<input id="hidden_channelcost_p_r_p" type="hidden" name="Channelcostppr" />
		<input id="hidden_channelcost_formula" type="hidden" name="Channelcostformula" />
		<input id="customs_code" type="hidden" name="customs_code" value="<?php echo request('customs_code','FARA00001')?>" />
		<input id="package_type" type="hidden" name="package_type" value="<?php echo request('type','BOX')?>" />
	</form>
	<div>
	<table class="FarTable">
	<caption>费用试算</caption>
	<tr>
		<th style="width:80px;">阿里订单号</th>
		<td>
			<input name="ali_order_no" id="ali_order_no" type="text" style="margin-right: 10px;"><input type="button" value="验算" class="btn btn-small" onclick="cal_result()">
		</td>
	</tr>
	<tr>
		<td id="cal_result" colspan="2">
			
		</td>
	</tr>
	</table>
	</div>
	<form id="fromimport" action="<?php echo url('/import')?>" method="post" enctype="multipart/form-data" style="display:none">
	    <input type="file"  name="file" id="fileimport">
	    <input type="hidden"  name="id_import" id="id_import" value="<?php echo request('id')?>">
	    <input type="hidden"  name="channel_id_import" id="channel_id_import" value="<?php echo request('channel_id')?>">
	    <input type="hidden"  name="package_type_import" id="package_type_import" value="<?php echo request('type','BOX')?>">
	</form>
</div>
	<script type="text/javascript">
	$('#daochu').click(function(){
		window.location.href="<?php echo url('/export',array('id'=>request('id'),'channel_id'=>request('channel_id'),'customs_code'=>request ( "customs_code" ,'FARA00001'),'type'=>request('type','BOX')))?>"   
	})
	function fileimport(){
		$('#fileimport').click();
	}
	$("#fileimport").change(function(){
		$("#fromimport").submit();	
	})
	/**
	验算
	**/
	function cal_result(){
		$('#cal_result').load("<?php echo url_standard('/calResult',array('product_id'=>request('id'),'channel_id'=>request('channel_id')))?>&ali_order_no="+$.trim($('#ali_order_no').val()));
	}
	/**
	 * 保存
	 */
	function Save(){
		//渠道成本-价格-偏派-分区表格
		var json="";
		var channel_cost_id="<?php echo $channelcost->channel_cost_id;?>";
		var product_id="<?php echo $channelcost->product_id;?>";
		var channel_id="<?php echo $channelcost->channel_id;?>";
		var flag=false;
		if($(".Channelcostppr").length>0){
			$(".Channelcostppr").each(function(){
				var price_manage_id=$(this).find('[name^=dropdown_price_manage1]').val();
				var partition_manage_id=$(this).find('[name^=dropdown_partition_manage1]').val();
				var single_lowest_weight=$(this).find('[name^=single_lowest_weight1]').val();
				
				var remote_manage_id=$(this).find('[name^=dropdown_remote_manage1]').val();
				var effective_time=$(this).find('[name^=datebox_effective_time]').val();
				var invalid_time=$(this).find('[name^=datebox_invalid_time]').val();
					json+='{"channel_cost_id":"'+channel_cost_id
					+'","product_id":"'+product_id
					+'","channel_id":"'+channel_id
					+'","price_manage_id":"'+price_manage_id
					+'","partition_manage_id":"'+partition_manage_id
					+'","single_lowest_weight":"'+single_lowest_weight
					+'","remote_manage_id":"'+remote_manage_id
					+'","effective_time":"'+effective_time
					+'","invalid_time":"'+invalid_time+'"},';
					if(price_manage_id=='' || partition_manage_id=='' || remote_manage_id=='' || effective_time=='' || invalid_time==''){
						flag=true;
					}
			});
			json="["+json.substring(0,json.length-1)+"]";
			$("#hidden_channelcost_p_r_p").val(json);
		}else{
			$.messager.alert('', '请先完善价格-偏派-分区体系信息');
			return false;
		}
		if(flag){
			$.messager.alert('', '请先完善价格-偏派-分区体系信息');
			return false;
		}
	}
	//新增渠道成本-价格-偏派-分区表格
	function addtr(){
		$(".Channelcostppr_hidden").show();
		$(".Channelcostppr_hidden").removeClass("Channelcostppr_hidden").addClass("Channelcostppr");
	}
	var tr_id=null;
	/**
	 * 删除
	 */
	function DeleteBefore(obj){
		tr_id=$(obj).attr("id")==undefined?"":$(obj).attr("id");
	}
	/**
	 * 回调
	 */
	function CallBack(obj,name){
		MessagerProgress("载入");
		//四个表格的数据
		if(name=="table_formula"){
			var flag = true;
			while(flag){
				var msg = Savechannelcost(obj,name);
				if(msg!=true){
					console.log(msg)
					if(msg=="timeerror"){
						alert('生效日期应小于失效日期!');
						flag = false;
						window.location.reload();
					}else if(msg=="formularepeat"){
						alert('费用项有效期内重复!');
						flag = false;
						window.location.reload();
					}else{
						if(confirm(msg+"\n保存失败请确认数据是否正确,点击[确定]重新保存 或点击[取消]撤销本次操作.")){
							flag = true;
						}else{
							window.location.reload();
						}
					}
				}else{
					flag = false;
					window.location.reload();
				}
			}
		}
		MessagerProgress("close");
	}
	/**
	 * 渠道操作费 JSON数据
	 */
	function GetchannelcostJSON(obj,name){
		var id = "";
		if(tr_id!=null){
			id=tr_id;
		}else{
			id=$(obj).attr("id")==undefined?"":$(obj).attr("id");
		}
		var type_id="<?php echo $channelCostType->type_id?>";
		
		var json="";
		var channel_cost_id="<?php echo $channelcost->channel_cost_id;?>";
		var package_type = $('#package_type').val();
		var customs_code = $('#customs_code').val();
		if(name=="table_formula"){
			var fee_name=$.trim($(obj).children().eq(0).text());
			var formula=$.trim($(obj).children().eq(1).text());
			var remark=$(obj).children().eq(2).text();
			var calculation_flag=$(obj).children().eq(3).children().length>0?"1":"0";
			var effective_time=$(obj).children().eq(4).text();
			var fail_time=$(obj).children().eq(5).text();
			var currency_code=$(obj).children().eq(6).text();
			var supplier=$(obj).children().eq(7).text();
			json+='{"channel_cost_formula_id":"'+id
				+'","channel_cost_id":"'+channel_cost_id
				+'","type_id":"'+type_id
				+'","package_type":"'+package_type
				+'","fee_name":"'+fee_name
				+'","customs_code":"'+customs_code
				+'","formula":"'+formula
				+'","remark":"'+remark
				+'","calculation_flag":"'+calculation_flag
				+'","effective_time":"'+effective_time
				+'","currency_code":"'+currency_code
				+'","supplier":"'+supplier
				+'","fail_time":"'+fail_time+'"},';
			json="["+json.substring(0,json.length-1)+"]";
			$("#hidden_channelcost_formula").val(json);
		}
	}	
	/**
	 * 保存渠道操作费信息
	 */
	function Savechannelcost(obj,name){
		var result="";
		
		// 渠道折扣 JSON数据
		GetchannelcostJSON(obj,name);
		//提交数据
		$.ajax({
			url:"<?php echo url('channelcost/saveoperate')?>",
			type:"POST",
			data:{
				"formula":$("#hidden_channelcost_formula").val(),
				"delete_flag":tr_id!=null?true:false},
			async : false,
			success:function(msg){
				result = msg;
			}
		});
		document.documentElement.scrollTop = 500;
		if(isNaN(result)){
			return result;
		}else{
			if(obj!=null){
				$(obj).attr("id",result);
			}
			return true;
		}
	}
</script>

<?PHP $this->_endblock();?>

