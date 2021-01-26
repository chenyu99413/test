<?PHP $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>渠道编辑<?php $this->_endblock(); ?>
<?PHP $this->_block('contents');?>
<?php
if (request('id') != null) {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'渠道管理' => '',
			'渠道列表' => url ( 'channel/search' ),
			'渠道编辑' => url ( 'channel/edit', array (
				'id' => $channel->channel_id 
			) ) 
		) 
	) );
} else {
	echo Q::control ( 'path', '', array (
		'path' => array (
			'渠道管理' => '',
			'渠道列表' => url ( 'channel/search' ),
			'新建渠道' => url ( 'channel/edit' ) 
		) 
	) );
}
?>
<?php $country = Helper_Array::toHashmap(Country::find()->asArray()->getAll(), 'code_word_two','chinese_name')?>
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
						<a class="btn btn-small btn-primary" href="<?php echo url('channel/channelexport',array('channel_id'=>$channel->channel_id))?>">
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
                	<!-- <th>申报总价阀值</th>
                	<td>
                		<input name="declare_threshold" id="declare_threshold" type="number" step="0.001"  value="<?php echo $channel->declare_threshold?$channel->declare_threshold:''?>">USD
                	</td> -->
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
							"items" => Channel::$method,
							"value" => $channel->print_method,
							"required" => "required",
							"style" => "width: 165px",
							"empty" => "true" 
						) )?>
					</td>
					<th>指定可用邮编及区域</th>
					<td>
						<?php echo Q::control('RadioGroup','postcode_verify',array(
                		    'items'=>array(1=>'是',0=>'否'),
							'value'=>$channel->postcode_verify
                		))?>
                		<a class="btn btn-small btn-info" target ="_blank" href="<?php echo url('channel/ChannelImportCode',array('channel_id'=>$channel->channel_id))?>"><i class="icon-edit"></i> 设置</a>
					</td>
				</tr>
				<tr>
					<th>是否启用渠道</th>
					<td>
						<?php echo Q::control('RadioGroup','channel_status',array(
                		    'items'=>array(1=>'是',2=>'否'),
							'value'=>$channel->channel_status
                		))?>
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
    		<a class="btn btn-inverse" href="<?php echo url('channel/search')?>">
    			<i class="icon-reply"></i> 返回
    		</a>
    		<button type="submit" class="btn btn-primary">
    			<i class="icon-save"></i> 保存
    		</button>
    	</div>
    </div>
	<?php if ($channel->channel_id):?>
		<div style="margin-left: 0px;">
		         <b>申报总价阀值设置区间(在此区间则无服务)</b>
		    <table id="table_networkfuel" class="FarTable">
				<thead>
					<tr>
						<th>开始值</th>
						<th>结束值</th>
						<th>国家组</th>
						<th width=120>操作</th>
					</tr>
				</thead>
				<tbody>
			    <?php foreach($declare_threshold as $temp):?>
				<tr id="<?php echo $temp->id?>">
						<td style="text-align: center;"><?php echo $temp->front?></td>
						<td style="text-align: center;"><?php echo $temp->after?></td>
						<td style="text-align: center;"><?php echo $temp->countrygroup->name;?>
						<input type="hidden" value="<?php echo $temp->id?>" /></td>
						<td>
							<a class="btn btn-mini" href="javascript:void(0);"
								onclick="EditRow([{'type':'number','required':'true'},{'type':'number','required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Channeldeclarethreshold::getdepartment()));?>,'required':'true'}],this);">
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
								onclick="NewRow([{'type':'number','required':'true'},{'type':'number','required':'true'},{'type':'select','option':<?php echo str_replace("\"","'",json_encode(Channeldeclarethreshold::getdepartment()));?>,'required':'true'}],this);">
								<i class="icon-plus"></i>
								新建
							</a>
						</td>
					</tr>
				</tbody>
			</table>
			<b>禁运国家</b>
			<table class="FarTable" style="table-layout:fixed;">
				<thead>
					<tr>
						<th width=120>生效日期</th>
						<th width=120>失效日期</th>
						<th>禁运国家</th>
						<th width=120>操作</th>
					</tr>
				</thead>
				<tbody>
			    <?php foreach($disabled_country as $temp):?>
				<tr id="<?php echo $temp->disabled_country_id?>">
						<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$temp->effect_time);?></td>
						<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d',$temp->failure_time);?></td>
						<td style="word-wrap:break-word;"><?php echo $temp->country_code_two;?></td>
						<td>
							<button type="button" class="btn btn-mini btn-info edit-disabled_country"
								data-toggle="tooltip" data-placement="top" title="修改"
								data-disabled_country_id="<?php echo $temp->disabled_country_id?>">
								<i class="icon-pencil"></i>
								编辑
							</button>
							<button type="button" class="btn btn-mini btn-danger delete"
								data-toggle="tooltip" data-placement="top" title="删除"
								data-disabled_country_id="<?php echo $temp->disabled_country_id?>">
								<i class="icon-trash"></i>
								删除
							</button>
						</td>
					</tr>
				<?php endforeach;?>
				<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>
							<button class="btn btn-mini btn-success edit-disabled_country"
								data-toggle="tooltip" data-placement="top" title="新建"
								data-disabled_country_id="">
								<i class="icon-plus"></i> 
								新建
							</button>
						</td>
					</tr>
				</tbody>
			</table>
			<b>限额设置</b>
			<table id="table_networkfuel" class="FarTable">
				<thead>
					<tr>
						<th>周期</th>
						<th>类型</th>
						<th>仓库</th>
						<th>已用额度</th>
						<th>可用额度</th>
						<th>最大值</th>
						<th>国家组</th>
						<th>生效时间</th>
						<th>失效时间</th>
						<th width=120>操作</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$cycle = array(''=>'','0'=>'每日','1'=>'每周','2'=>'每月');
					$type = array(''=>'','0'=>'票数','1'=>'实重','2'=>'计费重');
				?>
			    <?php foreach($limitation_amount as $temp):?>
				<tr id="<?php echo $temp->limitation_amount_id?>">
						<td style="text-align: center;"><?php echo $cycle[$temp->cycle];?></td>
						<td style="text-align: center;"><?php echo $type[$temp->type];?></td>
						<td style="text-align: center;"><?php echo Department::find('department_id = ?',$temp->department_id)->getOne()->department_name;?></td>
						<td style="text-align: center;"><a target="blank" href="<?php echo url('order/search',array('channel_id'=>$channel->channel_id))?>"><?php echo $temp->used_value?></a></td>
						<td style="text-align: center;"><?php echo $temp->max_value-$temp->used_value?></td>
						<td style="text-align: center;"><?php echo $temp->max_value;?></td>
						<td style="text-align: center;"><?php echo @$country_group[$temp->country_group_id];?></td>
						<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d', $temp->effect_time);?></td>
						<td style="text-align: center;"><?php echo Helper_Util::strDate('Y-m-d', $temp->failure_time);?></td>
						<td>
							<button type="button" class="btn btn-mini btn-info edit-limit-amount"
								data-toggle="tooltip" data-placement="top" title="修改"
								data-limitation_amount_id="<?php echo $temp->limitation_amount_id?>">
								<i class="icon-pencil"></i>
								编辑
							</button>
							<button type="button" class="btn btn-mini btn-danger delete-limit-amount"
								data-toggle="tooltip" data-placement="top" title="删除"
								data-limitation_amount_id="<?php echo $temp->limitation_amount_id?>">
								<i class="icon-trash"></i>
								删除
							</button>
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
						<td></td>
						<td></td>
						<td>
							<button class="btn btn-mini btn-success edit-limit-amount"
								data-toggle="tooltip" data-placement="top" title="新建"
								data-limitation_amount_id="">
								<i class="icon-plus"></i> 
								新建
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php endif;?>
		<input type="hidden" name="department_hidden" id="department_hidden">
</form>
<form id="formout2" action="<?php echo url('channel/channelimport')?>" method="post" enctype="multipart/form-data" style="display:none">
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
/**
 * 回调 保存数据
 */
function CallBack(obj,name){
	if(obj==null){
		return false;
	}
	$.ajax({
		url:"<?php echo url('channel/disabledsave')?>",
		type:"POST",
		data:{"channel_id":"<?php echo request('channel_id')?>",
			"disable_department":{
				"id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
				"front":$.trim($(obj).children().eq(0).text()),
				"after":$(obj).children().eq(1).text(),
				"country_group_id":$(obj).children().eq(2).text()}},
		success:function(msg){
			$(obj).attr("id",msg);
		}
	});
}
	/**
	 * 回调 删除数据
	 */
	function DeleteBefore(obj){
		$.ajax({
			url:"<?php echo url('channel/disableddel')?>",
			type:"POST",
			data:{"id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
			success:function(msg){
			}
		});
	}
$('.edit-disabled_country').on('click',function(e){
	e.preventDefault();
	var url = "<?php echo url('/EditModal')?>/channel_id/"+"<?php echo request('channel_id')?>"+"/disabled_country_id/" + $(this).data('disabled_country_id');
	layer.open({
		type: 2,
		title: '禁运国家明细',
		maxmin: true,
		shadeClose: true,
		area: ['550px', '500px'],
		content: url
	});
});
//删除
$('.delete').on('click', function () {
	var disabled_country_id = $(this).data('disabled_country_id');
	layer.confirm('你确定要删除此条数据吗？', {
		btn : ['确定', '取消']
	}, function (index, layero) {
		layer.close(index);
		var deleteload = layer.load(1);
		$.ajax({
			url : '<?php echo url("/DeleteModal")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				disabled_country_id : disabled_country_id
			},
		})
		.done(function (data) {
			layer.close(deleteload);
			layer.msg(data.message);
			if (data.success) {
				location.reload();
			}
		})
		.fail(function (data) {
			layer.close(deleteload);
			layer.alert('发生内部错误，暂时无法删除');
		});
	});
});
//删除偏派邮编
$('.deletezip').on('click', function () {
	var id = $(this).attr('zip-id');
	layer.confirm('你确定要删除此条数据吗？', {
		btn : ['确定', '取消']
	}, function (index, layero) {
		layer.close(index);
		var deleteload = layer.load(1);
		$.ajax({
			url : '<?php echo url("/DeleteZip")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				id : id
			},
		})
		.done(function (data) {
			layer.close(deleteload);
			layer.msg(data.message);
			if (data.success) {
				location.reload();
			}
		})
		.fail(function (data) {
			layer.close(deleteload);
			layer.alert('发生内部错误，暂时无法删除');
		});
	});
});
//限制额度
$('.edit-limit-amount').on('click',function(e){
	e.preventDefault();
	var url = "<?php echo url('/EditLimitModal')?>/channel_id/"+"<?php echo request('channel_id')?>"+"/limitation_amount_id/" + $(this).data('limitation_amount_id');
	layer.open({
		type: 2,
		title: '限制额度明细',
		maxmin: true,
		shadeClose: true,
		area: ['350px', '500px'],
		content: url
	});
});
//删除
$('.delete-limit-amount').on('click', function () {
	var limitation_amount_id = $(this).data('limitation_amount_id');
	layer.confirm('你确定要删除此条数据吗？', {
		btn : ['确定', '取消']
	}, function (index, layero) {
		layer.close(index);
		var deleteload = layer.load(1);
		$.ajax({
			url : '<?php echo url("/DeleteLimitModal")?>',
			type : 'POST',
			dataType : 'json',
			data : {
				limitation_amount_id : limitation_amount_id
			},
		})
		.done(function (data) {
			layer.close(deleteload);
			layer.msg(data.message);
			if (data.success) {
				location.reload();
			}
		})
		.fail(function (data) {
			layer.close(deleteload);
			layer.alert('发生内部错误，暂时无法删除');
		});
	});
});
</script>

<?PHP $this->_endblock();?>