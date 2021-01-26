<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>网络编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php

echo Q::control ( 'path', '', array (
	'path' => array (
		'产品管理' => '',
		'网络列表' => url ( 'network/search' ),
		'网络编辑' => ''
	) 
) )?>
<form method="POST">
	<div class="FarSearch">
		<table style="width:60%;">
			<tbody>
				<tr>
					<th width=150 class="required-title">网络代码</th>
					<td>
					    <?php if(request('id')):?>
						<input id="text_network_code" readonly="readonly" required="required"
							name="network[network_code]" type="text"
							value="<?php echo $network->network_code?>"
							style="margin-left: 5px;" />
						<?php else :?>
						<input id="text_network_code" required="required"
							name="network[network_code]" type="text"
							value="<?php echo $network->network_code?>"
							style="margin-left: 5px;" />
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<th width=150 class="required-title">网络名称</th>
					<td>
						<input required="required" name="network[network_name]"
							type="text" value="<?php echo $network->network_name?>"
							style="margin-left: 5px;" />
					</td>
				</tr>
				<tr>
					<th width=150 class="required-title">查询轨迹网址</th>
					<td>
						<input required="required" name="network[trace_url]" style="width:300px"
							type="text" value="<?php echo $network->trace_url?>"
							style="margin-left: 5px;" />
					</td>
				</tr>
				<?php if(request('id')):?>
				<tr>
					<th>BAF</th>
					<td>
						<table id="table_networkfuel" class="FarTable">
							<thead>
								<tr>
									<th>费率</th>
									<th>生效日期</th>
									<th>失效日期</th>
									<th width=160>操作</th>
								</tr>
							</thead>
							<tbody>
						    <?php foreach($network->networkfuel as $networkfuel):?>
							<tr id="<?php echo $networkfuel->network_fuel_id?>">
									<td style="text-align: right;"><?php echo $networkfuel->rate;?></td>
									<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$networkfuel->effective_date);?></td>
									<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$networkfuel->fail_date);?></td>
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
					</td>
				</tr>
				<?php endif;?>
				<?php if(request('id')):?>
				<tr>
					<th>发票模板</th>
					<td>
						<table id="table_countryinvoice" class="FarTable">
							<thead>
								<tr>
									<th>国家(英文逗号隔开,例如PE,VG)</th>
									<th>发票模板</th>
									<th width=160>操作</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								$invoices = CountryInvoice::$invoice_type;
								foreach ($invoices as $k=>$v){
									$invoice_arr[] = array (
										"id" => $k,"text" => $v
									);
								}
							?>
						    <?php foreach($network->countryinvoice as $countryinvoice):?>
							<tr id="<?php echo $countryinvoice->id?>">
									<td style="text-align: right;"><?php echo $countryinvoice->country_codes;?></td>
									<td style="text-align: right;"><?php echo CountryInvoice::$invoice_type[$countryinvoice->invoice_type];?></td>
									<td>
										<a class="btn btn-mini" href="javascript:void(0);"
											onclick="EditRow([{'type':'text','required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode($invoice_arr));?>,'required':'true'}],this);">
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
									<td>
										<a class="btn btn-mini btn-success" href="javascript:void(0);"
											onclick="NewRow([{'type':'text','required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode($invoice_arr));?>,'required':'true'}],this);">
											<i class="icon-plus"></i>
											新建
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<?php endif;?>
			</tbody>
		</table>
	</div>
	<div class="FarTool text-center">
		<a class="btn btn-inverse" href="<?php echo url('network/search')?>">
			<i class="icon-reply"></i>
			返回
		</a>
		<button class="btn btn-primary" type="submit" onclick="return Save();">
			<i class="icon-save"></i>
			保存
		</button>
	</div>
</form>

<script type="text/javascript">
	/**
     * 保存
	 */
	function Save(){
		var result = false;
		$.ajax({
			url:"<?php echo url('network/checknetwork')?>"+"?network_id=<?php echo $network->network_id?>&value="+$("#text_network_code").val(),
			type:"GET",
			async : false,
			success:function(msg){
				if(msg=="true")
					result = true;
			}
		});
		if(!result){
			alert("网络代码已存在,无法保存");
			return result;
		}
	}
	/**
	 * 回调 保存数据
	 */
	function CallBack(obj,name){
		if(obj==null){
			return false;
		}
		console.log(name)
		if(name=="table_networkfuel" ){
		
			$.ajax({
				url:"<?php echo url('network/networkfuelsave')?>",
				type:"POST",
				data:{"network_id":"<?php echo request('id')?>",
					"networkfuel":{
						"network_fuel_id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
						"rate":$(obj).children().eq(0).text(),
						"effective_date":$.trim($(obj).children().eq(1).text()),
						"fail_date":$(obj).children().eq(2).text()}},
				success:function(msg){
					$(obj).attr("id",msg);
				}
			});
		}else{
			$.ajax({
				url:"<?php echo url('/countryinvoicesave')?>",
				type:"POST",
				data:{"network_id":"<?php echo request('id')?>",
					"id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
					"countryinvoice":{
						"country_codes":$(obj).children().eq(0).text(),
						"invoice_type":$(obj).children().eq(1).text()}},
				success:function(msg){
					console.log(msg)
					$(obj).attr("id",msg);
				}
			});
		}
	}
		/**
		 * 回调 删除数据
		 */
		function DeleteBefore(obj){
			if(name=="table_networkfuel" ){
				$.ajax({
					url:"<?php echo url('network/delete')?>",
					type:"POST",
					data:{"network_fuel_id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
					success:function(msg){
					}
				});
			}else{
				$.ajax({
					url:"<?php echo url('/decountryinvoice')?>",
					type:"POST",
					data:{"id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
					success:function(msg){
					}
				});
			}
		}
</script>

<?PHP $this->_endblock();?>