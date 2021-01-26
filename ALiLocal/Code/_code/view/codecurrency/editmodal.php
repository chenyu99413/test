<?PHP $this->_extends('_layouts/modal_layout'); ?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('container_main');?>
<div style="width: 100%">
	<form style="margin-bottom: 0px;" id="edit-currency-form">
		<span>提示：比如1USD=7.0906CNY,汇率填7.0906。</span>
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col style="width: 80px;">
				<col style="width: 200px;">
			</colgroup>
			<tbody>
				<tr>
					<th class="required-title">代码</th>
					<td>
						<input required="required" type="text" name="code" id="code" value="<?php echo $currency->code?>"  />
						<input type="hidden" name="currency_id"
							value="<?php echo request('currency_id')?>" />
					</td>
				</tr>
				<tr>
					<th class="required-title">名称</th>
					<td>
						<input type="text" name="name" value="<?php echo $currency->name?>"
							required="required" />
					</td>
				</tr>
				<tr>
					<th class="required-title">汇率</th>
					<td>
						<input required="required" type="text" name="rate" id="rate" value="<?php echo $currency->code=='CNY'?1:$currency->rate?>"  />
					</td>
				</tr>
				<tr>
					<th class="required-title">汇率时间区间</th>
					<td>
						<input type="text" data-options = "" class="easyui-datebox" id="s_modifyDateStart" name="start_date"
							value="<?php echo $currency->start_date?date('Y-m-d',$currency->start_date):''?>" style="width: 120px;" >
					到
						<input type="text" data-options = "" class="easyui-datebox" id="s_modifyDateEnd" name="end_date"
							value="<?php echo $currency->end_date?date('Y-m-d',$currency->end_date):''?>" style="width: 120px;" >
					</td>
				</tr>
			</tbody>
		</table>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" onclick="checkdata()">
				<i class="icon-save"></i>
				保存
			</button>
		</div>
		<div>
			<table class="FarTable" style="width: 99.6%;">
				<thead>
					<tr>
						<th width="100px;">供应商</th>
						<th width="100px;">汇率</th>
						<th width="100px;">开始时间</th>
						<th width="100px;">结束时间</th>
						<th width="150px;">操作</th>
					</tr>
				</thead>
			</table>
			<div style="width: 100%; height: 400px; overflow: scroll;margin-top:-10px;">
			<?php 
			foreach ($supplier as $s){
				$supp [] = array (
					"id" => $s->supplier_id,"text" => $s->supplier_id.'-'.$s->supplier
				);
			}
			?>
								<table id="table_price" class="FarTable" style="width: 100%;">
									<tbody>
										<?php foreach ($item as $value):?>
										<tr id="<?php echo $value->item_id?>">
										<?php $supplier = Supplier::find('supplier_id=?',$value->supplier_id)->getOne();?>
										    <td width="100px;" style="text-align: right"><?php echo $value->supplier_id.'-'.$supplier->supplier?></td>
										    <td width="100px;" style="text-align: right"><?php echo $value->rate?></td>
										    <td width="100px;" style="text-align: right"><?php echo date('Y-m-d',$value->start_date)?></td>
										    <td width="100px;" style="text-align: right"><?php echo date('Y-m-d',$value->end_date)?></td>
											<td width="150px;">
												<a class="btn btn-mini" href="javascript:void(0);"
													onclick="EditRow([{'type':'select','option':<?php echo str_replace("\"","'",json_encode($supp));?>},{'type':'text'},{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'}],this);">
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
										<tr>
											<td width="100px;"></td>
											<td width="100px;"></td>
											<td width="100px;"></td>
											<td width="100px;"></td>
											<td width="150px;">
												<a class="btn btn-mini btn-success"
													href="javascript:void(0);"
													onclick="NewRow([{'type':'select','option':<?php echo str_replace("\"","'",json_encode($supp));?>},{'type':'text'},{'type':'date','value':$('#datebox_effective_date').val(),'required':'true'},{'type':'date','value':$('#datebox_expiration_date').val(),'required':'true'}],this);">
													<i class="icon-plus"></i>
													新建
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
		</div>
		<?php if (count($logs)){?>
		<div>
    		<table class="table table-bordered table-condensed">
    			<caption>操作日志</caption>
        		<thead>
            		<tr>
            			<th style="width:40px">操作人</th>
            			<th style="width:80px">时间</th>
            			<th style="width:100px">日志</th>
        			</tr>
        		</thead>
        		<tbody>
        			<?php foreach ( $logs as $log):?>
            		<tr>
            			<td><?php echo $log->staff_name;?></td>
            			<td><?php echo date('Y-m-d H:i:s',$log->create_time);?></td>
            			<td><?php echo $log->comment;?></td>
        			</tr>
        			<?php endforeach;?>
        		</tbody>
    		</table>
		</div>
		<?php }?>
	</form>
</div>
<?PHP $this->_endblock();?>
<?PHP $this->_block('page_js');?>
<script type="text/javascript">
/**
 * 回调 删除数据
 */
function DeleteBefore(obj){
	$.ajax({
		url:"<?php echo url('codecurrency/currencyitemdel')?>",
		type:"POST",
		data:{"item_id":$(obj).attr("id")==undefined?"":$(obj).attr("id")},
		success:function(msg){
		}
	});
}
/**
 * 回调 保存数据
 */
function CallBack(obj,name){
	console.log($(obj).children().eq(0));
	//return false;
	if(obj==null){
		return false;
	}
	$.ajax({
		url:"<?php echo url('codecurrency/currencyitem')?>",
		type:"POST",
		data:{"currency_id":"<?php echo request('currency_id')?>",
			"item":{
				"item_id":$(obj).attr("id")==undefined?"":$(obj).attr("id"),
				"supplier_id":$(obj).children().eq(0).text(),
				"rate":$.trim($(obj).children().eq(1).text()),
				"start_date":$(obj).children().eq(2).text(),
				"end_date":$(obj).children().eq(3).text()}},
		success:function(msg){
			if(msg == '币种不存在'){
				alert(msg)
			}else{
				$(obj).attr("id",msg);
			}
			
		}
	});
}
//检查数据
function checkdata(){
	result = true;
	var code = $("#code").val();
	var name = $("#name").val();
	var rate = $("#rate").val();
	var start = $("input[name='start_date']").val();
	var end = $("input[name='end_date']").val();
	if(code == "" || rate == "" || name == "" || start.length<1 ||end.length<1){
	    result = false;
	}
	
	if(start.length<1||end.length<1){
		alert('请设置汇率时间区间');
		result = false;
	}
	if(result){
    	var saveload = layer.load(1);
    	var index = parent.layer.getFrameIndex(window.name);
    	var form_data = $('#edit-currency-form').serialize();
    	$.ajax({
    		url: '<?php echo url("codecurrency/editsave")?>',
    		type: 'POST',
    		dataType: 'json',
    		data: form_data,
    	})
    	.done(function(data) {
    		layer.close(saveload);
    		parent.layer.msg(data.message);
    		if (data.success) {
    			parent.layer.close(index);
    			parent.$('#search-currency-btn').click();
    		}
    	})
    	.fail(function(data) {
    		layer.close(saveload);
    		parent.layer.alert('发生内部错误，暂时无法修改');
    	});
	}
	return result;
}
$(function(){
	var html = '<td style="width:33%"><a class="current-month">当月</a></td>';
	$('.combo-arrow').on('click',function(){
		if($('.datebox-button table tbody tr:eq(0)').find('td').size()<3){
    		$('.datebox-button').find('table tbody tr td').css('width','33%');
    		$('.datebox-button').find('table tbody tr').append(html);
		}
	})
	// 给按钮添加事件
    $(".datebox-button").on("click",".current-month",function(){
    	$('#s_modifyDateStart').datebox('setValue',time(1));
		$('#s_modifyDateEnd').datebox('setValue',time(0));
		$(this).closest("div.combo-panel").panel("close");
    });
});
//设月底跟月头时间
function time(number){
	var date=new Date();
	var strDate=date.getFullYear()+"-";
	if(number==0){ //0 为月底
    	strDate+=date.getMonth()+2+"-";
    	strDate+=number;
	}else{
    	strDate+=date.getMonth()+1+"-";
    	strDate+=number;
	}
	return strDate;
}
</script>
<?PHP $this->_endblock();?>