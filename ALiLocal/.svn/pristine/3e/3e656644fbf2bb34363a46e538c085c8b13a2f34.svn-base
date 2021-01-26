<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    <?php //head title 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="POST" id="searchForm">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
				    <th>
					<?php
                            echo Q::control ( 'dropdownlist', 'type', array (
                            'items'=>array('warehouse_confirm_time'=>'核查日','record_order_date'=>'发件日'),
                            'value' => request('type'),
                            'style'=>'width:90px'
                         ) )?>
				    </th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="start_date"
							value="<?php echo request('start_date')?>" style="width: 133px;">
					</td>
					<th>到</th>
					<td>
					<input type="text" data-options = "showSeconds:false" class="easyui-datetimebox" name="end_date"
						value="<?php echo request('end_date')?>" style="width: 133px;">
					</td>
					<th>阿里单号</th>
					<td>
					   <textarea name="ali_order_no" placeholder="每行一个订单号" cols="" rows="2" style="width:150px;"><?php echo request('ali_order_no')?></textarea>
					</td>
					<td>
					   <button class="btn btn-primary btn-small">
			             <i class="icon-search"></i>
			                                       搜索
		               </button>
					   <a class="btn btn-info btn-small" href="javascript:void(0)" onclick="recalculation(3)">
			             <i class="icon-cog"></i>
			                                        重算收付
		               </a>
		               <a class="btn btn-warning btn-small" href="javascript:void(0)" onclick="recalculation(1)">
			             <i class="icon-cog"></i>
			                                        重算应收
		               </a>
		               <a class="btn btn-warning btn-small" href="javascript:void(0)" onclick="recalculation(2)">
			             <i class="icon-cog"></i>
			                                        重算应付
		               </a>
					  </td>
				</tr>           
			</tbody>           
		</table>  
	</div>
	<table class="FarTable" style="width:60%">
            <thead>
        		<tr>
        		    <th><input type="checkbox" id="checkall"
						onclick="SelectAll(this,'checkbox_');getIds();"></th>
        			<th>序号</th>
    				<th>阿里订单号</th>
    				<th width="120px">核查时间</th>
    				<th>应收总和</th>
    				<th width="120px">发件日</th>
    				<th>应付总和</th>
    			</tr>
    		</thead>
    		<tbody>
    		    <?php if (isset($orders)):?>
            	<?php $i=1;?>
            	<?php foreach ($orders as $p):?>
    		    <tr>
    		    <td style="text-align: center"><input type="checkbox"  class="checkbox_"  value="<?php echo $p['order_id'] ?>" /></td>
    			<td><?php echo $i++ ?></td>
    			<td><a  target="_blank" href="<?php echo url('order/detail', array('order_id' => $p['order_id']))?>"> <?php echo $p['ali_order_no'] ?>
            		</a>
            	</td>
    			<td><?php echo Helper_Util::strDate('Y-m-d H:i', $p['warehouse_confirm_time'])?></td>
    			<td><?php echo $p['receivable_amount']?></td>
    			<td><?php echo Helper_Util::strDate('Y-m-d H:i', $p['record_order_date'])?></td>
    			<td ><?php echo $p['payment_amount']?></td>
        		</tr>
        	    <?php endforeach;?>
        	    <?php endif;?>
    		</tbody>
    	</table>
    	<input type="hidden" id="order_ids" name="order_ids" />
</form>
<script type="text/javascript">
/**
 * 获取订单id
 */
function getIds(){
	var ids = "";
	$(".checkbox_:checked").each(function (obj){
		ids += $(this).val() + ",";
	});
	ids = ids.substr(0,ids.length-1);
	$("#order_ids").val(ids);
}
/**
 * 选择数据
 */
$(".checkbox_").click(function(e){
	getIds();
	e.stopPropagation();
});
/**
 * 重新计算
 */
function recalculation(type){
	//检查是否勾选
	if($("#order_ids").val()){
		$.messager.progress();
		$.ajax({
			url:'<?php echo url('/newfee')?>',
			data:{order_ids:$("#order_ids").val(),'type':type},
			type:'post',
			dataType:'json',
			success:function(data){
				console.log(data)
				if(data!=null && data.message=='formulaerror'){
					$.messager.progress('close');
					alert('存在生效费用项无法计算，请联系系统管理员。');
					return false;
				}
				$.messager.progress('close');		
				$("form").submit();				
			},
			error: function (XMLHttpRequest, textStatus, errorThrown)
	        {
				$.messager.progress('close');
				alert('存在生效费用项无法计算，请联系系统管理员。');
				return false;
	        }
		});
	}else{
		$.messager.alert('重算收付','请先勾选订单数据');
	}
}
</script>
<?PHP $this->_endblock();?>

