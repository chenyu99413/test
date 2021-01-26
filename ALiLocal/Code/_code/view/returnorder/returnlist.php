<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR?>public/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet"
	href="<?php echo $_BASE_DIR?>public/css/tablesorter.css">
    <form method="POST" id="searchForm" style="margin-bottom:0px;">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>退货时间</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 120px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 120px;">
					</td>
					<th>末端/阿里单号</th>
					<td>   
					   <textarea name="order_no" placeholder="每行一个单号"
							style="width: 230px"><?php echo request("order_no")?></textarea>
                    </td>
                    <th>总单号</th>
					<td>   
					   <input type="text" name="return_total_no" value="<?php echo request("return_total_no")?>" style="width: 90%">
                    </td>
                    <td>
                     <button class="btn btn-primary btn-small" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="do" value="导出" class="btn btn-small btn-info">
			             <i class="icon-download"></i>
			                                       导出
		               </button>
                    </td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>
		<input type="hidden" name="return_id" id="return_id"  />
		<?php if(MyApp::checkVisible('tuijian-status')):?>
		<button class="btn btn-info batch" type="submit" name="do" value="15" style="margin: 5px 5px">
			批量转已确认
		</button>
		<button class="btn btn-info batch" type="submit" name="do" value="20" style="margin: 5px 5px">
			批量转待重发
		</button>
		<button class="btn btn-info batch" type="submit" name="do" value="30" style="margin: 5px 5px">
			批量转待销毁
		</button>
		<button class="btn btn-info batch" type="submit" name="do" value="40" style="margin: 5px 5px">
			批量转待退货
		</button>
		<?php endif;?>
	</div>
    <div class="tabs-container " style="min-width: 1148px;border-bottom:0px;margin-bottom: 10px">
       <?php
		echo Q::control ( "tabs", "description", array (
			"tabs" => $tabs,"active_id" => $active_id 
		) );
		?>
		<div style="width: 100%;overflow: scroll;" id='table-cont'>
			<table id="myTable" class="FarTable tablesorter" style="max-width: 7100px;margin-top:0px;">
            		<thead>
            			<tr>
            				<th class="x_scroll x_scroll_th"><input type="checkbox" onchange="selectall(this)"></th>
            				<th class="x_scroll x_scroll_th">退货仓库</th>
            				<th class="x_scroll x_scroll_th">末端单号</th>
            				<th class="x_scroll x_scroll_th">ALS单号</th>
            				<th class="x_scroll x_scroll_th">入库总单号</th>
            				<th>退货时间</th>
            				<th>仓储时间（小时）</th>
            				<?php if (request ( "parameters" ) == "daichongfa" || request ( "parameters" ) == "yidayin" || request ( "parameters" ) == "yichongfa"):?>
            				<th>重发单号</th>
            				<th>重发渠道</th>
            				<th>重发时间</th>
            				<?php endif;?>
            				<?php if (request ( "parameters" ) == "daixiaohui" || request ( "parameters" ) == "yixiaohui"):?>
            				<th>销毁时间</th>
            				<?php endif;?>
            				<?php if (request ( "parameters" ) == "daixiaohui" || request ( "parameters" ) == "yixiaohui"):?>
            				<th>退回单号</th>
            				<th>退回时间</th>
            				<?php endif;?>
            				<th>原件数</th>
            				<th>原重量</th>
            				<th>退货件数</th>
            				<th>退货重量</th>
            			</tr>
            		</thead>
            		<tbody>
            		<?php foreach ($orders as $order):?>
            			<tr>
            				<td class="x_scroll x_scroll_td"><input type="checkbox" class="ids" name="ids[]" value="<?php echo $order->return_order_id?>"></td>
            				<td><?php echo Department::find('department_id=?',$order->department_id)->getOne()->department_name;?></td>
            				<td><?php echo $order->tracking_no?></td>
            				<td><a target="_blank" href="<?php echo url('/returnedit',array('return_order_id'=>$order->return_order_id))?>"><?php echo $order->ali_order_no?></a></td>
            				<td><?php echo ReturnTotal::find('return_total_id=?',$order->return_total_id)->getOne()->return_total_no;?></td>
            				<td><?php echo date('Y-m-d H:i:s',$order->return_time)?></td>
            				<td><?php echo $order->storage_time?></td>
            				<?php if (request ( "parameters" ) == "daichongfa" || request ( "parameters" ) == "yidayin" || request ( "parameters" ) == "yichongfa"):?>
            				<td><?php echo $order->new_tracking_no?></td>
            				<td><?php echo ReturnChannel::find('channel_id=?',$order->channel_id)->getOne()->channel_name;?></td>
            				<td><?php echo $order->again_time ? date('Y-m-d H:i:s',$order->again_time):'';?></td>
            				<?php endif;?>
            				<?php if (request ( "parameters" ) == "daixiaohui" || request ( "parameters" ) == "yixiaohui"):?>
            				<td><?php echo $order->destroy_time ? date('Y-m-d H:i:s',$order->destroy_time):'';?></td>
            				<?php endif;?>
            				<?php if (request ( "parameters" ) == "daixiaohui" || request ( "parameters" ) == "yixiaohui"):?>
            				<td><?php echo $order->new_tracking_no?></td>
            				<td><?php echo $order->send_back_time ? date('Y-m-d H:i:s',$order->send_back_time):''?></td>
            				<?php endif;?>
            				<td><?php echo $order->original_num?></td>
            				<td><?php echo $order->original_weight?></td>
            				<td><?php echo $order->return_num?></td>
            				<td><?php echo $order->return_weight?></td>
            				
            			</tr>
            		<?php endforeach;?>
            		</tbody>
            	</table>
            </div>
	</div>
    <input id="parameters" type="hidden" name="parameters" value="<?php echo $parameters?>">
    </form>
	<?php
	$this->_control ( "pagination", "my-pagination", array (
		"pagination" => $pagination 
	) );
	?>
<?PHP $this->_endblock();?>
<script type="text/javascript">
//批量转设置
$('.batch').click(function(){
	if($(".ids:checked").length<=0){
		alert("请选择订单");
		return false;
	}
	var return_id = '';
	$(".ids").each(function(){
		if($(this).prop('checked')){
			return_id = return_id + $(this).val() + ','
		}
	});
	return_id=return_id.substring(0,return_id.length-1)
	$('#return_id').val(return_id);
})
//全选
function selectall(obj){
	$(".ids").each(function(){
		$(this).prop('checked',$(obj).prop('checked'))
	});
}
/**
 *  点击tabs设置隐藏框值 
 */	 
function TabSwitch(code){
	$("#parameters").val(code);
	$("#searchForm").trigger("submit");
}
</script>
