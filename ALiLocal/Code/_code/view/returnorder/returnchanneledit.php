<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>渠道编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
if (request('channel_id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'退件渠道管理' => url ( '/returnchannel' ),
			'退件渠道编辑' => url ( '/returnchanneledit' )
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'退件渠道管理' => url ( '/returnchannel' ),
			'退件渠道新建' => url ( '/returnchanneledit' )
		) 
	) );
}
?>
<form method="post">
	<div class="FarSearch span12" style="padding:5px;">
	<div class="span6">
		<table>
			<tbody>
				<tr>
					<th class="required-title">渠道名称</th>
    				<td>
    					<input name="channel_name" id="channel_name" type="text"  required="required" value="<?php echo $channel->channel_name?>">
    				</td>
    				<th>最长边限制</th>
    				<td>
    					<input type="number" step="0.01" id="length" name="length" value="<?php echo $channel->length?$channel->length:''?>"/>CM
    				</td>
				</tr>
				<tr>
					<th class="required-title">分组</th>
    				<td>
    					<?php echo Q::control('dropdownbox','channel_group_id',array(
    					    'items'=>Helper_Array::toHashmap(Channelgroup::find()->getAll(),'channel_group_id','channel_group_name'),
    					    'value'=>$channel->channel_group_id,
    					    'style'=>'width:165px',
    					    "empty"=>true,
    					    "required"=>true
    					))?>
    				</td>
    				<th>第二长边限制</th>
    				<td>
    					<input type="number" step="0.01" id="width" name="width" value="<?php echo $channel->width?$channel->width:''?>"/>CM
    				</td>
				</tr>
				<tr>
					<th>网络</th>
                	<td>
                		<?php echo Q::control('dropdownlist','network_code',array(
                			'items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_name'),
                			'value'=>$channel->network_code,
                			'style'=> 'width: 165px',
                		))?>
                	</td>
                	<th>高限制</th>
					<td>
						<input type="number" step="0.01" id="height" name="height" value="<?php echo $channel->height?$channel->height:''?>"/>CM
					</td>
				</tr>
				<tr>
					<th>末端网络</th>
                	<td>
                		<?php echo Q::control('dropdownbox','trace_network_code',array(
                			'items'=>Helper_Array::toHashmap(Network::find()->getAll(),'network_code','network_name'),
                			"empty"=>true,
                			'value'=>$channel->trace_network_code,
                			'style'=> 'width: 165px',		
                		))?>
                	</td>
                	<th>周长限制</th>
					<td>
						<input type="number" step="0.01" id="perimeter" name="perimeter" value="<?php echo $channel->perimeter?$channel->perimeter:''?>"/>CM
					</td>
				</tr>
				<tr>
					<th>发件人</th>
                	<td>
                		<?php
// 						echo Q::control ( 'myselect', 'sender_id', array (
// 							'items' => Helper_Array::toHashmap(Sender::find()->getAll(),'sender_id','sender_code'),
// 							'selected' => $checked,
// 							'style' => 'width:250px',
// 							'multiple' => 'multiple',
// 							'required' => 'required'
// 						) );
						?>
						<a class="btn btn-small btn-primary" href="<?php echo url('/returnchannelexport',array('channel_id'=>$channel->channel_id))?>">
						     <i class="icon-download"></i>
						     导出
						</a>
						<a class="btn btn-info btn-small"href="javascript:void(0)" onclick="fileout2()">
						<i class="icon-upload"></i> 导入 </a> 
						<a class="btn btn-small btn-info" href="<?php echo url('staff/sender',array('channel_id'=>$channel->channel_id))?>"><i class="icon-search"></i> 查看</a>
                	</td>
                	<th>围长限制</th>
					<td>
						<input type="number" step="0.01" name="girth" id="girth" value="<?php echo $channel->girth?$channel->girth:''?>"/>CM
					</td>
				</tr>
				<tr>
					<th>渠道账号</th>
                	<td>
                		<input name="account" id="account" type="text"  value="<?php echo $channel->account?>">
                	</td>
                	<th>单个包裹实重限制</th>
					<td>
						<input type="number" step="0.001" name="weight" id="weight" value="<?php echo $channel->weight?$channel->weight:''?>"/>KG
					</td>
				</tr>
				<tr>
					<th class="required-title">供应商</th>
                	<td>
                		<?php echo Q::control('dropdownbox','supplier_id',array(
                		    'items'=>Helper_Array::toHashmap(Supplier::find()->getAll(),'supplier_id','supplier'),
                		    "empty"=>true,
                		    'required'=>'required',
                		    'value'=>$channel->supplier_id,
                			'style'=> 'width: 165px',
                		))?>
                	</td>
                	<th>整票计费重限制</th>
					<td>
						<input type="number" step="0.001" name="total_cost_weight" id="total_cost_weight"  value="<?php echo $channel->total_cost_weight?$channel->total_cost_weight:''?>"/>KG
					</td>
				</tr>
				<tr>
					<th>标签标记</th>
                	<td>
                		<input name="label_sign" id="label_sign" type="text"   value="<?php echo $channel->label_sign?>">
                	</td>
                	<th>申报总价阀值</th>
                	<td>
                		<input name="declare_threshold" id="declare_threshold" type="number" step="0.001"  value="<?php echo $channel->declare_threshold?$channel->declare_threshold:''?>">USD
                	</td>
				</tr>
				<tr>
					<th>推送三免</th>
                	<td>
                		<?php echo Q::control('RadioGroup','send_kj',array(
                		    'items'=>array(1=>'是',2=>'否'),
                			'value'=>$channel->send_kj
                		))?>

                	</td>
                	<th>验证数据完整性</th>
    					<td>
    					<?php
							echo Q::control ( "RadioGroup", "check_complete", array (
								"items"=>array(1=>'是',2=>'否'),
								"value" => $channel->check_complete,
							) )?>
    					</td>
				</tr>
				<tr>
					<th>是否支持带电</th>
                	<td>
                		<?php echo Q::control('RadioGroup','has_battery',array(
                		    'items'=>array(1=>'是',2=>'否'),
                			'value'=>$channel->has_battery
                		))?>

                	</td>
                	<th>是否支持报关</th>
                	<td>
                		<?php echo Q::control('RadioGroup','is_declaration',array(
                		    'items'=>array(1=>'是',2=>'否'),
                			'value'=>$channel->is_declaration
                		))?>

                	</td>
				</tr>
				<tr>
					<th class="required-title">渠道类型</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "type", array (
							"items" => Product::$type,
							"value" => $channel->type,
							"required" => "required",
							"style" => "width: 165px",
							"empty" => "true" 
						) )?>
					</td>
					
					<th>分拣路由码</th>
                	<td>
                		<input name="sort_code" id="sort_code"  type="text" value="<?php echo $channel->sort_code?>">
                	</td>
				</tr>
				<tr>
					<th class="required-title">数据预报规则</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "forecast_type", array (
							"items" => array('1'=>'向下取整回调','2'=>'实重减3g','3'=>'以出库原数据'),
							"value" => $channel->forecast_type,
							"required" => "required",
							"style" => "width: 165px",
							"empty" => "true" 
						) )?>
					</td>
					<th>是否支持无FDA出货</th>
                	<td>
                		<?php echo Q::control('RadioGroup','is_pda',array(
                		    'items'=>array(1=>'是',0=>'否'),
                			'value'=>$channel->is_pda
                		))?>

                	</td>
				</tr>
				<tr>
					<th class="required-title">打单方式</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "print_method", array (
							"items" => ReturnChannel::$method,
							"value" => $channel->print_method,
							"required" => "required",
							"style" => "width: 165px",
							"empty" => "true" 
						) )?>
					</td>
					<th>验证偏派邮编</th>
					<td>
						<?php echo Q::control('RadioGroup','postcode_verify',array(
                		    'items'=>array(1=>'是',0=>'否'),
							'value'=>$channel->postcode_verify
                		))?>
                		<a class="btn btn-small btn-info" target ="_blank" href="<?php echo url('/ReturnChannelImportCode',array('channel_id'=>$channel->channel_id))?>"><i class="icon-edit"></i> 设置</a>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
		<div class="span5">
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
    	<div class="FarTool span10" style="text-align: center">
    		<a class="btn btn-inverse" href="<?php echo url('/returnchannel')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
		<input type="hidden" name="department_hidden" id="department_hidden">
</form>
<form id="formout2" action="<?php echo url('/returnchannelimport')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileout2">
    <input type="hidden" id="" name="channel_id" value="<?php echo $channel->channel_id?>">
</form>
<script type="text/javascript">
function fileout2(){
	$('#fileout2').click();
}
$(function(){
	$("#fileout2").change(function(){
		$("#formout2").submit();	
	})
	$("form").submit(function(){
		//可用部门
		var department = "";
		$($("#department_tree").tree("getChecked")).each(function(){
			department += $(this)[0].id+",";
		});
		$("#department_hidden").val(department.substring(0,department.length-1));
	});
})
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

</script>

<?PHP $this->_endblock();?>