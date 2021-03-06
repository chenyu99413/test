<div class="FarTool">
    <?php if (request("status")=="0"):?>
		<a class="btn btn-small" href="javascript:void(0);" onclick="opensplit()">拆分 </a>
	<?php endif;?>
	<label style="float: right">
		共有<?php echo $pagination['record_count'];?>
		条记录，金额总计：
		<span><?php echo strlen(trim($total,';'))>0?trim($total,';'):''?></span>
		，已选择：
		<span id="selected_tiao">0</span>
		条记录，金额总计(CNY)：<?php echo $fee_sum?>
		<span id="selected_money"></span>
	</label>
</div>
<style>
</style>
<div style="width: 960px; height: 330px; overflow: scroll;" class="StickyHeader">
	<table id="table_waybill" class="FarTable" style="max-width: none;">
		<thead>
			<tr>
				<th >
					<input type="checkbox" id="checkall"
						onclick="SelectAll(this,'checkbox');">
				</th>
				<th>发件日</th>
				<th>订单号</th>
				<th>运单号</th>
				<th>类型</th>
				<th>币种</th>
				<th>应收</th>
				<th>发票号</th>
				<th width="100px">开票日期</th>
				<th>凭证号</th>
				<th width="100px">销账日期</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($fees as $fee):?>
			<tr>
				<td align="center">
					<?php  echo '<input type="checkbox" class="checkbox" id="waybill_balance_id" name="checkbox[]" value="'; echo $fee->fee_id;echo '"  />'?></td>
				<td align="center"><?php echo Helper_Util::strDate('Y-m-d', $fee->order->record_order_date)?></td>
				<td align="left"><?php echo $fee->order->ali_order_no;?></td>
				<td align="left">
					<?php echo $fee->order->tracking_no;?>
					<a  target="tracking<?php echo $fee->order_id?>"
						href="<?php echo url('order/detail',array('order_id'=>$fee->order_id));?>">
						<i class="icon-th-large"></i></a>
				</td>
				<td align="left"><?php echo $fee->fee_item_name;?></td>
				<td align="left"><?php echo $fee->currency;?></td>
				<td align="right"><?php echo sprintf('%.2f',$fee->amount);?></td>
				<td align="left"><?php echo $fee->invoice_no;?></td>
				<td align="center"><?php echo Helper_Util::strDate('Y-m-d', $fee->invoice_time);?></td>
				<td align="left"><?php echo $fee->voucher_no;?></td>
				<td align="center"><?php echo Helper_Util::strDate('Y-m-d', $fee->voucher_time);?></td>
				<td style="display: none"><?php echo $fee->remark;?></td>
				<td style="display: none"><?php echo $fee->waybill_title;?></td>
				<td style="display: none"><?php echo $fee->bill_no;?></td>
			</tr>
			<?php endforeach;?>
			</tbody>

	</table>
</div>
<?php echo Q::control('paginationa','pagination',array('pagination'=>$pagination))?>
<table>
	<tbody>
		<tr>
			<td colspan="6" style="width: 120px;text-align:right">
				<a class="btn btn-info btn-small" target="_blank" href="<?php echo url('/invoiceimport')?>" ><i class="icon-upload"></i> 导入发票信息</a> 
			</td>
		</tr>
		<tr>
			<th width="80">发票号</th>
			<td>
				<input id="invoice_code" name="invoice_code">
			</td>
			<th>开票日期</th>
			<td><?php
			echo Q::control ( 'datebox', 'billing_dates', array (
				'name' => 'billing_date',
				'value' => date ( 'Y-m-d' ) 
			) )?></td>
			<th>账单抬头</th>
			<td>
				<?php
				echo Q::control ( 'dropdownbox', 'waybilltitle', array (
					'items' => Helper_Array::toHashmap(Title::find('customer_id = ?',$customer_id)->asArray()->getAll(),'name','name'),
					'empty' => 'true',
					"style" => "width:200px" 
				) )?>
			</td>
		</tr>
		<tr>
			<th>凭证号</th>
			<td>
				<input id="voucher_code" name="voucher_code" >
			</td>
			<th>备注</th>
			<td>
				<input id="remark" name="remark">
			</td>
			<th>账单号</th>
			<td>
				<input id="bill_no" name="bill_no">
			</td>
		</tr>
	</tbody>
</table>
<div class="FarTool">
	<button type="submit" class="btn btn-small btn-primary" onclick="return Save();">
		<i class="icon-save"></i>
		保存
	</button>
</div>
<script type="text/javascript">
var today='<?php echo date('Y-m-d')?>';
var lastVcode='<?php echo  request('status')?'':MyApp::getDate('lastVcode')?>';

$('.StickyHeader table').floatThead({
    useAbsolutePositioning: true,
    scrollContainer: function($table){
        return $table.closest(".StickyHeader");
    }
});
/**
 * 开票日期设置为当天日期
 */
function CheckData(){
	var d = new Date();
	var str = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
	$('#billing_dates').val(str);
}
	
/**
 * 多选框勾选事件 
 */
function Check(obj){
	var tds = $('.checkbox:checked').parent().parent().children();
	if($('.checkbox:checked').length==1){
		$('#invoice_code').val($(tds).eq(7).text());
		$('#billing_dates').val($(tds).eq(8).text().length?$(tds).eq(7).text():today);
		$('#remark').val($(tds).eq(11).text());
		$('#waybilltitle').val($(tds).eq(12).text());
		$('#voucher_code').val($(tds).eq(9).text());
		$('#bill_no').val($(tds).eq(13).text());
	}else{
		$('#invoice_code').val('');
	    $('#remark').val('');
	    $('#billing_dates').val('');
	    $('#waybilltitle').val('');
	    $('#voucher_code').val('');
	    $('#bill_no').val('');
	}
	//已选择条数赋值
	$('#selected_tiao').text($('.checkbox:checked').length);
	//已选择金额赋值
	var checkbox = $('.checkbox');
	var selected_money=0;
	var selected_currency='';
	var currency = new Array();
	var currency_amount = new Array();
	var selected_str = '';
	$('.checkbox:checked').each(function(){
		selected_currency = $(this).parents('tr').eq(0).find('td').eq(5).text();
		selected_money = parseFloat($(this).parents('tr').eq(0).find('td').eq(6).text());
		var i = $.inArray(selected_currency,currency);
		if(i == -1){
			currency.push(selected_currency);
			currency_amount[selected_currency] = selected_money.toFixed(2);
		}else{
			currency_amount[selected_currency] = (Number(currency_amount[selected_currency])+selected_money).toFixed(2);
		}
	});
	$.each(currency,function(k,v){
		if(v != ''){
			selected_str += v + ':' + currency_amount[v] + ';';
		}
	});
	$('#selected_money').text(selected_str);
}
	
/**
 * 保存按钮操作 
 */
 function Save(){
		var fee_id = new Array();
		$('.checkbox:checked').each(function(){
			fee_id.push($(this).val());
		});
		if($('.checkbox:checked').length>0){
			if($('#waybilltitle').val()==''){
				   alert("请选择账单抬头");
				   return false;
			}
	    	$.ajax({
				type:'post',
				url:'<?php echo url('statistics/offsedit')?>',
				dataType:'json',
				data : {  
						 'fee_id' : fee_id,
						 'invoice_code' : $('#invoice_code').val(),
						 'billing_date' : $('#billing_dates').val(),
						 'voucher_code' : $('#voucher_code').val(),
						 'waybill_title' :$('#waybilltitle').val(),
						 'remark' : $('#remark').val(),
						 'bill_no' : $('#bill_no').val()
	 				 },
	 			success:function(data){
		 			alert("保存成功");
		 			loadDeatil();
	 			}
			});
		}else{
			alert("没有选中的记录，请先在列表中勾选。");
		}
		return false;
	}/**
 * 打开拆分费用表框
 */
 function opensplit(){
 	var fee_id = new Array();
 	$('.checkbox:checked').each(function(){
 		fee_id.push($(this).val());
 	});
 	if($('.checkbox:checked').length !=1){
 		alert("每次必须选中一条数据");
 	}else{
 		$('#hidden_originamount').val($('.checkbox:checked').parent().parent().children().eq(6).text());
 		$('#dialog_split').dialog('open');
 	}
 }
</script>