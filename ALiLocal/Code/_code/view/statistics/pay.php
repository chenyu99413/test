<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
付款
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<form method="post" action="" style="margin-bottom: 2px;" id="page-top-search">
	<div
		style="border: 1px solid #d4d4d4; background-color: #f2f2f2; padding: 10px;">
		<table>
			<tbody>
				<tr>
					<th>
					<?php
                        echo Q::control ( 'dropdownlist', 'timetype', array (
                        'items'=>array('1'=>'出库日期','2'=>'订单日期'),
                        'value' => request('timetype', '1' ),
                        'style'=>'width:80px'
                     ) )?>
                    </th>
					<td colspan="3">
						<?php
						echo Q::control ( "datebox", "start_date", array (
							"value" => request ( "start_date", date ( 'Y-m-' ) . '01' ),
							"style" => "width: 90px"
						) )?>
						-
						<?php
						echo Q::control ( "datebox", "end_date", array (
							"value" => request ( "end_date", date ( 'Y-m-d' ) ),
							"style" => "width: 90px"
						) )?>
					</td>
					<th>供应商</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'supplier_id', array (
                        'items'=>Helper_Array::toHashmap(Supplier::find()->asArray()->getAll(),'supplier_id','supplier'),
                        'empty' => true, 
                        'value' => request('supplier_id'),
                        'style'=>'width:80px'
                     ) )?>
					</td>
					<th>渠道</th>
					<td>
						<?php
                        echo Q::control ( 'dropdownbox', 'channel_id', array (
                        'items'=>Helper_Array::toHashmap(Channel::find()->asArray()->getAll(),'channel_id','channel_name'),
                        'empty' => true, 
                        'value' => request('channel_id'),
                        'style'=>'width:100px'
                     ) )?>
					</td>
					<th>付款状态</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "status", array (
							"items" => array (
								"0" => "应付未销账",
								"1" => "应付已销账",
								"2" => "全部"
							),
							"value" => request ( "status", "0" ) 
						) )?>
					</td>
					<th>类型</th>
					<td>
						<input class="easyui-combotree" name="fee_type[]" data-options="url:'<?php echo url('statistics/feetypetree',array('checked'=>implode(',',request('fee_type',array()))))?>'
								, method:'get', multiple:true,width:'180px'" />
					</td>
					<th>每页</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "page_size", array (
							"items" => array (
								"30" => "30条","50" => "50条","100" => "100条",
								"200" => "200条","500" => "500条","1000" => "1000条",
								"1500" => "1500条","2000" => "2000条","2500" => "2500条",
								"3000" => "3000条","3500" => "3500条","5000" => "5000条"
							),"value" => request ( "page_size" ),
							'style'=>'width:80px',
						) )?>
					</td>
					<th>币种</th>
					<td>
						<?php
						echo Q::control ( "dropdownbox", "currency", array (
							"items" =>  Helper_Array::toHashmap(CodeCurrency::find()->asArray()->getAll(), 'code','code'),
							"empty" => true,
							"value" => request ( "currency"),
						) )?>
					</td>
				</tr>
				<tr>
					<td>
						<button class="btn btn-small btn-info" name="search" id="search">
							<i class="icon-search"></i>
							搜索
						</button>
					</td>
				    <td>
						<a class="btn btn-small btn-primary" href="javascript:void(0)" onclick="downloadpay()">
						     <i class="icon-download"></i>
						     导出
						</a>
					</td>
				    <td>
						<a class="btn btn-info btn-small"href="javascript:void(0)" onclick="file()"><i class="icon-upload"></i> 成本对账 </a> 
				    </td>
				    <td>
						<a class="btn btn-info btn-small"href="javascript:void(0)" onclick="fileout()"><i class="icon-upload"></i> 导入成本费用 </a> 
				    </td>
				    <td>
						<a class="btn btn-info btn-small"href="javascript:void(0)" onclick="fileout2()"><i class="icon-upload"></i> 导入差额 </a> 
				    </td>
				    <td>
						<a href="javascript:void(0);"
							onclick="$('#dialog_search').dialog('open');"> 更多搜索选项</a>
				    </td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="dialog_split" class="easyui-dialog" title="拆分费用" data-options="closed:true, modal:true" style="width: 200px; height: 100px;">
		<table style="margin-top: 5px; margin-bottom: 5px; width: 100%;">
			<tbody>
				<tr>
					<th style="text-align: right;">金额</th>
					<td style="padding: 1px 10px 2px;">
						<input id="text_amount" type="number" style="width: 70%" value="<?php echo request('amount')?>" min="0" step="0.01" required="required" />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<button class="btn btn-info" type="submit" onclick="split()">
							<i class="icon-filter"></i>
							拆分
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="filter_id" value="<?php echo request('filter_id')?>" />
	<input id="hidden_fee_type" type="hidden" name="fee_type_" value="<?php echo implode(',', request('fee_type',array()))?>" />
    <input id="hidden_ali_order_no" type="hidden" name="ali_order_no" value="<?php echo request('ali_order_no')?>" />
    <input id="hidden_tracking_no" type="hidden" name="tracking_no" value="<?php echo request('tracking_no')?>" />
	<input id="hidden_invoice_code" type="hidden" name="invoice_no" value="<?php echo request('invoice_no')?>" />
	<input id="hidden_voucher_code" type="hidden" name="voucher_no" value="<?php echo request('voucher_no')?>" />
	<input id="hidden_bill_no" type="hidden" name="bill_no" value="<?php echo request('bill_no')?>" />
	<input type="hidden" id="hidden_originamount"	value=""/>
	<input type="hidden" id="page" name="page" value="<?php echo request('page')?>">
</form>
<div style="clear: both;"></div>
<div>
	<div style="float: left; width: 210px;" id="filter-area">
		<div
			style="border: 1px solid #d4d4d4; background-color: #f2f2f2; width: 200px; padding: 4px;">供应商筛选</div>
		<div
			style="border: 1px solid #d4d4d4; background-color: #ffffff; width: 200px; padding: 4px; border-top: 0px;">
			<input type="text" name="filter-input" value="" placeholder="快速筛选"
				style="width: 176px; margin: 6px 6px 10px;">
			<ul id="filter-list" class="nav nav-list"
				style="overflow: auto; height: 400px;">
				<li class="active">
					<a class="filter-a">全部</a>
				</li>
				<?php foreach ($filters as $filter):?>
				<li>
					<a class="filter-a" data-filter_id="<?php echo $filter['filter_id']?>"><?php echo $filter['filter_name']?></a>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
	<div style="float: left; margin-left:10px; width: 945px;" id="data-area">
		<div id="detail" style="min-height: 420px;"></div>
	</div>
</div>
<div id="dialog_search" class="easyui-dialog" title="高级搜索"
		data-options="closed:true, modal:true"
		style="width: 800px; height: 450px;">
	<table style="margin-top: 5px; margin-bottom: 5px; width: 100%;">
		<tbody>
		   <tr>
				<th style="text-align: right;">订单号</th>
				<td style="padding: 1px 10px 2px;">
					<textarea id="text_ali_order_no" rows="3" placeholder="每行一个订单号"
						style="width: 90%"><?php echo request("ali_order_no")?></textarea>
				</td>
				<th style="text-align: right;">运单号</th>
				<td style="padding: 1px 10px 2px;">
					<textarea id="text_tracking_no" rows="3" placeholder="每行一个运单号"
						style="width: 90%"><?php echo request("tracking_no")?></textarea>
				</td>
			</tr>
			<tr>
				<th style="text-align: right;">发票号</th>
				<td style="padding: 1px 10px 2px;">
					<textarea id="text_invoice_code" rows="3" placeholder="每行一个发票号"
						style="width: 90%"><?php echo request("invoice_no")?></textarea>
				</td>
				<th style="text-align: right;">凭证号</th>
				<td style="padding: 1px 10px 2px;">
					<textarea id="text_voucher_code" rows="3" placeholder="每行一个凭证号"
						style="width: 90%"><?php echo request("voucher_no")?></textarea>
				</td>
			</tr>
			<tr>
				<th style="text-align: right;">账单号</th>
				<td style="padding: 1px 10px 2px;">
					<textarea id="text_bill_no" rows="3" placeholder="每行一个账单号"
						style="width: 90%"><?php echo request("bill_no")?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;">
					<button class="btn btn-primary" type="submit" onclick="Search()">
						<i class="icon-search"></i>
						搜索
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<form id="form" action="<?php echo url('statistics/emscompare')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="file">
    <input type="hidden" id="hiddenchannel_id" name="hiddenchannel_id">
    <input type="hidden" id="hiddensupplier_idt" name="hiddensupplier_idt">
    <input type="hidden" id="hiddencurrency" name="hiddencurrency">
</form>
<form id="formout" action="<?php echo url('statistics/outfee')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileout">
    <input type="hidden" id="hiddensupplier_id" name="hiddensupplier_id">
    <input type="hidden" id="hiddenoutcurrency" name="hiddenoutcurrency">
</form>
<form id="formout2" action="<?php echo url('statistics/difference')?>" method="post" enctype="multipart/form-data" style="display:none">
    <input type="file"  name="file" id="fileout2">
    <input type="hidden" id="hiddensupplier_id2" name="hiddensupplier_id">
</form>
<script type="text/javascript">
$(function(){
	$("#search").click(function(){
		$('#page-top-search').find('[name="filter_id"]').val('全部');
		$("#page").val('1');
	});
	// 初始化
	loadDeatil();
	// 左侧快速筛选
	$('[name="filter-input"]').on('keyup change',function(){
		var $filter = $(this);
		var input_value = $filter.val().trim().toUpperCase();
		if (input_value == "") {
			$('#filter-list').find('li').show();
		} else {
			$('#filter-list').find('li').each(function(){
				var li_value = $(this).find('a').html().trim().toUpperCase();
				if (li_value.indexOf(input_value) >= 0) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	});
	// 左侧快速筛选点击事件
	$('.filter-a').on('click',function(){
		$('#filter-list').find('li').removeClass('active');
		$(this).closest('li').addClass('active');
		$('#page-top-search').find('[name="filter_id"]').val($(this).data('filter_id'));
		$("#page").val('1');
		loadDeatil();
	});
	// 翻页异步加载
	$('#detail').on('click','.pagination a',function(){
		$("#page").val($(this).text());
		loadDeatil();
		return false;
	});
});
function loadDeatil(href){
	var $form = $('#page-top-search');
	var url = "<?php echo url('statistics/Paytable')?>";
// 	$("#detail").load(url,$form.serialize(),function(){
// 		console.log('loadDeatil');
// 	});
	$.ajax({
		url:url,
		data:$form.serialize(),
		type:'post',
		dataType:'html',
		success:function(data){
			$("#detail").html(data);
		}
	})
}

function file(){
	if($("#channel_id").val()!='' || $("#supplier_id").val()!=''){
		$("#hiddenchannel_id").val($("#channel_id").val());
		$("#hiddensupplier_idt").val($("#supplier_id").val());
	}else{
	    layer.msg('请选择渠道或者供应商!');
	    return false;
	}
	if($("#currency").val()!=''){
		$("#hiddencurrency").val($("#currency").val());
	}else{
		layer.msg('请选择币种!');
	    return false;
	}
	$('#file').click();
}
$(function(){
	$("#file").change(function(){
		$("#form").submit();	
	})
});
function fileout(){
	if($("#supplier_id").val()!=''){
		$("#hiddensupplier_id").val($("#supplier_id").val());
	}else{
		layer.msg('请选择供应商!');
		return false;
	}
	if($("#currency").val()!=''){
		$("#hiddenoutcurrency").val($("#currency").val());
	}else{
		layer.msg('请选择币种!');
	    return false;
	}
	$('#fileout').click();
}
function fileout2(){
	if($("#supplier_id").val()!=''){
		$("#hiddensupplier_id2").val($("#supplier_id").val());
	}else{
		layer.msg('请选择供应商!');
		return false;
	}
	
	$('#fileout2').click();
}
$(function(){
	$("#fileout").change(function(){
		$("#formout").submit();	
	})
	$("#fileout2").change(function(){
		$("#formout2").submit();	
	})
});
/**
 * 搜索
 */
function Search(){
	$("#hidden_ali_order_no").val($("#text_ali_order_no").val());
	$("#hidden_tracking_no").val($("#text_tracking_no").val());
	$("#hidden_invoice_code").val($("#text_invoice_code").val());
	$("#hidden_voucher_code").val($("#text_voucher_code").val());
	$("#hidden_bill_no").val($("#text_bill_no").val());
	$("#page").val('1');
	$("#page-top-search").attr("action","<?php echo url('statistics/pay')?>");
	$("#page-top-search").submit();
}
function downloadpay(){
// 	var $form = $('#page-top-search');
	//var url ="<!?php echo url('statistics/Payexport')?>";
	//window.location.href="<!?php echo url('statistics/Payexport')?>"+'?'+$form.serialize();
	$("#page-top-search").attr("action","<?php echo url('statistics/Payexport')?>");
	$("#page-top-search").submit();
	$("#page-top-search").attr("action","<?php echo url('statistics/Pay')?>");
}
/**
* 拆分费用
*/
function split(){
	if($('#text_amount').val()=='' || isNaN($('#text_amount').val()) ){
		alert("拆分金额不正确");
		return false;
	}else if(Number($('#text_amount').val())<=0){
		alert("拆分金额必须大于0");
		return false;
	}else if(Number($('#text_amount').val()) >= Number($('#hidden_originamount').val())){
		alert("拆分金额必须小于原金额");
		return false;
	}
	$('#dialog_split').dialog('close');
	$.ajax({
		type:'post',
		url:'<?php echo url('statistics/split')?>',
		data : {  
				 'fee_id' : $('.checkbox:checked').val(),
				 'originamount' : $('#hidden_originamount').val(),
				 'nowamount' : $('#text_amount').val()
				 },
			success:function(data){
	 			if(data=="true"){
	 				alert("拆分成功");
	 	 		}else{
	 	 			alert("拆分失败,数据异常");
	 	 	 	}
			}
	});
	$('#text_amount').val("");
}
</script>
<?PHP $this->_endblock();?>

