<?PHP $this->_extends('_layouts/default_layout'); ?>
<?PHP $this->_block('title');?>
    条件查询
<?PHP $this->_endblock();?>
<?PHP $this->_block('head');?>
    <?php //head 部分 ?>
<?PHP $this->_endblock();?>
<?PHP $this->_block('contents');?>
<div>
</div>
<form method="POST">
	<div class="FarSearch" >
		<table>
			<tbody>
				<tr>
    				<th>凭证号</th>
    				<td>
    					<input type="text" name="voucher_no"
    						value="<?php echo request('voucher_no')?>" style="width: 117px;" />
    				</td>
    				<th>发票号</th>
    				<td>
    					<input type="text" name="invoice_no"
    						value="<?php echo request('invoice_no')?>" style="width: 117px;" />
    				</td>
    				<th>账单抬头</th>
    				<td>
    					<input type="text" name="waybill_title"
    						value="<?php echo request('waybill_title')?>"
    						style="width: 117px;" />
    				</td>
    				<th>类型</th>
    				<td>
    					<?php
    					echo Q::control ( "dropdownbox", "fee_type", array (
    						"items" => array (
    							"1" => "应收","2" => "应付"
    						),"value" => request ( "fee_type" ),"empty" => true,
    						"style" => "width: 80px" 
    					) )?>
    				</td>
					<td>
					   <button class="btn btn-primary btn-mini" id="search">
			             <i class="icon-search"></i>
			                                         搜索
		               </button>
		               <button type="submit" name="export" class="btn btn-mini btn-info" value="export">
						 <i class="icon-download"></i>
							导出
					   </button>
					   <button type="submit" name="export" class="btn btn-mini btn-info" value="export1">
						 <i class="icon-download"></i>
							导出1
					   </button>
					   <a href="javascript:void(0);" onclick="$('#dialog_search').dialog('open');$('.window-shadow').css('top','106px');$('.panel').css('top','106px');$('#dialog_search').removeClass('hide');"> 更多搜索选项 </a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input id="hidden_invoice_nos" type="hidden" name="invoice_nos" value="" />

	<div id="dialog_search" class="easyui-dialog hide" title="高级搜索"
		data-options="closed:true, modal:true"
		style="width: 400px; height: 300px;">
		<table style="margin-top: 5px; margin-bottom: 5px; width: 100%;">
			<tbody>
				<tr>
					<th style="text-align: right;">发票号</th>
					<td style="padding: 1px 10px 2px;">
						<textarea id="text_invoice_nos" rows="8"
							placeholder="多个发票号用回车隔开" style="width: 90%"><?php echo request("invoice_nos")?></textarea>
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
	<table class="FarTable" style="max-width: none;">
		<thead>
			<tr>
				<th>类型</th>
				<th>运单号</th>
				<th>发件日</th>
				<th>登账日期</th>
				<th>客户/供应商</th>
				<th>部门</th>
				<th>金额</th>
				<th>费用项目</th>
				<th>发票号</th>
				<th>发票日期</th>
				<th>凭证号</th>
				<th>凭证日期</th>
				<th>账单抬头</th>
			</tr>
		</thead>
		<tbody>
		<?php if (isset($list)):?>
		<?php foreach ($list as $temp):?>
			<tr>
			    <td><?php echo ($temp->fee_type == '1')?'应收':'应付'?></td>
			    <td><?php echo $temp->order->tracking_no?></td>
			    <td><?php echo Helper_Util::strDate('Y-m-d',$temp->order->record_order_date)?></td>
			    <td><?php echo Helper_Util::strDate('Y-m-d',$temp->create_time)?></td>
			    <?php if($temp->fee_type == '1'):?>
			    <td><?php echo $temp->order->customer_id?$customer[$temp->order->customer_id]:''?></td>
			    <?php else:?>
			    <td><?php echo $temp->order->channel->supplier_id?$supplier[$temp->order->channel->supplier_id]:''?></td>
			    <?php endif;?>
			    <td><?php echo $temp->order->department_id?$dpt[$temp->order->department_id]:''?></td>
			    <td style="text-align:right;"><?php echo sprintf('%.2f',$temp->amount)?></td>
			    <td><?php echo $temp->fee_item_name?></td>
			    <td><?php echo $temp->invoice_no?></td>
			    <td><?php echo Helper_Util::strDate('Y-m-d',$temp->invoice_time)?></td>
			    <td><?php echo $temp->voucher_no?></td>
			    <td><?php echo Helper_Util::strDate('Y-m-d',$temp->voucher_time)?></td>
			    <td><?php echo $temp->waybill_title?></td>
			</tr>
		<?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<?php if (isset($list)):?>
    <div>总金额：<?php echo $sum?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;应收金额：<?php echo $receive?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;应付金额：<?php echo $pay?></div>
    <?php endif;?>
</form>
    <?php
    	if (isset($list)){
    		$this->_control ( "pagination", "my-pagination", array (
    			"pagination" => $pagination
    		) );
    	}
    ?>
<script type="text/javascript">
/**
 * 搜索
 */
function Search(){
	$("#hidden_invoice_nos").val($("#text_invoice_nos").val());
	$("form").submit();
}
</script>
<?PHP $this->_endblock();?>

