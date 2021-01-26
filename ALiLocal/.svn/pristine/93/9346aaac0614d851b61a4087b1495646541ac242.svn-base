<?PHP $this->_extends("_layouts/default_layout"); ?>
<?php $this->_block('title'); ?>产品编辑<?php $this->_endblock(); ?>
<?PHP $this->_block("contents");?>
<?php

echo Q::control ( "path", "", array (
	"path" => array (
		"产品管理" => "",
		"产品列表" => url ( "product/search" ),
		"产品编辑" => "" 
	) 
) )?>

<form method="post">
	<div class="tabs-container" >
    	<?php echo Q::control("tabs", "tabs_product", array ("tabs" => $tabs,"active_id" => "0"))?>
    	<div class="tabs-panels">
			<div style="padding: 10px;">
				<div>
					<div>
						<div class="FarSearch">
							<div class="span9">
								<table>
									<tbody>
										
										<tr>
											<th class="required-title" width="100">泛远产品代码</th>
											<td>
												<input id="text_product_name_far" type="text"
													name="product[product_name_far]"
													value="<?php echo $product->product_name_far?>"
													required="required" />
											</td>							
											<th class="required-title" width="100">泛远产品名称</th>
											<td>
												<input id="text_product_chinese_name_far" type="text"
													name="product[product_chinese_name_far]"
													value="<?php echo $product->product_chinese_name_far?>"
													required="required" />
											</td>
																						
										</tr>
										<tr>
											<th class="required-title" width="100">客户产品代码</th>
											<td>
												<input id="text_product_name" type="text"
													name="product[product_name]"
													value="<?php echo $product->product_name?>"
													required="required" />
											</td>										
											<th class="required-title" width="100">产品名称</th>
											<td>
												<input id="text_product_chinese_name" type="text"
													name="product[product_chinese_name]"
													value="<?php echo $product->product_chinese_name?>"
													required="required" />
											</td>
											
											
										</tr>
										<tr>
											<th class="required-title">网络</th>
											<td>
												<?php
												echo Q::control ( "dropdownbox", "dropdown_network", array (
													"name" => "product[network_id]",
													"items" => Helper_Array::toHashmap ( $networks, "network_id", "network_name" ),
													"value" => $product->network_id,
													"required" => "required",
													"style" => "width: 165px",
													"empty" => "true" 
												) )?>
											</td>
											<th class="required-title">计泡系数</th>
											<td>
											  <input type="number" name="product[ratio]" value="<?php echo $product->ratio?>"/>
										   </td>
										   <th class="required-title">产品类型</th>
											<td>
												<?php
												echo Q::control ( "dropdownbox", "type", array (
													"name" => "product[type]",
													"items" => Product::$type,
													"value" => $product->type,
													"required" => "required",
													"style" => "width: 165px",
													"empty" => "true" 
												) )?>
											</td>
											
										</tr>
										<tr>
											<th>毛利阈值</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[threshold]" value="<?php echo $product->threshold?$product->threshold:''?>"/>
	    									</td>
											<th>最长边限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[length]" value="<?php echo $product->length?$product->length:''?>"/>CM
	    									</td>
											<th>第二长边限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[width]" value="<?php echo $product->width?$product->width:''?>"/>CM
	    									</td>											
										</tr>
										<tr>
											<th>高限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[height]" value="<?php echo $product->height?$product->height:''?>"/>CM
	    									</td>
											<th>周长限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[perimeter]" value="<?php echo $product->perimeter?$product->perimeter:''?>"/>CM
	    									</td>
											<th>围长限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[girth]" value="<?php echo $product->girth?$product->girth:''?>"/>CM
	    									</td>											
	    									
										</tr>
										<tr>
											<th>整票计费重限制</th>
	    									<td>
	    										<input type="number" step="0.001" name="product[total_cost_weight]" value="<?php echo $product->total_cost_weight?$product->total_cost_weight:''?>"/>KG
	    									</td>
											<th>申报总价限制</th>
	    									<td>
	    										<input type="number" step="0.01" name="product[declare_threshold]" value="<?php echo $product->declare_threshold?$product->declare_threshold:''?>"/>USD
	    									</td>
	    									<th>单个包裹实重限制</th>
	    									<td>
	    										<input type="number" step="0.001" name="product[weight]" value="<?php echo $product->weight?$product->weight:''?>"/>KG
	    									</td>
	    									
										</tr>
										
										<tr>
											
	    									<th>是否支持报关</th>
						                	<td>
						                		<?php echo Q::control('RadioGroup','is_declaration',array(
						                		    'items'=>array(1=>'是',2=>'否'),
						                			"name" => "product[is_declaration]",
						                			'value'=>$product->is_declaration
						                		))?>
						
						                	</td>
						                	<th>是否支持一票多件</th>
						                	<td>
						                		<?php echo Q::control('RadioGroup','support_one',array(
						                		    'items'=>array(1=>'是',2=>'否'),
						                			"name" => "product[support_one]",
						                			'value'=>$product->support_one
						                		))?>
						
						                	</td>
						                	<th>是否检测FDA品类</th>
						                	<td>
						                		<?php echo Q::control('RadioGroup','is_pda',array(
						                		    'items'=>array(1=>'是',0=>'否'),
						                			"name" => "product[is_pda]",
						                			'value'=>$product->is_pda
						                		))?>
						
						                	</td>
										</tr>
										<tr>
											<th>验证数据完整性</th>
	    									<td>
	    										<?php
												echo Q::control ( "RadioGroup", "check_complete", array (
													"items"=>array(1=>'是',2=>'否',3=>'渠道验证'),
													"name" => "product[check_complete]",
													"value" => $product->check_complete,
												) )?>
	    									</td>
	    									<th></th>
	    									<td>    										
	    									</td>
	    									<th></th>
	    									<td>    										
	    									</td>
	    								</tr>
										<tr>
											<th>检查类型</th>
											<td colspan="5">
												<?php
												echo Q::control ( "check", "check_fuel", array (
													"name" => "product[check_fuel]",
													"value" => $product->check_fuel,
													"text" => "检查应收燃油" 
												) )?>
												&nbsp;&nbsp;
												<?php
												echo Q::control ( "check", "check_zip", array (
													"name" => "product[check_zip]",
													"value" => $product->check_zip,
													"text" => "检查无服务邮编、城市" 
												) )?>
												&nbsp;&nbsp;
												<?php
												echo Q::control ( "check", "check_has_battery", array (
													"name" => "product[check_has_battery]",
													"value" => $product->check_has_battery,
													"text" => "检查是否带电" 
												) )?>
												&nbsp;&nbsp;
											</td>
										</tr>
										<tr>
										   <th>入库要求</th>
										   <td colspan="3"><textarea name="product[remark]" rows="" cols="" style="width: 450px; height: 60px" ><?php echo $product->remark?></textarea></td>
										</tr>
										<tr>
										   <th>核查要求</th>
										   <td colspan="3"><textarea name="product[confirm_remark]" rows="" cols="" style="width: 450px; height: 60px" ><?php echo $product->confirm_remark?></textarea></td>
										</tr>
										<tr>
										   <th>打单要求</th>
										   <td colspan="3"><textarea name="product[label_remark]" rows="" cols="" style="width: 450px; height: 60px" ><?php echo $product->label_remark?></textarea></td>
										</tr>
										
									</tbody>
								</table>
								</div>
								<div class="span3">
								         <b> 可用部门</b>
									<label style="margin-left: 4px;"> 
										<input id="check_all" type="checkbox" style="margin-top:0px;"
											onclick="CheckAll(this);" />
										全选
									</label>
									<div class="easyui-panel" style="padding: 5px">
										<ul id="department_tree" class="easyui-tree"
											data-options="url:'<?php echo url('department/departmenttree',$department)?>',method:'get',checkbox:true,cascadeCheck:false"></ul>
									</div>
								</div>
							</div>
							<div style="float:left;width:100%;margin-bottom:8px">
								<a class="btn btn-samll btn-success" onclick="add()"
													href="javascript:void(0);">
													<i class="icon-plus"></i>
													新增产品-价格-分区-偏派体系
												</a>
							</div>
							<div>								
							<table class="FarTable" id="myTable" style="width:99%">
								<thead>
									<tr>
										<th class="required-title" width=160>公开价格表</th>
										<th class="required-title" width=150>分区表</th>
										<th class="required-title" width=120>偏派表</th>
										<th class="required-title" width=80>生效日期&nbsp;&gt;=</th>
										<th class="required-title" width=80>失效日期&nbsp;&lt;=</th>
									</tr>
								</thead>
								<tbody>
								    <?php if(strlen($product->product_id)):?>
									<?php $productpprs= Productppr::find("product_id=?",$product->product_id)->getAll()?>
									<?php if(count($productpprs)):?>
									<?php foreach ($productpprs as $productppr):?>
									<tr class="productppr">
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_price_manage", array (
												"items" => Helper_Array::toHashmap ( PriceManage::find ()->getAll (), "price_manage_id", "price_name" ),
												"value" => $productppr->price_manage_id,
												"required" => "required",
												"style" => "width: 190px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_partition_manage", array (
												"items" => Helper_Array::toHashmap ( PartitionManage::find ()->getAll (), "partition_manage_id", "partition_name" ),
												"value" => $productppr->partition_manage_id,
												"required" => "required",
												"style" => "width: 150px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_remote_manage", array (
												"items" => Helper_Array::toHashmap ( RemoteManage::find ()->getAll (), "remote_manage_id", "remote_name" ),
												"value" => $productppr->remote_manage_id,
												"required" => "required",
												"style" => "width: 120px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_effective_time_".$productppr->product_p_p_r_id, array (
												"value" => Helper_Util::strDate ( "Y-m-d", $productppr->effective_time ),
												"style" => "width: 80px",
												"required" => "required" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_invalid_time_".$productppr->product_p_p_r_id, array (
												"value" => Helper_Util::strDate ( "Y-m-d", $productppr->invalid_time ),
												"required" => "required" ,
											    "style"=>($productppr->invalid_time < Helper_Util::strDate("Y-m-d",time()))?"color:red;width: 80px":"width: 80px"
											) )?>
										</td>
									</tr>
									<?php endforeach;?>
									<?php endif;?>
									<?php endif;?>
									<tr class="productppr_hidden" style="display:none">
										<td><?php
											echo Q::control ( "dropdownbox", "dropdown_price_manage_0", array (
												"items" => Helper_Array::toHashmap ( PriceManage::find ()->getAll (), "price_manage_id", "price_name" ),
												"value" => '',
												"style" => "width: 190px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_partition_manage_0", array (
												"items" => Helper_Array::toHashmap ( PartitionManage::find ()->getAll (), "partition_manage_id", "partition_name" ),
												"value" => '',
												"style" => "width: 150px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "dropdownbox", "dropdown_remote_manage_0", array (
												"items" => Helper_Array::toHashmap ( RemoteManage::find ()->getAll (), "remote_manage_id", "remote_name" ),
												"value" => '',
												"style" => "width: 120px",
												"empty" => "true" 
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_effective_time_0", array (
												"value" => '',
												"style" => "width: 80px",
											) )?>
										</td>
										<td>
											<?php
											echo Q::control ( "datebox", "datebox_invalid_time_0", array (
												"value" => '',
												"style" => "width: 80px",
											) )?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="FarTool text-center">
							<a class="btn btn-inverse"
								href="<?php echo url('product/search')?>">
								<i class="icon-reply"></i>
								返回
							</a>
							<button type="submit" class="btn btn-primary"
								onclick="return Save();">
								<i class="icon-save"></i>
								保存
							</button>
						</div>
						<!----------------------------------------- 应收公式start ------------------------------------------------------>
						<div class="tabs-container" >
						<div class="FarTool">
							<a type="button" id="daochu" class="btn btn-small btn-primary" ><i class="icon-download"></i> 导出</a>
							<a class="btn btn-info btn-small" href="javascript:void(0)" onclick="fileimport();"><i class="icon-upload"></i> 导入 </a>
							<a type="button" href="<?php echo $_BASE_DIR?>public/download/应收公式操作费批量导入模板.xlsx" class="btn btn-small btn-primary" ><i class="icon-download"></i> 下载模板</a>
						</div>
							<?php echo Q::control("tabs", "customs_code", array ("tabs" => $tabs_customer,"active_id" => request("customs_code") == null ? "FARA00001" : request("customs_code")))?>
                			<?php echo Q::control("tabs", "channelcost_type", array ("tabs" => $tabs_type,"active_id" => request("type") == null ? "BOX" : request("type")))?>
                			<div class="tabs-panels">
                				<div class="panel-body panel-body-noheader panel-body-noborder"
                					style="padding: 10px;">
                					<table id="table_formula" class="FarTable">
                						<caption style="text-align:left;">
                							<strong style="margin-left:10px;margin-right:25px;">公式操作费</strong>
                							<strong> weight:计费重，icount:总件数，country:目的国二字码，over_count:超尺寸/超重件数，special_count:异形件数，packing_box:纸箱数量，packing_pak:包裹袋数量
                							net_weight:实重，girth:围长，first_length:最长边，second_length:第二长边，declaration_type:报关类型
                							</strong>
                						</caption>
                						<thead>
                							<tr>
                								<th width=140>费用名称</th>
                								<th >公式</th>
                								<th>备注</th>
                								<th width=30>自动</th>
                								<th width=100>生效日期&nbsp;&gt;=</th>
                								<th width=100>失效日期&nbsp;&lt;=</th>
                								<th width=100>币种</th>
                								<th width=100>操作</th>
                							</tr>
                						</thead>
                						<tbody>
                							<?php $package_type = request('type')?request('type'):'BOX'?>
                						    <?php foreach ($receivable_formulas as $temp):?>
                							<tr id="<?php echo $temp->receivable_formula_id?>">
                								<td><?php echo $temp->fee_name?>
                								<input type="hidden" value="<?php echo $temp->fee_name?>" />
                								</td>
                								<td  style="width:300px;word-break:break-all"><?php echo $temp->formula?></td>
                								<td><?php echo $temp->remark?></td>
                								<td><?php echo $temp->calculation_flag=="1"?"<i class='icon-ok'></i>":""?></td>
                								<td style="text-align: center"><?php echo Helper_Util::strDate("Y-m-d", $temp->effective_time)?></td>
                								<td style="text-align: center;<?php if ($temp->fail_time< time()):?> color:red;<?php endif;?>"><?php echo Helper_Util::strDate("Y-m-d", $temp->fail_time)?></td>
                								<td><?php echo $temp->currency_code?></td>
                								<td nowrap="nowrap">
                									<a class="btn btn-mini" href="javascript:void(0);"
                										onclick="EditRow([
                										{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Receivableformula::getFeename($package_type,$temp->receivable_formula_id,request("customs_code") == null ? "FARA00001" : request("customs_code"))));?>,'required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'text','required':'true'},
                										{'type':'checkbox'},
                										{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},
                										{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'},
                										{'type':'select','option':<?php echo str_replace("\"","'",json_encode(CodeCurrency::getCurrencyList()));?>}],this);">
                										<i class="icon-pencil"></i>
                										编辑
                									</a>
                									<a class="btn btn-mini btn-danger"
                										href="javascript:void(0);" onclick="DeleteRowFormula(this);">
                										<i class="icon-trash"></i>
                										删除
                									</a>
                								</td>
                							</tr>
                							<?php endforeach;?>
                							<tr>
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
                										onclick="NewRow([
                										{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Receivableformula::getFeename($package_type,null,request("customs_code") == null ? "FARA00001" : request("customs_code"))));?>,'required':'true'},
                										{'type':'text','required':'true'},{'type':'text','required':'true'},{'type':'checkbox'},
                										{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},
                										{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(CodeCurrency::getCurrencyList()));?>}],this);" >
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
                        <!----------------------------------------- 应收公式end ------------------------------------------------------>
						
						<h6 style="line-height:15px;margin-left:10px;">BAF（卖价燃油）</h6>
						<table class="FarTable" style="width:70%;">
							<thead>
								<tr>
									<th>费率</th>
									<th>生效日期</th>
									<th>失效日期</th>
									<th width=160>操作</th>
								</tr>
							</thead>
							<tbody>
						        <?php foreach($product->productfuel as $productfuel):?>
							    <tr id="<?php echo $productfuel->product_fuel_id?>">
									<td style="text-align: right;"><?php echo $productfuel->rate;?></td>
									<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$productfuel->effective_date);?></td>
									<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$productfuel->fail_date);?></td>
									<td>
										<a class="btn btn-mini" href="javascript:void(0);"
											onclick="EditRow([{'type':'number','precision':'4','min':'0','max':'1'},{'type':'date','required':'true'},{'type':'date','required':'true'}],this);">
											<i class="icon-pencil"></i>
											编辑
										</a>
										<a class="btn btn-mini btn-danger" href="javascript:void(0);"
											onclick="DeleteRow(this);">
											<i class="icon-trash"></i>
											删除
										</a>
									</td>
								</tr>
							    <?php endforeach;?>
							    <tr>
									<td></td>
									<td></td>
									<td></td>
									<td>
										<a class="btn btn-mini btn-success" href="javascript:void(0);"
											onclick="NewRow([{'type':'number','precision':'4','min':'0','max':'1'},{'type':'date','required':'true'},{'type':'date','required':'true'}],this);">
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
		</div>
	</div>

	<input id="hidden_product_p_p_r" type="hidden" name="productppr" />
	<input id="hidden_channelcost_formula" type="hidden" name="Channelcostformula" />
	<input id="customs_code" type="hidden" name="customs_code" value="<?php echo request('customs_code')?>" />
	<input type="hidden" name="department_hidden" id="department_hidden">
</form>
<form id="fromimport" action="<?php echo url('/formulaimport')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileimport">
    <input type="hidden"  name="id_import" id="id_import" value="<?php echo request('id')?>">
    <input type="hidden"  name="package_type_import" id="package_type_import" value="<?php echo request('type','BOX')?>">
</form>
<script type="text/javascript">
$(document).ready(function(){
	var is_scroll = $('#customs_code').val();
	console.log(is_scroll)
	if(is_scroll){
		document.documentElement.scrollTop = 500;
	}

	$("form").submit(function(){
		//可用部门
		var department = "";
		$($("#department_tree").tree("getChecked")).each(function(){
			department += $(this)[0].id+",";
		});
		$("#department_hidden").val(department.substring(0,department.length-1));
	});
})
//公式导出
$('#daochu').click(function(){
	window.location.href="<?php echo url('/formulaexport',array('id'=>request('id'),'customs_code'=>request ( "customs_code" ,'FARA00001')))?>"   
})
//公式导入
function fileimport(){
	$('#fileimport').click();
}
$("#fileimport").change(function(){
	$("#fromimport").submit();	
})
//新增产品-价格-偏派-分区表格
function add(){
	$(".productppr_hidden").show();
	$(".productppr_hidden").removeClass("productppr_hidden").addClass("productppr");
}

/**
 * 全选
 */
function CheckAll(obj){
	var check = obj.checked ? "check" : "uncheck";
	var roots = $("#department_tree").tree("getRoots");
	for(var i=0;i<roots.length;i++){
		var notes = $("#department_tree").tree("getChildren", roots[i]);
		for(var i=0;i<notes.length;i++){
			$("#department_tree").tree(check,notes[i].target);
		}
	}
}

	/**
	 * 保存 
	 */
	function Save(){
		var result = "";
		$.ajax({
			url:"<?php echo url('common/checkproduct')?>",
			type:"POST",
			data:{"old_name":"<?php echo $product->product_name?>"
				 ,"value_name":$("#text_product_name").val()},
			async : false,
			success:function(msg){
				result = msg;
			}
		});
		if(result != "true"){
			$.messager.alert('Error',result);
			return false;
		}
		//产品-价格-偏派-分区表格
		var json="";
		var product_id="<?php echo $product->product_id;?>";
		$(".productppr").each(function(){
			var price_manage_id=$(this).find('[name^=dropdown_price_manage]').val();
			var partition_manage_id=$(this).find('[name^=dropdown_partition_manage]').val();
			var remote_manage_id=$(this).find('[name^=dropdown_remote_manage]').val();
			var effective_time=$(this).find('[name^=datebox_effective_time]').val();
			var invalid_time=$(this).find('[name^=datebox_invalid_time]').val();
				json+='{"product_id":"'+product_id
				+'","price_manage_id":"'+price_manage_id
				+'","partition_manage_id":"'+partition_manage_id
				+'","remote_manage_id":"'+remote_manage_id
				+'","effective_time":"'+effective_time
				+'","invalid_time":"'+invalid_time+'"},';
		});
		json="["+json.substring(0,json.length-1)+"]";
		$("#hidden_product_p_p_r").val(json);
	}
	var tr_id=null;
	
	/**
	 * 回调 保存数据
	 */
	function CallBack(obj,name){
		//四个表格的数据
		if(name=="table_formula"){
			var flag = true;
			while(flag){
				var msg = Savechannelcost(obj,name);
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
					if(msg!=true){
						if(confirm(msg+"\n保存失败请确认数据是否正确,点击[确定]重新保存 或点击[取消]撤销本次操作.")){
							flag = true;
						}else{
							window.location.reload();
						}
					}else{
						flag = false;
						window.location.reload();
					}
				}
			}
		}else{
		
    		if(obj==null){
    			return false;
    		}
    		$.ajax({
    			url:"<?php echo url('product/productfuelsave')?>",
    			type:"POST",
    			data:{"product_id":"<?php echo request('id')?>",
    				"productfuel":{
    					"product_fuel_id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
    					"rate":$(obj).children().eq(0).text(),
    					"effective_date":$.trim($(obj).children().eq(1).text()),
    					"fail_date":$(obj).children().eq(2).text()}},
    			success:function(msg){
    				$(obj).attr("id",msg);
    			}
    		});
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
			url:"<?php echo url('product/saveoperate')?>",
			type:"POST",
			data:{
				"formula":$("#hidden_channelcost_formula").val(),
				"delete_flag":tr_id!=null?true:false},
			async : false,
			success:function(msg){
				result = msg;
			}
		});

		if(isNaN(result)){
			return result;
		}else{
			if(obj!=null){
				$(obj).attr("id",result);
			}
			return true;
		}
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
		var package_type="<?php echo request('type')?request('type'):'BOX'?>";
		var product_id = "<?php echo request('id')?>";
		var customs_code = "<?php echo request('customs_code')?:'FARA00001'?>";
		var json="";
		if(name=="table_formula"){
			var fee_name=$.trim($(obj).children().eq(0).text());
			var formula=$.trim($(obj).children().eq(1).text());
			var remark=$(obj).children().eq(2).text();
			var calculation_flag=$(obj).children().eq(3).children().length>0?"1":"0";
			var effective_time=$(obj).children().eq(4).text();
			var fail_time=$(obj).children().eq(5).text();
			var currency_code=$(obj).children().eq(6).text();
			json+='{"receivable_formula_id":"'+id
				+'","package_type":"'+package_type
				+'","product_id":"'+product_id
				+'","customs_code":"'+customs_code
				+'","fee_name":"'+fee_name
				+'","formula":"'+formula
				+'","remark":"'+remark
				+'","calculation_flag":"'+calculation_flag
				+'","effective_time":"'+effective_time
				+'","currency_code":"'+currency_code
				+'","fail_time":"'+fail_time+'"},';
			json="["+json.substring(0,json.length-1)+"]";
			$("#hidden_channelcost_formula").val(json);
		}
	}
		/**
		 * 回调 删除数据
		 */
		function DeleteBefore(obj){
			$.ajax({
				url:"<?php echo url('product/fueldelete')?>",
				type:"POST",
				data:{"product_fuel_id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
				success:function(msg){
				}
			});
		}
		function DeleteRowFormula(obj){
			if (confirm('确认要删除吗？')) {
				var tr = $(obj).parent().parent();
				var name = $(tr).parent().parent().attr("id");
				if (typeof (DeleteBefore) == "function") {
						tr_id=$(tr).attr("id")==undefined?"":$(tr).attr("id");
				}
				$(tr).remove();
				if (typeof (CallBack) == "function") {
					CallBack(null, name);
				}
				return true;
			} else {
				return false;
			}
		}
		 
</script>

<?PHP $this->_endblock();?>

